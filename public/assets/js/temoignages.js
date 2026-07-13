/**
 * AHD-RDC — SCRIPTS PAGE TÉMOIGNAGES
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES TÉMOIGNAGES AU SCROLL
    // ============================================================
    const temoignageCards = document.querySelectorAll('.temoignage-card');

    if (temoignageCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 100);

                    cardObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        temoignageCards.forEach(card => {
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 2. EFFET DE SURVOL SUR LES CARTES
    // ============================================================
    temoignageCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const quote = this.querySelector('.temoignage-card-quote');
            const avatar = this.querySelector('.temoignage-card-avatar');
            const categorie = this.querySelector('.temoignage-card-categorie');

            if (quote) {
                quote.style.opacity = '0.7';
                quote.style.transform = 'scale(1.1)';
                quote.style.transition = 'all 0.3s ease';
            }
            if (avatar) {
                avatar.style.transform = 'scale(1.1) rotate(-5deg)';
                avatar.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            if (categorie) {
                categorie.style.background = 'var(--color-primary)';
                categorie.style.color = '#fff';
                categorie.style.transition = 'all 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const quote = this.querySelector('.temoignage-card-quote');
            const avatar = this.querySelector('.temoignage-card-avatar');
            const categorie = this.querySelector('.temoignage-card-categorie');

            if (quote) {
                quote.style.opacity = '0.4';
                quote.style.transform = 'scale(1)';
            }
            if (avatar) {
                avatar.style.transform = 'scale(1) rotate(0deg)';
            }
            if (categorie) {
                categorie.style.background = 'var(--color-primary-lighter)';
                categorie.style.color = 'var(--color-primary)';
            }
        });
    });

    // ============================================================
    // 3. ANIMATION DES BOUTONS DE FILTRE
    // ============================================================
    const filtreBtns = document.querySelectorAll('.filtre-zone-btn');

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
    // 4. COMPTEUR DE TÉMOIGNAGES
    // ============================================================
    const countElement = document.querySelector('.temoignages-count strong');

    if (countElement) {
        const targetNumber = parseInt(countElement.textContent) || 0;

        if (targetNumber > 0 && 'IntersectionObserver' in window) {
            const countObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateNumber(countElement, targetNumber);
                        countObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            countObserver.observe(countElement);
        }
    }

    function animateNumber(element, target) {
        const duration = 1000;
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
    // 5. CONSERVER LA POSITION APRÈS FILTRE
    // ============================================================
    const temoignagesGrid = document.querySelector('.temoignages-grid-section');

    if (temoignagesGrid && window.location.search) {
        setTimeout(() => {
            const headerHeight = document.getElementById('header')?.offsetHeight || 90;
            const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
            const totalOffset = headerHeight + topbarHeight + 30;

            const targetPosition = temoignagesGrid.getBoundingClientRect().top + window.pageYOffset - totalOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }, 300);
    }

    // ============================================================
    // 6. INITIALISATION
    // ============================================================
    console.log(
        '%c💬 AHD-RDC %c— Page Témoignages chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});