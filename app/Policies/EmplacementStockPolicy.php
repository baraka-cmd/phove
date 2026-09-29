<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\EmplacementStock;
use App\Models\User;

/**
 * Policy des emplacements de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un emplacement est une référence organisationnelle
 *   (magasin, dépôt, zone). Il sert :
 *     - d'emplacement principal à un article ;
 *     - de localisation à un mouvement ;
 *     - de périmètre à un inventaire.
 *
 * INVARIANTS :
 *
 *   - Un emplacement utilisé par des mouvements n'est
 *     JAMAIS supprimé physiquement. On le désactive.
 *   - Un emplacement inactif ne peut plus être sélectionné
 *     pour de nouvelles opérations.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class EmplacementStockPolicy
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

    public function view(User $user, EmplacementStock $emplacement): bool
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

    public function update(User $user, EmplacementStock $emplacement): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CYCLE DE VIE
    |--------------------------------------------------------------------------
    */

    /**
     * Activer un emplacement inactif.
     */
    public function activate(User $user, EmplacementStock $emplacement): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $emplacement->estInactif();
    }

    /**
     * Désactiver un emplacement actif.
     *
     * La désactivation n'est PAS autorisée si l'emplacement
     * est encore utilisé comme emplacement principal par un
     * article actif. La vérification fine reste dans le
     * service EmplacementStockService (à créer si nécessaire).
     */
    public function deactivate(User $user, EmplacementStock $emplacement): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if ($emplacement->estInactif()) {
            return false;
        }

        if ($emplacement->stocksPrincipaux()->exists()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    |
    | Suppression autorisée UNIQUEMENT si :
    |   - aucun mouvement n'a été localisé dans cet emplacement ;
    |   - aucun inventaire n'a ciblé cet emplacement ;
    |   - aucun article n'y est rattaché comme emplacement principal.
    |
    | En pratique, on désactive plutôt que de supprimer.
    */

    public function delete(User $user, EmplacementStock $emplacement): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if ($emplacement->mouvements()->exists()) {
            return false;
        }

        if ($emplacement->inventaires()->exists()) {
            return false;
        }

        if ($emplacement->stocksPrincipaux()->exists()) {
            return false;
        }

        return true;
    }
}