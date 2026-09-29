/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : BULLETIN DE SALAIRE
 * FICHIER: bulletin.js
 * ============================================================
 *
 * Responsabilités :
 * - Préparation de l'impression
 * - Impression du bulletin
 * - Gestion des barres de progression
 * - Copie de référence
 * - Gestion de l'affichage d'impression
 * - Détection de chargement des images
 * - Accessibilité
 * - Raccourcis clavier
 *
 * Le bulletin est un document de consultation.
 *
 * Aucune modification métier n'est effectuée ici.
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
                ".salary-bulletin",

            print:
                "[data-print]",

            progress:
                "[data-progress]",

            copy:
                "[data-copy]",

            back:
                "[data-back]",

            logo:
                ".salary-bulletin-logo img",

            document:
                "[data-bulletin-document]",

            printArea:
                "[data-print-area]",

            noPrint:
                ".no-print",

            paymentRow:
                "[data-payment-row]",

            total:
                "[data-bulletin-total]",

            status:
                "[data-bulletin-status]"
        },

        progress: {
            min: 0,
            max: 100
        },

        print: {
            delay: 100
        }
    };

    /* ========================================================
       ÉTAT
       ======================================================== */

    const state = {

        initialized: false,

        printing: false
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
                    .replace(",", ".")
                    .replace(
                        /[^\d.-]/g,
                        ""
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

        sleep(milliseconds) {

            return new Promise(
                (resolve) => {

                    window.setTimeout(
                        resolve,
                        milliseconds
                    );
                }
            );
        }
    };

    /* ========================================================
       PROGRESSION DE PAIEMENT
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
                    utils.number(raw),
                    CONFIG.progress.min,
                    CONFIG.progress.max
                );

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

            /*
             * Texte accessible pour lecteur
             * d'écran lorsque nécessaire.
             */
            if (
                !element.getAttribute(
                    "aria-label"
                )
            ) {

                element.setAttribute(
                    "aria-label",
                    `Paiement effectué à ${value}%`
                );
            }
        }
    };

    /* ========================================================
       IMPRESSION
       ======================================================== */

    const printer = {

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

                            this.print();
                        }
                    );
                }
            );

            /*
             * Ctrl + P
             */
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

        async print() {

            if (
                state.printing
            ) {
                return;
            }

            state.printing =
                true;

            const root =
                dom.root;

            if (!root) {
                state.printing =
                    false;

                return;
            }

            root.classList.add(
                "is-printing"
            );

            /*
             * Préparer le document.
             */
            document.body.classList.add(
                "printing-salary-bulletin"
            );

            /*
             * S'assurer que les images
             * importantes sont chargées.
             */
            await this.waitForImages();

            /*
             * Laisser le navigateur appliquer
             * les styles d'impression.
             */
            await utils.sleep(
                CONFIG.print.delay
            );

            window.print();

            /*
             * Le navigateur déclenche
             * beforeprint / afterprint.
             * On conserve également
             * une sécurité de nettoyage.
             */
            window.setTimeout(
                () => {
                    this.cleanup();
                },
                1000
            );
        },

        cleanup() {

            state.printing =
                false;

            if (dom.root) {

                dom.root.classList.remove(
                    "is-printing"
                );
            }

            document.body.classList.remove(
                "printing-salary-bulletin"
            );
        },

        waitForImages() {

            const images =
                dom.findAll(
                    "img",
                    dom.root
                );

            if (!images.length) {
                return Promise.resolve();
            }

            const pending =
                images
                    .filter(
                        (image) =>
                            !image.complete
                    )
                    .map(
                        (image) =>
                            new Promise(
                                (resolve) => {

                                    const done =
                                        () => {
                                            resolve();
                                        };

                                    image.addEventListener(
                                        "load",
                                        done,
                                        {
                                            once: true
                                        }
                                    );

                                    image.addEventListener(
                                        "error",
                                        done,
                                        {
                                            once: true
                                        }
                                    );
                                }
                            )
                    );

            return Promise.all(
                pending
            );
        }
    };

    /* ========================================================
       ÉVÉNEMENTS D'IMPRESSION NATIFS
       ======================================================== */

    const printEvents = {

        init() {

            window.addEventListener(
                "beforeprint",
                () => {

                    state.printing =
                        true;

                    document.body.classList.add(
                        "printing-salary-bulletin"
                    );

                    dom.root?.classList.add(
                        "is-printing"
                    );
                }
            );

            window.addEventListener(
                "afterprint",
                () => {

                    printer.cleanup();
                }
            );
        }
    };

    /* ========================================================
       COPIE DE RÉFÉRENCE
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
                        async () => {

                            await this.copy(
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

            } catch (
                error
            ) {

                console.error(
                    "Erreur de copie.",
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

            textarea.style.position =
                "fixed";

            textarea.style.left =
                "-9999px";

            textarea.style.top =
                "0";

            document.body.appendChild(
                textarea
            );

            textarea.focus();

            textarea.select();

            document.execCommand(
                "copy"
            );

            textarea.remove();
        },

        feedback(element) {

            const original =
                element.dataset.originalText ||
                element.textContent;

            if (
                !element.dataset.originalText
            ) {

                element.dataset.originalText =
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
       LOGO
       ======================================================== */

    const logo = {

        init() {

            const image =
                dom.find(
                    CONFIG.selectors.logo,
                    dom.root
                );

            if (!image) {
                return;
            }

            image.addEventListener(
                "error",
                () => {

                    this.handleError(
                        image
                    );
                }
            );

            if (
                image.complete &&
                image.naturalWidth === 0
            ) {

                this.handleError(
                    image
                );
            }
        },

        handleError(image) {

            image.classList.add(
                "is-unavailable"
            );

            image.setAttribute(
                "aria-hidden",
                "true"
            );

            /*
             * Le conteneur peut afficher
             * automatiquement le fallback
             * prévu par le CSS.
             */
            image.parentElement?.classList.add(
                "has-logo-error"
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

            if (!rows.length) {
                return;
            }

            rows.forEach(
                (row, index) => {

                    row.dataset.rowNumber =
                        String(
                            index + 1
                        );

                    this.accessibility(
                        row
                    );
                }
            );
        },

        accessibility(row) {

            const links =
                row.querySelectorAll(
                    "a"
                );

            links.forEach(
                (link) => {

                    if (
                        !link.getAttribute(
                            "aria-label"
                        )
                    ) {

                        const reference =
                            row.dataset.reference ||
                            "";

                        link.setAttribute(
                            "aria-label",
                            reference
                                ? `Consulter le paiement ${reference}`
                                : "Consulter le paiement"
                        );
                    }
                }
            );
        }
    };

    /* ========================================================
       BOUTON RETOUR
       ======================================================== */

    const back = {

        init() {

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

                            /*
                             * Si un href est fourni,
                             * on laisse le navigateur
                             * gérer la navigation.
                             */

                            if (
                                button.hasAttribute(
                                    "href"
                                )
                            ) {
                                return;
                            }

                            event.preventDefault();

                            if (
                                window.history.length >
                                1
                            ) {

                                window.history.back();

                            } else {

                                window.location.href =
                                    "/";
                            }
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

            this.buttons();

            this.externalLinks();

            this.document();
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

                    const values =
                        new Set(
                            rel
                                .split(
                                    /\s+/
                                )
                                .filter(
                                    Boolean
                                )
                        );

                    values.add(
                        "noopener"
                    );

                    values.add(
                        "noreferrer"
                    );

                    link.setAttribute(
                        "rel",
                        Array.from(
                            values
                        ).join(" ")
                    );
                }
            );
        },

        document() {

            const documentElement =
                dom.find(
                    CONFIG.selectors.document,
                    dom.root
                );

            if (!documentElement) {
                return;
            }

            if (
                !documentElement.getAttribute(
                    "role"
                )
            ) {

                documentElement.setAttribute(
                    "role",
                    "document"
                );
            }

            if (
                !documentElement.getAttribute(
                    "aria-label"
                )
            ) {

                documentElement.setAttribute(
                    "aria-label",
                    "Bulletin de salaire"
                );
            }
        }
    };

    /* ========================================================
       DÉTECTION DU MODE IMPRESSION
       ======================================================== */

    const media = {

        init() {

            if (
                typeof window.matchMedia !==
                "function"
            ) {
                return;
            }

            const query =
                window.matchMedia(
                    "print"
                );

            const handler =
                (event) => {

                    if (event.matches) {

                        document.body.classList.add(
                            "printing-salary-bulletin"
                        );

                        dom.root?.classList.add(
                            "is-printing"
                        );

                    } else {

                        printer.cleanup();
                    }
                };

            if (
                typeof query.addEventListener ===
                "function"
            ) {

                query.addEventListener(
                    "change",
                    handler
                );

            } else if (
                typeof query.addListener ===
                "function"
            ) {

                query.addListener(
                    handler
                );
            }
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

        printer.init();

        printEvents.init();

        clipboard.init();

        logo.init();

        paymentTable.init();

        back.init();

        accessibility.init();

        media.init();

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

    window.PHOVE.salaryBulletin = {

        print() {
            printer.print();
        },

        refreshProgress() {
            progress.init();
        },

        isPrinting() {
            return state.printing;
        }
    };

})();