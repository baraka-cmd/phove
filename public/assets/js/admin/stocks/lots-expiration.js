/**
 * PHOVE — SCRIPT : SURVEILLANCE DES EXPIRATIONS DE LOTS
 * Vue : resources/views/admin/stocks/lots/expiration.blade.php
 *
 * Rôle :
 *   - Auto-submit du filtre "période"
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:          '.stock-lots-expiration-page__alert',
        filterForm:      '.stock-lots-expiration-page__filter-form',
        periodSelect:    '#periode',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initPeriodAutoSubmit();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. AUTO-SUBMIT DU FILTRE PÉRIODE
       ==========================================================
       Le <select id="periode"> se soumet automatiquement au
       changement pour un filtrage immédiat.

       Le champ recherche reste MANUEL.
       ========================================================== */
    function initPeriodAutoSubmit() {
        const select = document.querySelector(SELECTORS.periodSelect);
        const form   = document.querySelector(SELECTORS.filterForm);

        if (!select || !form) {
            return;
        }

        select.addEventListener('change', function () {
            form.submit();
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