<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'partenaires';

    /**
     * Les attributs modifiables en masse.
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
     */
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_partenaire')
                    ->withPivot('role', 'contribution', 'description', 'date_debut', 'date_fin')
                    ->withTimestamps();
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
            'technique' => 'Partenaire technique',
            'local' => 'Partenaire local',
            'international' => 'Partenaire international',
            'institutionnel' => 'Partenaire institutionnel',
            default => $this->type,
        };
    }
}