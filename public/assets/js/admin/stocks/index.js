/**
 * PHOVE — SCRIPT : LISTE DES ARTICLES DE STOCK
 * Vue : resources/views/admin/stocks/index.blade.php
 *
 * Rôle :
 *   - Gestion de la modale d'archivage
 *     (ouverture, fermeture, remplissage dynamique du formulaire)
 *   - Auto-dismiss des alertes de session
 *   - Auto-submit des filtres (selects uniquement)
 *   - Fermeture de la modale au clic extérieur
 *   - Fermeture de la modale avec la touche Échap
 *
 * IMPORTANT :
 *   Ce script NE MODIFIE AUCUNE donnée.
 */

(function () {
    'use strict';

    const ALERT_DEFAULT_DELAY = 6000;

    const SELECTORS = {
        alerts:        '.alert',
        filterForm:    '.stock-filter-form',
        modal:         '#archive-stock-modal',
        modalForm:     '#archive-stock-form',
        modalName:     '[data-archive-stock-name]',
        openModalBtn:  '[data-open-archive-modal]',
        closeModalBtn: '[data-close-modal]',
    };


    document.addEventListener('DOMContentLoaded', function () {
        initArchiveModal();
        initFilterAutoSubmit();
        initAlertsAutoDismiss();
    });


    /* ==========================================================
       1. MODALE D'ARCHIVAGE
       ==========================================================
       Fonctionnement :

       - Chaque bouton "Archiver" porte :
           data-open-archive-modal
           data-stock-id="123"
           data-stock-name="Riz blanc"

       - Au clic :
           * renseigne le nom dans la modale
           * renseigne l'action du formulaire
           * affiche la modale

       - Fermeture :
           * bouton "Annuler" ou "×"
           * clic en dehors de la modale
           * touche Échap
       ========================================================== */
    function initArchiveModal() {
        const modal = document.querySelector(SELECTORS.modal);
        const form = document.querySelector(SELECTORS.modalForm);
        const nameHolder = document.querySelector(SELECTORS.modalName);

        if (!modal || !form) {
            return;
        }

        /*
         * Ouverture de la modale depuis chaque bouton.
         */
        const openButtons = document.querySelectorAll(
            SELECTORS.openModalBtn
        );

        openButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const stockId = btn.dataset.stockId;
                const stockName = btn.dataset.stockName || 'cet article';

                /*
                 * Mise à jour du nom dans la modale.
                 */
                if (nameHolder) {
                    nameHolder.textContent = stockName;
                }

                /*
                 * Mise à jour de l'action du formulaire.
                 *
                 * On utilise l'URL fournie par data-stock-id.
                 * Le gabarit est récupéré depuis un attribut global
                 * ou construit à partir d'une route standard.
                 *
                 * Exemple d'action attendue :
                 *   /admin/stocks/{id}/archive
                 */
                if (stockId) {
                    form.setAttribute(
                        'action',
                        buildArchiveUrl(stockId)
                    );
                }

                /*
                 * Affichage de la modale.
                 */
                modal.hidden = false;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        /*
         * Fermeture de la modale (boutons ×, Annuler).
         */
        const closeButtons = modal.querySelectorAll(
            SELECTORS.closeModalBtn
        );

        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(modal);
            });
        });

        /*
         * Fermeture au clic sur l'overlay (hors dialog).
         */
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal(modal);
            }
        });

        /*
         * Fermeture avec Échap.
         */
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal(modal);
            }
        });
    }

    /**
     * Construit l'URL d'archivage à partir de l'identifiant.
     *
     * @param {string|number} stockId
     * @returns {string}
     */
    function buildArchiveUrl(stockId) {
        /*
         * On essaie d'abord de lire un gabarit exposé
         * dans le DOM (par exemple sur un élément parent).
         *
         * Sinon, on construit à partir du chemin actuel :
         *   /admin/stocks          →  /admin/stocks/{id}/archive
         *   /admin/stocks/         →  /admin/stocks/{id}/archive
         */
        const template =
            document.body.dataset.archiveUrlTemplate || null;

        if (template) {
            return template.replace('__ID__', stockId);
        }

        const base = window.location.pathname
            .replace(/\/$/, '');

        return base + '/' + stockId + '/archive';
    }

    /**
     * Ferme la modale.
     *
     * @param {HTMLElement} modal
     */
    function closeModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove('active');
        modal.hidden = true;
        document.body.style.overflow = '';
    }


    /* ==========================================================
       2. AUTO-SUBMIT DES FILTRES
       ==========================================================
       Les <select> du formulaire de filtres se soumettent
       automatiquement au changement.
       ========================================================== */
    function initFilterAutoSubmit() {
        const filterForm = document.querySelector(SELECTORS.filterForm);

        if (!filterForm) {
            return;
        }

        const selects = filterForm.querySelectorAll('select');

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }


    /* ==========================================================
       3. AUTO-DISMISS DES ALERTES
       ========================================================== */
    function initAlertsAutoDismiss() {
        const alerts = document.querySelectorAll(SELECTORS.alerts);

        alerts.forEach(function (alert) {
            if (alert.dataset.dismissed === 'true') {
                return;
            }

            /*
             * On ignore les alertes du résumé d'erreurs
             * de validation (elles doivent rester visibles).
             */
            if (alert.classList.contains('validation-summary')) {
                return;
            }

            alert.dataset.dismissed = 'true';

            const customDelay = parseInt(
                alert.getAttribute('data-auto-dismiss'),
                10
            );

            const delay = !isNaN(customDelay) && customDelay > 0
                ? customDelay
                : ALERT_DEFAULT_DELAY;

            setTimeout(function () {
                fadeOutAndRemove(alert);
            }, delay);
        });
    }


    /* ==========================================================
       4. UTILITAIRES INTERNES
       ========================================================== */

    function fadeOutAndRemove(element) {
        if (!element || !element.parentNode) {
            return;
        }

        element.style.transition =
            'opacity 0.4s ease, transform 0.4s ease';
        element.style.opacity = '0';
        element.style.transform = 'translateY(-8px)';

        setTimeout(function () {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, 400);
    }
})();