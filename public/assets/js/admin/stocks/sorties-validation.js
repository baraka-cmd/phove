/**
 * PHOVE — SCRIPT : ÉCRAN DE VALIDATION D'UN BON DE SORTIE
 * Vue : resources/views/admin/stocks/sorties/validation.blade.php
 *
 * Rôle :
 *   - Validation client des quantités validées
 *     (bornes : 0 <= valeur <= quantité demandée)
 *   - Confirmation avant la soumission
 *   - Empêche le double-clic sur le bouton "Valider le bon"
 *   - Auto-dismiss des alertes de session
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. La validation définitive
 *   reste côté serveur.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        form:           '#bon-sortie-validation-form',
        quantityInput:  'input[name*="[quantite_validee]"]',
        submitButton:   '[data-validation-submit]',
        alerts:         '.stock-sortie-validation__alert',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initQuantityValidation();
        initSubmitProtection();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. VALIDATION DES QUANTITÉS VALIDÉES
       ==========================================================
       Chaque input de quantité validée doit respecter :
           0 <= valeur <= quantité demandée (attribut max)

       On affiche une erreur inline si la contrainte est violée.
       ========================================================== */
    function initQuantityValidation() {
        const inputs = document.querySelectorAll(SELECTORS.quantityInput);

        if (!inputs.length) {
            return;
        }

        inputs.forEach(function (input) {
            /*
             * Contrôle à la saisie.
             */
            input.addEventListener('input', function () {
                checkQuantity(input);
            });

            /*
             * Contrôle à la perte de focus.
             */
            input.addEventListener('blur', function () {
                checkQuantity(input);
            });

            /*
             * Contrôle initial (si valeur par défaut).
             */
            checkQuantity(input);
        });
    }

    /**
     * Vérifie la validité d'un input de quantité validée.
     *
     * @param {HTMLInputElement} input
     * @returns {boolean}
     */
    function checkQuantity(input) {
        const value = parseFloat(input.value);
        const max = parseFloat(input.getAttribute('max') || 'Infinity');
        const min = parseFloat(input.getAttribute('min') || '0');

        /*
         * On supprime l'erreur existante avant tout contrôle.
         */
        clearInputError(input);

        /*
         * Valeur vide → on laisse passer (le serveur validera).
         */
        if (input.value === '' || isNaN(value)) {
            return true;
        }

        /*
         * Valeur négative.
         */
        if (value < min) {
            setInputError(
                input,
                'La quantité ne peut pas être négative.'
            );
            return false;
        }

        /*
         * Valeur supérieure à la quantité demandée.
         */
        if (!isNaN(max) && value > max) {
            setInputError(
                input,
                'La quantité validée ne peut pas dépasser la quantité demandée (' + max + ').'
            );
            return false;
        }

        return true;
    }

    /**
     * Ajoute une erreur inline sous un input.
     *
     * @param {HTMLInputElement} input
     * @param {string} message
     */
    function setInputError(input, message) {
        input.classList.add('is-invalid');

        const wrapper = input.closest('td') || input.parentNode;

        if (!wrapper) {
            return;
        }

        /*
         * Éviter les doublons : on cherche une erreur existante.
         */
        let error = wrapper.querySelector('.js-quantity-error');

        if (!error) {
            error = document.createElement('small');
            error.className =
                'stock-sortie-validation__input-error js-quantity-error';
            wrapper.appendChild(error);
        }

        error.textContent = message;
    }

    /**
     * Supprime l'erreur inline associée à un input.
     *
     * @param {HTMLInputElement} input
     */
    function clearInputError(input) {
        input.classList.remove('is-invalid');

        const wrapper = input.closest('td') || input.parentNode;

        if (!wrapper) {
            return;
        }

        const error = wrapper.querySelector('.js-quantity-error');

        if (error) {
            error.remove();
        }
    }


    /* ==========================================================
       2. PROTECTION CONTRE LE DOUBLE-CLIC
       ==========================================================
       Au moment de la soumission :
         - vérification finale de tous les inputs
         - si OK : bouton désactivé + texte modifié
       ========================================================== */
    function initSubmitProtection() {
        const form = document.querySelector(SELECTORS.form);

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            /*
             * Validation finale : on parcourt tous les inputs
             * pour vérifier qu'aucun n'est invalide.
             */
            const inputs = form.querySelectorAll(SELECTORS.quantityInput);

            let allValid = true;

            inputs.forEach(function (input) {
                if (!checkQuantity(input)) {
                    allValid = false;
                }
            });

            if (!allValid) {
                event.preventDefault();

                /*
                 * Focus sur le premier input invalide pour
                 * que l'utilisateur voie où est le problème.
                 */
                const firstInvalid = form.querySelector(
                    SELECTORS.quantityInput + '.is-invalid'
                );

                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return false;
            }

            /*
             * Désactivation du bouton après validation.
             */
            const button = form.querySelector(SELECTORS.submitButton);

            if (button) {
                button.disabled = true;
                button.style.opacity = '0.7';
                button.style.cursor = 'not-allowed';

                const textSpan = button.querySelector('span');

                if (textSpan) {
                    textSpan.textContent = 'Validation en cours...';
                }
            }
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

            /*
             * On n'auto-dismiss PAS les erreurs de validation.
             */
            if (alert.querySelector('.stock-sortie-validation__error-list')) {
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