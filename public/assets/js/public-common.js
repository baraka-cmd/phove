/**
 * AHD-RDC — SCRIPTS COMMUNS SITE PUBLIC
 * Action Humanitaire pour le Développement en RD Congo
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. STICKY HEADER — Changement d'apparence au scroll
    // ============================================================
    const header = document.getElementById('header');
    const topbar = document.getElementById('topbar');
    let lastScrollY = window.scrollY;

    function handleScroll() {
        const currentScrollY = window.scrollY;

        // 1. Gestion de la classe scrolled sur le header
        if (currentScrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // 2. Gestion de la topbar (CORRIGÉ)
        if (currentScrollY === 0) {
            // Si on est tout au début de la page, on réaffiche la topbar normalement
            topbar.classList.remove('hidden');
        } else if (currentScrollY > lastScrollY && currentScrollY > 200) {
            // Si on descend (scroll vers le bas), on cache la topbar
            topbar.classList.add('hidden');
        } else {
            // Si on remonte MAIS qu'on n'est pas encore au tout début (Y > 0),
            // on FORCE la topbar à rester cachée pour éviter qu'elle n'apparaisse trop tôt
            topbar.classList.add('hidden');
        }

        lastScrollY = currentScrollY;
    }


    window.addEventListener('scroll', handleScroll, { passive: true });

    // ============================================================
    // 2. MENU MOBILE
    // ============================================================
    const burgerMenu = document.getElementById('burgerMenu');
    const mainNav = document.getElementById('mainNav');
    const menuOverlay = document.getElementById('menuOverlay');

    if (burgerMenu && mainNav && menuOverlay) {
        // Ouvrir/fermer le menu
        burgerMenu.addEventListener('click', function () {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            menuOverlay.classList.toggle('active');
            document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
        });

        // Fermer le menu en cliquant sur l'overlay
        menuOverlay.addEventListener('click', function () {
            burgerMenu.classList.remove('active');
            mainNav.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Gestion des dropdowns dans le menu mobile
        const dropdownItems = mainNav.querySelectorAll('.has-dropdown');

        dropdownItems.forEach(item => {
            const link = item.querySelector('.header-menu-link');

            link.addEventListener('click', function (e) {
                if (window.innerWidth <= 992) {
                    e.preventDefault();
                    item.classList.toggle('dropdown-open');

                    // Fermer les autres dropdowns
                    dropdownItems.forEach(other => {
                        if (other !== item) {
                            other.classList.remove('dropdown-open');
                        }
                    });
                }
            });
        });

        // Fermer le menu si on clique sur un lien simple
        const menuLinks = mainNav.querySelectorAll('.header-menu-link:not(.has-dropdown .header-menu-link)');
        menuLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 992 && !this.closest('.has-dropdown')) {
                    burgerMenu.classList.remove('active');
                    mainNav.classList.remove('active');
                    menuOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    // ============================================================
    // 3. BARRE DE RECHERCHE
    // ============================================================
    const searchToggle = document.getElementById('searchToggle');
    const searchBar = document.getElementById('searchBar');
    const searchClose = document.getElementById('searchClose');
    const searchInput = searchBar ? searchBar.querySelector('.search-input') : null;

    if (searchToggle && searchBar && searchClose) {
        // Ouvrir la barre de recherche
        searchToggle.addEventListener('click', function () {
            searchBar.classList.toggle('active');
            if (searchBar.classList.contains('active') && searchInput) {
                setTimeout(() => searchInput.focus(), 300);
            }
        });

        // Fermer la barre de recherche
        searchClose.addEventListener('click', function () {
            searchBar.classList.remove('active');
            if (searchInput) searchInput.value = '';
        });

        // Fermer avec la touche Échap
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchBar.classList.contains('active')) {
                searchBar.classList.remove('active');
                if (searchInput) searchInput.value = '';
                searchToggle.focus();
            }
        });
    }

    // ============================================================
    // 4. SÉLECTEUR DE LANGUES
    // ============================================================
    const langToggle = document.getElementById('langToggle');
    const langDropdown = document.getElementById('langDropdown');
    const langOptions = langDropdown ? langDropdown.querySelectorAll('.topbar-lang-option') : [];
    const currentLangDisplay = document.getElementById('currentLang');

    if (langToggle && langDropdown) {
        // Ouvrir/fermer le dropdown
        langToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            langDropdown.classList.toggle('active');
            langToggle.classList.toggle('open');
        });

        // Sélectionner une langue
        langOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();
                const lang = this.getAttribute('data-lang');

                // Mettre à jour l'affichage
                langOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                if (currentLangDisplay) {
                    currentLangDisplay.textContent = lang.toUpperCase();
                }

                // Fermer le dropdown
                langDropdown.classList.remove('active');
                langToggle.classList.remove('open');

                // TODO: Implémenter le changement de langue
                console.log('Langue sélectionnée : ' + lang);
            });
        });

        // Fermer en cliquant ailleurs
        document.addEventListener('click', function (e) {
            if (!langToggle.contains(e.target) && !langDropdown.contains(e.target)) {
                langDropdown.classList.remove('active');
                langToggle.classList.remove('open');
            }
        });
    }

    // ============================================================
    // 5. BOUTON SCROLL TO TOP
    // ============================================================
    const scrollToTopBtn = document.getElementById('scrollToTop');

    if (scrollToTopBtn) {
        // Afficher/cacher le bouton
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });

        // Remonter en haut
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ============================================================
    // 6. ANIMATIONS AU SCROLL (Intersection Observer)
    // ============================================================
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        animatedElements.forEach(el => observer.observe(el));
    }

    // ============================================================
    // 7. COMPTEURS ANIMÉS
    // ============================================================
    const counters = document.querySelectorAll('.counter-value');

    if (counters.length > 0 && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target'));
                    const duration = 2000; // 2 secondes
                    const step = target / (duration / 16);
                    let current = 0;

                    function updateCounter() {
                        current += step;
                        if (current < target) {
                            counter.textContent = Math.floor(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    }

                    updateCounter();
                    counterObserver.unobserve(counter);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    // ============================================================
    // 8. SMOOTH SCROLL POUR LES ANCRES
    // ============================================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                const offsetTop = targetElement.offsetTop - header.offsetHeight - 20;

                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ============================================================
    // 9. NEWSLETTER — Validation email
    // ============================================================
    const newsletterForms = document.querySelectorAll('.footer-newsletter-form');

    newsletterForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !emailRegex.test(email)) {
                e.preventDefault();
                emailInput.classList.add('error');
                emailInput.style.borderColor = 'var(--color-accent)';
                emailInput.focus();

                // Animation de secousse
                emailInput.style.animation = 'none';
                emailInput.offsetHeight;
                emailInput.style.animation = 'shake 0.5s ease';

                setTimeout(() => {
                    emailInput.classList.remove('error');
                    emailInput.style.borderColor = '';
                }, 3000);
            }
        });
    });

    // Animation shake pour validation email
    const shakeStyle = document.createElement('style');
    shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
    `;
    document.head.appendChild(shakeStyle);

    // ============================================================
    // 10. GESTION DES DROPDOWNS AU CLAVIER (Accessibilité)
    // ============================================================
    const allDropdowns = document.querySelectorAll('.has-dropdown');

    allDropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('.header-menu-link');

        link.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                if (window.innerWidth > 992) {
                    e.preventDefault();
                    // Simuler le hover
                    dropdown.classList.toggle('keyboard-open');
                }
            }
        });
    });

    // Fermer les dropdowns au clavier avec Échap
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            allDropdowns.forEach(d => d.classList.remove('keyboard-open'));
        }
    });

    // ============================================================
    // 11. LAZY LOADING DES IMAGES
    // ============================================================
    const lazyImages = document.querySelectorAll('img[data-src]');

    if (lazyImages.length > 0 && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        }, { rootMargin: '50px' });

        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // ============================================================
    // 12. ACTIVER LE LIEN ACTIF DANS LE MENU
    // ============================================================
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.header-menu-link');

    menuLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && currentPath.startsWith(href) && href !== '/') {
            link.classList.add('active');
        }
    });

    // ============================================================
    // 13. FERMETURE AU CLIC EXTÉRIEUR DES DROPDOWNS
    // ============================================================
    document.addEventListener('click', function (e) {
        const clickedDropdown = e.target.closest('.has-dropdown');
        const clickedLang = e.target.closest('.topbar-languages');

        if (!clickedDropdown) {
            allDropdowns.forEach(d => d.classList.remove('keyboard-open'));
        }

        if (!clickedLang && langDropdown) {
            langDropdown.classList.remove('active');
            if (langToggle) langToggle.classList.remove('open');
        }
    });

    // ============================================================
    // 14. INITIALISATION
    // ============================================================
    console.log('%c🚀 AHD-RDC %c— Action Humanitaire pour le Développement en RD Congo',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 11px;');
    console.log('%c« Être ensemble pour le bien de la communauté »',
        'color: #D32F2F; font-style: italic; font-size: 12px;');
});