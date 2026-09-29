<?php

namespace App\Models;

use App\Enums\StatutReservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne d'une réservation de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne = un article réservé dans une réservation donnée.
 *
 *   Elle porte trois quantités distinctes :
 *
 *     quantite_demandee   → saisie utilisateur
 *     quantite_reservee   → effectivement bloquée
 *     quantite_consommee  → cumul converti en sortie physique
 *
 * INVARIANTS MÉTIER :
 *
 *   0 <= quantite_reservee <= quantite_demandee
 *   0 <= quantite_consommee <= quantite_reservee
 *
 *   Le solde actif d'une ligne est :
 *
 *     reste = quantite_reservee - quantite_consommee
 *
 *   C'est ce reste qui entre dans le calcul de la quantité
 *   réservée globale d'un article, lorsque la réservation
 *   parente est active (confirmee | partielle).
 *
 * IMPORTANT :
 *
 *   - `quantite_reservee` n'est incrémentée que par
 *     ReservationStockService::confirmer().
 *   - `quantite_consommee` n'est incrémentée que par
 *     ReservationStockService::consommer().
 *   - Les deux opérations sont transactionnelles avec
 *     verrouillage sur la réservation et les stocks concernés.
 */
class LigneReservationStock extends Model
{
    use HasFactory;

    protected $table = 'lignes_reservations_stocks';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'reservation_stock_id',
        'stock_id',
        'quantite_demandee',
        'quantite_reservee',
        'quantite_consommee',
        'unite',
        'notes',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'quantite_demandee'  => 'decimal:3',
            'quantite_reservee'  => 'decimal:3',
            'quantite_consommee' => 'decimal:3',

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
     * Réservation parente.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(
            ReservationStock::class,
            'reservation_stock_id'
        );
    }

    /**
     * Alias de `reservation()` pour compatibilité avec
     * d'anciens appels qui utilisaient `reservationStock`.
     */
    public function reservationStock(): BelongsTo
    {
        return $this->reservation();
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
     * Solde actif de la ligne.
     *
     *   reste = quantite_reservee - quantite_consommee
     *
     * Bornée à zéro.
     */
    public function quantiteRestante(): float
    {
        return max(
            0.0,
            (float) $this->quantite_reservee
                - (float) $this->quantite_consommee
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    /**
     * La quantité réservée a-t-elle été entièrement consommée ?
     */
    public function estEntierementConsommee(): bool
    {
        $reservee  = (float) $this->quantite_reservee;
        $consommee = (float) $this->quantite_consommee;

        return $reservee > 0 && $consommee >= $reservee;
    }

    /**
     * Une partie de la quantité réservée a-t-elle été consommée ?
     */
    public function estPartiellementConsommee(): bool
    {
        $reservee  = (float) $this->quantite_reservee;
        $consommee = (float) $this->quantite_consommee;

        return $consommee > 0 && $consommee < $reservee;
    }

    /**
     * Aucune quantité n'a encore été consommée.
     */
    public function estNonConsommee(): bool
    {
        return (float) $this->quantite_consommee <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Lignes dont la réservation parente est active
     * (confirmée ou partielle).
     */
    public function scopeActives(Builder $query): Builder
    {
        return $query->whereHas('reservation', function (Builder $q) {
            $q->whereIn('statut', [
                StatutReservation::CONFIRMEE->value,
                StatutReservation::PARTIELLE->value,
            ]);
        });
    }

    /**
     * Lignes ayant encore un solde actif
     * (quantite_reservee - quantite_consommee > 0).
     */
    public function scopeAvecSolde(Builder $query): Builder
    {
        return $query->whereRaw(
            'quantite_reservee - quantite_consommee > 0'
        );
    }
}