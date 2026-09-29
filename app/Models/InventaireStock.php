<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Inventaire de stock (en-tête).
 *
 * SÉMANTIQUE :
 *
 *   Un inventaire est une opération de comptage physique
 *   qui compare la quantité THÉORIQUE (calculée depuis les lots)
 *   avec la quantité PHYSIQUE (comptée sur le terrain).
 *
 *   Ce qui produit un ÉCART, ensuite transformé en mouvement
 *   d'ajustement par InventaireStockService::valider().
 *
 * WORKFLOW :
 *
 *   brouillon
 *       ↓
 *   en_cours
 *       ↓
 *   termine
 *       ↓
 *   valide
 *
 *   → annule (possible depuis brouillon ou en_cours)
 *
 * INVARIANTS :
 *
 *   - Un inventaire validé est définitif.
 *   - Un inventaire annulé est définitif.
 *   - Un inventaire ne modifie JAMAIS directement les quantités.
 *     Seul `valider()` génère les mouvements d'ajustement.
 *
 * IMPORTANT :
 *
 *   Ce modèle porte l'EN-TÊTE de l'inventaire (périmètre,
 *   acteurs, workflow). Les quantités comptées par article
 *   vivent dans `InventaireStockLigne`.
 *
 *   La quantité système au démarrage est FIGÉE dans chaque
 *   ligne (`quantite_theorique`). C'est cette photographie
 *   qui garantit que l'ajustement final est cohérent.
 */
class InventaireStock extends Model
{
    use HasFactory;

    protected $table = 'inventaires_stocks';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'numero',
        'emplacement_id',
        'date_debut',
        'date_cloture',
        'responsable_id',
        'valide_par',
        'statut',
        'valide_at',
        'notes',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'date_debut'   => 'date',
            'date_cloture' => 'date',
            'valide_at'    => 'datetime',

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
     * Emplacement ciblé (nullable = tous).
     */
    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(
            EmplacementStock::class,
            'emplacement_id'
        );
    }

    /**
     * Utilisateur responsable.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Utilisateur ayant validé.
     */
    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Lignes de comptage.
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(
            InventaireStockLigne::class,
            'inventaire_stock_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estBrouillon(): bool
    {
        return $this->statut === 'brouillon';
    }

    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    public function estTermine(): bool
    {
        return $this->statut === 'termine';
    }

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    public function estAnnule(): bool
    {
        return $this->statut === 'annule';
    }

    /**
     * L'inventaire est-il dans un état terminal ?
     */
    public function estFinalise(): bool
    {
        return $this->estValide() || $this->estAnnule();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — AUTORISATIONS D'ÉTAPE
    |--------------------------------------------------------------------------
    */

    public function peutEtreModifie(): bool
    {
        return $this->estBrouillon() || $this->estEnCours();
    }

    public function peutEtreCompte(): bool
    {
        return $this->estBrouillon() || $this->estEnCours();
    }

    public function peutEtreValide(): bool
    {
        return !$this->estFinalise();
    }

    public function peutEtreAnnule(): bool
    {
        return $this->estBrouillon() || $this->estEnCours();
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
     * Nombre de lignes avec un écart non nul.
     */
    public function nombreEcarts(): int
    {
        return $this->lignes()
            ->whereNotNull('ecart')
            ->where('ecart', '!=', 0)
            ->count();
    }

    /**
     * Quantité théorique totale.
     */
    public function quantiteTheorique(): float
    {
        return (float) $this->lignes()->sum('quantite_theorique');
    }

    /**
     * Quantité comptée totale (lignes comptées uniquement).
     */
    public function quantiteComptee(): float
    {
        return (float) $this->lignes()
            ->whereNotNull('quantite_comptee')
            ->sum('quantite_comptee');
    }

    /**
     * Écart global.
     */
    public function ecartGlobal(): float
    {
        return (float) $this->lignes()
            ->whereNotNull('ecart')
            ->sum('ecart');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTermines(Builder $query): Builder
    {
        return $query->where('statut', 'termine');
    }

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', 'valide');
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
              ->orWhere('notes', 'like', "%{$recherche}%");
        });
    }
}