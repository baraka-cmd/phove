/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : PAIEMENTS DES SALAIRES
 * VUE    : SHOW
 * FICHIER: show.js
 * ============================================================
 *
 * Page de consultation détaillée d'un paiement de salaire.
 *
 * Responsabilités :
 * - Gestion des barres de progression
 * - Impression
 * - Copie des références
 * - Confirmation des actions sensibles
 * - Navigation vers le salaire
 * - Accessibilité
 * - Gestion des images/logo
 * - Raccourci Ctrl + P
 *
 * Aucune logique métier financière critique n'est effectuée ici.
 * Les montants affichés proviennent du serveur Laravel.
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

            root:
                ".salary-payment-show",

            progress:
                "[data-progress]",

            print:
                "[data-print]",

            copy:
                "[data-copy]",

            sensitiveAction:
                "[data-sensitive-action]",

            confirmAction:
                "[data-confirm-action]",

            paymentAction:
                "[data-payment-action]",

            back:
                "[data-back]",

            logo:
                "img[data-logo]",

            externalLinks:
                'a[target="_blank"]',

            paymentRow:
                "[data-payment-row]",

            reference:
                "[data-payment-reference]"
        },

        animation: {

            copyFeedback:
                1500
        }
    };

    /* ========================================================
       ÉTAT
       ======================================================== */

    const state = {

        initialized:
            false,

        copiedElement:
            null
    };

    /* ========================================================
       DOM
       ======================================================== */

    const dom = {

        root:
            null,

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

        number(value) {

            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return 0;
            }

            const normalized =
                String(value)
                    .trim()
                    .replace(
                        /\s/g,
                        ""
                    )
                    .replace(
                        ",",
                        "."
                    );

            const result =
                Number.parseFloat(
                    normalized
                );

            return Number.isFinite(
                result
            )
                ? result
                : 0;
        },

        clamp(
            value,
            min = 0,
            max = 100
        ) {

            return Math.min(
                Math.max(
                    value,
                    min
                ),
                max
            );
        },

        isInput(element) {

            if (!element) {
                return false;
            }

            return [
                "INPUT",
                "TEXTAREA",
                "SELECT"
            ].includes(
                element.tagName
            );
        }
    };

    /* ========================================================
       PROGRESSION
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

            const value =
                utils.number(
                    element.dataset.progress ??
                    element.getAttribute(
                        "aria-valuenow"
                    ) ??
                    element.textContent
                );

            const percentage =
                utils.clamp(
                    value
                );

            /*
             * Le style width est volontairement
             * généré par JS afin d'éviter
             * les styles inline dans Blade.
             */
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
                String(
                    percentage
                )
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

            document.addEventListener(
                "keydown",
                (event) => {

                    if (
                        event.ctrlKey &&
                        event.key.toLowerCase() ===
                        "p"
                    ) {

                        event.preventDefault();

                        this.print();
                    }
                }
            );
        },

        print(button = null) {

            const url =
                button?.dataset.printUrl;

            if (url) {

                window.open(
                    url,
                    "_blank",
                    "noopener,noreferrer"
                );

                return;
            }

            document.body.classList.add(
                "is-printing"
            );

            window.setTimeout(
                () => {

                    window.print();

                },
                50
            );
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
                element.dataset.value ??
                element.textContent.trim();

            if (!value) {
                return;
            }

            try {

                if (
                    navigator.clipboard &&
                    window.isSecureContext
                ) {

                    await navigator.clipboard.writeText(
                        value
                    );

                } else {

                    this.fallback(
                        value
                    );
                }

                this.feedback(
                    element
                );

            } catch (error) {

                console.error(
                    "Erreur de copie :",
                    error
                );
            }
        },

        fallback(value) {

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

            textarea.style.left =
                "-9999px";

            document.body.appendChild(
                textarea
            );

            textarea.select();

            document.execCommand(
                "copy"
            );

            textarea.remove();
        },

        feedback(element) {

            const original =
                element.dataset.originalLabel ??
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
                CONFIG.animation.copyFeedback
            );
        }
    };

    /* ========================================================
       ACTIONS SENSIBLES
       ======================================================== */

    const actions = {

        init() {

            this.bindSensitive();

            this.bindConfirm();

            this.bindPayment();

            this.bindBack();
        },

        bindSensitive() {

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

                            const message =
                                element.dataset.confirmMessage ||
                                "Confirmez-vous cette opération ?";

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

        bindConfirm() {

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
                                "Voulez-vous continuer ?";

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

        bindPayment() {

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

                            const message =
                                element.dataset.confirmMessage ||
                                "Confirmez-vous cette opération de paiement ?";

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

            element.classList.add(
                "is-loading"
            );

            if (
                "disabled" in element
            ) {

                element.disabled =
                    true;
            }

            element.setAttribute(
                "aria-busy",
                "true"
            );
        },

        bindBack() {

            const buttons =
                dom.findAll(
                    CONFIG.selectors.back,
                    dom.root
                );

            buttons.forEach(
                (button) => {

                    button.addEventListener(
                        "click",
                        (event) => {

                            const fallback =
                                button.dataset.back;

                            if (
                                !fallback
                            ) {
                                return;
                            }

                            event.preventDefault();

                            window.location.href =
                                fallback;
                        }
                    );
                }
            );
        }
    };

    /* ========================================================
       TABLEAU DES PAIEMENTS
       ======================================================== */

    const paymentTable = {

        init() {

            const rows =
                dom.findAll(
                    CONFIG.selectors.paymentRow,
                    dom.root
                );

            rows.forEach(
                (row, index) => {

                    row.setAttribute(
                        "data-row-index",
                        String(
                            index + 1
                        )
                    );

                    this.bind(
                        row
                    );
                }
            );
        },

        bind(row) {

            row.addEventListener(
                "keydown",
                (event) => {

                    if (
                        event.key !==
                        "Enter"
                    ) {
                        return;
                    }

                    const link =
                        row.querySelector(
                            "a[href]"
                        );

                    if (link) {
                        link.click();
                    }
                }
            );
        }
    };

    /* ========================================================
       LOGO / IMAGES
       ======================================================== */

    const images = {

        init() {

            const logos =
                dom.findAll(
                    CONFIG.selectors.logo,
                    dom.root
                );

            logos.forEach(
                (logo) => {

                    logo.addEventListener(
                        "error",
                        () => {

                            logo.classList.add(
                                "is-broken"
                            );

                            logo.setAttribute(
                                "aria-hidden",
                                "true"
                            );
                        },
                        {
                            once: true
                        }
                    );
                }
            );
        }
    };

    /* ========================================================
       ACCESSIBILITÉ
       ======================================================== */

    const accessibility = {

        init() {

            this.inputs();

            this.links();

            this.buttons();

            this.reference();
        },

        inputs() {

            const fields =
                dom.findAll(
                    "input, select, textarea",
                    dom.root
                );

            fields.forEach(
                (field) => {

                    if (
                        field.required
                    ) {

                        field.setAttribute(
                            "aria-required",
                            "true"
                        );
                    }
                }
            );
        },

        links() {

            const links =
                dom.findAll(
                    CONFIG.selectors.externalLinks,
                    dom.root
                );

            links.forEach(
                (link) => {

                    link.setAttribute(
                        "rel",
                        "noopener noreferrer"
                    );
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

                    /*
                     * Ne pas modifier le type
                     * des boutons présents dans
                     * un formulaire.
                     */
                    if (
                        button.closest("form")
                    ) {
                        return;
                    }

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
        },

        reference() {

            const reference =
                dom.find(
                    CONFIG.selectors.reference,
                    dom.root
                );

            if (
                reference &&
                !reference.getAttribute(
                    "aria-label"
                )
            ) {

                reference.setAttribute(
                    "aria-label",
                    "Référence du paiement"
                );
            }
        }
    };

    /* ========================================================
       IMPRESSION
       ======================================================== */

    window.addEventListener(
        "afterprint",
        () => {

            document.body.classList.remove(
                "is-printing"
            );
        }
    );

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

        actions.init();

        paymentTable.init();

        images.init();

        accessibility.init();

        state.initialized =
            true;

        dom.root.dataset.jsReady =
            "true";
    }

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

    window.PHOVE.salaryPaymentShow = {

        print() {

            printing.print();
        },

        refreshProgress() {

            progress.init();
        }
    };

})();