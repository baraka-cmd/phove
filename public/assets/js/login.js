/**
 * AHD-RDC — SCRIPTS PAGE DE CONNEXION
 * Action Humanitaire pour le Développement en RD Congo
 */

document.addEventListener('DOMContentLoaded', function () {

    // -------------------------------------------------------
    // 1. TOGGLE VISIBILITÉ DU MOT DE PASSE
    // -------------------------------------------------------
    const togglePassword = document.querySelector('.toggle-password');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Changer l'icône
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('active');
            }
        });
    }

    // -------------------------------------------------------
    // 2. LOADER AU MOMENT DE LA SOUMISSION
    // -------------------------------------------------------
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function (e) {
            // Vérification rapide que les champs sont remplis
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('mot_de_passe').value.trim();

            if (!email || !password) {
                e.preventDefault();
                return;
            }

            // Afficher le loader
            loginBtn.classList.add('btn-loading');
            loginBtn.disabled = true;
        });
    }

    // -------------------------------------------------------
    // 3. EFFACER LES ERREURS AU FOCUS
    // -------------------------------------------------------
    const formInputs = document.querySelectorAll('.form-input');

    formInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.classList.remove('error');
            const errorSpan = this.closest('.form-group').querySelector('.form-error');
            if (errorSpan) {
                errorSpan.style.display = 'none';
            }
        });
    });

    // -------------------------------------------------------
    // 4. AUTO-FOCUS SUR LE CHAMP SUIVANT AVEC ENTRÉE
    // -------------------------------------------------------
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('mot_de_passe');

    if (emailInput && passwordInput) {
        emailInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== '') {
                e.preventDefault();
                passwordInput.focus();
            }
        });
    }

    // -------------------------------------------------------
    // 5. ANIMATION DES ALERTES
    // -------------------------------------------------------
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        // Auto-disparition après 8 secondes pour les succès
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                alert.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }, 8000);
        }

        // Bouton de fermeture sur les alertes
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            margin-left: auto;
            padding: 0 4px;
            font-size: 14px;
            opacity: 0.6;
            transition: opacity 0.2s;
        `;
        closeBtn.addEventListener('mouseenter', () => closeBtn.style.opacity = '1');
        closeBtn.addEventListener('mouseleave', () => closeBtn.style.opacity = '0.6');
        closeBtn.addEventListener('click', () => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        });

        alert.appendChild(closeBtn);
    });

});