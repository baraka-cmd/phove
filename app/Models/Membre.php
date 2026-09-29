<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'membres';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nom',
        'post_nom',
        'prenom',
        'sexe',
        'adresse',
        'telephone',
        'email',
        'photo',
        'categorie',
        'date_adhesion',
        'frais_adhesion',
        'cotisation_mensuelle',
        'statut',
        'motif_sanction',
        'date_sanction',
    ];

    /**
     * Cast des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_adhesion' => 'date',
            'date_sanction' => 'date',
            'frais_adhesion' => 'decimal:2',
            'cotisation_mensuelle' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Un membre peut être lié à un compte utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un membre peut occuper plusieurs postes dans les organes (historique).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organes()
    {
        return $this->hasMany(Organe::class);
    }

    /**
     * Un membre a plusieurs cotisations (une par mois).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    /**
     * Le dossier personnel du membre (12 pièces exigées).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dossierPersonnel()
    {
        return $this->hasOne(DossierPersonnel::class, 'membre_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le nom complet comme attribut Eloquent.
     *
     * Utilisation :
     * $membre->nom_complet
     *
     * Format congolais :
     * Nom Post-nom Prénom.
     *
     * @return string
     */
    public function getNomCompletAttribute(): string
    {
        return $this->nomComplet();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : uniquement les membres actifs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope : membres fondateurs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFondateurs($query)
    {
        return $query->where('categorie', 'fondateur');
    }

    /**
     * Scope : membres adhérents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdherents($query)
    {
        return $query->where('categorie', 'adherent');
    }

    /**
     * Scope : membres d'honneur.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHonneur($query)
    {
        return $query->where('categorie', 'honneur');
    }

    /**
     * Scope : membres sympathisants.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSympathisants($query)
    {
        return $query->where('categorie', 'sympathisant');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère le nom complet du membre.
     *
     * Cette méthode est conservée afin de ne pas casser
     * le code existant qui utilise $membre->nomComplet().
     *
     * Format congolais :
     * Nom Post-nom Prénom.
     *
     * @return string
     */
    public function nomComplet(): string
    {
        $parties = array_filter([
            $this->nom,
            $this->post_nom,
            $this->prenom,
        ]);

        return implode(' ', $parties);
    }

    /**
     * Vérifie si le membre est à jour de ses cotisations pour un mois donné.
     *
     * @param string $mois
     * @return bool
     */
    public function cotisationPayeePour(string $mois): bool
    {
        return $this->cotisations()
            ->where('mois', $mois)
            ->exists();
    }

    /**
     * Calcule le total des cotisations payées pour l'année en cours.
     *
     * @param int $annee
     * @return float
     */
    public function totalCotisationsAnnee(int $annee): float
    {
        return $this->cotisations()
            ->where('mois', 'like', $annee . '-%')
            ->sum('montant');
    }

    /**
     * Libellé de la catégorie du membre en français.
     *
     * @return string
     */
    public function categorieLibelle(): string
    {
        return match ($this->categorie) {
            'fondateur' => 'Membre fondateur',
            'adherent' => 'Membre adhérent',
            'honneur' => 'Membre d\'honneur',
            'sympathisant' => 'Membre sympathisant',
            default => $this->categorie ?? 'Non défini',
        };
    }

    /**
     * Libellé du statut du membre en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match ($this->statut) {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu',
            'exclu' => 'Exclu',
            'decede' => 'Décédé',
            'en_attente' => 'En attente',
            default => $this->statut ?? 'Non défini',
        };
    }

    /**
     * Vérifie si le membre a un dossier personnel.
     *
     * @return bool
     */
    public function aDossierPersonnel(): bool
    {
        return $this->dossierPersonnel()->exists();
    }

    /**
     * Vérifie si le membre est à jour de sa cotisation mensuelle.
     *
     * @return bool
     */
    public function cotisationAJour(): bool
    {
        $moisCourant = now()->format('Y-m');

        return $this->cotisationPayeePour($moisCourant);
    }

    /**
     * Retourne les initiales du membre pour les avatars.
     *
     * @return string
     */
    public function initiales(): string
    {
        $initiales = '';

        if ($this->prenom) {
            $initiales .= strtoupper(
                mb_substr($this->prenom, 0, 1)
            );
        }

        if ($this->nom) {
            $initiales .= strtoupper(
                mb_substr($this->nom, 0, 1)
            );
        }

        return $initiales ?: '?';
    }

    /**
     * Retourne l'URL de la photo du membre ou null.
     *
     * @return string|null
     */
    public function urlPhoto(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }
}