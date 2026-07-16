/**
 * AHD-RDC — SCRIPTS FORMULAIRE PROJET (CREATE & EDIT)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. PRÉVISUALISATION DE L'IMAGE
    // ============================================================
    const imageInput = document.getElementById('image_principale');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewImg = document.getElementById('imagePreviewImg');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageInfo = document.getElementById('imageInfo');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                // Vérifier le type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showToast('Format non supporté. Utilisez JPG, PNG ou WebP.', 'error');
                    this.value = '';
                    return;
                }

                // Vérifier la taille (max 5 Mo)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('L\'image ne doit pas dépasser 5 Mo.', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (imagePreviewImg) {
                        imagePreviewImg.src = e.target.result;
                        imagePreviewImg.style.display = 'block';
                    }
                    if (imagePlaceholder) {
                        imagePlaceholder.style.display = 'none';
                    }
                    if (imageInfo) {
                        imageInfo.innerHTML = '<i class="fas fa-check-circle" style="color: var(--color-success);"></i> ' + file.name + ' (' + formatFileSize(file.size) + ')';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' o';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
        return (bytes / 1048576).toFixed(1) + ' Mo';
    }

    // ============================================================
    // 2. VALIDATION DU FORMULAIRE
    // ============================================================
    const projetForm = document.querySelector('form');

    if (projetForm) {
        projetForm.addEventListener('submit', function (e) {
            const titre = document.getElementById('titre')?.value?.trim();
            const domaine = document.getElementById('domaine')?.value;
            const zone = document.getElementById('zone_geographique')?.value?.trim();
            const dateDebut = document.getElementById('date_debut')?.value;
            const statut = document.getElementById('statut')?.value;
            const description = document.getElementById('description')?.value?.trim();

            if (!titre || !domaine || !zone || !dateDebut || !statut || !description) {
                e.preventDefault();

                ['titre', 'domaine', 'zone_geographique', 'date_debut', 'statut', 'description'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field && !field.value) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fffbfb';
                        field.style.transition = 'all 0.25s ease';
                    }
                });

                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }

            // Vérifier la date de fin
            const dateFin = document.getElementById('date_fin')?.value;
            if (dateFin && dateFin < dateDebut) {
                e.preventDefault();
                document.getElementById('date_fin').style.borderColor = 'var(--color-accent)';
                showToast('La date de fin doit être postérieure à la date de début.', 'error');
                return;
            }
        });
    }

    // ============================================================
    // 3. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.projet-form-input, .projet-form-select, .projet-form-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 4. GÉNÉRATION DU SLUG EN TEMPS RÉEL
    // ============================================================
    const titreInput = document.getElementById('titre');
    const slugPreview = document.getElementById('slugPreview');

    if (titreInput && slugPreview) {
        titreInput.addEventListener('input', function () {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
            slugPreview.textContent = slug || '...';
        });

        // Initialiser
        const initialSlug = titreInput.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        slugPreview.textContent = initialSlug || '...';
    }

    // ============================================================
    // 5. TOAST NOTIFICATION
    // ============================================================
    function showToast(message, type = 'success', duration = 5000) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${type === 'success' ? 'Succès' : 'Erreur'}</div>
                <div class="toast-text">${message}</div>
            </div>
            <button class="toast-close" title="Fermer"><i class="fas fa-times"></i></button>
            <div class="toast-progress"></div>
        `;

        container.appendChild(toast);
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        });

        setTimeout(() => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // ============================================================
    // 6. ANIMATION DU FORMULAIRE
    // ============================================================
    const formCard = document.querySelector('.projet-form-card');

    if (formCard && 'IntersectionObserver' in window) {
        const formObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    formObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        formCard.style.opacity = '0';
        formCard.style.transform = 'translateY(20px)';
        formCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        formObserver.observe(formCard);
    }

    // ============================================================
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c📁 AHD-RDC %c— Formulaire Projet chargé',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});