/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UNE RÉSERVATION DE STOCK
 * Vue : resources/views/admin/stocks/reservations/show.blade.php
 *
 * Rôle :
 *   - Confirmation avant les actions de workflow
 *     (confirmer la réservation, annuler la réservation)
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. Il ne fait qu'ajouter
 *   des confirmations côté client AVANT qu'un formulaire ne soit
 *   soumis. La validation définitive reste côté serveur.
 */

(function () {
    'use strict';

    /**
     * Délai par défaut d'auto-dismiss d'une alerte (ms).
     */
    const ALERT_DEFAULT_DELAY = 6000;

    /**
     * Sélecteurs utilisés dans ce script.
     */
    const SELECTORS = {
        workflowForms: '.stock-reservation-show__action-form',
        alerts:        '.stock-reservation-show__alert',
    };


    /**
     * Point d'entrée.
     */
    document.addEventListener('DOMContentLoaded', function () {
        initWorkflowConfirmations();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CONFIRMATIONS DE WORKFLOW
       ==========================================================
       Chaque formulaire de workflow (confirmer, annuler) peut porter :
         - sur son bouton :
             data-confirm-action="true"
             data-confirm-message="..."
         - ou directement sur le formulaire :
             data-confirm="true"
             data-confirm-message="..."

       Le script intercepte le submit et demande confirmation.
       ========================================================== */
    function initWorkflowConfirmations() {
        const forms = document.querySelectorAll(SELECTORS.workflowForms);

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const button = form.querySelector('button[type="submit"]');

                /*
                 * Détection des attributs de confirmation :
                 * on regarde d'abord le bouton, puis le formulaire.
                 */
                const buttonHasConfirm =
                    button &&
                    button.dataset.confirmAction === 'true';

                const formHasConfirm =
                    form.dataset.confirm === 'true' ||
                    form.dataset.confirmAction === 'true';

                const needsConfirm =
                    buttonHasConfirm ||
                    formHasConfirm;

                if (!needsConfirm) {
                    return;
                }

                /*
                 * Récupération du message personnalisé.
                 */
                const message =
                    (button && button.dataset.confirmMessage) ||
                    form.dataset.confirmMessage ||
                    'Confirmez-vous cette opération ?';

                if (!window.confirm(message)) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    }


    /* ==========================================================
       2. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes (succès, erreur, warning) disparaissent
       automatiquement après un délai.

       - Si data-auto-dismiss est présent : on respecte.
       - Sinon : délai par défaut 6 secondes.

       On marque l'alerte avec data-dismissed="true" pour éviter
       un double traitement (si un autre script tente aussi).
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            alert.dataset.dismissed = 'true';

            const customDelay = parseInt(
                alert.getAttribute('data-auto-dismiss'),
                10
            );

            const delay = !isNaN(customDelay) && customDelay > 0
                ? customDelay
                : ALERT_DEFAULT_DELAY;

            setTimeout(function () {
                fadeOutAndRemove(alert);
            }, delay);
        });
    }


    /* ==========================================================
       3. UTILITAIRES INTERNES
       ========================================================== */

    /**
     * Fait disparaître un élément en douceur puis le supprime
     * du DOM.
     *
     * @param {HTMLElement} element
     */
    function fadeOutAndRemove(element) {
        if (!element || !element.parentNode) {
            return;
        }

        element.style.transition =
            'opacity 0.4s ease, transform 0.4s ease';
        element.style.opacity = '0';
        element.style.transform = 'translateY(-8px)';

        setTimeout(function () {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, 400);
    }
})();