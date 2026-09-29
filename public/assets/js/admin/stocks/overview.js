/**
 * PHOVE — SCRIPT : VUE D'ENSEMBLE DES STOCKS
 * Vue : resources/views/admin/stocks/overview.blade.php
 * Route : admin.stocks.overview
 *
 * Rôle :
 *   - Auto-dismiss des alertes de session (si présentes)
 *   - Aucune autre interaction (page en lecture seule)
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    /**
     * Délai par défaut d'auto-dismiss (ms).
     */
    const ALERT_DEFAULT_DELAY = 6000;

    /**
     * Sélecteurs.
     */
    const SELECTORS = {
        alerts: '.alert',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes de session disparaissent après un délai.

       On respecte l'attribut data-auto-dismiss s'il existe.
       On ignore les alertes marquées comme persistantes.
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            /*
             * On n'auto-dismiss PAS les alertes persistantes.
             */
            if (alert.classList.contains('alert-persistent')) {
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

    /**
     * Fait disparaître un élément en douceur puis le supprime.
     *
     * @param {HTMLElement} element
     */
    function fadeOutAndRemove(element) {
        if (!element || !element.parentNode) {
            return;
        }

        element.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        element.style.opacity = '0';
        element.style.transform = 'translateY(-8px)';

        setTimeout(function () {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, 400);
    }
})();