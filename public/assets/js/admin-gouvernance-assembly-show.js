/**
 * AHD-RDC — SCRIPTS DÉTAIL ASSEMBLÉE GÉNÉRALE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES AU SCROLL
    // ============================================================
    const animatedElements = document.querySelectorAll(
        '.assembly-show-header, .assembly-detail-card, .assembly-pv-section'
    );

    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }

    // ============================================================
    // 2. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-assembly');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement cette Assemblée Générale ?\n\nCette action est irréversible et supprimera également le PV associé.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 3. SURVOL DES CARTES
    // ============================================================
    const detailCards = document.querySelectorAll('.assembly-detail-card');

    detailCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.assembly-detail-card-header i');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.assembly-detail-card-header i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 4. SURVOL DE L'EN-TÊTE
    // ============================================================
    const headerIcon = document.querySelector('.assembly-show-icon');

    if (headerIcon) {
        headerIcon.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05) rotate(-3deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        headerIcon.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    }

    // ============================================================
    // 5. INITIALISATION
    // ============================================================
    console.log(
        '%c📋 AHD-RDC %c— Détail AG chargé',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});