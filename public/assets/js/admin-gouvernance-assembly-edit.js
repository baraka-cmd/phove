/**
 * AHD-RDC — SCRIPTS ÉDITION ASSEMBLÉE GÉNÉRALE
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. CALCUL AUTOMATIQUE DU QUORUM
    // ============================================================
    const membresTotal = document.getElementById('membres_total');
    const membresPresents = document.getElementById('membres_presents');
    const membresAbsents = document.getElementById('membres_absents');
    const quorumInfo = document.getElementById('quorumInfo');

    function updateQuorum() {
        if (!membresTotal || !membresPresents || !quorumInfo) return;

        const total = parseInt(membresTotal.value) || 0;
        const presents = parseInt(membresPresents.value) || 0;

        if (total > 0 && presents >= 0) {
            const quorumMinimum = total / 2;
            const atteint = presents >= quorumMinimum;
            const pourcentage = total > 0 ? Math.round((presents / total) * 100) : 0;

            quorumInfo.className = 'quorum-info-box ' + (atteint ? 'quorum-info-atteint' : 'quorum-info-non-atteint');
            quorumInfo.innerHTML = '<i class="fas fa-' + (atteint ? 'check-circle' : 'times-circle') + '"></i> ' +
                'Quorum ' + (atteint ? 'atteint' : 'non atteint') + ' : ' + presents + '/' + total + ' présents (' + pourcentage + '%)' +
                ' — Majorité simple requise : ' + Math.ceil(quorumMinimum) + ' membres';
        } else {
            quorumInfo.className = 'quorum-info-box quorum-info-default';
            quorumInfo.innerHTML = '<i class="fas fa-info-circle"></i> Remplissez les champs pour voir le quorum';
        }
    }

    if (membresTotal && membresPresents) {
        membresTotal.addEventListener('input', updateQuorum);
        membresPresents.addEventListener('input', updateQuorum);
        updateQuorum();
    }

    // ============================================================
    // 2. CALCUL AUTOMATIQUE DES ABSENTS
    // ============================================================
    function updateAbsents() {
        if (!membresTotal || !membresPresents || !membresAbsents) return;

        const total = parseInt(membresTotal.value) || 0;
        const presents = parseInt(membresPresents.value) || 0;
        const absents = Math.max(0, total - presents);
        membresAbsents.value = absents;
    }

    if (membresTotal && membresPresents && membresAbsents) {
        membresTotal.addEventListener('input', updateAbsents);
        membresPresents.addEventListener('input', updateAbsents);
    }

    // ============================================================
    // 3. VALIDATION DU FORMULAIRE
    // ============================================================
    const assemblyForm = document.querySelector('form');

    if (assemblyForm) {
        assemblyForm.addEventListener('submit', function (e) {
            const type = document.getElementById('type')?.value;
            const dateReunion = document.getElementById('date_reunion')?.value;
            const lieu = document.getElementById('lieu')?.value?.trim();
            const statut = document.getElementById('statut')?.value;

            if (!type || !dateReunion || !lieu || !statut) {
                e.preventDefault();

                ['type', 'date_reunion', 'lieu', 'statut'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field && !field.value) {
                        field.style.borderColor = 'var(--color-accent)';
                        field.style.background = '#fffbfb';
                        field.style.transition = 'all 0.25s ease';
                    }
                });

                showToast('Veuillez remplir tous les champs obligatoires (Type, Date, Lieu, Statut).', 'error');
                return;
            }
        });
    }

    // ============================================================
    // 4. EFFACER LES ERREURS AU FOCUS
    // ============================================================
    const formInputs = document.querySelectorAll('.assembly-edit-input, .assembly-edit-select, .assembly-edit-textarea');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    // ============================================================
    // 5. AFFICHAGE DU NOM DU FICHIER UPLOADÉ
    // ============================================================
    const fileInput = document.getElementById('pv_fichier');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const fileName = this.files[0]?.name;
            if (fileName) {
                let fileInfo = document.getElementById('fileInfo');
                if (!fileInfo) {
                    fileInfo = document.createElement('span');
                    fileInfo.id = 'fileInfo';
                    fileInfo.style.cssText = 'display: block; margin-top: 6px; font-size: 12px; color: var(--color-success); font-weight: 500;';
                    this.parentElement.appendChild(fileInfo);
                }
                fileInfo.innerHTML = '<i class="fas fa-check-circle"></i> Nouveau fichier : ' + fileName;
            }
        });
    }

    // ============================================================
    // 6. ANIMATION DU FORMULAIRE
    // ============================================================
    const formCard = document.querySelector('.assembly-edit-card');

    if (formCard && 'IntersectionObserver' in window) {
        const formObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    formObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        formCard.style.opacity = '0';
        formCard.style.transform = 'translateY(20px)';
        formCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        formObserver.observe(formCard);
    }

    // ============================================================
    // 7. TOAST NOTIFICATION
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
    // 8. INITIALISATION
    // ============================================================
    console.log(
        '%c📋 AHD-RDC %c— Édition AG chargée',
        'color: #16558F; font-weight: bold;',
        'color: #777;'
    );

});