/**
 * AHD-RDC — SCRIPTS GALERIE MÉDIAS (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. CONSTANTES ET RÉFÉRENCES GLOBALES
    // ============================================================
    const mediasGrid = document.getElementById('mediasGrid');
    const mediasBatchBar = document.getElementById('mediasBatchBar');
    const mediasBatchCount = document.getElementById('mediasBatchCount');
    const mediasBatchCountSpan = document.getElementById('mediasBatchCountSpan');
    const selectAllCheckbox = document.getElementById('selectAllMedias');
    const viewButtons = document.querySelectorAll('.medias-view-btn');
    const mediaCards = document.querySelectorAll('.media-card');
    const mediaCheckboxes = document.querySelectorAll('.media-card-checkbox-input');
    const filtreSelects = document.querySelectorAll('.medias-filtre-select');
    const searchInput = document.getElementById('mediaSearch');
    const batchDeleteBtn = document.getElementById('batchDeleteBtn');
    const batchAlbumBtn = document.getElementById('batchAlbumBtn');
    const batchPublierBtn = document.getElementById('batchPublierBtn');
    const batchDepublierBtn = document.getElementById('batchDepublierBtn');
    const batchCancelBtn = document.getElementById('batchCancelBtn');
    const albumModal = document.getElementById('albumModal');
    const albumModalClose = document.getElementById('albumModalClose');
    const albumModalCancel = document.getElementById('albumModalCancel');
    const albumModalSubmit = document.getElementById('albumModalSubmit');
    const albumModalInput = document.getElementById('albumModalInput');
    const batchAlbumForm = document.getElementById('batchAlbumForm');
    const batchAlbumInput = document.getElementById('batchAlbumInput');
    const batchPublierForm = document.getElementById('batchPublierForm');
    const batchPublierInput = document.getElementById('batchPublierInput');

    let selectedMedias = new Set();

    // ============================================================
    // 2. SWITCH VUE GRILLE / LISTE
    // ============================================================
    if (viewButtons.length > 0 && mediasGrid) {
        // Restaurer la préférence
        const savedView = localStorage.getItem('medias-view') || 'grid';
        setView(savedView);

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const view = this.getAttribute('data-view');
                setView(view);
                localStorage.setItem('medias-view', view);
            });
        });
    }

    function setView(view) {
        if (!mediasGrid) return;

        // Mettre à jour les boutons
        viewButtons.forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-view') === view);
        });

        // Mettre à jour la grille
        if (view === 'list') {
            mediasGrid.classList.add('list-view');
            document.querySelectorAll('.media-card').forEach(card => {
                card.classList.add('list-item');
            });
        } else {
            mediasGrid.classList.remove('list-view');
            document.querySelectorAll('.media-card').forEach(card => {
                card.classList.remove('list-item');
            });
        }
    }

    // ============================================================
    // 3. FILTRES AUTO
    // ============================================================
    if (filtreSelects.length > 0) {
        filtreSelects.forEach(select => {
            select.addEventListener('change', function () {
                const form = this.closest('form');
                if (form) form.submit();
            });
        });
    }

    // ============================================================
    // 4. RECHERCHE AVEC DEBOUNCE
    // ============================================================
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = this.closest('form');
                if (form) form.submit();
            }, 500);
        });
    }

    // ============================================================
    // 5. SÉLECTION DE MÉDIAS (BATCH)
    // ============================================================

    // Checkbox individuelle
    if (mediaCheckboxes.length > 0) {
        mediaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const mediaId = this.getAttribute('data-media-id');
                const card = this.closest('.media-card');

                if (this.checked) {
                    selectedMedias.add(mediaId);
                    if (card) card.classList.add('selected');
                } else {
                    selectedMedias.delete(mediaId);
                    if (card) card.classList.remove('selected');
                }

                updateBatchBar();
                updateSelectAllCheckbox();
            });
        });
    }

    // Clic sur la carte entière (hors actions)
    if (mediaCards.length > 0) {
        mediaCards.forEach(card => {
            card.addEventListener('click', function (e) {
                // Ne pas sélectionner si on clique sur un bouton, un lien, ou la checkbox
                if (e.target.closest('button') || e.target.closest('a') || e.target.closest('.media-card-checkbox') || e.target.closest('.media-card-overlay-btn')) {
                    return;
                }

                const checkbox = this.querySelector('.media-card-checkbox-input');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });
    }

    // Tout sélectionner / désélectionner
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;

            mediaCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                const mediaId = checkbox.getAttribute('data-media-id');
                const card = checkbox.closest('.media-card');

                if (isChecked) {
                    selectedMedias.add(mediaId);
                    if (card) card.classList.add('selected');
                } else {
                    selectedMedias.delete(mediaId);
                    if (card) card.classList.remove('selected');
                }
            });

            updateBatchBar();
        });
    }

    function updateSelectAllCheckbox() {
        if (!selectAllCheckbox) return;
        selectAllCheckbox.checked = selectedMedias.size === mediaCheckboxes.length;
        selectAllCheckbox.indeterminate = selectedMedias.size > 0 && selectedMedias.size < mediaCheckboxes.length;
    }

    function updateBatchBar() {
        if (!mediasBatchBar || !mediasBatchCountSpan) return;

        const count = selectedMedias.size;

        if (count > 0) {
            mediasBatchBar.classList.add('active');
            mediasBatchCountSpan.textContent = count;
        } else {
            mediasBatchBar.classList.remove('active');
        }
    }

    function getSelectedIds() {
        return Array.from(selectedMedias);
    }

    // Annuler la sélection
    if (batchCancelBtn) {
        batchCancelBtn.addEventListener('click', function () {
            selectedMedias.clear();
            mediaCheckboxes.forEach(cb => { cb.checked = false; });
            document.querySelectorAll('.media-card').forEach(c => c.classList.remove('selected'));
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBatchBar();
        });
    }

    // ============================================================
    // 6. ACTION BATCH : SUPPRIMER
    // ============================================================
    if (batchDeleteBtn) {
        batchDeleteBtn.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ' + ids.length + ' média(s) ?\n\nCette action est irréversible.')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.getAttribute('data-url') || '/admin/medias/batch-delete';
            form.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }

    // ============================================================
    // 7. ACTION BATCH : CHANGER L'ALBUM
    // ============================================================
    if (batchAlbumBtn && albumModal && albumModalInput) {
        batchAlbumBtn.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            albumModalInput.value = '';
            albumModal.classList.add('active');
            setTimeout(() => albumModalInput.focus(), 300);
        });
    }

    if (albumModalClose) {
        albumModalClose.addEventListener('click', closeAlbumModal);
    }

    if (albumModalCancel) {
        albumModalCancel.addEventListener('click', closeAlbumModal);
    }

    if (albumModal) {
        albumModal.addEventListener('click', function (e) {
            if (e.target === albumModal) {
                closeAlbumModal();
            }
        });
    }

    function closeAlbumModal() {
        if (albumModal) albumModal.classList.remove('active');
    }

    if (albumModalSubmit && batchAlbumForm && batchAlbumInput) {
        albumModalSubmit.addEventListener('click', function () {
            const album = albumModalInput.value.trim();
            if (!album) {
                albumModalInput.style.borderColor = 'var(--color-accent)';
                albumModalInput.focus();
                return;
            }

            const ids = getSelectedIds();
            batchAlbumInput.value = album;

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                batchAlbumForm.appendChild(input);
            });

            batchAlbumForm.submit();
        });
    }

    // ============================================================
    // 8. ACTION BATCH : PUBLIER / DÉPUBLIER
    // ============================================================
    if (batchPublierBtn && batchPublierForm && batchPublierInput) {
        batchPublierBtn.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            batchPublierInput.value = '1';

            // Nettoyer les anciens ids
            const oldIds = batchPublierForm.querySelectorAll('input[name="ids[]"]');
            oldIds.forEach(el => el.remove());

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                batchPublierForm.appendChild(input);
            });

            batchPublierForm.submit();
        });
    }

    if (batchDepublierBtn && batchPublierForm && batchPublierInput) {
        batchDepublierBtn.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            batchPublierInput.value = '0';

            // Nettoyer les anciens ids
            const oldIds = batchPublierForm.querySelectorAll('input[name="ids[]"]');
            oldIds.forEach(el => el.remove());

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                batchPublierForm.appendChild(input);
            });

            batchPublierForm.submit();
        });
    }

    // ============================================================
    // 9. LIGHTBOX
    // ============================================================
    const lightbox = document.getElementById('mediaLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxVideo = document.getElementById('lightboxVideo');
    const lightboxTitle = document.getElementById('lightboxTitle');
    const lightboxDescription = document.getElementById('lightboxDescription');
    const lightboxCounter = document.getElementById('lightboxCounter');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');

    let lightboxMedias = [];
    let lightboxIndex = 0;

    // Boutons "Voir" sur les cartes
    const cardViewButtons = document.querySelectorAll('.media-card-overlay-btn.view');

    if (cardViewButtons.length > 0 && lightbox) {
        cardViewButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const card = this.closest('.media-card');
                const mediaId = card?.getAttribute('data-media-id');
                const mediaType = card?.getAttribute('data-media-type');
                const mediaUrl = card?.getAttribute('data-media-url');
                const mediaTitre = card?.getAttribute('data-media-titre');
                const mediaDescription = card?.getAttribute('data-media-description');

                // Construire la liste de tous les médias visibles
                lightboxMedias = [];
                document.querySelectorAll('.media-card[data-media-id]').forEach(c => {
                    lightboxMedias.push({
                        id: c.getAttribute('data-media-id'),
                        type: c.getAttribute('data-media-type'),
                        url: c.getAttribute('data-media-url'),
                        titre: c.getAttribute('data-media-titre'),
                        description: c.getAttribute('data-media-description'),
                    });
                });

                lightboxIndex = lightboxMedias.findIndex(m => m.id === mediaId);
                if (lightboxIndex < 0) lightboxIndex = 0;

                showLightboxMedia(lightboxIndex);
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });
    }

    function showLightboxMedia(index) {
        if (!lightboxMedias.length || !lightboxImage || !lightboxVideo || !lightboxTitle || !lightboxDescription || !lightboxCounter) return;

        const media = lightboxMedias[index];
        if (!media) return;

        lightboxIndex = index;

        if (media.type === 'video') {
            lightboxImage.style.display = 'none';
            lightboxVideo.style.display = 'block';
            lightboxVideo.querySelector('source').src = media.url;
            lightboxVideo.load();
        } else {
            lightboxVideo.style.display = 'none';
            lightboxImage.style.display = 'block';
            lightboxImage.src = media.url;
        }

        lightboxTitle.textContent = media.titre || 'Sans titre';
        lightboxDescription.textContent = media.description || '';
        lightboxCounter.textContent = (index + 1) + ' / ' + lightboxMedias.length;

        // Mettre à jour l'état des boutons nav
        if (lightboxPrev) lightboxPrev.style.visibility = index > 0 ? 'visible' : 'hidden';
        if (lightboxNext) lightboxNext.style.visibility = index < lightboxMedias.length - 1 ? 'visible' : 'hidden';
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    function closeLightbox() {
        if (lightbox) {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
            if (lightboxVideo) lightboxVideo.pause();
        }
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', function (e) {
            e.stopPropagation();
            if (lightboxIndex > 0) {
                showLightboxMedia(lightboxIndex - 1);
            }
        });
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', function (e) {
            e.stopPropagation();
            if (lightboxIndex < lightboxMedias.length - 1) {
                showLightboxMedia(lightboxIndex + 1);
            }
        });
    }

    // Navigation clavier
    document.addEventListener('keydown', function (e) {
        if (!lightbox || !lightbox.classList.contains('active')) return;

        switch (e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                if (lightboxIndex > 0) showLightboxMedia(lightboxIndex - 1);
                break;
            case 'ArrowRight':
                if (lightboxIndex < lightboxMedias.length - 1) showLightboxMedia(lightboxIndex + 1);
                break;
        }
    });

        // ============================================================
    // 10. UPLOAD : DRAG & DROP & SYNCHRONISATION
    // ============================================================
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPreviewGrid = document.getElementById('uploadPreviewGrid');
    const uploadProgressContainer = document.getElementById('uploadProgressContainer');
    const uploadProgressFill = document.getElementById('uploadProgressFill');
    const uploadProgressText = document.getElementById('uploadProgressText');
    const uploadForm = document.getElementById('uploadForm');
    const submitUploadBtn = document.getElementById('submitUploadBtn');

    let pendingFiles = [];

    if (uploadZone && fileInput) {
        // Clic sur la zone
        uploadZone.addEventListener('click', function (e) {
            if (e.target !== fileInput && !e.target.closest('.upload-zone-btn')) {
                fileInput.click();
            }
        });

        // Sélection de fichiers
        fileInput.addEventListener('change', function () {
            handleFiles(this.files);
        });

        // Drag events
        uploadZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        // Coller depuis le presse-papier
        document.addEventListener('paste', function (e) {
            if (!uploadZone) return;
            const items = e.clipboardData?.items;
            if (!items) return;

            const files = [];
            for (const item of items) {
                if (item.kind === 'file') {
                    files.push(item.getAsFile());
                }
            }
            if (files.length > 0) {
                handleFiles(files);
            }
        });
    }

    function syncInputFiles() {
        if (!fileInput) return;
        const dt = new DataTransfer();
        pendingFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function handleFiles(files) {
        if (!files || files.length === 0) return;

        const maxSizeImage = 10 * 1024 * 1024;  // 10 Mo pour les images
        const maxSizeVideo = 50 * 1024 * 1024;  // 50 Mo pour les vidéos
        const imageFormats = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        const videoFormats = ['mp4', 'webm', 'ogg'];
        const acceptedFormats = [...imageFormats, ...videoFormats];
        const maxFiles = 20;

        let validFiles = [];
        let errors = [];

        Array.from(files).forEach(file => {
            const ext = file.name.split('.').pop().toLowerCase();

            if (!acceptedFormats.includes(ext)) {
                errors.push(file.name + ' : format non supporté');
                return;
            }

            const isImage = imageFormats.includes(ext);
            const maxSize = isImage ? maxSizeImage : maxSizeVideo;

            if (file.size > maxSize) {
                const maxMo = maxSize / (1024 * 1024);
                errors.push(file.name + ' : dépasse ' + maxMo + ' Mo');
                return;
            }

            if (pendingFiles.length + validFiles.length >= maxFiles) {
                errors.push('Maximum ' + maxFiles + ' fichiers');
                return;
            }

            validFiles.push(file);
        });

        if (errors.length > 0) {
            showToast(errors.join('<br>'), 'error');
        }

        if (validFiles.length > 0) {
            pendingFiles = pendingFiles.concat(validFiles);
            syncInputFiles();
            renderPreviewGrid();
            updateSubmitButton();
        }
    }

    function renderPreviewGrid() {
        if (!uploadPreviewGrid) return;

        uploadPreviewGrid.innerHTML = '';

        pendingFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'upload-preview-card';

            const reader = new FileReader();
            const isImage = file.type.startsWith('image/');
            const isVideo = file.type.startsWith('video/');

            reader.onload = function (e) {
                let mediaHTML = '';
                if (isImage) {
                    mediaHTML = `<img src="${e.target.result}" alt="${file.name}">`;
                } else if (isVideo) {
                    mediaHTML = `<video src="${e.target.result}" muted></video>`;
                } else {
                    mediaHTML = `<i class="fas fa-file" style="font-size: 2rem; color: #ccc;"></i>`;
                }

                card.innerHTML = `
                    <div class="upload-preview-thumb">${mediaHTML}</div>
                    <div class="upload-preview-name" title="${file.name}">${file.name}</div>
                    <div class="upload-preview-size">${formatFileSize(file.size)}</div>
                    <button type="button" class="upload-preview-remove" data-index="${index}" title="Retirer">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                // Bouton retirer
                card.querySelector('.upload-preview-remove').addEventListener('click', function (e) {
                    e.stopPropagation();
                    pendingFiles.splice(index, 1);
                    syncInputFiles();
                    renderPreviewGrid();
                    updateSubmitButton();
                });
            };

            reader.readAsDataURL(file);
            uploadPreviewGrid.appendChild(card);
        });
    }

    function updateSubmitButton() {
        if (!submitUploadBtn) return;
        submitUploadBtn.disabled = pendingFiles.length === 0;
        submitUploadBtn.textContent = pendingFiles.length > 0
            ? 'Enregistrer ' + pendingFiles.length + ' fichier(s)'
            : 'Aucun fichier sélectionné';
    }

    // Soumission du formulaire d'upload
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            if (pendingFiles.length === 0) {
                e.preventDefault();
                showToast('Veuillez sélectionner au moins un fichier.', 'error');
                return;
            }

            // S'assurer que le DataTransfer est bien synchronisé juste avant le submit
            syncInputFiles();

            // Afficher la progression
            if (uploadProgressContainer) {
                uploadProgressContainer.style.display = 'block';
            }
            if (uploadProgressFill) {
                uploadProgressFill.style.width = '100%';
            }
            if (uploadProgressText) {
                uploadProgressText.textContent = 'Téléversement en cours...';
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 o';
        const units = ['o', 'Ko', 'Mo', 'Go'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(1)) + ' ' + units[i];
    }


    // ============================================================
    // 11. CONFIRMATION SUPPRESSION INDIVIDUELLE
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-media');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const titre = this.getAttribute('data-titre') || 'ce média';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement "' + titre + '" ?\n\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 12. TOAST NOTIFICATION
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
        closeBtn.addEventListener('click', () => removeToast(toast));

        setTimeout(() => removeToast(toast), duration);
    }

    function removeToast(toast) {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }

    // ============================================================
    // 13. ANIMATIONS KPI
    // ============================================================
    const statCards = document.querySelectorAll('.medias-stat-card');

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
    // 14. INITIALISATION
    // ============================================================
    console.log(
        '%c🖼️ AHD-RDC %c— Galerie Médias chargée',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cDrag & Drop • Lightbox • Batch Actions • Vue Grille/Liste',
        'color: #28a745; font-size: 11px;'
    );
});
