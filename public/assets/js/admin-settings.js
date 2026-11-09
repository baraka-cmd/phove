/**
 * AHD-RDC — SCRIPTS PARAMÈTRES GÉNÉRAUX (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. PRÉVISUALISATION DES IMAGES (LOGO, FAVICON)
    // ============================================================
    const imageInputs = document.querySelectorAll('.settings-image-input');

    imageInputs.forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // Validation
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format non supporté. Utilisez JPG, PNG ou WebP.', 'error');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showToast('L\'image ne doit pas dépasser 2 Mo.', 'error');
                this.value = '';
                return;
            }

            // Prévisualisation
            const previewId = this.getAttribute('data-preview');
            const previewImg = document.getElementById(previewId);
            const placeholderId = this.getAttribute('data-placeholder');
            const placeholder = document.getElementById(placeholderId);

            if (previewImg) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Afficher le bouton de suppression
            const removeBtnId = this.getAttribute('data-remove-btn');
            const removeBtn = document.getElementById(removeBtnId);
            if (removeBtn) {
                removeBtn.style.display = 'inline-flex';
            }

            // Réinitialiser le flag de suppression
            const removeFlagId = this.getAttribute('data-remove-flag');
            const removeFlag = document.getElementById(removeFlagId);
            if (removeFlag) {
                removeFlag.value = '0';
            }
        });
    });

    // ============================================================
    // 2. SUPPRESSION D'IMAGE
    // ============================================================
    const removeButtons = document.querySelectorAll('.btn-remove-image');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const previewId = this.getAttribute('data-preview');
            const previewImg = document.getElementById(previewId);
            const placeholderId = this.getAttribute('data-placeholder');
            const placeholder = document.getElementById(placeholderId);
            const inputId = this.getAttribute('data-input');
            const input = document.getElementById(inputId);
            const removeFlagId = this.getAttribute('data-remove-flag');
            const removeFlag = document.getElementById(removeFlagId);

            if (!confirm('Voulez-vous vraiment supprimer cette image ?')) return;

            // Masquer l'aperçu
            if (previewImg) {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
            if (placeholder) {
                placeholder.style.display = 'flex';
            }

            // Vider l'input file
            if (input) {
                input.value = '';
            }

            // Activer le flag de suppression
            if (removeFlag) {
                removeFlag.value = '1';
            }

            // Masquer le bouton de suppression
            this.style.display = 'none';

            showToast('Image supprimée. Enregistrez pour confirmer.', 'success');
        });
    });

    // ============================================================
    // 3. TOGGLE SWITCH — MISE À JOUR DU TEXTE D'ÉTAT
    // ============================================================
    const switchInputs = document.querySelectorAll('.settings-switch input[type="checkbox"]');

    switchInputs.forEach(switchInput => {
        switchInput.addEventListener('change', function () {
            const stateId = this.getAttribute('data-state');
            const stateElement = document.getElementById(stateId);

            if (stateElement) {
                if (this.checked) {
                    stateElement.textContent = 'Activé';
                    stateElement.style.color = 'var(--color-success)';
                } else {
                    stateElement.textContent = 'Désactivé';
                    stateElement.style.color = 'var(--color-text-muted)';
                }
            }
        });
    });

    // ============================================================
    // 4. CONFIRMATION AVANT CHANGEMENT DE GROUPE SI MODIFICATIONS
    // ============================================================
    const settingsForm = document.getElementById('settingsForm');
    let formModified = false;

    if (settingsForm) {
        // Détecter les modifications
        const formInputs = settingsForm.querySelectorAll('input:not([type="checkbox"]):not([type="file"]), textarea, select');
        const formCheckboxes = settingsForm.querySelectorAll('input[type="checkbox"]');
        const formFiles = settingsForm.querySelectorAll('input[type="file"]');

        formInputs.forEach(input => {
            input.addEventListener('input', () => { formModified = true; });
        });

        formCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => { formModified = true; });
        });

        formFiles.forEach(fileInput => {
            fileInput.addEventListener('change', () => { formModified = true; });
        });

        // Avertir avant de quitter
        const tabLinks = document.querySelectorAll('.settings-tab');
        tabLinks.forEach(tab => {
            tab.addEventListener('click', function (e) {
                if (formModified) {
                    if (!confirm('Vous avez des modifications non enregistrées. Voulez-vous vraiment changer de groupe ?')) {
                        e.preventDefault();
                    }
                }
            });
        });

        // Réinitialiser après soumission
        settingsForm.addEventListener('submit', () => {
            formModified = false;
        });
    }

    // ============================================================
    // 5. COMPTEUR DE CARACTÈRES POUR TEXTAREAS
    // ============================================================
    const textareas = document.querySelectorAll('.settings-textarea');

    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        if (!maxLength) return;

        // Créer le compteur
        const counter = document.createElement('small');
        counter.style.cssText = 'font-size: 11px; color: var(--color-text-muted); display: block; text-align: right; margin-top: 4px;';
        counter.textContent = textarea.value.length + ' / ' + maxLength + ' caractères';
        textarea.parentNode.appendChild(counter);

        textarea.addEventListener('input', function () {
            counter.textContent = this.value.length + ' / ' + maxLength + ' caractères';

            if (this.value.length > maxLength * 0.9) {
                counter.style.color = '#e67e22';
            } else {
                counter.style.color = 'var(--color-text-muted)';
            }
        });
    });

    // ============================================================
    // 6. TOAST NOTIFICATION
    // ============================================================
    function showToast(message, type = 'success', duration = 4000) {
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
    // 7. SAUVEGARDE AVEC CTRL+S
    // ============================================================
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (settingsForm) {
                if (typeof settingsForm.requestSubmit === 'function') {
                    settingsForm.requestSubmit();
                } else {
                    settingsForm.submit();
                }
            }
        }
    });

    // ============================================================
    // 8. ANIMATION DES TABS
    // ============================================================
    const tabs = document.querySelectorAll('.settings-tab');
    tabs.forEach(tab => {
        tab.addEventListener('mouseenter', function () {
            if (!this.classList.contains('active')) {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.1)';
                    icon.style.transition = 'transform 0.2s ease';
                }
            }
        });

        tab.addEventListener('mouseleave', function () {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // ============================================================
    // 9. INITIALISATION
    // ============================================================
    console.log(
        '%c⚙️ AHD-RDC %c— Paramètres généraux chargés',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cIdentité • Contact • Réseaux sociaux • Pages • SEO • Maintenance',
        'color: #28a745; font-size: 11px;'
    );
});