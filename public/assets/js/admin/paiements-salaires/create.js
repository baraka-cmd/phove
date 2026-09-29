/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : PAIEMENTS DES SALAIRES
 * VUE    : CREATE
 * FICHIER: create.js
 * ============================================================
 *
 * Enregistrement d'un paiement de salaire.
 *
 * Responsabilités :
 * - Prévisualisation financière
 * - Calcul du reste à payer côté interface
 * - Validation UX
 * - Gestion du montant maximum
 * - Gestion de la date
 * - Compteur de caractères
 * - Confirmation du paiement
 * - Protection contre double soumission
 * - Gestion du chargement
 * - Accessibilité
 *
 * IMPORTANT :
 *
 * Le JavaScript ne remplace PAS :
 * - StorePaiementSalaireRequest
 * - PaiementSalaireService
 * - Policy
 * - transaction SQL
 * - lockForUpdate()
 *
 * Le serveur doit toujours recalculer :
 *
 * montant maximum =
 * salaire_net - total_paye
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
                ".salary-payment-create",

            form:
                "[data-payment-form]",

            amount:
                "[data-payment-amount]",

            amountInput:
                "input[name='montant']",

            maxAmount:
                "[data-max-amount]",

            netSalary:
                "[data-net-salary]",

            totalPaid:
                "[data-total-paid]",

            remaining:
                "[data-remaining]",

            remainingAfter:
                "[data-remaining-after]",

            percentage:
                "[data-payment-percentage]",

            progress:
                "[data-progress]",

            mode:
                "[data-payment-mode]",

            date:
                "[data-payment-date]",

            notes:
                "textarea[name='notes']",

            notesCounter:
                "[data-notes-counter]",

            submit:
                "[data-submit-payment]",

            cancel:
                "[data-cancel-payment]",

            confirm:
                "[data-confirm-payment]",

            copy:
                "[data-copy]",

            error:
                "[data-field-error]"
        },

        financial: {

            decimals:
                2,

            minimum:
                0.01
        },

        notes: {

            maxLength:
                2000
        }
    };

    /* ========================================================
       ÉTAT
       ======================================================== */

    const state = {

        initialized:
            false,

        submitting:
            false,

        originalRemaining:
            0,

        maxAmount:
            0
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

            let normalized =
                String(value)
                    .trim()
                    .replace(
                        /\s/g,
                        ""
                    );

            /*
             * Support :
             * 150000
             * 150000.50
             * 150000,50
             */
            if (
                normalized.includes(",") &&
                normalized.includes(".")
            ) {

                if (
                    normalized.lastIndexOf(",") >
                    normalized.lastIndexOf(".")
                ) {

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

            } else {

                normalized =
                    normalized.replace(
                        ",",
                        "."
                    );
            }

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

        round(value) {

            return Math.round(
                (
                    value +
                    Number.EPSILON
                ) *
                    100
            ) / 100;
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

        money(value) {

            const number =
                utils.round(
                    utils.number(
                        value
                    )
                );

            return new Intl.NumberFormat(
                "fr-FR",
                {
                    minimumFractionDigits:
                        CONFIG.financial.decimals,

                    maximumFractionDigits:
                        CONFIG.financial.decimals
                }
            ).format(
                number
            );
        },

        today() {

            const date =
                new Date();

            const year =
                date.getFullYear();

            const month =
                String(
                    date.getMonth() + 1
                ).padStart(
                    2,
                    "0"
                );

            const day =
                String(
                    date.getDate()
                ).padStart(
                    2,
                    "0"
                );

            return `${year}-${month}-${day}`;
        },

        isInput(
            element
        ) {

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
       ÉLÉMENTS FINANCIERS
       ======================================================== */

    const financial = {

        init() {

            const amount =
                dom.find(
                    CONFIG.selectors.amountInput,
                    dom.root
                );

            if (!amount) {
                return;
            }

            /*
             * Récupération du maximum transmis
             * par Laravel.
             */
            state.maxAmount =
                this.readMaximum(
                    amount
                );

            /*
             * Le reste actuel est généralement
             * fourni dans data-remaining.
             */
            state.originalRemaining =
                this.readRemaining();

            /*
             * Événement montant.
             */
            amount.addEventListener(
                "input",
                () => {

                    this.onAmountInput(
                        amount
                    );
                }
            );

            amount.addEventListener(
                "blur",
                () => {

                    this.normalizeInput(
                        amount
                    );

                    this.updatePreview(
                        amount
                    );
                }
            );

            amount.addEventListener(
                "focus",
                () => {

                    amount.select();
                }
            );

            this.updatePreview(
                amount
            );
        },

        readMaximum(
            amountInput
        ) {

            const value =
                amountInput.dataset.maxAmount ??
                dom.find(
                    CONFIG.selectors.maxAmount,
                    dom.root
                )?.dataset.value ??
                dom.find(
                    CONFIG.selectors.maxAmount,
                    dom.root
                )?.textContent;

            return Math.max(
                0,
                utils.number(
                    value
                )
            );
        },

        readRemaining() {

            const element =
                dom.find(
                    CONFIG.selectors.remaining,
                    dom.root
                );

            if (!element) {
                return 0;
            }

            return Math.max(
                0,
                utils.number(
                    element.dataset.value ??
                    element.textContent
                )
            );
        },

        readNet() {

            const element =
                dom.find(
                    CONFIG.selectors.netSalary,
                    dom.root
                );

            if (!element) {
                return 0;
            }

            return Math.max(
                0,
                utils.number(
                    element.dataset.value ??
                    element.textContent
                )
            );
        },

        readTotalPaid() {

            const element =
                dom.find(
                    CONFIG.selectors.totalPaid,
                    dom.root
                );

            if (!element) {
                return 0;
            }

            return Math.max(
                0,
                utils.number(
                    element.dataset.value ??
                    element.textContent
                )
            );
        },

        onAmountInput(
            input
        ) {

            const amount =
                utils.number(
                    input.value
                );

            this.clearError(
                input
            );

            if (
                amount >
                state.maxAmount
            ) {

                this.setError(
                    input,
                    `Le montant ne peut pas dépasser ${utils.money(state.maxAmount)}.`
                );
            }

            if (
                amount <
                    CONFIG.financial.minimum &&
                input.value !== ""
            ) {

                this.setError(
                    input,
                    `Le montant minimum est ${utils.money(CONFIG.financial.minimum)}.`
                );
            }

            this.updatePreview(
                input
            );
        },

        normalizeInput(
            input
        ) {

            const amount =
                utils.number(
                    input.value
                );

            if (!Number.isFinite(amount)) {

                input.value =
                    "";

                return;
            }

            input.value =
                amount
                    .toFixed(
                        CONFIG.financial.decimals
                    );
        },

        updatePreview(
            input
        ) {

            const amount =
                Math.max(
                    0,
                    utils.number(
                        input.value
                    )
                );

            /*
             * Pour l'UX :
             * on empêche le calcul d'afficher
             * un reste négatif.
             */
            const remainingAfter =
                Math.max(
                    0,
                    utils.round(
                        state.originalRemaining -
                        amount
                    )
                );

            const percentage =
                state.originalRemaining > 0
                    ? utils.clamp(
                        (
                            (
                                amount /
                                state.originalRemaining
                            ) *
                            100
                        ),
                        0,
                        100
                    )
                    : 0;

            this.renderRemaining(
                remainingAfter
            );

            this.renderPercentage(
                percentage
            );

            this.renderPreviewState(
                input,
                amount
            );
        },

        renderRemaining(
            value
        ) {

            const elements =
                dom.findAll(
                    CONFIG.selectors.remainingAfter,
                    dom.root
                );

            elements.forEach(
                (element) => {

                    element.textContent =
                        utils.money(
                            value
                        );

                    element.dataset.value =
                        String(
                            value
                        );
                }
            );
        },

        renderPercentage(
            value
        ) {

            const percentage =
                utils.clamp(
                    value,
                    0,
                    100
                );

            const progressElements =
                dom.findAll(
                    CONFIG.selectors.progress,
                    dom.root
                );

            progressElements.forEach(
                (element) => {

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

            const percentageElements =
                dom.findAll(
                    CONFIG.selectors.percentage,
                    dom.root
                );

            percentageElements.forEach(
                (element) => {

                    element.textContent =
                        `${percentage.toFixed(1)} %`;
                }
            );
        },

        renderPreviewState(
            input,
            amount
        ) {

            const exceeds =
                amount >
                state.maxAmount;

            const empty =
                amount <= 0;

            input.classList.toggle(
                "is-invalid",
                exceeds
            );

            input.classList.toggle(
                "is-empty",
                empty
            );

            const form =
                dom.find(
                    CONFIG.selectors.form,
                    dom.root
                );

            if (form) {

                form.classList.toggle(
                    "has-amount-error",
                    exceeds
                );
            }
        },

        setError(
            input,
            message
        ) {

            input.classList.add(
                "is-invalid"
            );

            input.setAttribute(
                "aria-invalid",
                "true"
            );

            const container =
                input.closest(
                    "[data-field]"
                ) ||
                input.parentElement;

            if (!container) {
                return;
            }

            let error =
                container.querySelector(
                    CONFIG.selectors.error
                );

            if (!error) {

                error =
                    document.createElement(
                        "div"
                    );

                error.className =
                    "field-error";

                error.dataset.fieldError =
                    "";

                container.appendChild(
                    error
                );
            }

            error.textContent =
                message;

            error.hidden =
                false;
        },

        clearError(
            input
        ) {

            input.classList.remove(
                "is-invalid"
            );

            input.removeAttribute(
                "aria-invalid"
            );

            const container =
                input.closest(
                    "[data-field]"
                ) ||
                input.parentElement;

            if (!container) {
                return;
            }

            const error =
                container.querySelector(
                    CONFIG.selectors.error
                );

            if (error) {

                error.textContent =
                    "";

                error.hidden =
                    true;
            }
        },

        validate() {

            const input =
                dom.find(
                    CONFIG.selectors.amountInput,
                    dom.root
                );

            if (!input) {
                return false;
            }

            const amount =
                utils.number(
                    input.value
                );

            this.clearError(
                input
            );

            if (
                !Number.isFinite(
                    amount
                ) ||
                amount <= 0
            ) {

                this.setError(
                    input,
                    "Veuillez saisir un montant de paiement valide."
                );

                input.focus();

                return false;
            }

            if (
                amount >
                state.maxAmount
            ) {

                this.setError(
                    input,
                    `Le montant maximum autorisé est de ${utils.money(state.maxAmount)}.`
                );

                input.focus();

                return false;
            }

            return true;
        }
    };

    /* ========================================================
       DATE
       ======================================================== */

    const dateField = {

        init() {

            const input =
                dom.find(
                    CONFIG.selectors.date,
                    dom.root
                );

            if (!input) {
                return;
            }

            /*
             * Si aucune date n'a été
             * fournie par old(), utiliser
             * aujourd'hui.
             */
            if (
                !input.value
            ) {

                input.value =
                    utils.today();
            }

            input.addEventListener(
                "change",
                () => {

                    this.validate(
                        input
                    );
                }
            );
        },

        validate(input) {

            if (!input.value) {

                this.setError(
                    input,
                    "La date du paiement est obligatoire."
                );

                return false;
            }

            this.clearError(
                input
            );

            return true;
        },

        setError(
            input,
            message
        ) {

            input.classList.add(
                "is-invalid"
            );

            input.setAttribute(
                "aria-invalid",
                "true"
            );

            const container =
                input.closest(
                    "[data-field]"
                ) ||
                input.parentElement;

            if (!container) {
                return;
            }

            let error =
                container.querySelector(
                    CONFIG.selectors.error
                );

            if (!error) {

                error =
                    document.createElement(
                        "div"
                    );

                error.className =
                    "field-error";

                error.dataset.fieldError =
                    "";

                container.appendChild(
                    error
                );
            }

            error.textContent =
                message;

            error.hidden =
                false;
        },

        clearError(input) {

            input.classList.remove(
                "is-invalid"
            );

            input.removeAttribute(
                "aria-invalid"
            );

            const container =
                input.closest(
                    "[data-field]"
                ) ||
                input.parentElement;

            if (!container) {
                return;
            }

            const error =
                container.querySelector(
                    CONFIG.selectors.error
                );

            if (error) {

                error.textContent =
                    "";

                error.hidden =
                    true;
            }
        }
    };

    /* ========================================================
       MODE DE PAIEMENT
       ======================================================== */

    const paymentMode = {

        init() {

            const input =
                dom.find(
                    CONFIG.selectors.mode,
                    dom.root
                );

            if (!input) {
                return;
            }

            input.addEventListener(
                "change",
                () => {

                    input.classList.remove(
                        "is-invalid"
                    );
                }
            );
        },

        validate() {

            const input =
                dom.find(
                    CONFIG.selectors.mode,
                    dom.root
                );

            if (!input) {
                return false;
            }

            if (!input.value) {

                input.classList.add(
                    "is-invalid"
                );

                input.focus();

                return false;
            }

            return true;
        }
    };

    /* ========================================================
       NOTES
       ======================================================== */

    const notes = {

        init() {

            const textarea =
                dom.find(
                    CONFIG.selectors.notes,
                    dom.root
                );

            if (!textarea) {
                return;
            }

            /*
             * Respecter le maxlength HTML
             * même si la Blade n'en contient
             * pas encore.
             */
            if (
                !textarea.maxLength ||
                textarea.maxLength < 0
            ) {

                textarea.maxLength =
                    CONFIG.notes.maxLength;
            }

            textarea.addEventListener(
                "input",
                () => {

                    this.update(
                        textarea
                    );
                }
            );

            this.update(
                textarea
            );
        },

        update(textarea) {

            const max =
                textarea.maxLength > 0
                    ? textarea.maxLength
                    : CONFIG.notes.maxLength;

            if (
                textarea.value.length >
                max
            ) {

                textarea.value =
                    textarea.value.substring(
                        0,
                        max
                    );
            }

            const counter =
                dom.find(
                    CONFIG.selectors.notesCounter,
                    dom.root
                );

            if (!counter) {
                return;
            }

            const current =
                textarea.value.length;

            counter.textContent =
                `${current} / ${max}`;

            counter.classList.toggle(
                "is-near-limit",
                current >=
                    Math.floor(
                        max * 0.9
                    )
            );

            counter.classList.toggle(
                "is-limit-reached",
                current >= max
            );
        }
    };

    /* ========================================================
       COPIE
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

                    document.body.appendChild(
                        textarea
                    );

                    textarea.select();

                    document.execCommand(
                        "copy"
                    );

                    textarea.remove();
                }

                this.feedback(
                    element
                );

            } catch (
                error
            ) {

                console.error(
                    "Erreur lors de la copie.",
                    error
                );
            }
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
                1500
            );
        }
    };

    /* ========================================================
       SOUMISSION DU PAIEMENT
       ======================================================== */

    const submission = {

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
                        state.submitting
                    ) {

                        event.preventDefault();

                        return;
                    }

                    /*
                     * Validation UX.
                     */
                    if (
                        !financial.validate()
                    ) {

                        event.preventDefault();

                        return;
                    }

                    if (
                        !paymentMode.validate()
                    ) {

                        event.preventDefault();

                        return;
                    }

                    const date =
                        dom.find(
                            CONFIG.selectors.date,
                            dom.root
                        );

                    if (
                        date &&
                        !dateField.validate(
                            date
                        )
                    ) {

                        event.preventDefault();

                        return;
                    }

                    /*
                     * Confirmation finale.
                     */
                    const amount =
                        utils.number(
                            dom.find(
                                CONFIG.selectors.amountInput,
                                dom.root
                            )?.value
                        );

                    const confirmationMessage =
                        form.dataset.confirmMessage ||
                        `Confirmez-vous le paiement de ${utils.money(amount)} ?`;

                    if (
                        !window.confirm(
                            confirmationMessage
                        )
                    ) {

                        event.preventDefault();

                        return;
                    }

                    this.lock(
                        form
                    );
                }
            );
        },

        lock(form) {

            state.submitting =
                true;

            form.classList.add(
                "is-submitting"
            );

            document.body.classList.add(
                "payment-submitting"
            );

            const buttons =
                form.querySelectorAll(
                    "button, input[type='submit']"
                );

            buttons.forEach(
                (button) => {

                    button.disabled =
                        true;

                    button.setAttribute(
                        "aria-busy",
                        "true"
                    );
                }
            );

            /*
             * Le bouton principal reçoit
             * un état visuel si le CSS
             * le prévoit.
             */
            const submit =
                dom.find(
                    CONFIG.selectors.submit,
                    form
                );

            if (submit) {

                submit.classList.add(
                    "is-loading"
                );

                submit.dataset.originalText =
                    submit.textContent;

                submit.textContent =
                    "Enregistrement...";
            }
        }
    };

    /* ========================================================
       ANNULATION / RETOUR
       ======================================================== */

    const navigation = {

        init() {

            const cancel =
                dom.find(
                    CONFIG.selectors.cancel,
                    dom.root
                );

            if (!cancel) {
                return;
            }

            cancel.addEventListener(
                "click",
                () => {

                    /*
                     * Aucun traitement spécial
                     * si c'est un lien normal.
                     */
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

            this.inputs();

            this.links();
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

        inputs() {

            const inputs =
                dom.findAll(
                    "input, select, textarea",
                    dom.root
                );

            inputs.forEach(
                (input) => {

                    if (
                        input.required
                    ) {

                        input.setAttribute(
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
                    'a[target="_blank"]',
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
        }
    };

    /* ========================================================
       PROTECTION CONTRE LA SORTIE ACCIDENTELLE
       ======================================================== */

    const unload = {

        init() {

            /*
             * On ne bloque pas la sortie pendant
             * toute la saisie : cela deviendrait
             * gênant.
             *
             * On protège uniquement lorsque
             * le formulaire est en cours
             * d'envoi.
             */
            window.addEventListener(
                "beforeunload",
                (event) => {

                    if (
                        !state.submitting
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

        financial.init();

        dateField.init();

        paymentMode.init();

        notes.init();

        clipboard.init();

        submission.init();

        navigation.init();

        accessibility.init();

        unload.init();

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

    window.PHOVE.salaryPaymentCreate = {

        refresh() {

            const input =
                dom.find(
                    CONFIG.selectors.amountInput,
                    dom.root
                );

            if (input) {

                financial.updatePreview(
                    input
                );
            }
        },

        validate() {

            return (
                financial.validate() &&
                paymentMode.validate()
            );
        },

        getMaximum() {

            return state.maxAmount;
        },

        getRemaining() {

            return state.originalRemaining;
        },

        isSubmitting() {

            return state.submitting;
        }
    };

})();