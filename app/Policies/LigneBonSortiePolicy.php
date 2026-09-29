<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\LigneBonSortie;
use App\Models\User;

/**
 * Policy des lignes de bon de sortie.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne de bon de sortie est TOUJOURS créée et
 *   modifiée par SortieStockService, jamais directement
 *   par un utilisateur.
 *
 *   Depuis l'interface :
 *     - on consulte les lignes via le bon parent ;
 *     - on ne crée JAMAIS une ligne orpheline ;
 *     - on ne modifie JAMAIS une quantité directement.
 *
 * RÈGLE :
 *
 *   Les actions `create`, `update`, `delete` sont TOUJOURS
 *   refusées. Les vues ne doivent jamais exposer ces actions.
 *
 * ACCÈS DE BASE :
 *
 *   La consultation exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class LigneBonSortiePolicy
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

    public function view(User $user, LigneBonSortie $ligne): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION, MODIFICATION, SUPPRESSION — TOUJOURS REFUSÉES
    |--------------------------------------------------------------------------
    |
    | Les lignes sont créées par SortieStockService::creerBon()
    | et mises à jour par SortieStockService::effectuerLigne().
    |
    | Aucune action directe n'est autorisée.
    */

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, LigneBonSortie $ligne): bool
    {
        return false;
    }

    public function delete(User $user, LigneBonSortie $ligne): bool
    {
        return false;
    }
}