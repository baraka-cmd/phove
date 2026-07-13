<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'medias';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'titre',
        'type',
        'fichier',
        'miniature',
        'projet_id',
        'album',
        'description',
        'date_prise',
        'lieu',
        'auteur',
        'publier',
        'ordre',
        'user_id',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_prise' => 'date',
            'publier' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet auquel le média est lié (optionnel).
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * L'admin qui a uploadé le média.
     */
    public function uploadPar()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les médias publiés.
     */
    public function scopePublie($query)
    {
        return $query->where('publier', true);
    }

    /**
     * Scope : filtrer par type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : filtrer par album.
     */
    public function scopeAlbum($query, string $album)
    {
        return $query->where('album', $album);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si le média est une image.
     */
    public function estImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Vérifie si le média est une vidéo.
     */
    public function estVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Retourne le chemin complet du fichier pour l'affichage.
     */
    public function url(): string
    {
        return asset('storage/' . $this->fichier);
    }

    /**
     * Retourne le chemin de la miniature ou une miniature par défaut.
     */
    public function urlMiniature(): string
    {
        if ($this->miniature) {
            return asset('storage/' . $this->miniature);
        }
        return $this->url();
    }
}