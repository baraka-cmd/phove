<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicateurProjet extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'indicateurs_projet';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'projet_id',
        'nom_indicateur',
        'valeur_cible',
        'valeur_actuelle',
        'unite',
        'description',
        'date_mise_a_jour',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_mise_a_jour' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet auquel appartient cet indicateur.
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
     * Calcule le pourcentage d'atteinte de l'objectif.
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
     */
    public function formater(): string
    {
        return $this->valeur_actuelle . ' / ' . $this->valeur_cible . ' ' . $this->unite;
    }

    /**
     * Vérifie si l'objectif est atteint.
     */
    public function objectifAtteint(): bool
    {
        return $this->valeur_actuelle >= $this->valeur_cible;
    }
}