/**
 * PHOVE — SCRIPT : ÉCRAN DE PRÉPARATION D'UN BON DE SORTIE
 * Vue : resources/views/admin/stocks/sorties/preparation.blade.php
 *
 * Rôle :
 *   - Confirmation avant l'exécution physique de la sortie
 *     (bouton "Effectuer la sortie")
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. La sortie physique est
 *   déclenchée uniquement après validation côté serveur.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        actionForm: '.stock-sortie-preparation__form',
        alerts:     '.stock-sortie-preparation__alert',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initSortieConfirmation();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CONFIRMATION DE LA SORTIE
       ==========================================================
       Le formulaire "Effectuer la sortie" porte :
           data-confirm="Voulez-vous réellement effectuer la sortie ?"

       Le script intercepte le submit et demande confirmation
       à l'utilisateur avant tout traitement serveur.
       ========================================================== */
    function initSortieConfirmation() {
        const form = document.querySelector(SELECTORS.actionForm);

        if (!form) {
            return;
        }

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

            /*
             * Empêche le double-clic accidentel en désactivant
             * le bouton submit après confirmation.
             */
            const button = form.querySelector('button[type="submit"]');

            if (button) {
                button.disabled = true;
                button.style.opacity = '0.7';
                button.style.cursor = 'not-allowed';

                const textSpan = button.querySelector('span');

                if (textSpan) {
                    textSpan.textContent = 'Traitement en cours...';
                }
            }
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

            /*
             * On n'auto-dismiss PAS les erreurs de validation
             * pour que l'utilisateur puisse les consulter.
             */
            if (
                alert.classList.contains(
                    'stock-sortie-preparation__alert--danger'
                ) &&
                alert.querySelector('.stock-sortie-preparation__error-list')
            ) {
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