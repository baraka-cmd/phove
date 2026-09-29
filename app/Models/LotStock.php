<?php

namespace App\Models;

use App\Enums\StatutLot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lot physique d'un article.
 *
 * SÉMANTIQUE :
 *
 *   Un lot est la brique qui porte RÉELLEMENT la quantité
 *   physique d'un article. La table `stocks` ne contient
 *   aucune quantité : tout le stock physique vit ici, sous
 *   forme de lots.
 *
 *   Un lot a :
 *     - une quantité initiale (immuable) ;
 *     - une quantité actuelle (vivante, modifiée uniquement
 *       par les Services, sous transaction) ;
 *     - un statut opérationnel (actif / epuise / expire / bloque) ;
 *     - des dates de traçabilité (fabrication, entrée, expiration).
 *
 * INVARIANTS MÉTIER :
 *
 *   - `quantite_initiale` ne doit JAMAIS être modifiée après
 *     création du lot.
 *   - `quantite_actuelle` est TOUJOURS dans [0, quantite_initiale].
 *   - `quantite_actuelle` ne doit être modifiée que par un Service
 *     métier, dans une transaction avec `lockForUpdate()`.
 *   - Le `statut` reflète l'état réel : un lot dont la quantité
 *     tombe à 0 devient `epuise`, un lot dont la date est
 *     dépassée devient `expire`, etc.
 *
 * FEFO :
 *
 *   La sélection des lots pour une sortie suit FEFO
 *   (First Expired, First Out) : on sort d'abord les lots
 *   dont la date d'expiration est la plus proche.
 *
 *   Le scope `fefo()` fournit l'ordre canonique. La sélection
 *   réelle est faite par les Services, sous verrouillage.
 */
class LotStock extends Model
{
    use HasFactory;

    protected $table = 'lots_stocks';

    /**
     * Attributs assignables en masse.
     *
     * On exclut les quantités : elles sont pilotées par les
     * Services, pas par un formulaire.
     */
    protected $fillable = [
        'stock_id',
        'numero_lot',
        'date_fabrication',
        'date_entree',
        'date_expiration',
        'quantite_initiale',
        'quantite_actuelle',
        'cout_unitaire',
        'fournisseur',
        'reference_document',
        'statut',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'statut'           => StatutLot::class,

            'date_fabrication' => 'date',
            'date_entree'      => 'date',
            'date_expiration'  => 'date',

            'quantite_initiale' => 'decimal:3',
            'quantite_actuelle' => 'decimal:3',
            'cout_unitaire'     => 'decimal:2',

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
     * Article auquel appartient ce lot.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /**
     * Mouvements associés à ce lot.
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'lot_stock_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estActif(): bool
    {
        return $this->statut === StatutLot::ACTIF;
    }

    public function estEpuise(): bool
    {
        return $this->statut === StatutLot::EPUISE;
    }

    public function estExpire(): bool
    {
        return $this->statut === StatutLot::EXPIRE;
    }

    public function estBloque(): bool
    {
        return $this->statut === StatutLot::BLOQUE;
    }

    /**
     * Le lot possède-t-il encore de la quantité ?
     */
    public function possedeDuStock(): bool
    {
        return (float) $this->quantite_actuelle > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — EXPIRATION
    |--------------------------------------------------------------------------
    */

    /**
     * Le lot a-t-il une date d'expiration ?
     */
    public function estPerissable(): bool
    {
        return $this->date_expiration !== null;
    }

    /**
     * La date d'expiration est-elle dépassée ? (indépendamment du statut)
     */
    public function estExpireSelonDate(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }

        return $this->date_expiration->isPast();
    }

    /**
     * Nombre de jours restants avant expiration.
     *
     *   > 0  → expire dans N jours
     *   = 0  → expire aujourd'hui
     *   < 0  → expiré depuis N jours
     *   null → pas de date d'expiration
     */
    public function joursAvantExpiration(): ?int
    {
        if (!$this->date_expiration) {
            return null;
        }

        return now()->startOfDay()->diffInDays(
            $this->date_expiration->startOfDay(),
            false
        );
    }

    /**
     * Le lot expire-t-il dans les N prochains jours ?
     */
    public function expireBientot(int $jours = 30): bool
    {
        $joursRestants = $this->joursAvantExpiration();

        if ($joursRestants === null) {
            return false;
        }

        return $joursRestants >= 0
            && $joursRestants <= $jours;
    }

    /**
     * Le lot peut-il être utilisé dans une sortie ?
     *
     * Conditions cumulatives :
     *   - statut ACTIF ;
     *   - quantité > 0 ;
     *   - non expiré (date).
     */
    public function peutEtreUtilise(): bool
    {
        if (!$this->estActif()) {
            return false;
        }

        if (!$this->possedeDuStock()) {
            return false;
        }

        if ($this->estExpireSelonDate()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — VALORISATION
    |--------------------------------------------------------------------------
    */

    /**
     * Valeur théorique restante dans le lot.
     */
    public function valeurStock(): float
    {
        return round(
            (float) $this->quantite_actuelle
            * (float) ($this->cout_unitaire ?? 0),
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Lots au statut ACTIF.
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('statut', StatutLot::ACTIF->value);
    }

    /**
     * Lots réellement disponibles pour une sortie :
     *   - statut ACTIF ;
     *   - quantité > 0 ;
     *   - date d'expiration non dépassée.
     */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query
            ->where('statut', StatutLot::ACTIF->value)
            ->where('quantite_actuelle', '>', 0)
            ->where(function (Builder $q) {
                $q->whereNull('date_expiration')
                  ->orWhereDate(
                      'date_expiration',
                      '>=',
                      now()->toDateString()
                  );
            });
    }

    /**
     * Ordre FEFO canonique.
     *
     * Règles :
     *   1. les lots avec date d'expiration d'abord ;
     *   2. parmi eux, ceux qui expirent le plus tôt en premier ;
     *   3. à égalité, le lot entré le plus tôt ;
     *   4. à égalité finale, l'id le plus petit (déterministe).
     *
     * NOTE :
     *   - on ne filtre PAS sur le statut ni sur la quantité
     *     ici : c'est un ordre, pas une sélection ;
     *   - la sélection + verrouillage sont faits par les Services.
     */
    public function scopeFefo(Builder $query): Builder
    {
        return $query
            ->orderByRaw(
                'CASE WHEN date_expiration IS NULL THEN 1 ELSE 0 END'
            )
            ->orderBy('date_expiration')
            ->orderBy('date_entree')
            ->orderBy('id');
    }

    /**
     * Lots actifs qui expirent dans les N prochains jours.
     */
    public function scopeExpirantBientot(
        Builder $query,
        int $jours = 30
    ): Builder {
        $dateAujourdhui = now()->toDateString();
        $dateLimite = now()->addDays($jours)->toDateString();

        return $query
            ->whereNotNull('date_expiration')
            ->whereDate('date_expiration', '>=', $dateAujourdhui)
            ->whereDate('date_expiration', '<=', $dateLimite)
            ->where('statut', StatutLot::ACTIF->value);
    }

    /**
     * Lots dont la date d'expiration est dépassée,
     * indépendamment du statut.
     */
    public function scopeExpires(Builder $query): Builder
    {
        return $query
            ->whereNotNull('date_expiration')
            ->whereDate(
                'date_expiration',
                '<',
                now()->toDateString()
            );
    }
}