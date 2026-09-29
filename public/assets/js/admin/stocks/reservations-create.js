/**
 * PHOVE — SCRIPT : CRÉATION D'UNE RÉSERVATION DE STOCK
 * Vue : resources/views/admin/stocks/reservations/create.blade.php
 *
 * Rôle :
 *   - Gestion dynamique des lignes d'articles
 *     (ajout via template, suppression, renumérotation)
 *   - Mise à jour de l'unité et de la disponibilité
 *     lorsqu'un article est sélectionné
 *   - Recalcul du total (nombre d'articles + quantité totale)
 *   - Compteur de caractères sur les notes
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const NOTES_MAX_LENGTH = 5000;

    const SELECTORS = {
        form:            '#reservation-stock-form',
        linesContainer:  '#reservation-lines',
        lineTemplate:    '#reservation-line-template',
        addLineBtn:      '#add-reservation-line',
        totalLines:      '[data-lines-count]',
        totalRequested:  '[data-total-requested]',
        notesInput:      '#notes',
        notesCounter:    '[data-counter-for="notes"]',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initLinesManagement();
        initNotesCounter();
        initQuantityTotals();
    });


    /* ==========================================================
       1. GESTION DES LIGNES D'ARTICLES
       ==========================================================
       Le Blade fournit un <template id="reservation-line-template">
       contenant une ligne vierge avec des marqueurs :
           __INDEX__  → index numérique
           __NUMBER__ → numéro affiché (index + 1)

       On clone ce template à chaque ajout de ligne.
       ========================================================== */
    function initLinesManagement() {
        const container = document.querySelector(SELECTORS.linesContainer);
        const template  = document.querySelector(SELECTORS.lineTemplate);
        const addBtn    = document.querySelector(SELECTORS.addLineBtn);

        if (!container || !template) {
            return;
        }

        /*
         * Bouton "Ajouter un article".
         */
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                addLine(container, template);
            });
        }

        /*
         * Délégation : suppression de ligne.
         */
        container.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('[data-remove-line]');

            if (!removeBtn) {
                return;
            }

            const line = removeBtn.closest('[data-reservation-line]');

            if (!line) {
                return;
            }

            const lines = container.querySelectorAll(
                '[data-reservation-line]'
            );

            /*
             * On empêche la suppression de la dernière ligne.
             */
            if (lines.length <= 1) {
                return;
            }

            line.remove();

            reindexLines(container);
            updateLinesSummary(container);
        });

        /*
         * Délégation : mise à jour unité + disponibilité
         * au changement d'article.
         */
        container.addEventListener('change', function (event) {
            const select = event.target.closest('[data-stock-select]');

            if (!select) {
                return;
            }

            updateLineAvailability(select);
        });

        /*
         * Délégation : recalcul des totaux.
         */
        container.addEventListener('input', function (event) {
            const input = event.target.closest('[data-quantity-requested]');

            if (!input) {
                return;
            }

            updateLinesSummary(container);
        });

        /*
         * Initialisation.
         */
        updateLinesSummary(container);
    }

    /**
     * Ajoute une nouvelle ligne vierge dans le conteneur.
     *
     * @param {HTMLElement} container
     * @param {HTMLTemplateElement} template
     */
    function addLine(container, template) {
        const lines = container.querySelectorAll(
            '[data-reservation-line]'
        );

        const index = lines.length;

        /*
         * Génération du HTML à partir du template.
         */
        const html = template.innerHTML
            .replace(/__INDEX__/g, index)
            .replace(/__NUMBER__/g, index + 1);

        /*
         * Insertion dans le conteneur.
         */
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();

        const newLine = wrapper.firstElementChild;

        if (!newLine) {
            return;
        }

        container.appendChild(newLine);

        reindexLines(container);
        updateLinesSummary(container);

        /*
         * Focus sur le premier champ de la nouvelle ligne.
         */
        const newSelect = newLine.querySelector('[data-stock-select]');

        if (newSelect) {
            newSelect.focus();
        }
    }

    /**
     * Recalcule les index des lignes.
     *
     * @param {HTMLElement} container
     */
    function reindexLines(container) {
        const lines = container.querySelectorAll(
            '[data-reservation-line]'
        );

        lines.forEach(function (line, index) {
            /*
             * Numéro affiché.
             */
            const numberSpan = line.querySelector(
                '.stock-reservation-create__line-number'
            );

            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }

            /*
             * Met à jour les attributs name/id des champs.
             */
            const fields = line.querySelectorAll(
                '[name^="lignes["], [id^="lignes_"]'
            );

            fields.forEach(function (field) {
                if (field.name) {
                    field.name = field.name.replace(
                        /lignes\[\d+\]/,
                        'lignes[' + index + ']'
                    );
                }

                if (field.id) {
                    field.id = field.id.replace(
                        /lignes_\d+_/,
                        'lignes_' + index + '_'
                    );
                }
            });

            /*
             * Met à jour les labels for=.
             */
            const labels = line.querySelectorAll('label[for^="lignes_"]');

            labels.forEach(function (label) {
                label.htmlFor = label.htmlFor.replace(
                    /lignes_\d+_/,
                    'lignes_' + index + '_'
                );
            });

            /*
             * Désactive le bouton supprimer si une seule ligne.
             */
            const removeBtn = line.querySelector('[data-remove-line]');

            if (removeBtn) {
                removeBtn.disabled = lines.length <= 1;
            }
        });
    }

    /**
     * Met à jour l'unité et la disponibilité d'une ligne.
     *
     * @param {HTMLSelectElement} select
     */
    function updateLineAvailability(select) {
        const line = select.closest('[data-reservation-line]');

        if (!line) {
            return;
        }

        const option = select.options[select.selectedIndex];

        const unitInput = line.querySelector('[data-unit-input]');
        const availableEl = line.querySelector('[data-available-quantity]');

        if (!option || !option.value) {
            if (availableEl) {
                availableEl.textContent = '—';
            }

            return;
        }

        const unite = option.dataset.unite || '';
        const disponible = parseFloat(
            option.dataset.quantiteDisponible || '0'
        );

        /*
         * Mise à jour automatique de l'unité.
         */
        if (unitInput && unite) {
            unitInput.value = unite;
        }

        /*
         * Mise à jour de la disponibilité.
         */
        if (availableEl) {
            availableEl.textContent =
                formatNumber(disponible) +
                (unite ? ' ' + unite : '');
        }
    }

    /**
     * Recalcule le nombre d'articles et la quantité totale demandée.
     *
     * @param {HTMLElement} container
     */
    function updateLinesSummary(container) {
        const lines = container.querySelectorAll(
            '[data-reservation-line]'
        );

        const totalLines = lines.length;

        let totalQuantity = 0;

        lines.forEach(function (line) {
            const input = line.querySelector('[data-quantity-requested]');

            if (input) {
                const value = parseFloat(input.value);

                if (!isNaN(value)) {
                    totalQuantity += value;
                }
            }
        });

        const totalLinesEl = document.querySelector(SELECTORS.totalLines);

        if (totalLinesEl) {
            totalLinesEl.textContent = totalLines;
        }

        const totalRequestedEl = document.querySelector(
            SELECTORS.totalRequested
        );

        if (totalRequestedEl) {
            totalRequestedEl.textContent = formatNumber(totalQuantity);
        }
    }


    /* ==========================================================
       2. COMPTEUR DE CARACTÈRES SUR LES NOTES
       ========================================================== */
    function initNotesCounter() {
        const input   = document.querySelector(SELECTORS.notesInput);
        const counter = document.querySelector(SELECTORS.notesCounter);

        if (!input || !counter) {
            return;
        }

        const update = function () {
            const length = input.value.length;

            counter.textContent = length + ' / ' + NOTES_MAX_LENGTH;

            applyCounterColor(counter, length, NOTES_MAX_LENGTH);
        };

        input.addEventListener('input', update);
        update();
    }


    /* ==========================================================
       3. UTILITAIRES
       ========================================================== */

    function applyCounterColor(counter, length, max) {
        if (length >= max) {
            counter.style.color = 'var(--color-error)';
        } else if (length >= max * 0.9) {
            counter.style.color = 'var(--color-warning)';
        } else {
            counter.style.color = '';
        }
    }

    function formatNumber(value) {
        return Number(value || 0).toFixed(2).replace('.', ',');
    }

    function initQuantityTotals() {
        const container = document.querySelector(SELECTORS.linesContainer);

        if (container) {
            updateLinesSummary(container);
        }
    }
})();