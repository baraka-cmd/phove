'use strict';

/**
 * ============================================================
 * PHOVE — STATISTIQUES DES PAIEMENTS DE SALAIRES
 * ============================================================
 *
 * Fichier :
 * public/assets/js/admin/paiements-salaires/statistiques.js
 *
 * Responsabilités :
 *
 * - gestion du filtre par mois ;
 * - soumission du formulaire ;
 * - état de chargement ;
 * - animation des statistiques ;
 * - animation des barres de progression ;
 * - impression ;
 * - réinitialisation ;
 * - accessibilité ;
 * - prévention des doubles soumissions.
 *
 * Aucun calcul financier critique n'est effectué ici.
 *
 * Les données financières proviennent exclusivement
 * du serveur Laravel.
 * ============================================================
 */

(function () {

    'use strict';


    /* ========================================================
       CONFIGURATION
    ======================================================== */

    const CONFIG = {
        selectors: {
            root: '.payment-statistics',

            filterForm: '[data-statistics-filter]',
            monthFilter: '[data-month-filter]',

            submitButton: '[data-submit-filter]',
            resetButton: '[data-reset-filters]',

            progressBars: '[data-progress]'
        },

        animation: {
            progressDelay: 80,
            progressDuration: 700
        }
    };


    /* ========================================================
       ÉTAT
    ======================================================== */

    const state = {
        initialized: false,
        submitting: false,
        printing: false
    };


    /* ========================================================
       UTILITAIRES DOM
    ======================================================== */

    function getRoot() {
        return document.querySelector(CONFIG.selectors.root);
    }


    function getElement(selector, parent = document) {
        return parent.querySelector(selector);
    }


    function getElements(selector, parent = document) {
        return Array.from(
            parent.querySelectorAll(selector)
        );
    }


    /* ========================================================
       INITIALISATION
    ======================================================== */

    function init() {

        if (state.initialized) {
            return;
        }

        const root = getRoot();

        /*
         * La page n'est pas présente.
         *
         * On quitte silencieusement afin de ne pas
         * générer d'erreur JavaScript sur d'autres pages.
         */
        if (!root) {
            return;
        }

        state.initialized = true;

        initializeFilterForm(root);

        initializeMonthFilter(root);

        initializeResetButton(root);

        initializeProgressBars(root);

        initializeKeyboardShortcuts();

        initializePrintEvents();

        initializePageShow();

        /*
         * Petite temporisation afin de permettre au navigateur
         * de terminer le rendu initial avant les animations.
         */
        window.setTimeout(function () {
            animateStatistics(root);
        }, 100);
    }


    /* ========================================================
       FORMULAIRE DE FILTRE
    ======================================================== */

    function initializeFilterForm(root) {

        const form = getElement(
            CONFIG.selectors.filterForm,
            root
        );

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {

            /*
             * Empêche les doubles clics pendant la navigation.
             */
            if (state.submitting) {
                event.preventDefault();
                return;
            }

            state.submitting = true;

            setSubmitLoadingState(form);

            /*
             * On nettoie les champs vides avant la soumission.
             *
             * Cela évite de générer des URLs comme :
             *
             * ?mois=
             */
            cleanEmptyFormFields(form);
        });
    }


    /* ========================================================
       FILTRE MOIS
    ======================================================== */

    function initializeMonthFilter(root) {

        const monthFilter = getElement(
            CONFIG.selectors.monthFilter,
            root
        );

        if (!monthFilter) {
            return;
        }

        /*
         * Lorsque l'utilisateur change le mois,
         * le formulaire est soumis automatiquement.
         *
         * Cela permet d'avoir une interface de statistiques
         * rapide et naturelle.
         */
        monthFilter.addEventListener('change', function () {

            const form = monthFilter.closest('form');

            if (!form || state.submitting) {
                return;
            }

            /*
             * Aucun mois sélectionné :
             * on peut quand même soumettre afin de demander
             * au serveur d'afficher la période globale.
             */
            state.submitting = true;

            setSubmitLoadingState(form);

            cleanEmptyFormFields(form);

            /*
             * requestSubmit() respecte le comportement normal
             * du formulaire et ses événements.
             */
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    }


    /* ========================================================
       NETTOYAGE DES CHAMPS VIDES
    ======================================================== */

    function cleanEmptyFormFields(form) {

        const fields = form.querySelectorAll(
            'input[name], select[name], textarea[name]'
        );

        fields.forEach(function (field) {

            /*
             * On ne désactive pas les boutons.
             */
            if (
                field.tagName === 'BUTTON'
                || field.type === 'submit'
            ) {
                return;
            }

            const value = String(
                field.value ?? ''
            ).trim();

            /*
             * Les champs vides ne sont pas envoyés.
             */
            if (value === '') {
                field.disabled = true;
            }
        });
    }


    /* ========================================================
       ÉTAT DE CHARGEMENT DU BOUTON
    ======================================================== */

    function setSubmitLoadingState(form) {

        const button = getElement(
            CONFIG.selectors.submitButton,
            form
        );

        if (!button) {
            return;
        }

        button.classList.add('is-loading');

        button.disabled = true;

        /*
         * Sauvegarde du contenu original.
         */
        if (!button.dataset.originalContent) {
            button.dataset.originalContent =
                button.innerHTML;
        }

        button.innerHTML =
            '<i class="fas fa-spinner"></i>' +
            '<span>Actualisation...</span>';
    }


    /* ========================================================
       BOUTON RÉINITIALISER
    ======================================================== */

    function initializeResetButton(root) {

        const resetButton = getElement(
            CONFIG.selectors.resetButton,
            root
        );

        if (!resetButton) {
            return;
        }

        resetButton.addEventListener(
            'click',
            function () {

                /*
                 * On évite de modifier l'URL manuellement.
                 *
                 * Le lien possède déjà l'URL propre de la route.
                 */
                resetButton.classList.add('is-loading');

                /*
                 * Petite indication visuelle avant navigation.
                 */
                const icon = resetButton.querySelector('i');

                if (icon) {
                    icon.className =
                        'fas fa-spinner';
                }
            }
        );
    }


    /* ========================================================
       BARRES DE PROGRESSION
    ======================================================== */

    function initializeProgressBars(root) {

        const progressBars = getElements(
            CONFIG.selectors.progressBars,
            root
        );

        if (!progressBars.length) {
            return;
        }

        progressBars.forEach(function (bar) {

            /*
             * Récupération du pourcentage envoyé par Blade.
             */
            let percentage = parseFloat(
                bar.dataset.progress
            );

            /*
             * Protection contre :
             *
             * NaN
             * valeurs négatives
             * valeurs supérieures à 100
             */
            if (!Number.isFinite(percentage)) {
                percentage = 0;
            }

            percentage = Math.max(
                0,
                Math.min(100, percentage)
            );

            /*
             * On commence à 0 afin que l'animation soit visible.
             */
            bar.style.width = '0%';

            /*
             * Stockage de la valeur normalisée.
             */
            bar.dataset.normalizedProgress =
                String(percentage);

        });
    }


    function animateStatistics(root) {

        const progressBars = getElements(
            CONFIG.selectors.progressBars,
            root
        );

        if (!progressBars.length) {
            return;
        }

        progressBars.forEach(function (bar, index) {

            const percentage = parseFloat(
                bar.dataset.normalizedProgress ?? '0'
            );

            window.setTimeout(function () {

                bar.style.width =
                    percentage + '%';

            }, CONFIG.animation.progressDelay * index);
        });
    }


    /* ========================================================
       IMPRESSION
    ======================================================== */

    function printPage() {

        if (state.printing) {
            return;
        }

        state.printing = true;

        document.body.classList.add(
            'statistics-printing'
        );

        /*
         * Laisser le navigateur appliquer la classe
         * avant de lancer l'impression.
         */
        window.setTimeout(function () {

            window.print();

        }, 50);
    }


    function initializePrintEvents() {

        /*
         * Après fermeture de la boîte d'impression,
         * on retire la classe temporaire.
         */
        window.addEventListener(
            'afterprint',
            function () {

                document.body.classList.remove(
                    'statistics-printing'
                );

                state.printing = false;
            }
        );
    }


    /* ========================================================
       RACCOURCIS CLAVIER
    ======================================================== */

    function initializeKeyboardShortcuts() {

        document.addEventListener(
            'keydown',
            function (event) {

                /*
                 * Ctrl + P sous Windows/Linux
                 * Cmd + P sous macOS.
                 */
                const isPrintShortcut =
                    (event.ctrlKey || event.metaKey)
                    && event.key.toLowerCase() === 'p';

                if (!isPrintShortcut) {
                    return;
                }

                /*
                 * On vérifie que nous sommes bien
                 * sur la page des statistiques.
                 */
                if (!getRoot()) {
                    return;
                }

                event.preventDefault();

                printPage();
            }
        );
    }


    /* ========================================================
       GESTION DES ÉVÉNEMENTS D'IMPRESSION
    ======================================================== */

    function initializePrintEvents() {

        /*
         * Évite une double déclaration si cette fonction
         * est appelée plusieurs fois.
         */
        if (window.__PHOVE_STATISTICS_PRINT_INITIALIZED__) {
            return;
        }

        window.__PHOVE_STATISTICS_PRINT_INITIALIZED__ = true;

        window.addEventListener(
            'afterprint',
            function () {

                document.body.classList.remove(
                    'statistics-printing'
                );

                state.printing = false;
            }
        );
    }


    /* ========================================================
       PAGESHOW
    ======================================================== */

    function initializePageShow() {

        window.addEventListener(
            'pageshow',
            function () {

                /*
                 * Lorsque l'utilisateur revient avec
                 * le bouton précédent du navigateur,
                 * le navigateur peut restaurer le formulaire
                 * et l'état du bouton.
                 */
                state.submitting = false;

                const root = getRoot();

                if (!root) {
                    return;
                }

                const submitButton = getElement(
                    CONFIG.selectors.submitButton,
                    root
                );

                if (submitButton) {

                    submitButton.disabled = false;

                    submitButton.classList.remove(
                        'is-loading'
                    );

                    if (submitButton.dataset.originalContent) {

                        submitButton.innerHTML =
                            submitButton.dataset.originalContent;
                    }
                }

                const resetButton = getElement(
                    CONFIG.selectors.resetButton,
                    root
                );

                if (resetButton) {

                    resetButton.classList.remove(
                        'is-loading'
                    );

                    const icon =
                        resetButton.querySelector('i');

                    if (icon) {
                        icon.className =
                            'fas fa-undo';
                    }
                }

                /*
                 * Les barres peuvent avoir été restaurées
                 * par le navigateur à une valeur précédente.
                 *
                 * On les réinitialise puis on relance
                 * leur animation.
                 */
                initializeProgressBars(root);

                window.setTimeout(function () {
                    animateStatistics(root);
                }, 100);
            }
        );
    }


    /* ========================================================
       ANIMATION DES NOMBRES
       ======================================================== */

    function animateNumbers(root) {

        /*
         * Cette fonctionnalité reste volontairement désactivée
         * par défaut.
         *
         * Les montants financiers affichés par Blade sont déjà
         * calculés côté serveur. Une animation automatique
         * des nombres peut être trompeuse pour une interface
         * financière si le DOM contient des valeurs formatées.
         */
        return;
    }


    /* ========================================================
       INITIALISATION GLOBALE
    ======================================================== */

    function initialize() {

        init();

        const root = getRoot();

        if (root) {
            animateNumbers(root);
        }
    }


    /* ========================================================
       EXPOSITION PUBLIQUE MINIMALE
    ======================================================== */

    window.PHOVEPaymentStatistics = {
        init: initialize,

        print: printPage
    };


    /* ========================================================
       DOM READY
    ======================================================== */

    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initialize,
            {
                once: true
            }
        );

    } else {

        initialize();
    }


})();