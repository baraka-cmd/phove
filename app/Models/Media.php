<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'taille',
        'projet_id',
        'article_id',      // ⭐ AJOUTER CETTE LIGNE
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
            'publier'    => 'boolean',
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
     * Retourne l'URL publique du fichier via la route custom "media.show".
     *
     * ⚠️ Ne PAS utiliser asset('storage/' . ...) car la route /media/{path}
     *    contourne le 403 de php artisan serve.
     */
    public function url(): ?string
    {
        if (!$this->fichier) {
            return null;
        }

        // Les URL Cloudinary doivent être utilisées directement : la route locale
        // /media/{path} ne sait servir que les fichiers du disque public.
        if (preg_match('~^https?://~i', $this->fichier)) {
            return $this->fichier;
        }

        return route('media.show', [
            'path' => ltrim($this->fichier, '/'),
        ]);
    }

    /**
     * Retourne l'URL de la miniature si elle existe,
     * sinon retombe sur l'URL du fichier principal.
     */
    public function urlMiniature(): ?string
    {
        if ($this->miniature) {
            if (preg_match('~^https?://~i', $this->miniature)) {
                return $this->miniature;
            }

            return route('media.show', [
                'path' => ltrim($this->miniature, '/'),
            ]);
        }

        // Pour les images Cloudinary, générer une miniature optimisée.
        if ($this->fichier && preg_match('~^https?://~i', $this->fichier)) {
            return media_miniature($this->fichier, null, 600, 420);
        }

        return $this->url();
    }

    /**
     * Vérifie si le fichier principal existe réellement sur le disque.
     *
     * Utile pour afficher un placeholder si le fichier a été supprimé
     * manuellement sans mise à jour de la BDD.
     */
    public function fichierExiste(): bool
    {
        if (!$this->fichier) {
            return false;
        }

        if (preg_match('~^https?://~i', $this->fichier)) {
            return true;
        }

        return Storage::disk('public')->exists($this->fichier);
    }

    /**
     * Vérifie si la miniature existe réellement sur le disque.
     */
    public function miniatureExiste(): bool
    {
        if (!$this->miniature) {
            return false;
        }

        if (preg_match('~^https?://~i', $this->miniature)) {
            return true;
        }

        return Storage::disk('public')->exists($this->miniature);
    }

    /**
     * Retourne la taille du fichier formatée pour l'affichage.
     * Retourne null si le fichier n'existe pas.
     */
    public function tailleFormatee(): ?string
    {
        if (!$this->fichier) {
            return null;
        }

        // La taille est enregistrée lors de l'upload, y compris pour Cloudinary.
        if (preg_match('~^https?://~i', $this->fichier)) {
            if (!$this->taille) {
                return null;
            }
            $taille = (int) $this->taille;
        } else {
            if (!Storage::disk('public')->exists($this->fichier)) {
                return null;
            }
            $taille = Storage::disk('public')->size($this->fichier);
        }

        if ($taille > 1048576) {
            return round($taille / 1048576, 1) . ' Mo';
        }

        return round($taille / 1024, 1) . ' Ko';
    }
}