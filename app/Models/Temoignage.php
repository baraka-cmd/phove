<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temoignage extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'temoignages';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'projet_id',
        'nom',
        'post_nom',
        'prenom',
        'sexe',
        'age',
        'categorie',
        'province',
        'localisation',
        'contenu',
        'photo',
        'statut',
        'motif_rejet',
        'date_soumission',
        'date_validation',
        'traite_par',
        'ordre',
    ];

    /**
     * Cast des attributs.
     */
    protected function casts(): array
    {
        return [
            'date_soumission' => 'datetime',
            'date_validation' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Le projet concerné par ce témoignage.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * L'admin qui a traité ce témoignage.
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
     * Scope : uniquement les témoignages approuvés.
     */
    public function scopeApprouve($query)
    {
        return $query->where('statut', 'approuve');
    }

    /**
     * Scope : témoignages en attente de validation.
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope : filtrer par statut.
     */
    public function scopeStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope : filtrer par catégorie.
     */
    public function scopeCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Scope : filtrer par province.
     */
    public function scopeProvince($query, string $province)
    {
        return $query->where('province', $province);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Nom complet du bénéficiaire (format congolais : Nom Post-nom Prénom).
     */
    public function nomComplet(): string
    {
        $parties = array_filter([$this->nom, $this->post_nom, $this->prenom]);
        return implode(' ', $parties);
    }

    /**
     * Initiales pour l'avatar (2 premières lettres).
     */
    public function initiales(): string
    {
        $nom = $this->nom;
        $prenom = $this->prenom;
        
        $initiales = '';
        if ($prenom) $initiales .= strtoupper(mb_substr($prenom, 0, 1));
        if ($nom) $initiales .= strtoupper(mb_substr($nom, 0, 1));
        
        return $initiales ?: '?';
    }

    /**
     * Vérifie si le témoignage est approuvé.
     */
    public function estApprouve(): bool
    {
        return $this->statut === 'approuve';
    }

    /**
     * Vérifie si le témoignage est en attente.
     */
    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Libellé du statut en français.
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'approuve' => 'Approuvé',
            'rejete' => 'Rejeté',
            'archive' => 'Archivé',
            default => $this->statut,
        };
    }

    /**
     * Libellé de la catégorie en français.
     */
    public function categorieLibelle(): string
    {
        return match($this->categorie) {
            'femme' => 'Femme',
            'enfant' => 'Enfant',
            'handicape' => 'Personne handicapée',
            'troisieme_age' => '3ème âge',
            'orphelin' => 'Orphelin',
            'deplace_interne' => 'Déplacé interne',
            'refugie' => 'Réfugié',
            'rapatrie' => 'Rapatrié',
            'autre' => 'Autre',
            default => $this->categorie,
        };
    }

    /**
     * Retourne l'URL de la photo du bénéficiaire ou null.
     */
    public function urlPhoto(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    /**
     * Extrait du témoignage (200 premiers caractères).
     */
    public function extrait(int $longueur = 200): string
    {
        return \Illuminate\Support\Str::limit($this->contenu, $longueur);
    }

    /**
     * Approuve le témoignage.
     */
    public function approuver(int $adminId): void
    {
        $this->update([
            'statut' => 'approuve',
            'date_validation' => now(),
            'traite_par' => $adminId,
            'motif_rejet' => null,
        ]);
    }

    /**
     * Rejette le témoignage.
     */
    public function rejeter(int $adminId, string $motif): void
    {
        $this->update([
            'statut' => 'rejete',
            'date_validation' => now(),
            'traite_par' => $adminId,
            'motif_rejet' => $motif,
        ]);
    }

    /**
     * Archive le témoignage.
     */
    public function archiver(): void
    {
        $this->update(['statut' => 'archive']);
    }
}