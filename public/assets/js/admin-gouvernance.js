/**
 * AHD-RDC — SCRIPTS GOUVERNANCE (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. CONFIRMATION DE RETRAIT D'UN MEMBRE
    // ============================================================
    const retirerButtons = document.querySelectorAll('.btn-retirer-membre');

    retirerButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce membre';
            const poste = this.getAttribute('data-poste') || '';
            const message = 'Êtes-vous sûr de vouloir retirer ' + nom + ' du poste de ' + poste + ' ?\n\nSon mandat sera marqué comme terminé.';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 2. CONFIRMATION DE SUPPRESSION D'UNE AG
    // ============================================================
    const deleteAssemblyButtons = document.querySelectorAll('.btn-delete-assembly');

    deleteAssemblyButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette Assemblée Générale ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 3. ANIMATION DES CARTES ORGANES
    // ============================================================
    const organeCards = document.querySelectorAll('.gouvernance-organe-card');

    if (organeCards.length > 0 && 'IntersectionObserver' in window) {
        const organeObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                    organeObserver.unobserve(card);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });

        organeCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            organeObserver.observe(card);
        });
    }

    // ============================================================
    // 4. ANIMATION DES CARTES STATISTIQUES AG
    // ============================================================
    const statCards = document.querySelectorAll('.assembly-stat-card');

    if (statCards.length > 0 && 'IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 80);
                    statObserver.unobserve(card);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -20px 0px' });

        statCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(15px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 5. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.assembly-stat-value, .gouvernance-organe-badge');

    if (kpiValues.length > 0 && 'IntersectionObserver' in window) {
        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const text = el.textContent.replace(/[^0-9]/g, '');
                    const target = parseInt(text) || 0;
                    if (target > 0) {
                        animateNumber(el, target);
                    }
                    kpiObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        kpiValues.forEach(el => kpiObserver.observe(el));
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
    // 6. SURVOL LIGNES TABLEAU
    // ============================================================
    const tableRows = document.querySelectorAll('.table-gouvernance tbody tr, .table-membres tbody tr');

    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background 0.2s ease';
        });
    });

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c🏛️ AHD-RDC %c— Gestion Gouvernance chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});