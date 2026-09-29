<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Les attributs modifiables en masse.
     *
     * @var array<int, string>
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
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * Cast des attributs (conversion automatique des types).
     *
     * @return array<string, string>
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function membre()
    {
        return $this->hasOne(Membre::class);
    }

    /**
     * Un utilisateur peut rédiger plusieurs articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Un utilisateur peut enregistrer plusieurs dons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function donsEnregistres()
    {
        return $this->hasMany(Don::class, 'enregistre_par');
    }

    /**
     * Un utilisateur peut enregistrer plusieurs cotisations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cotisationsEnregistrees()
    {
        return $this->hasMany(Cotisation::class, 'enregistre_par');
    }

    /**
     * Un utilisateur peut uploader plusieurs médias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Un utilisateur peut préparer plusieurs rapports financiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rapportsFinanciers()
    {
        return $this->hasMany(RapportFinancier::class, 'prepare_par');
    }

    /**
     * Un utilisateur peut traiter plusieurs messages de contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactsTraites()
    {
        return $this->hasMany(Contact::class, 'traite_par');
    }

    /**
     * Un utilisateur peut uploader plusieurs documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'upload_par');
    }

    /**
     * Un utilisateur peut valider plusieurs dossiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dossiersValides()
    {
        return $this->hasMany(DossierPersonnel::class, 'valide_par');
    }

    /**
     * Un utilisateur peut valider plusieurs salaires.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salairesValides()
    {
        return $this->hasMany(Salaire::class, 'valide_par');
    }

    /**
     * Un utilisateur peut payer plusieurs salaires.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salairesPayes()
    {
        return $this->hasMany(Salaire::class, 'paye_par');
    }

    /**
     * Un utilisateur peut effectuer plusieurs mouvements de stock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mouvementsStocks()
    {
        return $this->hasMany(MouvementStock::class, 'effectue_par');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : utilisateurs actifs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope : filtrer par rôle.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES DE VÉRIFICATION DES RÔLES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'utilisateur est Super Admin.
     *
     * @return bool
     */
    public function estSuperAdmin(): bool
    {
        return $this->role === Roles::SUPER_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est Directeur Général.
     *
     * @return bool
     */
    public function estDG(): bool
    {
        return $this->role === Roles::DG;
    }

    /**
     * Vérifie si l'utilisateur est Chef de Mission.
     *
     * @return bool
     */
    public function estChefMission(): bool
    {
        return $this->role === Roles::CHEF_MISSION;
    }

    /**
     * Vérifie si l'utilisateur est Chargé de Programme.
     *
     * @return bool
     */
    public function estChargeProgramme(): bool
    {
        return $this->role === Roles::CHARGE_PROGRAMME;
    }

    /**
     * Vérifie si l'utilisateur est Secrétaire.
     *
     * @return bool
     */
    public function estSecretaire(): bool
    {
        return $this->role === Roles::SECRETAIRE;
    }

    /**
     * Vérifie si l'utilisateur est Comptable.
     *
     * @return bool
     */
    public function estComptable(): bool
    {
        return $this->role === Roles::COMPTABLE;
    }

    /**
     * Vérifie si l'utilisateur est Caissier.
     *
     * @return bool
     */
    public function estCaissier(): bool
    {
        return $this->role === Roles::CAISSIER;
    }

    /**
     * Vérifie si l'utilisateur est Éditeur.
     *
     * @return bool
     */
    public function estEditeur(): bool
    {
        return $this->role === Roles::EDITEUR;
    }

    /**
     * Vérifie si l'utilisateur est un simple utilisateur.
     *
     * @return bool
     */
    public function estUtilisateur(): bool
    {
        return $this->role === Roles::UTILISATEUR;
    }

    /**
     * Vérifie si l'utilisateur a accès à l'administration.
     *
     * @return bool
     */
    public function aAccesAdmin(): bool
    {
        return in_array($this->role, array_keys(Roles::adminRoles()));
    }

    /**
     * Vérifie si l'utilisateur peut gérer les membres.
     *
     * @return bool
     */
    public function peutGererMembres(): bool
    {
        return in_array($this->role, Roles::gestionMembres());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les finances.
     *
     * @return bool
     */
    public function peutGererFinances(): bool
    {
        return in_array($this->role, Roles::gestionFinances());
    }

    /**
 * Scope : utilisateurs actifs uniquement.
 *
 * À utiliser partout où on liste des utilisateurs dans
 * les formulaires métier (bons de sortie, inventaires,
 * réservations, etc.).
 */
public function scopeActifs(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
{
    return $query->where('statut', 'actif');
}

    /**
     * Vérifie si l'utilisateur peut valider les salaires.
     *
     * @return bool
     */
    public function peutValiderSalaires(): bool
    {
        return in_array($this->role, Roles::validationSalaires());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les stocks.
     *
     * @return bool
     */
    public function peutGererStocks(): bool
    {
        return in_array($this->role, Roles::gestionStocks());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les projets.
     *
     * @return bool
     */
    public function peutGererProjets(): bool
    {
        return in_array($this->role, Roles::gestionProjets());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les dossiers.
     *
     * @return bool
     */
    public function peutGererDossiers(): bool
    {
        return in_array($this->role, Roles::gestionDossiers());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les utilisateurs.
     *
     * @return bool
     */
    public function peutGererUtilisateurs(): bool
    {
        return in_array($this->role, Roles::gestionUtilisateurs());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les paramètres.
     *
     * @return bool
     */
    public function peutGererParametres(): bool
    {
        return in_array($this->role, Roles::gestionParametres());
    }

    /**
     * Vérifie si l'utilisateur peut gérer la gouvernance.
     *
     * @return bool
     */
    public function peutGererGouvernance(): bool
    {
        return in_array($this->role, Roles::gestionGouvernance());
    }

    /**
     * Vérifie si l'utilisateur peut gérer les structures.
     *
     * @return bool
     */
    public function peutGererStructures(): bool
    {
        return in_array($this->role, Roles::gestionStructures());
    }

    /**
     * Vérifie si l'utilisateur peut rédiger du contenu.
     *
     * @return bool
     */
    public function peutRedigerContenu(): bool
    {
        return in_array($this->role, Roles::redactionContenu());
    }

    /**
     * Vérifie si l'utilisateur a accès à la messagerie.
     *
     * @return bool
     */
    public function aAccesMessagerie(): bool
    {
        return in_array($this->role, Roles::messagerie());
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     *
     * @param string|array $roles
     * @return bool
     */
    public function aRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le libellé du rôle en français.
     *
     * @return string
     */
    public function roleLibelle(): string
    {
        return Roles::libelle($this->role);
    }

    /**
     * Retourne le libellé du statut en français.
     *
     * @return string
     */
    public function statutLibelle(): string
    {
        return match($this->statut) {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu',
            default => $this->statut,
        };
    }

    /**
     * Vérifie si l'utilisateur est actif.
     *
     * @return bool
     */
    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Retourne les initiales pour l'avatar.
     *
     * @return string
     */
    public function initiales(): string
    {
        $mots = explode(' ', $this->nom);
        $initiales = '';
        foreach ($mots as $mot) {
            if (strlen($mot) > 1) {
                $initiales .= strtoupper(substr($mot, 0, 1));
            }
        }
        return $initiales ?: strtoupper(substr($this->nom, 0, 2));
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHENTIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Surcharge pour le système de reset password de Laravel.
     * Laravel utilise 'email' comme identifiant par défaut.
     *
     * @return string
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * Permet à Laravel d'envoyer la notification de reset.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }
}