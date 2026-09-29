<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\LigneReservationStock;
use App\Models\User;

/**
 * Policy des lignes de réservation de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne de réservation est TOUJOURS créée et modifiée
 *   par ReservationStockService, jamais directement par un
 *   utilisateur.
 *
 *   Depuis l'interface :
 *     - on consulte les lignes via la réservation parente ;
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
class LigneReservationStockPolicy
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

    public function view(User $user, LigneReservationStock $ligne): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION, MODIFICATION, SUPPRESSION — TOUJOURS REFUSÉES
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, LigneReservationStock $ligne): bool
    {
        return false;
    }

    public function delete(User $user, LigneReservationStock $ligne): bool
    {
        return false;
    }
}