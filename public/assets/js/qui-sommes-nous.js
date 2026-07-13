/**
 * AHD-RDC — SCRIPTS PAGE QUI SOMMES-NOUS
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES FONDATEURS AU SCROLL
    // ============================================================
    const founderCards = document.querySelectorAll('.founder-card');

    if (founderCards.length > 0 && 'IntersectionObserver' in window) {
        const founderObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, index * 80);

                    founderObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        founderCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px) scale(0.95)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            founderObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES AVATARS — EFFET DE ROTATION
    // ============================================================
    const founderAvatars = document.querySelectorAll('.founder-avatar');

    founderAvatars.forEach(avatar => {
        avatar.addEventListener('mouseenter', function () {
            this.style.transform = 'rotate(10deg) scale(1.1)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        avatar.addEventListener('mouseleave', function () {
            this.style.transform = 'rotate(0deg) scale(1)';
            this.style.transition = 'transform 0.3s ease';
        });
    });

    // ============================================================
    // 3. COMPTEUR D'ANNÉES D'EXPÉRIENCE (dans le badge)
    // ============================================================
    const experienceBadge = document.querySelector('.content-image-badge strong');

    if (experienceBadge) {
        const creationYear = 2017;
        const currentYear = new Date().getFullYear();
        const experience = currentYear - creationYear;

        if (experienceBadge.textContent.includes('+')) {
            experienceBadge.textContent = experience + '+';
        }
    }

    // ============================================================
    // 4. ANIMATION DE LA SECTION HISTORIQUE (TIMELINE)
    // ============================================================
    const historyItems = document.querySelectorAll('.history-item');

    if (historyItems.length > 0 && 'IntersectionObserver' in window) {
        const historyObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 150);

                    historyObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -20px 0px'
        });

        historyItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            historyObserver.observe(item);
        });
    }

    // ============================================================
    // 5. ANIMATION DU BLOC DE CITATION
    // ============================================================
    const quoteBlock = document.querySelector('.quote-block');

    if (quoteBlock && 'IntersectionObserver' in window) {
        const quoteObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    quoteBlock.style.opacity = '1';
                    quoteBlock.style.transform = 'translateY(0)';
                    quoteObserver.unobserve(quoteBlock);
                }
            });
        }, {
            threshold: 0.3
        });

        quoteBlock.style.opacity = '0';
        quoteBlock.style.transform = 'translateY(20px)';
        quoteBlock.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        quoteObserver.observe(quoteBlock);
    }

    // ============================================================
    // 6. EFFET PARALLAXE LÉGER SUR L'IMAGE DE SECTION
    // ============================================================
    const contentImages = document.querySelectorAll('.content-image');

    if (contentImages.length > 0 && window.innerWidth > 768) {
        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    contentImages.forEach(img => {
                        const rect = img.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        if (rect.top < windowHeight && rect.bottom > 0) {
                            const scrollPosition = (windowHeight - rect.top) / (windowHeight + rect.height);
                            const translateY = scrollPosition * 20 - 10;

                            img.style.transform = `translateY(${translateY}px)`;
                            img.style.transition = 'transform 0.1s ease';
                        }
                    });

                    ticking = false;
                });

                ticking = true;
            }
        }, { passive: true });
    }

    // ============================================================
    // 7. TOOLTIP SUR LES LOCALISATIONS DES FONDATEURS
    // ============================================================
    const locationElements = document.querySelectorAll('.founder-location');

    locationElements.forEach(loc => {
        loc.addEventListener('mouseenter', function () {
            this.style.color = 'var(--color-primary)';
            this.style.transition = 'color 0.2s ease';
        });

        loc.addEventListener('mouseleave', function () {
            this.style.color = 'var(--color-text-muted)';
        });
    });

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c👥 AHD-RDC %c— Page Qui sommes-nous chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});