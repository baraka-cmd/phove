/**
 * AHD-RDC — SCRIPTS PAGE DEVENIR PARTENAIRE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SÉLECTION DES RADIOS — EFFET VISUEL
    // ============================================================
    const radioItems = document.querySelectorAll('.partenaire-radio-item');

    radioItems.forEach(item => {
        const radio = item.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function () {
                const name = this.getAttribute('name');
                document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    const parent = r.closest('.partenaire-radio-item');
                    if (parent) {
                        parent.style.background = 'var(--color-bg-light)';
                        parent.style.borderColor = 'transparent';
                        parent.style.color = '#555';
                        parent.style.fontWeight = '400';
                    }
                });
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
    // 2. VALIDATION DU FORMULAIRE
    // ============================================================
    const partenaireForm = document.getElementById('partenaireForm');
    const partenaireSubmitBtn = document.getElementById('partenaireSubmitBtn');

    if (partenaireForm && partenaireSubmitBtn) {
        partenaireForm.addEventListener('submit', function (e) {
            const organisation = document.getElementById('organisation').value.trim();
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const telephone = document.getElementById('telephone').value.trim();
            const sujet = document.getElementById('sujet').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!organisation || !nom || !email || !telephone || !sujet || !message) {
                e.preventDefault();

                ['organisation', 'nom', 'email', 'telephone', 'sujet', 'message'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field && !field.value.trim()) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fff5f5';
                    }
                });

                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                document.getElementById('email').style.borderColor = 'var(--color-accent)';
                document.getElementById('email').style.background = '#fff5f5';
                alert('Veuillez entrer une adresse email valide.');
                return;
            }

            partenaireSubmitBtn.classList.add('btn-loading');
            partenaireSubmitBtn.disabled = true;
        });
    }

    // ============================================================
    // 3. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.partenaire-input, .partenaire-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 4. ANIMATION DES ÉTAPES AU SCROLL
    // ============================================================
    const partenaireSteps = document.querySelectorAll('.partenaire-step');

    if (partenaireSteps.length > 0 && 'IntersectionObserver' in window) {
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

        partenaireSteps.forEach(step => {
            stepObserver.observe(step);
        });
    }

    // ============================================================
    // 5. ANIMATION DES CARTES AVANTAGES
    // ============================================================
    const avantageCards = document.querySelectorAll('.partenaire-avantage-card');

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
    // 6. ANIMATION DES CARTES TYPES
    // ============================================================
    const typeCards = document.querySelectorAll('.partenaire-type-card');

    if (typeCards.length > 0 && 'IntersectionObserver' in window) {
        const typeObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('visible');
                    }, index * 100);

                    typeObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        typeCards.forEach(card => {
            typeObserver.observe(card);
        });
    }

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c🤝 AHD-RDC %c— Page Devenir partenaire chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});