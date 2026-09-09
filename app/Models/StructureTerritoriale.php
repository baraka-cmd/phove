<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StructureTerritoriale extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'structures_territoriales';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'type',
        'pays',
        'province',
        'territoire',
        'adresse',
        'description',
        'parent_id',
        'statut',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'statut' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * La structure parente (hiérarchie : Station → Base → Antenne).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(StructureTerritoriale::class, 'parent_id');
    }

    /**
     * Les structures enfants (hiérarchie inversée).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enfants()
    {
        return $this->hasMany(StructureTerritoriale::class, 'parent_id');
    }

    /**
     * Les organes (postes) rattachés à cette structure.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organes()
    {
        return $this->hasMany(Organe::class, 'structure_id');
    }

    /**
     * Les membres rattachés à cette structure via les organes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function membres()
    {
        return $this->hasManyThrough(
            Membre::class,
            Organe::class,
            'structure_id',
            'id',
            'id',
            'membre_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les structures actives.
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
     * Scope : uniquement les stations (pays).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStations($query)
    {
        return $query->where('type', 'station');
    }

    /**
     * Scope : uniquement les bases (provinces).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBases($query)
    {
        return $query->where('type', 'base');
    }

    /**
     * Scope : uniquement les antennes (territoires/communes).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAntennes($query)
    {
        return $query->where('type', 'antenne');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du type de structure en français.
     *
     * @return string
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'station' => 'Station (Pays)',
            'base' => 'Base (Province)',
            'antenne' => 'Antenne (Territoire/Commune)',
            default => $this->type,
        };
    }

    /**
     * Vérifie si la structure est active.
     *
     * @return bool
     */
    public function estActive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Retourne la localisation complète de la structure.
     *
     * @return string
     */
    public function localisationComplete(): string
    {
        $parties = array_filter([
            $this->territoire,
            $this->province,
            $this->pays,
        ]);

        return implode(', ', $parties) ?: '—';
    }

    /**
     * Retourne le nombre de postes rattachés à cette structure.
     *
     * @return int
     */
    public function nombrePostes(): int
    {
        return $this->organes()->where('actif', true)->count();
    }

    /**
     * Retourne le nombre de membres rattachés à cette structure.
     *
     * @return int
     */
    public function nombreMembres(): int
    {
        return $this->membres()->count();
    }

    /**
     * Vérifie si la structure a des enfants.
     *
     * @return bool
     */
    public function aDesEnfants(): bool
    {
        return $this->enfants()->count() > 0;
    }
}