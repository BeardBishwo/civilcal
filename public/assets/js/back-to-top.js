// Simple back-to-top button functionality
(function() {
    'use strict';

    console.log('Back to top script starting...');

    let backToTopBtn;

    function init() {
        backToTopBtn = document.getElementById('back-to-top-btn');

        if (!backToTopBtn) {
            backToTopBtn = createButton();
        }

        setupEventListeners();
        checkScroll();
    }

    function createButton() {
        const btn = document.createElement('div');
        btn.id = 'back-to-top-btn';
        btn.className = 'back-to-top-btn';
        btn.setAttribute('role', 'button');
        btn.setAttribute('aria-label', 'Back to top');
        btn.setAttribute('title', 'Back to top');
        btn.setAttribute('tabindex', '0');

        // Exactly match test file's HTML structure
        btn.innerHTML = '<i class="fas fa-arrow-up" aria-hidden="true"></i>';

        document.body.appendChild(btn);
        return btn;
    }

    function setupEventListeners() {
        window.addEventListener('scroll', checkScroll);
        backToTopBtn.addEventListener('click', scrollToTop);
    }

    function checkScroll() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    }

    function scrollToTop() {
        const duration = 1000; // Duration in milliseconds
        const startPosition = window.pageYOffset;
        const startTime = performance.now();

        // Easing function for smooth acceleration and deceleration
        function easeInOutCubic(t) {
            return t < 0.5 ?
                4 * t * t * t :
                1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function scroll() {
            const currentTime = performance.now();
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Apply easing to the progress
            const easedProgress = easeInOutCubic(progress);

            // Calculate new position
            const currentPosition = startPosition * (1 - easedProgress);

            window.scrollTo(0, currentPosition);

            if (progress < 1) {
                // Continue animation
                requestAnimationFrame(scroll);
            }
        }

        // Start the animation
        requestAnimationFrame(scroll);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    console.log('Back to top script loaded successfully');
})();