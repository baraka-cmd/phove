/**
 * AHD-RDC — SCRIPTS PAGE LISTE DES PROJETS
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. GESTION DU CHANGEMENT DE VUE (GRILLE / LISTE)
    // ============================================================
    const viewButtons = document.querySelectorAll('.projets-view-btn');
    const projetsGrid = document.getElementById('projetsGrid');

    if (viewButtons.length > 0 && projetsGrid) {
        // Récupérer la préférence sauvegardée
        const savedView = getCookie('projets_view') || 'grid';
        applyView(savedView);

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const view = this.getAttribute('data-view');
                applyView(view);
                setCookie('projets_view', view, 30);
            });
        });

        function applyView(view) {
            // Mettre à jour les boutons
            viewButtons.forEach(b => {
                b.classList.remove('active');
                if (b.getAttribute('data-view') === view) {
                    b.classList.add('active');
                }
            });

            // Mettre à jour la grille
            if (view === 'list') {
                projetsGrid.classList.add('list-view');
            } else {
                projetsGrid.classList.remove('list-view');
            }
        }
    }

    // ============================================================
    // 2. GESTION DES COOKIES
    // ============================================================
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
    }

    function getCookie(name) {
        const nameEQ = name + '=';
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(nameEQ) === 0) {
                return cookie.substring(nameEQ.length, cookie.length);
            }
        }
        return null;
    }

    // ============================================================
    // 3. ANIMATION DES CARTES PROJETS AU SCROLL
    // ============================================================
    const projetCards = document.querySelectorAll('.projet-card');

    if (projetCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 80);

                    cardObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        projetCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 4. ANIMATION DES BARRES DE PROGRESSION
    // ============================================================
    const progressBars = document.querySelectorAll('.projet-progress-fill');

    if (progressBars.length > 0 && 'IntersectionObserver' in window) {
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.getAttribute('data-width') || bar.style.width || '0%';

                    bar.style.width = '0%';
                    bar.style.transition = 'width 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';

                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 200);

                    progressObserver.unobserve(bar);
                }
            });
        }, {
            threshold: 0.5
        });

        progressBars.forEach(bar => progressObserver.observe(bar));
    }

    // ============================================================
    // 5. ANIMATION DES CARTES DE ZONES
    // ============================================================
    const zoneCards = document.querySelectorAll('.zone-card');

    if (zoneCards.length > 0 && 'IntersectionObserver' in window) {
        const zoneObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);

                    zoneObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        zoneCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(25px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            zoneObserver.observe(card);
        });
    }

    // ============================================================
    // 6. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.projet-stat-card');

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
        }, {
            threshold: 0.3
        });

        statCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(15px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 7. ANIMATION DES CHIFFRES DANS LES STATISTIQUES
    // ============================================================
    const statNumbers = document.querySelectorAll('.projet-stat-number');

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
            threshold: 0.5
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
            }
        }

        requestAnimationFrame(update);
    }

    // ============================================================
    // 8. SOUMISSION AUTOMATIQUE DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            this.closest('form').submit();
        });
    });

    // ============================================================
    // 9. RECHERCHE AVEC DÉLAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('projetSearch');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 600);
        });
    }

    // ============================================================
    // 10. EFFET DE SURVOL SUR LES CARTES PROJETS
    // ============================================================
    projetCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const badge = this.querySelector('.projet-card-badge');
            const domainBadge = this.querySelector('.projet-card-domain-badge');

            if (badge) {
                badge.style.transform = 'scale(1.05)';
                badge.style.transition = 'transform 0.3s ease';
            }
            if (domainBadge) {
                domainBadge.style.transform = 'scale(1.05)';
                domainBadge.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const badge = this.querySelector('.projet-card-badge');
            const domainBadge = this.querySelector('.projet-card-domain-badge');

            if (badge) {
                badge.style.transform = 'scale(1)';
            }
            if (domainBadge) {
                domainBadge.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 11. ACTIVER LA ZONE SI ELLE CORRESPOND AU FILTRE ACTIF
    // ============================================================
    const urlParams = new URLSearchParams(window.location.search);
    const activeZone = urlParams.get('zone');

    if (activeZone && zoneCards.length > 0) {
        zoneCards.forEach(card => {
            const cardZone = card.getAttribute('data-zone');
            if (cardZone === activeZone) {
                card.classList.add('zone-card-active');
                setTimeout(() => {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            }
        });
    }

    // ============================================================
    // 12. CONSERVER LA POSITION DE SCROLL APRÈS SOUMISSION
    // ============================================================
    const projetsListe = document.getElementById('projets-liste');

    if (projetsListe && window.location.search) {
        setTimeout(() => {
            const headerHeight = document.getElementById('header')?.offsetHeight || 90;
            const topbarHeight = document.getElementById('topbar')?.offsetHeight || 44;
            const totalOffset = headerHeight + topbarHeight + 20;

            const targetPosition = projetsListe.getBoundingClientRect().top + window.pageYOffset - totalOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }, 300);
    }

    // ============================================================
    // 13. INITIALISATION
    // ============================================================
    console.log(
        '%c📁 AHD-RDC %c— Page Projets chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});