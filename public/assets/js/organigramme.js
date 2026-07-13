/**
 * AHD-RDC — SCRIPTS PAGE ORGANIGRAMME
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES BOÎTES DE L'ORGANIGRAMME — EFFET CASCADE
    // ============================================================
    const orgBoxes = document.querySelectorAll('.org-box');

    if (orgBoxes.length > 0 && 'IntersectionObserver' in window) {
        const boxObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const box = entry.target;

                    setTimeout(() => {
                        box.classList.add('animated');
                    }, index * 100);

                    boxObserver.unobserve(box);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -30px 0px'
        });

        orgBoxes.forEach(box => {
            boxObserver.observe(box);
        });
    }

    // ============================================================
    // 2. EFFET DE SURVOL AVEC DÉTAILS
    // ============================================================
    orgBoxes.forEach(box => {
        box.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.org-box-icon');
            const title = this.querySelector('.org-box-title');

            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(-5deg)';
                icon.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }

            if (title) {
                title.style.color = 'var(--color-primary)';
                title.style.transition = 'color 0.3s ease';
            }
        });

        box.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.org-box-icon');
            const title = this.querySelector('.org-box-title');

            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
                icon.style.transition = 'transform 0.3s ease';
            }

            if (title) {
                title.style.color = 'var(--color-text-primary)';
            }
        });
    });

    // ============================================================
    // 3. ANIMATION DES LIGNES DE CONNEXION
    // ============================================================
    const orgLines = document.querySelectorAll('.org-line, .org-line-branch');

    if (orgLines.length > 0 && 'IntersectionObserver' in window) {
        const lineObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const line = entry.target;

                    setTimeout(() => {
                        line.style.opacity = '1';
                        line.style.transform = 'scaleY(1)';
                    }, index * 50);

                    lineObserver.unobserve(line);
                }
            });
        }, {
            threshold: 0.5
        });

        orgLines.forEach(line => {
            line.style.opacity = '0';
            line.style.transform = 'scaleY(0)';
            line.style.transformOrigin = 'top';
            line.style.transition = 'opacity 0.3s ease, transform 0.4s ease';
            lineObserver.observe(line);
        });
    }

    // ============================================================
    // 4. ANIMATION DES ÉLÉMENTS DE LA LÉGENDE
    // ============================================================
    const legendItems = document.querySelectorAll('.legend-item');

    if (legendItems.length > 0 && 'IntersectionObserver' in window) {
        const legendObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const item = entry.target;

                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, index * 60);

                    legendObserver.unobserve(item);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10px 0px'
        });

        legendItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-15px)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            legendObserver.observe(item);
        });
    }

    // ============================================================
    // 5. ANIMATION DES ÉLÉMENTS DE LA LISTE
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
    // 6. DÉFILEMENT HORIZONTAL TACTILE POUR MOBILE
    // ============================================================
    const orgChart = document.querySelector('.org-chart');

    if (orgChart && window.innerWidth <= 768) {
        let isDown = false;
        let startX;
        let scrollLeft;

        orgChart.addEventListener('mousedown', function (e) {
            isDown = true;
            orgChart.style.cursor = 'grabbing';
            startX = e.pageX - orgChart.offsetLeft;
            scrollLeft = orgChart.scrollLeft;
        });

        orgChart.addEventListener('mouseleave', function () {
            isDown = false;
            orgChart.style.cursor = 'grab';
        });

        orgChart.addEventListener('mouseup', function () {
            isDown = false;
            orgChart.style.cursor = 'grab';
        });

        orgChart.addEventListener('mousemove', function (e) {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - orgChart.offsetLeft;
            const walk = (x - startX) * 2;
            orgChart.scrollLeft = scrollLeft - walk;
        });

        // Style du curseur
        orgChart.style.cursor = 'grab';
        orgChart.style.overflowX = 'auto';
        orgChart.style.scrollBehavior = 'smooth';
    }

    // ============================================================
    // 7. ANIMATION DE LA GRILLE DE CONTENU
    // ============================================================
    const contentGrid = document.querySelector('.content-grid');

    if (contentGrid && 'IntersectionObserver' in window) {
        const gridObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    gridObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        contentGrid.style.opacity = '0';
        contentGrid.style.transform = 'translateY(30px)';
        contentGrid.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        gridObserver.observe(contentGrid);
    }

    // ============================================================
    // 8. TOOLTIP SUR LES BOÎTES
    // ============================================================
    orgBoxes.forEach(box => {
        const subtitle = box.querySelector('.org-box-subtitle');
        const info = box.querySelector('.org-box-info');

        if (subtitle || info) {
            box.setAttribute('data-tooltip', 'Cliquez pour plus de détails');
        }
    });

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c📊 AHD-RDC %c— Page Organigramme chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});