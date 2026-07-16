/**
 * AHD-RDC — SCRIPTS FICHE DÉTAILLÉE PROJET (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES BARRES DE PROGRESSION BUDGET
    // ============================================================
    const budgetBars = document.querySelectorAll('.projet-budget-fill');

    if (budgetBars.length > 0 && 'IntersectionObserver' in window) {
        const budgetObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 200);
                    budgetObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.5 });

        budgetBars.forEach(bar => budgetObserver.observe(bar));
    }

    // ============================================================
    // 2. ANIMATION DES BARRES KPI
    // ============================================================
    const kpiBars = document.querySelectorAll('.projet-kpi-fill');

    if (kpiBars.length > 0 && 'IntersectionObserver' in window) {
        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 200);
                    kpiObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.5 });

        kpiBars.forEach(bar => kpiObserver.observe(bar));
    }

    // ============================================================
    // 3. ANIMATION DES CARTES AU SCROLL
    // ============================================================
    const animatedElements = document.querySelectorAll(
        '.projet-show-header, .projet-detail-card'
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
    // 4. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-projet');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ce projet ?\n\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 5. SURVOL DES CARTES
    // ============================================================
    const detailCards = document.querySelectorAll('.projet-detail-card');

    detailCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.projet-detail-card-header i');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.projet-detail-card-header i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 6. SURVOL DE L'IMAGE D'EN-TÊTE
    // ============================================================
    const headerImage = document.querySelector('.projet-show-image');

    if (headerImage) {
        headerImage.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });

        headerImage.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    }

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c📁 AHD-RDC %c— Fiche Projet chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});