/**
 * AHD-RDC — SCRIPTS PAGE DEVENIR BÉNÉVOLE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SÉLECTION DES CHECKBOXES — EFFET VISUEL
    // ============================================================
    const checkboxItems = document.querySelectorAll('.benevole-checkbox-item');

    checkboxItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    item.style.background = 'var(--color-primary-lighter)';
                    item.style.borderColor = 'var(--color-primary)';
                    item.style.color = 'var(--color-primary)';
                    item.style.fontWeight = '600';
                } else {
                    item.style.background = 'var(--color-bg-light)';
                    item.style.borderColor = 'transparent';
                    item.style.color = '#555';
                    item.style.fontWeight = '400';
                }
            });
        }
    });

    // ============================================================
    // 2. SÉLECTION DES RADIOS — EFFET VISUEL
    // ============================================================
    const radioItems = document.querySelectorAll('.benevole-radio-item');

    radioItems.forEach(item => {
        const radio = item.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function () {
                // Réinitialiser tous les radios du même groupe
                const name = this.getAttribute('name');
                document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    const parent = r.closest('.benevole-radio-item');
                    if (parent) {
                        parent.style.background = 'var(--color-bg-light)';
                        parent.style.borderColor = 'transparent';
                        parent.style.color = '#555';
                        parent.style.fontWeight = '400';
                    }
                });
                // Activer le radio sélectionné
                if (this.checked) {
                    item.style.background = 'var(--color-primary-lighter)';
                    item.style.borderColor = 'var(--color-primary)';
                    item.style.color = 'var(--color-primary)';
                    item.style.fontWeight = '600';
                }
            });
        }
    });

    // ============================================================
    // 3. VALIDATION DU FORMULAIRE
    // ============================================================
    const benevoleForm = document.getElementById('benevoleForm');
    const benevoleSubmitBtn = document.getElementById('benevoleSubmitBtn');

    if (benevoleForm && benevoleSubmitBtn) {
        benevoleForm.addEventListener('submit', function (e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const telephone = document.getElementById('telephone').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!nom || !email || !telephone || !message) {
                e.preventDefault();
                
                // Surligner les champs vides
                ['nom', 'email', 'telephone', 'message'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field && !field.value.trim()) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fff5f5';
                    }
                });

                alert('Veuillez remplir tous les champs obligatoires (nom, email, téléphone et motivation).');
                return;
            }

            // Validation email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                document.getElementById('email').style.borderColor = 'var(--color-accent)';
                document.getElementById('email').style.background = '#fff5f5';
                alert('Veuillez entrer une adresse email valide.');
                return;
            }

            // Afficher le loader
            benevoleSubmitBtn.classList.add('btn-loading');
            benevoleSubmitBtn.disabled = true;
        });
    }

    // ============================================================
    // 4. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.benevole-input, .benevole-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 5. ANIMATION DES ÉTAPES AU SCROLL
    // ============================================================
    const benevoleSteps = document.querySelectorAll('.benevole-step');

    if (benevoleSteps.length > 0 && 'IntersectionObserver' in window) {
        const stepObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const step = entry.target;

                    setTimeout(() => {
                        step.classList.add('visible');
                    }, index * 150);

                    stepObserver.unobserve(step);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        benevoleSteps.forEach(step => {
            stepObserver.observe(step);
        });
    }

    // ============================================================
    // 6. ANIMATION DES CARTES AVANTAGES
    // ============================================================
    const avantageCards = document.querySelectorAll('.benevole-avantage-card');

    if (avantageCards.length > 0 && 'IntersectionObserver' in window) {
        const avantageObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);

                    avantageObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        avantageCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            avantageObserver.observe(card);
        });
    }

    // ============================================================
    // 7. ANIMATION DES CARTES BESOINS
    // ============================================================
    const besoinCards = document.querySelectorAll('.benevole-besoin-card');

    if (besoinCards.length > 0 && 'IntersectionObserver' in window) {
        const besoinObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('visible');
                    }, index * 100);

                    besoinObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        besoinCards.forEach(card => {
            besoinObserver.observe(card);
        });
    }

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c🤲 AHD-RDC %c— Page Devenir bénévole chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});