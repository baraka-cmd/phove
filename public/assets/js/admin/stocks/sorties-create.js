/**
 * PHOVE — SCRIPT : CRÉATION D'UN BON DE SORTIE
 * Vue : resources/views/admin/stocks/sorties/create.blade.php
 *
 * Rôle :
 *   - Gestion dynamique des lignes d'articles
 *     (ajout, suppression, renumérotation)
 *   - Mise à jour de l'unité et de la disponibilité
 *     lorsqu'un article est sélectionné
 *   - Recalcul du total (nombre d'articles + quantité totale)
 *   - Compteur de caractères sur le motif et les notes
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const MOTIF_MAX_LENGTH = 500;
    const NOTES_MAX_LENGTH = 5000;

    const SELECTORS = {
        form:               '#bon-sortie-form',
        linesContainer:     '#stock-lines',
        lineTemplate:       null, // pas de template côté Blade, on clone
        addLineBtn:         '#add-line-btn',
        emptyAddLineBtn:    '#empty-add-line-btn',
        noLinesMessage:     '#no-stock-lines',
        totalLines:         '#total-lines',
        totalQuantity:      '#total-quantity',
        motifInput:         '#motif',
        motifCounter:       '#motif-counter',
        notesInput:         '#notes',
        notesCounter:       '#notes-counter',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initLinesManagement();
        initMotifCounter();
        initNotesCounter();
        initQuantityTotals();
    });


    /* ==========================================================
       1. GESTION DES LIGNES D'ARTICLES
       ==========================================================
       - Ajouter une ligne
       - Supprimer une ligne
       - Recalculer les indices
       - Afficher/masquer le message "aucune ligne"
       ========================================================== */
    function initLinesManagement() {
        const container = document.querySelector(SELECTORS.linesContainer);
        const addBtn    = document.querySelector(SELECTORS.addLineBtn);
        const emptyBtn  = document.querySelector(SELECTORS.emptyAddLineBtn);

        if (!container) {
            return;
        }

        /*
         * Bouton "Ajouter un article".
         */
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                addLine(container);
            });
        }

        /*
         * Bouton depuis l'état vide.
         */
        if (emptyBtn) {
            emptyBtn.addEventListener('click', function () {
                addLine(container);
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

            const line = removeBtn.closest('.stock-line');

            if (!line) {
                return;
            }

            const lines = container.querySelectorAll('.stock-line');

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
            const input = event.target.closest('[data-quantity-input]');

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
     */
    function addLine(container) {
        const lines = container.querySelectorAll('.stock-line');

        if (lines.length === 0) {
            return;
        }

        /*
         * Clone la première ligne (qui existe toujours car
         * on empêche la suppression de la dernière).
         */
        const template = lines[0].cloneNode(true);

        /*
         * Réinitialise les champs.
         */
        const select = template.querySelector('[data-stock-select]');
        const quantity = template.querySelector('[data-quantity-input]');
        const notes = template.querySelector('input[type="text"]');
        const unitDisplay = template.querySelector('[data-unit-display]');
        const availability = template.querySelector('[data-available-display]');

        if (select) {
            select.value = '';
        }

        if (quantity) {
            quantity.value = '';
        }

        if (notes) {
            notes.value = '';
        }

        if (unitDisplay) {
            unitDisplay.textContent = 'unité';
        }

        if (availability) {
            availability.textContent = 'Disponibilité : —';
            availability.classList.remove('is-low', 'is-zero');
        }

        container.appendChild(template);

        reindexLines(container);
        updateLinesSummary(container);

        /*
         * Focus sur le premier champ de la nouvelle ligne.
         */
        const newSelect = template.querySelector('[data-stock-select]');

        if (newSelect) {
            newSelect.focus();
        }
    }

    /**
     * Recalcule les index des lignes (noms des champs,
     * ids, numéros affichés).
     *
     * @param {HTMLElement} container
     */
    function reindexLines(container) {
        const lines = container.querySelectorAll('.stock-line');

        lines.forEach(function (line, index) {
            /*
             * Met à jour le numéro affiché.
             */
            const numberSpan = line.querySelector('.line-number');

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
             * Met à jour data-line-index.
             */
            line.dataset.lineIndex = index;

            /*
             * Désactive le bouton "supprimer" si une seule ligne.
             */
            const removeBtn = line.querySelector('[data-remove-line]');

            if (removeBtn) {
                removeBtn.disabled = lines.length <= 1;
            }
        });
    }

    /**
     * Met à jour l'unité et la disponibilité d'une ligne
     * lorsqu'un article est sélectionné.
     *
     * @param {HTMLSelectElement} select
     */
    function updateLineAvailability(select) {
        const line = select.closest('.stock-line');

        if (!line) {
            return;
        }

        const option = select.options[select.selectedIndex];

        const unitDisplay = line.querySelector('[data-unit-display]');
        const availability = line.querySelector('[data-available-display]');

        if (!option || !option.value) {
            if (unitDisplay) {
                unitDisplay.textContent = 'unité';
            }

            if (availability) {
                availability.textContent = 'Disponibilité : —';
                availability.classList.remove('is-low', 'is-zero');
            }

            return;
        }

        const unite = option.dataset.unite || 'unité';
        const disponible = parseFloat(option.dataset.disponible || '0');

        if (unitDisplay) {
            unitDisplay.textContent = unite;
        }

        if (availability) {
            availability.textContent =
                'Disponibilité : ' +
                formatNumber(disponible) +
                ' ' +
                unite;

            availability.classList.remove('is-low', 'is-zero');

            if (disponible <= 0) {
                availability.classList.add('is-zero');
            } else if (disponible <= 10) {
                availability.classList.add('is-low');
            }
        }
    }

    /**
     * Recalcule le nombre d'articles et la quantité totale.
     *
     * @param {HTMLElement} container
     */
    function updateLinesSummary(container) {
        const lines = container.querySelectorAll('.stock-line');

        const totalLines = lines.length;

        let totalQuantity = 0;

        lines.forEach(function (line) {
            const input = line.querySelector('[data-quantity-input]');

            if (input) {
                const value = parseFloat(input.value);

                if (!isNaN(value)) {
                    totalQuantity += value;
                }
            }
        });

        /*
         * Mise à jour du compteur d'articles.
         */
        const totalLinesEl = document.querySelector(SELECTORS.totalLines);

        if (totalLinesEl) {
            totalLinesEl.textContent = totalLines;
        }

        /*
         * Mise à jour du total des quantités.
         */
        const totalQuantityEl = document.querySelector(SELECTORS.totalQuantity);

        if (totalQuantityEl) {
            totalQuantityEl.textContent = formatNumber(totalQuantity);
        }

        /*
         * Affichage/masquage du message "aucune ligne".
         */
        const noLinesMsg = document.querySelector(SELECTORS.noLinesMessage);

        if (noLinesMsg) {
            noLinesMsg.hidden = totalLines > 0;
        }
    }


    /* ==========================================================
       2. COMPTEUR DE CARACTÈRES SUR LE MOTIF
       ========================================================== */
    function initMotifCounter() {
        const input   = document.querySelector(SELECTORS.motifInput);
        const counter = document.querySelector(SELECTORS.motifCounter);

        if (!input || !counter) {
            return;
        }

        const update = function () {
            const length = input.value.length;

            counter.textContent = length + ' / ' + MOTIF_MAX_LENGTH;

            applyCounterColor(counter, length, MOTIF_MAX_LENGTH);
        };

        input.addEventListener('input', update);
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

            counter.textContent = length + ' / ' + NOTES_MAX_LENGTH;

            applyCounterColor(counter, length, NOTES_MAX_LENGTH);
        };

        input.addEventListener('input', update);
        update();
    }


    /* ==========================================================
       4. UTILITAIRES
       ========================================================== */

    /**
     * Applique une couleur au compteur selon la longueur.
     */
    function applyCounterColor(counter, length, max) {
        if (length >= max) {
            counter.style.color = 'var(--color-error)';
        } else if (length >= max * 0.9) {
            counter.style.color = 'var(--color-warning)';
        } else {
            counter.style.color = '';
        }
    }

    /**
     * Formate un nombre avec virgule et espaces.
     *
     * @param {number} value
     * @returns {string}
     */
    function formatNumber(value) {
        return Number(value || 0).toFixed(2).replace('.', ',');
    }

    /**
     * Recalcule les totaux au chargement (fonction publique
     * pour usage depuis initLinesManagement).
     */
    function initQuantityTotals() {
        const container = document.querySelector(SELECTORS.linesContainer);

        if (container) {
            updateLinesSummary(container);
        }
    }
})();