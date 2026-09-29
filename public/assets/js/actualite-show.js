/* ============================================================
   PHOVE — SITE PUBLIC
   VUE    : ACTUALITÉS — SHOW
   FICHIER: actualite-show.js

   Correspondance :
   resources/views/public/actualites/show.blade.php

   Fonctionnalités :
   - Partage de l'article (Web Share API + fallback)
   - Impression de l'article
   - Progression de lecture
   - Bouton retour en haut
   - Copie du lien
   ============================================================ */

(function () {
    'use strict';


    /* ============================================================
       1. CONFIGURATION
       ============================================================ */

    const CONFIG = {
        showReadingProgress: true,
        showScrollButton: true,
        enableShareButtons: true,
        enablePrint: true,
    };


    /* ============================================================
       2. POINT D'ENTRÉE
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function () {
        initShareButton();
        initPrintButton();
        initReadingProgress();
        initScrollToTop();
    });


    /* ============================================================
       3. PARTAGE DE L'ARTICLE
       ============================================================ */

    function initShareButton() {
        if (!CONFIG.enableShareButtons) return;

        const shareBtn = document.querySelector('.btn-share');
        if (!shareBtn) return;

        shareBtn.addEventListener('click', async function () {
            const title = shareBtn.getAttribute('data-title') || document.title;
            const text = shareBtn.getAttribute('data-text') || '';
            const url = window.location.href;

            // Web Share API (mobile / navigateurs modernes)
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: title,
                        text: text,
                        url: url,
                    });
                    return;
                } catch (error) {
                    // L'utilisateur a annulé → on ne fait rien
                    if (error.name === 'AbortError') return;
                    console.warn('[PHOVE] Web Share API indisponible, fallback activé.');
                }
            }

            // Fallback : menu de partage personnalisé
            showShareFallback(title, url, shareBtn);
        });
    }


    /**
     * Affiche un menu de partage alternatif.
     *
     * @param {string} title
     * @param {string} url
     * @param {HTMLElement} anchor
     */
    function showShareFallback(title, url, anchor) {
        // Supprimer un éventuel menu existant
        const existing = document.querySelector('.share-fallback-menu');
        if (existing) existing.remove();

        const menu = document.createElement('div');
        menu.className = 'share-fallback-menu';
        menu.style.cssText = `
            position: absolute;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 8px;
            z-index: 9999;
            min-width: 220px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        `;

        const encodedUrl = encodeURIComponent(url);
        const encodedTitle = encodeURIComponent(title);

        const networks = [
            { name: 'Facebook', icon: 'fa-facebook-f', url: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}` },
            { name: 'Twitter / X', icon: 'fa-x-twitter', url: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}` },
            { name: 'LinkedIn', icon: 'fa-linkedin-in', url: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}` },
            { name: 'WhatsApp', icon: 'fa-whatsapp', url: `https://wa.me/?text=${encodedTitle}%20${encodedUrl}` },
            { name: 'Email', icon: 'fa-envelope', url: `mailto:?subject=${encodedTitle}&body=${encodedUrl}` },
        ];

        networks.forEach(function (network) {
            const link = document.createElement('a');
            link.href = network.url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.innerHTML = `<i class="fab ${network.icon}"></i> ${network.name}`;
            link.style.cssText = `
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                border-radius: 6px;
                color: #334155;
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
                transition: background 0.15s ease;
            `;
            link.addEventListener('mouseenter', function () {
                this.style.background = '#F1F5F9';
            });
            link.addEventListener('mouseleave', function () {
                this.style.background = 'transparent';
            });
            menu.appendChild(link);
        });

        // Bouton "Copier le lien"
        const divider = document.createElement('div');
        divider.style.cssText = 'height: 1px; background: #E2E8F0; margin: 4px 0;';
        menu.appendChild(divider);

        const copyBtn = document.createElement('button');
        copyBtn.type = 'button';
        copyBtn.innerHTML = '<i class="fas fa-link"></i> Copier le lien';
        copyBtn.style.cssText = `
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border: none;
            background: transparent;
            border-radius: 6px;
            color: #334155;
            text-align: left;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s ease;
        `;
        copyBtn.addEventListener('mouseenter', function () {
            this.style.background = '#F1F5F9';
        });
        copyBtn.addEventListener('mouseleave', function () {
            this.style.background = 'transparent';
        });
        copyBtn.addEventListener('click', function () {
            copyToClipboard(url);
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
            copyBtn.style.color = '#10B981';
            setTimeout(function () {
                copyBtn.innerHTML = '<i class="fas fa-link"></i> Copier le lien';
                copyBtn.style.color = '#334155';
            }, 2000);
        });
        menu.appendChild(copyBtn);

        // Positionnement
        document.body.appendChild(menu);
        const rect = anchor.getBoundingClientRect();
        menu.style.position = 'absolute';
        menu.style.top = (rect.bottom + window.scrollY + 8) + 'px';
        menu.style.left = Math.min(rect.left + window.scrollX, window.innerWidth - 240) + 'px';

        // Fermeture au clic extérieur
        setTimeout(function () {
            document.addEventListener('click', closeOnClickOutside);
        }, 100);

        function closeOnClickOutside(event) {
            if (!menu.contains(event.target) && event.target !== anchor) {
                menu.remove();
                document.removeEventListener('click', closeOnClickOutside);
            }
        }
    }


    /* ============================================================
       4. IMPRESSION DE L'ARTICLE
       ============================================================ */

    function initPrintButton() {
        if (!CONFIG.enablePrint) return;

        const printBtn = document.querySelector('.btn-print');
        if (!printBtn) return;

        printBtn.addEventListener('click', function () {
            window.print();
        });
    }


    /* ============================================================
       5. BARRE DE PROGRESSION DE LECTURE
       ============================================================ */

    function initReadingProgress() {
        if (!CONFIG.showReadingProgress) return;

        const article = document.querySelector('.article-body');
        if (!article) return;

        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress-bar';
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #1A5B9C 0%, #F59E0B 100%);
            z-index: 99999;
            transition: width 0.1s ease;
            box-shadow: 0 2px 6px rgba(26, 91, 156, 0.3);
        `;
        document.body.appendChild(progressBar);

        function updateProgress() {
            const rect = article.getBoundingClientRect();
            const articleTop = rect.top + window.scrollY;
            const articleHeight = rect.height;
            const windowHeight = window.innerHeight;
            const scrollTop = window.scrollY;

            const start = articleTop - windowHeight;
            const end = articleTop + articleHeight;
            const current = scrollTop;

            let progress = ((current - start) / (end - start)) * 100;
            progress = Math.max(0, Math.min(100, progress));

            progressBar.style.width = progress + '%';
        }

        window.addEventListener('scroll', updateProgress, { passive: true });
        window.addEventListener('resize', updateProgress);
        updateProgress();
    }


    /* ============================================================
       6. BOUTON RETOUR EN HAUT
       ============================================================ */

    function initScrollToTop() {
        if (!CONFIG.showScrollButton) return;

        const btn = document.createElement('button');
        btn.className = 'article-scroll-top';
        btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        btn.title = 'Retour en haut';
        btn.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 46px;
            height: 46px;
            border: none;
            border-radius: 50%;
            background: #1A5B9C;
            color: #FFFFFF;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(26, 91, 156, 0.35);
            z-index: 9999;
        `;
        document.body.appendChild(btn);

        function toggleButton() {
            if (window.scrollY > 500) {
                btn.style.opacity = '1';
                btn.style.visibility = 'visible';
                btn.style.transform = 'translateY(0)';
            } else {
                btn.style.opacity = '0';
                btn.style.visibility = 'hidden';
                btn.style.transform = 'translateY(20px)';
            }
        }

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        btn.addEventListener('mouseenter', function () {
            this.style.background = '#124270';
            this.style.transform = 'translateY(-3px)';
        });

        btn.addEventListener('mouseleave', function () {
            this.style.background = '#1A5B9C';
            this.style.transform = 'translateY(0)';
        });

        window.addEventListener('scroll', toggleButton, { passive: true });
        toggleButton();
    }


    /* ============================================================
       7. COPIER DANS LE PRESSE-PAPIERS
       ============================================================ */

    /**
     * Copie une chaîne dans le presse-papiers.
     *
     * @param {string} text
     */
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).catch(function (err) {
                console.warn('[PHOVE] Erreur de copie :', err);
            });
        } else {
            // Fallback pour anciens navigateurs
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
            } catch (err) {
                console.warn('[PHOVE] Erreur de copie (fallback) :', err);
            }
            document.body.removeChild(textarea);
        }
    }


    /* ============================================================
       8. RACCOURCIS CLAVIER
       ============================================================ */

    document.addEventListener('keydown', function (event) {
        const tag = (event.target && event.target.tagName) || '';
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;

        // Ctrl + P → Imprimer
        if ((event.ctrlKey || event.metaKey) && (event.key === 'p' || event.key === 'P')) {
            // Laisse le navigateur gérer l'impression native
            return;
        }

        // Échap → Retour aux actualités
        if (event.key === 'Escape') {
            const backLink = document.querySelector('.article-actions a[href*="actualites"]');
            if (backLink) {
                window.location.href = backLink.href;
            }
        }
    });


    /* ============================================================
       9. LOG DE DÉMARRAGE
       ============================================================ */

    console.log(
        '%c📰 PHOVE %c— Page article chargée',
        'color: #1A5B9C; font-weight: bold; font-size: 13px;',
        'color: #64748B; font-size: 11px;'
    );

})();