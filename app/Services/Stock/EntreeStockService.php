<?php

namespace App\Services\Stock;

use App\Enums\NatureMouvement;
use App\Enums\StatutLot;
use App\Enums\StatutStock;
use App\Enums\TypeMouvement;
use App\Models\EmplacementStock;
use App\Models\LotStock;
use App\Models\MouvementStock;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Service métier des entrées de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une entrée de stock est une opération qui :
 *     1. augmente la quantité d'un lot (existant ou nouveau) ;
 *     2. crée une ligne dans le journal des mouvements.
 *
 *   Elle ne modifie JAMAIS la table `stocks` : la quantité
 *   physique n'y est PAS stockée. Elle est calculée à partir
 *   des lots.
 *
 * TRANSACTION :
 *
 *   Toute la méthode `enregistrer()` s'exécute dans une
 *   transaction unique :
 *
 *     DB::transaction {
 *       lock stock
 *       lock emplacement
 *       lock/créer lot
 *       mettre à jour quantité
 *       synchroniser statut
 *       créer mouvement (type=entree, sens=+1)
 *     }
 *
 * INVARIANTS :
 *
 *   - La quantité entrée est strictement > 0.
 *   - Le lot est identifié par (stock_id, numero_lot).
 *   - Un lot BLOQUE ne reçoit PAS de nouvelle quantité.
 *   - Un lot EXPIRE ne reçoit PAS de nouvelle quantité.
 *   - Le `statut` d'un lot est maintenu cohérent avec la
 *     réalité (quantité, date d'expiration).
 *
 * NATURE AUTORISÉE :
 *
 *   Seules les natures d'entrée sont acceptées :
 *     ACHAT, DON, RETOUR.
 */
class EntreeStockService
{
    /*
    |--------------------------------------------------------------------------
    | REQUÊTE (LECTURE)
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare la requête des entrées.
     *
     * Une entrée = un mouvement de type ENTREE.
     */
    public function requete(
        ?string $recherche = null,
        ?string $dateDebut = null,
        ?string $dateFin = null,
        ?string $nature = null
    ): Builder {
        $query = MouvementStock::query()
            ->where('type', TypeMouvement::ENTREE->value);

        if ($recherche !== null && trim($recherche) !== '') {
            $recherche = trim($recherche);

            $query->where(function (Builder $q) use ($recherche) {
                $q->where('reference_document', 'like', "%{$recherche}%")
                  ->orWhere('motif', 'like', "%{$recherche}%")
                  ->orWhere('notes', 'like', "%{$recherche}%")
                  ->orWhereHas('lotStock', function (Builder $lq) use ($recherche) {
                      $lq->where('numero_lot', 'like', "%{$recherche}%");
                  })
                  ->orWhereHas('stock', function (Builder $sq) use ($recherche) {
                      $sq->where('reference', 'like', "%{$recherche}%")
                         ->orWhere('designation', 'like', "%{$recherche}%");
                  });
            });
        }

        if ($dateDebut !== null && trim($dateDebut) !== '') {
            $query->whereDate('date_mouvement', '>=', trim($dateDebut));
        }

        if ($dateFin !== null && trim($dateFin) !== '') {
            $query->whereDate('date_mouvement', '<=', trim($dateFin));
        }

        if ($nature !== null && trim($nature) !== '') {
            $query->where('nature', trim($nature));
        }

        return $query;
    }

    /**
     * Récupère une entrée par identifiant.
     */
    public function trouver(int $entreeId): ?MouvementStock
    {
        return MouvementStock::query()
            ->whereKey($entreeId)
            ->where('type', TypeMouvement::ENTREE->value)
            ->with([
                'stock',
                'lotStock',
                'emplacement',
                'utilisateur',
                'projet',
            ])
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTREMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre une entrée de stock.
     *
     * @throws Throwable
     */
    public function enregistrer(
        array $data,
        int $utilisateurId
    ): MouvementStock {
        return DB::transaction(function () use ($data, $utilisateurId) {

            /*
             * 1. Validation métier.
             */
            $this->validerDonnees($data);

            /*
             * 2. Verrouillage du stock.
             */
            $stock = Stock::query()
                ->lockForUpdate()
                ->find($data['stock_id']);

            if (!$stock) {
                throw (new ModelNotFoundException())
                    ->setModel(Stock::class, [$data['stock_id']]);
            }

            if (!$stock->peutEtreUtilise()) {
                throw ValidationException::withMessages([
                    'stock_id' => sprintf(
                        'L’article "%s" est %s et ne peut pas recevoir d’entrée.',
                        $stock->designation,
                        $stock->statut->libelle()
                    ),
                ]);
            }

            /*
             * 3. Chargement de l'emplacement (optionnel).
             */
            $emplacement = $this->chargerEmplacement(
                $data['emplacement_id'] ?? null
            );

            /*
             * 4. Quantité.
             */
            $quantite = $this->decimal($data['quantite']);

            if ($quantite <= 0) {
                throw ValidationException::withMessages([
                    'quantite' =>
                        'La quantité doit être strictement supérieure à zéro.',
                ]);
            }

            /*
             * 5. Obtention ou création du lot.
             */
            $lot = $this->obtenirOuCreerLot($stock, $data);

            /*
             * 6. Mise à jour des quantités du lot.
             */
            $lot->quantite_initiale = $this->decimal($lot->quantite_initiale)
                + $quantite;

            $lot->quantite_actuelle = $this->decimal($lot->quantite_actuelle)
                + $quantite;

            /*
             * 7. Synchronisation du statut.
             */
            $lot->statut = $this->calculerStatutLot($lot);

            $lot->save();

            /*
             * 8. Création du mouvement.
             */
            $mouvement = MouvementStock::create([
                'stock_id'           => $stock->id,
                'lot_stock_id'       => $lot->id,
                'emplacement_id'     => $emplacement?->id,
                'type'               => TypeMouvement::ENTREE->value,
                'nature'             => $data['nature'],
                'quantite'           => $quantite,
                'sens'               => 1,
                'date_mouvement'     => $data['date_mouvement']
                    ?? now()->toDateString(),
                'reference_document' => $data['reference_document'] ?? null,
                'projet_id'          => $data['projet_id'] ?? null,
                'utilisateur_id'     => $utilisateurId,
                'motif'              => $data['motif'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'statut'             => 'valide',
            ]);

            return $mouvement->fresh([
                'stock',
                'lotStock',
                'emplacement',
                'utilisateur',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Valide les données d'une entrée.
     */
    private function validerDonnees(array $data): void
    {
        $erreurs = [];

        if (empty($data['stock_id']) || !is_numeric($data['stock_id'])) {
            $erreurs['stock_id'] = 'L’article est obligatoire.';
        }

        if (empty($data['quantite'])
            || !is_numeric($data['quantite'])
            || (float) $data['quantite'] <= 0
        ) {
            $erreurs['quantite'] =
                'La quantité doit être un nombre strictement positif.';
        }

        if (empty($data['numero_lot'])
            || trim((string) $data['numero_lot']) === ''
        ) {
            $erreurs['numero_lot'] =
                'Le numéro de lot est obligatoire pour une entrée.';
        }

        if (isset($data['nature'])
            && !$this->natureEstValide($data['nature'])
        ) {
            $erreurs['nature'] =
                'La nature n’est pas compatible avec une entrée.';
        }

        if (isset($data['date_expiration'])
            && isset($data['date_entree'])
            && $data['date_expiration'] < $data['date_entree']
        ) {
            $erreurs['date_expiration'] =
                'La date d’expiration ne peut pas précéder la date d’entrée.';
        }

        if ($erreurs !== []) {
            throw ValidationException::withMessages($erreurs);
        }
    }

    /**
     * Vérifie qu'une nature est compatible avec une entrée.
     */
    private function natureEstValide(mixed $nature): bool
    {
        if ($nature instanceof NatureMouvement) {
            return $nature->estEntree();
        }

        return is_string($nature)
            && in_array(
                $nature,
                NatureMouvement::naturesEntree(),
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | LOT
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère ou crée un lot pour l'article.
     *
     * @throws ValidationException
     */
    private function obtenirOuCreerLot(
        Stock $stock,
        array $data
    ): LotStock {
        $numeroLot = trim((string) $data['numero_lot']);

        $lot = LotStock::query()
            ->where('stock_id', $stock->id)
            ->where('numero_lot', $numeroLot)
            ->lockForUpdate()
            ->first();

        if ($lot) {
            /*
             * Un lot bloqué refuse toute nouvelle entrée.
             */
            if ($lot->estBloque()) {
                throw ValidationException::withMessages([
                    'numero_lot' => sprintf(
                        'Le lot "%s" est bloqué et ne peut pas être alimenté.',
                        $numeroLot
                    ),
                ]);
            }

            /*
             * Un lot expiré refuse également toute nouvelle entrée.
             */
            if ($lot->estExpire()) {
                throw ValidationException::withMessages([
                    'numero_lot' => sprintf(
                        'Le lot "%s" est expiré et ne peut pas être réactivé.',
                        $numeroLot
                    ),
                ]);
            }

            return $lot;
        }

        /*
         * Création d'un nouveau lot.
         */
        return LotStock::create([
            'stock_id'           => $stock->id,
            'numero_lot'         => $numeroLot,
            'date_fabrication'   => $data['date_fabrication'] ?? null,
            'date_entree'        => $data['date_entree']
                ?? now()->toDateString(),
            'date_expiration'    => $data['date_expiration'] ?? null,
            'quantite_initiale'  => 0,
            'quantite_actuelle'  => 0,
            'cout_unitaire'      => $data['cout_unitaire'] ?? null,
            'fournisseur'        => $data['fournisseur'] ?? null,
            'reference_document' => $data['reference_document'] ?? null,
            'statut'             => StatutLot::ACTIF->value,
        ]);
    }

    /**
     * Calcule le statut correct d'un lot après modification.
     *
     * Règle :
     *   1. si quantité <= 0         → EPUISE
     *   2. sinon si date expirée    → EXPIRE
     *   3. sinon si BLOQUE          → on conserve BLOQUE
     *   4. sinon                     → ACTIF
     */
    private function calculerStatutLot(LotStock $lot): string
    {
        if ((float) $lot->quantite_actuelle <= 0) {
            return StatutLot::EPUISE->value;
        }

        if ($lot->estExpireSelonDate()) {
            return StatutLot::EXPIRE->value;
        }

        /*
         * On ne réactive JAMAIS un lot bloqué automatiquement.
         */
        if ($lot->estBloque()) {
            return StatutLot::BLOQUE->value;
        }

        return StatutLot::ACTIF->value;
    }

    /*
    |--------------------------------------------------------------------------
    | EMPLACEMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Charge et verrouille l'emplacement, s'il est fourni.
     *
     * @throws ValidationException
     */
    private function chargerEmplacement(mixed $emplacementId): ?EmplacementStock
    {
        if ($emplacementId === null || $emplacementId === '') {
            return null;
        }

        $emplacement = EmplacementStock::query()
            ->lockForUpdate()
            ->find((int) $emplacementId);

        if (!$emplacement) {
            throw ValidationException::withMessages([
                'emplacement_id' => 'L’emplacement sélectionné n’existe pas.',
            ]);
        }

        if (!$emplacement->estActif()) {
            throw ValidationException::withMessages([
                'emplacement_id' => 'L’emplacement sélectionné est inactif.',
            ]);
        }

        return $emplacement;
    }

    /*
    |--------------------------------------------------------------------------
    | UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Convertit une valeur en décimal robuste.
     *
     * Supporte :
     *   - "1500.50"
     *   - "1 500,50"
     *   - "1,500.50"
     *   - "1.500,50"
     */
    private function decimal(mixed $value): float
    {
        if (is_string($value)) {
            $value = trim($value);

            $value = str_replace(
                ["\xc2\xa0", ' '],
                '',
                $value
            );

            if (str_contains($value, ',')
                && str_contains($value, '.')
            ) {
                if (strrpos($value, ',') > strrpos($value, '.')) {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                } else {
                    $value = str_replace(',', '', $value);
                }
            } elseif (str_contains($value, ',')) {
                $value = str_replace(',', '.', $value);
            }
        }

        return round((float) $value, 3);
    }
}