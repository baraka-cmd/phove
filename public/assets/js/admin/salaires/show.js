/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : DÉTAIL D'UN SALAIRE
 * FICHIER: show.js
 * ============================================================
 *
 * Responsabilités :
 * - Gestion des barres de progression
 * - Gestion des actions sensibles
 * - Confirmation des opérations
 * - Gestion des modales
 * - Copie des références
 * - Impression
 * - Raccourcis clavier
 * - Accessibilité
 * - Protection contre les doubles clics
 *
 * IMPORTANT :
 * Ce fichier ne contient aucune logique métier critique.
 *
 * Les règles métier sont toujours contrôlées côté Laravel :
 * - Policy
 * - Form Request
 * - Service
 * - Transaction
 *
 * ============================================================
 */

(function () {
    "use strict";

    /* ========================================================
       CONFIGURATION
       ======================================================== */

    const CONFIG = {

        selectors: {

            root: ".salary-show",

            progress:
                "[data-progress]",

            print:
                "[data-print]",

            copy:
                "[data-copy]",

            modal:
                "[data-modal]",

            modalOpen:
                "[data-modal-open]",

            modalClose:
                "[data-modal-close]",

            modalCancel:
                "[data-modal-cancel]",

            confirmAction:
                "[data-confirm-action]",

            sensitiveAction:
                "[data-sensitive-action]",

            paymentAction:
                "[data-payment-action]",

            table:
                "[data-payment-table]",

            paymentRow:
                "[data-payment-row]",

            emptyState:
                "[data-empty-state]",

            tooltip:
                "[data-tooltip]"
        },

        progress: {
            min: 0,
            max: 100
        },

        animation: {
            duration: 450
        },

        selectorsFallback: {
            modal:
                ".salary-modal"
        }
    };

    /* ========================================================
       ÉTAT GLOBAL
       ======================================================== */

    const state = {

        initialized: false,

        activeModal: null,

        lockedActions:
            new WeakSet()
    };

    /* ========================================================
       DOM
       ======================================================== */

    const dom = {

        root: null,

        find(
            selector,
            context = document
        ) {
            return context.querySelector(
                selector
            );
        },

        findAll(
            selector,
            context = document
        ) {
            return Array.from(
                context.querySelectorAll(
                    selector
                )
            );
        }
    };

    /* ========================================================
       UTILITAIRES
       ======================================================== */

    const utils = {

        parseNumber(value) {

            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return 0;
            }

            const normalized =
                String(value)
                    .replace(",", ".")
                    .replace(
                        /[^\d.-]/g,
                        ""
                    );

            const number =
                Number.parseFloat(
                    normalized
                );

            return Number.isFinite(number)
                ? number
                : 0;
        },

        clamp(
            value,
            min,
            max
        ) {
            return Math.min(
                Math.max(
                    value,
                    min
                ),
                max
            );
        },

        isElement(element) {
            return (
                element instanceof
                HTMLElement
            );
        },

        isVisible(element) {

            if (!element) {
                return false;
            }

            const style =
                window.getComputedStyle(
                    element
                );

            return (
                style.display !== "none" &&
                style.visibility !== "hidden"
            );
        },

        getFocusableElements(
            container
        ) {

            return Array.from(
                container.querySelectorAll(
                    [
                        "a[href]",
                        "button:not([disabled])",
                        "input:not([disabled])",
                        "select:not([disabled])",
                        "textarea:not([disabled])",
                        "[tabindex]:not([tabindex='-1'])"
                    ].join(",")
                )
            );
        }
    };

    /* ========================================================
       BARRES DE PROGRESSION
       ======================================================== */

    const progress = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.progress,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    this.render(
                        element
                    );
                }
            );
        },

        render(element) {

            const raw =
                element.dataset.progress ??
                element.getAttribute(
                    "data-progress"
                );

            const value =
                utils.clamp(
                    utils.parseNumber(
                        raw
                    ),
                    CONFIG.progress.min,
                    CONFIG.progress.max
                );

            /*
             * On utilise le style uniquement
             * comme résultat calculé à partir
             * de data-progress.
             *
             * Aucun pourcentage n'est codé
             * en dur dans la Blade.
             */
            element.style.width =
                `${value}%`;

            element.setAttribute(
                "role",
                "progressbar"
            );

            element.setAttribute(
                "aria-valuemin",
                String(
                    CONFIG.progress.min
                )
            );

            element.setAttribute(
                "aria-valuemax",
                String(
                    CONFIG.progress.max
                )
            );

            element.setAttribute(
                "aria-valuenow",
                String(value)
            );
        }
    };

    /* ========================================================
       IMPRESSION
       ======================================================== */

    const printing = {

        init() {

            const buttons =
                dom.findAll(
                    CONFIG.selectors.print,
                    dom.root
                );

            buttons.forEach(
                (button) => {

                    button.addEventListener(
                        "click",
                        (event) => {

                            event.preventDefault();

                            this.print(
                                button
                            );
                        }
                    );
                }
            );
        },

        print(button) {

            if (
                button &&
                button.dataset.printUrl
            ) {

                window.open(
                    button.dataset.printUrl,
                    "_blank",
                    "noopener,noreferrer"
                );

                return;
            }

            window.print();
        }
    };

    /* ========================================================
       COPIE DES RÉFÉRENCES
       ======================================================== */

    const clipboard = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.copy,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    element.addEventListener(
                        "click",
                        () => {

                            this.copy(
                                element
                            );
                        }
                    );
                }
            );
        },

        async copy(element) {

            const value =
                element.dataset.copyValue ??
                element.getAttribute(
                    "data-copy-value"
                ) ??
                element.textContent.trim();

            if (!value) {
                return;
            }

            try {

                await navigator.clipboard.writeText(
                    value
                );

                this.success(
                    element
                );

            } catch (
                error
            ) {

                this.fallback(
                    value,
                    element
                );
            }
        },

        fallback(
            value,
            element
        ) {

            const textarea =
                document.createElement(
                    "textarea"
                );

            textarea.value =
                value;

            textarea.setAttribute(
                "readonly",
                ""
            );

            textarea.style.position =
                "fixed";

            textarea.style.opacity =
                "0";

            document.body.appendChild(
                textarea
            );

            textarea.select();

            try {
                document.execCommand(
                    "copy"
                );

                this.success(
                    element
                );
            } catch (
                error
            ) {
                console.error(
                    "Impossible de copier la référence.",
                    error
                );
            }

            textarea.remove();
        },

        success(element) {

            const original =
                element.dataset.originalLabel ||
                element.textContent;

            if (
                !element.dataset.originalLabel
            ) {
                element.dataset.originalLabel =
                    original;
            }

            element.textContent =
                "Copié";

            element.classList.add(
                "is-copied"
            );

            window.setTimeout(
                () => {

                    element.textContent =
                        original;

                    element.classList.remove(
                        "is-copied"
                    );

                },
                1600
            );
        }
    };

    /* ========================================================
       MODALES
       ======================================================== */

    const modal = {

        init() {

            const openers =
                dom.findAll(
                    CONFIG.selectors.modalOpen,
                    dom.root
                );

            openers.forEach(
                (button) => {

                    button.addEventListener(
                        "click",
                        (event) => {

                            event.preventDefault();

                            this.openFrom(
                                button
                            );
                        }
                    );
                }
            );

            const closers =
                dom.findAll(
                    CONFIG.selectors.modalClose,
                    dom.root
                );

            closers.forEach(
                (button) => {

                    button.addEventListener(
                        "click",
                        (event) => {

                            event.preventDefault();

                            this.close(
                                button.closest(
                                    CONFIG.selectors.modal
                                )
                            );
                        }
                    );
                }
            );

            const cancels =
                dom.findAll(
                    CONFIG.selectors.modalCancel,
                    dom.root
                );

            cancels.forEach(
                (button) => {

                    button.addEventListener(
                        "click",
                        (event) => {

                            event.preventDefault();

                            this.close(
                                button.closest(
                                    CONFIG.selectors.modal
                                )
                            );
                        }
                    );
                }
            );

            /*
             * Fermeture par clic sur l'arrière-plan.
             */
            dom.findAll(
                CONFIG.selectors.modal,
                dom.root
            ).forEach(
                (element) => {

                    element.addEventListener(
                        "click",
                        (event) => {

                            if (
                                event.target ===
                                element
                            ) {
                                this.close(
                                    element
                                );
                            }
                        }
                    );
                }
            );

            /*
             * Fermeture avec Escape.
             */
            document.addEventListener(
                "keydown",
                (event) => {

                    if (
                        event.key ===
                        "Escape"
                    ) {

                        if (
                            state.activeModal
                        ) {

                            this.close(
                                state.activeModal
                            );
                        }
                    }
                }
            );
        },

        openFrom(button) {

            const target =
                button.dataset.modalOpen;

            if (!target) {
                return;
            }

            let modalElement = null;

            try {

                modalElement =
                    document.querySelector(
                        target
                    );

            } catch (
                error
            ) {

                console.error(
                    "Sélecteur de modal invalide.",
                    error
                );

                return;
            }

            if (!modalElement) {
                return;
            }

            this.open(
                modalElement
            );
        },

        open(element) {

            if (!element) {
                return;
            }

            if (
                state.activeModal &&
                state.activeModal !== element
            ) {

                this.close(
                    state.activeModal
                );
            }

            state.activeModal =
                element;

            element.hidden =
                false;

            element.classList.add(
                "is-open"
            );

            element.setAttribute(
                "aria-hidden",
                "false"
            );

            document.body.classList.add(
                "modal-open"
            );

            const focusable =
                utils.getFocusableElements(
                    element
                );

            if (
                focusable.length
            ) {

                window.setTimeout(
                    () => {
                        focusable[0].focus();
                    },
                    20
                );
            }
        },

        close(element) {

            if (!element) {
                return;
            }

            element.classList.remove(
                "is-open"
            );

            element.setAttribute(
                "aria-hidden",
                "true"
            );

            element.hidden =
                true;

            if (
                state.activeModal ===
                element
            ) {

                state.activeModal =
                    null;

                document.body.classList.remove(
                    "modal-open"
                );
            }
        }
    };

    /* ========================================================
       ACTIONS SENSIBLES
       ======================================================== */

    const actions = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.sensitiveAction,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    element.addEventListener(
                        "click",
                        (event) => {

                            if (
                                !this.confirm(
                                    element
                                )
                            ) {

                                event.preventDefault();

                                return;
                            }

                            this.lock(
                                element
                            );
                        }
                    );
                }
            );
        },

        confirm(element) {

            const message =
                element.dataset.confirmMessage ||
                "Confirmez-vous cette opération ?";

            return window.confirm(
                message
            );
        },

        lock(element) {

            if (
                state.lockedActions.has(
                    element
                )
            ) {
                return;
            }

            state.lockedActions.add(
                element
            );

            if (
                element.tagName ===
                "BUTTON"
            ) {

                element.disabled =
                    true;
            }

            element.classList.add(
                "is-loading"
            );
        }
    };

    /* ========================================================
       ACTIONS DE PAIEMENT
       ======================================================== */

    const paymentActions = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.paymentAction,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    element.addEventListener(
                        "click",
                        (event) => {

                            const allowed =
                                this.confirm(
                                    element
                                );

                            if (!allowed) {

                                event.preventDefault();

                                return;
                            }

                            this.lock(
                                element
                            );
                        }
                    );
                }
            );
        },

        confirm(element) {

            const message =
                element.dataset.confirmMessage ||
                "Voulez-vous continuer vers l'enregistrement du paiement ?";

            return window.confirm(
                message
            );
        },

        lock(element) {

            if (
                state.lockedActions.has(
                    element
                )
            ) {
                return;
            }

            state.lockedActions.add(
                element
            );

            element.classList.add(
                "is-loading"
            );

            if (
                "disabled" in element
            ) {
                element.disabled =
                    true;
            }
        }
    };

    /* ========================================================
       LIENS D'ACTION CONFIRMÉS
       ======================================================== */

    const confirmedActions = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.confirmAction,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    element.addEventListener(
                        "click",
                        (event) => {

                            const message =
                                element.dataset.confirmMessage ||
                                "Confirmez-vous cette action ?";

                            if (
                                !window.confirm(
                                    message
                                )
                            ) {

                                event.preventDefault();

                                return;
                            }

                            this.lock(
                                element
                            );
                        }
                    );
                }
            );
        },

        lock(element) {

            if (
                state.lockedActions.has(
                    element
                )
            ) {
                return;
            }

            state.lockedActions.add(
                element
            );

            element.classList.add(
                "is-loading"
            );
        }
    };

    /* ========================================================
       TABLEAU DES PAIEMENTS
       ======================================================== */

    const paymentTable = {

        init() {

            const table =
                dom.find(
                    CONFIG.selectors.table,
                    dom.root
                );

            if (!table) {
                return;
            }

            const rows =
                dom.findAll(
                    CONFIG.selectors.paymentRow,
                    table
                );

            rows.forEach(
                (row, index) => {

                    row.dataset.index =
                        String(
                            index + 1
                        );

                    this.bindRow(
                        row
                    );
                }
            );
        },

        bindRow(row) {

            row.addEventListener(
                "keydown",
                (event) => {

                    if (
                        event.key ===
                        "Enter"
                    ) {

                        const link =
                            row.querySelector(
                                "a"
                            );

                        if (link) {
                            link.click();
                        }
                    }
                }
            );
        }
    };

    /* ========================================================
       OUTILS D'ACCESSIBILITÉ
       ======================================================== */

    const accessibility = {

        init() {

            this.tooltips();

            this.externalLinks();

            this.buttons();
        },

        tooltips() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.tooltip,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    if (
                        !element.getAttribute(
                            "aria-label"
                        ) &&
                        element.dataset.tooltip
                    ) {

                        element.setAttribute(
                            "aria-label",
                            element.dataset.tooltip
                        );
                    }
                }
            );
        },

        externalLinks() {

            const links =
                dom.findAll(
                    'a[target="_blank"]',
                    dom.root
                );

            links.forEach(
                (link) => {

                    const rel =
                        link.getAttribute(
                            "rel"
                        ) || "";

                    if (
                        !rel.includes(
                            "noopener"
                        )
                    ) {

                        link.setAttribute(
                            "rel",
                            `${rel} noopener noreferrer`
                        );
                    }
                }
            );
        },

        buttons() {

            const buttons =
                dom.findAll(
                    "button",
                    dom.root
                );

            buttons.forEach(
                (button) => {

                    if (
                        !button.getAttribute(
                            "type"
                        )
                    ) {

                        button.setAttribute(
                            "type",
                            "button"
                        );
                    }
                }
            );
        }
    };

    /* ========================================================
       RACCOURCIS CLAVIER
       ======================================================== */

    const keyboard = {

        init() {

            document.addEventListener(
                "keydown",
                (event) => {

                    /*
                     * Ctrl + P
                     */
                    if (
                        event.ctrlKey &&
                        event.key.toLowerCase() ===
                        "p"
                    ) {

                        event.preventDefault();

                        window.print();
                    }
                }
            );
        }
    };

    /* ========================================================
       INITIALISATION
       ======================================================== */

    function init() {

        if (
            state.initialized
        ) {
            return;
        }

        dom.root =
            dom.find(
                CONFIG.selectors.root
            );

        if (!dom.root) {
            return;
        }

        progress.init();

        printing.init();

        clipboard.init();

        modal.init();

        actions.init();

        paymentActions.init();

        confirmedActions.init();

        paymentTable.init();

        accessibility.init();

        keyboard.init();

        state.initialized =
            true;

        dom.root.dataset.jsReady =
            "true";
    }

    /* ========================================================
       DOM READY
       ======================================================== */

    if (
        document.readyState ===
        "loading"
    ) {

        document.addEventListener(
            "DOMContentLoaded",
            init,
            {
                once: true
            }
        );

    } else {

        init();
    }

    /* ========================================================
       API PUBLIQUE
       ======================================================== */

    window.PHOVE =
        window.PHOVE || {};

    window.PHOVE.salaryShow = {

        refreshProgress() {
            progress.init();
        },

        print() {
            window.print();
        },

        openModal(selector) {

            const element =
                dom.find(
                    selector,
                    dom.root
                );

            if (element) {
                modal.open(
                    element
                );
            }
        },

        closeModal(selector) {

            const element =
                dom.find(
                    selector,
                    dom.root
                );

            if (element) {
                modal.close(
                    element
                );
            }
        }
    };

})();