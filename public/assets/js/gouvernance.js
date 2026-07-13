/**
 * AHD-RDC — SCRIPTS PAGE GOUVERNANCE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES D'ORGANES — EFFET CASCADE
    // ============================================================
    const organCards = document.querySelectorAll('.organ-preview-card');

    if (organCards.length > 0 && 'IntersectionObserver' in window) {
        const organObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                    }, index * 100);

                    organObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        organCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(40px) scale(0.9)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            organObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES MEMBRES DE L'ÉQUIPE
    // ============================================================
    const teamCards = document.querySelectorAll('.team-card');

    if (teamCards.length > 0 && 'IntersectionObserver' in window) {
        const teamObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateX(0)';
                    }, index * 120);

                    teamObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        teamCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            teamObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DES GRILLES DE CONTENU
    // ============================================================
    const contentGrids = document.querySelectorAll('.content-grid');
    const teamGrids = document.querySelectorAll('.team-grid');
    const organsGrids = document.querySelectorAll('.organs-overview');

    const allGrids = [...contentGrids, ...teamGrids, ...organsGrids];

    if (allGrids.length > 0 && 'IntersectionObserver' in window) {
        const gridObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    gridObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        allGrids.forEach(grid => {
            gridObserver.observe(grid);
        });
    }

    // ============================================================
    // 4. ANIMATION DES AVATARS — ROTATION AU SURVOL
    // ============================================================
    const avatars = document.querySelectorAll('.team-avatar');

    avatars.forEach(avatar => {
        avatar.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.15) rotate(8deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        avatar.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    });

    // ============================================================
    // 5. SMOOTH SCROLL VERS LES SECTIONS
    // ============================================================
    const organLinks = document.querySelectorAll('.organ-preview-link');

    organLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();

                const headerHeight = document.getElementById('header')?.offsetHeight || 90;
                const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
                const totalOffset = headerHeight + topbarHeight + 30;

                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - totalOffset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ============================================================
    // 6. ANIMATION DES BLOCS DE CITATION
    // ============================================================
    const quoteBlocks = document.querySelectorAll('.quote-block');

    if (quoteBlocks.length > 0 && 'IntersectionObserver' in window) {
        const quoteObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    quoteObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3
        });

        quoteBlocks.forEach(quote => {
            quote.style.opacity = '0';
            quote.style.transform = 'translateY(20px)';
            quote.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            quoteObserver.observe(quote);
        });
    }

    // ============================================================
    // 7. EFFET DE SURVOL SUR LES CARTES D'ORGANES
    // ============================================================
    organCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.organ-preview-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.organ-preview-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 8. ANIMATION DES ÉLÉMENTS DE LISTE
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
                    }, index * 60);

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
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c🏛️ AHD-RDC %c— Page Gouvernance chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});