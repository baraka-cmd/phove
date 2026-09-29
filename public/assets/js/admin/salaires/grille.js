/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : GRILLE SALARIALE
 * FICHIER: grille.js
 * ============================================================
 *
 * JavaScript principal de la grille salariale.
 *
 * PRINCIPES :
 * - JavaScript Vanilla
 * - Aucun jQuery
 * - Aucun Bootstrap JS
 * - Pas de logique métier critique côté client
 * - Laravel reste l'autorité métier
 * - Interface réactive et professionnelle
 * - Accessibilité
 * - Compatible Blade
 * - Préparation à une éventuelle évolution AJAX
 *
 * ============================================================
 */

(function () {
    "use strict";

    /**
     * --------------------------------------------------------
     * Configuration
     * --------------------------------------------------------
     */

    const CONFIG = {
        selectors: {
            root: ".salary-grid",

            monthForm: "[data-month-form]",
            monthInput: "[data-month-input]",
            monthSubmit: "[data-month-submit]",

            progress: "[data-progress]",

            printButton: "[data-print]",

            salaryRow: "[data-salary-row]",
            selectableRow: "[data-selectable-row]",

            salaryCheckbox: "[data-salary-checkbox]",
            selectAll: "[data-select-all]",

            refreshButton: "[data-refresh]",

            confirmAction: "[data-confirm-action]"
        },

        attributes: {
            progress: "data-progress",
            month: "data-month",
            salaryId: "data-salary-id",
            confirmMessage: "data-confirm-message"
        },

        progress: {
            min: 0,
            max: 100
        },

        classes: {
            selected: "is-selected",
            loading: "is-loading",
            disabled: "is-disabled"
        }
    };

    /**
     * --------------------------------------------------------
     * État
     * --------------------------------------------------------
     */

    const state = {
        initialized: false,
        selectedRows: new Set(),
        submitting: false
    };

    /**
     * --------------------------------------------------------
     * DOM
     * --------------------------------------------------------
     */

    const dom = {
        root: null,

        find(selector, context = document) {
            return context.querySelector(selector);
        },

        findAll(selector, context = document) {
            return Array.from(
                context.querySelectorAll(selector)
            );
        }
    };

    /**
     * --------------------------------------------------------
     * Utilitaires
     * --------------------------------------------------------
     */

    const utils = {
        isElement(element) {
            return element instanceof HTMLElement;
        },

        parseNumber(value, fallback = 0) {
            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return fallback;
            }

            const normalized = String(value)
                .replace(",", ".")
                .replace(/[^\d.-]/g, "");

            const parsed =
                Number.parseFloat(normalized);

            return Number.isFinite(parsed)
                ? parsed
                : fallback;
        },

        clamp(value, min, max) {
            return Math.min(
                Math.max(value, min),
                max
            );
        },

        formatNumber(value, decimals = 2) {
            const number =
                Number.parseFloat(value);

            if (!Number.isFinite(number)) {
                return "0";
            }

            return new Intl.NumberFormat(
                "fr-FR",
                {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }
            ).format(number);
        },

        getElementData(element, key) {
            if (!element) {
                return null;
            }

            return element.dataset[key] || null;
        }
    };

    /**
     * --------------------------------------------------------
     * Gestion du mois
     * --------------------------------------------------------
     */

    const month = {
        form: null,
        input: null,
        submit: null,

        init() {
            this.form = dom.find(
                CONFIG.selectors.monthForm,
                dom.root
            );

            this.input = dom.find(
                CONFIG.selectors.monthInput,
                dom.root
            );

            this.submit = dom.find(
                CONFIG.selectors.monthSubmit,
                dom.root
            );

            if (!this.form || !this.input) {
                return;
            }

            this.bind();
        },

        bind() {
            this.form.addEventListener(
                "submit",
                (event) => {
                    this.handleSubmit(event);
                }
            );

            this.input.addEventListener(
                "change",
                () => {
                    this.validate();
                }
            );

            this.input.addEventListener(
                "input",
                () => {
                    this.validate();
                }
            );
        },

        handleSubmit(event) {
            if (!this.validate()) {
                event.preventDefault();

                this.input.focus();

                return;
            }

            if (
                this.form.dataset.submitting ===
                "true"
            ) {
                event.preventDefault();

                return;
            }

            this.form.dataset.submitting =
                "true";

            this.setLoading(true);
        },

        validate() {
            if (!this.input) {
                return false;
            }

            const value =
                this.input.value.trim();

            /**
             * Le format attendu est YYYY-MM.
             */
            const validFormat =
                /^\d{4}-(0[1-9]|1[0-2])$/.test(
                    value
                );

            this.input.setCustomValidity(
                validFormat
                    ? ""
                    : "Veuillez sélectionner un mois valide."
            );

            return validFormat;
        },

        setLoading(isLoading) {
            if (!this.submit) {
                return;
            }

            if (isLoading) {
                this.submit.disabled = true;

                this.submit.classList.add(
                    CONFIG.classes.loading
                );

                this.submit.dataset.originalText =
                    this.submit.innerHTML;

                const loadingText =
                    this.submit.dataset.loadingText ||
                    "Chargement...";

                this.submit.innerHTML = `
                    <span
                        class="salary-grid-button-spinner"
                        aria-hidden="true"
                    ></span>
                    <span>${loadingText}</span>
                `;
            } else {
                this.submit.disabled = false;

                this.submit.classList.remove(
                    CONFIG.classes.loading
                );

                if (
                    this.submit.dataset.originalText
                ) {
                    this.submit.innerHTML =
                        this.submit.dataset.originalText;
                }
            }
        }
    };

    /**
     * --------------------------------------------------------
     * Progression des paiements
     * --------------------------------------------------------
     */

    const progress = {
        init() {
            const elements = dom.findAll(
                CONFIG.selectors.progress,
                dom.root
            );

            elements.forEach((element) => {
                this.update(element);
            });
        },

        update(element) {
            if (!utils.isElement(element)) {
                return;
            }

            const raw =
                element.getAttribute(
                    CONFIG.attributes.progress
                );

            const value =
                utils.parseNumber(raw);

            const percentage =
                utils.clamp(
                    value,
                    CONFIG.progress.min,
                    CONFIG.progress.max
                );

            element.style.width =
                `${percentage}%`;

            element.setAttribute(
                "role",
                "progressbar"
            );

            element.setAttribute(
                "aria-valuemin",
                "0"
            );

            element.setAttribute(
                "aria-valuemax",
                "100"
            );

            element.setAttribute(
                "aria-valuenow",
                String(percentage)
            );

            element.setAttribute(
                "aria-label",
                `Paiement à ${utils.formatNumber(
                    percentage,
                    0
                )} %`
            );
        }
    };

    /**
     * --------------------------------------------------------
     * Sélection des lignes
     * --------------------------------------------------------
     *
     * Utile si la Blade évolue vers des opérations groupées.
     * Même si aucune action groupée n'est encore disponible,
     * la sélection est gérée proprement.
     * --------------------------------------------------------
     */

    const selection = {
        selectAllCheckbox: null,
        checkboxes: [],

        init() {
            this.selectAllCheckbox = dom.find(
                CONFIG.selectors.selectAll,
                dom.root
            );

            this.checkboxes = dom.findAll(
                CONFIG.selectors.salaryCheckbox,
                dom.root
            );

            if (
                !this.selectAllCheckbox &&
                !this.checkboxes.length
            ) {
                return;
            }

            this.bind();

            this.syncState();
        },

        bind() {
            if (this.selectAllCheckbox) {
                this.selectAllCheckbox.addEventListener(
                    "change",
                    () => {
                        this.toggleAll(
                            this.selectAllCheckbox.checked
                        );
                    }
                );
            }

            this.checkboxes.forEach((checkbox) => {
                checkbox.addEventListener(
                    "change",
                    () => {
                        this.handleCheckboxChange(
                            checkbox
                        );
                    }
                );
            });
        },

        toggleAll(checked) {
            this.checkboxes.forEach(
                (checkbox) => {
                    if (checkbox.disabled) {
                        return;
                    }

                    checkbox.checked = checked;

                    this.handleCheckboxChange(
                        checkbox,
                        false
                    );
                }
            );

            this.syncState();
        },

        handleCheckboxChange(
            checkbox,
            synchronize = true
        ) {
            if (!checkbox) {
                return;
            }

            const row =
                checkbox.closest(
                    CONFIG.selectors.selectableRow
                );

            const id =
                checkbox.getAttribute(
                    CONFIG.attributes.salaryId
                ) ||
                checkbox.value;

            if (checkbox.checked) {
                if (id) {
                    state.selectedRows.add(id);
                }

                if (row) {
                    row.classList.add(
                        CONFIG.classes.selected
                    );
                }
            } else {
                if (id) {
                    state.selectedRows.delete(id);
                }

                if (row) {
                    row.classList.remove(
                        CONFIG.classes.selected
                    );
                }
            }

            if (synchronize) {
                this.syncState();
            }
        },

        syncState() {
            this.checkboxes.forEach(
                (checkbox) => {
                    const row =
                        checkbox.closest(
                            CONFIG.selectors.selectableRow
                        );

                    if (checkbox.checked) {
                        row?.classList.add(
                            CONFIG.classes.selected
                        );
                    } else {
                        row?.classList.remove(
                            CONFIG.classes.selected
                        );
                    }
                }
            );

            if (!this.selectAllCheckbox) {
                return;
            }

            const enabled =
                this.checkboxes.filter(
                    (checkbox) =>
                        !checkbox.disabled
                );

            const selected =
                enabled.filter(
                    (checkbox) =>
                        checkbox.checked
                );

            this.selectAllCheckbox.checked =
                enabled.length > 0 &&
                selected.length ===
                    enabled.length;

            this.selectAllCheckbox.indeterminate =
                selected.length > 0 &&
                selected.length <
                    enabled.length;

            this.selectAllCheckbox.setAttribute(
                "aria-checked",
                this.selectAllCheckbox.indeterminate
                    ? "mixed"
                    : String(
                        this.selectAllCheckbox.checked
                    )
            );
        },

        clear() {
            state.selectedRows.clear();

            this.checkboxes.forEach(
                (checkbox) => {
                    checkbox.checked = false;

                    const row =
                        checkbox.closest(
                            CONFIG.selectors.selectableRow
                        );

                    row?.classList.remove(
                        CONFIG.classes.selected
                    );
                }
            );

            this.syncState();
        },

        getSelectedIds() {
            return Array.from(
                state.selectedRows
            );
        }
    };

    /**
     * --------------------------------------------------------
     * Actions sensibles
     * --------------------------------------------------------
     */

    const actions = {
        init() {
            const elements = dom.findAll(
                CONFIG.selectors.confirmAction,
                dom.root
            );

            elements.forEach((element) => {
                element.addEventListener(
                    "click",
                    (event) => {
                        this.handle(
                            event,
                            element
                        );
                    }
                );
            });
        },

        handle(event, element) {
            if (!element) {
                return;
            }

            if (
                element.dataset.locked ===
                "true"
            ) {
                event.preventDefault();

                return;
            }

            const message =
                element.getAttribute(
                    CONFIG.attributes.confirmMessage
                ) ||
                element.dataset.confirm ||
                "Voulez-vous continuer cette opération ?";

            if (
                !window.confirm(message)
            ) {
                event.preventDefault();

                return;
            }

            this.lock(element);
        },

        lock(element) {
            element.dataset.locked =
                "true";

            if (
                element.tagName === "BUTTON" ||
                element.tagName === "INPUT"
            ) {
                element.disabled = true;
            }

            element.classList.add(
                CONFIG.classes.loading
            );
        }
    };

    /**
     * --------------------------------------------------------
     * Bouton actualiser
     * --------------------------------------------------------
     */

    const refresh = {
        init() {
            const button = dom.find(
                CONFIG.selectors.refreshButton,
                dom.root
            );

            if (!button) {
                return;
            }

            button.addEventListener(
                "click",
                (event) => {
                    event.preventDefault();

                    this.execute(button);
                }
            );
        },

        execute(button) {
            if (
                button.dataset.loading ===
                "true"
            ) {
                return;
            }

            button.dataset.loading =
                "true";

            button.disabled = true;

            button.classList.add(
                CONFIG.classes.loading
            );

            const url =
                button.getAttribute("href") ||
                button.dataset.url;

            if (url) {
                window.location.assign(url);

                return;
            }

            /**
             * Si aucune URL n'est définie,
             * on recharge simplement la page.
             */
            window.location.reload();
        }
    };

    /**
     * --------------------------------------------------------
     * Impression
     * --------------------------------------------------------
     */

    const printing = {
        init() {
            const buttons = dom.findAll(
                CONFIG.selectors.printButton,
                dom.root
            );

            buttons.forEach((button) => {
                button.addEventListener(
                    "click",
                    (event) => {
                        event.preventDefault();

                        this.print();
                    }
                );
            });
        },

        print() {
            /**
             * L'impression est volontairement
             * native afin que le CSS @media print
             * puisse contrôler la mise en page.
             */
            window.print();
        }
    };

    /**
     * --------------------------------------------------------
     * Navigation clavier dans la grille
     * --------------------------------------------------------
     */

    const keyboard = {
        init() {
            const rows = dom.findAll(
                CONFIG.selectors.salaryRow,
                dom.root
            );

            if (!rows.length) {
                return;
            }

            rows.forEach((row) => {
                row.addEventListener(
                    "keydown",
                    (event) => {
                        this.handleRowKeydown(
                            event,
                            row,
                            rows
                        );
                    }
                );
            });
        },

        handleRowKeydown(
            event,
            row,
            rows
        ) {
            if (
                event.key !== "ArrowDown" &&
                event.key !== "ArrowUp"
            ) {
                return;
            }

            const currentIndex =
                rows.indexOf(row);

            if (currentIndex === -1) {
                return;
            }

            event.preventDefault();

            let targetIndex =
                currentIndex +
                (
                    event.key === "ArrowDown"
                        ? 1
                        : -1
                );

            targetIndex =
                Math.max(
                    0,
                    Math.min(
                        targetIndex,
                        rows.length - 1
                    )
                );

            const target =
                rows[targetIndex];

            if (!target) {
                return;
            }

            target.focus();
        }
    };

    /**
     * --------------------------------------------------------
     * Gestion des formulaires
     * --------------------------------------------------------
     */

    const forms = {
        init() {
            const forms =
                dom.findAll(
                    "form",
                    dom.root
                );

            forms.forEach((form) => {
                form.addEventListener(
                    "submit",
                    (event) => {
                        this.handle(
                            event,
                            form
                        );
                    }
                );
            });
        },

        handle(event, form) {
            if (!form) {
                return;
            }

            if (
                form.dataset.submitting ===
                "true"
            ) {
                event.preventDefault();

                return;
            }

            /**
             * La validation HTML native reste active.
             */
            if (
                !form.checkValidity()
            ) {
                return;
            }

            form.dataset.submitting =
                "true";

            const buttons =
                form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );

            buttons.forEach(
                (button) => {
                    button.disabled =
                        true;

                    button.classList.add(
                        CONFIG.classes.loading
                    );
                }
            );
        }
    };

    /**
     * --------------------------------------------------------
     * Gestion de la visibilité du mois courant
     * --------------------------------------------------------
     *
     * Ajoute une information visuelle si la grille
     * correspond au mois courant.
     * --------------------------------------------------------
     */

    const currentMonth = {
        init() {
            const value =
                dom.root?.dataset.currentMonth;

            const input =
                month.input;

            if (!value || !input) {
                return;
            }

            if (
                input.value === value
            ) {
                dom.root.classList.add(
                    "is-current-period"
                );
            } else {
                dom.root.classList.remove(
                    "is-current-period"
                );
            }
        }
    };

    /**
     * --------------------------------------------------------
     * Initialisation globale
     * --------------------------------------------------------
     */

    function init() {
        if (state.initialized) {
            return;
        }

        dom.root = dom.find(
            CONFIG.selectors.root
        );

        if (!dom.root) {
            return;
        }

        month.init();
        progress.init();
        selection.init();
        actions.init();
        refresh.init();
        printing.init();
        keyboard.init();
        forms.init();
        currentMonth.init();

        state.initialized = true;

        dom.root.dataset.jsReady =
            "true";
    }

    /**
     * --------------------------------------------------------
     * DOM Ready
     * --------------------------------------------------------
     */

    if (
        document.readyState ===
        "loading"
    ) {
        document.addEventListener(
            "DOMContentLoaded",
            init,
            { once: true }
        );
    } else {
        init();
    }

    /**
     * --------------------------------------------------------
     * API publique minimale
     * --------------------------------------------------------
     */

    window.PHOVE =
        window.PHOVE || {};

    window.PHOVE.salaryGrid = {
        refreshProgress() {
            progress.init();
        },

        clearSelection() {
            selection.clear();
        },

        getSelectedSalaryIds() {
            return selection.getSelectedIds();
        },

        print() {
            printing.print();
        }
    };
})();