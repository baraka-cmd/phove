/**
 * AHD-RDC — SCRIPTS PAGE CONTACT
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. ANIMATION DES CARTES COORDONNÉES AU SCROLL
    // ============================================================
    const coordCards = document.querySelectorAll('.contact-coord-card');

    if (coordCards.length > 0 && 'IntersectionObserver' in window) {
        const coordObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);

                    coordObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -20px 0px'
        });

        coordCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(25px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            coordObserver.observe(card);
        });
    }

    // ============================================================
    // 2. VALIDATION DU FORMULAIRE
    // ============================================================
    const contactForm = document.getElementById('contactForm');
    const contactSubmitBtn = document.getElementById('contactSubmitBtn');

    if (contactForm && contactSubmitBtn) {
        contactForm.addEventListener('submit', function (e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const motif = document.getElementById('motif').value;
            const sujet = document.getElementById('sujet').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!nom || !email || !motif || !sujet || !message) {
                e.preventDefault();
                
                // Mettre en évidence les champs vides
                [document.getElementById('nom'), 
                 document.getElementById('email'), 
                 document.getElementById('motif'), 
                 document.getElementById('sujet'), 
                 document.getElementById('message')].forEach(field => {
                    if (field && !field.value.trim()) {
                        field.classList.add('error');
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fff5f5';
                    }
                });

                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }

            // Validation email simple
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                document.getElementById('email').classList.add('error');
                showToast('Veuillez entrer une adresse email valide.', 'error');
                return;
            }

            // Afficher le loader
            contactSubmitBtn.classList.add('btn-loading');
            contactSubmitBtn.disabled = true;
        });
    }

    // ============================================================
    // 3. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.contact-input, .contact-select, .contact-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.classList.remove('error');
            this.style.borderColor = '';
            this.style.background = '';
            const errorSpan = this.parentElement.parentElement.querySelector('.contact-error');
            if (errorSpan) {
                errorSpan.remove();
            }
        });
    });

    // ============================================================
    // 4. COMPTEUR DE CARACTÈRES POUR LE MESSAGE
    // ============================================================
    const messageTextarea = document.getElementById('message');
    
    if (messageTextarea) {
        const maxLength = 5000;
        
        // Créer le compteur
        const counter = document.createElement('div');
        counter.className = 'contact-char-counter';
        counter.style.cssText = 'text-align: right; font-size: 12px; color: var(--color-text-muted); margin-top: 6px;';
        counter.textContent = `0 / ${maxLength} caractères`;
        messageTextarea.parentElement.appendChild(counter);

        messageTextarea.addEventListener('input', function () {
            const length = this.value.length;
            counter.textContent = `${length} / ${maxLength} caractères`;

            if (length > maxLength * 0.9) {
                counter.style.color = 'var(--color-accent)';
                counter.style.fontWeight = '600';
            } else {
                counter.style.color = 'var(--color-text-muted)';
                counter.style.fontWeight = '400';
            }
        });
    }

    // ============================================================
    // 5. AUTO-FOCUS SUR LE CHAMP SUIVANT AVEC ENTRÉE
    // ============================================================
    const focusableFields = ['nom', 'email', 'telephone', 'organisation', 'sujet'];

    focusableFields.forEach((fieldId, index) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && this.value.trim() !== '') {
                    e.preventDefault();
                    const nextField = document.getElementById(focusableFields[index + 1]);
                    if (nextField) {
                        nextField.focus();
                    }
                }
            });
        }
    });

    // ============================================================
    // 6. ANIMATION DE LA SIDEBAR
    // ============================================================
    const sidebarCards = document.querySelectorAll('.contact-sidebar-card, .contact-slogan');

    if (sidebarCards.length > 0 && 'IntersectionObserver' in window) {
        const sidebarObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 120);

                    sidebarObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        sidebarCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            sidebarObserver.observe(card);
        });
    }

    // ============================================================
    // 7. EFFET DE SURVOL SUR LES RÉSEAUX SOCIAUX
    // ============================================================
    const socialLinks = document.querySelectorAll('.contact-social');

    socialLinks.forEach(link => {
        link.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-4px) scale(1.1)';
            this.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        });

        link.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.transition = 'all 0.3s ease';
        });
    });

    // ============================================================
    // 8. DISPARITION AUTO DE L'ALERTE SUCCÈS
    // ============================================================
    const successAlert = document.querySelector('.contact-alert-success');

    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-10px)';
            successAlert.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                if (successAlert.parentNode) {
                    successAlert.remove();
                }
            }, 500);
        }, 8000);
    }

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c📬 AHD-RDC %c— Page Contact chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});