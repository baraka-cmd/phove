<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Enums\StatutStock;
use App\Models\Stock;
use App\Models\User;

/**
 * Policy du catalogue d'articles de stock.
 *
 * SÉMANTIQUE :
 *
 *   Cette policy autorise les actions sur la FICHE article
 *   (catalogue). Elle n'autorise PAS les opérations de
 *   quantité (entrées, sorties, inventaires, etc.) qui
 *   possèdent leurs propres policies et services.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 *
 * RÈGLES MÉTIER (vérifiées ici en lecture rapide) :
 *
 *   - On ne modifie pas un article archivé.
 *   - On n'archive pas un article qui a encore du stock
 *     physique ou réservé.
 *   - On ne supprime que si aucun lot, aucun mouvement.
 *
 *   Ces règles sont AUSSI vérifiées dans StockService
 *   (qui reste la source de vérité transactionnelle).
 */
class StockPolicy
{
    /*
    |--------------------------------------------------------------------------
    | HELPER COMMUN
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie que l'utilisateur est actif et autorisé
     * à gérer les stocks.
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

    public function view(User $user, Stock $stock): bool
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
     * Modification d'un article.
     *
     * Interdit sur un article archivé.
     */
    public function update(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $stock->statut !== StatutStock::ARCHIVE;
    }

    /*
    |--------------------------------------------------------------------------
    | CYCLE DE VIE
    |--------------------------------------------------------------------------
    */

    /**
     * Activation : uniquement sur un article INACTIF.
     */
    public function activate(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $stock->statut === StatutStock::INACTIF;
    }

    /**
     * Désactivation : uniquement sur un article ACTIF.
     */
    public function deactivate(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $stock->statut === StatutStock::ACTIF;
    }

    /**
     * Archivage.
     *
     * Conditions vérifiées ici :
     *   - utilisateur autorisé ;
     *   - article non déjà archivé ;
     *   - quantité physique = 0 ;
     *   - quantité réservée = 0.
     *
     * StockService re-vérifie de manière transactionnelle.
     */
    public function archive(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if ($stock->statut === StatutStock::ARCHIVE) {
            return false;
        }

        if ($stock->quantitePhysique() > 0) {
            return false;
        }

        if ($stock->quantiteReservee() > 0) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | OPÉRATIONS DE STOCK (DÉLÉGATION)
    |--------------------------------------------------------------------------
    |
    | Ces actions sont autorisées ici pour la vue, mais leur
    | exécution réelle est vérifiée par les services dédiés
    | (EntreeStockService, SortieStockService, etc.).
    */

    /**
     * Enregistrer une entrée pour cet article.
     */
    public function enter(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $stock->peutEtreUtilise();
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATIONS SPÉCIFIQUES
    |--------------------------------------------------------------------------
    */

    public function viewMovements(User $user, Stock $stock): bool
    {
        return $this->peutGererStocks($user);
    }

    public function viewLots(User $user, Stock $stock): bool
    {
        return $this->peutGererStocks($user);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    |
    | Suppression physique autorisée UNIQUEMENT si l'article
    | n'a AUCUN lot et AUCUN mouvement.
    |
    | En pratique, la suppression est quasi impossible :
    | tout article ayant servi est conservé pour traçabilité.
    */

    public function delete(User $user, Stock $stock): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        if ($stock->lots()->exists()) {
            return false;
        }

        if ($stock->mouvements()->exists()) {
            return false;
        }

        return true;
    }
}