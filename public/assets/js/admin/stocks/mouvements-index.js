/**
 * PHOVE — SCRIPT : LISTE DES MOUVEMENTS DE STOCK
 * Vue : resources/views/admin/stocks/mouvements/index.blade.php
 *
 * Rôle :
 *   - Auto-submit des filtres (selects uniquement)
 *   - Auto-dismiss des alertes de session
 *   - Débounce sur la recherche pour éviter les requêtes
 *     à chaque frappe (optionnel)
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:      '.stock-mouvements-alert',
        filterForm:  '#stock-mouvements-filter-form',
        selectFilters: 'select',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initFilterAutoSubmit();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Seuls les <select> du formulaire de filtres se soumettent
       automatiquement au changement.

       Les champs recherche / date restent MANUELS
       (l'utilisateur clique sur "Filtrer").
       ========================================================== */
    function initFilterAutoSubmit() {
        const form = document.querySelector(SELECTORS.filterForm);

        if (!form) {
            return;
        }

        const selects = form.querySelectorAll(SELECTORS.selectFilters);

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                form.submit();
            });
        });
    }


    /* ==========================================================
       2. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes de session disparaissent après un délai.

       L'attribut data-auto-dismiss est respecté s'il est présent.
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