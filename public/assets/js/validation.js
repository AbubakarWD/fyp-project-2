/**
 * BloodLife — Form Validation Script
 * Provides client-side validation rules with accessible feedback indicators.
 */

document.addEventListener('DOMContentLoaded', () => {
    const validatedForms = document.querySelectorAll('.needs-validation');

    validatedForms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Custom password matching validation
            const pass = form.querySelector('input[name="password"]');
            const confirmPass = form.querySelector('input[name="confirm_password"]');

            if (pass && confirmPass) {
                if (pass.value !== confirmPass.value) {
                    confirmPass.setCustomValidity("Passwords do not match");
                    confirmPass.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    confirmPass.setCustomValidity("");
                    confirmPass.classList.remove('is-invalid');
                }
            }

            form.classList.add('was-validated');
        }, false);
    });
});
