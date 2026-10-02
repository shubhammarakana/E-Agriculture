// js/login.js

document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const roleBtns = document.querySelectorAll('.role-btn');
    const roleInput = document.getElementById('roleInput');

    // Role Toggle Logic
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active from all
            roleBtns.forEach(b => b.classList.remove('active'));
            // Add active to clicked
            btn.classList.add('active');

            // Update Hidden Input
            roleInput.value = btn.dataset.role;
        });
    });
});
