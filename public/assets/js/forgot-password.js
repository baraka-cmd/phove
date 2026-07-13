/**
 * AHD-RDC — SCRIPTS PAGE MOT DE PASSE OUBLIÉ
 */

document.addEventListener('DOMContentLoaded', function () {

    // -------------------------------------------------------
    // 1. LOADER À LA SOUMISSION
    // -------------------------------------------------------
    const forgotForm = document.getElementById('forgotPasswordForm');
    const forgotBtn = document.getElementById('forgotPasswordBtn');

    if (forgotForm && forgotBtn) {
        forgotForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email').value.trim();

            if (!email) {
                e.preventDefault();
                return;
            }

            forgotBtn.classList.add('btn-loading');
            forgotBtn.disabled = true;
        });
    }

    // -------------------------------------------------------
    // 2. EFFACER LES ERREURS AU FOCUS
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