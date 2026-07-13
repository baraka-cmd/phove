/**
 * AHD-RDC — SCRIPTS PAGE DEVENIR MEMBRE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. UPLOAD ET PRÉVISUALISATION DE LA PHOTO
    // ============================================================
    const photoInput = document.getElementById('photo');
    const photoLabel = document.getElementById('photoLabel');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoRemove = document.getElementById('photoRemove');

    if (photoInput && photoLabel && photoPreview && photoPreviewImg && photoRemove) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                // Vérifier le type de fichier
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Veuillez sélectionner une image au format JPG, JPEG ou PNG.');
                    this.value = '';
                    return;
                }

                // Vérifier la taille (max 2 Mo)
                if (file.size > 2 * 1024 * 1024) {
                    alert('La photo ne doit pas dépasser 2 Mo.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    photoPreviewImg.src = e.target.result;
                    photoLabel.style.display = 'none';
                    photoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Supprimer la photo
        photoRemove.addEventListener('click', function () {
            photoInput.value = '';
            photoPreviewImg.src = '';
            photoLabel.style.display = 'flex';
            photoPreview.style.display = 'none';
        });
    }

    // ============================================================
    // 2. MISE À JOUR DU RÉCAPITULATIF EN TEMPS RÉEL
    // ============================================================
    const recapIdentite = document.getElementById('recapIdentite');
    const recapCategorie = document.getElementById('recapCategorie');
    const recapTelephone = document.getElementById('recapTelephone');

    const nomInput = document.getElementById('nom');
    const postNomInput = document.getElementById('post_nom');
    const prenomInput = document.getElementById('prenom');
    const categorieSelect = document.getElementById('categorie');
    const telephoneInput = document.getElementById('telephone');

    function updateRecap() {
        // Identité
        if (recapIdentite && nomInput && prenomInput) {
            const nom = nomInput.value.trim();
            const postNom = postNomInput ? postNomInput.value.trim() : '';
            const prenom = prenomInput.value.trim();
            const identite = [nom, postNom, prenom].filter(Boolean).join(' ');
            recapIdentite.textContent = identite || '—';
        }

        // Catégorie
        if (recapCategorie && categorieSelect) {
            const selectedOption = categorieSelect.options[categorieSelect.selectedIndex];
            recapCategorie.textContent = selectedOption && selectedOption.value ? selectedOption.text : '—';
        }

        // Téléphone
        if (recapTelephone && telephoneInput) {
            recapTelephone.textContent = telephoneInput.value.trim() || '—';
        }
    }

    // Écouter les changements
    [nomInput, postNomInput, prenomInput, categorieSelect, telephoneInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateRecap);
            el.addEventListener('change', updateRecap);
        }
    });

    // Initialiser le récapitulatif
    updateRecap();

    // ============================================================
    // 3. VALIDATION DU FORMULAIRE
    // ============================================================
    const adhesionForm = document.getElementById('adhesionForm');
    const adhesionSubmitBtn = document.getElementById('adhesionSubmitBtn');

    if (adhesionForm && adhesionSubmitBtn) {
        adhesionForm.addEventListener('submit', function (e) {
            // Vérifier les engagements
            const checkboxes = adhesionForm.querySelectorAll('.adhesion-checkbox[required]');
            let allChecked = true;

            checkboxes.forEach(cb => {
                if (!cb.checked) {
                    allChecked = false;
                    cb.parentElement.style.borderColor = 'var(--color-accent)';
                    cb.parentElement.style.background = '#fff5f5';
                }
            });

            if (!allChecked) {
                e.preventDefault();
                alert('Veuillez accepter tous les engagements requis avant de soumettre votre demande.');
                
                // Scroll vers les engagements
                const engagementsSection = document.querySelector('.adhesion-engagements');
                if (engagementsSection) {
                    engagementsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Afficher le loader
            adhesionSubmitBtn.classList.add('btn-loading');
            adhesionSubmitBtn.disabled = true;
        });
    }

    // Réinitialiser le style des checkboxes au clic
    document.querySelectorAll('.adhesion-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                this.parentElement.style.borderColor = '';
                this.parentElement.style.background = '';
            }
        });
    });

    // ============================================================
    // 4. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.adhesion-input, .adhesion-select, .adhesion-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.classList.remove('error');
            const errorSpan = this.parentElement.querySelector('.adhesion-error');
            if (errorSpan) {
                errorSpan.remove();
            }
        });
    });

    // ============================================================
    // 5. ANIMATION DES ÉTAPES AU SCROLL
    // ============================================================
    const adhesionSteps = document.querySelectorAll('.adhesion-step');

    if (adhesionSteps.length > 0 && 'IntersectionObserver' in window) {
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

        adhesionSteps.forEach(step => {
            stepObserver.observe(step);
        });
    }

    // ============================================================
    // 6. ANIMATION DES CARTES AVANTAGES
    // ============================================================
    const avantageCards = document.querySelectorAll('.adhesion-avantage-card');

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
    // 7. ANIMATION DES CARTES CATÉGORIES
    // ============================================================
    const categorieCards = document.querySelectorAll('.adhesion-categorie-card');

    if (categorieCards.length > 0 && 'IntersectionObserver' in window) {
        const categorieObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const card = entry.target;

                    setTimeout(() => {
                        card.classList.add('visible');
                    }, index * 120);

                    categorieObserver.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -20px 0px'
        });

        categorieCards.forEach(card => {
            categorieObserver.observe(card);
        });
    }

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c👥 AHD-RDC %c— Page Devenir membre chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});