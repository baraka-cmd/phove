<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\InventaireStockLigne;
use App\Models\User;

/**
 * Policy des lignes d'inventaire de stock.
 *
 * SÉMANTIQUE :
 *
 *   Une ligne d'inventaire est TOUJOURS créée par
 *   InventaireStockService::creer() et mise à jour par
 *   InventaireStockService::compter() (via enregistrerComptage).
 *
 *   L'utilisateur n'écrit JAMAIS directement dans la table ;
 *   il saisit un comptage qui passe par le service métier.
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
class InventaireStockLignePolicy
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

    public function view(User $user, InventaireStockLigne $ligne): bool
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

    public function update(User $user, InventaireStockLigne $ligne): bool
    {
        return false;
    }

    public function delete(User $user, InventaireStockLigne $ligne): bool
    {
        return false;
    }
}