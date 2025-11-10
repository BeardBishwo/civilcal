<!-- Premium Theme Scripts -->
    <script src="/themes/premium/assets/js/premium-theme.js"></script>
    
    <!-- Google Analytics (Optional) -->
    <?php if (isset($googleAnalyticsId) && !empty($googleAnalyticsId)): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $googleAnalyticsId; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $googleAnalyticsId; ?>');
        </script>
    <?php endif; ?>
    
    <!-- Custom Page Scripts -->
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline Scripts for Dynamic Content -->
    <script>
        // Initialize page-specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            initializeTooltips();
            
            // Initialize form enhancements
            initializeForms();
            
            // Initialize accessibility features
            initializeAccessibility();
            
            // Initialize analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'page_view', {
                    page_title: document.title,
                    page_location: window.location.href
                });
            }
        });
        
        function initializeTooltips() {
            const tooltipElements = document.querySelectorAll('[data-tooltip]');
            tooltipElements.forEach(element => {
                element.addEventListener('mouseenter', showTooltip);
                element.addEventListener('mouseleave', hideTooltip);
            });
        }
        
        function showTooltip(event) {
            const text = event.target.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = text;
            document.body.appendChild(tooltip);
            
            const rect = event.target.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        }
        
        function hideTooltip() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        }
        
        function initializeForms() {
            // Auto-save form data
            const forms = document.querySelectorAll('form[data-auto-save]');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        localStorage.setItem('form_' + form.id + '_' + input.name, input.value);
                    });
                    
                    // Load saved data
                    const savedValue = localStorage.getItem('form_' + form.id + '_' + input.name);
                    if (savedValue) {
                        input.value = savedValue;
                    }
                });
            });
        }
        
        function initializeAccessibility() {
            // Skip to main content functionality
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                skipLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const mainContent = document.querySelector('main');
                    if (mainContent) {
                        mainContent.focus();
                        mainContent.scrollIntoView();
                    }
                });
            }
            
            // Keyboard navigation for dropdowns
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close all open dropdowns
                    const openDropdowns = document.querySelectorAll('.show');
                    openDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                }
            });
        }
        
        // Global error handling
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            
            // Send error to analytics (if configured)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'exception', {
                    description: e.error.toString(),
                    fatal: false
                });
            }
        });
        
        // Performance monitoring
        window.addEventListener('load', function() {
            setTimeout(function() {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('Page Load Time:', perfData.loadEventEnd - perfData.loadEventStart + 'ms');
                }
            }, 0);
        });
    </script>
    
    <!-- Service Worker Registration (Optional) -->
    <?php if (isset($enableServiceWorker) && $enableServiceWorker): ?>
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => console.log('SW registered'))
                    .catch(error => console.log('SW registration failed'));
            }
        </script>
    <?php endif; ?>
    
    <!-- Additional Footer Content -->
    <?php if (isset($footerContent)): ?>
        <?php echo $footerContent; ?>
    <?php endif; ?>
    
</body>
</html>
