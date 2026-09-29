/**
 * PHOVE — SCRIPT : LISTE DES LOTS DE STOCK
 * Vue : resources/views/admin/stocks/lots/index.blade.php
 *
 * Rôle :
 *   - Auto-submit des filtres au changement (statut uniquement)
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:     '.stock-lots-page__alert',
        filterForm: '.stock-lots-page__filters',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initFilterAutoSubmit();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Seuls les <select> du formulaire de filtres se soumettent
       automatiquement. Les champs texte et date restent manuels.
       ========================================================== */
    function initFilterAutoSubmit() {
        const filterForm = document.querySelector(SELECTORS.filterForm);

        if (!filterForm) {
            return;
        }

        const selects = filterForm.querySelectorAll('select');

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                filterForm.submit();
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