/**
 * PHOVE — SCRIPT : CRÉATION D'UNE ENTRÉE DE STOCK
 * Vue : resources/views/admin/stocks/entrees/create.blade.php
 *
 * Rôle :
 *   - Mise à jour dynamique de l'unité affichée
 *     quand l'utilisateur sélectionne un article.
 *   - Validation client basique des dates (expiration > entrée).
 *   - Loader sur le bouton submit.
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. La validation
 *   définitive reste côté serveur.
 */

(function () {
    'use strict';

    const SELECTORS = {
        form:          '.stock-entry-form',
        stockSelect:   '#stock_id',
        unitDisplay:   '#stock-unit',
        dateEntree:    '#date_entree',
        dateFabrication: '#date_fabrication',
        dateExpiration:  '#date_expiration',
        submitButton:  '.btn-success',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initUnitDisplay();
        initDateValidation();
        initSubmitLoader();
    });


    /* ==========================================================
       1. AFFICHAGE DE L'UNITÉ
       ==========================================================
       Quand l'utilisateur sélectionne un article, on met à jour
       le badge "unité" à droite du champ "Quantité reçue".

       L'unité est lue depuis l'attribut data-unite de l'option.
       ========================================================== */
    function initUnitDisplay() {
        const select = document.querySelector(SELECTORS.stockSelect);
        const unitEl = document.querySelector(SELECTORS.unitDisplay);

        if (!select || !unitEl) {
            return;
        }

        const update = function () {
            const option = select.options[select.selectedIndex];

            if (!option || !option.value) {
                unitEl.textContent = 'unité';
                return;
            }

            const unite = option.dataset.unite || null;

            unitEl.textContent = unite || 'unité';
        };

        select.addEventListener('change', update);

        /*
         * Initialisation au chargement.
         */
        update();
    }


    /* ==========================================================
       2. VALIDATION DES DATES (côté client)
       ==========================================================
       Empêche l'utilisateur de soumettre un formulaire incohérent
       (expiration avant entrée, fabrication après entrée, etc.).

       Cette validation est UNIQUEMENT côté client.
       Le serveur revalide tout.
       ========================================================== */
    function initDateValidation() {
        const dateEntree      = document.querySelector(SELECTORS.dateEntree);
        const dateFabrication = document.querySelector(SELECTORS.dateFabrication);
        const dateExpiration  = document.querySelector(SELECTORS.dateExpiration);

        if (!dateEntree) {
            return;
        }

        /*
         * Contrôle croisé à chaque changement de date.
         */
        const check = function () {
            clearFieldError(dateFabrication);
            clearFieldError(dateExpiration);

            const entree      = dateEntree.value;
            const fabrication = dateFabrication ? dateFabrication.value : null;
            const expiration  = dateExpiration ? dateExpiration.value : null;

            /*
             * Fabrication ne doit pas être après entrée.
             */
            if (fabrication && entree && fabrication > entree) {
                setFieldError(
                    dateFabrication,
                    'La date de fabrication ne peut pas être postérieure à la date d’entrée.'
                );
            }

            /*
             * Expiration doit être après entrée.
             */
            if (expiration && entree && expiration <= entree) {
                setFieldError(
                    dateExpiration,
                    'La date d’expiration doit être postérieure à la date d’entrée.'
                );
            }

            /*
             * Expiration doit être après fabrication.
             */
            if (expiration && fabrication && expiration <= fabrication) {
                setFieldError(
                    dateExpiration,
                    'La date d’expiration doit être postérieure à la date de fabrication.'
                );
            }
        };

        dateEntree.addEventListener('change', check);

        if (dateFabrication) {
            dateFabrication.addEventListener('change', check);
        }

        if (dateExpiration) {
            dateExpiration.addEventListener('change', check);
        }
    }

    /**
     * Ajoute une erreur inline sous un champ.
     *
     * @param {HTMLInputElement} input
     * @param {string} message
     */
    function setFieldError(input, message) {
        if (!input) {
            return;
        }

        input.classList.add('is-invalid');

        /*
         * Éviter les doublons.
         */
        const parent = input.parentNode;
        const existing = parent.querySelector('.field-error.js-error');

        if (existing) {
            existing.textContent = message;
            return;
        }

        const error = document.createElement('div');
        error.className = 'field-error js-error';
        error.textContent = message;

        parent.insertAdjacentElement('afterend', error);
    }

    /**
     * Supprime l'erreur inline associée à un champ.
     *
     * @param {HTMLInputElement} input
     */
    function clearFieldError(input) {
        if (!input) {
            return;
        }

        input.classList.remove('is-invalid');

        const parent = input.parentNode;
        const next = parent.nextElementSibling;

        if (next && next.classList.contains('js-error')) {
            next.remove();
        }
    }


    /* ==========================================================
       3. LOADER SUR LE BOUTON SUBMIT
       ==========================================================
       Au moment de la soumission :
         - le bouton passe en mode chargement
         - le texte est remplacé par un spinner
         - le bouton est désactivé
       ========================================================== */
    function initSubmitLoader() {
        const form   = document.querySelector(SELECTORS.form);
        const button = document.querySelector(SELECTORS.submitButton);

        if (!form || !button) {
            return;
        }

        /*
         * Sauvegarde du contenu HTML original.
         */
        const originalHtml = button.innerHTML;

        form.addEventListener('submit', function () {
            /*
             * Si le formulaire échoue la validation HTML5,
             * on n'active pas le loader.
             */
            if (!form.checkValidity()) {
                return;
            }

            button.disabled = true;
            button.style.opacity = '0.7';
            button.style.cursor = 'not-allowed';

            button.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        });

        /*
         * Restauration au retour en arrière (bfcache).
         */
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                button.disabled = false;
                button.style.opacity = '';
                button.style.cursor = '';
                button.innerHTML = originalHtml;
            }
        });
    }
})();