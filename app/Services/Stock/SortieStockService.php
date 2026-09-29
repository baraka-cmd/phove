<?php

namespace App\Services\Stock;

use App\Enums\NatureMouvement;
use App\Enums\StatutBonSortie;
use App\Enums\StatutLot;
use App\Enums\TypeMouvement;
use App\Models\BonSortie;
use App\Models\LigneBonSortie;
use App\Models\LotStock;
use App\Models\MouvementStock;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SortieStockService
{
    /**
     * Prépare la requête des bons de sortie.
     *
     * Cette méthode centralise les filtres utilisés
     * par la liste des bons de sortie.
     *
     * Le contrôleur reste responsable uniquement
     * de récupérer les paramètres HTTP et d'afficher
     * la vue.
     */
    public function requeteBons(
        ?string $recherche = null,
        ?string $statut = null,
        ?int $projetId = null,
        ?string $dateDebut = null,
        ?string $dateFin = null
    ): Builder {
        $query = BonSortie::query();

        /*
         * Recherche générale.
         */
        if ($recherche !== null && trim($recherche) !== '') {
            $recherche = trim($recherche);

            $query->where(function (Builder $q) use ($recherche) {
                $q->where(
                    'numero',
                    'like',
                    "%{$recherche}%"
                )
                    ->orWhere(
                        'reference',
                        'like',
                        "%{$recherche}%"
                    )
                    ->orWhere(
                        'motif',
                        'like',
                        "%{$recherche}%"
                    )
                    ->orWhere(
                        'notes',
                        'like',
                        "%{$recherche}%"
                    );
            });
        }

        /*
         * Filtre par statut.
         */
        if ($statut !== null && trim($statut) !== '') {
            $query->where(
                'statut',
                trim($statut)
            );
        }

        /*
         * Filtre par projet.
         */
        if ($projetId !== null && $projetId > 0) {
            $query->where(
                'projet_id',
                $projetId
            );
        }

        /*
         * Filtre par date de sortie.
         */
        if ($dateDebut !== null && trim($dateDebut) !== '') {
            $query->whereDate(
                'date_sortie',
                '>=',
                $dateDebut
            );
        }

        if ($dateFin !== null && trim($dateFin) !== '') {
            $query->whereDate(
                'date_sortie',
                '<=',
                $dateFin
            );
        }

        return $query;
    }

    /**
     * Crée un bon de sortie avec ses lignes.
     *
     * IMPORTANT :
     *
     * Cette méthode NE MODIFIE PAS le stock.
     *
     * Elle :
     *
     * 1. valide les données métier minimales ;
     * 2. génère le numéro ;
     * 3. crée le bon ;
     * 4. crée ses lignes ;
     * 5. initialise le bon en brouillon.
     *
     * La modification physique des lots et la création
     * des mouvements sont exclusivement réalisées
     * par effectuer().
     *
     * @throws Throwable
     */
    public function creerBon(
        array $data,
        ?int $utilisateurId = null
    ): BonSortie {
        return DB::transaction(function () use (
            $data,
            $utilisateurId
        ) {
            /*
             * Les lignes sont traitées séparément
             * des données principales du bon.
             */
            $lignes = $data['lignes'] ?? [];

            unset($data['lignes']);

            /*
             * Un bon sans ligne n'a aucun sens.
             */
            if (!is_array($lignes) || empty($lignes)) {
                throw ValidationException::withMessages([
                    'lignes' =>
                        'Le bon de sortie doit contenir au moins un article.',
                ]);
            }

            /*
             * Génération serveur du numéro métier.
             *
             * Le numéro ne doit jamais venir du formulaire.
             */
            $numero = $this->genererNumero();

            /*
             * Date de demande.
             *
             * Elle correspond à la création de la demande.
             */
            $dateDemande =
                $data['date_demande']
                ?? now()->toDateString();

            /*
             * Préparation des données du bon.
             *
             * Le statut est imposé côté serveur.
             */
            $bonData = [
                'numero' =>
                    $numero,

                'demandeur_id' =>
                    $data['demandeur_id']
                    ?? $utilisateurId,

                'projet_id' =>
                    $data['projet_id']
                    ?? null,

                'date_demande' =>
                    $dateDemande,

                /*
                 * Aucune sortie physique n'existe encore.
                 */
                'date_sortie' =>
                    null,

                'reference' =>
                    $data['reference']
                    ?? null,

                'motif' =>
                    $data['motif']
                    ?? null,

                'notes' =>
                    $data['notes']
                    ?? null,

                /*
                 * Tout nouveau bon commence en brouillon.
                 */
                'statut' =>
                    StatutBonSortie::BROUILLON->value,
            ];

            /*
             * Le demandeur doit être connu.
             */
            if (
                empty($bonData['demandeur_id'])
                || !is_numeric($bonData['demandeur_id'])
            ) {
                throw ValidationException::withMessages([
                    'demandeur_id' =>
                        'Le demandeur du bon de sortie est obligatoire.',
                ]);
            }

            /*
             * Création du bon.
             *
             * Aucune quantité de stock n'est modifiée.
             */
            $bon = BonSortie::create(
                $bonData
            );

            /*
             * Articles déjà présents dans ce bon.
             *
             * Cette vérification est faite en mémoire afin
             * de produire un message métier clair avant
             * la contrainte UNIQUE de la base.
             */
            $stocksDejaAjoutes = [];

            /*
             * Création des lignes.
             */
            foreach ($lignes as $index => $ligne) {
                if (!is_array($ligne)) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}" =>
                            'La ligne du bon de sortie est invalide.',
                    ]);
                }

                /*
                 * Article.
                 */
                $stockId = $ligne['stock_id'] ?? null;

                if (
                    $stockId === null
                    || $stockId === ''
                    || !is_numeric($stockId)
                ) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.stock_id" =>
                            'L’article de la ligne est obligatoire.',
                    ]);
                }

                $stockId = (int) $stockId;

                /*
                 * Quantité demandée.
                 */
                $quantiteDemandee =
                    $ligne['quantite_demandee']
                    ?? null;

                if (
                    $quantiteDemandee === null
                    || $quantiteDemandee === ''
                    || !is_numeric($quantiteDemandee)
                ) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.quantite_demandee" =>
                            'La quantité demandée est obligatoire et doit être numérique.',
                    ]);
                }

                $quantiteDemandee =
                    round(
                        (float) $quantiteDemandee,
                        2
                    );

                if ($quantiteDemandee <= 0) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.quantite_demandee" =>
                            'La quantité demandée doit être supérieure à zéro.',
                    ]);
                }

                /*
                 * Empêche le même article d'être ajouté
                 * plusieurs fois dans le même bon.
                 */
                if (isset($stocksDejaAjoutes[$stockId])) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.stock_id" =>
                            'Un même article ne peut apparaître qu’une seule fois dans un bon de sortie.',
                    ]);
                }

                $stocksDejaAjoutes[$stockId] = true;

                /*
                 * Vérification de l'existence de l'article.
                 *
                 * Cette lecture ne modifie pas le stock.
                 */
                $stock = Stock::query()
                    ->whereKey($stockId)
                    ->first();

                if (!$stock) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.stock_id" =>
                            sprintf(
                                'L’article de stock #%d est introuvable.',
                                $stockId
                            ),
                    ]);
                }

                /*
                 * Unité.
                 *
                 * Si elle est fournie par la requête, elle est
                 * utilisée. Sinon, on reprend celle du stock.
                 */
                $unite =
                    $ligne['unite']
                    ?? $stock->unite;

                if (
                    $unite === null
                    || trim((string) $unite) === ''
                ) {
                    throw ValidationException::withMessages([
                        "lignes.{$index}.unite" =>
                            'L’unité de l’article est obligatoire.',
                    ]);
                }

                /*
                 * Création de la ligne.
                 *
                 * La quantité validée est initialisée avec
                 * la quantité demandée car la base de données
                 * interdit NULL sur cette colonne.
                 *
                 * Aucune quantité physique n'est encore sortie.
                 */
                LigneBonSortie::create([
                    'bon_sortie_id' =>
                        $bon->id,

                    'stock_id' =>
                        $stock->id,

                    'quantite_demandee' =>
                        $quantiteDemandee,

                    'quantite_validee' =>
                        $quantiteDemandee,

                    'quantite_sortie' =>
                        0,

                    'unite' =>
                        trim((string) $unite),

                    'notes' =>
                        isset($ligne['notes'])
                        && $ligne['notes'] !== null
                            ? trim((string) $ligne['notes'])
                            : null,
                ]);
            }

            /*
             * Recharge le bon avec ses lignes.
             */
            $bon->load('lignes');

            return $bon;
        });
    }

    /**
     * Génère un numéro métier unique.
     *
     * Exemple :
     *
     * BS-20260911-A7F3K9
     */
    private function genererNumero(): string
    {
        do {
            $numero =
                'BS-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(
                    Str::random(6)
                );
        } while (
            BonSortie::query()
                ->where(
                    'numero',
                    $numero
                )
                ->exists()
        );

        return $numero;
    }

    /**
     * Effectue la sortie physique complète d'un bon.
     *
     * Workflow :
     *
     * 1. verrouiller le bon ;
     * 2. vérifier son statut ;
     * 3. verrouiller les lignes ;
     * 4. verrouiller les stocks ;
     * 5. calculer le stock physique ;
     * 6. calculer le stock réservé ;
     * 7. calculer le stock disponible ;
     * 8. vérifier la disponibilité ;
     * 9. sélectionner les lots en FEFO ;
     * 10. créer les mouvements ;
     * 11. décrémenter les lots ;
     * 12. mettre à jour les lignes ;
     * 13. marquer le bon comme sorti.
     *
     * @throws Throwable
     */
    public function effectuer(
        BonSortie $bonSortie,
        ?int $utilisateurId = null
    ): BonSortie {
        return DB::transaction(function () use (
            $bonSortie,
            $utilisateurId
        ) {
            /*
             * Verrouillage pessimiste du bon.
             */
            $bon = BonSortie::query()
                ->whereKey($bonSortie->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Vérification de l'état du workflow.
             */
            $this->verifierEtatBon($bon);

            /*
             * Verrouillage des lignes.
             */
            $lignes = LigneBonSortie::query()
                ->where(
                    'bon_sortie_id',
                    $bon->id
                )
                ->lockForUpdate()
                ->get();

            if ($lignes->isEmpty()) {
                throw ValidationException::withMessages([
                    'bon_sortie' =>
                        'Le bon de sortie ne contient aucune ligne.',
                ]);
            }

            /*
             * Traitement de chaque article.
             */
            foreach ($lignes as $ligne) {
                $this->effectuerLigne(
                    $bon,
                    $ligne,
                    $utilisateurId
                );
            }

            /*
             * Toutes les lignes ont été exécutées.
             */
            $bon->update([
                'statut' =>
                    StatutBonSortie::SORTI->value,

                'date_sortie' =>
                    now(),
            ]);

            /*
             * Retourne une instance fraîche.
             */
            $bon->refresh();

            return $bon;
        });
    }

    /**
     * Effectue la sortie physique d'une ligne.
     */
    private function effectuerLigne(
        BonSortie $bon,
        LigneBonSortie $ligne,
        ?int $utilisateurId
    ): void {
        /*
         * Quantité officiellement autorisée.
         */
        $quantite =
            $this->quantiteAExecuter($ligne);

        if ($quantite <= 0) {
            throw ValidationException::withMessages([
                'quantite' =>
                    sprintf(
                        'La quantité à sortir pour la ligne #%d doit être supérieure à zéro.',
                        $ligne->id
                    ),
            ]);
        }

        /*
         * Verrouillage du stock.
         *
         * Aucun "where actif" ici :
         * la colonne "actif" n'existe pas dans
         * la table stocks de notre schéma actuel.
         */
        $stock = Stock::query()
            ->whereKey($ligne->stock_id)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            throw ValidationException::withMessages([
                'stock' =>
                    sprintf(
                        'Le stock #%d associé à la ligne #%d est introuvable.',
                        $ligne->stock_id,
                        $ligne->id
                    ),
            ]);
        }

        /*
         * Calcul du stock physique à partir des lots.
         */
        $stockPhysique =
            $this->calculerStockPhysique(
                $stock
            );

        /*
         * Calcul des quantités réservées.
         */
        $stockReserve =
            $this->calculerStockReserve(
                $stock
            );

        /*
         * Stock réellement disponible.
         */
        $stockDisponible = max(
            0,
            round(
                $stockPhysique
                - $stockReserve,
                2
            )
        );

        /*
         * Protection contre le dépassement.
         */
        if ($quantite > $stockDisponible) {
            throw ValidationException::withMessages([
                'quantite' =>
                    sprintf(
                        'Stock insuffisant pour "%s". Demandé : %s %s. Stock disponible : %s %s.',
                        $stock->designation,
                        $this->formatQuantite($quantite),
                        $stock->unite,
                        $this->formatQuantite($stockDisponible),
                        $stock->unite
                    ),
            ]);
        }

        /*
         * Sélection FEFO.
         */
        $lots =
            $this->selectionnerLotsFEFO(
                $stock,
                $quantite
            );

        /*
         * Quantité restante à sortir.
         */
        $reste = $quantite;

        /*
         * Consommation des lots.
         */
        foreach ($lots as $lot) {
            if ($reste <= 0) {
                break;
            }

            /*
             * Quantité actuellement disponible dans le lot.
             */
            $quantiteLot =
                round(
                    (float) $lot->quantite_actuelle,
                    2
                );

            if ($quantiteLot <= 0) {
                continue;
            }

            /*
             * Quantité prélevée dans ce lot.
             */
            $quantiteSortie =
                min(
                    $reste,
                    $quantiteLot
                );

            /*
             * Création du mouvement historique.
             */
            $this->creerMouvement(
                $bon,
                $stock,
                $lot,
                $quantiteSortie,
                $utilisateurId
            );

            /*
             * Nouvelle quantité du lot.
             */
            $nouvelleQuantite =
                round(
                    $quantiteLot
                    - $quantiteSortie,
                    2
                );

            /*
             * Protection contre une valeur négative
             * due aux arrondis.
             */
            $nouvelleQuantite =
                max(
                    0,
                    $nouvelleQuantite
                );

            $lot->quantite_actuelle =
                $nouvelleQuantite;

            /*
             * Mise à jour du statut du lot.
             */
            if ($nouvelleQuantite <= 0) {
                $lot->statut =
                    StatutLot::EPUISE->value;
            } elseif (
                $lot->date_expiration !== null
                && $lot->date_expiration
                    < now()->toDateString()
            ) {
                $lot->statut =
                    StatutLot::EXPIRE->value;
            } else {
                $lot->statut =
                    StatutLot::ACTIF->value;
            }

            /*
             * Persistance.
             */
            $lot->save();

            /*
             * Mise à jour du reste.
             */
            $reste =
                round(
                    $reste
                    - $quantiteSortie,
                    2
                );
        }

        /*
         * Une sortie partielle silencieuse est interdite.
         */
        if ($reste > 0) {
            throw ValidationException::withMessages([
                'quantite' =>
                    sprintf(
                        'La sortie du stock "%s" n’a pas pu être exécutée entièrement. Reste : %s %s.',
                        $stock->designation,
                        $this->formatQuantite($reste),
                        $stock->unite
                    ),
            ]);
        }

        /*
         * Mise à jour de la ligne.
         */
        $ligne->update([
            'quantite_sortie' =>
                $quantite,
        ]);
    }

    /**
     * Sélectionne les lots selon FEFO.
     *
     * First Expired, First Out.
     */
    private function selectionnerLotsFEFO(
        Stock $stock,
        float $quantiteDemandee
    ): Collection {
        $lots = LotStock::query()
            ->where(
                'stock_id',
                $stock->id
            )

            /*
             * Seuls les lots avec une quantité positive
             * peuvent être consommés.
             */
            ->where(
                'quantite_actuelle',
                '>',
                0
            )

            /*
             * Seuls les lots actifs sont sélectionnables.
             */
            ->where(
                'statut',
                StatutLot::ACTIF->value
            )

            /*
             * Un lot déjà expiré n'est pas sélectionné.
             */
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'date_expiration'
                    )
                    ->orWhereDate(
                        'date_expiration',
                        '>=',
                        now()->toDateString()
                    );
            })

            /*
             * Les lots périssables passent en premier.
             */
            ->orderByRaw(
                'CASE
                    WHEN date_expiration IS NULL THEN 1
                    ELSE 0
                 END'
            )

            /*
             * Date d'expiration la plus proche.
             */
            ->orderBy(
                'date_expiration'
            )

            /*
             * En cas d'égalité :
             * lot entré le plus tôt.
             */
            ->orderBy(
                'date_entree'
            )

            /*
             * Critère déterministe final.
             */
            ->orderBy(
                'id'
            )

            /*
             * Verrouillage des lots.
             */
            ->lockForUpdate()

            ->get();

        /*
         * Capacité totale réelle des lots.
         */
        $totalDisponible =
            round(
                $lots->sum(
                    fn (LotStock $lot) =>
                        (float) $lot->quantite_actuelle
                ),
                2
            );

        if (
            $totalDisponible
            < $quantiteDemandee
        ) {
            throw ValidationException::withMessages([
                'stock' =>
                    sprintf(
                        'Les lots disponibles de "%s" ne permettent pas de satisfaire cette sortie.',
                        $stock->designation
                    ),
            ]);
        }

        return $lots;
    }

    /**
     * Calcule le stock physique à partir des lots.
     *
     * Les lots actifs et bloqués représentent encore
     * une quantité physiquement présente.
     */
    private function calculerStockPhysique(
        Stock $stock
    ): float {
        return round(
            (float) LotStock::query()
                ->where(
                    'stock_id',
                    $stock->id
                )
                ->whereIn(
                    'statut',
                    [
                        StatutLot::ACTIF->value,
                        StatutLot::BLOQUE->value,
                    ]
                )
                ->sum(
                    'quantite_actuelle'
                ),
            2
        );
    }

    /**
     * Calcule la quantité réservée pour un article.
     *
     * Une réservation confirmée ou partielle
     * bloque la quantité non encore consommée.
     */
    private function calculerStockReserve(
        Stock $stock
    ): float {
        $resultat = DB::table(
            'lignes_reservations_stocks'
        )
            ->join(
                'reservations_stocks',
                'reservations_stocks.id',
                '=',
                'lignes_reservations_stocks.reservation_stock_id'
            )
            ->where(
                'lignes_reservations_stocks.stock_id',
                $stock->id
            )
            ->whereIn(
                'reservations_stocks.statut',
                [
                    'confirmee',
                    'partielle',
                ]
            )
            ->selectRaw(
                'COALESCE(
                    SUM(
                        GREATEST(
                            COALESCE(
                                lignes_reservations_stocks.quantite_reservee,
                                0
                            )
                            -
                            COALESCE(
                                lignes_reservations_stocks.quantite_consommee,
                                0
                            ),
                            0
                        )
                    ),
                    0
                ) AS total_reserve'
            )
            ->value(
                'total_reserve'
            );

        return round(
            (float) (
                $resultat ?? 0
            ),
            2
        );
    }

    /**
     * Crée un mouvement de sortie.
     *
     * Un mouvement validé constitue une trace historique.
     */
    private function creerMouvement(
        BonSortie $bon,
        Stock $stock,
        LotStock $lot,
        float $quantite,
        ?int $utilisateurId
    ): MouvementStock {
        return MouvementStock::create([
            'stock_id' =>
                $stock->id,

            'lot_stock_id' =>
                $lot->id,

            'emplacement_id' =>
                $stock->emplacement_principal_id,

            'type' =>
                TypeMouvement::SORTIE->value,

            'nature' =>
                NatureMouvement::DISTRIBUTION->value,

            'quantite' =>
                round(
                    $quantite,
                    2
                ),

            'date_mouvement' =>
                now(),

            'reference_document' =>
                $bon->numero,

            'bon_sortie_id' =>
                $bon->id,

            'projet_id' =>
                $bon->projet_id,

            'utilisateur_id' =>
                $utilisateurId,

            'motif' =>
                $bon->motif,

            'notes' =>
                $bon->notes,

            /*
             * Le mouvement de sortie est immédiatement
             * validé puisque nous sommes dans effectuer().
             */
            'statut' =>
                'valide',
        ]);
    }



        /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    |
    | Comptage des bons de sortie par statut.
    |
    | Le paramètre $statut (optionnel) permet de restreindre le calcul
    | au périmètre filtré par la vue.
    |
    | Clés retournées (alignées sur la vue sorties/index.blade.php) :
    |   - total
    |   - brouillon
    |   - soumis
    |   - valide
    |   - en_preparation
    |   - sorti
    |   - annule
    */

    /**
     * Retourne les statistiques des bons de sortie.
     *
     * @param string|null $statut Filtre optionnel sur le statut courant.
     * @return array<string, int>
     */
    public function statistiques(?string $statut = null): array
    {
        $query = BonSortie::query();

        /*
         * Filtre optionnel : si la vue est filtrée sur un statut,
         * les compteurs restent cohérents avec le périmètre affiché.
         */
        if ($statut !== null && trim($statut) !== '') {
            $query->where('statut', trim($statut));
        }

        return [
            'total'          => (clone $query)->count(),
            'brouillon'      => (clone $query)->where(
                'statut',
                StatutBonSortie::BROUILLON->value
            )->count(),
            'soumis'         => (clone $query)->where(
                'statut',
                StatutBonSortie::SOUMIS->value
            )->count(),
            'valide'         => (clone $query)->where(
                'statut',
                StatutBonSortie::VALIDE->value
            )->count(),
            'en_preparation' => (clone $query)->where(
                'statut',
                StatutBonSortie::EN_PREPARATION->value
            )->count(),
            'sorti'          => (clone $query)->where(
                'statut',
                StatutBonSortie::SORTI->value
            )->count(),
            'annule'         => (clone $query)->where(
                'statut',
                StatutBonSortie::ANNULE->value
            )->count(),
        ];
    }

    
    /**
     * Vérifie que le bon peut être exécuté.
     */
    private function verifierEtatBon(
        BonSortie $bon
    ): void {
        $statutsAutorises = [
            StatutBonSortie::VALIDE->value,
            StatutBonSortie::EN_PREPARATION->value,
        ];

        if (
            !in_array(
                $bon->statut,
                $statutsAutorises,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'bon_sortie' =>
                    sprintf(
                        'Le bon de sortie "%s" ne peut pas être exécuté dans son état actuel.',
                        $bon->numero
                    ),
            ]);
        }
    }

    /**
     * Détermine la quantité à exécuter.
     *
     * La quantité validée est prioritaire.
     */
    private function quantiteAExecuter(
        LigneBonSortie $ligne
    ): float {
        /*
         * Comme quantite_validee est obligatoire dans
         * la base, elle doit normalement toujours être
         * disponible.
         */
        $quantite =
            $ligne->quantite_validee;

        /*
         * Sécurité supplémentaire :
         * si jamais l'ancienne donnée est incohérente,
         * on retombe sur la quantité demandée.
         */
        if (
            $quantite === null
            || $quantite === ''
        ) {
            $quantite =
                $ligne->quantite_demandee;
        }

        return round(
            (float) $quantite,
            2
        );
    }

    /**
 * Prépare le résumé complet d'un bon de sortie.
 *
 * Cette méthode est destinée principalement aux vues
 * de consultation du bon.
 *
 * Elle ne modifie aucune donnée.
 *
 * Elle retourne notamment :
 * - le nombre de lignes ;
 * - la quantité demandée ;
 * - la quantité validée ;
 * - la quantité déjà sortie ;
 * - le pourcentage d'exécution ;
 * - le montant/état global lorsque disponible.
 */
/**
 * Prépare le résumé complet d'un bon de sortie.
 *
 * Cette méthode est utilisée par la page de détail
 * du bon de sortie.
 *
 * Elle ne modifie aucune donnée.
 *
 * @return array<string, mixed>
 */
public function resumeBon(
    BonSortie $bonSortie
): array {
    /*
     * Recharge les lignes et les stocks associés.
     */
    $bonSortie->loadMissing([
        'lignes.stock',
    ]);

    /*
     * Récupération des lignes.
     */
    $lignes = $bonSortie->lignes;

    /*
     * Nombre total de lignes.
     */
    $nombreLignes = $lignes->count();

    /*
     * Quantité totale demandée.
     */
    $quantiteDemandee = round(
        (float) $lignes->sum(
            fn (LigneBonSortie $ligne): float =>
                (float) $ligne->quantite_demandee
        ),
        2
    );

    /*
     * Quantité totale validée.
     *
     * Dans notre schéma actuel, cette colonne
     * est obligatoire.
     */
    $quantiteValidee = round(
        (float) $lignes->sum(
            fn (LigneBonSortie $ligne): float =>
                (float) $ligne->quantite_validee
        ),
        2
    );

    /*
     * Quantité réellement sortie.
     */
    $quantiteSortie = round(
        (float) $lignes->sum(
            fn (LigneBonSortie $ligne): float =>
                (float) $ligne->quantite_sortie
        ),
        2
    );

    /*
     * Quantité restante à sortir.
     */
    $resteASortir = max(
        0,
        round(
            $quantiteValidee - $quantiteSortie,
            2
        )
    );

    /*
     * Pourcentage d'exécution.
     */
    $pourcentageExecution = 0.0;

    if ($quantiteValidee > 0) {
        $pourcentageExecution = round(
            min(
                100,
                (
                    $quantiteSortie
                    / $quantiteValidee
                ) * 100
            ),
            2
        );
    }

    /*
     * Le bon est entièrement exécuté.
     */
    $entierementSorti =
        $quantiteValidee > 0
        && $resteASortir <= 0;

    /*
     * Le bon est partiellement exécuté.
     */
    $partiellementSorti =
        $quantiteSortie > 0
        && $resteASortir > 0;

    /*
     * Aucune sortie n'a encore été effectuée.
     */
    $nonCommence =
        $quantiteSortie <= 0;

    /*
     * IMPORTANT :
     *
     * Le modèle peut retourner un Enum StatutBonSortie
     * grâce à son cast.
     *
     * Le Blade, lui, utilise le statut comme clé d'un
     * tableau PHP. Il faut donc lui fournir une chaîne.
     */
    $statut = $bonSortie->statut;

    if ($statut instanceof StatutBonSortie) {
        $statut = $statut->value;
    } elseif ($statut !== null) {
        $statut = (string) $statut;
    } else {
        $statut = '';
    }

    /*
     * Résumé final.
     */
    return [
        /*
         * Informations générales.
         */
        'nombre_lignes' =>
            $nombreLignes,

        'statut' =>
            $statut,

        'numero' =>
            $bonSortie->numero,

        'date_demande' =>
            $bonSortie->date_demande,

        'date_sortie' =>
            $bonSortie->date_sortie,

        /*
         * Quantités.
         */
        'quantite_demandee' =>
            $quantiteDemandee,

        'quantite_validee' =>
            $quantiteValidee,

        'quantite_sortie' =>
            $quantiteSortie,

        'reste_a_sortir' =>
            $resteASortir,

        /*
         * Avancement.
         */
        'pourcentage_execution' =>
            $pourcentageExecution,

        'entierement_sorti' =>
            $entierementSorti,

        'partiellement_sorti' =>
            $partiellementSorti,

        'non_commence' =>
            $nonCommence,
    ];
}

    /**
     * Soumet un bon de sortie (passe son statut de brouillon à soumis).
     *
     * @throws Throwable
     */
        public function soumettreBon(
        BonSortie $bonSortie,
        ?int $utilisateurId = null
    ): BonSortie {
        return DB::transaction(function () use ($bonSortie) {
            /*
             * Verrouillage pessimiste pour éviter les conflits.
             */
            $bon = BonSortie::query()
                ->whereKey($bonSortie->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Normalisation sécurisée du statut (qu'il soit un objet Enum ou une chaîne).
             */
            $statutActuel = $bon->statut;
            if ($statutActuel instanceof StatutBonSortie) {
                $statutActuel = $statutActuel->value;
            } else {
                $statutActuel = (string) $statutActuel;
            }

            /*
             * Vérification que le bon est bien en brouillon.
             */
            if ($statutActuel !== StatutBonSortie::BROUILLON->value) {
                throw ValidationException::withMessages([
                    'bon_sortie' => 'Seul un bon à l’état de brouillon peut être soumis.',
                ]);
            }

            /*
             * Passage au statut soumis.
             */
            $bon->update([
                'statut' => StatutBonSortie::SOUMIS->value,
            ]);

            $bon->refresh();

            return $bon;
        });
    }



    /**
     * Formate une quantité pour les messages.
     */
    private function formatQuantite(
        float $quantite
    ): string {
        return number_format(
            $quantite,
            2,
            ',',
            ' '
        );
    }
}