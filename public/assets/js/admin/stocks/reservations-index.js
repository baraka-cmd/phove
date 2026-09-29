/**
 * PHOVE — SCRIPT : LISTE DES RÉSERVATIONS DE STOCK
 * Vue : resources/views/admin/stocks/reservations/index.blade.php
 *
 * Rôle :
 *   - Confirmation avant les actions de workflow inline
 *     (confirmer une réservation, annuler une réservation)
 *   - Auto-submit des filtres au changement
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
        actionForms: '.stock-reservations-index__action-form',
        filtersForm: '.stock-reservations-index__filters',
        alerts:      '.stock-reservations-index__alert',
    };


    /**
     * Point d'entrée.
     */
    document.addEventListener('DOMContentLoaded', function () {
        initWorkflowConfirmations();
        initFilterAutoSubmit();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CONFIRMATIONS DE WORKFLOW
       ==========================================================
       Chaque formulaire d'action dans la liste (confirmer, annuler)
       porte les attributs :
           data-confirm="true"
           data-confirm-message="Message..."
       ou, portés par le bouton :
           data-confirm="true"
           data-confirm-message="..."

       On intercepte le submit pour demander confirmation.
       ========================================================== */
    function initWorkflowConfirmations() {
        const forms = document.querySelectorAll(SELECTORS.actionForms);

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const button = form.querySelector('button[type="submit"]');

                /*
                 * Si pas de bouton, on vérifie les attributs
                 * directement sur le formulaire.
                 */
                const trigger = button || form;

                const needsConfirm =
                    trigger.dataset.confirm === 'true' ||
                    form.dataset.confirm === 'true';

                if (!needsConfirm) {
                    return;
                }

                const message =
                    trigger.dataset.confirmMessage ||
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
       2. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Les <select> situés dans le formulaire de filtres se
       soumettent automatiquement au changement.

       Les champs <input type="search"> et <input type="date">
       restent MANUELS : l'utilisateur doit cliquer sur "Rechercher".
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
       3. AUTO-DISMISS DES ALERTES
       ==========================================================
       Les alertes (succès / erreur) disparaissent automatiquement
       après un délai.

       On respecte l'attribut data-auto-dismiss s'il existe
       (en millisecondes). Sinon on applique le délai par défaut.

       On marque l'alerte avec data-dismissed="true" pour éviter
       un double traitement.
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
       4. UTILITAIRES INTERNES
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