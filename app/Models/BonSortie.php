<?php

namespace App\Models;

use App\Enums\StatutBonSortie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bon de sortie de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un bon de sortie est le document métier qui porte le
 *   workflow de sortie physique d'articles. Il permet de
 *   demander, faire valider, préparer puis exécuter une
 *   sortie réelle.
 *
 *   Le bon lui-même ne modifie AUCUNE quantité. C'est
 *   SortieStockService::effectuer() qui, à l'état
 *   `en_preparation`, génère les mouvements de sortie
 *   et décrémente les lots — sous transaction et verrouillage.
 *
 * WORKFLOW NOMINAL :
 *
 *   brouillon
 *       ↓
 *   soumis
 *       ↓
 *   valide
 *       ↓
 *   en_preparation
 *       ↓
 *   sorti
 *
 * Depuis `brouillon`, `soumis` ou `valide`, un bon peut
 * également être passé à `annule`.
 *
 * INVARIANTS :
 *   - Un bon `sorti` est définitif : il ne peut plus changer.
 *   - Un bon `annule` est définitif : il ne peut plus changer.
 *   - Un bon `brouillon` ne peut pas être exécuté.
 *   - Un bon `en_preparation` est le seul état depuis lequel
 *     `effectuer()` est autorisé.
 */
class BonSortie extends Model
{
    use HasFactory;

    protected $table = 'bons_sortie';

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'numero',
        'reference',
        'date_demande',
        'date_validation',
        'date_sortie',
        'demandeur_id',
        'validateur_id',
        'preparateur_id',
        'destinataire',
        'projet_id',
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
            'statut' => StatutBonSortie::class,

            'date_demande'    => 'date',
            'date_validation' => 'date',
            'date_sortie'     => 'date',

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
     * Lignes composant le bon.
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(LigneBonSortie::class, 'bon_sortie_id');
    }

    /**
     * Mouvements générés par ce bon.
     */
    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'bon_sortie_id');
    }

    /**
     * Utilisateur ayant demandé la sortie.
     */
    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    /**
     * Utilisateur ayant validé le bon.
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    /**
     * Utilisateur ayant préparé physiquement la sortie.
     */
    public function preparateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'preparateur_id');
    }

    /**
     * Projet bénéficiaire.
     */
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — ÉTAT
    |--------------------------------------------------------------------------
    */

    public function estBrouillon(): bool
    {
        return $this->statut === StatutBonSortie::BROUILLON;
    }

    public function estSoumis(): bool
    {
        return $this->statut === StatutBonSortie::SOUMIS;
    }

    public function estValide(): bool
    {
        return $this->statut === StatutBonSortie::VALIDE;
    }

    public function estEnPreparation(): bool
    {
        return $this->statut === StatutBonSortie::EN_PREPARATION;
    }

    public function estSorti(): bool
    {
        return $this->statut === StatutBonSortie::SORTI;
    }

    public function estAnnule(): bool
    {
        return $this->statut === StatutBonSortie::ANNULE;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES MÉTIER — AUTORISATIONS D'ÉTAPE
    |--------------------------------------------------------------------------
    |
    | Ces méthodes décrivent CE QUE le bon peut faire à un moment
    | donné. Elles sont STRICTEMENT en lecture.
    |
    | Le Service reste responsable de la vérification transactionnelle
    | (disponibilité, verrouillage, cohérence). Ces méthodes servent
    | à la vue et aux contrôles rapides côté Controller.
    */

    /**
     * Le bon peut-il être modifié (côté document) ?
     */
    public function peutEtreModifie(): bool
    {
        return $this->estBrouillon()
            || $this->estSoumis();
    }

    /**
     * Le bon peut-il être soumis ?
     */
    public function peutEtreSoumis(): bool
    {
        return $this->estBrouillon();
    }

    /**
     * Le bon peut-il être validé ?
     */
    public function peutEtreValide(): bool
    {
        return $this->estSoumis();
    }

    /**
     * Le bon peut-il passer en préparation ?
     */
    public function peutEtrePrepare(): bool
    {
        return $this->estValide();
    }

    /**
     * Le bon peut-il être exécuté (sortie physique) ?
     */
    public function peutEtreExecute(): bool
    {
        return $this->estEnPreparation();
    }

    /**
     * Le bon peut-il être annulé ?
     *
     * Uniquement depuis les états non terminaux.
     */
    public function peutEtreAnnule(): bool
    {
        return in_array(
            $this->statut,
            [
                StatutBonSortie::BROUILLON,
                StatutBonSortie::SOUMIS,
                StatutBonSortie::VALIDE,
            ],
            true
        );
    }

    /**
     * Le bon est-il dans un état terminal ?
     */
    public function estTermine(): bool
    {
        return $this->estSorti() || $this->estAnnule();
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
     * Quantité totale demandée sur le bon.
     */
    public function quantiteDemandee(): float
    {
        return (float) $this->lignes()->sum('quantite_demandee');
    }

    /**
     * Quantité totale validée sur le bon.
     */
    public function quantiteValidee(): float
    {
        return (float) $this->lignes()->sum('quantite_validee');
    }

    /**
     * Quantité totale sortie sur le bon.
     */
    public function quantiteSortie(): float
    {
        return (float) $this->lignes()->sum('quantite_sortie');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Bons en cours de traitement (non terminaux).
     */
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            StatutBonSortie::SOUMIS->value,
            StatutBonSortie::VALIDE->value,
            StatutBonSortie::EN_PREPARATION->value,
        ]);
    }

    /**
     * Bons sortis.
     */
    public function scopeSortis(Builder $query): Builder
    {
        return $query->where(
            'statut',
            StatutBonSortie::SORTI->value
        );
    }

    /**
     * Bons annulés.
     */
    public function scopeAnnules(Builder $query): Builder
    {
        return $query->where(
            'statut',
            StatutBonSortie::ANNULE->value
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
     * Recherche multi-champs sur le bon.
     *
     *   - numero
     *   - reference
     *   - motif
     *   - notes
     *   - destinataire
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
              ->orWhere('reference', 'like', "%{$recherche}%")
              ->orWhere('motif', 'like', "%{$recherche}%")
              ->orWhere('notes', 'like', "%{$recherche}%")
              ->orWhere('destinataire', 'like', "%{$recherche}%");
        });
    }
}