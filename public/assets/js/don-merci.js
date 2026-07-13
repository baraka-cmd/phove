/**
 * AHD-RDC — SCRIPTS PAGE REMERCIEMENT DON
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CONFETTIS
    // ============================================================
    const confettiSpans = document.querySelectorAll('.merci-confetti span');

    if (confettiSpans.length > 0) {
        confettiSpans.forEach((span, index) => {
            span.style.animationDelay = (0.1 + index * 0.08) + 's';
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES IMPACT AU SCROLL
    // ============================================================
    const impactCards = document.querySelectorAll('.merci-impact-card');

    if (impactCards.length > 0 && 'IntersectionObserver' in window) {
        const impactObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 120);

                    impactObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        impactCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(25px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            impactObserver.observe(card);
        });
    }

    // ============================================================
    // 3. BOUTON PARTAGER (Web Share API)
    // ============================================================
    const shareButtons = document.querySelectorAll('.btn-share, .merci-share-btn');

    shareButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url') || window.location.origin;
            const title = this.getAttribute('data-title') || 'Je soutiens AHD-RDC';
            const text = this.getAttribute('data-text') || 'Je viens de faire un don à AHD-RDC pour soutenir leur action humanitaire en République Démocratique du Congo.';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).catch(() => {});
            } else {
                // Fallback : copier le lien
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
                    btn.style.background = '#28a745';
                    btn.style.borderColor = '#28a745';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.background = '';
                        btn.style.borderColor = '';
                    }, 2500);
                }).catch(() => {});
            }
        });
    });

    // ============================================================
    // 4. EFFET DE SURVOL SUR LES CARTES IMPACT
    // ============================================================
    impactCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.merci-impact-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(-5deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.merci-impact-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
                icon.style.transition = 'all 0.3s ease';
            }
        });
    });

    // ============================================================
    // 5. EFFET DE SURVOL SUR LA CARTE PRINCIPALE
    // ============================================================
    const merciCard = document.querySelector('.merci-card');

    if (merciCard) {
        merciCard.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.merci-icon');
            if (icon) {
                icon.style.transform = 'scale(1.08)';
                icon.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
        });

        merciCard.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.merci-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
    }

    // ============================================================
    // 6. ANIMATION DES CARTES MOBILE MONEY
    // ============================================================
    const mobileCards = document.querySelectorAll('.merci-mobile-number');

    mobileCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 6px 20px rgba(0,0,0,0.08)';
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // ============================================================
    // 7. IMPRESSION DU RÉCAPITULATIF
    // ============================================================
    const printButtons = document.querySelectorAll('.btn-print');

    printButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            window.print();
        });
    });

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c🎉 AHD-RDC %c— Page Remerciement chargée',
        'color: #28a745; font-weight: bold;',
        'color: #777;'
    );
    console.log(
        '%cMerci pour votre générosité ! Votre don change des vies en RD Congo.',
        'color: #D32F2F; font-style: italic; font-size: 12px;'
    );

});