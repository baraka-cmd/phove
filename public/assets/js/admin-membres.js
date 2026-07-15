/**
 * AHD-RDC — SCRIPTS GESTION MEMBRES (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.membres-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DELAI (DEBOUNCE)
    // ============================================================
    const searchInput = document.getElementById('membreSearch');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }, 500);
        });
    }

    // ============================================================
    // 3. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-membre');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce membre';
            const message = 'Êtes-vous sûr de vouloir supprimer définitivement ' + nom + ' ? Cette action est irréversible et supprimera également toutes ses cotisations et son historique.';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. MODALE DE CHANGEMENT DE STATUT RAPIDE
    // ============================================================
    const statusButtons = document.querySelectorAll('.btn-change-status');

    statusButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const membreId = this.getAttribute('data-id');
            const membreNom = this.getAttribute('data-nom');
            const action = this.getAttribute('data-action');
            const form = document.getElementById('statusForm');

            if (form) {
                form.action = '/admin/membres/' + membreId + '/change-status';
                document.getElementById('statusAction').value = action;
                document.getElementById('statusMembreNom').textContent = membreNom;
                document.getElementById('statusModal').classList.add('active');
            }
        });
    });

    // Fermer la modale
    const modalClose = document.querySelector('.modal-close');
    const modalOverlay = document.querySelector('.modal-overlay');

    if (modalClose) {
        modalClose.addEventListener('click', function () {
            document.getElementById('statusModal').classList.remove('active');
        });
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === modalOverlay) {
                modalOverlay.classList.remove('active');
            }
        });
    }

    // ============================================================
    // 5. PRÉVISUALISATION PHOTO (CREATE / EDIT)
    // ============================================================
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
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
    // 6. ANIMATION DES STATISTIQUES
    // ============================================================
    const statCards = document.querySelectorAll('.membre-stat-card');

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
    // 7. SURVOL DES LIGNES DU TABLEAU
    // ============================================================
    const tableRows = document.querySelectorAll('.table-membres tbody tr');

    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background 0.2s ease';
        });
    });

    // ============================================================
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c👥 AHD-RDC %c— Gestion Membres chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});