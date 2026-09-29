<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne de comptage d'un inventaire.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne = un article compté dans un inventaire donné.
 *
 *   Elle porte trois valeurs clés :
 *
 *     quantite_theorique  → figée à la création de l'inventaire
 *     quantite_comptee    → saisie lors du comptage physique
 *     ecart               → quantite_comptee - quantite_theorique
 *
 * INVARIANTS MÉTIER :
 *
 *   - `quantite_theorique` est IMMUABLE après création.
 *     C'est la PHOTOGRAPHIE du stock au démarrage de l'inventaire.
 *
 *   - `quantite_comptee` peut rester NULL tant que le comptage
 *     n'a pas eu lieu (comptage progressif).
 *
 *   - `ecart` est recalculé à chaque saisie du comptage.
 *     Il est NULL tant que `quantite_comptee` est NULL.
 *
 *   - Un article ne peut apparaître qu'UNE seule fois dans
 *     un inventaire donné (contrainte UNIQUE en base).
 *
 * RÔLE CRITIQUE :
 *
 *   C'est l'immuabilité de `quantite_theorique` qui permet, à la
 *   validation de l'inventaire, de garantir que le stock n'a pas
 *   changé entre-temps. Si le stock a changé, la validation est
 *   rejetée : il faut recommencer l'inventaire.
 *
 * IMPORTANT :
 *
 *   - `quantite_theorique` est écrite UNE SEULE FOIS, à la
 *     création de la ligne, par InventaireStockService::creer().
 *   - `quantite_comptee` et `ecart` sont mis à jour par
 *     InventaireStockService::compter() (ou comptabiliser()).
 *   - Aucune de ces opérations ne modifie les quantités
 *     physiques du stock. Seul InventaireStockService::valider()
 *     génère un mouvement d'ajustement.
 */
class InventaireStockLigne extends Model
{
    use HasFactory;

    protected $table = 'inventaire_stock_lignes';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'inventaire_stock_id',
        'stock_id',
        'quantite_theorique',
        'quantite_comptee',
        'ecart',
        'unite',
        'notes',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'quantite_theorique' => 'decimal:3',
            'quantite_comptee'   => 'decimal:3',
            'ecart'              => 'decimal:3',

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
     * Inventaire parent.
     */
    public function inventaire(): BelongsTo
    {
        return $this->belongsTo(
            InventaireStock::class,
            'inventaire_stock_id'
        );
    }

    /**
     * Alias de `inventaire()` pour compatibilité avec les
     * vues et services qui utilisent `inventaireStock`.
     */
    public function inventaireStock(): BelongsTo
    {
        return $this->inventaire();
    }

    /**
     * Article compté.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    /**
     * Le comptage a-t-il été effectué sur cette ligne ?
     */
    public function estComptee(): bool
    {
        return $this->quantite_comptee !== null;
    }

    /**
     * La ligne présente-t-elle un écart significatif ?
     *
     * Tolérance sur les flottants : 0.0001.
     */
    public function aUnEcart(): bool
    {
        return $this->ecart !== null
            && abs((float) $this->ecart) > 0.0001;
    }

    /**
     * Surplus constaté (compté > théorique).
     */
    public function estSurplus(): bool
    {
        return $this->aUnEcart()
            && (float) $this->ecart > 0;
    }

    /**
     * Manquant constaté (compté < théorique).
     */
    public function estManquant(): bool
    {
        return $this->aUnEcart()
            && (float) $this->ecart < 0;
    }

    /**
     * Aucun écart constaté.
     */
    public function estConforme(): bool
    {
        return $this->estComptee() && !$this->aUnEcart();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — CALCUL
    |--------------------------------------------------------------------------
    */

    /**
     * Recalcule l'écart à partir des quantités.
     *
     * IMPORTANT :
     *   Cette méthode ne persiste PAS.
     *   Elle mute uniquement l'attribut en mémoire.
     *   Le service appelera explicitement `$ligne->save()`.
     *
     * Règle :
     *   - si `quantite_comptee` est NULL, l'écart devient NULL ;
     *   - sinon, l'écart vaut (comptee - theorique), arrondi à 3 décimales.
     */
    public function recalculerEcart(): void
    {
        if ($this->quantite_comptee === null) {
            $this->ecart = null;
            return;
        }

        $this->ecart = round(
            (float) $this->quantite_comptee
                - (float) $this->quantite_theorique,
            3
        );
    }

    /**
     * Enregistre un comptage physique sur cette ligne.
     *
     * Cette méthode est un RACCOURCI pratique. Elle reste
     * une mutation mémoire : elle n'ouvre AUCUNE transaction
     * et ne remplace PAS l'appel au service métier.
     *
     * Le service InventaireStockService::compter() reste
     * responsable de la transaction, du verrouillage et de
     * la cohérence globale de l'inventaire.
     */
    public function enregistrerComptage(float $quantiteComptee): void
    {
        $this->quantite_comptee = round($quantiteComptee, 3);
        $this->recalculerEcart();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Lignes effectivement comptées.
     */
    public function scopeComptees(Builder $query): Builder
    {
        return $query->whereNotNull('quantite_comptee');
    }

    /**
     * Lignes non encore comptées.
     */
    public function scopeNonComptees(Builder $query): Builder
    {
        return $query->whereNull('quantite_comptee');
    }

    /**
     * Lignes présentant un écart non nul.
     */
    public function scopeAvecEcart(Builder $query): Builder
    {
        return $query
            ->whereNotNull('ecart')
            ->where('ecart', '!=', 0);
    }

    /**
     * Lignes sans écart (conformes).
     */
    public function scopeSansEcart(Builder $query): Builder
    {
        return $query
            ->whereNotNull('ecart')
            ->where('ecart', 0);
    }

    /**
     * Lignes en surplus (compté > théorique).
     */
    public function scopeSurplus(Builder $query): Builder
    {
        return $query->where('ecart', '>', 0);
    }

    /**
     * Lignes en manquant (compté < théorique).
     */
    public function scopeManquants(Builder $query): Builder
    {
        return $query->where('ecart', '<', 0);
    }
}