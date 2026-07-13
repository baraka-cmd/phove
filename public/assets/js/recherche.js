/**
 * AHD-RDC — SCRIPTS PAGE RECHERCHE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. FOCUS AUTO SUR LA BARRE DE RECHERCHE
    // ============================================================
    const searchInput = document.querySelector('.recherche-bar-input');

    if (searchInput && !searchInput.value.trim()) {
        searchInput.focus();
    }

    // ============================================================
    // 2. SURVOL DES CARTES RÉSULTATS
    // ============================================================
    const rechercheCards = document.querySelectorAll('.recherche-card, .recherche-list-card, .recherche-temoignage-card');

    rechercheCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.recherche-list-icon, .recherche-doc-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        card.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.recherche-list-icon, .recherche-doc-icon');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 3. ANIMATION DES SECTIONS AU SCROLL
    // ============================================================
    const rechercheSections = document.querySelectorAll('.recherche-section');

    if (rechercheSections.length > 0 && 'IntersectionObserver' in window) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const section = entry.target;

                    setTimeout(() => {
                        section.style.opacity = '1';
                        section.style.transform = 'translateY(0)';
                    }, index * 100);

                    sectionObserver.unobserve(section);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        rechercheSections.forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(25px)';
            section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            sectionObserver.observe(section);
        });
    }

    // ============================================================
    // 4. ANIMATION DE L'ICÔNE VIDE
    // ============================================================
    const emptyIcon = document.querySelector('.recherche-empty-icon');

    if (emptyIcon) {
        emptyIcon.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.1) rotate(-5deg)';
            this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        emptyIcon.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.transition = 'transform 0.3s ease';
        });
    }

    // ============================================================
    // 5. SURVOL DES SUGGESTIONS
    // ============================================================
    const suggestionLinks = document.querySelectorAll('.recherche-suggestions-list a');

    suggestionLinks.forEach(link => {
        link.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.2s ease';
        });

        link.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // ============================================================
    // 6. COMPTEUR DE RÉSULTATS — ANIMATION DU NOMBRE
    // ============================================================
    const compteurStrong = document.querySelector('.recherche-compteur strong');

    if (compteurStrong && 'IntersectionObserver' in window) {
        const compteurObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const targetNumber = parseInt(compteurStrong.textContent) || 0;

                    if (targetNumber > 0) {
                        animateNumber(compteurStrong, targetNumber);
                    }

                    compteurObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        compteurObserver.observe(compteurStrong);
    }

    function animateNumber(element, target) {
        const duration = 800;
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
    // 7. RACCOURCI CLAVIER — FOCUS BARRE DE RECHERCHE
    // ============================================================
    document.addEventListener('keydown', function (e) {
        // Ctrl+K ou / pour focus la barre de recherche
        if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !e.ctrlKey && !e.metaKey && document.activeElement === document.body)) {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c🔍 AHD-RDC %c— Page Recherche chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});