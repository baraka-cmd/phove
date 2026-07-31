/**
 * AHD-RDC — SCRIPTS GESTION ARTICLES (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.articles-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DELAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('articleSearch');
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
    const deleteButtons = document.querySelectorAll('.btn-delete-article');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const titre = this.getAttribute('data-titre') || 'cet article';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement "' + titre + '" ?\n\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. PRÉVISUALISATION DE L'IMAGE
    // ============================================================
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewImg = document.getElementById('imagePreviewImg');
    const imagePlaceholder = document.getElementById('imagePlaceholder');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('L\'image ne doit pas dépasser 5 Mo.');
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
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================================
    // 5. GÉNÉRATION DU SLUG EN TEMPS RÉEL
    // ============================================================
    const titreInput = document.getElementById('titre');
    const slugPreview = document.getElementById('slugPreview');

    if (titreInput && slugPreview) {
        titreInput.addEventListener('input', function () {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
            slugPreview.textContent = slug || '...';
        });

        // Initialiser
        const initialSlug = titreInput.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        if (slugPreview && initialSlug) {
            slugPreview.textContent = initialSlug;
        }
    }

    // ============================================================
    // 6. VALIDATION DU FORMULAIRE
    // ============================================================
    const articleForm = document.querySelector('form');

    if (articleForm && articleForm.querySelector('textarea[name="contenu"]')) {
        articleForm.addEventListener('submit', function (e) {
            const titre = document.getElementById('titre')?.value?.trim();
            const contenu = document.querySelector('textarea[name="contenu"]')?.value?.trim();
            const categorie = document.getElementById('categorie')?.value;

            if (!titre || !contenu || !categorie) {
                e.preventDefault();

                ['titre', 'categorie'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field && !field.value) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fffbfb';
                    }
                });

                if (!contenu) {
                    const textarea = document.querySelector('textarea[name="contenu"]');
                    if (textarea) {
                        textarea.style.borderColor = 'var(--color-accent)';
                        textarea.style.background = '#fffbfb';
                    }
                }

                showToast('Veuillez remplir tous les champs obligatoires (titre, contenu, catégorie).', 'error');
                return;
            }
        });
    }

    // ============================================================
    // 7. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.article-form-input, .article-form-select, .article-form-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 8. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.article-stat-card');

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
    const kpiValues = document.querySelectorAll('.article-stat-value');

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
    // 10. TOAST NOTIFICATION
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
    // 11. INITIALISATION
    // ============================================================
    console.log(
        '%c📰 AHD-RDC %c— Gestion Articles chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});