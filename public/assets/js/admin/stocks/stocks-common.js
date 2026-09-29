/**
 * ============================================================================
 * PHOVE — MODULE STOCKS
 * JavaScript commun
 * ============================================================================
 *
 * Fichier :
 * public/assets/js/admin/stocks/stocks-common.js
 *
 * Rôle :
 * - fonctionnalités JavaScript partagées par les pages Stocks ;
 * - confirmation des actions sensibles ;
 * - fermeture des alertes ;
 * - gestion des boutons de soumission ;
 * - formatage des nombres ;
 * - formatage des montants ;
 * - recherche locale générique ;
 * - gestion des éléments nécessitant un tooltip ;
 * - prévention des doubles soumissions ;
 * - helpers réutilisables par les fichiers JS spécifiques.
 *
 * IMPORTANT :
 * Ce fichier ne contient PAS la logique métier spécifique :
 * - des entrées ;
 * - des sorties ;
 * - des réservations ;
 * - des lots ;
 * - des inventaires ;
 * - des mouvements.
 *
 * Ces comportements restent dans leurs fichiers JS respectifs.
 * ============================================================================
 */

(function () {
    'use strict';


    /* =========================================================================
       1. NAMESPACE GLOBAL
       ========================================================================= */

    window.PhoveStocks = window.PhoveStocks || {};


    /* =========================================================================
       2. CONFIGURATION COMMUNE
       ========================================================================= */

    const StocksConfig = {

        selectors: {

            alert: '[data-stocks-alert]',

            alertClose: '[data-stocks-alert-close]',

            confirm: '[data-stocks-confirm]',

            submitButton:
                'button[type="submit"], input[type="submit"]',

            localSearch:
                '[data-stocks-local-search]',

            searchTarget:
                '[data-stocks-search-target]',

            tooltip:
                '[data-stocks-tooltip]',

        },

        classes: {

            hidden: 'stocks-hidden',

            loading: 'is-loading',

        },

        messages: {

            confirm:
                'Êtes-vous sûr de vouloir effectuer cette action ?',

            processing:
                'Traitement en cours…',

        },

    };


    /* =========================================================================
       3. HELPERS DOM
       ========================================================================= */

    const Dom = {

        /**
         * Sélectionne un élément.
         */
        one(selector, parent = document) {
            return parent.querySelector(selector);
        },


        /**
         * Sélectionne plusieurs éléments.
         */
        all(selector, parent = document) {
            return Array.from(
                parent.querySelectorAll(selector)
            );
        },


        /**
         * Ajoute une classe.
         */
        addClass(element, className) {

            if (!element) {
                return;
            }

            element.classList.add(className);

        },


        /**
         * Supprime une classe.
         */
        removeClass(element, className) {

            if (!element) {
                return;
            }

            element.classList.remove(className);

        },


        /**
         * Bascule une classe.
         */
        toggleClass(element, className, force) {

            if (!element) {
                return;
            }

            element.classList.toggle(
                className,
                force
            );

        },


        /**
         * Vérifie si un élément possède une classe.
         */
        hasClass(element, className) {

            return !!element
                && element.classList.contains(className);

        },

    };


    /* =========================================================================
       4. NOTIFICATIONS / ALERTES
       ========================================================================= */

    const Alerts = {

        /**
         * Ferme une alerte.
         */
        close(alert) {

            if (!alert) {
                return;
            }

            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-4px)';

            window.setTimeout(function () {

                if (alert && alert.parentNode) {
                    alert.remove();
                }

            }, 200);

        },


        /**
         * Initialise les boutons de fermeture.
         */
        init() {

            Dom.all(
                StocksConfig.selectors.alertClose
            ).forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const alert =
                            button.closest(
                                StocksConfig.selectors.alert
                            );

                        Alerts.close(alert);

                    }
                );

            });

        },

    };


    /* =========================================================================
       5. CONFIRMATION DES ACTIONS
       ========================================================================= */

    const Confirmations = {

        /**
         * Initialise les confirmations.
         *
         * Exemple Blade :
         *
         * <form
         *     method="POST"
         *     data-stocks-confirm="Voulez-vous supprimer ce stock ?"
         * >
         */
        init() {

            Dom.all(
                StocksConfig.selectors.confirm
            ).forEach(function (element) {

                element.addEventListener(
                    'click',
                    function (event) {

                        const message =
                            element.dataset.stocksConfirm
                            || StocksConfig.messages.confirm;

                        if (!window.confirm(message)) {

                            event.preventDefault();

                        }

                    }
                );

            });

        },

    };


    /* =========================================================================
       6. PROTECTION CONTRE LES DOUBLES SOUMISSIONS
       ========================================================================= */

    const FormProtection = {

        /**
         * Initialise la protection des formulaires.
         */
        init() {

            Dom.all('form').forEach(function (form) {

                /*
                 * Les formulaires peuvent explicitement désactiver
                 * cette fonctionnalité.
                 */
                if (
                    form.hasAttribute(
                        'data-stocks-no-submit-lock'
                    )
                ) {
                    return;
                }


                form.addEventListener(
                    'submit',
                    function (event) {

                        /*
                         * Si le formulaire a déjà été soumis,
                         * on bloque la deuxième soumission.
                         */
                        if (
                            form.dataset.stocksSubmitted
                            === 'true'
                        ) {

                            event.preventDefault();

                            return;

                        }


                        form.dataset.stocksSubmitted = 'true';


                        /*
                         * Désactivation des boutons submit.
                         */
                        const buttons =
                            form.querySelectorAll(
                                StocksConfig.selectors.submitButton
                            );


                        buttons.forEach(function (button) {

                            /*
                             * On conserve le texte original.
                             */
                            if (
                                !button.dataset.originalText
                            ) {

                                button.dataset.originalText =
                                    button.innerHTML;

                            }


                            button.disabled = true;

                            button.classList.add(
                                StocksConfig.classes.loading
                            );


                            /*
                             * On ne modifie pas les boutons qui
                             * portent explicitement data-no-loading.
                             */
                            if (
                                !button.hasAttribute(
                                    'data-stocks-no-loading'
                                )
                            ) {

                                const loadingText =
                                    button.dataset.loadingText
                                    || StocksConfig.messages.processing;


                                if (
                                    button.tagName
                                    === 'BUTTON'
                                ) {

                                    button.innerHTML =
                                        '<i class="fas fa-spinner fa-spin"></i> '
                                        + loadingText;

                                } else {

                                    button.value =
                                        loadingText;

                                }

                            }

                        });

                    }
                );

            });

        },

    };


    /* =========================================================================
       7. FORMATAGE DES NOMBRES
       ========================================================================= */

    const Numbers = {

        /**
         * Convertit une valeur en nombre sûr.
         */
        toNumber(value, fallback = 0) {

            if (
                value === null
                || value === undefined
                || value === ''
            ) {
                return fallback;
            }


            const number = Number(value);


            return Number.isFinite(number)
                ? number
                : fallback;

        },


        /**
         * Formate un nombre selon la locale française.
         */
        format(value, decimals = 2) {

            const number =
                Numbers.toNumber(value);


            return new Intl.NumberFormat(
                'fr-FR',
                {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                }
            ).format(number);

        },


        /**
         * Formate une quantité.
         *
         * Exemple :
         * 1500 -> 1 500
         */
        formatQuantity(value) {

            const number =
                Numbers.toNumber(value);


            return new Intl.NumberFormat(
                'fr-FR',
                {
                    maximumFractionDigits: 3,
                }
            ).format(number);

        },


        /**
         * Formate un montant.
         *
         * Exemple :
         * 1500.50 -> 1 500,50
         */
        formatAmount(value, currency = '') {

            const formatted =
                Numbers.format(value, 2);


            if (!currency) {
                return formatted;
            }


            return `${formatted} ${currency}`;

        },

    };


    /* =========================================================================
       8. FORMATAGE DES DATES
       ========================================================================= */

    const Dates = {

        /**
         * Transforme YYYY-MM-DD en DD/MM/YYYY.
         */
        formatDate(value) {

            if (!value) {
                return '';
            }


            const parts =
                String(value).split('-');


            if (parts.length !== 3) {
                return value;
            }


            return `${parts[2]}/${parts[1]}/${parts[0]}`;

        },


        /**
         * Transforme une date ISO en date française.
         */
        formatDateTime(value) {

            if (!value) {
                return '';
            }


            const date =
                new Date(value);


            if (Number.isNaN(date.getTime())) {
                return value;
            }


            return new Intl.DateTimeFormat(
                'fr-FR',
                {
                    dateStyle: 'short',
                    timeStyle: 'short',
                }
            ).format(date);

        },

    };


    /* =========================================================================
       9. RECHERCHE LOCALE
       ========================================================================= */

    const LocalSearch = {

        /**
         * Initialise les recherches locales.
         *
         * Exemple :
         *
         * <input
         *     type="search"
         *     data-stocks-local-search
         * >
         *
         * <tr data-stocks-search-target>
         *     ...
         * </tr>
         */
        init() {

            Dom.all(
                StocksConfig.selectors.localSearch
            ).forEach(function (input) {

                input.addEventListener(
                    'input',
                    function () {

                        const query =
                            input.value
                                .trim()
                                .toLowerCase();


                        const container =
                            input.closest(
                                '[data-stocks-search-container]'
                            );


                        if (!container) {
                            return;
                        }


                        const targets =
                            Dom.all(
                                StocksConfig.selectors.searchTarget,
                                container
                            );


                        targets.forEach(function (target) {

                            const text =
                                target.textContent
                                    .trim()
                                    .toLowerCase();


                            const visible =
                                query === ''
                                || text.includes(query);


                            target.hidden =
                                !visible;

                        });

                    }
                );

            });

        },

    };


    /* =========================================================================
       10. AUTO-DISMISS DES ALERTES
       ========================================================================= */

    const AutoDismiss = {

        /**
         * Ferme automatiquement les alertes configurées.
         *
         * Exemple :
         *
         * <div
         *     data-stocks-alert
         *     data-stocks-alert-timeout="5000"
         * >
         */
        init() {

            Dom.all(
                StocksConfig.selectors.alert
            ).forEach(function (alert) {

                const timeout =
                    parseInt(
                        alert.dataset.stocksAlertTimeout
                        || '0',
                        10
                    );


                if (
                    !Number.isFinite(timeout)
                    || timeout <= 0
                ) {
                    return;
                }


                window.setTimeout(
                    function () {

                        Alerts.close(alert);

                    },
                    timeout
                );

            });

        },

    };


    /* =========================================================================
       11. TOOLTIPS NATIFS
       ========================================================================= */

    const Tooltips = {

        /**
         * Ajoute un title aux éléments qui le demandent.
         *
         * Exemple :
         *
         * <button
         *     data-stocks-tooltip="Modifier"
         * >
         */
        init() {

            Dom.all(
                StocksConfig.selectors.tooltip
            ).forEach(function (element) {

                const text =
                    element.dataset.stocksTooltip;


                if (
                    text
                    && !element.hasAttribute('title')
                ) {

                    element.setAttribute(
                        'title',
                        text
                    );

                }

            });

        },

    };


    /* =========================================================================
       12. GESTION DES CHAMPS NUMÉRIQUES
       ========================================================================= */

    const NumericInputs = {

        /**
         * Nettoie les valeurs numériques avant soumission.
         *
         * Ne transforme pas les valeurs de manière agressive :
         * la validation Laravel reste la source de vérité.
         */
        init() {

            Dom.all(
                'input[data-stocks-number]'
            ).forEach(function (input) {

                input.addEventListener(
                    'blur',
                    function () {

                        if (
                            input.value === ''
                        ) {
                            return;
                        }


                        let value =
                            input.value.trim();


                        /*
                         * Accepte la virgule française.
                         */
                        if (
                            input.dataset.stocksDecimal
                            !== undefined
                        ) {

                            value =
                                value.replace(',', '.');

                        }


                        input.value = value;

                    }
                );

            });

        },

    };


    /* =========================================================================
       13. COPIE DANS LE PRESSE-PAPIERS
       ========================================================================= */

    const Clipboard = {

        /**
         * Initialise les boutons de copie.
         *
         * Exemple :
         *
         * <button
         *     type="button"
         *     data-stocks-copy="#reference"
         * >
         *     Copier
         * </button>
         */
        init() {

            Dom.all(
                '[data-stocks-copy]'
            ).forEach(function (button) {

                button.addEventListener(
                    'click',
                    async function () {

                        const selector =
                            button.dataset.stocksCopy;


                        if (!selector) {
                            return;
                        }


                        const source =
                            Dom.one(selector);


                        if (!source) {
                            return;
                        }


                        const value =
                            source.value
                            ?? source.textContent
                            ?? '';


                        if (!value) {
                            return;
                        }


                        try {

                            await navigator.clipboard.writeText(
                                String(value).trim()
                            );


                            const original =
                                button.innerHTML;


                            button.innerHTML =
                                '<i class="fas fa-check"></i> Copié';


                            window.setTimeout(
                                function () {

                                    button.innerHTML =
                                        original;

                                },
                                1500
                            );


                        } catch (error) {

                            /*
                             * Fallback silencieux :
                             * la fonction reste non bloquante.
                             */
                            console.warn(
                                'Copie impossible :',
                                error
                            );

                        }

                    }
                );

            });

        },

    };


    /* =========================================================================
       14. CONFIRMATION AVANT SORTIE DE PAGE AVEC FORMULAIRE MODIFIÉ
       ========================================================================= */

    const UnsavedChanges = {

        forms: new Set(),

        enabled: false,


        /**
         * Initialise la détection des modifications.
         *
         * Activation uniquement pour les formulaires portant :
         *
         * data-stocks-unsaved
         */
        init() {

            const forms =
                Dom.all(
                    'form[data-stocks-unsaved]'
                );


            if (!forms.length) {
                return;
            }


            forms.forEach(function (form) {

                let initialData =
                    new FormData(form);


                let initialSerialized =
                    UnsavedChanges.serialize(
                        initialData
                    );


                form.addEventListener(
                    'input',
                    function () {

                        const currentData =
                            new FormData(form);


                        const currentSerialized =
                            UnsavedChanges.serialize(
                                currentData
                            );


                        if (
                            currentSerialized
                            !== initialSerialized
                        ) {

                            UnsavedChanges.forms.add(
                                form
                            );

                        } else {

                            UnsavedChanges.forms.delete(
                                form
                            );

                        }

                    }
                );


                form.addEventListener(
                    'submit',
                    function () {

                        UnsavedChanges.forms.delete(
                            form
                        );

                    }
                );

            });


            if (!UnsavedChanges.enabled) {

                window.addEventListener(
                    'beforeunload',
                    function (event) {

                        if (
                            UnsavedChanges.forms.size
                            === 0
                        ) {
                            return;
                        }


                        event.preventDefault();

                        event.returnValue = '';

                    }
                );


                UnsavedChanges.enabled = true;

            }

        },


        /**
         * Sérialise FormData.
         */
        serialize(formData) {

            const entries =
                Array.from(
                    formData.entries()
                );


            return JSON.stringify(
                entries
            );

        },

    };


    /* =========================================================================
       15. DÉLÉGATION DES ACTIONS DATA
       ========================================================================= */

    const DataActions = {

        /**
         * Gestion des actions génériques.
         *
         * data-stocks-action="scroll-top"
         * data-stocks-action="close"
         */
        init() {

            document.addEventListener(
                'click',
                function (event) {

                    const element =
                        event.target.closest(
                            '[data-stocks-action]'
                        );


                    if (!element) {
                        return;
                    }


                    const action =
                        element.dataset.stocksAction;


                    switch (action) {

                        case 'scroll-top':

                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth',
                            });

                            break;


                        case 'close':

                            const targetSelector =
                                element.dataset.stocksTarget;


                            if (!targetSelector) {
                                return;
                            }


                            const target =
                                Dom.one(
                                    targetSelector
                                );


                            if (target) {

                                Alerts.close(
                                    target
                                );

                            }

                            break;


                        default:

                            /*
                             * Les actions inconnues sont ignorées.
                             * Elles pourront être traitées par un
                             * fichier JS spécifique.
                             */

                            break;

                    }

                }
            );

        },

    };


    /* =========================================================================
       16. ÉTAT DE CHARGEMENT
       ========================================================================= */

    const Loading = {

        /**
         * Active visuellement un élément.
         */
        start(element, text = null) {

            if (!element) {
                return;
            }


            if (
                !element.dataset.stocksOriginalHtml
            ) {

                element.dataset.stocksOriginalHtml =
                    element.innerHTML;

            }


            element.disabled = true;


            element.classList.add(
                StocksConfig.classes.loading
            );


            if (text) {

                element.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> '
                    + text;

            }

        },


        /**
         * Restaure un élément.
         */
        stop(element) {

            if (!element) {
                return;
            }


            element.disabled = false;


            element.classList.remove(
                StocksConfig.classes.loading
            );


            if (
                element.dataset.stocksOriginalHtml
            ) {

                element.innerHTML =
                    element.dataset.stocksOriginalHtml;

                delete element.dataset.stocksOriginalHtml;

            }

        },

    };


    /* =========================================================================
       17. EXPOSITION PUBLIQUE
       ========================================================================= */

    window.PhoveStocks.Dom =
        Dom;

    window.PhoveStocks.Alerts =
        Alerts;

    window.PhoveStocks.Confirmations =
        Confirmations;

    window.PhoveStocks.FormProtection =
        FormProtection;

    window.PhoveStocks.Numbers =
        Numbers;

    window.PhoveStocks.Dates =
        Dates;

    window.PhoveStocks.LocalSearch =
        LocalSearch;

    window.PhoveStocks.Tooltips =
        Tooltips;

    window.PhoveStocks.Clipboard =
        Clipboard;

    window.PhoveStocks.Loading =
        Loading;

    window.PhoveStocks.Config =
        StocksConfig;


    /* =========================================================================
       18. INITIALISATION COMMUNE
       ========================================================================= */

    function initStocksCommon() {

        Alerts.init();

        AutoDismiss.init();

        Confirmations.init();

        FormProtection.init();

        LocalSearch.init();

        Tooltips.init();

        NumericInputs.init();

        Clipboard.init();

        UnsavedChanges.init();

        DataActions.init();

    }


    /* =========================================================================
       19. DOM READY
       ========================================================================= */

    if (
        document.readyState
        === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initStocksCommon
        );

    } else {

        initStocksCommon();

    }


})();