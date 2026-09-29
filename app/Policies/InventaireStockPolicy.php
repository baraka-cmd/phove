<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\InventaireStock;
use App\Models\User;

/**
 * Policy des inventaires de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un inventaire est une opération de comptage physique.
 *   Le workflow est :
 *
 *     brouillon → en_cours → termine → valide
 *
 *   Depuis brouillon ou en_cours, on peut annuler.
 *
 *   La policy s'appuie sur les méthodes `peutEtreX()` du
 *   modèle `InventaireStock`. Les contrôles transactionnels
 *   (cohérence, verrouillage, ajustements) restent dans
 *   InventaireStockService.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class InventaireStockPolicy
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

    public function view(User $user, InventaireStock $inventaire): bool
    {
        return $this->peutGererStocks($user);
    }

    /**
     * Consulter les écarts d'un inventaire.
     */
    public function viewAdjustments(User $user, InventaireStock $inventaire): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION / MODIFICATION
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /**
     * Modification : uniquement tant que l'inventaire n'est
     * pas finalisé.
     */
    public function update(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->peutEtreModifie();
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistrer un comptage.
     *
     * Uniquement possible tant que l'inventaire n'est pas
     * finalisé (brouillon ou en_cours).
     */
    public function perform(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->peutEtreCompte();
    }

    /**
     * Valider l'inventaire.
     *
     * Refusé si déjà finalisé (validé ou annulé).
     */
    public function validate(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->peutEtreValide();
    }

    /**
     * Clôturer l'inventaire.
     *
     * NB : dans notre workflow, la clôture est implicite
     * lors de la validation. Cette méthode est exposée pour
     * cohérence avec un éventuel workflow séparé.
     */
    public function close(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->peutEtreValide();
    }

    /**
     * Annuler l'inventaire.
     *
     * Uniquement possible tant que l'inventaire n'est pas
     * finalisé.
     */
    public function cancel(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->peutEtreAnnule();
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    |
    | Suppression autorisée uniquement sur un BROUILLON.
    | Un inventaire EN_COURS ou finalisé ne doit pas être
    | supprimé : on l'annule.
    */

    public function delete(User $user, InventaireStock $inventaire): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $inventaire->estBrouillon();
    }
}