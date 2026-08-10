<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'messages';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'expediteur_id',
        'destinataire_id',
        'sujet',
        'contenu',
        'lu',
        'date_lecture',
        'lu_par',
        'repondu',
        'date_reponse',
        'archived_exp',
        'archived_dest',
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
            'archived_exp' => 'boolean',
            'archived_dest' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'utilisateur qui a envoyé le message.
     */
    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    /**
     * L'utilisateur qui reçoit le message.
     */
    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    /**
     * L'utilisateur qui a marqué le message comme lu.
     */
    public function lecteur()
    {
        return $this->belongsTo(User::class, 'lu_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Messages non lus pour un utilisateur donné.
     */
    public function scopeNonLus($query, int $userId)
    {
        return $query->where('destinataire_id', $userId)
                     ->where('lu', false)
                     ->where('archived_dest', false);
    }

    /**
     * Messages reçus par un utilisateur (non archivés).
     */
    public function scopeRecus($query, int $userId)
    {
        return $query->where('destinataire_id', $userId)
                     ->where('archived_dest', false);
    }

    /**
     * Messages envoyés par un utilisateur (non archivés).
     */
    public function scopeEnvoyes($query, int $userId)
    {
        return $query->where('expediteur_id', $userId)
                     ->where('archived_exp', false);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Marque le message comme lu.
     */
    public function marquerLu(int $userId): void
    {
        if (!$this->lu) {
            $this->update([
                'lu' => true,
                'date_lecture' => now(),
                'lu_par' => $userId,
            ]);
        }
    }

    /**
     * Vérifie si le message est non lu pour le destinataire.
     */
    public function estNonLu(): bool
    {
        return !$this->lu;
    }

    /**
     * Archive le message pour l'expéditeur.
     */
    public function archiverPourExpediteur(): void
    {
        $this->update(['archived_exp' => true]);
    }

    /**
     * Archive le message pour le destinataire.
     */
    public function archiverPourDestinataire(): void
    {
        $this->update(['archived_dest' => true]);
    }
}