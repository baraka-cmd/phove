/**
 * PHOVE — SCRIPT : LISTE DES INVENTAIRES DE STOCK
 * Vue : resources/views/admin/stocks/inventaires/index.blade.php
 *
 * Rôle :
 *   - Auto-dismiss des alertes de session
 *   - Auto-submit des filtres au changement
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 *
 * NOTE :
 *   La validation d'un inventaire se fait depuis la fiche
 *   détaillée (`inventaires/show.blade.php`), pas depuis la liste.
 *   Aucune confirmation de workflow n'est requise ici.
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
        alerts:     '.stock-inventaires-alert',
        filterForm: '.stock-inventaires-filter-form',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initAlertsAutoDismiss();
        initFilterAutoSubmit();
    });


    /* ==========================================================
       1. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes (succès, erreur) disparaissent automatiquement
       après un délai.

       L'attribut data-auto-dismiss est respecté s'il est présent
       sur l'alerte. Le bouton de fermeture est aussi géré.
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            alert.dataset.dismissed = 'true';

            /*
             * Bouton de fermeture manuelle.
             */
            const closeBtn = alert.querySelector(
                '.stock-inventaires-alert__close'
            );

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    fadeOutAndRemove(alert);
                });
            }

            /*
             * Auto-dismiss.
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

       Les <input type="search"> et <input type="date"> restent
       MANUELS (l'utilisateur clique sur "Rechercher").
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
       3. UTILITAIRES INTERNES
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