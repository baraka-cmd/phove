<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'partenaires';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'sigle',
        'type',
        'email',
        'telephone',
        'adresse',
        'site_web',
        'logo',
        'description',
        'statut',
        'date_debut',
        'date_fin',
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
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Les projets auxquels le partenaire participe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_partenaire')
                    ->withPivot('role', 'contribution', 'description', 'date_debut', 'date_fin')
                    ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les partenaires actifs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope : filtrer par type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : partenaires internationaux.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInternationaux($query)
    {
        return $query->where('type', 'international');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si le partenaire est actif.
     *
     * @return bool
     */
    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Récupère le nom du type de partenaire en français.
     *
     * @return string
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'bailleur_fonds' => 'Bailleur de fonds',
            'technique' => 'Partenaire technique',
            'local' => 'Partenaire local',
            'international' => 'Partenaire international',
            'institutionnel' => 'Partenaire institutionnel',
            default => $this->type ?? 'Non défini',
        };
    }

    /**
     * Retourne le sigle du partenaire ou les initiales si non défini.
     *
     * @return string
     */
    public function sigleOuInitiales(): string
    {
        if ($this->sigle) {
            return strtoupper($this->sigle);
        }

        // Générer les initiales à partir du nom
        $mots = explode(' ', $this->nom);
        $initiales = '';
        foreach ($mots as $mot) {
            if (strlen($mot) > 2) {
                $initiales .= strtoupper(substr($mot, 0, 1));
            }
        }
        return $initiales ?: strtoupper(substr($this->nom, 0, 2));
    }

    /**
     * Retourne l'URL du site web avec https:// si non défini.
     *
     * @return string|null
     */
    public function urlSiteWeb(): ?string
    {
        if (!$this->site_web) {
            return null;
        }

        if (str_starts_with($this->site_web, 'http://') || str_starts_with($this->site_web, 'https://')) {
            return $this->site_web;
        }

        return 'https://' . $this->site_web;
    }

    /**
     * Retourne l'URL du logo ou null si non défini.
     *
     * @return string|null
     */
    public function urlLogo(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Retourne le nombre de projets associés.
     *
     * @return int
     */
    public function nombreProjets(): int
    {
        return $this->projets()->count();
    }

    /**
     * Retourne le total des contributions financières.
     *
     * @return float
     */
    public function totalContributions(): float
    {
        return $this->projets()->sum('contribution');
    }
}