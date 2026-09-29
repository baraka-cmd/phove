/**
 * PHOVE — ADMINISTRATION
 * MODULE : PAIEMENTS DES SALAIRES
 * VUE : RAPPORT
 *
 * Fichier :
 * public/assets/js/admin/paiements-salaires/rapport.js
 *
 * Responsabilités :
 * - Gestion des filtres
 * - Réinitialisation des filtres
 * - Validation des dates
 * - Soumission du formulaire
 * - Gestion des barres de progression
 * - Impression du rapport
 * - Copie des références
 * - Gestion du tableau
 * - Accessibilité
 * - Protection contre les doubles soumissions
 */

(function () {
    'use strict';

    /* =========================================================
     * CONFIGURATION
     * ========================================================= */

    const CONFIG = {
        selectors: {
            root: '.salary-payment-report',

            filterForm: '[data-filter-form]',

            monthFilter: '[data-month-filter]',
            dateFrom: '[data-date-from]',
            dateTo: '[data-date-to]',
            modeFilter: '[data-mode-filter]',
            searchInput: '[data-search-input]',

            submitFilter: '[data-submit-filter]',
            resetFilters: '[data-reset-filters]',

            progress: '[data-progress]',

            print: '[data-print]',

            copy: '[data-copy]',

            paymentTable: '[data-payment-table]',
            paymentRow: '[data-payment-row]'
        },

        search: {
            minLength: 2,
            delay: 450
        },

        animation: {
            progressDuration: 500
        }
    };


    /* =========================================================
     * ÉTAT GLOBAL
     * ========================================================= */

    const state = {
        initialized: false,
        submitting: false,
        searchTimer: null
    };


    /* =========================================================
     * OUTILS DOM
     * ========================================================= */

    function getRoot() {
        return document.querySelector(CONFIG.selectors.root);
    }


    function qs(selector, parent = document) {
        return parent.querySelector(selector);
    }


    function qsa(selector, parent = document) {
        return Array.from(parent.querySelectorAll(selector));
    }


    function hasElement(element) {
        return element !== null && element !== undefined;
    }


    /* =========================================================
     * UTILITAIRES
     * ========================================================= */

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }


    function normalizeText(value) {
        return String(value ?? '')
            .trim()
            .toLowerCase();
    }


    function escapeHtml(value) {
        const div = document.createElement('div');

        div.textContent = String(value ?? '');

        return div.innerHTML;
    }


    /* =========================================================
     * PROGRESSIONS
     * ========================================================= */

    function initializeProgressBars(root) {
        const progressBars = qsa(
            CONFIG.selectors.progress,
            root
        );

        progressBars.forEach(function (bar) {
            let value = parseFloat(
                bar.dataset.progress || '0'
            );

            if (!Number.isFinite(value)) {
                value = 0;
            }

            value = clamp(value, 0, 100);

            /*
             * On conserve la valeur dans data-progress.
             */
            bar.dataset.progress = value.toString();

            /*
             * Accessibilité.
             */
            bar.setAttribute(
                'role',
                'progressbar'
            );

            bar.setAttribute(
                'aria-valuemin',
                '0'
            );

            bar.setAttribute(
                'aria-valuemax',
                '100'
            );

            bar.setAttribute(
                'aria-valuenow',
                value.toString()
            );

            /*
             * L'animation est volontairement faite
             * en JavaScript puisque la valeur est dynamique.
             */
            requestAnimationFrame(function () {
                bar.style.width = value + '%';
            });
        });
    }


    /* =========================================================
     * FILTRES
     * ========================================================= */

    function getFilterForm(root) {
        return qs(
            CONFIG.selectors.filterForm,
            root
        );
    }


    function getFilterValues(form) {
        if (!form) {
            return {};
        }

        const formData = new FormData(form);

        return {
            mois: String(
                formData.get('mois') || ''
            ).trim(),

            date_debut: String(
                formData.get('date_debut') || ''
            ).trim(),

            date_fin: String(
                formData.get('date_fin') || ''
            ).trim(),

            mode_paiement: String(
                formData.get('mode_paiement') || ''
            ).trim(),

            recherche: String(
                formData.get('recherche') || ''
            ).trim()
        };
    }


    function validateDateRange(form) {
        const dateFrom = qs(
            CONFIG.selectors.dateFrom,
            form
        );

        const dateTo = qs(
            CONFIG.selectors.dateTo,
            form
        );

        if (!dateFrom || !dateTo) {
            return true;
        }

        /*
         * On réinitialise les erreurs visuelles.
         */
        dateFrom.removeAttribute('aria-invalid');
        dateTo.removeAttribute('aria-invalid');

        dateFrom.classList.remove('is-invalid');
        dateTo.classList.remove('is-invalid');

        if (
            dateFrom.value &&
            dateTo.value &&
            dateFrom.value > dateTo.value
        ) {
            dateFrom.classList.add('is-invalid');
            dateTo.classList.add('is-invalid');

            dateFrom.setAttribute(
                'aria-invalid',
                'true'
            );

            dateTo.setAttribute(
                'aria-invalid',
                'true'
            );

            showFilterError(
                form,
                'La date de début ne peut pas être postérieure à la date de fin.'
            );

            dateFrom.focus();

            return false;
        }

        clearFilterError(form);

        return true;
    }


    function showFilterError(form, message) {
        if (!form) {
            return;
        }

        let error = qs(
            '[data-filter-error]',
            form
        );

        if (!error) {
            error = document.createElement('div');

            error.dataset.filterError = '';

            error.className =
                'filter-validation-error';

            error.setAttribute(
                'role',
                'alert'
            );

            form.prepend(error);
        }

        error.textContent = message;
    }


    function clearFilterError(form) {
        if (!form) {
            return;
        }

        const error = qs(
            '[data-filter-error]',
            form
        );

        if (error) {
            error.remove();
        }
    }


    function setSubmitting(form, submitting) {
        if (!form) {
            return;
        }

        state.submitting = submitting;

        const submitButton = qs(
            CONFIG.selectors.submitFilter,
            form
        );

        if (!submitButton) {
            return;
        }

        if (submitting) {
            submitButton.disabled = true;

            submitButton.dataset.originalHtml =
                submitButton.innerHTML;

            submitButton.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Chargement...';
        } else {
            submitButton.disabled = false;

            if (
                submitButton.dataset.originalHtml
            ) {
                submitButton.innerHTML =
                    submitButton.dataset.originalHtml;
            }
        }
    }


    function submitFilters(form) {
        if (!form || state.submitting) {
            return;
        }

        if (!validateDateRange(form)) {
            return;
        }

        setSubmitting(
            form,
            true
        );

        /*
         * Le navigateur effectue ensuite le GET normalement.
         */
        form.submit();
    }


    function initializeFilterForm(root) {
        const form = getFilterForm(root);

        if (!form) {
            return;
        }

        /*
         * Soumission classique.
         */
        form.addEventListener(
            'submit',
            function (event) {
                event.preventDefault();

                submitFilters(form);
            }
        );


        /*
         * Date début.
         */
        const dateFrom = qs(
            CONFIG.selectors.dateFrom,
            form
        );

        if (dateFrom) {
            dateFrom.addEventListener(
                'change',
                function () {
                    validateDateRange(form);
                }
            );
        }


        /*
         * Date fin.
         */
        const dateTo = qs(
            CONFIG.selectors.dateTo,
            form
        );

        if (dateTo) {
            dateTo.addEventListener(
                'change',
                function () {
                    validateDateRange(form);
                }
            );
        }


        /*
         * Mode de paiement.
         *
         * data-auto-submit est présent dans ton Blade.
         */
        const modeFilter = qs(
            CONFIG.selectors.modeFilter,
            form
        );

        if (modeFilter) {
            modeFilter.addEventListener(
                'change',
                function () {
                    if (
                        modeFilter.hasAttribute(
                            'data-auto-submit'
                        )
                    ) {
                        submitFilters(form);
                    }
                }
            );
        }


        /*
         * Recherche.
         *
         * On évite de recharger la page à chaque frappe.
         *
         * Le formulaire est soumis lorsque l'utilisateur
         * arrête de taper.
         */
        const searchInput = qs(
            CONFIG.selectors.searchInput,
            form
        );

        if (searchInput) {
            searchInput.addEventListener(
                'keydown',
                function (event) {
                    if (
                        event.key === 'Enter'
                    ) {
                        event.preventDefault();

                        submitFilters(form);
                    }
                }
            );
        }


        /*
         * Bouton Appliquer.
         */
        const submitButton = qs(
            CONFIG.selectors.submitFilter,
            form
        );

        if (submitButton) {
            submitButton.addEventListener(
                'click',
                function (event) {
                    event.preventDefault();

                    submitFilters(form);
                }
            );
        }
    }


    /* =========================================================
     * RÉINITIALISATION DES FILTRES
     * ========================================================= */

    function resetFilters(root) {
        const form = getFilterForm(root);

        if (!form) {
            return;
        }

        const resetUrl =
            form.dataset.resetUrl ||
            form.action ||
            window.location.pathname;

        /*
         * On retourne simplement à l'URL du rapport
         * sans query string.
         */
        window.location.href = resetUrl;
    }


    function initializeResetButtons(root) {
        const buttons = qsa(
            CONFIG.selectors.resetFilters,
            root
        );

        buttons.forEach(function (button) {
            button.addEventListener(
                'click',
                function (event) {
                    event.preventDefault();

                    resetFilters(root);
                }
            );
        });
    }


    /* =========================================================
     * IMPRESSION
     * ========================================================= */

    function printReport() {
        /*
         * Petit délai pour laisser les interfaces éventuelles
         * terminer leurs animations.
         */
        window.setTimeout(
            function () {
                window.print();
            },
            50
        );
    }


    function initializePrintButtons(root) {
        const buttons = qsa(
            CONFIG.selectors.print,
            root
        );

        buttons.forEach(function (button) {
            button.addEventListener(
                'click',
                function (event) {
                    event.preventDefault();

                    const printUrl =
                        button.dataset.printUrl;

                    /*
                     * Si une URL d'impression spécifique
                     * est fournie, on l'utilise.
                     */
                    if (printUrl) {
                        window.open(
                            printUrl,
                            '_blank',
                            'noopener,noreferrer'
                        );

                        return;
                    }

                    printReport();
                }
            );
        });
    }


    /* =========================================================
     * COPIE DES RÉFÉRENCES
     * ========================================================= */

    async function copyText(text) {
        if (!text) {
            return false;
        }

        /*
         * API Clipboard moderne.
         */
        if (
            navigator.clipboard &&
            typeof navigator.clipboard.writeText ===
                'function'
        ) {
            try {
                await navigator.clipboard.writeText(
                    text
                );

                return true;
            } catch (error) {
                /*
                 * On continue avec la méthode de secours.
                 */
            }
        }

        /*
         * Méthode compatible anciens navigateurs.
         */
        const textarea =
            document.createElement('textarea');

        textarea.value = text;

        textarea.setAttribute(
            'readonly',
            ''
        );

        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';

        document.body.appendChild(textarea);

        textarea.select();

        let success = false;

        try {
            success =
                document.execCommand('copy');
        } catch (error) {
            success = false;
        }

        textarea.remove();

        return success;
    }


    function showCopyFeedback(button, success) {
        if (!button) {
            return;
        }

        const originalHtml =
            button.innerHTML;

        const originalTitle =
            button.getAttribute('title');

        if (success) {
            button.innerHTML =
                '<i class="fas fa-check"></i>';

            button.classList.add(
                'is-copied'
            );

            button.setAttribute(
                'title',
                'Référence copiée'
            );
        } else {
            button.innerHTML =
                '<i class="fas fa-xmark"></i>';

            button.classList.add(
                'copy-error'
            );

            button.setAttribute(
                'title',
                'Impossible de copier'
            );
        }

        window.setTimeout(
            function () {
                button.innerHTML =
                    originalHtml;

                button.classList.remove(
                    'is-copied',
                    'copy-error'
                );

                if (originalTitle !== null) {
                    button.setAttribute(
                        'title',
                        originalTitle
                    );
                }
            },
            1400
        );
    }


    function initializeCopyButtons(root) {
        const buttons = qsa(
            CONFIG.selectors.copy,
            root
        );

        buttons.forEach(function (button) {
            button.addEventListener(
                'click',
                async function (event) {
                    event.preventDefault();

                    const value =
                        button.dataset.copyValue ||
                        button.textContent.trim();

                    if (!value || value === '—') {
                        return;
                    }

                    const success =
                        await copyText(value);

                    showCopyFeedback(
                        button,
                        success
                    );
                }
            );
        });
    }


    /* =========================================================
     * TABLEAU
     * ========================================================= */

    function initializePaymentTable(root) {
        const table = qs(
            CONFIG.selectors.paymentTable,
            root
        );

        if (!table) {
            return;
        }

        const rows = qsa(
            CONFIG.selectors.paymentRow,
            table
        );

        rows.forEach(function (row, index) {
            /*
             * Accessibilité clavier.
             */
            row.setAttribute(
                'tabindex',
                '0'
            );

            row.dataset.rowIndex =
                String(index + 1);

            row.addEventListener(
                'keydown',
                function (event) {
                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {
                        /*
                         * Si l'utilisateur est déjà
                         * sur un bouton/lien, on ne bloque pas
                         * son comportement normal.
                         */
                        const target =
                            event.target;

                        if (
                            target.closest(
                                'a, button, input, select, textarea'
                            )
                        ) {
                            return;
                        }

                        event.preventDefault();

                        const firstAction =
                            qs(
                                'a, button',
                                row
                            );

                        if (firstAction) {
                            firstAction.focus();
                        }
                    }
                }
            );
        });
    }


    /* =========================================================
     * ACCESSIBILITÉ DES FILTRES
     * ========================================================= */

    function initializeFilterAccessibility(root) {
        const form = getFilterForm(root);

        if (!form) {
            return;
        }

        const controls = form.querySelectorAll(
            'input, select'
        );

        controls.forEach(function (control) {
            control.addEventListener(
                'input',
                function () {
                    control.removeAttribute(
                        'aria-invalid'
                    );

                    control.classList.remove(
                        'is-invalid'
                    );
                }
            );
        });
    }


    /* =========================================================
     * CLAVIER GLOBAL
     * ========================================================= */

    function initializeKeyboardShortcuts(root) {
        document.addEventListener(
            'keydown',
            function (event) {
                /*
                 * Ctrl + P / Cmd + P :
                 * laisser le navigateur gérer l'impression.
                 */
                if (
                    (event.ctrlKey ||
                        event.metaKey) &&
                    event.key.toLowerCase() === 'p'
                ) {
                    /*
                     * On ne fait rien :
                     * le navigateur ouvrira sa boîte
                     * d'impression.
                     */
                    return;
                }

                /*
                 * Échap :
                 * retire les états invalides.
                 */
                if (event.key === 'Escape') {
                    const form =
                        getFilterForm(root);

                    if (form) {
                        clearFilterError(form);
                    }
                }
            }
        );
    }


    /* =========================================================
     * DÉTECTION DES PARAMÈTRES ACTIFS
     * ========================================================= */

    function getActiveFilters() {
        const params =
            new URLSearchParams(
                window.location.search
            );

        return {
            mois: params.get('mois') || '',
            date_debut:
                params.get('date_debut') || '',
            date_fin:
                params.get('date_fin') || '',
            mode_paiement:
                params.get('mode_paiement') || '',
            recherche:
                params.get('recherche') || ''
        };
    }


    function markActiveFilters(root) {
        const filters =
            getActiveFilters();

        const form =
            getFilterForm(root);

        if (!form) {
            return;
        }

        const controls = {
            mois: qs(
                CONFIG.selectors.monthFilter,
                form
            ),

            date_debut: qs(
                CONFIG.selectors.dateFrom,
                form
            ),

            date_fin: qs(
                CONFIG.selectors.dateTo,
                form
            ),

            mode_paiement: qs(
                CONFIG.selectors.modeFilter,
                form
            ),

            recherche: qs(
                CONFIG.selectors.searchInput,
                form
            )
        };

        Object.keys(controls).forEach(
            function (key) {
                const control =
                    controls[key];

                if (!control) {
                    return;
                }

                const value =
                    filters[key];

                if (value) {
                    control.classList.add(
                        'has-value'
                    );
                } else {
                    control.classList.remove(
                        'has-value'
                    );
                }
            }
        );
    }


    /* =========================================================
     * PROTECTION CONTRE LA DOUBLE SOUMISSION
     * ========================================================= */

    function initializeSubmitProtection(root) {
        const form =
            getFilterForm(root);

        if (!form) {
            return;
        }

        form.addEventListener(
            'submit',
            function () {
                if (state.submitting) {
                    return;
                }

                state.submitting = true;
            }
        );
    }


    /* =========================================================
     * INITIALISATION
     * ========================================================= */

    function init() {
        if (state.initialized) {
            return;
        }

        const root = getRoot();

        if (!root) {
            return;
        }

        state.initialized = true;

        initializeProgressBars(root);

        initializeFilterForm(root);

        initializeResetButtons(root);

        initializePrintButtons(root);

        initializeCopyButtons(root);

        initializePaymentTable(root);

        initializeFilterAccessibility(root);

        initializeKeyboardShortcuts(root);

        initializeSubmitProtection(root);

        markActiveFilters(root);

        /*
         * Marque le module comme prêt.
         */
        root.classList.add(
            'is-ready'
        );
    }


    /* =========================================================
     * API PUBLIQUE
     * ========================================================= */

    window.PHOVE =
        window.PHOVE || {};

    window.PHOVE.salaryPaymentReport = {
        init: init,

        print: printReport,

        reset: function () {
            const root = getRoot();

            if (root) {
                resetFilters(root);
            }
        },

        getFilters: getActiveFilters,

        refreshProgress: function () {
            const root = getRoot();

            if (root) {
                initializeProgressBars(root);
            }
        }
    };


    /* =========================================================
     * DÉMARRAGE
     * ========================================================= */

    if (
        document.readyState ===
        'loading'
    ) {
        document.addEventListener(
            'DOMContentLoaded',
            init,
            {
                once: true
            }
        );
    } else {
        init();
    }

})();