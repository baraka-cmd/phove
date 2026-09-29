<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\ReservationStock;
use App\Models\User;

/**
 * Policy des réservations de stock.
 *
 * SÉMANTIQUE :
 *
 *   Cette policy autorise les actions du WORKFLOW d'une
 *   réservation. Elle s'appuie sur les méthodes du modèle
 *   `ReservationStock::peutEtreX()`.
 *
 *   Les contrôles de disponibilité et de verrouillage
 *   restent dans ReservationStockService.
 *
 * ACCÈS DE BASE :
 *
 *   Toute action exige un utilisateur ACTIF appartenant
 *   à `Roles::gestionStocks()`.
 */
class ReservationStockPolicy
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

    public function view(User $user, ReservationStock $reservation): bool
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
     * Modification : uniquement en brouillon.
     */
    public function update(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->peutEtreModifiee();
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW
    |--------------------------------------------------------------------------
    */

    /**
     * Confirmer : uniquement en brouillon.
     */
    public function confirm(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->peutEtreConfirmee();
    }

    /**
     * Consommer : uniquement si la réservation est active.
     *
     * Le contrôle de quantité disponible et de disponibilité
     * réelle reste dans ReservationStockService.
     */
    public function consume(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->peutEtreConsommee();
    }

    /**
     * Annuler : possible tant que la réservation n'est pas terminale.
     */
    public function cancel(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->peutEtreAnnulee();
    }

    /**
     * Expirer : uniquement si la date est dépassée et que
     * la réservation est active.
     */
    public function expire(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->peutEtreExpiree()
            && $reservation->estExpireeSelonDate();
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    */

    /**
     * Suppression : uniquement un brouillon.
     *
     * En pratique, on annule plutôt que de supprimer, pour
     * conserver la traçabilité documentaire.
     */
    public function delete(User $user, ReservationStock $reservation): bool
    {
        if (!$this->peutGererStocks($user)) {
            return false;
        }

        return $reservation->estBrouillon();
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTATIONS SPÉCIFIQUES
    |--------------------------------------------------------------------------
    */

    public function viewLines(User $user, ReservationStock $reservation): bool
    {
        return $this->peutGererStocks($user);
    }
}