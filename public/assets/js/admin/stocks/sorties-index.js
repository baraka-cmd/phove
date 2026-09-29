/**
 * PHOVE — SCRIPT : LISTE DES BONS DE SORTIE
 * Vue : resources/views/admin/stocks/sorties/index.blade.php
 *
 * Rôle :
 *   - Confirmation avant les actions de workflow inline
 *     (soumettre un bon, préparer un bon)
 *   - Soumission automatique des filtres au changement
 *   - Gestion des alertes auto-dismiss
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. Il ne fait qu'ajouter
 *   des confirmations côté client AVANT qu'un formulaire ne soit
 *   soumis. La validation définitive reste côté serveur.
 */

(function () {
    'use strict';

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
       Chaque bouton soumettant un formulaire (soumettre, préparer)
       peut porter les attributs :
           data-confirm="true"
           data-confirm-message="Message..."
       On intercepte le submit pour demander confirmation.
       ========================================================== */
    function initWorkflowConfirmations() {
        const forms = document.querySelectorAll('.inline-action-form');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const button = form.querySelector('button[type="submit"]');

                if (!button) {
                    return;
                }

                const needsConfirm = button.dataset.confirm === 'true';

                if (!needsConfirm) {
                    return;
                }

                const message =
                    button.dataset.confirmMessage ||
                    'Confirmez-vous cette opération ?';

                if (!window.confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }


    /* ==========================================================
       2. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Les filtres principaux de la page peuvent se soumettre
       automatiquement lorsqu'un select change.

       Critère : les selects situés dans .filters-form.
       Les champs de type date et texte restent manuels.
       ========================================================== */
    function initFilterAutoSubmit() {
        const filterForm = document.querySelector('.filters-form');

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
       Les alertes de session (succès / erreur) disparaissent
       automatiquement après quelques secondes.

       On respecte l'attribut data-auto-dismiss s'il existe
       (en millisecondes). Sinon on applique un délai par défaut.
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            const customDelay = parseInt(
                alert.getAttribute('data-auto-dismiss'),
                10
            );

            const delay = !isNaN(customDelay) && customDelay > 0
                ? customDelay
                : 6000;

            setTimeout(function () {
                fadeOutAndRemove(alert);
            }, delay);
        });
    }

    /**
     * Fait disparaître un élément en douceur puis le supprime.
     */
    function fadeOutAndRemove(el) {
        if (!el || !el.parentNode) {
            return;
        }

        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';

        setTimeout(function () {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 400);
    }
})();