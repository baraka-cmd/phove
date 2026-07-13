<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organe extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'organes';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'membre_id',
        'organe',
        'poste',
        'date_debut',
        'date_fin',
        'actif',
        'ordre',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'actif' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le membre qui occupe ce poste.
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé de l'organe en français.
     */
    public function organeLibelle(): string
    {
        return match($this->organe) {
            'assemblee_generale' => 'Assemblée Générale',
            'conseil_administration' => 'Conseil d\'Administration',
            'direction_executive' => 'Direction Exécutive',
            'commission_controle' => 'Commission de Contrôle',
            default => $this->organe,
        };
    }

    /**
     * Vérifie si le mandat est en cours.
     */
    public function mandatEnCours(): bool
    {
        return $this->actif;
    }

    /**
     * Calcule la durée du mandat en mois.
     */
    public function dureeMandat(): int
    {
        $fin = $this->date_fin ?? now();
        return $this->date_debut->diffInMonths($fin);
    }
}