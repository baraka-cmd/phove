/**
 * PHOVE — SCRIPT : LISTE DES ENTRÉES DE STOCK
 * Vue : resources/views/admin/stocks/entrees/index.blade.php
 *
 * Rôle :
 *   - Auto-dismiss des alertes de session
 *   - Auto-submit des filtres (selects uniquement)
 *   - Fermeture manuelle d'une alerte via son bouton
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:     '.alert',
        filtersForm: '.filters-form',
        alertClose:  '.alert-close',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initAlertsAutoDismiss();
        initFilterAutoSubmit();
    });


    /* ==========================================================
       1. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes de session disparaissent après un délai.
       Les erreurs de validation et les alertes marquées comme
       persistantes restent visibles.
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            /*
             * On n'auto-dismiss PAS :
             *   - les résumés d'erreurs de validation
             *   - les alertes marquées .alert-persistent
             */
            if (
                alert.classList.contains('validation-summary') ||
                alert.classList.contains('alert-persistent')
            ) {
                return;
            }

            alert.dataset.dismissed = 'true';

            /*
             * Fermeture manuelle par le bouton ×.
             */
            const closeBtn = alert.querySelector(SELECTORS.alertClose);

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    fadeOutAndRemove(alert);
                });
            }

            /*
             * Auto-dismiss programmé.
             */
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
       2. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Les <select> du formulaire de filtres se soumettent
       automatiquement au changement.

       Les champs texte et date restent manuels.
       ========================================================== */
    function initFilterAutoSubmit() {
        const filterForm = document.querySelector(SELECTORS.filtersForm);

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