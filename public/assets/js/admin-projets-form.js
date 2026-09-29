/* ============================================================
   FORMULAIRE PROJET — Admin PHOVE
   ------------------------------------------------------------
   Fonctionnalités :
     - Aperçu de l'image principale (1 fichier)
     - Aperçu de la galerie média (multi-fichiers + drag & drop)
     - Slug automatique en direct
     - Validation côté client (formats + tailles)
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ========================================================
       1. APERÇU DE L'IMAGE PRINCIPALE (1 fichier)
       ======================================================== */

    const imageInput       = document.getElementById('image_principale');
    const imagePreviewImg  = document.getElementById('imagePreviewImg');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageInfo        = document.getElementById('imageInfo');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) {
                resetImagePreview();
                return;
            }

            // ─── Validation ───
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            if (!allowedTypes.includes(file.type)) {
                alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                resetImagePreview();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 5 Mo.');
                resetImagePreview();
                return;
            }

            // ─── Aperçu ───
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

            // ─── Info fichier ───
            if (imageInfo) {
                imageInfo.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' Ko)';
            }
        });
    }

    /**
     * Réinitialise l'aperçu de l'image principale.
     */
    function resetImagePreview() {
        if (imageInput) imageInput.value = '';
        if (imagePreviewImg) {
            imagePreviewImg.src = '';
            imagePreviewImg.style.display = 'none';
        }
        if (imagePlaceholder) imagePlaceholder.style.display = 'block';
        if (imageInfo) imageInfo.textContent = 'Aucun fichier sélectionné';
    }

    /* ========================================================
       2. APERÇU DE LA GALERIE MÉDIA (multi-fichiers)
       ======================================================== */

    const galerieInput   = document.getElementById('galerie');
    const galeriePreview = document.getElementById('galeriePreview');

    if (galerieInput && galeriePreview) {

        galerieInput.addEventListener('change', function () {
            galeriePreview.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length === 0) {
                return;
            }

            files.forEach(function (file) {
                const item = creerApercuFichier(file);
                if (item) {
                    galeriePreview.appendChild(item);
                }
            });
        });
    }

    /**
     * Crée un élément d'aperçu pour un fichier.
     *
     * @param  {File} file
     * @return {HTMLElement|null}
     */
    function creerApercuFichier(file) {
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        // Accepter uniquement images et vidéos
        if (!isImage && !isVideo) {
            return null;
        }

        const item = document.createElement('div');
        item.className = 'galerie-preview-item';

        // ─── Aperçu image ───
        if (isImage) {
            const img = document.createElement('img');
            img.alt = file.name;

            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            item.appendChild(img);
        }
        // ─── Placeholder vidéo ───
        else {
            const videoPlaceholder = document.createElement('div');
            videoPlaceholder.className = 'galerie-preview-video';
            videoPlaceholder.innerHTML = '<i class="fas fa-video"></i>';
            item.appendChild(videoPlaceholder);
        }

        // ─── Badge type ───
        const badge = document.createElement('span');
        badge.className = 'galerie-preview-badge';
        badge.innerHTML = isVideo
            ? '<i class="fas fa-video"></i>'
            : '<i class="fas fa-image"></i>';
        item.appendChild(badge);

        // ─── Info taille ───
        const taille = document.createElement('span');
        taille.className = 'galerie-preview-size';
        taille.textContent = Math.round(file.size / 1024) + ' Ko';
        item.appendChild(taille);

        return item;
    }

    /* ========================================================
       3. DRAG & DROP SUR LA ZONE GALERIE
       ======================================================== */

    const galerieLabel = document.querySelector('.projet-galerie-label');

    if (galerieLabel && galerieInput) {

        // Surbrillance pendant le drag
        ['dragenter', 'dragover'].forEach(function (event) {
            galerieLabel.addEventListener(event, function (e) {
                e.preventDefault();
                e.stopPropagation();
                galerieLabel.classList.add('is-dragover');
            });
        });

        // Retirer la surbrillance
        ['dragleave', 'drop'].forEach(function (event) {
            galerieLabel.addEventListener(event, function (e) {
                e.preventDefault();
                e.stopPropagation();
                galerieLabel.classList.remove('is-dragover');
            });
        });

        // Traitement du drop
        galerieLabel.addEventListener('drop', function (e) {
            e.preventDefault();

            const files = e.dataTransfer.files;

            if (files.length > 0) {
                // Créer un FileList compatible avec l'input
                const dataTransfer = new DataTransfer();

                Array.from(files).forEach(function (file) {
                    dataTransfer.items.add(file);
                });

                galerieInput.files = dataTransfer.files;

                // Déclencher l'événement change pour l'aperçu
                galerieInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    /* ========================================================
       4. SLUG AUTOMATIQUE (page create)
       ======================================================== */

    const titreInput  = document.getElementById('titre');
    const slugPreview = document.getElementById('slugPreview');

    if (titreInput && slugPreview) {
        titreInput.addEventListener('input', function () {
            const slug = genererSlug(this.value);
            slugPreview.textContent = slug || '...';
        });
    }

    /**
     * Génère un slug à partir d'une chaîne.
     *
     * @param  {string} texte
     * @return {string}
     */
    function genererSlug(texte) {
        return texte
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')  // Supprimer les accents
            .replace(/[^a-z0-9\s-]/g, '')     // Garder lettres, chiffres, espaces, tirets
            .trim()
            .replace(/\s+/g, '-')             // Remplacer les espaces par des tirets
            .replace(/-+/g, '-');             // Éviter les tirets multiples
    }

    /* ========================================================
       5. VALIDATION FORMULAIRE AVANT SOUMISSION
       ======================================================== */

    const projetForm = document.querySelector('form[enctype="multipart/form-data"]');

    if (projetForm) {
        projetForm.addEventListener('submit', function (e) {
            // Vérifier que la galerie ne contient pas plus de 20 fichiers
            if (galerieInput && galerieInput.files.length > 20) {
                e.preventDefault();
                alert('Vous ne pouvez pas uploader plus de 20 fichiers à la fois.');
                return false;
            }

            // Vérifier la taille de chaque fichier de la galerie
            if (galerieInput) {
                const fichiers = Array.from(galerieInput.files);
                const fichiersTropGros = fichiers.filter(function (file) {
                    return file.size > 50 * 1024 * 1024; // 50 Mo
                });

                if (fichiersTropGros.length > 0) {
                    e.preventDefault();
                    alert(
                        'Fichier(s) trop volumineux (max 50 Mo) :\n' +
                        fichiersTropGros.map(f => '• ' + f.name).join('\n')
                    );
                    return false;
                }
            }
        });
    }

});