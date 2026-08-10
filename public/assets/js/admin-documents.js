/**
 * AHD-RDC — SCRIPTS GESTION DOCUMENTS OFFICIELS (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.documents-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DEBOUNCE
    // ============================================================
    const searchInput = document.getElementById('documentSearch');
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
    // 3. UPLOAD DE FICHIER — PRÉVISUALISATION
    // ============================================================
    const fileInput = document.getElementById('fichier');
    const uploadZone = document.getElementById('uploadZone');
    const uploadFileInfo = document.getElementById('uploadFileInfo');
    const uploadFileName = document.getElementById('uploadFileName');
    const uploadFileSize = document.getElementById('uploadFileSize');
    const uploadFileIcon = document.getElementById('uploadFileIcon');
    const uploadFileRemove = document.getElementById('uploadFileRemove');
    const uploadIconMain = document.getElementById('uploadIconMain');
    const uploadTextMain = document.getElementById('uploadTextMain');
    const uploadSubtextMain = document.getElementById('uploadSubtextMain');

    if (fileInput && uploadZone) {
        // Clic sur la zone
        uploadZone.addEventListener('click', function (e) {
            if (e.target !== fileInput && e.target !== uploadFileRemove) {
                fileInput.click();
            }
        });

        // Changement de fichier
        fileInput.addEventListener('change', function () {
            handleFileSelection(this.files);
        });

        // Drag & drop
        uploadZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = 'var(--color-primary)';
            this.style.background = 'var(--color-primary-lighter)';
        });

        uploadZone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.style.borderColor = '#d0d5dd';
            this.style.background = '#fafbfc';
        });

        uploadZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '#d0d5dd';
            this.style.background = '#fafbfc';
            handleFileSelection(e.dataTransfer.files);
        });
    }

    function handleFileSelection(files) {
        if (!files || files.length === 0) return;

        const file = files[0];
        const acceptedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];
        const ext = file.name.split('.').pop().toLowerCase();
        const maxSize = 20 * 1024 * 1024; // 20 Mo

        if (!acceptedExtensions.includes(ext)) {
            showToast('Format non supporté. Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV.', 'error');
            fileInput.value = '';
            return;
        }

        if (file.size > maxSize) {
            showToast('Le fichier dépasse la taille maximale de 20 Mo.', 'error');
            fileInput.value = '';
            return;
        }

        // Mettre à jour l'affichage
        if (uploadZone) uploadZone.classList.add('has-file');
        if (uploadFileInfo) uploadFileInfo.style.display = 'flex';
        if (uploadFileName) uploadFileName.textContent = file.name;
        if (uploadFileSize) uploadFileSize.textContent = formatFileSize(file.size);
        if (uploadIconMain) uploadIconMain.className = 'fas fa-file-alt';
        if (uploadTextMain) uploadTextMain.textContent = 'Fichier sélectionné';
        if (uploadSubtextMain) uploadSubtextMain.textContent = 'Cliquez pour changer de fichier';

        // Icône selon le type
        if (uploadFileIcon) {
            const iconClass = getFileIconClass(ext);
            uploadFileIcon.innerHTML = '<i class="' + iconClass + '"></i>';
            uploadFileIcon.className = 'document-upload-file-icon ' + getFileColorClass(ext);
        }
    }

    // Supprimer le fichier sélectionné
    if (uploadFileRemove && fileInput) {
        uploadFileRemove.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInput.value = '';
            if (uploadZone) uploadZone.classList.remove('has-file');
            if (uploadFileInfo) uploadFileInfo.style.display = 'none';
            if (uploadIconMain) uploadIconMain.className = 'fas fa-cloud-upload-alt';
            if (uploadTextMain) uploadTextMain.textContent = 'Cliquez pour sélectionner un fichier';
            if (uploadSubtextMain) uploadSubtextMain.textContent = 'ou glissez-déposez votre document ici';
        });
    }

    function getFileIconClass(ext) {
        const icons = {
            'pdf': 'fas fa-file-pdf',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'xls': 'fas fa-file-excel',
            'xlsx': 'fas fa-file-excel',
            'ppt': 'fas fa-file-powerpoint',
            'pptx': 'fas fa-file-powerpoint',
            'txt': 'fas fa-file-alt',
            'csv': 'fas fa-file-csv'
        };
        return icons[ext] || 'fas fa-file';
    }

    function getFileColorClass(ext) {
        const colors = {
            'pdf': 'pdf',
            'doc': 'doc',
            'docx': 'doc',
            'xls': 'xls',
            'xlsx': 'xls',
            'ppt': 'ppt',
            'pptx': 'ppt'
        };
        return colors[ext] || 'autre';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 o';
        const units = ['o', 'Ko', 'Mo', 'Go'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(1)) + ' ' + units[i];
    }

    // ============================================================
    // 4. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-document');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const titre = this.getAttribute('data-titre') || 'ce document';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement "' + titre + '" ?\n\nCette action est irréversible et le fichier sera supprimé du serveur.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 5. VALIDATION DU FORMULAIRE
    // ============================================================
    const documentForm = document.querySelector('form');

    if (documentForm && documentForm.querySelector('input[name="titre"]')) {
        documentForm.addEventListener('submit', function (e) {
            const titre = document.getElementById('titre')?.value?.trim();
            const type = document.getElementById('type')?.value;
            const fichierInput = document.getElementById('fichier');
            const isCreateForm = documentForm.querySelector('input[name="_method"]')?.value !== 'PUT';

            let errors = [];

            if (!titre) errors.push({ field: 'titre', message: 'Le titre est obligatoire.' });
            if (!type) errors.push({ field: 'type', message: 'Le type de document est obligatoire.' });
            if (isCreateForm && fichierInput && !fichierInput.files.length) {
                errors.push({ field: 'fichier', message: 'Veuillez sélectionner un fichier.' });
            }

            if (errors.length > 0) {
                e.preventDefault();

                errors.forEach(err => {
                    const field = document.getElementById(err.field);
                    if (field) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fffbfb';
                    }
                    if (err.field === 'fichier' && uploadZone) {
                        uploadZone.style.borderColor = 'var(--color-accent)';
                        uploadZone.style.background = '#fffbfb';
                    }
                });

                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
            }
        });
    }

    // ============================================================
    // 6. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.document-form-input, .document-form-select, .document-form-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
            if (uploadZone) {
                uploadZone.style.borderColor = '#d0d5dd';
                uploadZone.style.background = '#fafbfc';
            }
        });
    });

    // ============================================================
    // 7. ANIMATION DES KPI
    // ============================================================
    const statCards = document.querySelectorAll('.document-stat-card');

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
    // 8. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.document-stat-value');

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
    // 9. TOAST NOTIFICATION
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
    // 10. INITIALISATION
    // ============================================================
    console.log(
        '%c📄 AHD-RDC %c— Gestion Documents chargée',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cUpload • Filtres • KPI Animés • Drag & Drop',
        'color: #28a745; font-size: 11px;'
    );
});