<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'articles';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'titre',
        'slug',
        'contenu',
        'extrait',
        'categorie',
        'image',
        'statut',
        'user_id',
        'date_publication',
        'projet_id',
        'epingle',
        'vues',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_publication' => 'datetime',
            'epingle' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * L'auteur de l'article (admin).
     */
    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Le projet lié à cet article (optionnel).
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (requêtes prédéfinies)
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les articles publiés.
     */
    public function scopePublie($query)
    {
        return $query->where('statut', 'publie')
                     ->where('date_publication', '<=', now());
    }

    /**
     * Scope : articles épinglés en premier.
     */
    public function scopeEpingles($query)
    {
        return $query->where('epingle', true);
    }

    /**
     * Scope : filtrer par catégorie.
     */
    public function scopeCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'article est publié.
     */
    public function estPublie(): bool
    {
        return $this->statut === 'publie' && $this->date_publication <= now();
    }

    /**
     * Incrémente le compteur de vues.
     */
    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    /**
     * Libellé de la catégorie en français.
     */
    public function categorieLibelle(): string
    {
        return match($this->categorie) {
            'actualite' => 'Actualité',
            'projet' => 'Projet',
            'evenement' => 'Événement',
            'rapport' => 'Rapport',
            'temoignage' => 'Témoignage',
            'communique' => 'Communiqué',
            default => $this->categorie,
        };
    }

    /**
     * Génère un extrait automatique si non défini.
     */
    public function extraitOuDefaut(int $longueur = 200): string
    {
        if (!empty($this->extrait)) {
            return $this->extrait;
        }
        return Str::limit(strip_tags($this->contenu), $longueur);
    }

    /**
     * Génère le slug automatiquement à partir du titre.
     */
    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->titre);
            }
            if (empty($article->date_publication) && $article->statut === 'publie') {
                $article->date_publication = now();
            }
        });

        static::updating(function (Article $article) {
            if ($article->isDirty('titre')) {
                $article->slug = Str::slug($article->titre);
            }
        });
    }
}