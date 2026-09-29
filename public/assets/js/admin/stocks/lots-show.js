/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UN LOT DE STOCK
 * Vue : resources/views/admin/stocks/lots/show.blade.php
 *
 * Rôle :
 *   - Confirmation avant les actions "Bloquer" et "Débloquer"
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:        '.stock-lot-show-page__alert',
        actionForms:   '.stock-lot-show-page__action-form',
        actionButton:  '.stock-lot-show-page__action-button',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initWorkflowConfirmations();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CONFIRMATIONS DE WORKFLOW
       ==========================================================
       Chaque formulaire d'action (bloquer / débloquer) doit être
       confirmé avant submit.

       Le bouton porte :
           data-confirm="Voulez-vous vraiment ... ?"
       ========================================================== */
    function initWorkflowConfirmations() {
        const forms = document.querySelectorAll(SELECTORS.actionForms);

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const button = form.querySelector(
                    'button[type="submit"]'
                );

                if (!button) {
                    return;
                }

                /*
                 * Le message peut être :
                 *   - sur le bouton  (data-confirm="...")
                 *   - sur le formulaire (data-confirm="...")
                 */
                const message =
                    button.dataset.confirm ||
                    form.dataset.confirm ||
                    null;

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