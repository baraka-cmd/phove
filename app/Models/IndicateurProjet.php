<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicateurProjet extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'indicateurs_projet';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'projet_id',
        'domaine',
        'nom_indicateur',
        'valeur_cible',
        'valeur_actuelle',
        'unite',
        'description',
        'date_mise_a_jour',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_mise_a_jour' => 'date',
            'valeur_cible' => 'integer',
            'valeur_actuelle' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet auquel appartient cet indicateur (peut être null si global).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : indicateurs par domaine.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $domaine
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomaine($query, string $domaine)
    {
        return $query->where('domaine', $domaine);
    }

    /**
     * Scope : indicateurs globaux (sans projet).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGlobaux($query)
    {
        return $query->whereNull('projet_id');
    }

    /**
     * Scope : indicateurs liés à un projet.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDuProjet($query)
    {
        return $query->whereNotNull('projet_id');
    }

    /**
     * Scope : objectifs atteints.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtteints($query)
    {
        return $query->whereColumn('valeur_actuelle', '>=', 'valeur_cible');
    }

    /**
     * Scope : objectifs en cours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnCours($query)
    {
        return $query->whereColumn('valeur_actuelle', '<', 'valeur_cible');
    }

    /*
    |--------------------------------------------------------------------------
    | CONSTANTES
    |--------------------------------------------------------------------------
    */

    /**
     * Les 9 domaines d'intervention PHOVE.
     *
     * @var array<string, string>
     */
    public const DOMAINES = [
        'sante_nutrition' => 'Santé & Nutrition',
        'education' => 'Éducation & Formation',
        'protection' => 'Protection Sociale',
        'agriculture' => 'Agriculture & Élevage',
        'infrastructure' => 'Infrastructures',
        'economique' => 'Activités Socio-Économiques',
        'spirituel' => 'Accompagnement Spirituel',
        'psychosocial' => 'Accompagnement Psychosocial',
        'judiciaire' => 'Assistance Judiciaire',
    ];

    /**
     * Les unités de mesure disponibles.
     *
     * @var array<int, string>
     */
    public const UNITES = [
        'personnes',
        'orphelins',
        'veuves',
        'femmes',
        'enfants',
        'ménages',
        'fidèles',
        'patients',
        'consultations',
        'formations',
        'attestations',
        'tonnes',
        'kg',
        'litres',
        'km',
        'écoles',
        'centres de santé',
        'routes',
        'logements',
        'dossiers',
        'affaires',
        'AGR',
        'semences',
        'outils',
        'prédicateurs',
        'groupes',
    ];

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le pourcentage d'atteinte de l'objectif.
     *
     * @return float
     */
    public function pourcentage(): float
    {
        if ($this->valeur_cible <= 0) {
            return 0;
        }
        return round(($this->valeur_actuelle / $this->valeur_cible) * 100, 1);
    }

    /**
     * Formate l'indicateur avec son unité.
     *
     * @return string
     */
    public function formater(): string
    {
        return number_format($this->valeur_actuelle, 0, ',', ' ') . ' / ' 
            . number_format($this->valeur_cible, 0, ',', ' ') . ' ' . $this->unite;
    }

    /**
     * Vérifie si l'objectif est atteint.
     *
     * @return bool
     */
    public function objectifAtteint(): bool
    {
        return $this->valeur_actuelle >= $this->valeur_cible;
    }

    /**
     * Libellé du domaine en français.
     *
     * @return string
     */
    public function domaineLibelle(): string
    {
        return self::DOMAINES[$this->domaine] ?? ($this->projet ? $this->projet->domaineLibelle() : 'Non défini');
    }

    /**
     * Retourne l'icône Font Awesome du domaine.
     *
     * @return string
     */
    public function domaineIcone(): string
    {
        return match($this->domaine) {
            'sante_nutrition' => 'heartbeat',
            'education' => 'graduation-cap',
            'protection' => 'shield-alt',
            'agriculture' => 'tractor',
            'infrastructure' => 'building',
            'economique' => 'hand-holding-usd',
            'spirituel' => 'dove',
            'psychosocial' => 'brain',
            'judiciaire' => 'gavel',
            default => 'chart-line',
        };
    }

    /**
     * Vérifie si l'indicateur est global (sans projet).
     *
     * @return bool
     */
    public function estGlobal(): bool
    {
        return $this->projet_id === null;
    }

    /**
     * Vérifie si l'indicateur est lié à un projet.
     *
     * @return bool
     */
    public function estDuProjet(): bool
    {
        return $this->projet_id !== null;
    }

    /**
     * Retourne la couleur du domaine pour les graphiques.
     *
     * @return string
     */
    public function domaineCouleur(): string
    {
        return match($this->domaine) {
            'sante_nutrition' => '#EF4444',
            'education' => '#3B82F6',
            'protection' => '#8B5CF6',
            'agriculture' => '#10B981',
            'infrastructure' => '#F59E0B',
            'economique' => '#06B6D4',
            'spirituel' => '#6366F1',
            'psychosocial' => '#EC4899',
            'judiciaire' => '#A855F7',
            default => '#64748B',
        };
    }

    /**
     * Statistiques globales par domaine.
     *
     * @param string|null $domaine
     * @return array
     */
    public static function statsParDomaine(?string $domaine = null): array
    {
        $query = self::query();

        if ($domaine) {
            $query->where('domaine', $domaine);
        }

        $total = $query->count();
        $atteints = (clone $query)->whereColumn('valeur_actuelle', '>=', 'valeur_cible')->count();
        $enCours = $total - $atteints;
        $tauxGlobal = $total > 0 ? round(($atteints / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'atteints' => $atteints,
            'en_cours' => $enCours,
            'taux_global' => $tauxGlobal,
        ];
    }
}