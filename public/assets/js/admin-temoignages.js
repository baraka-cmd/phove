/**
 * AHD-RDC — SCRIPTS GESTION TÉMOIGNAGES (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.temoignages-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DEBOUNCE
    // ============================================================
    const searchInput = document.getElementById('temoignageSearch');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = this.closest('form');
                if (form) form.submit();
            }, 500);
        });
    }

    // ============================================================
    // 3. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-temoignage');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce témoignage';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement le témoignage de "' + nom + '" ?\n\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. MODALE DE REJET
    // ============================================================
    const rejectModal = document.getElementById('rejectModal');
    const rejectModalClose = document.getElementById('rejectModalClose');
    const rejectModalCancel = document.getElementById('rejectModalCancel');
    const rejectModalSubmit = document.getElementById('rejectModalSubmit');
    const rejectModalTextarea = document.getElementById('rejectModalTextarea');
    const rejectForm = document.getElementById('rejectForm');
    const rejectMotifInput = document.getElementById('rejectMotifInput');
    const rejectButtons = document.querySelectorAll('.btn-reject-temoignage');

    let currentRejectUrl = '';

    if (rejectButtons.length > 0 && rejectModal) {
        rejectButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                currentRejectUrl = this.getAttribute('data-url') || this.closest('form')?.getAttribute('action') || '';
                
                if (rejectModalTextarea) {
                    rejectModalTextarea.value = '';
                    rejectModalTextarea.style.borderColor = '#e0e4e8';
                }
                
                rejectModal.classList.add('active');
                setTimeout(() => {
                    if (rejectModalTextarea) rejectModalTextarea.focus();
                }, 300);
            });
        });
    }

    function closeRejectModal() {
        if (rejectModal) rejectModal.classList.remove('active');
    }

    if (rejectModalClose) rejectModalClose.addEventListener('click', closeRejectModal);
    if (rejectModalCancel) rejectModalCancel.addEventListener('click', closeRejectModal);

    if (rejectModal) {
        rejectModal.addEventListener('click', function (e) {
            if (e.target === rejectModal) closeRejectModal();
        });
    }

    if (rejectModalSubmit && rejectModalTextarea && rejectForm && rejectMotifInput) {
        rejectModalSubmit.addEventListener('click', function () {
            const motif = rejectModalTextarea.value.trim();
            
            if (!motif) {
                rejectModalTextarea.style.borderColor = 'var(--color-accent)';
                rejectModalTextarea.focus();
                return;
            }

            rejectMotifInput.value = motif;
            rejectForm.action = currentRejectUrl;
            rejectForm.submit();
        });
    }

    // ============================================================
    // 5. PRÉVISUALISATION DE LA PHOTO
    // ============================================================
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showToast('Format non supporté. Utilisez JPG, PNG ou WebP.', 'error');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showToast('La photo ne doit pas dépasser 5 Mo.', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (photoPreviewImg) {
                        photoPreviewImg.src = e.target.result;
                        photoPreviewImg.style.display = 'block';
                    }
                    if (photoPlaceholder) {
                        photoPlaceholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================================
    // 6. VALIDATION DU FORMULAIRE
    // ============================================================
    const temoignageForm = document.querySelector('form');

    if (temoignageForm && temoignageForm.querySelector('textarea[name="contenu"]')) {
        temoignageForm.addEventListener('submit', function (e) {
            const nom = document.getElementById('nom')?.value?.trim();
            const sexe = document.getElementById('sexe')?.value;
            const categorie = document.getElementById('categorie')?.value;
            const contenu = document.querySelector('textarea[name="contenu"]')?.value?.trim();
            const statut = document.getElementById('statut')?.value;

            let errors = [];

            if (!nom) errors.push({ field: 'nom', message: 'Le nom est obligatoire.' });
            if (!sexe) errors.push({ field: 'sexe', message: 'Le sexe est obligatoire.' });
            if (!categorie) errors.push({ field: 'categorie', message: 'La catégorie est obligatoire.' });
            if (!contenu) errors.push({ field: 'contenu', message: 'Le contenu du témoignage est obligatoire.' });
            if (!statut) errors.push({ field: 'statut', message: 'Le statut est obligatoire.' });

            if (errors.length > 0) {
                e.preventDefault();

                errors.forEach(err => {
                    const field = document.getElementById(err.field);
                    if (field) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fffbfb';
                    }
                });

                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
            }
        });
    }

    // ============================================================
    // 7. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.temoignage-form-input, .temoignage-form-select, .temoignage-form-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 8. ANIMATION DES KPI
    // ============================================================
    const statCards = document.querySelectorAll('.temoignage-stat-card');

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
        }, { threshold: 0.2, rootMargin: '0px 0px -20px 0px' });

        statCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(15px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            statObserver.observe(card);
        });
    }

    // ============================================================
    // 9. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.temoignage-stat-value');

    if (kpiValues.length > 0 && 'IntersectionObserver' in window) {
        const kpiObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const text = el.textContent.replace(/[^0-9]/g, '');
                    const target = parseInt(text) || 0;
                    if (target > 0) {
                        animateNumber(el, target);
                    }
                    kpiObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        kpiValues.forEach(el => kpiObserver.observe(el));
    }

    function animateNumber(element, target) {
        const duration = 1000;
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
    // 10. CONFIRMATION APPROBATION RAPIDE
    // ============================================================
    const approveButtons = document.querySelectorAll('.btn-approve-temoignage');

    approveButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce bénéficiaire';
            if (!confirm('Approuver le témoignage de "' + nom + '" ?\n\nIl sera visible publiquement sur le site.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 11. TOAST NOTIFICATION
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
            <button class="toast-close" title="Fermer">
                <i class="fas fa-times"></i>
            </button>
            <div class="toast-progress"></div>
        `;

        container.appendChild(toast);

        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        });

        setTimeout(() => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // ============================================================
    // 12. INITIALISATION
    // ============================================================
    console.log(
        '%c💬 AHD-RDC %c— Gestion Témoignages chargée',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cValidation • Modération • KPI Animés',
        'color: #28a745; font-size: 11px;'
    );
});