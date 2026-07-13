<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * La table associée au modèle.
     */
    protected $table = 'users';

    /**
     * Les attributs modifiables en masse.
     */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'mot_de_passe',
        'role',
        'statut',
        'photo_profil',
        'derniere_connexion',
    ];

    /**
     * Les attributs cachés (jamais exposés en JSON).
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * Cast des attributs (conversion automatique des types).
     */
    protected function casts(): array
    {
        return [
            'mot_de_passe' => 'hashed',
            'derniere_connexion' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Un utilisateur peut être lié à un membre.
     */
    public function membre()
    {
        return $this->hasOne(Membre::class);
    }

    /**
     * Un utilisateur peut rédiger plusieurs articles.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Un utilisateur peut enregistrer plusieurs dons.
     */
    public function donsEnregistres()
    {
        return $this->hasMany(Don::class, 'enregistre_par');
    }

    /**
     * Un utilisateur peut enregistrer plusieurs cotisations.
     */
    public function cotisationsEnregistrees()
    {
        return $this->hasMany(Cotisation::class, 'enregistre_par');
    }

    /**
     * Un utilisateur peut uploader plusieurs médias.
     */
    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Un utilisateur peut préparer plusieurs rapports financiers.
     */
    public function rapportsFinanciers()
    {
        return $this->hasMany(RapportFinancier::class, 'prepare_par');
    }

    /**
     * Un utilisateur peut traiter plusieurs messages de contact.
     */
    public function contactsTraites()
    {
        return $this->hasMany(Contact::class, 'traite_par');
    }

    /**
     * Un utilisateur peut uploader plusieurs documents.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'upload_par');
    }

    /**
     * Vérifie si l'utilisateur est Super Admin.
     */
    public function estSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Vérifie si l'utilisateur est Admin.
     */
    public function estAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Vérifie si l'utilisateur est Éditeur.
     */
    public function estEditeur(): bool
    {
        return $this->role === 'editeur';
    }


    /**
     * Surcharge pour le système de reset password de Laravel.
     * Laravel utilise 'email' comme identifiant par défaut,
     * mais notre modèle a déjà le champ 'email'.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * Permet à Laravel d'envoyer la notification de reset.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }
}