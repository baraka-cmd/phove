/**
 * PHOVE — SCRIPT : CRÉATION D'UN INVENTAIRE DE STOCK
 * Vue : resources/views/admin/stocks/inventaires/create.blade.php
 *
 * Rôle :
 *   - Affichage conditionnel de la section "Sélection manuelle"
 *     en fonction du périmètre choisi (scope)
 *   - Recherche live dans la liste des articles
 *     (filtre côté client)
 *   - Compteur de caractères sur les notes
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const NOTES_MAX_LENGTH = 5000;

    const SELECTORS = {
        scopeRadios:        'input[name="scope"]',
        manualSection:      '#manual-selection-section',
        stockSearchInput:   '[data-stock-search]',
        stockLabels:        '[data-stock-label]',
        notesInput:         '#notes',
        notesCounter:       '[data-character-count="notes"]',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initScopeToggle();
        initStockSearch();
        initNotesCounter();
    });


    /* ==========================================================
       1. TOGGLE DE LA SECTION "SÉLECTION MANUELLE"
       ==========================================================
       Trois périmètres possibles (radio) :
           - all         : tous les articles
           - with_stock  : articles avec stock
           - manual      : sélection manuelle

       La section #manual-selection-section n'est visible que
       lorsque "manual" est sélectionné.
       ========================================================== */
    function initScopeToggle() {
        const radios = document.querySelectorAll(SELECTORS.scopeRadios);
        const section = document.querySelector(SELECTORS.manualSection);

        if (!radios.length || !section) {
            return;
        }

        const update = function () {
            const selected = document.querySelector(
                SELECTORS.scopeRadios + ':checked'
            );

            const value = selected ? selected.value : 'all';

            if (value === 'manual') {
                section.hidden = false;
            } else {
                section.hidden = true;
            }
        };

        radios.forEach(function (radio) {
            radio.addEventListener('change', update);
        });

        /*
         * Initialisation : on applique l'état au chargement
         * (utile après une erreur de validation où le scope
         * peut déjà être "manual").
         */
        update();
    }


    /* ==========================================================
       2. RECHERCHE LIVE DANS LA LISTE DES ARTICLES
       ==========================================================
       Filtre les articles affichés en fonction de la recherche
       (numéro, référence, désignation).

       Le filtrage est 100% côté client (aucune requête serveur).
       ========================================================== */
    function initStockSearch() {
        const input = document.querySelector(SELECTORS.stockSearchInput);
        const labels = document.querySelectorAll(SELECTORS.stockLabels);

        if (!input || !labels.length) {
            return;
        }

        const update = function () {
            const query = input.value
                .trim()
                .toLowerCase();

            labels.forEach(function (label) {
                /*
                 * Si la recherche est vide, on affiche tout.
                 */
                if (query === '') {
                    label.hidden = false;
                    return;
                }

                const text = (label.textContent || '')
                    .toLowerCase();

                if (text.indexOf(query) !== -1) {
                    label.hidden = false;
                } else {
                    label.hidden = true;
                }
            });
        };

        input.addEventListener('input', update);

        /*
         * Initialisation.
         */
        update();
    }


    /* ==========================================================
       3. COMPTEUR DE CARACTÈRES SUR LES NOTES
       ========================================================== */
    function initNotesCounter() {
        const input   = document.querySelector(SELECTORS.notesInput);
        const counter = document.querySelector(SELECTORS.notesCounter);

        if (!input || !counter) {
            return;
        }

        const update = function () {
            const length = input.value.length;

            counter.textContent = length;

            if (length >= NOTES_MAX_LENGTH) {
                counter.style.color = 'var(--color-error)';
            } else if (length >= NOTES_MAX_LENGTH * 0.9) {
                counter.style.color = 'var(--color-warning)';
            } else {
                counter.style.color = '';
            }
        };

        input.addEventListener('input', update);

        /*
         * Initialisation.
         */
        update();
    }
})();