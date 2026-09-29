/* ============================================================
   GOUVERNANCE — PAGE ORGANE
   ------------------------------------------------------------
   - Formulaire d'édition de poste (masqué par défaut)
   - Prévisualisation de la photo avant upload
   - Suppression de la photo sélectionnée
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ========================================================
       1. FORMULAIRE D'ÉDITION DU POSTE
       ======================================================== */

    const editFormCard = document.getElementById('editFormCard');
    const editForm     = document.getElementById('editForm');
    const editPoste    = document.getElementById('edit_poste');
    const editDateDeb  = document.getElementById('edit_date_debut');
    const editDateFin  = document.getElementById('edit_date_fin');
    const editActif    = document.getElementById('edit_actif');
    const editOrdre    = document.getElementById('edit_ordre');

    if (editFormCard && editForm) {

        document.querySelectorAll('.btn-edit-poste').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const id         = this.dataset.id;
                const poste      = this.dataset.poste;
                const dateDebut  = this.dataset.dateDebut;
                const dateFin    = this.dataset.dateFin;
                const actif      = this.dataset.actif;
                const ordre      = this.dataset.ordre;

                // URL dynamique : /admin/gouvernance/organes/{id}/update
                const baseUrl = editForm.dataset.actionBase;
                editForm.action = baseUrl + '/' + id + '/update';

                // Remplissage des champs
                if (editPoste)    editPoste.value    = poste;
                if (editDateDeb)  editDateDeb.value  = dateDebut;
                if (editDateFin)  editDateFin.value  = dateFin || '';
                if (editActif)    editActif.checked  = actif === '1';
                if (editOrdre)    editOrdre.value    = ordre;

                // Affichage + scroll
                editFormCard.style.display = 'block';
                editFormCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    /* ========================================================
       2. PRÉVISUALISATION DE LA PHOTO
       ======================================================== */

    const photoInput      = document.getElementById('photo');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholder= document.getElementById('photoPlaceholder');
    const photoRemoveBtn  = document.getElementById('photoRemoveBtn');

    if (photoInput && photoPreviewImg && photoPlaceholder) {

        photoInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) {
                resetPhotoPreview();
                return;
            }

            // Validation côté client
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            if (!allowedTypes.includes(file.type)) {
                alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                resetPhotoPreview();
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('La photo ne doit pas dépasser 2 Mo.');
                resetPhotoPreview();
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                photoPreviewImg.src = e.target.result;
                photoPreviewImg.style.display = 'block';
                photoPlaceholder.style.display = 'none';

                if (photoRemoveBtn) {
                    photoRemoveBtn.style.display = 'flex';
                }
            };

            reader.readAsDataURL(file);
        });

        // Suppression de la photo sélectionnée
        if (photoRemoveBtn) {
            photoRemoveBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetPhotoPreview();
            });
        }

        function resetPhotoPreview() {
            photoInput.value = '';
            photoPreviewImg.src = '';
            photoPreviewImg.style.display = 'none';
            photoPlaceholder.style.display = 'flex';

            if (photoRemoveBtn) {
                photoRemoveBtn.style.display = 'none';
            }
        }
    }
});