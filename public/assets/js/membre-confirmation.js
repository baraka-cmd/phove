/**
 * AHD-RDC — SCRIPTS PAGE CONFIRMATION ADHÉSION
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DE L'ICÔNE DE SUCCÈS (rebond)
    // ============================================================
    const successIcon = document.querySelector('.membre-confirmation-icon');

    if (successIcon) {
        setTimeout(() => {
            successIcon.style.transform = 'scale(1.1)';
            successIcon.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        }, 600);

        setTimeout(() => {
            successIcon.style.transform = 'scale(1)';
            successIcon.style.transition = 'transform 0.3s ease';
        }, 900);
    }

    // ============================================================
    // 2. ANIMATION DES CARTES ÉTAPES AU SURVOL
    // ============================================================
    const etapeCards = document.querySelectorAll('.membre-etape-card');

    etapeCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const numero = this.querySelector('.membre-etape-numero');
            if (numero) {
                numero.style.transform = 'scale(1.1)';
                numero.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                numero.style.background = 'var(--color-accent)';
            }
        });

        card.addEventListener('mouseleave', function () {
            const numero = this.querySelector('.membre-etape-numero');
            if (numero) {
                numero.style.transform = 'scale(1)';
                numero.style.background = 'var(--color-primary)';
                numero.style.transition = 'all 0.3s ease';
            }
        });
    });

    // ============================================================
    // 3. ANIMATION DES CARTES INFOS IMPORTANTES
    // ============================================================
    const infoCards = document.querySelectorAll('.membre-info-card');

    if (infoCards.length > 0 && 'IntersectionObserver' in window) {
        const infoObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateX(0)';
                    }, index * 100);

                    infoObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10px 0px'
        });

        infoCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-15px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            infoObserver.observe(card);
        });
    }

    // ============================================================
    // 4. BOUTON PARTAGER
    // ============================================================
    const shareButtons = document.querySelectorAll('.btn-share');

    shareButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const url = window.location.origin;
            const title = 'Je viens d\'adhérer à AHD-RDC';
            const text = 'Je rejoins AHD-RDC pour contribuer au développement communautaire et à la paix en République Démocratique du Congo.';

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
                    btn.style.background = '#28a745';
                    btn.style.borderColor = '#28a745';
                    btn.style.color = '#fff';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.background = '';
                        btn.style.borderColor = '';
                        btn.style.color = '';
                    }, 2500);
                }).catch(() => {});
            }
        });
    });

    // ============================================================
    // 5. INITIALISATION
    // ============================================================
    console.log(
        '%c✅ AHD-RDC %c— Page Confirmation adhésion chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );
    console.log(
        '%cBienvenue dans la communauté AHD-RDC !',
        'color: #28a745; font-style: italic; font-size: 12px;'
    );

});