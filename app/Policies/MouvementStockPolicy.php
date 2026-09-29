<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\MouvementStock;
use App\Models\User;

/**
 * Policy des mouvements de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un mouvement est un enregistrement IMMUABLE dans
 *   l'historique du stock. Il est créé EXCLUSIVEMENT par
 *   les services métier (EntreeStockService, SortieStockService,
 *   InventaireStockService, ReservationStockService).
 *
 *   AUCUNE création, AUCUNE modification, AUCUNE suppression
 *   directe n'est autorisée depuis l'interface.
 *
 *   Seules les consultations et les exports sont permis.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class MouvementStockPolicy
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

    public function view(User $user, MouvementStock $mouvement): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATIONS SPÉCIFIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Consulter les mouvements d'un article.
     */
    public function viewForStock(User $user, MouvementStock $mouvement): bool
    {
        return $this->peutGererStocks($user);
    }

    /**
     * Consulter les mouvements d'un lot.
     */
    public function viewForLot(User $user, MouvementStock $mouvement): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT
    |--------------------------------------------------------------------------
    */

    /**
     * Export / impression de l'historique.
     */
    public function export(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION, MODIFICATION, SUPPRESSION
    |--------------------------------------------------------------------------
    |
    | Ces actions sont TOUJOURS refusées depuis l'interface.
    |
    | Un mouvement est créé par un Service, jamais par un
    | utilisateur à travers un Controller.
    |
    | Ces méthodes existent pour que les `@can('create', ...)`
    | et autres appels échouent explicitement, sans erreur
    | silencieuse.
    */

    /**
     * Toujours refusé.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Toujours refusé : les mouvements sont immuables.
     */
    public function update(User $user, MouvementStock $mouvement): bool
    {
        return false;
    }

    /**
     * Toujours refusé : les mouvements ne sont jamais supprimés.
     */
    public function delete(User $user, MouvementStock $mouvement): bool
    {
        return false;
    }

    /**
     * Toujours refusé : pas de restauration.
     */
    public function restore(User $user, MouvementStock $mouvement): bool
    {
        return false;
    }

    /**
     * Toujours refusé : pas de suppression définitive.
     */
    public function forceDelete(User $user, MouvementStock $mouvement): bool
    {
        return false;
    }
}