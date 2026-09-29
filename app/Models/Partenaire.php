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
            'date_fin'   => 'date',
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

    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

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
     */
    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Récupère le nom du type de partenaire en français.
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'bailleur_fonds' => 'Bailleur de fonds',
            'technique'      => 'Partenaire technique',
            'local'          => 'Partenaire local',
            'international'  => 'Partenaire international',
            'institutionnel' => 'Partenaire institutionnel',
            default          => $this->type ?? 'Non défini',
        };
    }

    /**
     * Retourne les initiales du partenaire calculées à partir du nom.
     *
     * ⚠️ La colonne "sigle" n'existe pas en BDD — on utilise les initiales.
     *
     * Exemples :
     *   "Food and Agriculture Organization" → "FAO"
     *   "World Food Programme"              → "WFP"
     *   "Office for the Coordination..."    → "OC"
     */
    public function sigleOuInitiales(): string
    {
        if (empty($this->nom)) {
            return '?';
        }

        // Mots ignorés (articles, prépositions) pour un acronyme propre
        $ignorer = ['de', 'du', 'des', 'la', 'le', 'les', 'et', 'for', 'of', 'the', 'and', 'a', 'an', 'in', 'on'];

        $mots = preg_split('/[\s\-]+/u', trim($this->nom));
        $initiales = '';

        foreach ($mots as $mot) {
            if ($mot === '') {
                continue;
            }
            if (in_array(mb_strtolower($mot), $ignorer, true)) {
                continue;
            }
            $initiales .= mb_strtoupper(mb_substr($mot, 0, 1));
        }

        // Fallback : si rien trouvé, on prend les 2 premières lettres du nom
        if ($initiales === '') {
            $initiales = mb_strtoupper(mb_substr($this->nom, 0, 2));
        }

        return $initiales;
    }

    /**
     * Retourne l'URL du site web avec https:// si non défini.
     */
    public function urlSiteWeb(): ?string
    {
        if (!$this->site_web) {
            return null;
        }

        if (str_starts_with($this->site_web, 'http://') 
            || str_starts_with($this->site_web, 'https://')) {
            return $this->site_web;
        }

        return 'https://' . $this->site_web;
    }

    /**
     * Retourne l'URL du logo via la route custom "media.show".
     *
     * ✅ Corrigé : utilise route('media.show') au lieu de asset('storage/...')
     */
    public function urlLogo(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return route('media.show', [
            'path' => ltrim($this->logo, '/'),
        ]);
    }

    /**
     * Retourne le nombre de projets associés.
     */
    public function nombreProjets(): int
    {
        return $this->projets()->count();
    }

    /**
     * Retourne le total des contributions financières.
     */
    public function totalContributions(): float
    {
        return (float) $this->projets()->sum('contribution');
    }
}