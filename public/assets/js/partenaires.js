/**
 * AHD-RDC — SCRIPTS PAGE PARTENAIRES
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES PARTENAIRES AU SCROLL
    // ============================================================
    const partenaireCards = document.querySelectorAll('.partenaire-card');

    if (partenaireCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 150);

                    cardObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        partenaireCards.forEach(card => {
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES TYPES DE PARTENARIAT
    // ============================================================
    const typeCards = document.querySelectorAll('.partenariat-type-card');

    if (typeCards.length > 0 && 'IntersectionObserver' in window) {
        const typeObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 100);

                    typeObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        typeCards.forEach(card => {
            typeObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DE LA CARTE DEVENIR PARTENAIRE
    // ============================================================
    const devenirCard = document.querySelector('.devenir-partenaire-card');

    if (devenirCard && 'IntersectionObserver' in window) {
        const devenirObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    devenirObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -40px 0px'
        });

        devenirObserver.observe(devenirCard);
    }

    // ============================================================
    // 4. EFFET DE SURVOL SUR LES LOGOS PARTENAIRES
    // ============================================================
    const partenaireLogos = document.querySelectorAll('.partenaire-logo');

    partenaireLogos.forEach(logo => {
        logo.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.08) rotate(-3deg)';
            this.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        logo.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'all 0.3s ease';
        });
    });

    // ============================================================
    // 5. ANIMATION DU CERCLE DEVENIR PARTENAIRE
    // ============================================================
    const devenirCircle = document.querySelector('.devenir-partenaire-circle');

    if (devenirCircle) {
        let rotationDegree = 0;

        devenirCircle.addEventListener('mouseenter', function () {
            rotationDegree += 10;
            this.style.transform = `rotate(${rotationDegree}deg) scale(1.05)`;
            this.style.transition = 'all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            this.style.borderColor = 'var(--color-accent)';
        });

        devenirCircle.addEventListener('mouseleave', function () {
            this.style.transform = 'rotate(0deg) scale(1)';
            this.style.transition = 'all 0.4s ease';
            this.style.borderColor = 'rgba(22, 85, 143, 0.15)';
        });
    }

    // ============================================================
    // 6. EFFET DE SURVOL SUR LES TAGS DE PROJETS
    // ============================================================
    const projetTags = document.querySelectorAll('.partenaire-projet-tag');

    projetTags.forEach(tag => {
        tag.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.2s ease';
        });

        tag.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // ============================================================
    // 7. ANIMATION DES ÉLÉMENTS DE LA LISTE
    // ============================================================
    const checkListItems = document.querySelectorAll('.devenir-partenaire-content .check-list li');

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
    // 8. ANIMATION DES ICÔNES DES TYPES DE PARTENARIAT
    // ============================================================
    const typeIcons = document.querySelectorAll('.partenariat-type-icon');

    typeIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.15) rotate(-5deg)';
            this.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        icon.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'all 0.3s ease';
        });
    });

    // ============================================================
    // 9. BOUTON PARTAGER LA PAGE
    // ============================================================
    const shareButtons = document.querySelectorAll('.btn-share');

    shareButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url') || window.location.href;
            const title = this.getAttribute('data-title') || document.title;

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: 'Découvrez les partenaires d\'AHD-RDC',
                    url: url
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
                    btn.style.color = 'var(--color-success)';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.color = '';
                    }, 2000);
                }).catch(() => {});
            }
        });
    });

    // ============================================================
    // 10. INITIALISATION
    // ============================================================
    console.log(
        '%c🤝 AHD-RDC %c— Page Partenaires chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});