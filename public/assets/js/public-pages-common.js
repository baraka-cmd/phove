/**
 * AHD-RDC — SCRIPTS COMMUNS PAGES INSTITUTIONNELLES
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES ÉLÉMENTS AU SCROLL (Intersection Observer)
    // ============================================================
    const animatedElements = document.querySelectorAll(
        '.info-card, .content-block, .timeline-item, .quote-block, .number-list li, .check-list li'
    );

    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            let delay = 0;

            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const el = entry.target;

                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, delay);

                    delay += 60;
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }

    // ============================================================
    // 2. COMPTEUR POUR L'ANNÉE D'EXPÉRIENCE
    // ============================================================
    const experienceYearElements = document.querySelectorAll('.experience-year');

    if (experienceYearElements.length > 0) {
        const creationYear = 2017;
        const currentYear = new Date().getFullYear();
        const experience = currentYear - creationYear;

        experienceYearElements.forEach(el => {
            el.textContent = experience;
        });
    }

    // ============================================================
    // 3. SMOOTH SCROLL POUR LES ANCRES INTERNES
    // ============================================================
    const internalLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');

    internalLinks.forEach(link => {
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
    // 4. DÉTECTION DU HASH DANS L'URL POUR SCROLL AUTO
    // ============================================================
    if (window.location.hash) {
        const targetId = window.location.hash;
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
            setTimeout(() => {
                const headerHeight = document.getElementById('header')?.offsetHeight || 90;
                const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
                const totalOffset = headerHeight + topbarHeight + 30;

                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - totalOffset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }, 300);
        }
    }

    // ============================================================
    // 5. GESTION DES TABLEAUX RESPONSIVE
    // ============================================================
    const tables = document.querySelectorAll('.table');

    tables.forEach(table => {
        const wrapper = table.closest('.table-wrapper');
        if (wrapper && window.innerWidth < 768) {
            wrapper.setAttribute('data-responsive', 'true');
        }
    });

    // ============================================================
    // 6. TOOLTIP SUR LES ÉLÉMENTS AVEC ATTRIBUT DATA-TOOLTIP
    // ============================================================
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(el => {
        el.addEventListener('mouseenter', function (e) {
            const tooltipText = this.getAttribute('data-tooltip');

            if (tooltipText) {
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = tooltipText;
                tooltip.style.cssText = `
                    position: absolute;
                    background: var(--color-bg-dark, #1A1A1A);
                    color: #fff;
                    padding: 6px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    white-space: nowrap;
                    z-index: 1000;
                    pointer-events: none;
                    transition: opacity 0.2s ease;
                    opacity: 0;
                `;

                document.body.appendChild(tooltip);

                const rect = this.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();

                tooltip.style.left = rect.left + (rect.width / 2) - (tooltipRect.width / 2) + 'px';
                tooltip.style.top = rect.top - tooltipRect.height - 8 + 'px';

                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);

                this._tooltip = tooltip;
            }
        });

        el.addEventListener('mouseleave', function () {
            if (this._tooltip) {
                this._tooltip.style.opacity = '0';
                setTimeout(() => {
                    if (this._tooltip && this._tooltip.parentNode) {
                        this._tooltip.remove();
                    }
                    this._tooltip = null;
                }, 200);
            }
        });
    });

    // ============================================================
    // 7. LECTURE DE PROGRESSION DE PAGE
    // ============================================================
    const pageProgressBar = document.createElement('div');
    pageProgressBar.className = 'page-progress-bar';
    pageProgressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, var(--color-accent, #D32F2F) 0%, var(--color-primary, #16558F) 100%);
        z-index: 9999;
        transition: width 0.15s ease;
    `;
    document.body.appendChild(pageProgressBar);

    window.addEventListener('scroll', function () {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        pageProgressBar.style.width = Math.min(scrollPercent, 100) + '%';
    }, { passive: true });

    // ============================================================
    // 8. BOUTON IMPRIMER LA PAGE
    // ============================================================
    const printButtons = document.querySelectorAll('.btn-print');

    printButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            window.print();
        });
    });

    // ============================================================
    // 9. BOUTON PARTAGER (Web Share API)
    // ============================================================
    const shareButtons = document.querySelectorAll('.btn-share');

    shareButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.getAttribute('data-url') || window.location.href;
            const title = this.getAttribute('data-title') || document.title;
            const text = this.getAttribute('data-text') || '';

            if (navigator.share) {
                navigator.share({ title, text, url }).catch(() => {});
            } else {
                // Fallback : copier le lien
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Lien copié !';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                    }, 2000);
                }).catch(() => {});
            }
        });
    });

    // ============================================================
    // 10. EFFET DE SURVOL SUR LES CARTES D'INFORMATION
    // ============================================================
    const infoCards = document.querySelectorAll('.info-card');

    infoCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.info-card-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(-5deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.info-card-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // ============================================================
    // 11. INITIALISATION
    // ============================================================
    console.log(
        '%c📄 AHD-RDC %c— Pages institutionnelles chargées',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});