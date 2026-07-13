<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Projet extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'projets';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'titre',
        'slug',
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
     */
    public function indicateurs()
    {
        return $this->hasMany(IndicateurProjet::class);
    }

    /**
     * Les bénéficiaires aidés par ce projet.
     */
    public function beneficiaires()
    {
        return $this->hasMany(Beneficiaire::class);
    }

    /**
     * Les dons reçus pour ce projet.
     */
    public function dons()
    {
        return $this->hasMany(Don::class);
    }

    /**
     * Les partenaires associés à ce projet.
     */
    public function partenaires()
    {
        return $this->belongsToMany(Partenaire::class, 'projet_partenaire')
                    ->withPivot('role', 'contribution', 'description', 'date_debut', 'date_fin')
                    ->withTimestamps();
    }

    /**
     * Les médias (photos, vidéos) liés à ce projet.
     */
    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Les articles qui mentionnent ce projet.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calcule le pourcentage d'avancement du budget.
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
     */
    public function dureeEnMois(): int
    {
        return $this->date_debut->diffInMonths($this->date_fin ?? now());
    }

    /**
     * Vérifie si le projet est en cours.
     */
    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifie si le projet est terminé.
     */
    public function estTermine(): bool
    {
        return $this->statut === 'termine';
    }

    /**
     * Libellé du domaine d'intervention en français.
     */
    public function domaineLibelle(): string
    {
        return match($this->domaine) {
            'sante_nutrition' => 'Santé-Nutrition',
            'education' => 'Éducation',
            'protection' => 'Protection de l\'enfant et de la femme',
            'wash' => 'Eau, Assainissement et Hygiène',
            'securite_alimentaire' => 'Sécurité Alimentaire',
            'communication' => 'Communication',
            'environnement' => 'Protection de l\'environnement',
            'paix' => 'Promotion de la Paix',
            'genre' => 'Sensibilité au genre',
            default => $this->domaine,
        };
    }

    /**
     * Libellé du statut en français.
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
     * Génère le slug automatiquement à partir du titre.
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