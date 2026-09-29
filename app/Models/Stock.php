<?php

namespace App\Models;

use App\Enums\StatutStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Article du catalogue de stock.
 *
 * IMPORTANT — SÉMANTIQUE :
 *
 *   Ce modèle représente UNIQUEMENT la fiche descriptive
 *   d'un article. Il ne contient AUCUNE quantité.
 *
 *   Les quantités réelles sont portées par les lots
 *   (`LotStock`), et leur historique par les mouvements
 *   (`MouvementStock`).
 *
 *   La quantité physique d'un article est calculée
 *   dynamiquement à partir de ses lots :
 *
 *     physique    = somme quantite_actuelle
 *                 des lots ACTIF + BLOQUE
 *
 *     réservée    = somme (quantite_reservee - quantite_consommee)
 *                 des lignes de réservations actives
 *
 *     disponible  = physique - réservée  (>= 0)
 *
 *   Le statut `statut` (actif / inactif / archive) est un état
 *   ADMINISTRATIF, il ne reflète PAS un niveau de quantité.
 */
class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    /**
     * Attributs assignables en masse.
     *
     * On exclut volontairement :
     *   - toute quantité (calculée) ;
     *   - tout statut opérationnel (géré par les services).
     */
    protected $fillable = [
        'reference',
        'designation',
        'categorie',
        'unite',
        'seuil_alerte',
        'emplacement_principal_id',
        'description',
        'statut',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'statut'          => StatutStock::class,
            'seuil_alerte'    => 'decimal:3',
            'created_at'      => 'datetime',
            'updated_at'      => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Emplacement principal de l'article.
     */
    public function emplacementPrincipal(): BelongsTo
    {
        return $this->belongsTo(
            EmplacementStock::class,
            'emplacement_principal_id'
        );
    }

    /**
     * Lots de l'article.
     */
    public function lots(): HasMany
    {
        return $this->hasMany(LotStock::class, 'stock_id');
    }

    /**
     * Mouvements de l'article.
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'stock_id');
    }

    /**
     * Lignes de réservation concernant cet article.
     */
    public function lignesReservations(): HasMany
    {
        return $this->hasMany(LigneReservationStock::class, 'stock_id');
    }

    /**
     * Lignes de bon de sortie concernant cet article.
     */
    public function lignesBonsSortie(): HasMany
    {
        return $this->hasMany(LigneBonSortie::class, 'stock_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — QUANTITÉS
    |--------------------------------------------------------------------------
    |
    | Ces méthodes sont STRICTEMENT en lecture.
    | Elles ne modifient JAMAIS le stock.
    |
    | Toute modification passe obligatoirement par un Service
    | métier, dans une transaction, avec verrouillage.
    |
    */

    /**
     * Quantité physique réellement présente en stock.
     *
     * Calculée à partir des lots ACTIF et BLOQUE.
     *
     * On inclut BLOQUE car un lot bloqué reste physiquement
     * présent (il n'est juste pas utilisable pour une sortie).
     */
    public function quantitePhysique(): float
    {
        return (float) $this->lots()
            ->whereIn('statut', [
                \App\Enums\StatutLot::ACTIF->value,
                \App\Enums\StatutLot::BLOQUE->value,
            ])
            ->sum('quantite_actuelle');
    }

    /**
     * Quantité réservée active.
     *
     * Seules les réservations confirmées ou partielles sont
     * considérées comme actives. Le solde pris en compte est :
     *
     *   quantite_reservee - quantite_consommee
     *
     * pour ne pas compter les quantités déjà sorties.
     */
    public function quantiteReservee(): float
    {
        return (float) $this->lignesReservations()
            ->whereHas('reservation', function (Builder $q) {
                $q->whereIn('statut', [
                    \App\Enums\StatutReservation::CONFIRMEE->value,
                    \App\Enums\StatutReservation::PARTIELLE->value,
                ]);
            })
            ->selectRaw(
                'COALESCE(SUM(quantite_reservee - quantite_consommee), 0) AS total'
            )
            ->value('total');
    }

    /**
     * Quantité disponible.
     *
     *   disponible = physique - réservée
     *
     * Bornée à zéro pour ne jamais exposer de négatif.
     */
    public function quantiteDisponible(): float
    {
        return max(
            0.0,
            $this->quantitePhysique() - $this->quantiteReservee()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTATS
    |--------------------------------------------------------------------------
    */

    public function estActif(): bool
    {
        return $this->statut === StatutStock::ACTIF;
    }

    public function estInactif(): bool
    {
        return $this->statut === StatutStock::INACTIF;
    }

    public function estArchive(): bool
    {
        return $this->statut === StatutStock::ARCHIVE;
    }

    /**
     * Rupture : aucune quantité disponible.
     */
    public function estEnRupture(): bool
    {
        return $this->quantiteDisponible() <= 0;
    }

    /**
     * Sous le seuil d'alerte (si un seuil est configuré).
     */
    public function estSousSeuil(): bool
    {
        if ($this->seuil_alerte === null) {
            return false;
        }

        return $this->quantiteDisponible()
            <= (float) $this->seuil_alerte;
    }

    /**
     * L'article peut-il être utilisé dans une nouvelle opération ?
     *
     * Seul un article ACTIF peut être utilisé.
     */
    public function peutEtreUtilise(): bool
    {
        return $this->estActif();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where(
            'statut',
            StatutStock::ACTIF->value
        );
    }

    public function scopeArchives(Builder $query): Builder
    {
        return $query->where(
            'statut',
            StatutStock::ARCHIVE->value
        );
    }

    public function scopeCategorie(
        Builder $query,
        ?string $categorie
    ): Builder {
        if (!$categorie) {
            return $query;
        }

        return $query->where('categorie', $categorie);
    }

    /**
     * Recherche par référence, désignation ou description.
     */
    public function scopeRecherche(
        Builder $query,
        ?string $recherche
    ): Builder {
        $recherche = $recherche !== null
            ? trim($recherche)
            : null;

        if ($recherche === null || $recherche === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($recherche) {
            $q->where('reference', 'like', "%{$recherche}%")
              ->orWhere('designation', 'like', "%{$recherche}%")
              ->orWhere('description', 'like', "%{$recherche}%");
        });
    }
}