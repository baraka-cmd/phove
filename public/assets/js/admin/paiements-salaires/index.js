/**
 * ==========================================================================
 * PHOVE — INDEX DES PAIEMENTS DE SALAIRES
 * ==========================================================================
 *
 * Fichier :
 * public/assets/js/admin/paiements-salaires/index.js
 *
 * Responsabilités :
 * - gestion des filtres ;
 * - recherche avec délai ;
 * - soumission du formulaire ;
 * - réinitialisation des filtres ;
 * - impression ;
 * - copie des références ;
 * - feedback utilisateur ;
 * - protection contre les doubles soumissions ;
 * - gestion propre des états de chargement.
 *
 * Le JavaScript ne contient aucune logique financière.
 * Les montants et statistiques viennent exclusivement du serveur.
 * ==========================================================================
 */

'use strict';


/* ==========================================================================
   CONFIGURATION
   ========================================================================== */

const PHOVE_PAYMENT_INDEX = {
    selectors: {
        root: '.payment-index',

        filterForm: '[data-filter-form]',

        searchInput: '[data-search-input]',

        monthFilter: '[data-month-filter]',

        modeFilter: '[data-mode-filter]',

        dateFilter: '[data-date-filter]',

        autoSubmit: '[data-auto-submit]',

        submitFilter: '[data-submit-filter]',

        clearSearch: '[data-clear-search]',

        resetFilters: '[data-reset-filters]',

        print: '[data-print]',

        copy: '[data-copy]',

        paymentTable: '[data-payment-table]',

        paymentRow: '[data-payment-row]',

        globalLoader: '#globalLoader'
    },

    searchDelay: 450,

    loadingClass: 'is-loading',

    copiedClass: 'is-copied'
};


/* ==========================================================================
   ÉTAT
   ========================================================================== */

const paymentIndexState = {
    searchTimer: null,

    isSubmitting: false,

    isPrinting: false
};


/* ==========================================================================
   INITIALISATION
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initializePaymentIndex();
});


/**
 * Initialise toutes les fonctionnalités de la page.
 */
function initializePaymentIndex() {

    const root = document.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.root
    );

    /*
     * Si le script est chargé accidentellement sur une autre page,
     * on quitte silencieusement.
     */
    if (!root) {
        return;
    }

    initializeFilters(root);

    initializeSearch(root);

    initializeAutoSubmit(root);

    initializeFilterReset(root);

    initializePrint(root);

    initializeCopyButtons(root);

    initializeKeyboardNavigation(root);

    initializeTableAccessibility(root);
}


/* ==========================================================================
   FILTRES
   ========================================================================== */

/**
 * Initialise les événements des filtres.
 */
function initializeFilters(root) {

    const form = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.filterForm
    );

    if (!form) {
        return;
    }

    /*
     * Empêche les soumissions multiples très rapides.
     */
    form.addEventListener('submit', handleFilterSubmit);
}


/**
 * Gestion de la soumission du formulaire.
 */
function handleFilterSubmit(event) {

    const form = event.currentTarget;

    if (!form) {
        return;
    }

    /*
     * Si une soumission est déjà en cours,
     * on bloque la seconde.
     */
    if (paymentIndexState.isSubmitting) {
        event.preventDefault();
        return;
    }

    paymentIndexState.isSubmitting = true;

    form.classList.add(
        PHOVE_PAYMENT_INDEX.loadingClass
    );

    const submitButton = form.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.submitFilter
    );

    if (submitButton) {
        setButtonLoading(
            submitButton,
            'Recherche...',
            'fa-search'
        );
    }

    showGlobalLoader();
}


/* ==========================================================================
   RECHERCHE
   ========================================================================== */

/**
 * Initialise la recherche.
 *
 * La recherche n'est pas exécutée à chaque caractère.
 * Un délai évite de multiplier inutilement les requêtes.
 */
function initializeSearch(root) {

    const input = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.searchInput
    );

    if (!input) {
        return;
    }

    input.addEventListener('input', handleSearchInput);

    /*
     * Escape vide rapidement la recherche.
     */
    input.addEventListener('keydown', handleSearchKeyboard);
}


/**
 * Gestion de la saisie de recherche.
 */
function handleSearchInput(event) {

    const input = event.currentTarget;

    if (!input) {
        return;
    }

    clearTimeout(paymentIndexState.searchTimer);

    const value = input.value.trim();

    /*
     * On ne déclenche pas immédiatement une requête.
     */
    paymentIndexState.searchTimer = setTimeout(() => {

        /*
         * Recherche uniquement si :
         *
         * - au moins 2 caractères ;
         * - ou champ complètement vide.
         */
        if (value.length >= 2 || value.length === 0) {
            submitFilterForm(input);
        }

    }, PHOVE_PAYMENT_INDEX.searchDelay);
}


/**
 * Gestion du clavier dans la recherche.
 */
function handleSearchKeyboard(event) {

    /*
     * Escape :
     * vider la recherche.
     */
    if (event.key === 'Escape') {

        event.preventDefault();

        event.currentTarget.value = '';

        clearTimeout(
            paymentIndexState.searchTimer
        );

        /*
         * On ne soumet pas automatiquement immédiatement
         * pour éviter une navigation agressive.
         */
        event.currentTarget.focus();
    }

    /*
     * Entrée :
     * recherche immédiate.
     */
    if (event.key === 'Enter') {

        event.preventDefault();

        clearTimeout(
            paymentIndexState.searchTimer
        );

        submitFilterForm(event.currentTarget);
    }
}


/**
 * Soumet le formulaire associé à un élément.
 */
function submitFilterForm(element) {

    if (!element) {
        return;
    }

    const root = element.closest(
        PHOVE_PAYMENT_INDEX.selectors.root
    );

    if (!root) {
        return;
    }

    const form = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.filterForm
    );

    if (!form) {
        return;
    }

    if (paymentIndexState.isSubmitting) {
        return;
    }

    /*
     * Déclenche l'événement submit normal.
     */
    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
    } else {
        form.submit();
    }
}


/* ==========================================================================
   AUTO-SOUMISSION
   ========================================================================== */

/**
 * Initialise les champs possédant :
 *
 * data-auto-submit
 */
function initializeAutoSubmit(root) {

    const fields = root.querySelectorAll(
        PHOVE_PAYMENT_INDEX.selectors.autoSubmit
    );

    if (!fields.length) {
        return;
    }

    fields.forEach((field) => {

        field.addEventListener('change', () => {

            submitFilterForm(field);

        });

    });
}


/**
 * Les champs mois et date sont également soumis
 * lorsqu'ils changent.
 */
function initializeDateAndMonthAutoSubmit(root) {

    const fields = [
        PHOVE_PAYMENT_INDEX.selectors.monthFilter,
        PHOVE_PAYMENT_INDEX.selectors.dateFilter
    ];

    fields.forEach((selector) => {

        const elements = root.querySelectorAll(selector);

        elements.forEach((element) => {

            element.addEventListener('change', () => {
                submitFilterForm(element);
            });

        });

    });
}


/* ==========================================================================
   RÉINITIALISATION
   ========================================================================== */

/**
 * Initialise les boutons de réinitialisation.
 */
function initializeFilterReset(root) {

    /*
     * Bouton "Effacer".
     */
    const clearButton = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.clearSearch
    );

    if (clearButton) {

        clearButton.addEventListener(
            'click',
            handleClearFilters
        );

    }


    /*
     * Lien "Réinitialiser".
     */
    const resetButton = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.resetFilters
    );

    if (resetButton) {

        resetButton.addEventListener(
            'click',
            handleResetFilters
        );

    }
}


/**
 * Efface les champs sans forcément quitter immédiatement
 * la page.
 */
function handleClearFilters(event) {

    event.preventDefault();

    const button = event.currentTarget;

    const root = button.closest(
        PHOVE_PAYMENT_INDEX.selectors.root
    );

    if (!root) {
        return;
    }

    const form = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.filterForm
    );

    if (!form) {
        return;
    }

    /*
     * Effacement des champs.
     */
    const inputs = form.querySelectorAll(
        'input, select'
    );

    inputs.forEach((input) => {

        if (input.type === 'search'
            || input.type === 'text'
            || input.type === 'date'
            || input.type === 'month') {

            input.value = '';

        }

        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }

    });

    /*
     * Annulation du timer de recherche.
     */
    clearTimeout(
        paymentIndexState.searchTimer
    );

    /*
     * Retour au début.
     */
    const searchInput = root.querySelector(
        PHOVE_PAYMENT_INDEX.selectors.searchInput
    );

    if (searchInput) {
        searchInput.focus();
    }

    /*
     * On soumet ensuite le formulaire nettoyé.
     */
    submitFilterForm(form);
}


/**
 * Gestion du lien de réinitialisation complet.
 */
function handleResetFilters(event) {

    const link = event.currentTarget;

    if (!link) {
        return;
    }

    /*
     * Laisser le navigateur suivre le lien.
     *
     * On ajoute simplement l'état de chargement.
     */
    link.classList.add(
        PHOVE_PAYMENT_INDEX.loadingClass
    );

    showGlobalLoader();
}


/* ==========================================================================
   IMPRESSION
   ========================================================================== */

/**
 * Initialise tous les boutons possédant :
 *
 * data-print
 */
function initializePrint(root) {

    const printButtons = root.querySelectorAll(
        PHOVE_PAYMENT_INDEX.selectors.print
    );

    if (!printButtons.length) {
        return;
    }

    printButtons.forEach((button) => {

        button.addEventListener(
            'click',
            handlePrint
        );

    });
}


/**
 * Lance l'impression de la page actuelle.
 */
function handlePrint(event) {

    event.preventDefault();

    if (paymentIndexState.isPrinting) {
        return;
    }

    paymentIndexState.isPrinting = true;

    document.documentElement.classList.add(
        'is-printing'
    );

    document.body.classList.add(
        'is-printing'
    );

    /*
     * Laisser le navigateur préparer le rendu.
     */
    window.setTimeout(() => {

        window.print();

    }, 80);
}


/**
 * Nettoyage après impression.
 */
window.addEventListener('afterprint', () => {

    paymentIndexState.isPrinting = false;

    document.documentElement.classList.remove(
        'is-printing'
    );

    document.body.classList.remove(
        'is-printing'
    );

});


/**
 * Support de Ctrl + P / Cmd + P.
 */
document.addEventListener('keydown', (event) => {

    const isPrintShortcut =
        (event.ctrlKey || event.metaKey)
        && event.key.toLowerCase() === 'p';

    if (!isPrintShortcut) {
        return;
    }

    /*
     * Le navigateur sait déjà imprimer.
     *
     * On prépare simplement la page.
     */
    document.documentElement.classList.add(
        'is-printing'
    );

    document.body.classList.add(
        'is-printing'
    );

});


/* ==========================================================================
   COPIE DES RÉFÉRENCES
   ========================================================================== */

/**
 * Initialise les boutons :
 *
 * data-copy
 * data-copy-value="..."
 */
function initializeCopyButtons(root) {

    const buttons = root.querySelectorAll(
        PHOVE_PAYMENT_INDEX.selectors.copy
    );

    if (!buttons.length) {
        return;
    }

    buttons.forEach((button) => {

        button.addEventListener(
            'click',
            handleCopy
        );

    });
}


/**
 * Copie une référence dans le presse-papiers.
 */
async function handleCopy(event) {

    event.preventDefault();

    const button = event.currentTarget;

    if (!button) {
        return;
    }

    const value =
        button.dataset.copyValue
        ?? button.textContent.trim();

    if (!value) {
        return;
    }

    try {

        await copyToClipboard(value);

        showCopySuccess(button);

    } catch (error) {

        /*
         * Fallback pour les navigateurs ne permettant pas
         * navigator.clipboard.
         */
        const copied = fallbackCopy(value);

        if (copied) {
            showCopySuccess(button);
        } else {
            showCopyError(button);
        }

        console.error(
            'PHOVE — Impossible de copier la référence :',
            error
        );

    }
}


/**
 * Utilise l'API Clipboard moderne.
 */
async function copyToClipboard(value) {

    if (
        !navigator.clipboard
        || typeof navigator.clipboard.writeText !== 'function'
    ) {
        throw new Error(
            'Clipboard API indisponible.'
        );
    }

    await navigator.clipboard.writeText(value);
}


/**
 * Fallback de copie.
 */
function fallbackCopy(value) {

    const textarea =
        document.createElement('textarea');

    textarea.value = value;

    textarea.setAttribute(
        'readonly',
        ''
    );

    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    textarea.style.pointerEvents = 'none';

    document.body.appendChild(textarea);

    textarea.select();
    textarea.setSelectionRange(
        0,
        textarea.value.length
    );

    let success = false;

    try {

        success = document.execCommand('copy');

    } catch (error) {

        success = false;

    }

    textarea.remove();

    return success;
}


/**
 * Feedback visuel après copie.
 */
function showCopySuccess(button) {

    if (!button) {
        return;
    }

    const originalContent =
        button.innerHTML;

    button.classList.add(
        PHOVE_PAYMENT_INDEX.copiedClass
    );

    button.innerHTML =
        '<i class="fas fa-check"></i> Copié';

    button.setAttribute(
        'aria-label',
        'Référence copiée'
    );

    window.setTimeout(() => {

        button.classList.remove(
            PHOVE_PAYMENT_INDEX.copiedClass
        );

        button.innerHTML =
            originalContent;

    }, 1400);
}


/**
 * Feedback en cas d'échec.
 */
function showCopyError(button) {

    if (!button) {
        return;
    }

    const originalContent =
        button.innerHTML;

    button.innerHTML =
        '<i class="fas fa-exclamation-triangle"></i> Échec';

    window.setTimeout(() => {

        button.innerHTML =
            originalContent;

    }, 1600);
}


/* ==========================================================================
   ACCESSIBILITÉ DU TABLEAU
   ========================================================================== */

/**
 * Rend les lignes du tableau navigables au clavier.
 */
function initializeTableAccessibility(root) {

    const rows = root.querySelectorAll(
        PHOVE_PAYMENT_INDEX.selectors.paymentRow
    );

    if (!rows.length) {
        return;
    }

    rows.forEach((row) => {

        row.addEventListener('keydown', (event) => {

            /*
             * Enter :
             * ouvrir le premier lien de la ligne.
             */
            if (event.key === 'Enter') {

                event.preventDefault();

                const firstLink =
                    row.querySelector('a');

                if (firstLink) {
                    firstLink.click();
                }

            }

        });

    });
}


/**
 * Ajoute quelques comportements clavier
 * aux éléments interactifs.
 */
function initializeKeyboardNavigation(root) {

    root.addEventListener('keydown', (event) => {

        /*
         * Escape peut fermer certains états de chargement
         * visuels, sans annuler une requête serveur.
         */
        if (event.key === 'Escape') {

            clearTimeout(
                paymentIndexState.searchTimer
            );

        }

    });
}


/* ==========================================================================
   LOADER GLOBAL
   ========================================================================== */

/**
 * Affiche le loader global du layout.
 */
function showGlobalLoader() {

    const loader =
        document.querySelector(
            PHOVE_PAYMENT_INDEX.selectors.globalLoader
        );

    if (!loader) {
        return;
    }

    loader.classList.add('active');
}


/**
 * Masque le loader global.
 */
function hideGlobalLoader() {

    const loader =
        document.querySelector(
            PHOVE_PAYMENT_INDEX.selectors.globalLoader
        );

    if (!loader) {
        return;
    }

    loader.classList.remove('active');
}


/* ==========================================================================
   ÉTAT DE CHARGEMENT DES BOUTONS
   ========================================================================== */

/**
 * Transforme temporairement le contenu d'un bouton
 * en état de chargement.
 */
function setButtonLoading(
    button,
    loadingText = 'Chargement...',
    iconClass = 'fa-spinner'
) {

    if (!button) {
        return;
    }

    /*
     * Ne pas écraser plusieurs fois le contenu original.
     */
    if (!button.dataset.originalContent) {

        button.dataset.originalContent =
            button.innerHTML;

    }

    button.disabled = true;

    button.setAttribute(
        'aria-busy',
        'true'
    );

    button.innerHTML = `
        <i class="fas ${iconClass} fa-spin"></i>
        <span>${loadingText}</span>
    `;
}


/**
 * Restaure le bouton.
 */
function restoreButton(button) {

    if (!button) {
        return;
    }

    if (button.dataset.originalContent) {

        button.innerHTML =
            button.dataset.originalContent;

        delete button.dataset.originalContent;

    }

    button.disabled = false;

    button.removeAttribute(
        'aria-busy'
    );
}


/* ==========================================================================
   GESTION DE PAGE
   ========================================================================== */

/**
 * Lorsque la page est restaurée depuis le cache navigateur,
 * on remet l'état normal.
 */
window.addEventListener('pageshow', () => {

    paymentIndexState.isSubmitting = false;

    hideGlobalLoader();

    document
        .querySelectorAll(
            `${PHOVE_PAYMENT_INDEX.selectors.root} .${PHOVE_PAYMENT_INDEX.loadingClass}`
        )
        .forEach((element) => {

            element.classList.remove(
                PHOVE_PAYMENT_INDEX.loadingClass
            );

        });

    document
        .querySelectorAll(
            PHOVE_PAYMENT_INDEX.selectors.submitFilter
        )
        .forEach((button) => {

            restoreButton(button);

        });

});


/* ==========================================================================
   PROTECTION CONTRE LES CLICS MULTIPLES
   ========================================================================== */

/**
 * Évite plusieurs clics rapides sur les liens d'action.
 */
document.addEventListener('click', (event) => {

    const actionLink =
        event.target.closest(
            '.payment-index .action-btn'
        );

    if (!actionLink) {
        return;
    }

    /*
     * Les liens ouverts dans un nouvel onglet
     * restent libres.
     */
    if (
        actionLink.target === '_blank'
        || event.ctrlKey
        || event.metaKey
        || event.shiftKey
        || event.altKey
    ) {
        return;
    }

    actionLink.classList.add(
        PHOVE_PAYMENT_INDEX.loadingClass
    );

});


/* ==========================================================================
   EXPORT MINIMAL
   ========================================================================== */

/*
 * API interne légère.
 *
 * Utile si une autre fonctionnalité PHOVE doit déclencher
 * l'impression depuis cette page.
 */
window.PHOVEPaymentIndex = {
    print: () => {

        document.documentElement.classList.add(
            'is-printing'
        );

        document.body.classList.add(
            'is-printing'
        );

        window.setTimeout(() => {
            window.print();
        }, 80);

    },

    clearSearchTimer: () => {

        clearTimeout(
            paymentIndexState.searchTimer
        );

    }
};