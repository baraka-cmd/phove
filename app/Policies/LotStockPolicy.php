<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\LotStock;
use App\Models\User;

/**
 * Policy des lots de stock.
 *
 * SÉMANTIQUE :
 *
 *   Un lot est une unité physique d'article. Il est géré
 *   exclusivement par les Services métier (EntreeStockService,
 *   SortieStockService, LotStockService).
 *
 *   Cette policy autorise uniquement :
 *     - la consultation ;
 *     - le blocage / déblocage manuel ;
 *     - le traitement des expirations ;
 *     - la création administrative (rare, via EntreeStockService).
 *
 *   Elle NE permet PAS :
 *     - de modifier la quantité d'un lot ;
 *     - de supprimer un lot ayant de l'historique.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class LotStockPolicy
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

    public function view(User $user, LotStock $lot): bool
    {
        return $this->peutGererStocks($user);
    }

    /**
     * Consultation des lots proches de l'expiration.
     */
    public function viewExpiration(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    |
    | Un lot est créé NORMALEMENT via une entrée de stock
    | (EntreeStockService). Cette méthode ne sert qu'aux cas
    | exceptionnels d'administration.
    */

    public function create(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION
    |--------------------------------------------------------------------------
    |
    | On ne modifie JAMAIS la quantité d'un lot depuis cette
    | policy. Seules les métadonnées (dates, fournisseur)
    | peuvent être corrigées, et uniquement sur un lot ACTIF
    | ou EPUISE.
    |
    | Les règles fines (par exemple ne pas modifier un lot
    | ayant déjà des mouvements) restent dans le Service.
    */

    public function update(User $user, LotStock $lot): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        /*
         * Un lot expiré ou bloqué est figé.
         */
        if ($lot->estExpire() || $lot->estBloque()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCAGE / DÉBLOCAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Bloquer un lot.
     *
     * Seul un lot ACTIF peut être bloqué.
     */
    public function block(User $user, LotStock $lot): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $lot->statut->peutEtreBloque();
    }

    /**
     * Débloquer un lot.
     *
     * Seul un lot BLOQUE peut être débloqué.
     */
    public function unblock(User $user, LotStock $lot): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $lot->statut->peutEtreDebloque();
    }

    /*
    |--------------------------------------------------------------------------
    | TRAITEMENT DES EXPIRATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Traiter les expirations globales.
     *
     * Action sur la collection entière des lots, pas sur
     * un lot individuel.
     */
    public function processExpirations(User $user): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    |
    | Suppression autorisée uniquement si :
    |   - le lot n'a jamais servi (aucun mouvement) ;
    |   - sa quantité actuelle est nulle.
    |
    | En pratique, on ne supprime jamais un lot :
    | on le laisse à EPUISE pour préserver la traçabilité.
    */

    public function delete(User $user, LotStock $lot): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if ((float) $lot->quantite_actuelle > 0) {
            return false;
        }

        if ($lot->mouvements()->exists()) {
            return false;
        }

        return true;
    }
}