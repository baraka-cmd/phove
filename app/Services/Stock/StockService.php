<?php

namespace App\Services\Stock;

use App\Enums\StatutStock;
use App\Models\EmplacementStock;
use App\Models\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Service métier du catalogue de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce service gère UNIQUEMENT le catalogue (fiche article).
 *   Il ne touche JAMAIS aux quantités physiques, aux lots,
 *   aux mouvements ni aux réservations.
 *
 *   Les opérations de stock sont portées par leurs services
 *   dédiés :
 *     - EntreeStockService
 *     - SortieStockService
 *     - InventaireStockService
 *     - ReservationStockService
 *     - LotStockService
 *
 * SÉMANTIQUE DES QUANTITÉS :
 *
 *   Aucune quantité n'est stockée dans la table `stocks`.
 *
 *   - Quantité physique    = somme des quantite_actuelle
 *                            des lots ACTIF + BLOQUE.
 *   - Quantité réservée    = somme des (quantite_reservee - quantite_consommee)
 *                            des lignes de réservations actives.
 *   - Quantité disponible  = physique - réservée (>= 0).
 *
 * INVARIANTS :
 *
 *   - Le statut (actif / inactif / archive) est un état
 *     ADMINISTRATIF, pas un niveau de quantité.
 *   - Aucune écriture de quantité dans `stocks`.
 *   - Toute écriture est transactionnelle.
 *   - L'archivage n'est autorisé que si :
 *       - quantité physique = 0 ;
 *       - quantité réservée = 0.
 */
class StockService
{
    /*
    |--------------------------------------------------------------------------
    | LECTURE / RECHERCHE
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare la requête de recherche des articles.
     *
     * Retourne un Builder, laissant au Controller le soin
     * d'appliquer `with()`, `paginate()`, `withQueryString()`, etc.
     */
    public function requete(
        ?string $recherche = null,
        ?string $categorie = null,
        ?string $statut = null,
        ?bool $actif = null
    ): Builder {
        return Stock::query()
            ->when(
                $recherche,
                fn (Builder $q, string $r) => $q->recherche($r)
            )
            ->when(
                $categorie,
                fn (Builder $q, string $c) => $q->categorie($c)
            )
            ->when(
                $statut,
                fn (Builder $q, string $s) => $q->where('statut', $s)
            )
            ->when(
                $actif !== null,
                fn (Builder $q) => $q->where(
                    'statut',
                    $actif
                        ? StatutStock::ACTIF->value
                        : StatutStock::INACTIF->value
                )
            );
    }

    /**
     * Retourne les articles paginés pour la liste admin.
     *
     * Inclut :
     *   - l'emplacement principal ;
     *   - le nombre de lots ;
     *   - la somme des quantités actuelles des lots.
     *
     * `withSum('lots', 'quantite_actuelle')` évite N+1.
     */
    public function paginer(
        Builder $query,
        int $parPage = 15
    ): LengthAwarePaginator {
        return $query
            ->with('emplacementPrincipal')
            ->withCount('lots')
            ->withSum('lots', 'quantite_actuelle')
            ->orderBy('designation')
            ->paginate($parPage)
            ->withQueryString();
    }

    /**
     * Trouve un article par identifiant.
     */
    public function trouver(int $id): Stock
    {
        return Stock::query()
            ->with([
                'emplacementPrincipal',
                'lots',
            ])
            ->findOrFail($id);
    }

    /**
     * Trouve un article par référence.
     */
    public function trouverParReference(string $reference): Stock
    {
        return Stock::query()
            ->where('reference', $reference)
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION / MODIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Crée un nouvel article.
     *
     * Aucun lot, aucune quantité : uniquement la fiche catalogue.
     *
     * @throws ValidationException
     */
    public function creer(array $data): Stock
    {
        return DB::transaction(function () use ($data) {

            $this->verifierReferenceUnique($data['reference'] ?? null);

            /*
             * Vérification de l'emplacement principal s'il est fourni.
             */
            if (!empty($data['emplacement_principal_id'])) {
                $this->verifierEmplacement(
                    (int) $data['emplacement_principal_id']
                );
            }

            $stock = new Stock();

            $stock->reference    = $data['reference'];
            $stock->designation  = $data['designation'];
            $stock->categorie    = $data['categorie'];
            $stock->unite        = $data['unite'];

            $stock->seuil_alerte =
                $data['seuil_alerte'] ?? null;

            $stock->emplacement_principal_id =
                $data['emplacement_principal_id'] ?? null;

            $stock->description =
                $data['description'] ?? null;

            /*
             * Un nouvel article est ACTIF par défaut.
             */
            $stock->statut = StatutStock::ACTIF;

            $stock->save();

            return $stock->fresh('emplacementPrincipal');
        });
    }

    /**
     * Modifie les informations descriptives d'un article.
     *
     * Ne modifie JAMAIS le statut ni les quantités.
     *
     * @throws ValidationException
     */
    public function modifier(Stock $stock, array $data): Stock
    {
        return DB::transaction(function () use ($stock, $data) {

            $stock = Stock::query()
                ->lockForUpdate()
                ->findOrFail($stock->id);

            if ($stock->estArchive()) {
                throw ValidationException::withMessages([
                    'stock' => 'Un article archivé ne peut pas être modifié.',
                ]);
            }

            /*
             * Référence : unicité vérifiée uniquement si elle change.
             */
            if (
                array_key_exists('reference', $data)
                && $data['reference'] !== $stock->reference
            ) {
                $this->verifierReferenceUnique($data['reference']);
                $stock->reference = $data['reference'];
            }

            /*
             * Vérification de l'emplacement si modifié.
             */
            if (
                array_key_exists('emplacement_principal_id', $data)
                && $data['emplacement_principal_id']
                && (int) $data['emplacement_principal_id'] !== $stock->emplacement_principal_id
            ) {
                $this->verifierEmplacement(
                    (int) $data['emplacement_principal_id']
                );
                $stock->emplacement_principal_id = $data['emplacement_principal_id'];
            } elseif (array_key_exists('emplacement_principal_id', $data)
                && $data['emplacement_principal_id'] === null
            ) {
                $stock->emplacement_principal_id = null;
            }

            foreach ([
                'designation',
                'categorie',
                'unite',
                'seuil_alerte',
                'description',
            ] as $champ) {
                if (array_key_exists($champ, $data)) {
                    $stock->{$champ} = $data[$champ];
                }
            }

            $stock->save();

            return $stock->fresh('emplacementPrincipal');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | STATUT ADMINISTRATIF
    |--------------------------------------------------------------------------
    */

    /**
     * Active un article inactif.
     *
     * Idempotent : si l'article est déjà actif, on le retourne.
     *
     * @throws ValidationException
     */
    public function activer(Stock $stock): Stock
    {
        return DB::transaction(function () use ($stock) {

            $stock = Stock::query()
                ->lockForUpdate()
                ->findOrFail($stock->id);

            if ($stock->estArchive()) {
                throw ValidationException::withMessages([
                    'stock' => 'Un article archivé ne peut pas être réactivé.',
                ]);
            }

            if ($stock->estActif()) {
                return $stock;
            }

            $stock->statut = StatutStock::ACTIF;
            $stock->save();

            return $stock->fresh('emplacementPrincipal');
        });
    }

    /**
     * Désactive un article actif.
     *
     * Idempotent : si l'article est déjà inactif, on le retourne.
     *
     * @throws ValidationException
     */
    public function desactiver(Stock $stock): Stock
    {
        return DB::transaction(function () use ($stock) {

            $stock = Stock::query()
                ->lockForUpdate()
                ->findOrFail($stock->id);

            if ($stock->estArchive()) {
                throw ValidationException::withMessages([
                    'stock' => 'Un article archivé ne peut pas être désactivé.',
                ]);
            }

            if ($stock->estInactif()) {
                return $stock;
            }

            $stock->statut = StatutStock::INACTIF;
            $stock->save();

            return $stock->fresh('emplacementPrincipal');
        });
    }

    /**
     * Archive un article.
     *
     * Conditions STRICTES :
     *   - quantité physique = 0 ;
     *   - quantité réservée = 0.
     *
     * L'archivage est DÉFINITIF : un article archivé ne peut
     * plus être réactivé.
     *
     * @throws ValidationException
     */
    public function archiver(Stock $stock): Stock
    {
        return DB::transaction(function () use ($stock) {

            $stock = Stock::query()
                ->lockForUpdate()
                ->findOrFail($stock->id);

            if ($stock->estArchive()) {
                return $stock;
            }

            /*
             * Vérification quantité physique.
             */
            $physique = $stock->quantitePhysique();

            if ($physique > 0) {
                throw ValidationException::withMessages([
                    'stock' => sprintf(
                        'Impossible d’archiver "%s" : il reste %s %s en stock physique.',
                        $stock->designation,
                        $physique,
                        $stock->unite
                    ),
                ]);
            }

            /*
             * Vérification quantité réservée.
             */
            $reservee = $stock->quantiteReservee();

            if ($reservee > 0) {
                throw ValidationException::withMessages([
                    'stock' => sprintf(
                        'Impossible d’archiver "%s" : %s %s sont encore réservés.',
                        $stock->designation,
                        $reservee,
                        $stock->unite
                    ),
                ]);
            }

            $stock->statut = StatutStock::ARCHIVE;
            $stock->save();

            return $stock->fresh('emplacementPrincipal');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | QUANTITÉS (LECTURE SEULE)
    |--------------------------------------------------------------------------
    |
    | Ces méthodes sont strictement en LECTURE.
    | Elles délèguent au modèle Stock, qui reste la source de vérité.
    */

    /**
     * Quantité physique totale (somme des lots ACTIF + BLOQUE).
     */
    public function quantitePhysique(Stock $stock): float
    {
        return round($stock->quantitePhysique(), 3);
    }

    /**
     * Quantité réservée active.
     */
    public function quantiteReservee(Stock $stock): float
    {
        return round($stock->quantiteReservee(), 3);
    }

    /**
     * Quantité disponible (physique - réservée).
     */
    public function quantiteDisponible(Stock $stock): float
    {
        return round($stock->quantiteDisponible(), 3);
    }

    /**
     * Retourne le bloc complet des quantités et états d'un article.
     *
     * Utilisé notamment par la fiche détaillée d'un article.
     *
     * @return array<string, mixed>
     */
    public function quantites(Stock $stock): array
    {
        $physique   = $this->quantitePhysique($stock);
        $reservee   = $this->quantiteReservee($stock);
        $disponible = $this->quantiteDisponible($stock);

        return [
            'physique'       => $physique,
            'reservee'       => $reservee,
            'disponible'     => $disponible,
            'seuil_alerte'   => $stock->seuil_alerte !== null
                ? (float) $stock->seuil_alerte
                : null,
            'en_rupture'     => $stock->estEnRupture(),
            'sous_seuil'     => $stock->estSousSeuil(),
            'est_disponible' => $disponible > 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Statistiques propres à un article.
     *
     * @return array<string, mixed>
     */
    public function statistiquesArticle(Stock $stock): array
    {
        $quantites = $this->quantites($stock);

        $nombreLots          = $stock->lots()->count();
        $nombreMouvements    = $stock->mouvements()->count();
        $nombreReservations  = $stock->lignesReservations()->count();
        $nombreSorties       = $stock->lignesBonsSortie()->count();

        $pourcentageDisponible = 0.0;
        $pourcentageReserve    = 0.0;

        if ($quantites['physique'] > 0) {
            $pourcentageDisponible = round(
                ($quantites['disponible'] / $quantites['physique']) * 100,
                2
            );
            $pourcentageReserve = round(
                ($quantites['reservee'] / $quantites['physique']) * 100,
                2
            );
        }

        return [
            'reference'   => $stock->reference,
            'designation' => $stock->designation,
            'categorie'   => $stock->categorie,
            'unite'       => $stock->unite,

            'statut'      => $stock->statut instanceof StatutStock
                ? $stock->statut->value
                : (string) $stock->statut,

            'est_actif'   => $stock->estActif(),
            'est_inactif' => $stock->estInactif(),
            'est_archive' => $stock->estArchive(),

            'quantite_physique'   => $quantites['physique'],
            'quantite_reservee'   => $quantites['reservee'],
            'quantite_disponible' => $quantites['disponible'],
            'seuil_alerte'        => $quantites['seuil_alerte'],

            'en_rupture'     => $quantites['en_rupture'],
            'sous_seuil'     => $quantites['sous_seuil'],
            'est_disponible' => $quantites['est_disponible'],

            'pourcentage_disponible' => $pourcentageDisponible,
            'pourcentage_reserve'    => $pourcentageReserve,

            'nombre_lots'         => $nombreLots,
            'nombre_mouvements'   => $nombreMouvements,
            'nombre_reservations' => $nombreReservations,
            'nombre_sorties'      => $nombreSorties,
        ];
    }

    /**
     * Statistiques globales du catalogue.
     *
     * @return array<string, mixed>
     */
    public function statistiques(): array
    {
        $stocks = Stock::query()
            ->actifs()
            ->with('lots')
            ->get();

        $quantitePhysique   = 0.0;
        $quantiteReservee   = 0.0;
        $quantiteDisponible = 0.0;

        $ruptures      = 0;
        $stocksFaibles = 0;

        foreach ($stocks as $stock) {
            $physique   = $stock->quantitePhysique();
            $reservee   = $stock->quantiteReservee();
            $disponible = $stock->quantiteDisponible();

            $quantitePhysique   += $physique;
            $quantiteReservee   += $reservee;
            $quantiteDisponible += $disponible;

            if ($stock->estEnRupture()) {
                $ruptures++;
            }

            if ($stock->estSousSeuil()) {
                $stocksFaibles++;
            }
        }

        return [
            'articles_actifs' =>
                $stocks->count(),

            'articles_inactifs' =>
                Stock::query()
                    ->where('statut', StatutStock::INACTIF->value)
                    ->count(),

            'articles_archives' =>
                Stock::query()
                    ->where('statut', StatutStock::ARCHIVE->value)
                    ->count(),

            'ruptures' =>
                $ruptures,

            'stocks_faibles' =>
                $stocksFaibles,

            'quantite_physique' =>
                round($quantitePhysique, 3),

            'quantite_reservee' =>
                round($quantiteReservee, 3),

            'quantite_disponible' =>
                round($quantiteDisponible, 3),
        ];
    }

    /**
     * Articles en rupture de stock.
     */
    public function ruptures(): Collection
    {
        return Stock::query()
            ->actifs()
            ->with('emplacementPrincipal')
            ->get()
            ->filter(fn (Stock $s) => $s->estEnRupture())
            ->values();
    }

    /**
     * Articles sous leur seuil d'alerte.
     */
    public function stocksFaibles(): Collection
    {
        return Stock::query()
            ->actifs()
            ->with('emplacementPrincipal')
            ->get()
            ->filter(fn (Stock $s) => $s->estSousSeuil())
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie qu'une référence d'article n'existe pas déjà.
     *
     * @throws ValidationException
     */
    private function verifierReferenceUnique(?string $reference): void
    {
        if (!$reference || trim($reference) === '') {
            throw ValidationException::withMessages([
                'reference' => 'La référence est obligatoire.',
            ]);
        }

        $existe = Stock::query()
            ->where('reference', $reference)
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'reference' => sprintf(
                    'La référence "%s" est déjà utilisée.',
                    $reference
                ),
            ]);
        }
    }

    /**
     * Vérifie qu'un emplacement existe et est actif.
     *
     * @throws ValidationException
     */
    private function verifierEmplacement(int $emplacementId): void
    {
        $emplacement = EmplacementStock::query()
            ->find($emplacementId);

        if (!$emplacement) {
            throw ValidationException::withMessages([
                'emplacement_principal_id' =>
                    'L’emplacement sélectionné n’existe pas.',
            ]);
        }

        if (!$emplacement->estActif()) {
            throw ValidationException::withMessages([
                'emplacement_principal_id' =>
                    'L’emplacement sélectionné est inactif.',
            ]);
        }
    }
}