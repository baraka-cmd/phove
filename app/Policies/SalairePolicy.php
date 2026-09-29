<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Salaire;
use App\Models\User;

class SalairePolicy
{
    /**
     * Détermine si l'utilisateur peut consulter la liste des salaires.
     *
     * Les salaires étant des données financières sensibles,
     * seuls les utilisateurs autorisés à gérer les finances
     * ou à valider les salaires peuvent y accéder.
     */
    public function viewAny(User $user): bool
    {
        return $user->estActif()
            && (
                $user->peutGererFinances()
                || $user->peutValiderSalaires()
            );
    }

    /**
     * Détermine si l'utilisateur peut consulter un salaire précis.
     */
    public function view(User $user, Salaire $salaire): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Détermine si l'utilisateur peut créer un nouveau salaire.
     *
     * La création concerne uniquement la préparation du salaire.
     * Le salaire est créé en brouillon.
     *
     * Le comptable peut préparer les salaires.
     * Le DG et le Super Admin peuvent également les préparer.
     */
    public function create(User $user): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        return $user->aRole([
            Roles::SUPER_ADMIN,
            Roles::DG,
            Roles::COMPTABLE,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut modifier un salaire.
     *
     * Un salaire déjà validé ou ayant reçu un paiement
     * ne doit plus être modifiable.
     *
     * Cette règle protège l'intégrité des données financières.
     */
    public function update(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        if (!$salaire->peutEtreModifie()) {
            return false;
        }

        return $user->aRole([
            Roles::SUPER_ADMIN,
            Roles::DG,
            Roles::COMPTABLE,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut valider un salaire.
     *
     * La validation est volontairement séparée de la création.
     *
     * Seuls :
     * - Super Admin
     * - Directeur Général
     *
     * peuvent valider un salaire.
     */
    public function validate(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        if (!$user->peutValiderSalaires()) {
            return false;
        }

        return $salaire->peutEtreValide();
    }

    /**
     * Détermine si l'utilisateur peut enregistrer un paiement
     * pour un salaire.
     *
     * Le paiement est une opération financière distincte
     * de la validation du salaire.
     *
     * Le comptable peut enregistrer les paiements.
     * Le caissier peut également enregistrer un paiement,
     * car son rôle est directement lié à l'exécution des paiements.
     *
     * Le Super Admin et le DG conservent naturellement cet accès.
     */
    public function pay(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        if (!$salaire->peutRecevoirPaiement()) {
            return false;
        }

        return $user->aRole([
            Roles::SUPER_ADMIN,
            Roles::DG,
            Roles::COMPTABLE,
            Roles::CAISSIER,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut consulter l'historique
     * des paiements d'un salaire.
     */
    public function viewPayments(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        return $this->view($user, $salaire);
    }

    /**
     * Détermine si l'utilisateur peut annuler un salaire.
     *
     * L'annulation est une opération sensible.
     *
     * Elle est réservée au Super Admin et au DG.
     *
     * Un motif obligatoire sera demandé par le formulaire
     * et vérifié également dans le Service.
     */
    public function cancel(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        if (!$salaire->peutEtreAnnule()) {
            return false;
        }

        return $user->aRole([
            Roles::SUPER_ADMIN,
            Roles::DG,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un salaire.
     *
     * Pour des raisons d'audit financier, la suppression
     * est fortement limitée.
     *
     * Un salaire ayant reçu un paiement ne doit jamais
     * être supprimé.
     *
     * Même un salaire validé ne doit pas être supprimé.
     *
     * Seul un brouillon peut éventuellement être supprimé.
     */
    public function delete(User $user, Salaire $salaire): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        if (!$salaire->estBrouillon()) {
            return false;
        }

        return $user->aRole([
            Roles::SUPER_ADMIN,
            Roles::DG,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut restaurer un salaire.
     *
     * Nous n'utilisons pas de SoftDeletes sur les salaires.
     * Cette méthode est donc volontairement désactivée.
     */
    public function restore(User $user, Salaire $salaire): bool
    {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement
     * un salaire.
     *
     * Pour les données financières, la suppression définitive
     * n'est pas autorisée par la Policy.
     *
     * L'historique doit être conservé.
     */
    public function forceDelete(User $user, Salaire $salaire): bool
    {
        return false;
    }
}