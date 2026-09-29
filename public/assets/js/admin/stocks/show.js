/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UN ARTICLE DE STOCK
 * Vue : resources/views/admin/stocks/show.blade.php
 *
 * Rôle :
 *   - Auto-dismiss des alertes de session
 *   - Animation de la barre de progression (facultatif)
 *   - Confirmation sur le formulaire d'archivage
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    /**
     * Sélecteurs.
     */
    const SELECTORS = {
        alerts:           '.alert',
        archiveForm:      '.archive-form',
        progressBar:      '.progress-fill',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initAlertsAutoDismiss();
        initProgressAnimation();
        initArchiveConfirmation();
    });


    /* ==========================================================
       1. AUTO-DISMISS DES ALERTES
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
             * On n'auto-dismiss PAS :
             *   - les résumés de validation
             */
            if (alert.classList.contains('validation-summary')) {
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
       2. ANIMATION DE LA BARRE DE PROGRESSION
       ==========================================================
       La barre de progression (niveau de stock disponible) est
       animée au chargement de la page.

       Le Blade fournit la largeur via style="width: X%".
       On l'anime de 0 à X pour un effet visuel.
       ========================================================== */
    function initProgressAnimation() {
        const bars = document.querySelectorAll(SELECTORS.progressBar);

        if (!bars.length) {
            return;
        }

        bars.forEach(function (bar) {
            const target = bar.style.width;

            if (!target) {
                return;
            }

            /*
             * On part de 0 puis on anime vers la valeur cible.
             */
            bar.style.transition = 'width 0.8s ease';
            bar.style.width = '0';

            /*
             * On force un reflow avant de réappliquer la valeur.
             */
            void bar.offsetWidth;

            requestAnimationFrame(function () {
                bar.style.width = target;
            });
        });
    }


    /* ==========================================================
       3. CONFIRMATION SUR L'ARCHIVAGE
       ==========================================================
       Le formulaire d'archivage est confirmé avant submit.
       ========================================================== */
    function initArchiveConfirmation() {
        const forms = document.querySelectorAll(SELECTORS.archiveForm);

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const message =
                    'Voulez-vous vraiment archiver cet article ? ' +
                    'Cette action le rendra inactif sans le supprimer.';

                if (!window.confirm(message)) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    }


    /* ==========================================================
       4. UTILITAIRES INTERNES
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