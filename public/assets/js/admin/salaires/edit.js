/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : MODIFICATION D'UN SALAIRE
 * FICHIER: edit.js
 * ============================================================
 *
 * Responsabilités :
 * - Calcul dynamique du salaire net
 * - Gestion des montants
 * - Validation UX
 * - Gestion des notes
 * - Affichage du résumé financier
 * - Protection contre double soumission
 * - Gestion du verrouillage
 * - Accessibilité
 *
 * IMPORTANT :
 * Ce JavaScript ne remplace jamais :
 *
 * Policy
 * Request
 * Service
 * Transaction
 *
 * Laravel reste l'autorité métier.
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
            root: ".salary-edit",

            form: "[data-salary-edit-form]",

            base: "[data-salaire-base]",
            primes: "[data-salaire-primes]",
            retenues: "[data-salaire-retenues]",

            net: "[data-salaire-net]",

            moneyInput: "[data-money-input]",

            member: "[data-member-select]",
            organ: "[data-organe-select]",
            month: "[data-month-input]",

            notes: "[data-notes]",
            notesCounter: "[data-notes-counter]",

            submit: "[data-submit-salary]",

            cancel: "[data-cancel-edit]",

            calculationBox:
                "[data-calculation-box]",

            summaryBase:
                "[data-summary-base]",

            summaryPrimes:
                "[data-summary-primes]",

            summaryRetenues:
                "[data-summary-retenues]",

            summaryNet:
                "[data-summary-net]",

            status:
                "[data-salary-status]",

            paymentProgress:
                "[data-progress]",

            invalid:
                "[data-field-error]"
        },

        limits: {
            amount: {
                min: 0,
                max: 999999999999.99
            },

            notes: 2000
        }
    };

    /* ========================================================
       ÉTAT
       ======================================================== */

    const state = {
        initialized: false,
        submitting: false
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

        parseMoney(value) {
            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return 0;
            }

            let normalized =
                String(value)
                    .trim()
                    .replace(/\s/g, "")
                    .replace(
                        /[^\d,.-]/g,
                        ""
                    );

            if (
                normalized.includes(",") &&
                normalized.includes(".")
            ) {
                const comma =
                    normalized.lastIndexOf(
                        ","
                    );

                const dot =
                    normalized.lastIndexOf(
                        "."
                    );

                if (comma > dot) {
                    normalized =
                        normalized
                            .replace(
                                /\./g,
                                ""
                            )
                            .replace(
                                ",",
                                "."
                            );
                } else {
                    normalized =
                        normalized.replace(
                            /,/g,
                            ""
                        );
                }
            } else if (
                normalized.includes(",")
            ) {
                normalized =
                    normalized.replace(
                        ",",
                        "."
                    );
            }

            const parsed =
                Number.parseFloat(
                    normalized
                );

            return Number.isFinite(parsed)
                ? parsed
                : 0;
        },

        formatMoney(value) {
            const number =
                Number.isFinite(value)
                    ? value
                    : 0;

            return new Intl.NumberFormat(
                "fr-FR",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            ).format(number);
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
        }
    };

    /* ========================================================
       RÉFÉRENCE DES CHAMPS
       ======================================================== */

    const fields = {

        base: null,
        primes: null,
        retenues: null,
        net: null,

        member: null,
        organ: null,
        month: null,

        notes: null,
        submit: null,

        init() {

            this.base =
                dom.find(
                    CONFIG.selectors.base,
                    dom.root
                ) ||
                dom.find(
                    '[name="salaire_base"]',
                    dom.root
                );

            this.primes =
                dom.find(
                    CONFIG.selectors.primes,
                    dom.root
                ) ||
                dom.find(
                    '[name="primes"]',
                    dom.root
                );

            this.retenues =
                dom.find(
                    CONFIG.selectors.retenues,
                    dom.root
                ) ||
                dom.find(
                    '[name="retenues"]',
                    dom.root
                );

            this.net =
                dom.find(
                    CONFIG.selectors.net,
                    dom.root
                );

            this.member =
                dom.find(
                    CONFIG.selectors.member,
                    dom.root
                ) ||
                dom.find(
                    '[name="membre_id"]',
                    dom.root
                );

            this.organ =
                dom.find(
                    CONFIG.selectors.organ,
                    dom.root
                ) ||
                dom.find(
                    '[name="organe_id"]',
                    dom.root
                );

            this.month =
                dom.find(
                    CONFIG.selectors.month,
                    dom.root
                ) ||
                dom.find(
                    '[name="mois"]',
                    dom.root
                );

            this.notes =
                dom.find(
                    CONFIG.selectors.notes,
                    dom.root
                ) ||
                dom.find(
                    '[name="notes"]',
                    dom.root
                );

            this.submit =
                dom.find(
                    CONFIG.selectors.submit,
                    dom.root
                ) ||
                dom.find(
                    'button[type="submit"]',
                    dom.root
                );
        }
    };

    /* ========================================================
       CALCUL FINANCIER
       ======================================================== */

    const calculator = {

        init() {

            const inputs = [
                fields.base,
                fields.primes,
                fields.retenues
            ];

            inputs.forEach(
                (input) => {

                    if (!input) {
                        return;
                    }

                    input.addEventListener(
                        "input",
                        () => {
                            this.update();
                        }
                    );

                    input.addEventListener(
                        "change",
                        () => {
                            this.update();
                        }
                    );

                    input.addEventListener(
                        "blur",
                        () => {
                            this.normalize(
                                input
                            );

                            this.update();
                        }
                    );
                }
            );

            this.update();
        },

        values() {

            return {
                base:
                    utils.parseMoney(
                        fields.base?.value
                    ),

                primes:
                    utils.parseMoney(
                        fields.primes?.value
                    ),

                retenues:
                    utils.parseMoney(
                        fields.retenues?.value
                    )
            };
        },

        calculate() {

            const values =
                this.values();

            const net =
                values.base +
                values.primes -
                values.retenues;

            return {
                ...values,
                net
            };
        },

        update() {

            const result =
                this.calculate();

            const net =
                Math.max(
                    result.net,
                    0
                );

            /*
             * Affichage du net.
             */
            if (fields.net) {

                if (
                    fields.net.tagName ===
                    "INPUT"
                ) {
                    fields.net.value =
                        net.toFixed(2);
                } else {
                    fields.net.textContent =
                        utils.formatMoney(
                            net
                        );
                }

                fields.net.dataset.calculatedNet =
                    net.toFixed(2);
            }

            /*
             * Résumé financier.
             */
            this.updateSummary(
                result
            );

            /*
             * État visuel.
             */
            this.updateState(
                result
            );
        },

        updateSummary(result) {

            const elements = {

                base:
                    dom.find(
                        CONFIG.selectors.summaryBase,
                        dom.root
                    ),

                primes:
                    dom.find(
                        CONFIG.selectors.summaryPrimes,
                        dom.root
                    ),

                retenues:
                    dom.find(
                        CONFIG.selectors.summaryRetenues,
                        dom.root
                    ),

                net:
                    dom.find(
                        CONFIG.selectors.summaryNet,
                        dom.root
                    )
            };

            if (elements.base) {
                elements.base.textContent =
                    utils.formatMoney(
                        result.base
                    );
            }

            if (elements.primes) {
                elements.primes.textContent =
                    utils.formatMoney(
                        result.primes
                    );
            }

            if (elements.retenues) {
                elements.retenues.textContent =
                    utils.formatMoney(
                        result.retenues
                    );
            }

            if (elements.net) {
                elements.net.textContent =
                    utils.formatMoney(
                        Math.max(
                            result.net,
                            0
                        )
                    );
            }
        },

        updateState(result) {

            const box =
                dom.find(
                    CONFIG.selectors.calculationBox,
                    dom.root
                );

            if (!box) {
                return;
            }

            box.classList.remove(
                "is-valid",
                "is-invalid"
            );

            if (
                result.net < 0
            ) {

                box.classList.add(
                    "is-invalid"
                );

                box.setAttribute(
                    "aria-invalid",
                    "true"
                );

            } else {

                box.classList.add(
                    "is-valid"
                );

                box.setAttribute(
                    "aria-invalid",
                    "false"
                );
            }
        },

        normalize(input) {

            if (!input) {
                return;
            }

            if (
                input.value === ""
            ) {
                return;
            }

            const value =
                utils.parseMoney(
                    input.value
                );

            input.value =
                value.toFixed(2);
        }
    };

    /* ========================================================
       CHAMPS MONÉTAIRES
       ======================================================== */

    const moneyInputs = {

        init() {

            const inputs =
                dom.findAll(
                    CONFIG.selectors.moneyInput,
                    dom.root
                );

            inputs.forEach(
                (input) => {
                    this.bind(
                        input
                    );
                }
            );
        },

        bind(input) {

            input.addEventListener(
                "input",
                () => {
                    this.clean(
                        input
                    );
                }
            );

            input.addEventListener(
                "blur",
                () => {
                    this.format(
                        input
                    );
                }
            );

            input.addEventListener(
                "keydown",
                (event) => {
                    this.keyboard(
                        event
                    );
                }
            );
        },

        clean(input) {

            let value =
                String(
                    input.value
                );

            value =
                value.replace(
                    /[^\d.,]/g,
                    ""
                );

            const separator =
                value.search(
                    /[.,]/
                );

            if (
                separator !== -1
            ) {

                const integer =
                    value.substring(
                        0,
                        separator
                    );

                const decimal =
                    value.substring(
                        separator + 1
                    )
                    .replace(
                        /[.,]/g,
                        ""
                    )
                    .substring(
                        0,
                        2
                    );

                value =
                    integer +
                    "." +
                    decimal;
            }

            input.value =
                value;
        },

        format(input) {

            if (
                input.value === ""
            ) {
                return;
            }

            const value =
                utils.parseMoney(
                    input.value
                );

            input.value =
                value.toFixed(2);
        },

        keyboard(event) {

            const allowed = [
                "Backspace",
                "Delete",
                "Tab",
                "Escape",
                "Enter",
                "ArrowLeft",
                "ArrowRight",
                "Home",
                "End"
            ];

            if (
                allowed.includes(
                    event.key
                )
            ) {
                return;
            }

            if (
                event.ctrlKey ||
                event.metaKey
            ) {
                return;
            }

            if (
                /^\d$/.test(
                    event.key
                )
            ) {
                return;
            }

            if (
                event.key === "." ||
                event.key === ","
            ) {
                return;
            }

            event.preventDefault();
        }
    };

    /* ========================================================
       COMPTEUR DES NOTES
       ======================================================== */

    const notes = {

        init() {

            if (!fields.notes) {
                return;
            }

            fields.notes.addEventListener(
                "input",
                () => {
                    this.update();
                }
            );

            this.update();
        },

        update() {

            const counter =
                dom.find(
                    CONFIG.selectors.notesCounter,
                    dom.root
                );

            if (!counter) {
                return;
            }

            const max =
                Number(
                    fields.notes.maxLength
                ) ||
                CONFIG.limits.notes;

            const length =
                fields.notes.value.length;

            counter.textContent =
                `${length} / ${max}`;

            counter.classList.toggle(
                "is-warning",
                length >= max * 0.8
            );

            counter.classList.toggle(
                "is-danger",
                length >= max
            );

            counter.setAttribute(
                "aria-live",
                "polite"
            );
        }
    };

    /* ========================================================
       PROGRESSION DES PAIEMENTS
       ======================================================== */

    const progress = {

        init() {

            const elements =
                dom.findAll(
                    CONFIG.selectors.paymentProgress,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    const raw =
                        element.getAttribute(
                            "data-progress"
                        );

                    const value =
                        utils.parseMoney(
                            raw
                        );

                    const percentage =
                        utils.clamp(
                            value,
                            0,
                            100
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
                        String(
                            percentage
                        )
                    );
                }
            );
        }
    };

    /* ========================================================
       VALIDATION
       ======================================================== */

    const validation = {

        init() {

            const form =
                dom.find(
                    CONFIG.selectors.form,
                    dom.root
                );

            if (!form) {
                return;
            }

            form.addEventListener(
                "submit",
                (event) => {

                    if (
                        !this.validate(
                            form
                        )
                    ) {

                        event.preventDefault();

                        const invalid =
                            form.querySelector(
                                ":invalid"
                            );

                        invalid?.focus();

                        return;
                    }

                    submission.lock(
                        form
                    );
                }
            );

            [
                fields.base,
                fields.primes,
                fields.retenues
            ].forEach(
                (field) => {

                    field?.addEventListener(
                        "blur",
                        () => {
                            this.field(
                                field
                            );
                        }
                    );
                }
            );
        },

        validate(form) {

            let valid = true;

            if (
                !form.checkValidity()
            ) {
                valid = false;
            }

            const result =
                calculator.calculate();

            if (
                result.net < 0
            ) {

                valid = false;

                this.invalid(
                    fields.retenues,
                    "Les retenues ne peuvent pas dépasser le salaire brut."
                );
            }

            if (
                result.base >
                CONFIG.limits.amount.max
            ) {

                valid = false;

                this.invalid(
                    fields.base,
                    "Le salaire de base dépasse la limite autorisée."
                );
            }

            return valid;
        },

        field(field) {

            if (!field) {
                return true;
            }

            if (
                field.checkValidity()
            ) {

                this.clear(
                    field
                );

                return true;
            }

            this.invalid(
                field,
                field.validationMessage
            );

            return false;
        },

        invalid(
            field,
            message
        ) {

            if (!field) {
                return;
            }

            field.classList.add(
                "is-invalid"
            );

            field.setAttribute(
                "aria-invalid",
                "true"
            );

            const container =
                field.closest(
                    "[data-field]"
                );

            const error =
                container?.querySelector(
                    CONFIG.selectors.invalid
                );

            if (
                error &&
                message
            ) {
                error.textContent =
                    message;
            }
        },

        clear(field) {

            field.classList.remove(
                "is-invalid"
            );

            field.setAttribute(
                "aria-invalid",
                "false"
            );
        }
    };

    /* ========================================================
       SOUMISSION
       ======================================================== */

    const submission = {

        lock(form) {

            if (
                state.submitting
            ) {
                return;
            }

            state.submitting =
                true;

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
                        "is-loading"
                    );

                    if (
                        button.tagName ===
                        "BUTTON"
                    ) {

                        button.dataset.originalText =
                            button.innerHTML;

                        const text =
                            button.dataset.loadingText ||
                            "Mise à jour...";

                        button.innerHTML = `
                            <span
                                class="salary-edit-spinner"
                                aria-hidden="true"
                            ></span>
                            <span>${text}</span>
                        `;
                    }
                }
            );
        }
    };

    /* ========================================================
       ANNULATION DE L'ÉDITION
       ======================================================== */

    const cancel = {

        init() {

            const button =
                dom.find(
                    CONFIG.selectors.cancel,
                    dom.root
                );

            if (!button) {
                return;
            }

            button.addEventListener(
                "click",
                (event) => {

                    const form =
                        dom.find(
                            CONFIG.selectors.form,
                            dom.root
                        );

                    if (
                        !form ||
                        !this.hasChanges(
                            form
                        )
                    ) {
                        return;
                    }

                    const confirmed =
                        window.confirm(
                            "Des modifications n'ont pas encore été enregistrées. Voulez-vous quitter cette page ?"
                        );

                    if (!confirmed) {
                        event.preventDefault();
                    }
                }
            );
        },

        hasChanges(form) {

            const elements =
                form.querySelectorAll(
                    "input, select, textarea"
                );

            for (
                const element
                of elements
            ) {

                if (
                    [
                        "hidden",
                        "submit",
                        "button"
                    ].includes(
                        element.type
                    )
                ) {
                    continue;
                }

                if (
                    element.defaultValue !==
                    element.value
                ) {
                    return true;
                }
            }

            return false;
        }
    };

    /* ========================================================
       PROTECTION DE FERMETURE
       ======================================================== */

    const beforeUnload = {

        init() {

            const form =
                dom.find(
                    CONFIG.selectors.form,
                    dom.root
                );

            if (!form) {
                return;
            }

            window.addEventListener(
                "beforeunload",
                (event) => {

                    if (
                        state.submitting
                    ) {
                        return;
                    }

                    if (
                        !cancel.hasChanges(
                            form
                        )
                    ) {
                        return;
                    }

                    event.preventDefault();

                    event.returnValue =
                        "";
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

        fields.init();

        calculator.init();

        moneyInputs.init();

        notes.init();

        progress.init();

        validation.init();

        cancel.init();

        beforeUnload.init();

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

    window.PHOVE.salaryEdit = {

        calculate() {
            return calculator.calculate();
        },

        refresh() {
            calculator.update();
        },

        hasUnsavedChanges() {

            const form =
                dom.find(
                    CONFIG.selectors.form,
                    dom.root
                );

            return form
                ? cancel.hasChanges(
                    form
                )
                : false;
        }
    };

})();