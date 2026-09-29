/**
 * PHOVE — SCRIPT : CRÉATION D'UN ARTICLE DE STOCK
 * Vue : resources/views/admin/stocks/create.blade.php
 *
 * Rôle :
 *   - Compteur de caractères live sur la description
 *   - Récapitulatif live (mise à jour des valeurs au fur et à mesure)
 *   - Loader sur le bouton submit
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée. Il ne fait que
 *   mettre à jour l'affichage.
 */

(function () {
    'use strict';

    /**
     * Longueur maximale de la description.
     */
    const DESCRIPTION_MAX_LENGTH = 5000;


    document.addEventListener('DOMContentLoaded', function () {
        initDescriptionCounter();
        initSummaryUpdates();
        initSubmitLoader();
    });


    /* ==========================================================
       1. COMPTEUR DE CARACTÈRES SUR LA DESCRIPTION
       ========================================================== */
    function initDescriptionCounter() {
        const textarea = document.querySelector(
            '[data-stock-description]'
        );

        const counter = document.querySelector(
            '[data-stock-description-counter]'
        );

        if (!textarea || !counter) {
            return;
        }

        const update = function () {
            const length = textarea.value.length;

            counter.textContent =
                length + ' caractère' +
                (length > 1 ? 's' : '');

            /*
             * Alerte visuelle si la limite est approchée.
             */
            if (length >= DESCRIPTION_MAX_LENGTH) {
                counter.style.color = 'var(--color-error)';
            } else if (length >= DESCRIPTION_MAX_LENGTH * 0.9) {
                counter.style.color = 'var(--color-warning)';
            } else {
                counter.style.color = '';
            }
        };

        textarea.addEventListener('input', update);

        /*
         * Initialisation au chargement.
         */
        update();
    }


    /* ==========================================================
       2. RÉCAPITULATIF LIVE
       ==========================================================
       Met à jour les données affichées dans la carte
       "Vérification" en fonction des champs du formulaire.
       ========================================================== */
    function initSummaryUpdates() {
        bindSummary(
            '#reference',
            '[data-summary-reference]'
        );

        bindSummary(
            '#designation',
            '[data-summary-designation]'
        );

        bindSummary(
            '#categorie',
            '[data-summary-category]',
            formatCategory
        );

        bindSummary(
            '#unite',
            '[data-summary-unit]'
        );

        bindSummary(
            '#seuil_alerte',
            '[data-summary-threshold]',
            formatThreshold
        );

        bindSummary(
            '#emplacement_principal_id',
            '[data-summary-location]',
            formatLocation
        );
    }

    /**
     * Lie un champ à un emplacement du résumé.
     *
     * @param {string} inputSelector
     * @param {string} summarySelector
     * @param {Function} [formatter]
     */
    function bindSummary(inputSelector, summarySelector, formatter) {
        const input = document.querySelector(inputSelector);
        const target = document.querySelector(summarySelector);

        if (!input || !target) {
            return;
        }

        const update = function () {
            let value = input.value;

            if (formatter) {
                value = formatter(value, input);
            }

            target.textContent = value && String(value).trim() !== ''
                ? value
                : '—';
        };

        input.addEventListener('input', update);
        input.addEventListener('change', update);

        /*
         * Initialisation au chargement.
         */
        update();
    }

    /**
     * Formate la catégorie en libellé lisible.
     *
     * @param {string} value
     * @returns {string}
     */
    function formatCategory(value) {
        if (!value) {
            return '';
        }

        const labels = {
            vivres:      'Vivres',
            medicaments: 'Médicaments',
            habillement: 'Habillement',
            abris:       'Abris',
            materiel:    'Matériel',
            semences:    'Semences',
            outils:      'Outils',
            autre:       'Autre',
        };

        return labels[value] || value;
    }

    /**
     * Formate le seuil d'alerte.
     *
     * @param {string} value
     * @param {HTMLInputElement} input
     * @returns {string}
     */
    function formatThreshold(value, input) {
        if (!value || value === '') {
            return '';
        }

        const unit = document.querySelector('#unite');
        const unitText = unit && unit.value ? ' ' + unit.value : '';

        return value + unitText;
    }

    /**
     * Formate le libellé de l'emplacement.
     *
     * @param {string} value
     * @param {HTMLSelectElement} select
     * @returns {string}
     */
    function formatLocation(value, select) {
        if (!value) {
            return '';
        }

        const option = select.querySelector(
            'option[value="' + value + '"]'
        );

        return option ? option.textContent.trim() : '';
    }


    /* ==========================================================
       3. LOADER SUR LE BOUTON SUBMIT
       ==========================================================
       Au moment de la soumission du formulaire :
         - le bouton passe en mode "chargement"
         - l'icône + le texte sont masqués
         - le loader est affiché
         - le bouton est désactivé
       ========================================================== */
    function initSubmitLoader() {
        const form = document.querySelector('[data-stock-form]');
        const button = document.querySelector('[data-stock-submit]');

        if (!form || !button) {
            return;
        }

        const icon = button.querySelector('[data-submit-icon]');
        const text = button.querySelector('[data-submit-text]');
        const loader = button.querySelector('[data-submit-loader]');

        form.addEventListener('submit', function () {
            /*
             * Sécurité : on n'active le loader que si le formulaire
             * passe la validation HTML5.
             */
            if (!form.checkValidity()) {
                return;
            }

            button.disabled = true;

            if (icon) {
                icon.hidden = true;
            }

            if (text) {
                text.hidden = true;
            }

            if (loader) {
                loader.hidden = false;
            }
        });
    }
})();