<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'contacts';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'organisation',
        'sujet',
        'message',
        'motif',
        'lu',
        'date_lecture',
        'repondu',
        'date_reponse',
        'note_interne',
        'traite_par',
        'statut',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'lu' => 'boolean',
            'date_lecture' => 'datetime',
            'repondu' => 'boolean',
            'date_reponse' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'admin qui a traité ce message.
     */
    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les messages non lus.
     */
    public function scopeNonLus($query)
    {
        return $query->where('lu', false);
    }

    /**
     * Scope : messages en attente de réponse.
     */
    public function scopeEnAttente($query)
    {
        return $query->where('repondu', false);
    }

    /**
     * Scope : filtrer par statut.
     */
    public function scopeStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Marque le message comme lu.
     */
    public function marquerLu(): void
    {
        if (!$this->lu) {
            $this->update([
                'lu' => true,
                'date_lecture' => now(),
                'statut' => 'en_cours',
            ]);
        }
    }

    /**
     * Marque le message comme répondu.
     */
    public function marquerRepondu(): void
    {
        $this->update([
            'repondu' => true,
            'date_reponse' => now(),
            'statut' => 'traite',
        ]);
    }

    /**
     * Archive le message.
     */
    public function archiver(): void
    {
        $this->update(['statut' => 'archive']);
    }

    /**
     * Libellé du motif en français.
     */
    public function motifLibelle(): string
    {
        return match($this->motif) {
            'partenariat' => 'Demande de partenariat',
            'don' => 'Faire un don',
            'adhesion' => 'Demande d\'adhésion',
            'benevolat' => 'Proposition de bénévolat',
            'information' => 'Demande d\'information',
            'presse' => 'Contact presse',
            'plainte' => 'Plainte',
            'autre' => 'Autre',
            default => $this->motif,
        };
    }

    /**
     * Libellé du statut en français.
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'nouveau' => 'Nouveau',
            'en_cours' => 'En cours',
            'traite' => 'Traité',
            'archive' => 'Archivé',
            default => $this->statut,
        };
    }
}