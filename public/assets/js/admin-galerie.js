/* ============================================================
   GALERIE MÉDIA — Admin (réutilisable)
   ------------------------------------------------------------
   Fonctionne avec :
     - #galerie (input file multi)
     - #galeriePreview (conteneur d'aperçu)
     - .projet-galerie-label (zone d'upload)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    const galerieInput   = document.getElementById('galerie');
    const galeriePreview = document.getElementById('galeriePreview');

    if (!galerieInput || !galeriePreview) {
        return; // Pas de galerie sur cette page
    }

    /* ========================================================
       1. APERÇU À LA SÉLECTION
       ======================================================== */

    galerieInput.addEventListener('change', function () {
        galeriePreview.innerHTML = '';

        const files = Array.from(this.files);
        if (files.length === 0) return;

        files.forEach(function (file) {
            const item = creerApercuFichier(file);
            if (item) {
                galeriePreview.appendChild(item);
            }
        });
    });

    /* ========================================================
       2. CRÉATION D'UN APERÇU
       ======================================================== */

    function creerApercuFichier(file) {
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        if (!isImage && !isVideo) return null;

        const item = document.createElement('div');
        item.className = 'galerie-preview-item';

        // Aperçu
        if (isImage) {
            const img = document.createElement('img');
            img.alt = file.name;

            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            item.appendChild(img);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'galerie-preview-video';
            placeholder.innerHTML = '<i class="fas fa-video"></i>';
            item.appendChild(placeholder);
        }

        // Badge type
        const badge = document.createElement('span');
        badge.className = 'galerie-preview-badge';
        badge.innerHTML = isVideo
            ? '<i class="fas fa-video"></i>'
            : '<i class="fas fa-image"></i>';
        item.appendChild(badge);

        // Taille
        const taille = document.createElement('span');
        taille.className = 'galerie-preview-size';
        taille.textContent = Math.round(file.size / 1024) + ' Ko';
        item.appendChild(taille);

        return item;
    }

    /* ========================================================
       3. DRAG & DROP
       ======================================================== */

    const galerieLabel = document.querySelector('.projet-galerie-label');

    if (galerieLabel) {
        ['dragenter', 'dragover'].forEach(function (event) {
            galerieLabel.addEventListener(event, function (e) {
                e.preventDefault();
                e.stopPropagation();
                galerieLabel.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (event) {
            galerieLabel.addEventListener(event, function (e) {
                e.preventDefault();
                e.stopPropagation();
                galerieLabel.classList.remove('is-dragover');
            });
        });

        galerieLabel.addEventListener('drop', function (e) {
            e.preventDefault();

            const files = e.dataTransfer.files;
            if (files.length === 0) return;

            const dataTransfer = new DataTransfer();
            Array.from(files).forEach(function (file) {
                dataTransfer.items.add(file);
            });

            galerieInput.files = dataTransfer.files;
            galerieInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    /* ========================================================
       4. VALIDATION À LA SOUMISSION
       ======================================================== */

    const form = galerieInput.closest('form');

    if (form) {
        form.addEventListener('submit', function (e) {
            if (galerieInput.files.length > 20) {
                e.preventDefault();
                alert('Vous ne pouvez pas uploader plus de 20 fichiers à la fois.');
                return false;
            }

            const tropGros = Array.from(galerieInput.files).filter(function (file) {
                return file.size > 50 * 1024 * 1024;
            });

            if (tropGros.length > 0) {
                e.preventDefault();
                alert(
                    'Fichier(s) trop volumineux (max 50 Mo) :\n' +
                    tropGros.map(f => '• ' + f.name).join('\n')
                );
                return false;
            }
        });
    }
});