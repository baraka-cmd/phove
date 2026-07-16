/**
 * AHD-RDC — SCRIPTS GOUVERNANCE — DÉTAIL ORGANE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. CONFIRMATION DE RETRAIT D'UN MEMBRE
    // ============================================================
    const retirerButtons = document.querySelectorAll('.btn-retirer-membre');

    retirerButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce membre';
            const poste = this.getAttribute('data-poste') || '';
            const message = 'Êtes-vous sûr de vouloir retirer ' + nom + ' du poste de ' + poste + ' ?\n\nSon mandat sera marqué comme terminé.';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 2. GESTION DU FORMULAIRE D'ÉDITION
    // ============================================================
    const editFormCard = document.getElementById('editFormCard');
    const editForm = document.getElementById('editForm');

    if (editFormCard && editForm) {
        document.querySelectorAll('.btn-edit-poste').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const id = this.getAttribute('data-id');
                const poste = this.getAttribute('data-poste');
                const dateDebut = this.getAttribute('data-date-debut');
                const dateFin = this.getAttribute('data-date-fin');
                const actif = this.getAttribute('data-actif');
                const ordre = this.getAttribute('data-ordre');

                editForm.action = '/admin/gouvernance/organes/' + id + '/update';
                document.getElementById('edit_poste').value = poste;
                document.getElementById('edit_date_debut').value = dateDebut;
                document.getElementById('edit_date_fin').value = dateFin;
                document.getElementById('edit_actif').checked = actif === '1';
                document.getElementById('edit_ordre').value = ordre;

                editFormCard.style.display = 'block';
                editFormCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Animation d'attention
                editFormCard.style.boxShadow = '0 0 0 4px rgba(22, 85, 143, 0.2)';
                setTimeout(() => {
                    editFormCard.style.boxShadow = '';
                    editFormCard.style.transition = 'box-shadow 0.6s ease';
                }, 1500);
            });
        });
    }

    // ============================================================
    // 3. ANIMATION DES LIGNES DU TABLEAU
    // ============================================================
    const tableRows = document.querySelectorAll('.table-gouvernance tbody tr');

    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background 0.2s ease';
        });
    });

    // ============================================================
    // 4. ANIMATION DU FORMULAIRE D'AJOUT AU SCROLL
    // ============================================================
    const formCard = document.querySelector('.gouvernance-form-card');

    if (formCard && 'IntersectionObserver' in window) {
        const formObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    formObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        formCard.style.opacity = '0';
        formCard.style.transform = 'translateY(20px)';
        formCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        formObserver.observe(formCard);
    }

    // ============================================================
    // 5. INITIALISATION
    // ============================================================
    console.log(
        '%c🏛️ AHD-RDC %c— Détail Organe chargé',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});