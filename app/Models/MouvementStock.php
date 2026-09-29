<?php

namespace App\Models;

use App\Enums\NatureMouvement;
use App\Enums\TypeMouvement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mouvement de stock — journal immuable.
 *
 * SÉMANTIQUE :
 *
 *   Un mouvement est un FAIT enregistré dans l'historique
 *   du stock. Il ne doit JAMAIS être modifié ni supprimé.
 *
 *   Chaque opération métier (entrée, sortie, ajustement,
 *   transfert) produit une ou plusieurs lignes dans cette
 *   table, dans la MÊME transaction que la modification
 *   physique du stock.
 *
 * RÔLE :
 *
 *   - Audit : qui, quoi, quand, combien, pourquoi.
 *   - Reconstitution de l'historique d'un article ou d'un lot.
 *   - Preuve métier en cas de contrôle ou de litige.
 *   - Source pour les rapports et KPI.
 *
 * INVARIANTS :
 *
 *   - `quantite` est TOUJOURS strictement positive.
 *   - Le SENS du mouvement est porté par `sens` :
 *       +1  → augmentation du stock (entrée)
 *       -1  → diminution du stock (sortie, manquant d'inventaire)
 *        0  → neutre (transfert interne)
 *   - Le TYPE (`type`) décrit la mécanique comptable.
 *   - La NATURE (`nature`) décrit le motif métier.
 *   - Un mouvement est créé avec `statut = valide` par défaut.
 *   - Un mouvement annulé n'est jamais supprimé ni modifié :
 *     il est marqué `annule` et une contre-passation est créée.
 *
 * NE JAMAIS FAIRE :
 *
 *   - Modifier une quantité depuis un Controller ou une Vue.
 *   - Supprimer un mouvement.
 *   - Écrire un mouvement hors d'une transaction métier.
 */
class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stocks';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'stock_id',
        'lot_stock_id',
        'emplacement_id',
        'type',
        'nature',
        'quantite',
        'sens',
        'date_mouvement',
        'reference_document',
        'bon_sortie_id',
        'projet_id',
        'utilisateur_id',
        'motif',
        'notes',
        'statut',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'type'   => TypeMouvement::class,
            'nature' => NatureMouvement::class,

            'quantite' => 'decimal:3',
            'sens'     => 'integer',

            'date_mouvement' => 'date',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Article concerné.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /**
     * Lot concerné (nullable).
     *
     * Peut être null pour les mouvements non tracés par lot
     * (ex : corrections globales, écritures historiques).
     */
    public function lotStock(): BelongsTo
    {
        return $this->belongsTo(LotStock::class, 'lot_stock_id');
    }

    /**
     * Alias court pour `lotStock()`.
     *
     * Utilisé dans les vues pour un accès rapide au lot.
     */
    public function lot(): BelongsTo
    {
        return $this->lotStock();
    }

    /**
     * Emplacement concerné (nullable).
     */
    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(
            EmplacementStock::class,
            'emplacement_id'
        );
    }

    /**
     * Bon de sortie source (nullable).
     */
    public function bonSortie(): BelongsTo
    {
        return $this->belongsTo(BonSortie::class, 'bon_sortie_id');
    }

    /**
     * Projet concerné (nullable).
     */
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /**
     * Utilisateur ayant effectué le mouvement.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — TYPE
    |--------------------------------------------------------------------------
    */

    public function estEntree(): bool
    {
        return $this->type === TypeMouvement::ENTREE;
    }

    public function estSortie(): bool
    {
        return $this->type === TypeMouvement::SORTIE;
    }

    public function estTransfert(): bool
    {
        return $this->type === TypeMouvement::TRANSFERT;
    }

    public function estAjustement(): bool
    {
        return $this->type === TypeMouvement::AJUSTEMENT;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — NATURE
    |--------------------------------------------------------------------------
    */

    public function estDistribution(): bool
    {
        return $this->nature === NatureMouvement::DISTRIBUTION;
    }

    public function estPeremption(): bool
    {
        return $this->nature === NatureMouvement::PEREMPTION;
    }

    public function estPerte(): bool
    {
        return $this->nature === NatureMouvement::PERTE;
    }

    public function estInventaire(): bool
    {
        return $this->nature === NatureMouvement::INVENTAIRE;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — QUANTITÉ SIGNÉE
    |--------------------------------------------------------------------------
    |
    | La quantité physique d'un mouvement est TOUJOURS positive.
    | Pour l'affichage ou l'agrégation, on utilise une quantité
    | signée :
    |
    |   quantiteSignee() = quantite * sens
    |
    |   +5  → entrée de 5
    |   -3  → sortie de 3 ou manquant d'inventaire de 3
    |    0  → neutre (transfert)
    |
    */

    /**
     * Quantité signée (positive, négative ou nulle).
     */
    public function quantiteSignee(): float
    {
        return round(
            (float) $this->quantite * (int) $this->sens,
            3
        );
    }

    /**
     * Quantité formatée pour l'affichage.
     *
     *   +5,000
     *   -3,000
     *    0,000
     */
    public function quantiteFormatee(): string
    {
        $quantite = $this->quantiteSignee();

        $signe = '';
        if ($quantite > 0) {
            $signe = '+';
        } elseif ($quantite < 0) {
            $signe = '-';
        }

        return $signe . number_format(
            abs($quantite),
            3,
            ',',
            ' '
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — STATUT
    |--------------------------------------------------------------------------
    */

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    public function estAnnule(): bool
    {
        return $this->statut === 'annule';
    }

    /**
     * Un mouvement est TOUJOURS de l'historique.
     *
     * Cette méthode existe pour être utilisée par les Policies
     * et les vues : elle matérialise l'interdiction de
     * modification/suppression au niveau du modèle.
     */
    public function estHistorique(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Nom de l'utilisateur ayant effectué le mouvement.
     *
     * Gère plusieurs conventions de colonnes possibles.
     */
    public function nomEffectuePar(): string
    {
        $user = $this->utilisateur;

        if (!$user) {
            return 'Système';
        }

        return $user->name
            ?? trim(
                ($user->prenom ?? '') . ' ' . ($user->nom ?? '')
            )
            ?: 'Utilisateur';
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeEntrees(Builder $query): Builder
    {
        return $query->where(
            'type',
            TypeMouvement::ENTREE->value
        );
    }

    public function scopeSorties(Builder $query): Builder
    {
        return $query->where(
            'type',
            TypeMouvement::SORTIE->value
        );
    }

    public function scopeTransferts(Builder $query): Builder
    {
        return $query->where(
            'type',
            TypeMouvement::TRANSFERT->value
        );
    }

    public function scopeAjustements(Builder $query): Builder
    {
        return $query->where(
            'type',
            TypeMouvement::AJUSTEMENT->value
        );
    }

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', 'valide');
    }

    public function scopePourStock(
        Builder $query,
        int $stockId
    ): Builder {
        return $query->where('stock_id', $stockId);
    }

    public function scopePourLot(
        Builder $query,
        int $lotId
    ): Builder {
        return $query->where('lot_stock_id', $lotId);
    }

    public function scopeEntreDates(
        Builder $query,
        ?string $dateDebut,
        ?string $dateFin
    ): Builder {
        if ($dateDebut) {
            $query->whereDate('date_mouvement', '>=', $dateDebut);
        }

        if ($dateFin) {
            $query->whereDate('date_mouvement', '<=', $dateFin);
        }

        return $query;
    }
}