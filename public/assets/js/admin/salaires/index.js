/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : INDEX
 * FICHIER: index.js
 * ============================================================
 *
 * JavaScript principal de la liste des salaires.
 *
 * PRINCIPES :
 * - JavaScript Vanilla
 * - Aucun jQuery
 * - Aucun Bootstrap JS
 * - Aucun appel métier côté client
 * - Le serveur Laravel reste l'autorité
 * - Progressive enhancement
 * - Compatible avec Blade
 * - Protection contre les doubles soumissions
 * - Accessibilité
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
            root: ".salary-index",

            filterForm: "[data-salary-filter-form]",
            resetFilters: "[data-reset-filters]",
            searchInput: "[data-salary-search]",

            confirmAction: "[data-confirm-action]",
            submitButton: "[data-submit-lock]",

            progress: "[data-progress]",

            printButton: "[data-print]",

            table: "[data-salary-table]",
            row: "[data-salary-row]"
        },

        attributes: {
            progress: "data-progress",
            confirmMessage: "data-confirm-message",
            action: "data-action"
        },

        classes: {
            loading: "is-loading",
            submitted: "is-submitted",
            hidden: "is-hidden"
        },

        keyboard: {
            search: {
                key: "f",
                ctrl: true,
                meta: true
            }
        },

        progress: {
            min: 0,
            max: 100
        },

        debounceDelay: 350
    };

    /**
     * --------------------------------------------------------
     * État interne
     * --------------------------------------------------------
     */
    const state = {
        initialized: false,
        filterTimer: null,
        submitting: false
    };

    /**
     * --------------------------------------------------------
     * Utilitaires DOM
     * --------------------------------------------------------
     */

    const dom = {
        root: null,

        find(selector, context = document) {
            return context.querySelector(selector);
        },

        findAll(selector, context = document) {
            return Array.from(context.querySelectorAll(selector));
        }
    };

    /**
     * --------------------------------------------------------
     * Utilitaires généraux
     * --------------------------------------------------------
     */

    const utils = {
        isElement(element) {
            return element instanceof HTMLElement;
        },

        clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        },

        parseNumber(value, fallback = 0) {
            if (value === null || value === undefined || value === "") {
                return fallback;
            }

            const normalized = String(value)
                .replace(",", ".")
                .replace(/[^\d.-]/g, "");

            const number = Number.parseFloat(normalized);

            return Number.isFinite(number) ? number : fallback;
        },

        debounce(callback, delay) {
            return function (...args) {
                window.clearTimeout(state.filterTimer);

                state.filterTimer = window.setTimeout(() => {
                    callback.apply(this, args);
                }, delay);
            };
        },

        getFormAction(form) {
            if (!form) {
                return null;
            }

            return form.getAttribute("action");
        },

        isExternalUrl(url) {
            if (!url) {
                return false;
            }

            try {
                const parsed = new URL(url, window.location.origin);

                return parsed.origin !== window.location.origin;
            } catch {
                return false;
            }
        }
    };

    /**
     * --------------------------------------------------------
     * Progression des paiements
     * --------------------------------------------------------
     *
     * La Blade peut utiliser :
     *
     * <div class="..." data-progress="75"></div>
     *
     * Le JS applique ensuite la largeur.
     *
     * Cela évite les styles inline dans Blade.
     * --------------------------------------------------------
     */

    const progress = {
        init() {
            const elements = dom.findAll(
                CONFIG.selectors.progress,
                dom.root || document
            );

            elements.forEach((element) => {
                this.update(element);
            });
        },

        update(element) {
            if (!utils.isElement(element)) {
                return;
            }

            const rawValue = element.getAttribute(
                CONFIG.attributes.progress
            );

            const value = utils.parseNumber(rawValue);

            const percentage = utils.clamp(
                value,
                CONFIG.progress.min,
                CONFIG.progress.max
            );

            element.style.width = `${percentage}%`;

            element.setAttribute(
                "aria-valuenow",
                String(percentage)
            );

            if (!element.hasAttribute("role")) {
                element.setAttribute("role", "progressbar");
            }

            if (!element.hasAttribute("aria-valuemin")) {
                element.setAttribute("aria-valuemin", "0");
            }

            if (!element.hasAttribute("aria-valuemax")) {
                element.setAttribute("aria-valuemax", "100");
            }
        }
    };

    /**
     * --------------------------------------------------------
     * Gestion des filtres
     * --------------------------------------------------------
     */

    const filters = {
        form: null,
        searchInput: null,

        init() {
            this.form = dom.find(CONFIG.selectors.filterForm, dom.root);
            this.searchInput = dom.find(
                CONFIG.selectors.searchInput,
                dom.root
            );

            if (!this.form) {
                return;
            }

            this.bind();

            this.normalizeEmptyFields();
        },

        bind() {
            this.form.addEventListener("submit", (event) => {
                this.handleSubmit(event);
            });

            const resetButton = dom.find(
                CONFIG.selectors.resetFilters,
                dom.root
            );

            if (resetButton) {
                resetButton.addEventListener("click", () => {
                    this.reset();
                });
            }

            if (this.searchInput) {
                this.searchInput.addEventListener(
                    "keydown",
                    (event) => {
                        if (event.key === "Enter") {
                            return;
                        }

                        if (event.key === "Escape") {
                            this.searchInput.value = "";
                            this.searchInput.focus();
                        }
                    }
                );
            }
        },

        handleSubmit(event) {
            const form = event.currentTarget;

            if (!form) {
                return;
            }

            this.normalizeEmptyFields();

            const submitter =
                event.submitter ||
                form.querySelector('[type="submit"]');

            if (submitter) {
                this.lockSubmitButton(submitter);
            }
        },

        normalizeEmptyFields() {
            if (!this.form) {
                return;
            }

            const fields = this.form.querySelectorAll(
                "input, select, textarea"
            );

            fields.forEach((field) => {
                if (
                    field.type === "hidden" ||
                    field.type === "submit" ||
                    field.type === "button" ||
                    field.type === "reset"
                ) {
                    return;
                }

                if (
                    typeof field.value === "string" &&
                    field.value.trim() === ""
                ) {
                    /**
                     * On ne désactive pas automatiquement le champ.
                     * Laravel peut avoir besoin de recevoir une valeur vide.
                     */
                }
            });
        },

        reset() {
            if (!this.form) {
                return;
            }

            const action = this.getResetUrl();

            if (action) {
                window.location.assign(action);
                return;
            }

            this.form.reset();

            if (this.searchInput) {
                this.searchInput.focus();
            }
        },

        getResetUrl() {
            const button = dom.find(
                CONFIG.selectors.resetFilters,
                dom.root
            );

            if (!button) {
                return null;
            }

            return (
                button.getAttribute("href") ||
                button.dataset.url ||
                null
            );
        },

        lockSubmitButton(button) {
            if (!utils.isElement(button)) {
                return;
            }

            if (button.dataset.locked === "true") {
                return;
            }

            button.dataset.originalText =
                button.innerHTML;

            button.dataset.locked = "true";

            button.disabled = true;

            button.classList.add(
                CONFIG.classes.loading
            );

            const loadingText =
                button.dataset.loadingText ||
                "Chargement...";

            button.innerHTML = `
                <span
                    class="salary-index-button-spinner"
                    aria-hidden="true"
                ></span>
                <span>${loadingText}</span>
            `;
        }
    };

    /**
     * --------------------------------------------------------
     * Actions sensibles
     * --------------------------------------------------------
     *
     * Les décisions restent côté Laravel.
     *
     * Le JS ne fait qu'ajouter une confirmation utilisateur.
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
                        this.handleClick(event, element);
                    }
                );
            });
        },

        handleClick(event, element) {
            if (!utils.isElement(element)) {
                return;
            }

            /**
             * Si le bouton est déjà verrouillé,
             * on évite un double clic.
             */
            if (element.dataset.locked === "true") {
                event.preventDefault();
                return;
            }

            const message =
                element.getAttribute(
                    CONFIG.attributes.confirmMessage
                ) ||
                element.dataset.confirm ||
                "Voulez-vous continuer cette opération ?";

            const confirmed = window.confirm(message);

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            this.lock(element);
        },

        lock(element) {
            if (!utils.isElement(element)) {
                return;
            }

            element.dataset.locked = "true";

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
     * Protection des formulaires POST/PUT/DELETE
     * --------------------------------------------------------
     */

    const forms = {
        init() {
            const elements = dom.findAll(
                "form",
                dom.root
            );

            elements.forEach((form) => {
                form.addEventListener("submit", (event) => {
                    this.handleSubmit(event, form);
                });
            });
        },

        handleSubmit(event, form) {
            if (
                !form ||
                form.dataset.protected === "true"
            ) {
                return;
            }

            /**
             * On ne bloque pas le formulaire.
             * On verrouille uniquement les boutons.
             */
            const submitButtons = form.querySelectorAll(
                'button[type="submit"], input[type="submit"]'
            );

            if (!submitButtons.length) {
                return;
            }

            submitButtons.forEach((button) => {
                if (button.dataset.locked === "true") {
                    event.preventDefault();
                    return;
                }

                button.dataset.locked = "true";

                button.disabled = true;

                button.classList.add(
                    CONFIG.classes.loading
                );

                const loadingText =
                    button.dataset.loadingText;

                if (
                    loadingText &&
                    button.tagName === "BUTTON"
                ) {
                    button.dataset.originalText =
                        button.innerHTML;

                    button.innerHTML = `
                        <span
                            class="salary-index-button-spinner"
                            aria-hidden="true"
                        ></span>
                        <span>${loadingText}</span>
                    `;
                }
            });

            form.dataset.protected = "true";
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
                button.addEventListener("click", (event) => {
                    event.preventDefault();

                    this.print();
                });
            });
        },

        print() {
            window.print();
        }
    };

    /**
     * --------------------------------------------------------
     * Raccourcis clavier
     * --------------------------------------------------------
     */

    const keyboard = {
        init() {
            document.addEventListener(
                "keydown",
                (event) => {
                    this.handle(event);
                }
            );
        },

        handle(event) {
            const isModifierPressed =
                event.ctrlKey || event.metaKey;

            if (
                CONFIG.keyboard.search.ctrl &&
                isModifierPressed &&
                event.key.toLowerCase() ===
                    CONFIG.keyboard.search.key
            ) {
                if (!filters.searchInput) {
                    return;
                }

                /**
                 * Évite d'interférer avec un champ
                 * texte déjà actif.
                 */
                const activeElement =
                    document.activeElement;

                const isEditable =
                    activeElement &&
                    (
                        activeElement.tagName === "INPUT" ||
                        activeElement.tagName === "TEXTAREA" ||
                        activeElement.isContentEditable
                    );

                if (isEditable) {
                    return;
                }

                event.preventDefault();

                filters.searchInput.focus();

                filters.searchInput.select();
            }
        }
    };

    /**
     * --------------------------------------------------------
     * Amélioration des liens externes
     * --------------------------------------------------------
     */

    const links = {
        init() {
            const anchors = dom.findAll(
                "a[target='_blank']",
                dom.root
            );

            anchors.forEach((anchor) => {
                const current =
                    anchor.getAttribute("rel") || "";

                const values = new Set(
                    current
                        .split(/\s+/)
                        .filter(Boolean)
                );

                values.add("noopener");
                values.add("noreferrer");

                anchor.setAttribute(
                    "rel",
                    Array.from(values).join(" ")
                );
            });
        }
    };

    /**
     * --------------------------------------------------------
     * Détection du clic sur les liens d'action
     * --------------------------------------------------------
     */

    const navigation = {
        init() {
            const anchors = dom.findAll(
                "a",
                dom.root
            );

            anchors.forEach((anchor) => {
                anchor.addEventListener(
                    "click",
                    (event) => {
                        this.handle(anchor, event);
                    }
                );
            });
        },

        handle(anchor, event) {
            if (
                !anchor ||
                event.defaultPrevented
            ) {
                return;
            }

            if (
                anchor.target === "_blank" ||
                anchor.hasAttribute("download")
            ) {
                return;
            }

            if (
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            const href =
                anchor.getAttribute("href");

            if (!href || href === "#") {
                return;
            }

            if (
                anchor.dataset.noNavigationLock !==
                undefined
            ) {
                return;
            }

            /**
             * On ne verrouille pas tous les liens :
             * les liens normaux doivent rester naturels.
             */
        }
    };

    /**
     * --------------------------------------------------------
     * Initialisation
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

        progress.init();
        filters.init();
        actions.init();
        forms.init();
        printing.init();
        keyboard.init();
        links.init();
        navigation.init();

        state.initialized = true;

        dom.root.dataset.jsReady = "true";
    }

    /**
     * --------------------------------------------------------
     * DOM Ready
     * --------------------------------------------------------
     */

    if (document.readyState === "loading") {
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
     * API minimale pour d'éventuels modules externes
     * --------------------------------------------------------
     *
     * Aucun détail interne n'est exposé.
     * --------------------------------------------------------
     */

    window.PHOVE = window.PHOVE || {};

    window.PHOVE.salaryIndex = {
        refreshProgress() {
            progress.init();
        },

        print() {
            printing.print();
        }
    };
})();