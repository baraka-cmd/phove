<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiaire extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'beneficiaires';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'projet_id',
        'nom',
        'post_nom',
        'prenom',
        'sexe',
        'age',
        'categorie',
        'localisation',
        'province',
        'type_aide',
        'date_aide',
        'description_aide',
        'telephone',
        'temoignage',
        'temoignage_public',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_aide' => 'date',
            'temoignage_public' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet qui a apporté l'aide.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Nom complet du bénéficiaire.
     */
    public function nomComplet(): string
    {
        $parties = array_filter([$this->nom, $this->post_nom, $this->prenom]);
        return implode(' ', $parties);
    }

    /**
     * Libellé de la catégorie en français.
     */
    public function categorieLibelle(): string
    {
        return match($this->categorie) {
            'femme' => 'Femme',
            'enfant' => 'Enfant',
            'handicape' => 'Personne handicapée',
            'troisieme_age' => '3ème âge',
            'orphelin' => 'Orphelin',
            'deplace_interne' => 'Déplacé interne',
            'refugie' => 'Réfugié',
            'rapatrie' => 'Rapatrié',
            'autre' => 'Autre',
            default => $this->categorie,
        };
    }

    /**
     * Vérifie si le bénéficiaire a donné un témoignage publiable.
     */
    public function aTemoignagePublic(): bool
    {
        return $this->temoignage_public && !empty($this->temoignage);
    }
}