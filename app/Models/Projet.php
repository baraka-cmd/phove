<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Projet extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'projets';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'slug',
        'code_projet',
        'domaine',
        'zone_geographique',
        'province',
        'date_debut',
        'date_fin',
        'budget_total',
        'budget_utilise',
        'bailleur',
        'source_financement',
        'statut',
        'description',
        'objectifs',
        'resultats_attendus',
        'image_principale',
        'publier',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'budget_total' => 'decimal:2',
            'budget_utilise' => 'decimal:2',
            'publier' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Les indicateurs de performance (KPI) du projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function indicateurs()
    {
        return $this->hasMany(IndicateurProjet::class);
    }

    /**
     * Les bénéficiaires aidés par ce projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function beneficiaires()
    {
        return $this->hasMany(Beneficiaire::class);
    }

    /**
     * Les dons reçus pour ce projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dons()
    {
        return $this->hasMany(Don::class);
    }

    /**
     * Les partenaires associés à ce projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function partenaires()
    {
        return $this->belongsToMany(Partenaire::class, 'projet_partenaire')
                    ->withPivot('role', 'contribution', 'description', 'date_debut', 'date_fin')
                    ->withTimestamps();
    }

    /**
     * Les médias (photos, vidéos) liés à ce projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Les articles qui mentionnent ce projet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les projets publiés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublie($query)
    {
        return $query->where('publier', true);
    }

    /**
     * Scope : projets en cours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope : projets terminés.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTermines($query)
    {
        return $query->where('statut', 'termine');
    }

    /**
     * Scope : filtrer par domaine.
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
     * Scope : filtrer par zone géographique.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $zone
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeZone($query, string $zone)
    {
        return $query->where('zone_geographique', 'like', '%' . $zone . '%');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le pourcentage d'avancement du budget.
     *
     * @return float
     */
    public function pourcentageBudget(): float
    {
        if ($this->budget_total <= 0) {
            return 0;
        }
        return round(($this->budget_utilise / $this->budget_total) * 100, 1);
    }

    /**
     * Calcule la durée du projet en mois.
     *
     * @return int
     */
    public function dureeEnMois(): int
    {
        return $this->date_debut->diffInMonths($this->date_fin ?? now());
    }

    /**
     * Vérifie si le projet est en cours.
     *
     * @return bool
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si le projet est terminé.
     *
     * @return bool
     */
    public function estTermine(): bool
    {
        return $this->statut === 'termine';
    }

    /**
     * Vérifie si le projet est publié publiquement.
     *
     * @return bool
     */
    public function estPublie(): bool
    {
        return $this->publier;
    }

    /**
     * Retourne le code du projet ou "N/A" si non défini.
     *
     * @return string
     */
    public function codeProjetOuDefaut(): string
    {
        return $this->code_projet ?: 'N/A';
    }

    /**
     * Libellé du domaine d'intervention en français.
     *
     * @return string
     */
    public function domaineLibelle(): string
    {
        return match($this->domaine) {
            'sante_nutrition' => 'Santé-Nutrition',
            'education' => 'Éducation & Formation',
            'protection' => 'Protection Sociale',
            'wash' => 'Eau, Hygiène & Assainissement',
            'securite_alimentaire' => 'Sécurité Alimentaire & Agriculture',
            'communication' => 'Communication',
            'environnement' => 'Protection de l\'environnement',
            'paix' => 'Promotion de la Paix',
            'genre' => 'Sensibilité au genre',
            'spirituel' => 'Accompagnement Spirituel',
            'infrastructure' => 'Infrastructures',
            'economique' => 'Activités Socio-Économiques',
            'judiciaire' => 'Assistance Judiciaire',
            default => $this->domaine,
        };
    }

    /**
     * Libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'en_preparation' => 'En préparation',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
            default => $this->statut,
        };
    }

    /**
     * Retourne l'URL de l'image principale ou le logo PHOVE par défaut.
     *
     * @return string
     */
    /**
 * Retourne l'URL de l'image principale via la route custom "media.show",
 * ou le logo PHOVE par défaut si aucune image.
 */
public function urlImage(): string
{
    if ($this->image_principale) {
        return route('media.show', [
            'path' => ltrim($this->image_principale, '/')
        ]);
    }
    return asset('assets/images/logo-phove.png');
}

/**
 * Retourne le pourcentage d'avancement (basé sur le budget).
 * Utilisé dans les cartes projet pour la barre de progression.
 */
public function avancement(): int
{
    if ($this->budget_total <= 0) {
        return 0;
    }
    return (int) round(($this->budget_utilise / $this->budget_total) * 100);
}

    /**
     * Retourne le nombre total de bénéficiaires du projet.
     *
     * @return int
     */
    public function nombreBeneficiaires(): int
    {
        return $this->beneficiaires()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    /**
     * Boot : génère le slug automatiquement à partir du titre.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Projet $projet) {
            if (empty($projet->slug)) {
                $projet->slug = Str::slug($projet->titre);
            }
        });

        static::updating(function (Projet $projet) {
            if ($projet->isDirty('titre')) {
                $projet->slug = Str::slug($projet->titre);
            }
        });
    }
}