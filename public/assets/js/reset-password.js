/**
 * AHD-RDC — SCRIPTS PAGE RÉINITIALISATION MOT DE PASSE
 */

document.addEventListener('DOMContentLoaded', function () {

    // -------------------------------------------------------
    // 1. TOGGLE VISIBILITÉ DES MOTS DE PASSE
    // -------------------------------------------------------
    const togglePasswords = document.querySelectorAll('.toggle-password');

    togglePasswords.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('active');
            }
        });
    });

    // -------------------------------------------------------
    // 2. VÉRIFICATION EN TEMPS RÉEL DE LA FORCE DU MOT DE PASSE
    // -------------------------------------------------------
    const passwordInput = document.getElementById('mot_de_passe');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            const strength = calculateStrength(password);

            // Afficher la barre si du texte est saisi
            if (password.length > 0) {
                strengthBar.classList.add('visible');
            } else {
                strengthBar.classList.remove('visible');
            }

            // Mettre à jour la barre
            strengthFill.className = 'strength-fill';
            strengthText.className = 'strength-text';

            if (strength <= 2) {
                strengthFill.classList.add('weak');
                strengthText.classList.add('weak');
                strengthText.textContent = 'Faible';
            } else if (strength <= 3) {
                strengthFill.classList.add('medium');
                strengthText.classList.add('medium');
                strengthText.textContent = 'Moyen';
            } else if (strength <= 4) {
                strengthFill.classList.add('good');
                strengthText.classList.add('good');
                strengthText.textContent = 'Bon';
            } else {
                strengthFill.classList.add('strong');
                strengthText.classList.add('strong');
                strengthText.textContent = 'Très fort';
            }

            // Mettre à jour les exigences
            updateRequirement(reqLength, password.length >= 8);
            updateRequirement(reqUppercase, /[A-Z]/.test(password));
            updateRequirement(reqLowercase, /[a-z]/.test(password));
            updateRequirement(reqNumber, /[0-9]/.test(password));
            updateRequirement(reqSpecial, /[^A-Za-z0-9]/.test(password));
        });
    }

    function calculateStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function updateRequirement(element, valid) {
        if (valid) {
            element.classList.add('valid');
        } else {
            element.classList.remove('valid');
        }
    }

    // -------------------------------------------------------
    // 3. LOADER À LA SOUMISSION
    // -------------------------------------------------------
    const resetForm = document.getElementById('resetPasswordForm');
    const resetBtn = document.getElementById('resetPasswordBtn');

    if (resetForm && resetBtn) {
        resetForm.addEventListener('submit', function (e) {
            const password = document.getElementById('mot_de_passe').value.trim();
            const confirmation = document.getElementById('mot_de_passe_confirmation').value.trim();

            if (!password || !confirmation || password.length < 8) {
                e.preventDefault();
                return;
            }

            resetBtn.classList.add('btn-loading');
            resetBtn.disabled = true;
        });
    }

    // -------------------------------------------------------
    // 4. EFFACER LES ERREURS AU FOCUS
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

});