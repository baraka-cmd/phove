<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssembleeGenerale extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'assemblees_generales';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'type',
        'date_reunion',
        'lieu',
        'ordre_du_jour',
        'membres_total',
        'membres_presents',
        'membres_absents',
        'quorum_atteint',
        'decisions',
        'resolutions',
        'pv_fichier',
        'statut',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_reunion' => 'date',
            'quorum_atteint' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Pas de relations directes. Les participants sont les membres effectifs.
     * Cette table est autonome et documente la gouvernance.
     */

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Libellé du type d'AG en français.
     */
    public function typeLibelle(): string
    {
        return match($this->type) {
            'ordinaire' => 'Assemblée Générale Ordinaire',
            'extraordinaire' => 'Assemblée Générale Extraordinaire',
            default => $this->type,
        };
    }

    /**
     * Libellé du statut en français.
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'planifiee' => 'Planifiée',
            'tenue' => 'Tenue',
            'annulee' => 'Annulée',
            'reportee' => 'Reportée',
            default => $this->statut,
        };
    }

    /**
     * Calcule le taux de participation.
     */
    public function tauxParticipation(): float
    {
        if ($this->membres_total <= 0) {
            return 0;
        }
        return round(($this->membres_presents / $this->membres_total) * 100, 1);
    }

    /**
     * Vérifie si le quorum statutaire est atteint (majorité simple, article 25).
     */
    public function verifierQuorum(): bool
    {
        return $this->membres_presents >= ($this->membres_total / 2);
    }

    /**
     * Vérifie si l'AG s'est tenue.
     */
    public function estTenue(): bool
    {
        return $this->statut === 'tenue';
    }
}