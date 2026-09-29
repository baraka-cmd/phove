<?php

namespace App\Services\Stock;

use App\Enums\NatureMouvement;
use App\Enums\StatutLot;
use App\Enums\TypeMouvement;
use App\Models\InventaireStock;
use App\Models\InventaireStockLigne;
use App\Models\LotStock;
use App\Models\MouvementStock;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Service métier des inventaires de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un inventaire est une opération de comptage physique qui
 *   compare la quantité THÉORIQUE (calculée depuis les lots)
 *   avec la quantité PHYSIQUE (constatée sur le terrain).
 *
 * WORKFLOW :
 *
 *   brouillon
 *       ↓ creer() + compter()
 *   en_cours
 *       ↓ compter() sur toutes les lignes
 *   termine
 *       ↓ valider()
 *   valide
 *
 *   Depuis brouillon ou en_cours, un inventaire peut être
 *   annulé via annuler().
 *
 * INVARIANTS CRITIQUES :
 *
 *   1. La création d'un inventaire FIXE la photographie
 *      du stock dans `inventaire_stock_lignes.quantite_theorique`.
 *      Cette quantité devient IMMUABLE.
 *
 *   2. Tant que l'inventaire n'est pas validé, AUCUN stock
 *      n'est modifié. Seuls les comptages sont enregistrés.
 *
 *   3. À la validation, on recalcule la quantité système
 *      ACTUELLE pour chaque article. Si elle diffère de la
 *      photographie initiale → on REFUSE la validation, car
 *      le stock a bougé entre-temps et l'ajustement serait
 *      faux.
 *
 *   4. Pour chaque ligne avec un écart non nul, on génère
 *      UN mouvement d'ajustement par lot concerné.
 *
 *   5. Toute écriture est transactionnelle et verrouillée.
 */
class InventaireStockService
{
    /*
    |--------------------------------------------------------------------------
    | REQUÊTE (LECTURE)
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare la requête de recherche des inventaires.
     */
    public function requete(
        ?string $recherche = null,
        ?string $statut = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): Builder {
        return InventaireStock::query()
            ->with(['emplacement', 'responsable'])
            ->when(
                $recherche,
                fn (Builder $q, string $r) => $q->recherche($r)
            )
            ->when(
                $statut,
                fn (Builder $q, string $s) => $q->where('statut', $s)
            )
            ->when(
                $dateDebut && trim($dateDebut) !== '',
                fn (Builder $q) => $q->whereDate('date_debut', '>=', $dateDebut)
            )
            ->when(
                $dateFin && trim($dateFin) !== '',
                fn (Builder $q) => $q->whereDate('date_debut', '<=', $dateFin)
            );
    }

    /**
     * Récupère un inventaire par identifiant.
     */
    public function trouver(int $id): InventaireStock
    {
        return InventaireStock::query()
            ->with([
                'lignes.stock',
                'emplacement',
                'responsable',
                'validePar',
            ])
            ->findOrFail($id);
    }

    /**
     * Inventaires d'un article donné.
     */
    public function inventairesDuStock(int $stockId): Collection
    {
        return InventaireStock::query()
            ->whereHas('lignes', function (Builder $q) use ($stockId) {
                $q->where('stock_id', $stockId);
            })
            ->with(['emplacement', 'responsable', 'validePar'])
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Dernier inventaire VALIDÉ d'un article.
     */
    public function dernierInventaire(Stock $stock): ?InventaireStock
    {
        return InventaireStock::query()
            ->where('statut', 'valide')
            ->whereHas('lignes', function (Builder $q) use ($stock) {
                $q->where('stock_id', $stock->id);
            })
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Crée un inventaire et fige la photographie du stock.
     *
     * AUCUN stock n'est modifié à ce stade.
     *
     * Pour chaque article ciblé, on crée une ligne avec :
     *   - quantite_theorique = quantité système actuelle ;
     *   - quantite_comptee   = null ;
     *   - ecart              = null.
     *
     * @param array $data
     *   - emplacement_id : int|null
     *   - date_debut     : string
     *   - stock_ids      : array<int>  (optionnel — si vide, tous les articles du périmètre)
     *   - notes          : string|null
     *
     * @throws Throwable
     */
    public function creer(
        array $data,
        int $utilisateurId
    ): InventaireStock {
        return DB::transaction(function () use ($data, $utilisateurId) {

            $emplacementId = $data['emplacement_id'] ?? null;

            /*
             * Détermination des articles ciblés.
             */
            $stockIds = $data['stock_ids'] ?? [];

            if (empty($stockIds)) {
                /*
                 * Inventaire global.
                 */
                $stockIds = Stock::query()
                    ->actifs()
                    ->when(
                        $emplacementId,
                        fn (Builder $q) => $q->where(
                            'emplacement_principal_id',
                            $emplacementId
                        )
                    )
                    ->pluck('id')
                    ->all();
            } else {
                /*
                 * Inventaire ciblé : on valide les IDs.
                 */
                $stockIds = Stock::query()
                    ->whereIn('id', $stockIds)
                    ->where('statut', \App\Enums\StatutStock::ACTIF->value)
                    ->pluck('id')
                    ->all();
            }

            if (empty($stockIds)) {
                throw ValidationException::withMessages([
                    'stock_ids' =>
                        'Aucun article actif n’a été trouvé pour ce périmètre.',
                ]);
            }

            /*
             * Création de l'en-tête.
             */
            $inventaire = InventaireStock::create([
                'numero'          => $this->genererNumero(),
                'emplacement_id'  => $emplacementId,
                'date_debut'      => $data['date_debut'] ?? now()->toDateString(),
                'responsable_id'  => $data['responsable_id'] ?? $utilisateurId,
                'statut'          => 'en_cours',
                'notes'           => $data['notes'] ?? null,
            ]);

            /*
             * Création des lignes avec photographie figée.
             *
             * On verrouille chaque stock au passage pour garantir
             * que la photographie est cohérente avec la réalité
             * au moment T.
             */
            foreach (array_chunk($stockIds, 100) as $chunk) {

                $stocks = Stock::query()
                    ->whereIn('id', $chunk)
                    ->lockForUpdate()
                    ->get();

                foreach ($stocks as $stock) {

                    $quantiteTheorique = $this->calculerQuantiteSysteme($stock);

                    InventaireStockLigne::create([
                        'inventaire_stock_id' => $inventaire->id,
                        'stock_id'            => $stock->id,
                        'quantite_theorique'  => $quantiteTheorique,
                        'quantite_comptee'    => null,
                        'ecart'               => null,
                        'unite'               => $stock->unite,
                    ]);
                }
            }

            return $inventaire->fresh([
                'lignes.stock',
                'emplacement',
                'responsable',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | COMPTAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre le comptage physique d'une ligne.
     *
     * AUCUN stock n'est modifié.
     * Seule la ligne d'inventaire est mise à jour.
     *
     * @throws Throwable
     */
    public function compter(
        InventaireStock $inventaire,
        int $ligneId,
        float $quantiteComptee
    ): InventaireStockLigne {
        if ($quantiteComptee < 0) {
            throw ValidationException::withMessages([
                'quantite_comptee' =>
                    'La quantité comptée ne peut pas être négative.',
            ]);
        }

        return DB::transaction(function () use (
            $inventaire,
            $ligneId,
            $quantiteComptee
        ) {

            $inventaire = InventaireStock::query()
                ->lockForUpdate()
                ->findOrFail($inventaire->id);

            if (!$inventaire->peutEtreCompte()) {
                throw ValidationException::withMessages([
                    'inventaire' => sprintf(
                        'Un inventaire "%s" ne peut pas être compté.',
                        $inventaire->statut
                    ),
                ]);
            }

            $ligne = InventaireStockLigne::query()
                ->where('inventaire_stock_id', $inventaire->id)
                ->lockForUpdate()
                ->findOrFail($ligneId);

            $ligne->enregistrerComptage($quantiteComptee);
            $ligne->save();

            return $ligne->fresh('stock');
        });
    }

    /**
     * Enregistre le comptage de plusieurs lignes d'un coup.
     *
     * @param array<int, float> $comptages  [ligne_id => quantite]
     *
     * @throws Throwable
     */
    public function compterLot(
        InventaireStock $inventaire,
        array $comptages
    ): InventaireStock {
        return DB::transaction(function () use ($inventaire, $comptages) {

            $inventaire = InventaireStock::query()
                ->lockForUpdate()
                ->findOrFail($inventaire->id);

            if (!$inventaire->peutEtreCompte()) {
                throw ValidationException::withMessages([
                    'inventaire' => sprintf(
                        'Un inventaire "%s" ne peut pas être compté.',
                        $inventaire->statut
                    ),
                ]);
            }

            foreach ($comptages as $ligneId => $quantite) {

                if ($quantite < 0) {
                    throw ValidationException::withMessages([
                        'quantite_comptee' => sprintf(
                            'Quantité négative interdite pour la ligne #%d.',
                            $ligneId
                        ),
                    ]);
                }

                $ligne = InventaireStockLigne::query()
                    ->where('inventaire_stock_id', $inventaire->id)
                    ->lockForUpdate()
                    ->find($ligneId);

                if (!$ligne) {
                    throw ValidationException::withMessages([
                        'lignes' => sprintf(
                            'La ligne #%d n’appartient pas à cet inventaire.',
                            $ligneId
                        ),
                    ]);
                }

                $ligne->enregistrerComptage($quantite);
                $ligne->save();
            }

            return $inventaire->fresh([
                'lignes.stock',
                'emplacement',
                'responsable',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Valide un inventaire : applique les ajustements.
     *
     * Conditions :
     *   - toutes les lignes doivent être comptées ;
     *   - la quantité système actuelle doit être identique
     *     à la photographie de départ, pour chaque article.
     *
     * Si un écart est constaté, on génère un mouvement
     * d'ajustement par lot concerné et on met à jour le lot.
     *
     * @throws Throwable
     */
    public function valider(
        InventaireStock $inventaire,
        array $lignesAAdjuster,
        int $utilisateurId
    ): InventaireStock {
        return DB::transaction(function () use (
            $inventaire,
            $lignesAAdjuster,
            $utilisateurId
        ) {
            $inventaire = InventaireStock::query()
                ->lockForUpdate()
                ->findOrFail($inventaire->id);

            if ($inventaire->estFinalise()) {
                throw ValidationException::withMessages([
                    'inventaire' =>
                        'Cet inventaire est déjà finalisé.',
                ]);
            }

            $inventaire->load('lignes.stock');

            if ($inventaire->lignes->isEmpty()) {
                throw ValidationException::withMessages([
                    'inventaire' =>
                        'Cet inventaire ne contient aucune ligne.',
                ]);
            }

            /*
             * Vérification : toutes les lignes doivent être comptées.
             */
            $nonComptees = $inventaire->lignes->reject(
                fn (InventaireStockLigne $l) => $l->estComptee()
            );

            if ($nonComptees->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'inventaire' => sprintf(
                        '%d ligne(s) n’ont pas été comptées.',
                        $nonComptees->count()
                    ),
                ]);
            }

            /*
             * Lock des stocks concernés (triés).
             */
            $stockIds = $inventaire->lignes
                ->pluck('stock_id')
                ->unique()
                ->sort()
                ->values();

            $stocks = Stock::query()
                ->whereIn('id', $stockIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            /*
             * Vérification de cohérence : la photographie
             * doit correspondre à la réalité actuelle.
             */
            foreach ($inventaire->lignes as $ligne) {

                $stock = $stocks->get($ligne->stock_id);

                if (!$stock) {
                    throw ValidationException::withMessages([
                        'inventaire' => sprintf(
                            'Le stock #%d est introuvable.',
                            $ligne->stock_id
                        ),
                    ]);
                }

                $quantiteActuelle = $this->calculerQuantiteSysteme($stock);

                if (round($quantiteActuelle, 3) !== round((float) $ligne->quantite_theorique, 3)) {
                    throw ValidationException::withMessages([
                        'inventaire' => sprintf(
                            'Le stock "%s" a changé depuis le début de l’inventaire (théorique figé : %s, actuel : %s). Veuillez recommencer l’inventaire.',
                            $stock->designation,
                            $ligne->quantite_theorique,
                            $quantiteActuelle
                        ),
                    ]);
                }
            }

            /*
             * Application des ajustements, ligne par ligne.
             */
            foreach ($inventaire->lignes as $ligne) {

                if (!$ligne->aUnEcart()) {
                    continue;
                }

                $stock = $stocks->get($ligne->stock_id);

                $this->appliquerAjustement(
                    $inventaire,
                    $stock,
                    $ligne,
                    $utilisateurId
                );
            }

            /*
             * MAJ du statut de l'inventaire.
             */
            $inventaire->update([
                'statut'       => 'valide',
                'valide_par'   => $utilisateurId,
                'valide_at'    => now(),
                'date_cloture' => now()->toDateString(),
            ]);

            return $inventaire->fresh([
                'lignes.stock',
                'emplacement',
                'responsable',
                'validePar',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule un inventaire.
     *
     * @throws Throwable
     */
    public function annuler(InventaireStock $inventaire): InventaireStock
    {
        return DB::transaction(function () use ($inventaire) {

            $inventaire = InventaireStock::query()
                ->lockForUpdate()
                ->findOrFail($inventaire->id);

            if ($inventaire->estFinalise()) {
                throw ValidationException::withMessages([
                    'inventaire' =>
                        'Un inventaire finalisé ne peut plus être annulé.',
                ]);
            }

            $inventaire->update([
                'statut'       => 'annule',
                'date_cloture' => now()->toDateString(),
            ]);

            return $inventaire->fresh([
                'lignes.stock',
                'emplacement',
                'responsable',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RÉSUMÉ
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne un résumé chiffré de l'inventaire.
     *
     * @return array<string, mixed>
     */
    public function resume(InventaireStock $inventaire): array
    {
        $inventaire->loadMissing('lignes');

        $lignes = $inventaire->lignes;

        $theorique = round((float) $lignes->sum('quantite_theorique'), 3);
        $compte    = round((float) $lignes->sum('quantite_comptee'), 3);
        $ecart     = round($compte - $theorique, 3);

        $nombreEcarts = $lignes
            ->filter(fn (InventaireStockLigne $l) => $l->aUnEcart())
            ->count();

        return [
            'nombre_lignes'   => $lignes->count(),
            'nombre_ecarts'   => $nombreEcarts,
            'quantite_systeme' => $theorique,
            'quantite_comptee' => $compte,
            'ecart_global'    => $ecart,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Applique un ajustement pour une ligne d'inventaire.
     *
     * Crée un mouvement d'ajustement par lot concerné et met
     * à jour les lots correspondants.
     */
    private function appliquerAjustement(
        InventaireStock $inventaire,
        Stock $stock,
        InventaireStockLigne $ligne,
        int $utilisateurId
    ): void {
        $ecart = round((float) $ligne->ecart, 3);

        if ($ecart === 0.0) {
            return;
        }

        if ($ecart > 0) {
            /*
             * Surplus : on ajoute la quantité à un lot actif
             * (ou on crée un lot technique si aucun lot n'existe).
             */
            $lot = LotStock::query()
                ->where('stock_id', $stock->id)
                ->where('statut', StatutLot::ACTIF->value)
                ->fefo()
                ->lockForUpdate()
                ->first();

            if (!$lot) {
                /*
                 * Aucun lot actif : on crée un lot technique
                 * dédié à l'ajustement.
                 */
                $lot = LotStock::create([
                    'stock_id'           => $stock->id,
                    'numero_lot'         => 'AJUST-' . $inventaire->numero,
                    'date_entree'        => now()->toDateString(),
                    'quantite_initiale'  => 0,
                    'quantite_actuelle'  => 0,
                    'statut'             => StatutLot::ACTIF->value,
                    'reference_document' => $inventaire->numero,
                ]);
            }

            $this->creerMouvementAjustement(
                $inventaire,
                $stock,
                $lot,
                $ecart,
                $utilisateurId
            );

            $lot->quantite_initiale = round(
                (float) $lot->quantite_initiale + $ecart,
                3
            );
            $lot->quantite_actuelle = round(
                (float) $lot->quantite_actuelle + $ecart,
                3
            );
            $lot->statut = $this->determinerStatutLot($lot);
            $lot->save();

            return;
        }

        /*
         * Manquant : on retire la quantité sur les lots
         * disponibles en FEFO.
         */
        $reste = abs($ecart);

        $lots = LotStock::query()
            ->where('stock_id', $stock->id)
            ->where('quantite_actuelle', '>', 0)
            ->whereIn('statut', [
                StatutLot::ACTIF->value,
                StatutLot::BLOQUE->value,
            ])
            ->fefo()
            ->lockForUpdate()
            ->get();

        foreach ($lots as $lot) {

            if ($reste <= 0) {
                break;
            }

            $disponible = round((float) $lot->quantite_actuelle, 3);
            $prelevement = min($reste, $disponible);

            $this->creerMouvementAjustement(
                $inventaire,
                $stock,
                $lot,
                -$prelevement,
                $utilisateurId
            );

            $lot->quantite_actuelle = round(
                (float) $lot->quantite_actuelle - $prelevement,
                3
            );
            $lot->statut = $this->determinerStatutLot($lot);
            $lot->save();

            $reste = round($reste - $prelevement, 3);
        }

        if ($reste > 0) {
            throw ValidationException::withMessages([
                'inventaire' => sprintf(
                    'Impossible d’appliquer le manquant de %s sur "%s" : quantité insuffisante dans les lots.',
                    $reste,
                    $stock->designation
                ),
            ]);
        }
    }

    /**
     * Crée un mouvement d'ajustement.
     */
    private function creerMouvementAjustement(
        InventaireStock $inventaire,
        Stock $stock,
        LotStock $lot,
        float $quantiteSignee,
        int $utilisateurId
    ): MouvementStock {
        return MouvementStock::create([
            'stock_id'           => $stock->id,
            'lot_stock_id'       => $lot->id,
            'emplacement_id'     => $stock->emplacement_principal_id,
            'type'               => TypeMouvement::AJUSTEMENT->value,
            'nature'             => NatureMouvement::INVENTAIRE->value,
            'quantite'           => round(abs($quantiteSignee), 3),
            'sens'               => $quantiteSignee >= 0 ? 1 : -1,
            'date_mouvement'     => now()->toDateString(),
            'reference_document' => $inventaire->numero,
            'utilisateur_id'     => $utilisateurId,
            'motif'              => 'Ajustement d’inventaire',
            'notes'              => $inventaire->notes,
            'statut'             => 'valide',
        ]);
    }


        /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    |
    | Comptage global des inventaires par état.
    |
    | Clés retournées (alignées sur la vue inventaires/index.blade.php) :
    |   - total
    |   - en_cours
    |   - avec_ecarts
    |   - valides
    */

    /**
     * Retourne les statistiques des inventaires.
     *
     * @return array<string, int>
     */
    public function statistiques(): array
    {
        return [
            'total' => InventaireStock::query()->count(),

            'en_cours' => InventaireStock::query()
                ->whereIn('statut', [
                    'brouillon',
                    'en_cours',
                ])
                ->count(),

            'avec_ecarts' => InventaireStock::query()
                ->whereHas('lignes', function ($query) {
                    $query
                        ->whereNotNull('ecart')
                        ->where('ecart', '!=', 0);
                })
                ->count(),

            'valides' => InventaireStock::query()
                ->where('statut', 'valide')
                ->count(),
        ];
    }

    
    /**
     * Détermine le statut correct d'un lot après ajustement.
     */
    private function determinerStatutLot(LotStock $lot): StatutLot
    {
        if ((float) $lot->quantite_actuelle <= 0) {
            return StatutLot::EPUISE;
        }

        if ($lot->estExpireSelonDate()) {
            return StatutLot::EXPIRE;
        }

        if ($lot->estBloque()) {
            return StatutLot::BLOQUE;
        }

        return StatutLot::ACTIF;
    }

    /**
     * Calcule la quantité système d'un article.
     *
     * = somme des quantités actuelles des lots ACTIF + BLOQUE.
     */
    private function calculerQuantiteSysteme(Stock $stock): float
    {
        return round(
            (float) LotStock::query()
                ->where('stock_id', $stock->id)
                ->whereIn('statut', [
                    StatutLot::ACTIF->value,
                    StatutLot::BLOQUE->value,
                ])
                ->sum('quantite_actuelle'),
            3
        );
    }

    /**
     * Génère un numéro unique d'inventaire.
     *
     * Format : INV-YYYY-NNNNNN
     */
    private function genererNumero(): string
    {
        $prefixe = 'INV-' . now()->format('Y') . '-';

        $dernier = InventaireStock::query()
            ->where('numero', 'like', $prefixe . '%')
            ->orderByDesc('id')
            ->value('numero');

        if (!$dernier) {
            return $prefixe . '000001';
        }

        $sequence = (int) substr($dernier, strlen($prefixe));

        return $prefixe . str_pad(
            (string) ($sequence + 1),
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    
}