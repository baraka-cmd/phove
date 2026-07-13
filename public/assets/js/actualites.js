/**
 * AHD-RDC — SCRIPTS PAGE ACTUALITÉS
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES ARTICLES AU SCROLL
    // ============================================================
    const articleCards = document.querySelectorAll('.article-card');

    if (articleCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 80);

                    cardObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        articleCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.actualite-stat-card');

    if (statCards.length > 0 && 'IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);

                    statObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -20px 0px'
        });

        statCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DES CHIFFRES STATISTIQUES
    // ============================================================
    const statNumbers = document.querySelectorAll('.actualite-stat-number');

    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        const numberObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const targetNumber = parseInt(element.textContent) || 0;

                    if (targetNumber > 0) {
                        animateNumber(element, targetNumber);
                    }

                    numberObserver.unobserve(element);
                }
            });
        }, {
            threshold: 0.5
        });

        statNumbers.forEach(num => numberObserver.observe(num));
    }

    function animateNumber(element, target) {
        const duration = 1200;
        const startTime = performance.now();
        const startValue = 0;

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(2, -8 * progress);
            const currentValue = Math.floor(startValue + (target - startValue) * eased);

            element.textContent = currentValue.toLocaleString('fr-FR');

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = target.toLocaleString('fr-FR');
            }
        }

        requestAnimationFrame(update);
    }

    // ============================================================
    // 4. RECHERCHE AVEC DÉLAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('articleSearch');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    }

    // ============================================================
    // 5. FILTRES ACTIFS — SURVOL
    // ============================================================
    const filtreBtns = document.querySelectorAll('.filtre-btn');

    filtreBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function () {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.2s ease';
            }
        });

        btn.addEventListener('mouseleave', function () {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });

    // ============================================================
    // 6. EFFET DE SURVOL SUR LES CARTES ARTICLES
    // ============================================================
    articleCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const badge = this.querySelector('.article-card-badge');
            const pin = this.querySelector('.article-card-pin');

            if (badge) {
                badge.style.transform = 'scale(1.05)';
                badge.style.transition = 'transform 0.3s ease';
            }
            if (pin) {
                pin.style.transform = 'scale(1.1) rotate(10deg)';
                pin.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const badge = this.querySelector('.article-card-badge');
            const pin = this.querySelector('.article-card-pin');

            if (badge) badge.style.transform = 'scale(1)';
            if (pin) {
                pin.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // ============================================================
    // 7. CONSERVER LA POSITION APRÈS FILTRE
    // ============================================================
    const actualitesListe = document.getElementById('actualites-liste');

    if (actualitesListe && window.location.search) {
        setTimeout(() => {
            const headerHeight = document.getElementById('header')?.offsetHeight || 90;
            const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
            const totalOffset = headerHeight + topbarHeight + 30;

            const targetPosition = actualitesListe.getBoundingClientRect().top + window.pageYOffset - totalOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }, 300);
    }

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c📰 AHD-RDC %c— Page Actualités chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});