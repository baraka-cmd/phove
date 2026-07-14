/**
 * AHD-RDC — SCRIPTS COMMUNS DASHBOARD ADMINISTRATEUR
 * Action Humanitaire pour le Développement en RD Congo
 * « Être ensemble pour le bien de la communauté »
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. TOGGLE SIDEBAR (RÉDUIRE / AGRANDIR)
    // ============================================================
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');

    // Bouton dans la sidebar (flèche)
    if (sidebarCollapseBtn && sidebar) {
        sidebarCollapseBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Bouton dans la topbar
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Restaurer l'état de la sidebar
    if (sidebar && localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    // Mobile : ouvrir la sidebar
    if (sidebarToggleMobile && sidebar && sidebarOverlay) {
        sidebarToggleMobile.addEventListener('click', function () {
            sidebar.classList.add('mobile-open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // Mobile : fermer la sidebar
    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // ============================================================
    // 2. GESTION DES SOUS-MENUS
    // ============================================================
    const menuItems = document.querySelectorAll('.sidebar-menu-item.has-submenu');

    menuItems.forEach(item => {
        const link = item.querySelector('.sidebar-menu-link');

        if (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Fermer les autres sous-menus
                menuItems.forEach(other => {
                    if (other !== item && other.classList.contains('open')) {
                        other.classList.remove('open');
                    }
                });

                // Ouvrir/fermer ce sous-menu
                item.classList.toggle('open');

                // Si la sidebar est réduite, l'agrandir
                if (sidebar && sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        }
    });

    // Activer le menu courant
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-menu-link, .sidebar-submenu-link');

    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.startsWith(href) && href !== '/admin') {
            link.classList.add('active');

            // Ouvrir le parent si c'est un sous-menu
            const parentItem = link.closest('.sidebar-menu-item');
            if (parentItem) {
                parentItem.classList.add('open');
            }
        }
    });

    // ============================================================
    // 3. RECHERCHE RAPIDE TOPBAR
    // ============================================================
    const searchBtn = document.getElementById('topbarSearchBtn');
    const searchWrapper = document.getElementById('topbarSearchWrapper');
    const searchInput = document.getElementById('topbarSearchInput');
    const searchClose = document.getElementById('topbarSearchClose');

    if (searchBtn && searchWrapper && searchInput && searchClose) {
        searchBtn.addEventListener('click', function () {
            searchWrapper.classList.add('active');
            setTimeout(() => searchInput.focus(), 300);
        });

        searchClose.addEventListener('click', function () {
            searchWrapper.classList.remove('active');
            searchInput.value = '';
        });

        // Fermer avec Échap
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchWrapper.classList.contains('active')) {
                searchWrapper.classList.remove('active');
                searchInput.value = '';
            }
        });

        // Recherche à la soumission
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = '/admin/recherche?q=' + encodeURIComponent(query);
                }
            }
        });
    }

    // ============================================================
    // 4. NOTIFICATIONS DROPDOWN
    // ============================================================
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('active');

            // Fermer le dropdown utilisateur s'il est ouvert
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                userDropdown.classList.remove('active');
            }
        });

        // Fermer en cliquant ailleurs
        document.addEventListener('click', function (e) {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('active');
            }
        });
    }

    // ============================================================
    // 5. PROFIL UTILISATEUR DROPDOWN
    // ============================================================
    const userBtn = document.getElementById('userBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');

            // Fermer le dropdown notifications s'il est ouvert
            if (notifDropdown) {
                notifDropdown.classList.remove('active');
            }
        });

        // Fermer en cliquant ailleurs
        document.addEventListener('click', function (e) {
            if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // ============================================================
    // 6. MODE SOMBRE (DARK MODE)
    // ============================================================
    const darkModeBtn = document.getElementById('darkModeBtn');

    if (darkModeBtn) {
        // Vérifier la préférence sauvegardée
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
            updateDarkModeIcon(true);
        }

        darkModeBtn.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('dark-mode', isDark);
            updateDarkModeIcon(isDark);
        });

        function updateDarkModeIcon(isDark) {
            const icon = darkModeBtn.querySelector('i');
            if (icon) {
                if (isDark) {
                    icon.className = 'fas fa-sun';
                    darkModeBtn.style.color = '#f39c12';
                    darkModeBtn.style.borderColor = '#f39c12';
                } else {
                    icon.className = 'fas fa-moon';
                    darkModeBtn.style.color = '';
                    darkModeBtn.style.borderColor = '';
                }
            }
        }
    }

    // ============================================================
    // 7. HORLOGE EN TEMPS RÉEL DANS LE FOOTER
    // ============================================================
    const clockElement = document.getElementById('footerClock');

    if (clockElement) {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockElement.textContent = `${hours}:${minutes}:${seconds}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

    // ============================================================
    // 8. LOADER GLOBAL (AFFICHER/CACHER)
    // ============================================================
    const globalLoader = document.getElementById('globalLoader');

    window.showLoader = function () {
        if (globalLoader) {
            globalLoader.classList.add('active');
        }
    };

    window.hideLoader = function () {
        if (globalLoader) {
            globalLoader.classList.remove('active');
        }
    };

    // Cacher le loader après le chargement de la page
    if (globalLoader) {
        window.addEventListener('load', function () {
            setTimeout(hideLoader, 400);
        });
    }

    // ============================================================
    // 9. TOAST NOTIFICATIONS
    // ============================================================
    window.showToast = function (message, type = 'success', duration = 5000) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${type === 'success' ? 'Succès' : type === 'error' ? 'Erreur' : 'Information'}</div>
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
    };

    function removeToast(toast) {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }

    // ============================================================
    // 10. CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 11. FERMETURE AUTO DES ALERTES
    // ============================================================
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-persistent')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                alert.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }, 6000);
        }
    });

    // ============================================================
    // 12. INITIALISATION
    // ============================================================
    console.log(
        '%c🛡️ AHD-RDC %c— Dashboard Administrateur chargé',
        'color: #16558F; font-weight: bold; font-size: 14px;',
        'color: #777; font-size: 12px;'
    );
    console.log(
        '%c« Être ensemble pour le bien de la communauté »',
        'color: #D32F2F; font-style: italic; font-size: 11px;'
    );

});