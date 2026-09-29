/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UNE ENTRÉE DE STOCK
 * Vue : resources/views/admin/stocks/entrees/show.blade.php
 *
 * Rôle :
 *   - Auto-dismiss des alertes de session
 *
 * NOTE :
 *   Cette vue ne contient aucune action de workflow
 *   (lecture seule). Le script se contente donc d'auto-masquer
 *   les alertes de session.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts: '.stock-entree-show .alert',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. AUTO-DISMISS DES ALERTES
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
       2. UTILITAIRES INTERNES
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