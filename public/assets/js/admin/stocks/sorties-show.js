/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UN BON DE SORTIE
 * Vue : resources/views/admin/stocks/sorties/show.blade.php
 *
 * Rôle :
 *   - Confirmation avant les actions de workflow
 *     (soumettre, valider, préparer, annuler)
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        actionForms: '.action-form',
        alerts:      '.alert',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initWorkflowConfirmations();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CONFIRMATIONS DE WORKFLOW
       ==========================================================
       Chaque formulaire d'action porte :
           data-confirm="Message de confirmation"
       Le script intercepte le submit et demande confirmation.
       ========================================================== */
    function initWorkflowConfirmations() {
        const forms = document.querySelectorAll(SELECTORS.actionForms);

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const message = form.dataset.confirm;

                /*
                 * Pas de message → pas de confirmation.
                 */
                if (!message) {
                    return;
                }

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
       Les alertes de session disparaissent après un délai.
       Les erreurs de validation restent visibles.
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            if (alert.classList.contains('validation-summary')) {
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