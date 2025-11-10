document.addEventListener('DOMContentLoaded', function() {
    // --- Enhanced Theme Toggle --- //
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    let currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    function applyTheme(theme) {
        document.body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        currentTheme = theme;
        if (themeToggleBtn) {
            const icon = themeToggleBtn.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        // Add smooth transition
        document.body.style.transition = 'all 0.3s ease-in-out';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }

    // Apply initial theme
    applyTheme(currentTheme);

    // --- Enhanced Mobile Nav Toggle --- //
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function() {
            const mobileNav = document.getElementById('mobileNav');
            if (mobileNav) {
                mobileNav.classList.toggle('active');
                // Add ARIA attributes for accessibility
                const isActive = mobileNav.classList.contains('active');
                hamburgerBtn.setAttribute('aria-expanded', isActive);
            }
        });
    }

    // --- User Dropdown Menu --- //
    const userMenuTrigger = document.querySelector('.user-menu-trigger');
    if (userMenuTrigger) {
        const userDropdown = document.querySelector('.user-dropdown');
        if (userDropdown) {
            userMenuTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                userDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenuTrigger.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }
    }

    // --- Smooth Scrolling for Anchor Links --- //
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // --- Calculator Form Enhancement --- //
    const calculatorForms = document.querySelectorAll('.calculator-form');
    calculatorForms.forEach(form => {
        const submitBtn = form.querySelector('.btn-calculate');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                // Add loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculating...';
                this.disabled = true;

                // Remove loading state after 2 seconds (adjust as needed)
                setTimeout(() => {
                    this.innerHTML = 'Calculate';
                    this.disabled = false;
                }, 2000);
            });
        }
    });

    // --- Back to Top Button --- //
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // --- Category Cards Animation --- //
    const categoryCards = document.querySelectorAll('.category-card');
    if (categoryCards.length > 0) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        categoryCards.forEach((card, index) => {
            // Set initial styles
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;

            observer.observe(card);
        });
    }

    // --- Search Functionality --- //
    const searchInput = document.querySelector('.search-input');
    const searchSuggestions = document.querySelector('.search-suggestions');

    if (searchInput && searchSuggestions) {
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    // Simulate search suggestions (replace with actual API call)
                    const suggestions = [
                        'Civil Engineering Calculator',
                        'Electrical Load Calculator',
                        'HVAC Sizing Tool',
                        'Structural Beam Calculator',
                        'Plumbing Flow Calculator'
                    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));

                    displaySearchSuggestions(suggestions);
                }, 300);
            } else {
                hideSearchSuggestions();
            }
        });

        function displaySearchSuggestions(suggestions) {
            if (suggestions.length > 0) {
                searchSuggestions.innerHTML = suggestions
                    .map(suggestion => `<div class="suggestion-item">${suggestion}</div>`)
                    .join('');
                searchSuggestions.style.display = 'block';
            } else {
                hideSearchSuggestions();
            }
        }

        function hideSearchSuggestions() {
            searchSuggestions.style.display = 'none';
        }
    }

    // --- Form Validation Enhancement --- //
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');

                    // Remove error class when user starts typing
                    field.addEventListener('input', function() {
                        this.classList.remove('error');
                    });
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error field
                const firstError = this.querySelector('.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });

    // --- Keyboard Navigation Enhancement --- //
    document.addEventListener('keydown', function(e) {
        // ESC key to close dropdowns
        if (e.key === 'Escape') {
            const openDropdowns = document.querySelectorAll('.dropdown.show, .user-dropdown.show');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }

        // Enter key on buttons
        if (e.key === 'Enter' && e.target.classList.contains('btn')) {
            e.target.click();
        }
    });

    // --- Performance Monitoring --- //
    if ('performance' in window) {
        window.addEventListener('load', function() {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log(`Page load time: ${perfData.loadEventEnd - perfData.loadEventStart}ms`);
                }
            }, 0);
        });
    }

    // --- Error Handling --- //
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        // You could send this to an error tracking service
    });

    // --- Lazy Loading for Images --- //
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});