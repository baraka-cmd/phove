/* ============================================================
   PHOVE — GALERIE PUBLIQUE (JavaScript)
   ------------------------------------------------------------
   Pilote la lightbox média pour la page /galerie.

   Fonctionnalités :
     - Ouverture au clic / touche Entrée / touche Espace
     - Navigation : boutons, flèches clavier, swipe tactile
     - Fermeture : bouton, Échap, clic arrière-plan
     - Lecture vidéo automatique dans la lightbox
     - Blocage du scroll de la page en arrière-plan
     - Accessibilité : focus trap, aria-hidden, focus management
     - Délégation d'événements (dynamique)
   ============================================================ */

(function () {
    'use strict';

    /* ========================================================
       1. ÉTAT GLOBAL
       ======================================================== */

    let cards = [];            // Cartes médias (NodeList → Array)
    let currentIndex = 0;      // Index courant
    let lastFocusedElement = null; // Élément qui avait le focus avant ouverture

    // Éléments DOM de la lightbox
    let lightbox, lightboxImg, lightboxVid, lightboxTitle,
        lightboxDesc, lightboxCounter, btnClose, btnPrev, btnNext;

    // Swipe tactile
    let touchStartX = 0;
    let touchEndX = 0;

    /* ========================================================
       2. INITIALISATION
       ======================================================== */

    document.addEventListener('DOMContentLoaded', function () {
        // Récupération des éléments
        lightbox         = document.getElementById('mediaLightbox');
        lightboxImg      = document.getElementById('lightboxImage');
        lightboxVid      = document.getElementById('lightboxVideo');
        lightboxTitle    = document.getElementById('lightboxTitle');
        lightboxDesc     = document.getElementById('lightboxDescription');
        lightboxCounter  = document.getElementById('lightboxCounter');

        btnClose = document.getElementById('lightboxClose');
        btnPrev  = document.getElementById('lightboxPrev');
        btnNext  = document.getElementById('lightboxNext');

        // Si la lightbox n'existe pas sur cette page, on ne fait rien
        if (!lightbox) {
            return;
        }

        // Récupération des cartes médias
        cards = Array.from(document.querySelectorAll('.media-card'));

        // Pas de médias → rien à faire
        if (cards.length === 0) {
            return;
        }

        // Attacher les événements
        attacherEvenementsCartes();
        attacherEvenementsLightbox();
        attacherEvenementsClavier();
        attacherEvenementsTactiles();
    });

    /* ========================================================
       3. ÉVÉNEMENTS SUR LES CARTES
       ======================================================== */

    function attacherEvenementsCartes() {
        cards.forEach(function (card, index) {
            // Clic
            card.addEventListener('click', function (e) {
                e.preventDefault();
                ouvrirLightbox(index);
            });

            // Clavier : Entrée ou Espace
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    ouvrirLightbox(index);
                }
            });
        });
    }

    /* ========================================================
       4. ÉVÉNEMENTS LIGHTBOX
       ======================================================== */

    function attacherEvenementsLightbox() {
        if (btnClose) {
            btnClose.addEventListener('click', fermerLightbox);
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', mediaPrecedent);
        }

        if (btnNext) {
            btnNext.addEventListener('click', mediaSuivant);
        }

        // Clic sur l'arrière-plan (mais pas sur le contenu)
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                fermerLightbox();
            }
        });
    }

    /* ========================================================
       5. ÉVÉNEMENTS CLAVIER (globaux)
       ======================================================== */

    function attacherEvenementsClavier() {
        document.addEventListener('keydown', function (e) {
            // Rien à faire si la lightbox est fermée
            if (!lightbox.classList.contains('active')) {
                return;
            }

            switch (e.key) {
                case 'Escape':
                    e.preventDefault();
                    fermerLightbox();
                    break;

                case 'ArrowLeft':
                    e.preventDefault();
                    mediaPrecedent();
                    break;

                case 'ArrowRight':
                    e.preventDefault();
                    mediaSuivant();
                    break;
            }
        });
    }

    /* ========================================================
       6. ÉVÉNEMENTS TACTILES (swipe)
       ======================================================== */

    function attacherEvenementsTactiles() {
        lightbox.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        lightbox.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            gererSwipe();
        }, { passive: true });
    }

    function gererSwipe() {
        const seuil = 60; // pixels minimum pour déclencher
        const delta = touchEndX - touchStartX;

        if (Math.abs(delta) < seuil) {
            return;
        }

        if (delta > 0) {
            // Swipe vers la droite → média précédent
            mediaPrecedent();
        } else {
            // Swipe vers la gauche → média suivant
            mediaSuivant();
        }
    }

    /* ========================================================
       7. OUVERTURE / FERMETURE
       ======================================================== */

    function ouvrirLightbox(index) {
        if (index < 0 || index >= cards.length) {
            return;
        }

        currentIndex = index;
        lastFocusedElement = document.activeElement;

        const card = cards[index];

        // Récupération des données depuis les data-*
        const type        = card.dataset.mediaType || 'image';
        const url         = card.dataset.mediaUrl || '';
        const titre       = card.dataset.mediaTitre || '';
        const description = card.dataset.mediaDescription || '';

        // Reset des éléments média
        lightboxImg.style.display = 'none';
        lightboxVid.style.display = 'none';
        lightboxImg.removeAttribute('src');
        lightboxImg.alt = '';

        // Pause la vidéo précédente si elle jouait
        if (lightboxVid && !lightboxVid.paused) {
            lightboxVid.pause();
        }

        // Affichage selon le type
        if (type === 'video') {
            afficherVideo(url);
        } else {
            afficherImage(url, titre);
        }

        // Titre et description
        if (lightboxTitle) {
            lightboxTitle.textContent = titre;
        }
        if (lightboxDesc) {
            lightboxDesc.textContent = description;
        }

        // Compteur (1 / N)
        if (lightboxCounter) {
            lightboxCounter.textContent = (index + 1) + ' / ' + cards.length;
        }

        // Affichage de la modale
        lightbox.classList.add('active');
        lightbox.setAttribute('aria-hidden', 'false');

        // Blocage du scroll arrière-plan
        document.body.style.overflow = 'hidden';

        // Focus sur le bouton fermer (accessibilité)
        setTimeout(function () {
            if (btnClose) {
                btnClose.focus();
            }
        }, 100);
    }

    function fermerLightbox() {
        // Pause la vidéo si elle joue
        if (lightboxVid && !lightboxVid.paused) {
            lightboxVid.pause();
            lightboxVid.currentTime = 0;
        }

        // Masquer la modale
        lightbox.classList.remove('active');
        lightbox.setAttribute('aria-hidden', 'true');

        // Restaurer le scroll
        document.body.style.overflow = '';

        // Restaurer le focus
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
    }

    /* ========================================================
       8. AFFICHAGE DES MÉDIAS
       ======================================================== */

    function afficherImage(url, alt) {
        if (!lightboxImg) return;

        lightboxImg.src = url || '';
        lightboxImg.alt = alt || '';
        lightboxImg.style.display = 'block';

        // Gestion d'erreur de chargement
        lightboxImg.onerror = function () {
            this.onerror = null;
            this.src = '/assets/images/logo-phove.png';
        };
    }

    function afficherVideo(url) {
        if (!lightboxVid || !url) return;

        const source = lightboxVid.querySelector('source');

        if (source) {
            source.src = url;
        } else {
            // Si pas de <source>, on met l'URL directement sur la balise
            lightboxVid.src = url;
        }

        // Recharger la vidéo
        lightboxVid.load();
        lightboxVid.style.display = 'block';

        // Tentative de lecture automatique (peut être bloquée par le navigateur)
        const playPromise = lightboxVid.play();

        if (playPromise !== undefined) {
            playPromise.catch(function () {
                // Lecture automatique bloquée → pas grave, l'utilisateur clique sur play
            });
        }
    }

    /* ========================================================
       9. NAVIGATION
       ======================================================== */

    function mediaPrecedent() {
        if (cards.length <= 1) return;

        currentIndex = (currentIndex - 1 + cards.length) % cards.length;
        ouvrirLightbox(currentIndex);
    }

    function mediaSuivant() {
        if (cards.length <= 1) return;

        currentIndex = (currentIndex + 1) % cards.length;
        ouvrirLightbox(currentIndex);
    }

    /* ========================================================
       10. API PUBLIQUE (optionnelle)
       ========================================================
       Permet d'appeler window.PhoveGalerie.close() depuis la console
       ou depuis un autre script.
       ======================================================== */

    window.PhoveGalerie = {
        open:  ouvrirLightbox,
        close: fermerLightbox,
        next:  mediaSuivant,
        prev:  mediaPrecedent,
        getIndex: function () { return currentIndex; },
        getTotal: function () { return cards.length; },
    };

})();