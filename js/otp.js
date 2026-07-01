/**
 * otp.js
 * Handles the 6-box OTP input UX (auto-advance, backspace, paste)
 * and combines the digits into a single hidden field before submit.
 * Also previews the uploaded delivery proof photo.
 */

document.addEventListener('DOMContentLoaded', function () {
    const otpBoxes = document.querySelectorAll('.otp-box');
    const otpHidden = document.getElementById('otpHidden');
    const form = document.getElementById('verifyForm');
    const photoInput = document.getElementById('proof_photo');
    const photoPreview = document.getElementById('photoPreview');

    otpBoxes.forEach((box, index) => {
        box.addEventListener('input', function () {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 1 && index < otpBoxes.length - 1) {
                otpBoxes[index + 1].focus();
            }
            syncHiddenOtp();
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                otpBoxes[index - 1].focus();
            }
        });

        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            for (let i = 0; i < otpBoxes.length; i++) {
                otpBoxes[i].value = pasted[i] || '';
            }
            syncHiddenOtp();
        });
    });

    function syncHiddenOtp() {
        let code = '';
        otpBoxes.forEach(box => code += box.value);
        otpHidden.value = code;
    }

    if (form) {
        form.addEventListener('submit', function () {
            syncHiddenOtp();
        });
    }

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
