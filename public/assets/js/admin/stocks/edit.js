/**
 * PHOVE — SCRIPT : MODIFICATION D'UN ARTICLE DE STOCK
 * Vue : resources/views/admin/stocks/edit.blade.php
 *
 * Rôle :
 *   - Compteur de caractères live sur la description
 *   - Loader sur le bouton submit (au moment de l'enregistrement)
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 *   Il ne fait que mettre à jour l'affichage.
 */

(function () {
    'use strict';

    /**
     * Longueur maximale de la description (alignée sur le maxlength).
     */
    const DESCRIPTION_MAX_LENGTH = 5000;

    /**
     * Sélecteurs.
     */
    const SELECTORS = {
        form:                '.stock-edit-form',
        textarea:            '#description',
        counter:             '.stock-edit-character-counter',
        submitButton:        '.stock-edit-btn--primary',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initDescriptionCounter();
        initSubmitLoader();
    });


    /* ==========================================================
       1. COMPTEUR DE CARACTÈRES
       ==========================================================
       Met à jour le compteur affiché sous la description.
       Alerte visuelle si la limite est approchée.
       ========================================================== */
    function initDescriptionCounter() {
        const textarea = document.querySelector(SELECTORS.textarea);
        const counter  = document.querySelector(SELECTORS.counter);

        if (!textarea || !counter) {
            return;
        }

        const update = function () {
            const length = textarea.value.length;

            counter.textContent =
                length + ' / ' + DESCRIPTION_MAX_LENGTH;

            /*
             * Indicateur visuel :
             *   - vert  : < 90 %
             *   - orange : 90 % - 100 %
             *   - rouge : = 100 %
             */
            if (length >= DESCRIPTION_MAX_LENGTH) {
                counter.style.color = 'var(--color-error)';
                counter.style.fontWeight = '700';
            } else if (length >= DESCRIPTION_MAX_LENGTH * 0.9) {
                counter.style.color = 'var(--color-warning)';
                counter.style.fontWeight = '600';
            } else {
                counter.style.color = '';
                counter.style.fontWeight = '';
            }
        };

        textarea.addEventListener('input', update);

        /*
         * Initialisation au chargement.
         */
        update();
    }


    /* ==========================================================
       2. LOADER SUR LE BOUTON SUBMIT
       ==========================================================
       Au moment de la soumission du formulaire :
         - le bouton passe en mode "chargement"
         - le texte est remplacé
         - le bouton est désactivé
       ========================================================== */
    function initSubmitLoader() {
        const form   = document.querySelector(SELECTORS.form);
        const button = document.querySelector(SELECTORS.submitButton);

        if (!form || !button) {
            return;
        }

        const textSpan = button.querySelector(
            '.stock-edit-btn__text'
        );

        /*
         * Sauvegarde du texte original.
         */
        const originalText = textSpan
            ? textSpan.textContent.trim()
            : '';

        form.addEventListener('submit', function () {
            /*
             * Sécurité : on n'active le loader que si la validation
             * HTML5 passe.
             */
            if (!form.checkValidity()) {
                return;
            }

            button.disabled = true;
            button.style.opacity = '0.7';
            button.style.cursor = 'not-allowed';

            /*
             * Remplacement du texte par un spinner.
             */
            if (textSpan) {
                textSpan.innerHTML =
                    '<i class="fas fa-spinner fa-spin" ' +
                    'aria-hidden="true"></i> Enregistrement...';
            }
        });

        /*
         * Sécurité : si l'utilisateur revient en arrière
         * (bfcache), le bouton reste dans son état de chargement.
         * On le restaure.
         */
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                button.disabled = false;
                button.style.opacity = '';
                button.style.cursor = '';

                if (textSpan && originalText) {
                    textSpan.textContent = originalText;
                }
            }
        });
    }
})();