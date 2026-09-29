
/**
 * ============================================================
 * PHOVE — ADMINISTRATION
 * MODULE : SALAIRES
 * VUE    : CRÉATION D'UN SALAIRE
 * FICHIER: create.js
 * ============================================================
 *
 * Responsabilités :
 * - Calcul dynamique du salaire net
 * - Mise à jour de la prévisualisation financière
 * - Prévisualisation du membre sélectionné
 * - Gestion du mois
 * - Gestion des champs monétaires
 * - Validation UX du formulaire
 * - Compteur de caractères
 * - Protection contre les doubles soumissions
 * - Gestion de l'état de chargement
 * - Accessibilité
 *
 * IMPORTANT :
 * Le calcul JavaScript est uniquement indicatif.
 * Le calcul définitif doit être effectué côté Laravel
 * par SalaireService.
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
            root: ".salary-create",
            form: "#salaryCreateForm",

            member: "#membre_id",
            organ: "#organe_id",
            month: "#mois",
            reference: "#reference",

            base: "#salaire_base",
            primes: "#primes",
            retenues: "#retenues",

            notes: "#notes",

            previewBase: "#previewBase",
            previewPrimes: "#previewPrimes",
            previewRetenues: "#previewRetenues",
            previewNet: "#previewNet",
            netPreview: "#netPreview",

            memberSummary: "#memberSummary",
            memberAvatar: "#memberAvatar",
            memberName: "#memberName",
            memberMeta: "#memberMeta",

            summaryMonth: "#summaryMonth",
            summaryBase: "#summaryBase",
            summaryPrimes: "#summaryPrimes",
            summaryRetenues: "#summaryRetenues",
            summaryNet: "#summaryNet",

            notesCounter: "#notesCounter",

            reset: "#resetSalaryForm",
            submit: "#submitSalaryForm",

            calculationBox: ".calculation-box"
        },

        limits: {
            amount: {
                min: 0,
                max: 999999999999.99
            },

            notes: 1000
        },

        currency: "USD"
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

        find(selector, context = document) {
            if (!selector) {
                return null;
            }

            return context.querySelector(selector);
        },

        findAll(selector, context = document) {
            if (!selector) {
                return [];
            }

            return Array.from(
                context.querySelectorAll(selector)
            );
        }
    };


    /* ========================================================
       RÉFÉRENCE DES CHAMPS
       ======================================================== */

    const fields = {
        form: null,

        member: null,
        organ: null,
        month: null,
        reference: null,

        base: null,
        primes: null,
        retenues: null,

        notes: null,

        previewBase: null,
        previewPrimes: null,
        previewRetenues: null,
        previewNet: null,
        netPreview: null,

        memberSummary: null,
        memberAvatar: null,
        memberName: null,
        memberMeta: null,

        summaryMonth: null,
        summaryBase: null,
        summaryPrimes: null,
        summaryRetenues: null,
        summaryNet: null,

        notesCounter: null,

        reset: null,
        submit: null,

        calculationBox: null,

        init() {
            this.form = dom.find(
                CONFIG.selectors.form,
                dom.root
            );

            this.member = dom.find(
                CONFIG.selectors.member,
                dom.root
            );

            this.organ = dom.find(
                CONFIG.selectors.organ,
                dom.root
            );

            this.month = dom.find(
                CONFIG.selectors.month,
                dom.root
            );

            this.reference = dom.find(
                CONFIG.selectors.reference,
                dom.root
            );

            this.base = dom.find(
                CONFIG.selectors.base,
                dom.root
            );

            this.primes = dom.find(
                CONFIG.selectors.primes,
                dom.root
            );

            this.retenues = dom.find(
                CONFIG.selectors.retenues,
                dom.root
            );

            this.notes = dom.find(
                CONFIG.selectors.notes,
                dom.root
            );

            this.previewBase = dom.find(
                CONFIG.selectors.previewBase,
                dom.root
            );

            this.previewPrimes = dom.find(
                CONFIG.selectors.previewPrimes,
                dom.root
            );

            this.previewRetenues = dom.find(
                CONFIG.selectors.previewRetenues,
                dom.root
            );

            this.previewNet = dom.find(
                CONFIG.selectors.previewNet,
                dom.root
            );

            this.netPreview = dom.find(
                CONFIG.selectors.netPreview,
                dom.root
            );

            this.memberSummary = dom.find(
                CONFIG.selectors.memberSummary,
                dom.root
            );

            this.memberAvatar = dom.find(
                CONFIG.selectors.memberAvatar,
                dom.root
            );

            this.memberName = dom.find(
                CONFIG.selectors.memberName,
                dom.root
            );

            this.memberMeta = dom.find(
                CONFIG.selectors.memberMeta,
                dom.root
            );

            this.summaryMonth = dom.find(
                CONFIG.selectors.summaryMonth,
                dom.root
            );

            this.summaryBase = dom.find(
                CONFIG.selectors.summaryBase,
                dom.root
            );

            this.summaryPrimes = dom.find(
                CONFIG.selectors.summaryPrimes,
                dom.root
            );

            this.summaryRetenues = dom.find(
                CONFIG.selectors.summaryRetenues,
                dom.root
            );

            this.summaryNet = dom.find(
                CONFIG.selectors.summaryNet,
                dom.root
            );

            this.notesCounter = dom.find(
                CONFIG.selectors.notesCounter,
                dom.root
            );

            this.reset = dom.find(
                CONFIG.selectors.reset,
                dom.root
            );

            this.submit = dom.find(
                CONFIG.selectors.submit,
                dom.root
            );

            this.calculationBox = dom.find(
                CONFIG.selectors.calculationBox,
                dom.root
            );
        }
    };


    /* ========================================================
       UTILITAIRES
       ======================================================== */

    const utils = {

        /**
         * Vérifie qu'un élément DOM est valide.
         */
        isElement(element) {
            return element instanceof HTMLElement;
        },


        /**
         * Convertit une valeur monétaire en nombre.
         *
         * Exemples :
         * 500
         * 500.50
         * 500,50
         * 1 500,50
         * 1.500,50
         * 1,500.50
         */
        parseMoney(value) {
            if (
                value === null ||
                value === undefined ||
                value === ""
            ) {
                return 0;
            }

            let normalized = String(value)
                .trim()
                .replace(/\s/g, "")
                .replace(/[^\d,.-]/g, "");

            if (!normalized) {
                return 0;
            }

            /*
             * Deux séparateurs présents.
             */
            if (
                normalized.includes(",") &&
                normalized.includes(".")
            ) {
                const lastComma =
                    normalized.lastIndexOf(",");

                const lastDot =
                    normalized.lastIndexOf(".");

                /*
                 * Format européen :
                 * 1.500,50
                 */
                if (lastComma > lastDot) {
                    normalized = normalized
                        .replace(/\./g, "")
                        .replace(",", ".");
                }

                /*
                 * Format anglo-saxon :
                 * 1,500.50
                 */
                else {
                    normalized = normalized
                        .replace(/,/g, "");
                }
            }

            /*
             * Virgule seule :
             * 500,50
             */
            else if (normalized.includes(",")) {
                normalized = normalized.replace(
                    ",",
                    "."
                );
            }

            const number =
                Number.parseFloat(normalized);

            return Number.isFinite(number)
                ? number
                : 0;
        },


        /**
         * Formate un montant en français.
         */
        formatMoney(value) {
            const number =
                Number.isFinite(Number(value))
                    ? Number(value)
                    : 0;

            return new Intl.NumberFormat(
                "fr-FR",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            ).format(number);
        },


        /**
         * Formate un montant avec devise.
         */
        formatCurrency(value) {
            return `${this.formatMoney(value)} ${CONFIG.currency}`;
        },


        /**
         * Génère une référence temporaire côté interface.
         *
         * IMPORTANT :
         * Cette référence UX n'est PAS une sécurité.
         * Le serveur doit générer/valider la référence définitive.
         */
        generateReference() {
            const now = new Date();

            const year =
                now.getFullYear();

            const month =
                String(now.getMonth() + 1)
                    .padStart(2, "0");

            const day =
                String(now.getDate())
                    .padStart(2, "0");

            const random =
                Math.floor(
                    1000 + Math.random() * 9000
                );

            return `SAL-${year}${month}${day}-${random}`;
        },


        /**
         * Définit le texte d'un élément.
         */
        setText(element, value) {
            if (!element) {
                return;
            }

            element.textContent = value;
        },


        /**
         * Retourne la valeur numérique d'un champ.
         */
        getFieldMoney(field) {
            return this.parseMoney(
                field?.value
            );
        }
    };


    /* ========================================================
       CALCUL DU SALAIRE
       ======================================================== */

    const calculator = {

        init() {
            const moneyFields = [
                fields.base,
                fields.primes,
                fields.retenues
            ];

            moneyFields.forEach((field) => {
                if (!field) {
                    return;
                }

                field.addEventListener(
                    "input",
                    () => {
                        this.update();
                    }
                );

                field.addEventListener(
                    "change",
                    () => {
                        this.update();
                    }
                );

                field.addEventListener(
                    "blur",
                    () => {
                        moneyInputs.format(field);
                        this.update();
                    }
                );
            });

            this.update();
        },


        getValues() {
            return {
                base: utils.getFieldMoney(
                    fields.base
                ),

                primes: utils.getFieldMoney(
                    fields.primes
                ),

                retenues: utils.getFieldMoney(
                    fields.retenues
                )
            };
        },


        calculate() {
            const values =
                this.getValues();

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

            this.updateDisplay(result);
            this.updateState(result);
        },


        updateDisplay(result) {
            const displayedNet =
                Math.max(
                    result.net,
                    0
                );

            /*
             * Bloc de calcul principal.
             */
            utils.setText(
                fields.previewBase,
                utils.formatCurrency(
                    result.base
                )
            );

            utils.setText(
                fields.previewPrimes,
                utils.formatCurrency(
                    result.primes
                )
            );

            utils.setText(
                fields.previewRetenues,
                utils.formatCurrency(
                    result.retenues
                )
            );

            utils.setText(
                fields.previewNet,
                utils.formatCurrency(
                    displayedNet
                )
            );


            /*
             * Aperçu du montant net.
             */
            if (fields.netPreview) {
                fields.netPreview.innerHTML = `
                    ${utils.formatMoney(displayedNet)}
                    <small>${CONFIG.currency}</small>
                `;
            }


            /*
             * Résumé latéral.
             */
            utils.setText(
                fields.summaryBase,
                utils.formatCurrency(
                    result.base
                )
            );

            utils.setText(
                fields.summaryPrimes,
                utils.formatCurrency(
                    result.primes
                )
            );

            utils.setText(
                fields.summaryRetenues,
                utils.formatCurrency(
                    result.retenues
                )
            );

            utils.setText(
                fields.summaryNet,
                utils.formatCurrency(
                    displayedNet
                )
            );
        },


        updateState(result) {
            if (!fields.calculationBox) {
                return;
            }

            fields.calculationBox.classList.remove(
                "is-valid",
                "is-invalid"
            );

            /*
             * Le net négatif est invalide.
             */
            if (result.net < 0) {
                fields.calculationBox.classList.add(
                    "is-invalid"
                );

                fields.calculationBox.setAttribute(
                    "aria-invalid",
                    "true"
                );

                return;
            }

            fields.calculationBox.classList.add(
                "is-valid"
            );

            fields.calculationBox.setAttribute(
                "aria-invalid",
                "false"
            );
        }
    };


    /* ========================================================
       CHAMPS MONÉTAIRES
       ======================================================== */

    const moneyInputs = {

        init() {
            const inputs = [
                fields.base,
                fields.primes,
                fields.retenues
            ].filter(Boolean);

            inputs.forEach((input) => {
                this.bind(input);
            });
        },


        bind(input) {
            input.addEventListener(
                "input",
                () => {
                    this.clean(input);
                }
            );

            input.addEventListener(
                "blur",
                () => {
                    this.format(input);
                }
            );

            input.addEventListener(
                "keydown",
                (event) => {
                    this.handleKeydown(
                        event,
                        input
                    );
                }
            );
        },


        clean(input) {
            let value =
                String(input.value);

            /*
             * Autorise uniquement :
             * chiffres
             * virgule
             * point
             */
            value = value.replace(
                /[^\d.,]/g,
                ""
            );

            /*
             * Un seul séparateur décimal.
             */
            const firstSeparator =
                value.search(/[.,]/);

            if (firstSeparator !== -1) {
                const before =
                    value.substring(
                        0,
                        firstSeparator + 1
                    );

                const after =
                    value
                        .substring(
                            firstSeparator + 1
                        )
                        .replace(
                            /[.,]/g,
                            ""
                        );

                value =
                    before +
                    after.substring(0, 2);
            }

            input.value = value;
        },


        format(input) {
            if (!input || input.value === "") {
                return;
            }

            const value =
                utils.parseMoney(
                    input.value
                );

            input.value =
                value.toFixed(2);
        },


        handleKeydown(event) {
            const allowedKeys = [
                "Backspace",
                "Delete",
                "Tab",
                "Escape",
                "Enter",
                "ArrowLeft",
                "ArrowRight",
                "ArrowUp",
                "ArrowDown",
                "Home",
                "End"
            ];

            if (
                allowedKeys.includes(
                    event.key
                )
            ) {
                return;
            }

            /*
             * Raccourcis clavier :
             * Ctrl+C
             * Ctrl+V
             * Ctrl+A
             * Ctrl+X
             */
            if (
                event.ctrlKey ||
                event.metaKey
            ) {
                return;
            }

            /*
             * Chiffres.
             */
            if (/^\d$/.test(event.key)) {
                return;
            }

            /*
             * Séparateurs.
             */
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
       PRÉVISUALISATION DU MEMBRE
       ======================================================== */

    const memberPreview = {

        init() {
            if (!fields.member) {
                return;
            }

            fields.member.addEventListener(
                "change",
                () => {
                    this.update();
                }
            );

            this.update();
        },


        update() {
            if (!fields.member) {
                return;
            }

            const selectedOption =
                fields.member.options[
                    fields.member.selectedIndex
                ];

            /*
             * Aucun membre.
             */
            if (
                !selectedOption ||
                !selectedOption.value
            ) {
                utils.setText(
                    fields.memberName,
                    "Aucun membre sélectionné"
                );

                utils.setText(
                    fields.memberMeta,
                    "Sélectionnez un membre pour afficher son résumé."
                );

                this.resetAvatar();

                return;
            }

            const nom =
                selectedOption.dataset.nom || "";

            const postNom =
                selectedOption.dataset.postNom || "";

            const prenom =
                selectedOption.dataset.prenom || "";

            const telephone =
                selectedOption.dataset.telephone || "";

            const photo =
                selectedOption.dataset.photo || "";


            /*
             * Nom complet.
             */
            const fullName = [
                nom,
                postNom,
                prenom
            ]
                .filter(Boolean)
                .join(" ")
                .trim();


            utils.setText(
                fields.memberName,
                fullName || "Membre sélectionné"
            );


            /*
             * Informations secondaires.
             */
            utils.setText(
                fields.memberMeta,
                telephone
                    ? telephone
                    : "Membre du personnel"
            );


            /*
             * Photo ou initiale.
             */
            if (photo) {
                this.setAvatarPhoto(photo);
            } else {
                this.setAvatarInitial(
                    fullName
                );
            }
        },


        resetAvatar() {
            if (!fields.memberAvatar) {
                return;
            }

            fields.memberAvatar.innerHTML =
                '<i class="fas fa-user"></i>';
        },


        setAvatarPhoto(photo) {
            if (!fields.memberAvatar) {
                return;
            }

            /*
             * On accepte une URL absolue ou un chemin
             * fourni par le backend.
             */
            const image =
                document.createElement("img");

            image.src = photo;
            image.alt = "Photo du membre";
            image.loading = "lazy";

            image.addEventListener(
                "error",
                () => {
                    const name =
                        fields.memberName?.textContent ||
                        "";

                    this.setAvatarInitial(name);
                },
                {
                    once: true
                }
            );

            fields.memberAvatar.replaceChildren(
                image
            );
        },


        setAvatarInitial(name) {
            if (!fields.memberAvatar) {
                return;
            }

            const words =
                String(name)
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean);

            let initials = "";

            if (words.length >= 2) {
                initials =
                    words[0].charAt(0) +
                    words[1].charAt(0);
            } else if (words.length === 1) {
                initials =
                    words[0].substring(0, 2);
            }

            initials =
                initials.toUpperCase();

            fields.memberAvatar.textContent =
                initials || "M";
        }
    };


    /* ========================================================
       GESTION DU MOIS
       ======================================================== */

    const monthPreview = {

        init() {
            if (!fields.month) {
                return;
            }

            fields.month.addEventListener(
                "change",
                () => {
                    this.update();
                }
            );

            fields.month.addEventListener(
                "input",
                () => {
                    this.update();
                }
            );

            this.update();
        },


        update() {
            if (!fields.summaryMonth) {
                return;
            }

            const month =
                fields.month?.value || "";

            fields.summaryMonth.textContent =
                month || "—";
        }
    };


    /* ========================================================
       COMPTEUR DE CARACTÈRES
       ======================================================== */

    const characterCounter = {

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
            if (!fields.notesCounter) {
                return;
            }

            const max =
                Number(fields.notes.maxLength) ||
                CONFIG.limits.notes;

            const length =
                fields.notes.value.length;

            fields.notesCounter.textContent =
                `${length} / ${max}`;

            fields.notesCounter.classList.toggle(
                "is-warning",
                length >= max * 0.8
            );

            fields.notesCounter.classList.toggle(
                "is-danger",
                length >= max
            );

            fields.notesCounter.setAttribute(
                "aria-live",
                "polite"
            );
        }
    };


    /* ========================================================
       RÉFÉRENCE
       ======================================================== */

    const referenceManager = {

        init() {
            if (!fields.reference) {
                return;
            }

            /*
             * Si Laravel a déjà fourni une référence
             * via old(), on la conserve.
             *
             * Sinon, génération d'une référence UX.
             */
            if (!fields.reference.value) {
                fields.reference.value =
                    utils.generateReference();
            }
        }
    };


    /* ========================================================
       VALIDATION UX
       ======================================================== */

    const validation = {

        init() {
            if (!fields.form) {
                return;
            }

            fields.form.addEventListener(
                "submit",
                (event) => {
                    if (!this.validate()) {
                        event.preventDefault();

                        const firstInvalid =
                            fields.form.querySelector(
                                ":invalid"
                            );

                        if (firstInvalid) {
                            firstInvalid.focus();
                        }

                        return;
                    }

                    submission.lock();
                }
            );


            /*
             * Validation au blur.
             */
            [
                fields.member,
                fields.organ,
                fields.month,
                fields.base,
                fields.primes,
                fields.retenues
            ].forEach((field) => {
                if (!field) {
                    return;
                }

                field.addEventListener(
                    "blur",
                    () => {
                        this.validateField(field);
                    }
                );
            });
        },


        validate() {
            if (!fields.form) {
                return false;
            }

            let valid =
                fields.form.checkValidity();


            const result =
                calculator.calculate();


            /*
             * Le net ne peut pas être négatif.
             */
            if (result.net < 0) {
                valid = false;

                this.markInvalid(
                    fields.retenues,
                    "Les retenues ne peuvent pas dépasser le total du salaire."
                );
            }


            /*
             * Salaire de base.
             */
            if (
                result.base <
                    CONFIG.limits.amount.min ||
                result.base >
                    CONFIG.limits.amount.max
            ) {
                valid = false;

                this.markInvalid(
                    fields.base,
                    "Le salaire de base n'est pas valide."
                );
            }


            /*
             * Primes.
             */
            if (
                result.primes <
                    CONFIG.limits.amount.min ||
                result.primes >
                    CONFIG.limits.amount.max
            ) {
                valid = false;

                this.markInvalid(
                    fields.primes,
                    "Le montant des primes n'est pas valide."
                );
            }


            /*
             * Retenues.
             */
            if (
                result.retenues <
                    CONFIG.limits.amount.min ||
                result.retenues >
                    CONFIG.limits.amount.max
            ) {
                valid = false;

                this.markInvalid(
                    fields.retenues,
                    "Le montant des retenues n'est pas valide."
                );
            }

            return valid;
        },


        validateField(field) {
            if (!field) {
                return true;
            }

            if (field.checkValidity()) {
                this.clearInvalid(field);

                return true;
            }

            this.markInvalid(
                field,
                field.validationMessage
            );

            return false;
        },


        markInvalid(field, message) {
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

            /*
             * Cherche le conteneur du champ.
             */
            const container =
                field.closest(".form-group");


            /*
             * Cherche un message d'erreur existant.
             */
            const existingError =
                container?.querySelector(
                    ".field-error"
                );


            if (existingError) {
                existingError.textContent =
                    message;

                return;
            }


            /*
             * Création d'un message UX uniquement
             * si Laravel n'en a pas déjà fourni.
             */
            if (container && message) {
                const error =
                    document.createElement("span");

                error.className =
                    "field-error js-field-error";

                error.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    ${this.escapeHtml(message)}
                `;

                container.appendChild(error);
            }
        },


        clearInvalid(field) {
            if (!field) {
                return;
            }

            field.classList.remove(
                "is-invalid"
            );

            field.setAttribute(
                "aria-invalid",
                "false"
            );


            const container =
                field.closest(".form-group");


            const jsError =
                container?.querySelector(
                    ".js-field-error"
                );


            if (jsError) {
                jsError.remove();
            }
        },


        escapeHtml(value) {
            const div =
                document.createElement("div");

            div.textContent =
                String(value);

            return div.innerHTML;
        }
    };


    /* ========================================================
       SOUMISSION
       ======================================================== */

    const submission = {

        lock() {
            if (
                state.submitting ||
                !fields.form
            ) {
                return;
            }

            state.submitting = true;

            fields.form.dataset.submitting =
                "true";


            const buttons =
                fields.form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );


            buttons.forEach((button) => {
                button.disabled = true;

                button.classList.add(
                    "is-loading"
                );


                const loadingText =
                    button.dataset.loadingText ||
                    "Enregistrement...";


                if (
                    button.tagName ===
                    "BUTTON"
                ) {
                    button.dataset.originalHtml =
                        button.innerHTML;


                    button.innerHTML = `
                        <span
                            class="salary-create-spinner"
                            aria-hidden="true"
                        ></span>
                        <span>${loadingText}</span>
                    `;
                }
            });
        }
    };


    /* ========================================================
       RÉINITIALISATION
       ======================================================== */

    const reset = {

        init() {
            if (!fields.reset) {
                return;
            }

            fields.reset.addEventListener(
                "click",
                (event) => {
                    this.handle(event);
                }
            );
        },


        handle(event) {
            const confirmed =
                window.confirm(
                    "Voulez-vous vraiment réinitialiser le formulaire ?"
                );

            if (!confirmed) {
                event.preventDefault();

                return;
            }


            /*
             * Laisser le navigateur effectuer
             * le reset natif.
             *
             * On rafraîchit ensuite les prévisualisations.
             */
            window.setTimeout(() => {
                state.submitting = false;

                if (fields.form) {
                    fields.form.dataset.submitting =
                        "false";
                }

                calculator.update();
                memberPreview.update();
                monthPreview.update();
                characterCounter.update();

                /*
                 * Regénération d'une référence UX.
                 */
                if (fields.reference) {
                    fields.reference.value =
                        utils.generateReference();
                }

                /*
                 * Réactivation du bouton.
                 */
                if (fields.submit) {
                    fields.submit.disabled = false;

                    fields.submit.classList.remove(
                        "is-loading"
                    );

                    fields.submit.innerHTML = `
                        <i class="fas fa-save"></i>
                        <span class="btn-text">
                            Enregistrer le salaire
                        </span>
                    `;
                }
            }, 0);
        }
    };


    /* ========================================================
       ACCESSIBILITÉ
       ======================================================== */

    const accessibility = {

        init() {
            [
                fields.base,
                fields.primes,
                fields.retenues
            ].forEach((field) => {
                if (!field) {
                    return;
                }

                if (!field.hasAttribute("aria-invalid")) {
                    field.setAttribute(
                        "aria-invalid",
                        "false"
                    );
                }
            });


            if (
                fields.calculationBox &&
                !fields.calculationBox.hasAttribute(
                    "role"
                )
            ) {
                fields.calculationBox.setAttribute(
                    "role",
                    "status"
                );

                fields.calculationBox.setAttribute(
                    "aria-live",
                    "polite"
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


        dom.root =
            dom.find(
                CONFIG.selectors.root
            );


        /*
         * Le JS peut être chargé globalement.
         * Si la page n'est pas celle de création,
         * on ne fait rien.
         */
        if (!dom.root) {
            return;
        }


        fields.init();


        /*
         * Le formulaire doit exister.
         */
        if (!fields.form) {
            return;
        }


        referenceManager.init();

        moneyInputs.init();

        calculator.init();

        memberPreview.init();

        monthPreview.init();

        characterCounter.init();

        validation.init();

        reset.init();

        accessibility.init();


        state.initialized = true;

        dom.root.dataset.jsReady =
            "true";


        /*
         * Debug léger uniquement en développement.
         */
        if (
            window.location.hostname ===
            "127.0.0.1" ||
            window.location.hostname ===
            "localhost"
        ) {
            console.info(
                "PHOVE Salary Create : JS initialisé."
            );
        }
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
       API PUBLIQUE MINIMALE
       ======================================================== */

    window.PHOVE =
        window.PHOVE || {};

    window.PHOVE.salaryCreate = {

        calculate() {
            return calculator.calculate();
        },

        refresh() {
            calculator.update();
            memberPreview.update();
            monthPreview.update();
            characterCounter.update();
        }
    };

})();
