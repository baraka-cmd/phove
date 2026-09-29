<?php

namespace App\Models;

use App\Enums\StatutReservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Réservation de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une réservation BLOQUE une quantité pour un usage futur,
 *   SANS effectuer de sortie physique. C'est une "promesse"
 *   de disponibilité.
 *
 *   Tant qu'une réservation est dans un état actif
 *   (confirmee | partielle), sa quantité non encore consommée
 *   est comptée dans le calcul de la quantité réservée globale
 *   d'un article, et donc retirée de la disponibilité.
 *
 * WORKFLOW :
 *
 *   brouillon
 *       ↓ (confirmer)
 *   confirmee ──┐
 *       ↓       │ (annuler)
 *   partielle ──┼──→ annulee
 *       ↓       │
 *   consommee   │
 *               └──→ expiree
 *
 * INVARIANTS :
 *
 *   - Seules les réservations `confirmee` et `partielle` sont
 *     considérées comme actives.
 *   - Une réservation `consommee`, `expiree` ou `annulee`
 *     n'impacte plus la disponibilité.
 *   - Une réservation `brouillon` ne bloque rien.
 *
 * IMPORTANT :
 *
 *   Les quantités par article sont portées par `LigneReservationStock`.
 *   Ce modèle porte uniquement l'en-tête (workflow, dates, contexte).
 */
class ReservationStock extends Model
{
    use HasFactory;

    protected $table = 'reservations_stocks';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'numero',
        'date_reservation',
        'date_expiration',
        'demandeur_id',
        'projet_id',
        'destinataire',
        'motif',
        'statut',
        'notes',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'statut' => StatutReservation::class,

            'date_reservation' => 'date',
            'date_expiration'  => 'date',

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
     * Utilisateur ayant créé la réservation.
     */
    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    /**
     * Projet associé.
     */
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /**
     * Lignes de la réservation.
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(
            LigneReservationStock::class,
            'reservation_stock_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estBrouillon(): bool
    {
        return $this->statut === StatutReservation::BROUILLON;
    }

    public function estConfirmee(): bool
    {
        return $this->statut === StatutReservation::CONFIRMEE;
    }

    public function estPartielle(): bool
    {
        return $this->statut === StatutReservation::PARTIELLE;
    }

    public function estConsommee(): bool
    {
        return $this->statut === StatutReservation::CONSOMMEE;
    }

    public function estExpiree(): bool
    {
        return $this->statut === StatutReservation::EXPIREE;
    }

    public function estAnnulee(): bool
    {
        return $this->statut === StatutReservation::ANNULEE;
    }

    /**
     * La réservation impacte-t-elle la disponibilité ?
     *
     * Seules `confirmee` et `partielle` sont actives.
     */
    public function estActive(): bool
    {
        return $this->estConfirmee() || $this->estPartielle();
    }

    /**
     * La réservation est-elle dans un état terminal ?
     */
    public function estTerminee(): bool
    {
        return in_array(
            $this->statut,
            [
                StatutReservation::CONSOMMEE,
                StatutReservation::EXPIREE,
                StatutReservation::ANNULEE,
            ],
            true
        );
    }

    /**
     * La date d'expiration est-elle dépassée ? (indépendamment du statut)
     */
    public function estExpireeSelonDate(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }

        return $this->date_expiration->isPast();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — AUTORISATIONS D'ÉTAPE
    |--------------------------------------------------------------------------
    */

    public function peutEtreModifiee(): bool
    {
        return $this->estBrouillon();
    }

    public function peutEtreConfirmee(): bool
    {
        return $this->estBrouillon();
    }

    public function peutEtreConsommee(): bool
    {
        return $this->estActive();
    }

    public function peutEtreAnnulee(): bool
    {
        return !$this->estTerminee();
    }

    public function peutEtreExpiree(): bool
    {
        return $this->estActive()
            && $this->date_expiration !== null
            && $this->date_expiration->isPast();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — CALCULS
    |--------------------------------------------------------------------------
    */

    /**
     * Nombre total de lignes.
     */
    public function nombreLignes(): int
    {
        return $this->lignes()->count();
    }

    /**
     * Quantité totale demandée.
     */
    public function quantiteDemandee(): float
    {
        return (float) $this->lignes()->sum('quantite_demandee');
    }

    /**
     * Quantité totale réservée.
     */
    public function quantiteReservee(): float
    {
        return (float) $this->lignes()->sum('quantite_reservee');
    }

    /**
     * Quantité totale déjà consommée.
     */
    public function quantiteConsommee(): float
    {
        return (float) $this->lignes()->sum('quantite_consommee');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Réservations actives (impactant la disponibilité).
     */
    public function scopeActives(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            StatutReservation::CONFIRMEE->value,
            StatutReservation::PARTIELLE->value,
        ]);
    }

    /**
     * Réservations expirées.
     */
    public function scopeExpirees(Builder $query): Builder
    {
        return $query->where(
            'statut',
            StatutReservation::EXPIREE->value
        );
    }

    /**
     * Filtre par statut donné.
     */
    public function scopeStatut(
        Builder $query,
        ?string $statut
    ): Builder {
        if (!$statut) {
            return $query;
        }

        return $query->where('statut', $statut);
    }

    /**
     * Recherche multi-champs.
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
            $q->where('numero', 'like', "%{$recherche}%")
              ->orWhere('destinataire', 'like', "%{$recherche}%")
              ->orWhere('motif', 'like', "%{$recherche}%")
              ->orWhere('notes', 'like', "%{$recherche}%");
        });
    }
}