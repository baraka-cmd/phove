/**
 * PHOVE — SCRIPT : FICHE DÉTAILLÉE D'UN INVENTAIRE DE STOCK
 * Vue : resources/views/admin/stocks/inventaires/show.blade.php
 *
 * Rôle :
 *   - Calcul live des écarts pendant la saisie des quantités
 *   - Mise à jour du badge d'état par ligne
 *   - Confirmation avant validation / annulation
 *   - Auto-dismiss des alertes
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 *   Il NE DÉSACTIVE AUCUN bouton. Le serveur reste seul juge
 *   de la validité d'une action.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    /**
     * Sélecteurs.
     */
    const SELECTORS = {
        comptageInputs: '[data-comptage-input]',
        alerts:         '.stock-inventaire-show-alert',
        inlineForms:    '.stock-inventaire-show-inline-form',
        actionButtons:  '.stock-inventaire-show-action-button',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initComptageLive();
        initWorkflowConfirmations();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. CALCUL LIVE DES ÉCARTS
       ==========================================================
       Chaque input porte :
           data-comptage-input
           data-theorique="123.45"

       À la saisie :
           - calcul de l'écart (compté - théorique)
           - mise à jour de la cellule "Écart"
           - mise à jour du badge d'état
       ========================================================== */
    function initComptageLive() {
        const inputs = document.querySelectorAll(SELECTORS.comptageInputs);

        if (!inputs.length) {
            return;
        }

        inputs.forEach(function (input) {
            input.addEventListener('input', function () {
                updateRowFromInput(input);
            });

            input.addEventListener('change', function () {
                updateRowFromInput(input);
            });
        });
    }

    /**
     * Recalcule l'écart d'une ligne et met à jour l'affichage.
     *
     * @param {HTMLInputElement} input
     */
    function updateRowFromInput(input) {
        const row = input.closest('tr');
        if (!row) {
            return;
        }

        const theorique = parseFloat(input.dataset.theorique || '0');
        const valeur    = input.value.trim();

        const ecartEl = row.querySelector('[data-comptage-ecart]');
        const badgeEl = row.querySelector('[data-comptage-badge]');

        /*
         * Valeur vide → état "non compté".
         */
        if (valeur === '' || isNaN(parseFloat(valeur))) {
            if (ecartEl) {
                ecartEl.className = 'stock-inventaire-show-difference';
                ecartEl.innerHTML = '<span class="stock-inventaire-show-muted">—</span>';
            }

            if (badgeEl) {
                badgeEl.className = 'stock-inventaire-show-badge stock-inventaire-show-badge--neutral';
                badgeEl.innerHTML = '<i class="fas fa-clock"></i> Non compté';
            }

            return;
        }

        const comptee = parseFloat(valeur);
        const ecart   = comptee - theorique;

        /*
         * Mise à jour de la cellule "Écart".
         */
        if (ecartEl) {
            let classe = 'stock-inventaire-show-difference';

            if (ecart > 0) {
                classe += ' stock-inventaire-show-difference--positive';
            } else if (ecart < 0) {
                classe += ' stock-inventaire-show-difference--negative';
            } else {
                classe += ' stock-inventaire-show-difference--zero';
            }

            const signe = ecart > 0 ? '+' : '';

            ecartEl.className = classe;
            ecartEl.textContent = signe + ecart.toFixed(2).replace('.', ',');
        }

        /*
         * Mise à jour du badge d'état.
         */
        if (badgeEl) {
            if (ecart > 0) {
                badgeEl.className = 'stock-inventaire-show-badge stock-inventaire-show-badge--positive';
                badgeEl.innerHTML = '<i class="fas fa-arrow-up"></i> Surplus';
            } else if (ecart < 0) {
                badgeEl.className = 'stock-inventaire-show-badge stock-inventaire-show-badge--negative';
                badgeEl.innerHTML = '<i class="fas fa-arrow-down"></i> Manquant';
            } else {
                badgeEl.className = 'stock-inventaire-show-badge stock-inventaire-show-badge--neutral';
                badgeEl.innerHTML = '<i class="fas fa-check"></i> Conforme';
            }
        }
    }


    /* ==========================================================
       2. CONFIRMATIONS DE WORKFLOW
       ==========================================================
       On cible les formulaires qui :
           - portent la classe .stock-inventaire-show-inline-form
           - OU contiennent un bouton .stock-inventaire-show-action-button
           - OU ont un action contenant /valider ou /annuler

       Le script intercepte le submit et demande confirmation
       si le bouton ou le formulaire porte data-confirm-action.
       ========================================================== */
    function initWorkflowConfirmations() {
        /*
         * a) Formulaires inline (carte statut, haut de page).
         */
        bindConfirmationsToForms(
            document.querySelectorAll(SELECTORS.inlineForms)
        );

        /*
         * b) Formulaires contenant un bouton de la sidebar.
         */
        const sidebarForms = document.querySelectorAll(
            'form:has(' + SELECTORS.actionButtons + ')'
        );

        bindConfirmationsToForms(sidebarForms);

        /*
         * c) Sécurité : tous les formulaires qui pointent vers
         *    /valider ou /annuler.
         */
        const workflowForms = document.querySelectorAll(
            'form[action*="/valider"], form[action*="/annuler"]'
        );

        bindConfirmationsToForms(workflowForms);
    }

    /**
     * Attache une confirmation au submit de chaque formulaire.
     *
     * @param {NodeList} forms
     */
    function bindConfirmationsToForms(forms) {
        forms.forEach(function (form) {
            /*
             * Éviter le double binding.
             */
            if (form.dataset.confirmBound === 'true') {
                return;
            }

            form.dataset.confirmBound = 'true';

            form.addEventListener('submit', function (event) {
                const button = form.querySelector('button[type="submit"]');

                const needsConfirm =
                    (button && button.dataset.confirmAction === 'true') ||
                    (button && button.dataset.confirm === 'true') ||
                    form.dataset.confirmAction === 'true' ||
                    form.dataset.confirm === 'true';

                if (!needsConfirm) {
                    return;
                }

                const message =
                    (button && button.dataset.confirmMessage) ||
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
       3. AUTO-DISMISS DES ALERTES
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

            alert.dataset.dismissed = 'true';

            /*
             * On n'auto-dismiss PAS les erreurs de validation
             * (celles contenant une liste).
             */
            if (alert.querySelector('ul')) {
                return;
            }

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