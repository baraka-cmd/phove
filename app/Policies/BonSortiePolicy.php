<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\BonSortie;
use App\Models\User;

/**
 * Policy des bons de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Cette policy autorise les actions du WORKFLOW d'un bon
 *   de sortie. Elle s'appuie sur les méthodes du modèle
 *   `BonSortie::peutEtreX()` qui centralisent les règles
 *   de transition.
 *
 *   Les contrôles transactionnels (disponibilité, FEFO)
 *   restent dans SortieStockService.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class BonSortiePolicy
{
    /*
    |--------------------------------------------------------------------------
    | HELPER COMMUN
    |--------------------------------------------------------------------------
    */

    private function peutGererStocks(User $user): bool
    {
        return $user->estActif()
            && in_array(
                $user->role,
                Roles::gestionStocks(),
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATION
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    public function view(User $user, BonSortie $bon): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION / SUPPRESSION
    |--------------------------------------------------------------------------
    */

    /**
     * Modification : uniquement tant que le bon est modifiable
     * (brouillon ou soumis).
     */
    public function update(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->peutEtreModifie();
    }

    /**
     * Suppression : uniquement un brouillon.
     */
    public function delete(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->estBrouillon();
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW
    |--------------------------------------------------------------------------
    */

    /**
     * Soumettre : uniquement un brouillon.
     */
    public function submit(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->peutEtreSoumis();
    }

    /**
     * Valider : uniquement un bon soumis.
     *
     * NB : le validateur ne peut pas être le demandeur.
     * (Séparation des rôles, exigence de contrôle interne.)
     */
    public function validate(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if (!$bon->peutEtreValide()) {
            return false;
        }

        return $bon->demandeur_id !== $user->id;
    }

    /**
     * Préparer : uniquement un bon validé.
     */
    public function prepare(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->peutEtrePrepare();
    }

    /**
     * Exécuter la sortie physique : uniquement en préparation.
     */
    public function release(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->peutEtreExecute();
    }

    /**
     * Annuler : brouillon, soumis ou validé.
     */
    public function cancel(User $user, BonSortie $bon): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $bon->peutEtreAnnule();
    }

    /*
    |--------------------------------------------------------------------------
    | IMPRESSION
    |--------------------------------------------------------------------------
    */

    public function print(User $user, BonSortie $bon): bool
    {
        return $this->peutGererStocks($user);
    }
}