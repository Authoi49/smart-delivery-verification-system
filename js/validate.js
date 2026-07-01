/**
 * validate.js
 * Client-side validation for the registration form.
 * This runs BEFORE the form submits to give instant feedback.
 * Server-side validation in register.php is still the source of truth.
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let isValid = true;
        clearErrors(form);

        const fullName = form.querySelector('#full_name');
        const email = form.querySelector('#email');
        const phone = form.querySelector('#phone');
        const password = form.querySelector('#password');
        const confirmPassword = form.querySelector('#confirm_password');

        if (fullName.value.trim() === '') {
            showError(fullName, 'Full name is required.');
            isValid = false;
        }

        if (!isValidEmail(email.value.trim())) {
            showError(email, 'Please enter a valid email address.');
            isValid = false;
        }

        if (!isValidPhone(phone.value.trim())) {
            showError(phone, 'Please enter a valid phone number.');
            isValid = false;
        }

        if (password.value.length < 6) {
            showError(password, 'Password must be at least 6 characters.');
            isValid = false;
        }

        if (confirmPassword.value !== password.value) {
            showError(confirmPassword, 'Passwords do not match.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function isValidPhone(value) {
        return /^[0-9+\-\s]{7,20}$/.test(value);
    }

    function showError(inputEl, message) {
        const div = document.createElement('div');
        div.className = 'form-error js-error';
        div.textContent = message;
        inputEl.parentElement.appendChild(div);
        inputEl.style.borderColor = '#dc2626';
    }

    function clearErrors(form) {
        form.querySelectorAll('.js-error').forEach(el => el.remove());
        form.querySelectorAll('input').forEach(el => el.style.borderColor = '');
    }
});
