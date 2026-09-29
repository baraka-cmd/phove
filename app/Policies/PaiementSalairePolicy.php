<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\PaiementSalaire;
use App\Models\User;

class PaiementSalairePolicy
{
    /**
     * Détermine si l'utilisateur peut consulter la liste
     * des paiements de salaires.
     *
     * Les paiements constituent des opérations financières
     * sensibles.
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
     * Détermine si l'utilisateur peut consulter un paiement précis.
     */
    public function view(User $user, PaiementSalaire $paiement): bool
    {
        if (!$user->estActif()) {
            return false;
        }

        return $this->viewAny($user);
    }

    /**
     * Détermine si l'utilisateur peut créer/enregistrer
     * un paiement de salaire.
     *
     * IMPORTANT :
     * cette méthode vérifie uniquement le droit de l'utilisateur.
     *
     * Les règles métier supplémentaires sont contrôlées
     * dans PaiementSalaireService :
     *
     * - salaire validé ;
     * - montant supérieur à zéro ;
     * - montant <= reste à payer ;
     * - salaire non annulé ;
     * - mode de paiement valide ;
     * - transaction DB ;
     * - verrouillage pessimiste.
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
            Roles::CAISSIER,
        ]);
    }

    /**
     * Détermine si l'utilisateur peut enregistrer un paiement
     * pour un salaire donné.
     *
     * Cette méthode peut être utilisée lorsque la Policy
     * est appelée depuis une page de salaire.
     */
    public function createForSalary(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return $this->create($user);
    }

    /**
     * Détermine si l'utilisateur peut modifier un paiement.
     *
     * Par défaut, un paiement financier enregistré ne doit
     * pas être modifié directement.
     *
     * Pourquoi ?
     *
     * Modifier :
     *     500 $
     *
     * en :
     *     300 $
     *
     * après enregistrement détruirait la traçabilité
     * de l'opération originale.
     *
     * La bonne approche professionnelle est :
     *
     * paiement original
     *        ↓
     * annulation/correction contrôlée
     *        ↓
     * nouvelle opération traçable
     *
     * Nous désactivons donc update().
     */
    public function update(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut annuler un paiement.
     *
     * Cette fonctionnalité sera activée lorsque le modèle
     * et la migration PaiementSalaire posséderont les champs
     * nécessaires à l'annulation :
     *
     * - annule_par
     * - annule_at
     * - motif_annulation
     *
     * Tant que cette architecture n'est pas présente,
     * aucune annulation directe n'est autorisée.
     */
    public function cancel(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un paiement.
     *
     * JAMAIS de suppression directe d'un paiement financier
     * déjà enregistré.
     *
     * La conservation de l'historique est essentielle.
     */
    public function delete(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut restaurer un paiement.
     *
     * Pas de SoftDeletes et pas de restauration directe.
     */
    public function restore(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement
     * un paiement.
     *
     * Toujours interdit pour garantir l'intégrité
     * de l'historique financier.
     */
    public function forceDelete(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return false;
    }

    /**
     * Détermine si l'utilisateur peut consulter les paiements
     * associés à un salaire.
     */
    public function viewSalaryPayments(
        User $user,
        PaiementSalaire $paiement
    ): bool {
        return $this->view($user, $paiement);
    }
}