/**
 * AHD-RDC — SCRIPTS GESTION PARTENAIRES (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.partenaires-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DELAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('partenaireSearch');
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
    const deleteButtons = document.querySelectorAll('.btn-delete-partenaire');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce partenaire';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement "' + nom + '" ?\n\nCette action est irréversible et supprimera également toutes les associations aux projets.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. PRÉVISUALISATION DU LOGO
    // ============================================================
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const logoPlaceholder = document.getElementById('logoPlaceholder');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                    this.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Le logo ne doit pas dépasser 2 Mo.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (logoPreviewImg) {
                        logoPreviewImg.src = e.target.result;
                        logoPreviewImg.style.display = 'block';
                    }
                    if (logoPlaceholder) {
                        logoPlaceholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================================
    // 5. ANIMATION DES CARTES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.partenaire-stat-card');

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
    // 6. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.partenaire-stat-value');

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
    // 7. INITIALISATION
    // ============================================================
    console.log(
        '%c🤝 AHD-RDC %c— Gestion Partenaires chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});