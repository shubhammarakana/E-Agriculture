// js/register.js

document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const roleBtns = document.querySelectorAll('.role-btn');
    const roleInput = document.getElementById('roleInput');
    const nameLabel = document.getElementById('nameLabel');
    const imageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('imagePreview');
    const defaultIcon = document.getElementById('defaultIcon');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('pwd-strength-bar');

    // Role Toggle Logic
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active from all
            roleBtns.forEach(b => b.classList.remove('active'));
            // Add active to clicked
            btn.classList.add('active');

            // Update Hidden Input and Labels
            const role = btn.dataset.role;
            roleInput.value = role;

            if (role === 'farmer') {
                nameLabel.textContent = 'Farmer Name';
            } else if (role === 'buyer') {
                nameLabel.textContent = 'Name / Business Name';
            }
        });
    });

    // Image Preview Logic
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    if (defaultIcon) defaultIcon.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Password Strength
    if (password && strengthBar) {
        password.addEventListener('input', () => {
            const val = password.value;
            let strength = 0;
            if (val.length > 5) strength += 20;
            if (val.length > 8) strength += 20;
            if (/[A-Z]/.test(val)) strength += 20;
            if (/[0-9]/.test(val)) strength += 20;
            if (/[^A-Za-z0-9]/.test(val)) strength += 20;

            strengthBar.style.width = strength + '%';

            if (strength <= 40) strengthBar.style.backgroundColor = '#ff4d4d'; // Red
            else if (strength <= 80) strengthBar.style.backgroundColor = '#ffbf00'; // Orange
            else strengthBar.style.backgroundColor = '#4caf50'; // Green
        });
    }

    // Match Password Validation (Visual only)
    if (confirmPassword) {
        confirmPassword.addEventListener('input', () => {
            if (confirmPassword.value !== password.value) {
                confirmPassword.style.borderColor = '#ff4d4d';
            } else {
                confirmPassword.style.borderColor = '#16a34a'; // Green match
            }
        });
    }
});
