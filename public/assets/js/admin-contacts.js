/**
 * AHD-RDC — SCRIPTS MESSAGES DE CONTACT (ADMIN)
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. SOUMISSION AUTO DES FILTRES
    // ============================================================
    const filtreSelects = document.querySelectorAll('.contacts-filtre-select');

    filtreSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // ============================================================
    // 2. RECHERCHE AVEC DEBOUNCE
    // ============================================================
    const searchInput = document.getElementById('contactSearch');
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
    const deleteButtons = document.querySelectorAll('.btn-delete-contact');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const nom = this.getAttribute('data-nom') || 'ce message';
            if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement le message de "' + nom + '" ?\n\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 4. CONFIRMATION SUPPRESSION CONVERSATION ENTIÈRE
    // ============================================================
    const deleteConvButtons = document.querySelectorAll('.btn-delete-conversation');

    deleteConvButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const email = this.getAttribute('data-email') || 'cet expéditeur';
            const count = this.getAttribute('data-count') || 'plusieurs';
            if (!confirm('Êtes-vous sûr de vouloir supprimer TOUS les messages de "' + email + '" ?\n\n' + count + ' message(s) seront définitivement supprimés.\nCette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 5. MARQUER COMME LU / NON LU (AJAX)
    // ============================================================
    const toggleReadButtons = document.querySelectorAll('.btn-toggle-read');

    toggleReadButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const isRead = this.getAttribute('data-read') === 'true';
            const row = this.closest('.contacts-list-item') || this.closest('.contact-message-item');

            if (!url) return;

            fetch(url, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (row) {
                        if (isRead) {
                            row.classList.add('non-lu');
                            this.setAttribute('data-read', 'false');
                            this.querySelector('i').className = 'fas fa-envelope';
                            this.setAttribute('title', 'Marquer comme lu');
                        } else {
                            row.classList.remove('non-lu');
                            this.setAttribute('data-read', 'true');
                            this.querySelector('i').className = 'fas fa-envelope-open';
                            this.setAttribute('title', 'Marquer comme non lu');
                        }
                    }
                    showToast(data.message || 'Statut mis à jour.', 'success');
                }
            })
            .catch(() => {
                showToast('Erreur lors de la mise à jour.', 'error');
            });
        });
    });

    // ============================================================
    // 6. CHANGEMENT RAPIDE DE STATUT
    // ============================================================
    const statutSelects = document.querySelectorAll('.contact-statut-select');

    statutSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    // ============================================================
    // 7. VALIDATION RÉPONSE
    // ============================================================
    const replyForm = document.getElementById('replyForm');
    const replyTextarea = document.getElementById('replyTextarea');

    if (replyForm && replyTextarea) {
        replyForm.addEventListener('submit', function (e) {
            const reponse = replyTextarea.value.trim();

            if (!reponse) {
                e.preventDefault();
                replyTextarea.style.borderColor = 'var(--color-accent)';
                replyTextarea.style.background = '#fffbfb';
                replyTextarea.focus();
                showToast('Veuillez rédiger une réponse avant d\'envoyer.', 'error');
                return;
            }

            
        });

        replyTextarea.addEventListener('focus', function () {
            this.style.borderColor = '';
            this.style.background = '';
        });
    }

    // ============================================================
    // 8. COMPTEUR DE CARACTÈRES POUR LA RÉPONSE
    // ============================================================
    if (replyTextarea) {
        const replyCounter = document.getElementById('replyCounter');
        if (replyCounter) {
            replyCounter.textContent = replyTextarea.value.length;
            replyTextarea.addEventListener('input', function () {
                replyCounter.textContent = this.value.length;
            });
        }
    }

    // ============================================================
    // 9. VALIDATION NOTE INTERNE
    // ============================================================
    const noteForm = document.getElementById('noteForm');
    const noteTextarea = document.getElementById('noteTextarea');

    if (noteForm && noteTextarea) {
        noteForm.addEventListener('submit', function (e) {
            const note = noteTextarea.value.trim();

            if (note.length > 2000) {
                e.preventDefault();
                showToast('La note interne ne doit pas dépasser 2000 caractères.', 'error');
            }
        });
    }

    // ============================================================
    // 10. ANIMATION DES KPI
    // ============================================================
    const statCards = document.querySelectorAll('.contact-stat-card');

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
    // 11. ANIMATION DES CHIFFRES KPI
    // ============================================================
    const kpiValues = document.querySelectorAll('.contact-stat-value');

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
    // 13. INITIALISATION
    // ============================================================
    console.log(
        '%c📬 AHD-RDC %c— Boîte de réception chargée',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%cMessages • Conversations • Réponses • Filtres',
        'color: #28a745; font-size: 11px;'
    );
});