/**
 * AHD-RDC — SCRIPTS PAGE NOS VALEURS
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES SECTIONS DE VALEURS AU SCROLL
    // ============================================================
    const contentGrids = document.querySelectorAll('.content-grid');

    if (contentGrids.length > 0 && 'IntersectionObserver' in window) {
        const gridObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    gridObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        contentGrids.forEach(grid => {
            gridObserver.observe(grid);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES RÉCAPITULATIVES — EFFET CASCADE
    // ============================================================
    const recapCards = document.querySelectorAll('.value-recap-card');

    if (recapCards.length > 0 && 'IntersectionObserver' in window) {
        const recapObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, index * 100);

                    recapObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        recapCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(40px) scale(0.9)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            recapObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DES BADGES NUMÉROTÉS
    // ============================================================
    const valueBadges = document.querySelectorAll('.value-number-badge');

    valueBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.15) rotate(10deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        badge.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    });

    // ============================================================
    // 4. ANIMATION DES ICÔNES DE VALEURS
    // ============================================================
    const valueIcons = document.querySelectorAll('.value-icon-large');

    valueIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function () {
            const innerIcon = this.querySelector('i');
            if (innerIcon) {
                innerIcon.style.transform = 'scale(1.2)';
                innerIcon.style.transition = 'transform 0.3s ease';
            }
        });

        icon.addEventListener('mouseleave', function () {
            const innerIcon = this.querySelector('i');
            if (innerIcon) {
                innerIcon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 5. ANIMATION DES ÉLÉMENTS DE LA LISTE (CHECK-LIST)
    // ============================================================
    const checkListItems = document.querySelectorAll('.check-list li');

    if (checkListItems.length > 0 && 'IntersectionObserver' in window) {
        const listObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 80);

                    listObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10px 0px'
        });

        checkListItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-15px)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            listObserver.observe(item);
        });
    }

    // ============================================================
    // 6. COMPTEUR DE L'ANNÉE D'EXPÉRIENCE
    // ============================================================
    const experienceSpans = document.querySelectorAll('.experience-year');

    if (experienceSpans.length > 0) {
        const creationYear = 2017;
        const currentYear = new Date().getFullYear();
        const experience = currentYear - creationYear;

        experienceSpans.forEach(span => {
            span.textContent = experience;
        });
    }

    // ============================================================
    // 7. EFFET PARALLAXE LÉGER SUR LES IMAGES
    // ============================================================
    const valueImages = document.querySelectorAll('.content-image');

    if (valueImages.length > 0 && window.innerWidth > 768) {
        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    valueImages.forEach(img => {
                        const rect = img.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        if (rect.top < windowHeight && rect.bottom > 0) {
                            const scrollPosition = (windowHeight - rect.top) / (windowHeight + rect.height);
                            const translateY = scrollPosition * 25 - 12;

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
    // 8. MISE EN ÉVIDENCE DE LA VALEUR AU SURVOL
    // ============================================================
    const valueSections = document.querySelectorAll('.page-section-light .content-grid, .page-section-white .content-grid');

    valueSections.forEach(section => {
        section.addEventListener('mouseenter', function () {
            const badge = this.querySelector('.value-number-badge');
            const icon = this.querySelector('.value-icon-large');

            if (badge) {
                badge.style.boxShadow = '0 8px 25px rgba(211, 47, 47, 0.5)';
                badge.style.transition = 'box-shadow 0.3s ease';
            }
            if (icon) {
                icon.style.boxShadow = '0 8px 25px rgba(22, 85, 143, 0.3)';
                icon.style.transition = 'box-shadow 0.3s ease';
            }
        });

        section.addEventListener('mouseleave', function () {
            const badge = this.querySelector('.value-number-badge');
            const icon = this.querySelector('.value-icon-large');

            if (badge) {
                badge.style.boxShadow = '0 4px 15px rgba(22, 85, 143, 0.3)';
            }
            if (icon) {
                icon.style.boxShadow = 'none';
            }
        });
    });

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c💎 AHD-RDC %c— Page Nos Valeurs chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});