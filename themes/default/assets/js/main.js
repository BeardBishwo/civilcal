document.addEventListener('DOMContentLoaded', function () {
    // --- Permanent Dark Theme (Navy Blue) --- //
    // Set dark theme permanently - no light mode
    document.body.classList.add('dark-theme');
    document.body.setAttribute('data-theme', 'dark');
    
    // Disable theme toggle button
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        themeToggleBtn.style.opacity = '0.3';
        themeToggleBtn.style.cursor = 'not-allowed';
        themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        themeToggleBtn.title = 'Dark Mode (Always On)';
    }

    console.log('Dark navy blue theme active (permanent)');

    // --- Mobile Nav Toggle --- //
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function() {
            document.getElementById('mobileNav').classList.toggle('active');
        });
    }
});
