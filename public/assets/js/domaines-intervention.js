/**
 * AHD-RDC — SCRIPTS PAGE DOMAINES D'INTERVENTION
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES SECTIONS DOMAINES AU SCROLL
    // ============================================================
    const domaineSections = document.querySelectorAll('.domaine-detail-section');

    if (domaineSections.length > 0 && 'IntersectionObserver' in window) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const section = entry.target;

                    setTimeout(() => {
                        section.classList.add('animated');
                    }, index * 100);

                    sectionObserver.unobserve(section);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        domaineSections.forEach(section => {
            sectionObserver.observe(section);
        });
    }

    // ============================================================
    // 2. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.domaine-stat-card');

    if (statCards.length > 0 && 'IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('animated');
                    }, index * 120);

                    statObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -20px 0px'
        });

        statCards.forEach(card => {
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 3. ANIMATION DES CHIFFRES DANS LES STATISTIQUES
    // ============================================================
    const statNumbers = document.querySelectorAll('.domaine-stat-number');

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
            threshold: 0.6
        });

        statNumbers.forEach(num => numberObserver.observe(num));
    }

    function animateNumber(element, target) {
        const duration = 1500;
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
                element.style.transform = 'scale(1.1)';
                element.style.transition = 'transform 0.2s ease';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 200);
            }
        }

        requestAnimationFrame(update);
    }

    // ============================================================
    // 4. EFFET DE SURVOL SUR LES ICÔNES DES DOMAINES
    // ============================================================
    const domaineIcons = document.querySelectorAll('.domaine-detail-icon');

    domaineIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.08) rotate(-5deg)';
            this.style.transition = 'all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        icon.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'all 0.4s ease';
        });
    });

    // ============================================================
    // 5. EFFET PARALLAXE SUR LES CERCLES DÉCORATIFS
    // ============================================================
    const domaineSectionsAll = document.querySelectorAll('.domaine-detail-section');

    if (domaineSectionsAll.length > 0 && window.innerWidth > 768) {
        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    domaineSectionsAll.forEach(section => {
                        const pseudoBefore = section.querySelector('::before');
                        const rect = section.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        if (rect.top < windowHeight && rect.bottom > 0) {
                            const scrollPosition = (windowHeight - rect.top) / (windowHeight + rect.height);
                            const translateY = scrollPosition * 30 - 15;

                            const circle = section.querySelector('.domaine-detail-visual .domaine-detail-icon');
                            if (circle) {
                                circle.style.transform = `translateY(${translateY * 0.3}px)`;
                            }
                        }
                    });

                    ticking = false;
                });

                ticking = true;
            }
        }, { passive: true });
    }

    // ============================================================
    // 6. SMOOTH SCROLL POUR LES ANCRES
    // ============================================================
    const hash = window.location.hash;

    if (hash) {
        const targetElement = document.querySelector(hash);

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
            }, 400);
        }
    }

    // ============================================================
    // 7. MISE EN ÉVIDENCE DU DOMAINE ACTIF
    // ============================================================
    const domaineLinks = document.querySelectorAll('.domaine-detail-actions .btn');

    domaineLinks.forEach(link => {
        link.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 10px 25px rgba(22, 85, 143, 0.25)';
            this.style.transition = 'all 0.3s ease';
        });

        link.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c📐 AHD-RDC %c— Page Domaines d\'intervention chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});