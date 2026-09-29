/**
 * ==========================================================================
 * PHOVE — IMPRESSION D'UN PAIEMENT DE SALAIRE
 * ==========================================================================
 *
 * Fichier :
 * public/assets/js/admin/paiements-salaires/imprimer.js
 *
 * Responsabilités :
 * - lancer l'impression ;
 * - gérer les barres de progression ;
 * - gérer le retour navigateur ;
 * - préparer proprement l'impression ;
 * - restaurer l'état après impression ;
 * - éviter les erreurs si certains éléments sont absents.
 *
 * Aucun calcul financier n'est effectué ici.
 * Les données financières viennent du serveur.
 */

'use strict';


/* ==========================================================================
   INITIALISATION
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initPrintPage();
});


/**
 * Initialise toutes les fonctionnalités de la page.
 */
function initPrintPage() {
    initProgressBars();
    initPrintButtons();
    initBackButtons();
    initKeyboardShortcuts();
    initPrintEvents();
}


/* ==========================================================================
   BARRES DE PROGRESSION
   ========================================================================== */

/**
 * Initialise les barres ayant l'attribut :
 *
 * data-progress="75"
 *
 * Le CSS conserve la présentation.
 * Le JavaScript applique uniquement la largeur dynamique.
 */
function initProgressBars() {
    const progressBars = document.querySelectorAll('[data-progress]');

    if (!progressBars.length) {
        return;
    }

    progressBars.forEach((bar) => {
        const rawProgress = bar.dataset.progress ?? '0';

        let progress = Number.parseFloat(rawProgress);

        if (!Number.isFinite(progress)) {
            progress = 0;
        }

        progress = Math.min(100, Math.max(0, progress));

        /*
         * Utilisation de requestAnimationFrame afin de permettre
         * à l'animation CSS de fonctionner correctement.
         */
        requestAnimationFrame(() => {
            bar.style.width = `${progress}%`;
        });

        /*
         * Accessibilité.
         */
        bar.setAttribute('role', 'progressbar');
        bar.setAttribute('aria-valuemin', '0');
        bar.setAttribute('aria-valuemax', '100');
        bar.setAttribute('aria-valuenow', String(Math.round(progress)));
    });
}


/* ==========================================================================
   BOUTONS D'IMPRESSION
   ========================================================================== */

/**
 * Initialise tous les éléments possédant :
 *
 * data-print
 */
function initPrintButtons() {
    const printButtons = document.querySelectorAll('[data-print]');

    if (!printButtons.length) {
        return;
    }

    printButtons.forEach((button) => {
        button.addEventListener('click', handlePrintClick);
    });
}


/**
 * Gestion du clic sur le bouton Imprimer.
 */
function handlePrintClick(event) {
    event.preventDefault();

    prepareForPrint();

    /*
     * Petit délai pour laisser le navigateur appliquer
     * les changements éventuels avant l'ouverture de
     * la boîte d'impression.
     */
    window.setTimeout(() => {
        window.print();
    }, 50);
}


/* ==========================================================================
   PRÉPARATION DE L'IMPRESSION
   ========================================================================== */

/**
 * Prépare le document avant l'impression.
 *
 * Cette fonction reste volontairement légère :
 * aucune modification du contenu financier.
 */
function prepareForPrint() {
    document.documentElement.classList.add('is-printing');
    document.body.classList.add('is-printing');

    /*
     * S'assurer que toutes les progressions sont calculées
     * avant l'impression.
     */
    initProgressBars();
}


/**
 * Nettoie l'état après l'impression.
 */
function cleanupAfterPrint() {
    document.documentElement.classList.remove('is-printing');
    document.body.classList.remove('is-printing');
}


/* ==========================================================================
   ÉVÉNEMENTS D'IMPRESSION
   ========================================================================== */

/**
 * Initialise les événements liés à l'impression.
 */
function initPrintEvents() {
    /*
     * Événement moderne.
     */
    window.addEventListener('beforeprint', () => {
        prepareForPrint();
    });

    window.addEventListener('afterprint', () => {
        cleanupAfterPrint();
    });
}


/* ==========================================================================
   BOUTON RETOUR
   ========================================================================== */

/**
 * Initialise les boutons possédant :
 *
 * data-back
 *
 * Même si le Blade actuel utilise un <a>, cette fonction
 * permet également de supporter un bouton dans le futur.
 */
function initBackButtons() {
    const backButtons = document.querySelectorAll('[data-back]');

    if (!backButtons.length) {
        return;
    }

    backButtons.forEach((button) => {
        button.addEventListener('click', handleBackClick);
    });
}


/**
 * Retour à la page précédente.
 */
function handleBackClick(event) {
    event.preventDefault();

    /*
     * S'il existe une véritable page précédente,
     * on l'utilise.
     */
    if (window.history.length > 1) {
        window.history.back();
        return;
    }

    /*
     * Fallback si l'historique n'est pas disponible.
     */
    const fallbackUrl = buttonFallbackUrl(event.currentTarget);

    if (fallbackUrl) {
        window.location.href = fallbackUrl;
    }
}


/**
 * Récupère une éventuelle URL de secours.
 *
 * Exemple :
 *
 * <button data-back data-back-url="/admin/...">
 */
function buttonFallbackUrl(button) {
    if (!button) {
        return null;
    }

    return button.dataset.backUrl || null;
}


/* ==========================================================================
   RACCOURCIS CLAVIER
   ========================================================================== */

/**
 * Gestion des raccourcis clavier.
 *
 * Ctrl + P
 * Command + P
 *
 * Le navigateur gère déjà Ctrl + P nativement.
 * Nous interceptons seulement le comportement afin
 * de préparer correctement le document.
 */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (event) => {
        const isPrintShortcut =
            (event.ctrlKey || event.metaKey)
            && event.key.toLowerCase() === 'p';

        if (!isPrintShortcut) {
            return;
        }

        event.preventDefault();

        prepareForPrint();

        window.setTimeout(() => {
            window.print();
        }, 50);
    });
}


/* ==========================================================================
   UTILITAIRE — IMPRESSION PROGRAMMATIQUE
   ========================================================================== */

/**
 * Fonction publique interne permettant de déclencher
 * l'impression depuis d'autres éléments.
 *
 * Exemple futur :
 *
 * window.PHOVEPrintPayment();
 */
window.PHOVEPrintPayment = function () {
    prepareForPrint();

    window.setTimeout(() => {
        window.print();
    }, 50);
};


/* ==========================================================================
   UTILITAIRE — VÉRIFICATION DE LA PAGE
   ========================================================================== */

/**
 * Vérifie que nous sommes bien sur la page d'impression.
 *
 * Cela évite qu'une fonction globale soit utilisée
 * accidentellement sur une autre page.
 */
function isPrintPage() {
    return Boolean(
        document.querySelector('.salary-payment-print')
        && document.querySelector('.payment-print-document')
    );
}


/* ==========================================================================
   PROTECTION
   ========================================================================== */

/**
 * Si le script est chargé par erreur sur une autre page,
 * il ne doit provoquer aucune erreur.
 */
if (!isPrintPage()) {
    /*
     * Rien à faire.
     *
     * Le script reste silencieux afin de ne pas casser
     * les autres pages de l'administration.
     */
}