/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : PAIEMENTS DES SALAIRES
 * VUE    : RECU
 * FICHIER: recu.js
 * ============================================================
 *
 * Gestion du reçu officiel d'un paiement de salaire.
 *
 * Responsabilités :
 * - Impression du reçu
 * - Préparation avant impression
 * - Gestion des barres de progression
 * - Copie des références
 * - Gestion du logo
 * - Gestion de la navigation
 * - Accessibilité
 * - Rendu propre avant impression
 *
 * IMPORTANT :
 * Le JavaScript ne modifie aucune donnée financière
 * provenant du serveur.
 *
 * Laravel reste responsable de :
 * - l'autorisation
 * - la validation
 * - les calculs
 * - l'intégrité des données
 * - la sécurité
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
            root: ".salary-payment-receipt",

            print: "[data-print]",

            progress: "[data-progress]",

            copy: "[data-copy]",

            logo: "img[data-logo]",

            back: "[data-back]",

            externalLinks: 'a[target="_blank"]',

            document: "[data-receipt-document]",

            paymentReference: "[data-payment-reference]",

            printOnly: "[data-print-only]",

            noPrint: "[data-no-print]"
        },

        copyFeedbackDuration: 1500,

        printDelay: 100
    };

    /* ========================================================
       ÉTAT DE L'APPLICATION
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

        find(selector, context = document) {
            return context.querySelector(selector);
        },

        findAll(selector, context = document) {
            return Array.from(
                context.querySelectorAll(selector)
            );
        }
    };

    /* ========================================================
       UTILITAIRES
       ======================================================== */

    const utils = {
        /**
         * Convertit une valeur en nombre.
         *
         * Supporte notamment :
         * 1000
         * 1000.50
         * 1000,50
         * "1 000,50"
         */
        number(value) {
            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return 0;
            }

            let normalized = String(value).trim();

            normalized = normalized.replace(/\s/g, "");

            /*
             * Si la valeur contient à la fois
             * une virgule et un point, on détermine
             * quel séparateur représente les décimales.
             */
            if (
                normalized.includes(",") &&
                normalized.includes(".")
            ) {
                const commaPosition =
                    normalized.lastIndexOf(",");

                const dotPosition =
                    normalized.lastIndexOf(".");

                if (commaPosition > dotPosition) {
                    normalized = normalized
                        .replace(/\./g, "")
                        .replace(",", ".");
                } else {
                    normalized = normalized.replace(/,/g, "");
                }
            } else if (normalized.includes(",")) {
                normalized = normalized.replace(",", ".");
            }

            const result = Number.parseFloat(normalized);

            return Number.isFinite(result) ? result : 0;
        },

        /**
         * Limite une valeur entre min et max.
         */
        clamp(value, min = 0, max = 100) {
            return Math.min(
                Math.max(value, min),
                max
            );
        },

        /**
         * Attend un nombre de millisecondes.
         */
        sleep(milliseconds) {
            return new Promise((resolve) => {
                window.setTimeout(
                    resolve,
                    milliseconds
                );
            });
        }
    };

    /* ========================================================
       BARRES DE PROGRESSION
       ======================================================== */

    const progress = {
        /**
         * Initialise toutes les progressions
         * présentes sur le reçu.
         */
        init() {
            const elements = dom.findAll(
                CONFIG.selectors.progress,
                dom.root
            );

            elements.forEach((element) => {
                this.render(element);
            });
        },

        /**
         * Rend une barre de progression.
         */
        render(element) {
            const rawValue =
                element.dataset.progress ??
                element.getAttribute("aria-valuenow") ??
                "0";

            const value = utils.number(rawValue);

            const percentage = utils.clamp(
                value,
                0,
                100
            );

            /*
             * La largeur dynamique est appliquée
             * par JavaScript afin d'éviter le CSS
             * inline dans la Blade.
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
                String(percentage)
            );
        }
    };

    /* ========================================================
       IMPRESSION
       ======================================================== */

    const printing = {
        /**
         * Initialise les boutons d'impression.
         */
        init() {
            const buttons = dom.findAll(
                CONFIG.selectors.print,
                dom.root
            );

            buttons.forEach((button) => {
                button.addEventListener(
                    "click",
                    async (event) => {
                        event.preventDefault();

                        await this.print(button);
                    }
                );
            });

            /*
             * Raccourci Ctrl + P.
             */
            document.addEventListener(
                "keydown",
                async (event) => {
                    if (
                        event.ctrlKey &&
                        event.key.toLowerCase() === "p"
                    ) {
                        event.preventDefault();

                        await this.print();
                    }
                }
            );
        },

        /**
         * Lance l'impression du reçu.
         */
        async print(button = null) {
            /*
             * Empêche plusieurs impressions
             * simultanées.
             */
            if (state.printing) {
                return;
            }

            state.printing = true;

            /*
             * Si une URL d'impression spécifique
             * est fournie par Laravel.
             */
            const printUrl =
                button?.dataset?.printUrl;

            if (printUrl) {
                state.printing = false;

                window.open(
                    printUrl,
                    "_blank",
                    "noopener,noreferrer"
                );

                return;
            }

            /*
             * Préparation du document.
             */
            document.body.classList.add(
                "is-printing"
            );

            if (dom.root) {
                dom.root.classList.add(
                    "is-printing"
                );
            }

            /*
             * Attendre que les images soient
             * complètement chargées.
             */
            await this.waitForImages();

            /*
             * Laisser le navigateur finaliser
             * le rendu du document.
             */
            await utils.sleep(
                CONFIG.printDelay
            );

            window.print();

            /*
             * Nettoyage de secours.
             */
            window.setTimeout(
                () => {
                    this.cleanup();
                },
                500
            );
        },

        /**
         * Supprime les états temporaires
         * liés à l'impression.
         */
        cleanup() {
            document.body.classList.remove(
                "is-printing"
            );

            if (dom.root) {
                dom.root.classList.remove(
                    "is-printing"
                );
            }

            state.printing = false;
        },

        /**
         * Attend le chargement des images.
         */
        async waitForImages() {
            const images = dom.findAll(
                "img",
                dom.root
            );

            const pendingImages = images
                .filter((image) => {
                    return !image.complete;
                })
                .map((image) => {
                    return new Promise((resolve) => {
                        const resolveImage = () => {
                            resolve();
                        };

                        image.addEventListener(
                            "load",
                            resolveImage,
                            {
                                once: true
                            }
                        );

                        image.addEventListener(
                            "error",
                            resolveImage,
                            {
                                once: true
                            }
                        );
                    });
                });

            if (pendingImages.length === 0) {
                return;
            }

            await Promise.all(
                pendingImages
            );
        }
    };

    /* ========================================================
       ÉVÉNEMENTS NATIFS D'IMPRESSION
       ======================================================== */

    window.addEventListener(
        "beforeprint",
        () => {
            if (!dom.root) {
                return;
            }

            document.body.classList.add(
                "is-printing"
            );

            dom.root.classList.add(
                "is-printing"
            );
        }
    );

    window.addEventListener(
        "afterprint",
        () => {
            document.body.classList.remove(
                "is-printing"
            );

            if (dom.root) {
                dom.root.classList.remove(
                    "is-printing"
                );
            }

            state.printing = false;
        }
    );

    /* ========================================================
       PRESSE-PAPIER
       ======================================================== */

    const clipboard = {
        /**
         * Initialise les boutons de copie.
         */
        init() {
            const elements = dom.findAll(
                CONFIG.selectors.copy,
                dom.root
            );

            elements.forEach((element) => {
                element.addEventListener(
                    "click",
                    async () => {
                        await this.copy(element);
                    }
                );
            });
        },

        /**
         * Copie une valeur.
         */
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
                    this.fallbackCopy(value);
                }

                this.feedback(element);
            } catch (error) {
                console.error(
                    "PHOVE : impossible de copier la référence.",
                    error
                );
            }
        },

        /**
         * Méthode de secours pour les anciens
         * navigateurs ou contextes non sécurisés.
         */
        fallbackCopy(value) {
            const textarea =
                document.createElement(
                    "textarea"
                );

            textarea.value = value;

            textarea.setAttribute(
                "readonly",
                ""
            );

            textarea.style.position =
                "fixed";

            textarea.style.left =
                "-9999px";

            textarea.style.top =
                "0";

            textarea.style.opacity =
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

        /**
         * Feedback visuel après copie.
         */
        feedback(element) {
            const originalLabel =
                element.dataset.originalLabel ??
                element.textContent;

            if (
                !element.dataset.originalLabel
            ) {
                element.dataset.originalLabel =
                    originalLabel;
            }

            element.textContent =
                "Copié";

            element.classList.add(
                "is-copied"
            );

            window.setTimeout(
                () => {
                    element.textContent =
                        originalLabel;

                    element.classList.remove(
                        "is-copied"
                    );
                },
                CONFIG.copyFeedbackDuration
            );
        }
    };

    /* ========================================================
       GESTION DES IMAGES
       ======================================================== */

    const images = {
        /**
         * Initialise les logos et images.
         */
        init() {
            const logos = dom.findAll(
                CONFIG.selectors.logo,
                dom.root
            );

            logos.forEach((logo) => {
                logo.addEventListener(
                    "error",
                    () => {
                        this.handleError(
                            logo
                        );
                    },
                    {
                        once: true
                    }
                );
            });
        },

        /**
         * Gestion d'une image indisponible.
         */
        handleError(image) {
            image.classList.add(
                "is-broken"
            );

            image.setAttribute(
                "aria-hidden",
                "true"
            );

            image.setAttribute(
                "data-image-error",
                "true"
            );
        }
    };

    /* ========================================================
       NAVIGATION
       ======================================================== */

    const navigation = {
        init() {
            const buttons = dom.findAll(
                CONFIG.selectors.back,
                dom.root
            );

            buttons.forEach((button) => {
                button.addEventListener(
                    "click",
                    (event) => {
                        const url =
                            button.dataset.back;

                        if (!url) {
                            return;
                        }

                        event.preventDefault();

                        window.location.href =
                            url;
                    }
                );
            });
        }
    };

    /* ========================================================
       ACCESSIBILITÉ
       ======================================================== */

    const accessibility = {
        init() {
            this.buttons();

            this.links();

            this.document();

            this.reference();
        },

        /**
         * Sécurise les boutons qui ne sont
         * pas contenus dans un formulaire.
         */
        buttons() {
            const buttons = dom.findAll(
                "button",
                dom.root
            );

            buttons.forEach((button) => {
                /*
                 * Ne jamais modifier le type
                 * d'un bouton appartenant à
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
            });
        },

        /**
         * Sécurise les liens externes.
         */
        links() {
            const links = dom.findAll(
                CONFIG.selectors.externalLinks,
                dom.root
            );

            links.forEach((link) => {
                const currentRel =
                    link.getAttribute("rel") || "";

                const relValues =
                    new Set(
                        currentRel
                            .split(/\s+/)
                            .filter(Boolean)
                    );

                relValues.add(
                    "noopener"
                );

                relValues.add(
                    "noreferrer"
                );

                link.setAttribute(
                    "rel",
                    Array.from(
                        relValues
                    ).join(" ")
                );
            });
        },

        /**
         * Identifie le reçu comme document.
         */
        document() {
            const receiptDocument =
                dom.find(
                    CONFIG.selectors.document,
                    dom.root
                );

            if (!receiptDocument) {
                return;
            }

            if (
                !receiptDocument.getAttribute(
                    "role"
                )
            ) {
                receiptDocument.setAttribute(
                    "role",
                    "document"
                );
            }

            if (
                !receiptDocument.getAttribute(
                    "aria-label"
                )
            ) {
                receiptDocument.setAttribute(
                    "aria-label",
                    "Reçu de paiement de salaire"
                );
            }
        },

        /**
         * Rend la référence du paiement
         * accessible.
         */
        reference() {
            const reference =
                dom.find(
                    CONFIG.selectors.paymentReference,
                    dom.root
                );

            if (
                !reference
            ) {
                return;
            }

            if (
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
       INITIALISATION
       ======================================================== */

    function init() {
        if (state.initialized) {
            return;
        }

        dom.root = dom.find(
            CONFIG.selectors.root
        );

        /*
         * Le fichier est chargé uniquement
         * sur cette page.
         */
        if (!dom.root) {
            return;
        }

        progress.init();

        printing.init();

        clipboard.init();

        images.init();

        navigation.init();

        accessibility.init();

        state.initialized = true;

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
       API PUBLIQUE PHOVE
       ======================================================== */

    window.PHOVE =
        window.PHOVE || {};

    window.PHOVE.salaryPaymentReceipt = {
        /**
         * Lance l'impression.
         */
        print() {
            return printing.print();
        },

        /**
         * Recalcule les progressions.
         */
        refreshProgress() {
            progress.init();
        },

        /**
         * Indique si une impression
         * est actuellement en cours.
         */
        isPrinting() {
            return state.printing;
        },

        /**
         * Vérifie si le module est initialisé.
         */
        isInitialized() {
            return state.initialized;
        }
    };

})();