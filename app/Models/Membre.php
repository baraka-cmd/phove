<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'membres';

    /**
     * Les attributs modifiables en masse.
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
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un membre peut occuper plusieurs postes dans les organes (historique).
     */
    public function organes()
    {
        return $this->hasMany(Organe::class);
    }

    /**
     * Un membre a plusieurs cotisations (une par mois).
     */
    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère le nom complet du membre (format congolais).
     */
    public function nomComplet(): string
    {
        return $this->nom . ' ' . $this->post_nom . ' ' . $this->prenom;
    }

    /**
     * Vérifie si le membre est à jour de ses cotisations pour un mois donné.
     */
    public function cotisationPayeePour(string $mois): bool
    {
        return $this->cotisations()->where('mois', $mois)->exists();
    }

    /**
     * Calcule le total des cotisations payées pour l'année en cours.
     */
    public function totalCotisationsAnnee(int $annee): float
    {
        return $this->cotisations()
            ->where('mois', 'like', $annee . '-%')
            ->sum('montant');
    }
}