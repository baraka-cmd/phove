<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organe extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'organes';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'membre_id',
        'structure_id',
        'organe',
        'poste',
        'niveau',
        'date_debut',
        'date_fin',
        'actif',
        'ordre',
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * La structure territoriale associée à ce poste.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function structure()
    {
        return $this->belongsTo(StructureTerritoriale::class, 'structure_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les postes actifs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope : filtrer par organe.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $organe
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrgane($query, string $organe)
    {
        return $query->where('organe', $organe);
    }

    /**
     * Scope : filtrer par niveau hiérarchique.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $niveau
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNiveau($query, string $niveau)
    {
        return $query->where('niveau', $niveau);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé de l'organe en français.
     *
     * @return string
     */
    public function organeLibelle(): string
    {
        return match($this->organe) {
            'assemblee_generale' => 'Assemblée Générale',
            'conseil_administration' => 'Conseil d\'Administration',
            'direction_executive' => 'Direction Générale',
            'commission_controle' => 'Commission de Contrôle',
            'comite_suivi' => 'Comité de Suivi',
            'comite_gestion' => 'Comité de Gestion',
            'chef_mission' => 'Chefs de Missions',
            'charge_programme' => 'Chargés des Programmes',
            'services_administratifs' => 'Services Administratifs',
            'operations_terrain' => 'Opérations de Terrain',
            'logistique' => 'Logistique',
            'intendance' => 'Intendance',
            default => $this->organe,
        };
    }

    /**
     * Libellé du niveau hiérarchique en français.
     *
     * @return string
     */
    public function niveauLibelle(): string
    {
        return match($this->niveau) {
            'international' => 'International',
            'national' => 'National',
            'provincial' => 'Provincial',
            'local' => 'Local',
            default => $this->niveau ?? 'Non défini',
        };
    }

    /**
     * Vérifie si le mandat est en cours.
     *
     * @return bool
     */
    public function mandatEnCours(): bool
    {
        return $this->actif;
    }

    /**
     * Calcule la durée du mandat en mois.
     *
     * @return int
     */
    public function dureeMandat(): int
    {
        $fin = $this->date_fin ?? now();
        return $this->date_debut->diffInMonths($fin);
    }

    /**
     * Retourne le nom complet du membre occupant le poste.
     *
     * @return string
     */
    public function nomMembre(): string
    {
        return $this->membre ? $this->membre->nomComplet() : 'Poste vacant';
    }

    /**
     * Retourne le nom de la structure associée.
     *
     * @return string
     */
    public function nomStructure(): string
    {
        return $this->structure ? $this->structure->nom : '—';
    }
}