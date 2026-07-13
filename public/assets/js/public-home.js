/**
 * AHD-RDC — SCRIPTS PAGE D'ACCUEIL
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. HERO SLIDER — Défilement automatique avec navigation
    // ============================================================
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroIndicators = document.querySelectorAll('.hero-indicator');
    const heroPrev = document.getElementById('heroPrev');
    const heroNext = document.getElementById('heroNext');
    const hero = document.querySelector('.hero');

    if (heroSlides.length > 0) {
        let currentSlide = 0;
        let slideInterval;
        const slideDuration = 6000; // 6 secondes par slide
        let isTransitioning = false;

        function goToSlide(index) {
            if (isTransitioning) return;
            isTransitioning = true;

            // Retirer la classe active de tous les slides et indicateurs
            heroSlides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.zIndex = '0';
            });
            heroIndicators.forEach(indicator => indicator.classList.remove('active'));

            // Activer le slide courant
            heroSlides[index].classList.add('active');
            heroSlides[index].style.zIndex = '1';
            if (heroIndicators[index]) {
                heroIndicators[index].classList.add('active');
            }

            currentSlide = index;

            // Débloquer la transition après l'animation
            setTimeout(() => {
                isTransitioning = false;
            }, 800);
        }

        function nextSlide() {
            const next = (currentSlide + 1) % heroSlides.length;
            goToSlide(next);
        }

        function prevSlide() {
            const prev = (currentSlide - 1 + heroSlides.length) % heroSlides.length;
            goToSlide(prev);
        }

        // Démarrer le slider automatique
        function startSlider() {
            stopSlider();
            slideInterval = setInterval(nextSlide, slideDuration);
        }

        function stopSlider() {
            if (slideInterval) {
                clearInterval(slideInterval);
                slideInterval = null;
            }
        }

        // Clic sur les indicateurs
        heroIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', function () {
                if (index !== currentSlide) {
                    stopSlider();
                    goToSlide(index);
                    startSlider();
                }
            });
        });

        // Flèches de navigation
        if (heroPrev) {
            heroPrev.addEventListener('click', function () {
                stopSlider();
                prevSlide();
                startSlider();
            });
        }

        if (heroNext) {
            heroNext.addEventListener('click', function () {
                stopSlider();
                nextSlide();
                startSlider();
            });
        }

        // Navigation au clavier
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                stopSlider();
                prevSlide();
                startSlider();
            } else if (e.key === 'ArrowRight') {
                stopSlider();
                nextSlide();
                startSlider();
            }
        });

        // Swipe sur mobile
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;

        if (hero) {
            hero.addEventListener('touchstart', function (e) {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, { passive: true });

            hero.addEventListener('touchend', function (e) {
                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;

                const diffX = touchStartX - touchEndX;
                const diffY = touchStartY - touchEndY;

                // Ne déclencher que si le swipe est plus horizontal que vertical
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                    stopSlider();
                    if (diffX > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                    startSlider();
                }
            }, { passive: true });
        }

        // Pause au survol
        if (hero) {
            hero.addEventListener('mouseenter', stopSlider);
            hero.addEventListener('mouseleave', startSlider);
        }

        // Pause quand la page n'est pas visible
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopSlider();
            } else {
                startSlider();
            }
        });

        // Lancer le slider
        startSlider();
    }

    // ============================================================
    // 2. COMPTEURS ANIMÉS — Intersection Observer avec easing
    // ============================================================
    const counterValues = document.querySelectorAll('.counter-value');

    if (counterValues.length > 0 && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target')) || 0;
                    const duration = 2200;
                    const startTime = performance.now();
                    const startValue = 0;

                    function updateCounter(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        // Fonction d'easing easeOutExpo pour un effet de décélération fluide
                        const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

                        const currentValue = Math.floor(startValue + (target - startValue) * eased);
                        counter.textContent = currentValue.toLocaleString('fr-FR');

                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target.toLocaleString('fr-FR');
                            // Petite animation de pulse à la fin
                            counter.style.transform = 'scale(1.1)';
                            setTimeout(() => {
                                counter.style.transform = 'scale(1)';
                            }, 200);
                        }
                    }

                    requestAnimationFrame(updateCounter);
                    counterObserver.unobserve(counter);
                }
            });
        }, {
            threshold: 0.4,
            rootMargin: '0px 0px -30px 0px'
        });

        counterValues.forEach(counter => {
            counter.style.transition = 'transform 0.2s ease';
            counterObserver.observe(counter);
        });
    }

    // ============================================================
    // 3. ANIMATIONS DES CARTES AU SCROLL — Effet cascade
    // ============================================================
    const animatedCards = document.querySelectorAll(
        '.domain-card, .project-card, .testimonial-card, .about-value-item'
    );

    if (animatedCards.length > 0 && 'IntersectionObserver' in window) {
        const cardObserver = new IntersectionObserver((entries) => {
            let delay = 0;

            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, delay);

                    delay += 80; // Délai progressif pour l'effet cascade
                    cardObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        animatedCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(35px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            cardObserver.observe(card);
        });
    }

    // ============================================================
    // 4. PROGRESS BARS ANIMÉES
    // ============================================================
    const progressBars = document.querySelectorAll('.progress-fill');

    if (progressBars.length > 0 && 'IntersectionObserver' in window) {
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.getAttribute('data-width') || bar.style.width || '0%';

                    // Réinitialiser puis animer
                    bar.style.width = '0%';
                    bar.style.transition = 'width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';

                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 300);

                    progressObserver.unobserve(bar);
                }
            });
        }, {
            threshold: 0.5,
            rootMargin: '0px 0px -20px 0px'
        });

        progressBars.forEach(bar => progressObserver.observe(bar));
    }

    // ============================================================
    // 5. EFFET PARALLAXE LÉGER SUR LE HERO
    // ============================================================
    if (hero && window.innerWidth > 768) {
        let ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    const scrollY = window.scrollY;
                    const heroHeight = hero.offsetHeight;

                    if (scrollY < heroHeight) {
                        const parallaxValue = scrollY * 0.25;
                        const activeSlide = hero.querySelector('.hero-slide.active');

                        if (activeSlide) {
                            activeSlide.style.transform = `translateY(${parallaxValue}px)`;
                        }

                        // Effet de fondu sur le contenu
                        const heroContent = hero.querySelector('.hero-content');
                        if (heroContent && scrollY > 100) {
                            const opacity = 1 - (scrollY - 100) / (heroHeight - 200);
                            heroContent.style.opacity = Math.max(opacity, 0.3);
                            heroContent.style.transform = `translateY(${parallaxValue * 0.5}px)`;
                            heroContent.style.transition = 'none';
                        } else if (heroContent) {
                            heroContent.style.opacity = '1';
                            heroContent.style.transform = 'translateY(0)';
                        }
                    }

                    ticking = false;
                });

                ticking = true;
            }
        }, { passive: true });
    }

    // ============================================================
    // 6. EFFET 3D AU SURVOL SUR LES CARTES DOMAINES
    // ============================================================
    const domainCards = document.querySelectorAll('.domain-card');

    domainCards.forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / centerY * -6;
            const rotateY = (x - centerX) / centerX * 6;

            card.style.transform = `perspective(600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
            card.style.transition = 'transform 0.1s ease, box-shadow 0.3s ease, border-color 0.3s ease';
        });

        card.addEventListener('mouseleave', function () {
            card.style.transform = 'perspective(600px) rotateX(0) rotateY(0) translateY(0)';
            card.style.transition = 'transform 0.5s ease, box-shadow 0.3s ease, border-color 0.3s ease';
        });
    });

    // ============================================================
    // 7. COMPTEUR DE L'ANNÉE EN COURS POUR LE COPYRIGHT
    // ============================================================
    const yearElements = document.querySelectorAll('.current-year');

    if (yearElements.length > 0) {
        const currentYear = new Date().getFullYear();
        yearElements.forEach(el => {
            el.textContent = currentYear;
        });
    }

    // ============================================================
    // 8. LAZY LOADING DES IMAGES DANS LES CARTES PROJETS
    // ============================================================
    const lazyProjectImages = document.querySelectorAll('.project-card-image img[loading="lazy"]');

    if (lazyProjectImages.length > 0 && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;

                    // Ajouter une classe pour l'animation de chargement
                    img.classList.add('image-loaded');

                    // Effet de fondu à l'apparition
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.5s ease';
                    img.onload = function () {
                        img.style.opacity = '1';
                    };
                    // Si l'image est déjà en cache
                    if (img.complete) {
                        img.style.opacity = '1';
                    }

                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px 0px'
        });

        lazyProjectImages.forEach(img => imageObserver.observe(img));
    }

    // ============================================================
    // 9. SMOOTH SCROLL POUR LES ANCRES INTERNES
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
                const totalOffset = headerHeight + topbarHeight + 20;

                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - totalOffset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ============================================================
    // 10. ANIMATION DES CHIFFRES DANS LE BADGE DE L'IMAGE ABOUT
    // ============================================================
    const badgeNumber = document.querySelector('.badge-number');

    if (badgeNumber && 'IntersectionObserver' in window) {
        const badgeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const text = badgeNumber.textContent;
                    const number = parseInt(text) || 0;

                    if (number > 0) {
                        badgeNumber.style.transform = 'scale(1.3)';
                        badgeNumber.style.transition = 'transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';

                        setTimeout(() => {
                            badgeNumber.style.transform = 'scale(1)';
                        }, 400);
                    }

                    badgeObserver.unobserve(badgeNumber);
                }
            });
        }, {
            threshold: 0.6
        });

        badgeObserver.observe(badgeNumber);
    }

    // ============================================================
    // 11. DÉTECTION DU MODE SOMBRE DU SYSTÈME
    // ============================================================
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

    function handleDarkModeChange(e) {
        if (e.matches) {
            document.body.classList.add('dark-mode-preference');
        } else {
            document.body.classList.remove('dark-mode-preference');
        }
    }

    darkModeMediaQuery.addEventListener('change', handleDarkModeChange);
    // Vérification initiale
    handleDarkModeChange(darkModeMediaQuery);

    // ============================================================
    // 12. INDICATEUR DE DÉFILEMENT (Scroll Progress)
    // ============================================================
    const scrollProgressBar = document.createElement('div');
    scrollProgressBar.className = 'scroll-progress-bar';
    scrollProgressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, var(--color-accent, #D32F2F) 0%, var(--color-primary, #16558F) 100%);
        z-index: 9999;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(scrollProgressBar);

    window.addEventListener('scroll', function () {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        scrollProgressBar.style.width = Math.min(scrollPercent, 100) + '%';
    }, { passive: true });

    // ============================================================
    // 13. MESSAGE DE BIENVENUE DANS LA CONSOLE
    // ============================================================
    console.log(
        '%c🚀 AHD-RDC %c— Action Humanitaire pour le Développement en RD Congo',
        'color: #16558F; font-weight: bold; font-size: 16px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%c« Être ensemble pour le bien de la communauté »',
        'color: #D32F2F; font-style: italic; font-size: 13px;'
    );
    console.log(
        '%c🏗️ Plateforme développée avec ❤️ | Minova, Kalehe, Sud-Kivu, RD Congo',
        'color: #999; font-size: 10px;'
    );

    // ============================================================
    // 14. RÉINITIALISER LES ANIMATIONS SI L'UTILISATEUR PRÉFÈRE
    //     LA RÉDUCTION DE MOUVEMENT (Accessibilité)
    // ============================================================
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (prefersReducedMotion.matches) {
        // Désactiver le slider automatique
        if (slideInterval) {
            clearInterval(slideInterval);
        }

        // Réduire les animations
        document.querySelectorAll('*').forEach(el => {
            el.style.transitionDuration = '0.01ms !important';
            el.style.animationDuration = '0.01ms !important';
        });
    }

});