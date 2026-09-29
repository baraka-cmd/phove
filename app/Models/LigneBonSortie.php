<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne d'un bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne = un article demandé dans un bon de sortie.
 *
 *   Elle porte trois quantités distinctes qui reflètent
 *   l'évolution du document :
 *
 *     quantite_demandee  → saisie initiale
 *     quantite_validee   → ajustée lors de la validation
 *     quantite_sortie    → cumul réellement sorti (FEFO)
 *
 * INVARIANTS MÉTIER :
 *
 *   0 <= quantite_validee <= quantite_demandee
 *   0 <= quantite_sortie  <= quantite_validee
 *
 *   Ces invariants sont vérifiés par SortieStockService,
 *   pas par ce modèle. Ici, on expose seulement les lectures.
 *
 * IMPORTANT :
 *
 *   `quantite_sortie` ne doit être incrémentée QUE par
 *   SortieStockService::effectuerLigne(), sous transaction,
 *   avec `lockForUpdate()`.
 *
 * CONTRAINTE BASE :
 *
 *   Un article ne peut apparaître qu'une seule fois dans
 *   un même bon (contrainte UNIQUE en base).
 */
class LigneBonSortie extends Model
{
    use HasFactory;

    protected $table = 'lignes_bons_sortie';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'bon_sortie_id',
        'stock_id',
        'quantite_demandee',
        'quantite_validee',
        'quantite_sortie',
        'unite',
        'notes',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'quantite_demandee' => 'decimal:3',
            'quantite_validee'  => 'decimal:3',
            'quantite_sortie'   => 'decimal:3',

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
     * Bon de sortie auquel appartient cette ligne.
     */
    public function bonSortie(): BelongsTo
    {
        return $this->belongsTo(BonSortie::class, 'bon_sortie_id');
    }

    /**
     * Article concerné.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — QUANTITÉS
    |--------------------------------------------------------------------------
    */

    /**
     * Quantité validée encore à sortir.
     *
     *   reste = quantite_validee - quantite_sortie
     *
     * Bornée à zéro.
     */
    public function quantiteRestante(): float
    {
        return max(
            0.0,
            (float) $this->quantite_validee
                - (float) $this->quantite_sortie
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    /**
     * La ligne a-t-elle été validée avec une quantité > 0 ?
     */
    public function estValidee(): bool
    {
        return (float) $this->quantite_validee > 0;
    }

    /**
     * Aucune quantité n'a encore été sortie.
     */
    public function estNonSortie(): bool
    {
        return (float) $this->quantite_sortie <= 0;
    }

    /**
     * Une partie de la quantité validée a été sortie.
     */
    public function estPartiellementSortie(): bool
    {
        $validee = (float) $this->quantite_validee;
        $sortie  = (float) $this->quantite_sortie;

        return $sortie > 0 && $sortie < $validee;
    }

    /**
     * La quantité validée a été entièrement sortie.
     */
    public function estEntierementSortie(): bool
    {
        if (!$this->estValidee()) {
            return false;
        }

        return $this->quantiteRestante() <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    |
    | Volontairement absents :
    |   - Les requêtes sur les lignes se font presque toujours
    |     via la relation `bonSortie->lignes` ou via un filtre
    |     par bon.
    |   - Les scopes génériques sur `stock_id` sont portés par
    |     `Stock::lignesBonsSortie()`.
    |
    */
}