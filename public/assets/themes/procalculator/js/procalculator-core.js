/**
 * ProCalculator Premium Theme - Core JavaScript
 * Ultra-Premium $100,000 Quality Functionality
 */

class ProCalculatorCore {
    constructor() {
        this.theme = 'procalculator';
        this.version = '1.0.0';
        this.isInitialized = false;
        this.modules = new Map();
        this.eventListeners = new Map();
        this.animations = new Map();

        this.init();
    }

    /**
     * Initialize the core system
     */
    async init() {
        console.log('🚀 ProCalculator Premium Theme Loading...');

        try {
            // Load core modules
            await this.loadModules();

            // Initialize animations system
            this.initAnimations();

            // Initialize glassmorphism effects
            this.initGlassmorphism();

            // Setup global event handlers
            this.setupGlobalEvents();

            // Initialize responsive features
            this.initResponsive();

            // Initialize accessibility
            this.initAccessibility();
            
            // Initialize user dropdown and dark mode toggle via glassmorphism module
            const glassModule = this.modules.get('glassmorphism');
            if (glassModule) {
                if (typeof glassModule.initUserDropdown === 'function') {
                    glassModule.initUserDropdown();
                }
                if (typeof glassModule.initDarkModeToggle === 'function') {
                    glassModule.initDarkModeToggle();
                }
            }

            this.isInitialized = true;
            console.log('✅ ProCalculator Premium Theme Loaded Successfully');

            // Dispatch ready event
            this.dispatchEvent('pc:ready');

        } catch (error) {
            console.error('❌ ProCalculator Premium Theme Failed to Load:', error);
        }
    }

    /**
     * Load all JavaScript modules
     */
    async loadModules() {
        const modules = [
            'auth-enhanced',
            'animations',
            'dashboard',
            'glassmorphism'
        ];

        for (const module of modules) {
            try {
                await this.loadModule(module);
            } catch (error) {
                console.warn(`⚠️ Failed to load module: ${module}`, error);
            }
        }
    }

    /**
     * Load individual module
     */
    async loadModule(moduleName) {
        // This would typically load from external files
        // For now, we'll implement the core functionality inline
        const module = this.createModule(moduleName);
        if (module) {
            this.modules.set(moduleName, module);
            await module.init();
        }
    }

    /**
     * Create module instance
     */
    createModule(moduleName) {
        const modules = {
            'auth-enhanced': ProCalculatorAuth,
            'animations': ProCalculatorAnimations,
            'dashboard': ProCalculatorDashboard,
            'glassmorphism': ProCalculatorGlassmorphism
        };

        const ModuleClass = modules[moduleName];
        if (ModuleClass) {
            return new ModuleClass(this);
        }
        return null;
    }

    /**
     * Initialize animations system
     */
    initAnimations() {
        // Intersection Observer for scroll animations
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('pc-animate-in');
                        this.animateElement(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });

            // Observe all animatable elements
            document.querySelectorAll('.pc-card, .pc-btn, .pc-heading').forEach(el => {
                this.observer.observe(el);
            });
        }
    }

    /**
     * Initialize glassmorphism effects
     */
    initGlassmorphism() {
        // Add glassmorphism class to eligible elements
        document.querySelectorAll('.pc-card').forEach(card => {
            card.classList.add('pc-glassmorphism');
        });

        // Add parallax effect to background elements
        this.initParallax();
    }

    /**
     * Initialize parallax scrolling effects
     */
    initParallax() {
        if (!this.isMobile()) {
            window.addEventListener('scroll', this.throttle(() => {
                const scrolled = window.pageYOffset;
                const parallax = document.querySelectorAll('.pc-parallax');

                parallax.forEach(element => {
                    const speed = element.dataset.speed || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translate3d(0, ${yPos}px, 0)`;
                });
            }, 16));
        }
    }

    /**
     * Setup global event handlers
     */
    setupGlobalEvents() {
        // Smooth scrolling for anchor links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                e.preventDefault();
                this.smoothScroll(link.getAttribute('href'));
            }
        });

        // Form enhancements
        document.addEventListener('submit', this.handleFormSubmit.bind(this));

        // Dynamic content loading
        this.initLazyLoading();

        // Premium interactions
        this.initPremiumInteractions();
    }

    /**
     * Initialize responsive features
     */
    initResponsive() {
        // Mobile menu toggle
        const mobileMenuToggle = document.querySelector('.pc-mobile-menu-toggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                this.toggleMobileMenu();
            });
        }

        // Window resize handling
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));
    }

    /**
     * Initialize accessibility features
     */
    initAccessibility() {
        // Focus management
        this.initFocusManagement();

        // Keyboard navigation
        this.initKeyboardNavigation();
    }

    /**
     * Animate individual element
     */
    animateElement(element, animation = 'fadeInUp') {
        element.classList.add(`pc-animate-${animation}`);

        setTimeout(() => {
            element.classList.remove(`pc-animate-${animation}`);
        }, 1000);
    }

    /**
     * Smooth scroll to element
     */
    smoothScroll(target) {
        const element = document.querySelector(target);
        if (element) {
            const offsetTop = element.offsetTop - 100;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }

    /**
     * Handle form submissions
     */
    async handleFormSubmit(e) {
        const form = e.target;
        
        // Skip auth forms - they have their own handlers
        if (form.id === 'loginForm' || form.id === 'registerForm' || form.id === 'forgotForm') {
            return;
        }
        
        if (form.classList.contains('pc-premium-form')) {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');

            // Show loading state
            this.setButtonLoading(submitBtn, true);

            try {
                // Here you would typically make an API call
                const response = await this.submitForm(formData);
                this.handleFormSuccess(response, form);
            } catch (error) {
                this.handleFormError(error, form);
            } finally {
                this.setButtonLoading(submitBtn, false);
            }
        }
    }

    /**
     * Submit form data
     */
    async submitForm(formData) {
        // Mock API call - replace with actual implementation
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({ success: true, message: 'Form submitted successfully' });
            }, 1000);
        });
    }

    /**
     * Handle form success
     */
    handleFormSuccess(response, form) {
        this.showNotification('success', response.message);
        form.reset();
        this.dispatchEvent('pc:form:success', { form, response });
    }

    /**
     * Handle form error
     */
    handleFormError(error, form) {
        this.showNotification('error', error.message || 'An error occurred');
        this.dispatchEvent('pc:form:error', { form, error });
    }

    /**
     * Show notification
     */
    showNotification(type, message) {
        const notification = this.createNotification(type, message);
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('pc-notification-show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('pc-notification-show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    /**
     * Create notification element
     */
    createNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `pc-notification pc-notification-${type}`;
        notification.innerHTML = `
            <div class="pc-notification-content">
                <span class="pc-notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="pc-notification-message">${message}</span>
                <button class="pc-notification-close">&times;</button>
            </div>
        `;

        notification.querySelector('.pc-notification-close').addEventListener('click', () => {
            notification.classList.remove('pc-notification-show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        });

        return notification;
    }

    /**
     * Get notification icon
     */
    getNotificationIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || 'ℹ';
    }

    /**
     * Set button loading state
     */
    setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            button.classList.add('pc-loading');
            button.innerHTML = `
                <span class="pc-spinner"></span>
                Loading...
            `;
        } else {
            button.disabled = false;
            button.classList.remove('pc-loading');
            // Restore original button text (you might want to store this)
        }
    }

    /**
     * Initialize lazy loading
     */
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('pc-lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img.pc-lazy').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Initialize premium interactions
     */
    initPremiumInteractions() {
        // Floating elements
        document.querySelectorAll('.pc-float').forEach(element => {
            this.addFloatingEffect(element);
        });

        // Magnetic buttons
        document.querySelectorAll('.pc-magnetic').forEach(button => {
            this.addMagneticEffect(button);
        });
    }

    /**
     * Add floating effect
     */
    addFloatingEffect(element) {
        let floatId;
        const float = () => {
            const time = Date.now() * 0.002;
            const y = Math.sin(time) * 10;
            element.style.transform = `translateY(${y}px)`;
            floatId = requestAnimationFrame(float);
        };
        floatId = requestAnimationFrame(float);

        element.addEventListener('mouseenter', () => {
            cancelAnimationFrame(floatId);
        });

        element.addEventListener('mouseleave', () => {
            floatId = requestAnimationFrame(float);
        });
    }

    /**
     * Add magnetic effect
     */
    addMagneticEffect(element) {
        element.addEventListener('mousemove', (e) => {
            const rect = element.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            element.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translate(0px, 0px)';
        });
    }

    /**
     * Check if device is mobile
     */
    isMobile() {
        return window.innerWidth <= 768;
    }

    /**
     * Toggle mobile menu
     */
    toggleMobileMenu() {
        const menu = document.querySelector('.pc-mobile-menu');
        const body = document.body;

        if (menu) {
            menu.classList.toggle('pc-menu-open');
            body.classList.toggle('pc-menu-open');
        }
    }

    /**
     * Handle window resize
     */
    handleResize() {
        // Close mobile menu on resize
        if (window.innerWidth > 768) {
            const menu = document.querySelector('.pc-mobile-menu');
            const body = document.body;

            if (menu && menu.classList.contains('pc-menu-open')) {
                menu.classList.remove('pc-menu-open');
                body.classList.remove('pc-menu-open');
            }
        }
    }

    /**
     * Initialize focus management
     */
    initFocusManagement() {
        // Trap focus in modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    /**
     * Initialize keyboard navigation
     */
    initKeyboardNavigation() {
        // Arrow key navigation for menus
        document.addEventListener('keydown', (e) => {
            if (e.target.matches('.pc-dropdown-trigger')) {
                this.handleDropdownNavigation(e);
            }
        });
    }

    /**
     * Handle dropdown navigation
     */
    handleDropdownNavigation(e) {
        const dropdown = e.target.nextElementSibling;
        if (!dropdown) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                dropdown.querySelector('a, button').focus();
                break;
            case 'Escape':
                dropdown.classList.remove('pc-dropdown-open');
                e.target.focus();
                break;
        }
    }

    /**
     * Close all modals
     */
    closeAllModals() {
        document.querySelectorAll('.pc-modal.pc-modal-open').forEach(modal => {
            modal.classList.remove('pc-modal-open');
        });
    }

    /**
     * Utility functions
     */
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Event system
     */
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    off(event, callback) {
        if (this.eventListeners.has(event)) {
            const callbacks = this.eventListeners.get(event);
            const index = callbacks.indexOf(callback);
            if (index > -1) {
                callbacks.splice(index, 1);
            }
        }
    }

    dispatchEvent(event, data = {}) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                callback(data);
            });
        }
        // Also dispatch native DOM event
        document.dispatchEvent(new CustomEvent(`pc:${event}`, { detail: data }));
    }
}

// Module classes will be defined below
class ProCalculatorAuth {
    constructor(core) {
        this.core = core;
        this.isLoggedIn = false;
        this.user = null;
    }

    async init() {
        this.checkAuthStatus();
        this.setupAuthEvents();
    }

    checkAuthStatus() {
        // Check if user is logged in
        // This would typically check cookies, localStorage, or make an API call
    }

    setupAuthEvents() {
        // Setup authentication-related event listeners
    }

    async login(credentials) {
        // Login implementation
    }

    async register(userData) {
        // Registration implementation
    }

    async logout() {
        // Logout implementation
    }
}

class ProCalculatorAnimations {
    constructor(core) {
        this.core = core;
    }

    async init() {
        this.setupScrollAnimations();
        this.setupHoverAnimations();
    }

    setupScrollAnimations() {
        // Scroll-based animations
    }

    setupHoverAnimations() {
        // Hover-based animations
    }
}

class ProCalculatorDashboard {
    constructor(core) {
        this.core = core;
    }

    async init() {
        this.initDashboard();
    }

    initDashboard() {
        // Dashboard initialization
    }
}

class ProCalculatorGlassmorphism {
    constructor(core) {
        this.core = core;
    }

    async init() {
        this.initGlassmorphismEffects();
    }

    initGlassmorphismEffects() {
        // Glassmorphism effects
    }
    
    /**
     * Initialize user dropdown
     */
    initUserDropdown() {
        const userToggle = document.querySelector('.user-profile-toggle');
        const userDropdown = document.querySelector('.user-dropdown-menu');
        
        if (userToggle && userDropdown) {
            // Toggle dropdown on click
            userToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const isExpanded = userToggle.getAttribute('aria-expanded') === 'true';
                userToggle.setAttribute('aria-expanded', !isExpanded);
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                    userToggle.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Prevent dropdown from closing when clicking inside
            userDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    }
    
    /**
     * Initialize dark mode toggle
     */
    initDarkModeToggle() {
        console.log('🌓 Initializing dark mode toggle...');
        
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeCheckbox = document.getElementById('darkModeCheckbox');
        
        if (!darkModeToggle || !darkModeCheckbox) {
            console.warn('⚠️ Dark mode elements not found:', {
                toggle: !!darkModeToggle,
                checkbox: !!darkModeCheckbox
            });
            return;
        }
        
        console.log('✅ Dark mode elements found');
        
        // Load saved preference (default to dark mode for premium look)
        const savedMode = localStorage.getItem('darkMode');
        const isDarkMode = savedMode === null ? true : savedMode === 'true';
        
        console.log('💾 Saved mode:', savedMode, 'Using dark mode:', isDarkMode);
        
        // Apply initial state immediately
        darkModeCheckbox.checked = isDarkMode;
        this.applyDarkMode(isDarkMode, true);
        this.updateDarkModeUI(isDarkMode, darkModeToggle);
        
        // Toggle dark mode on button click
        const toggleHandler = (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🔄 Toggle clicked!');
            
            // Toggle checkbox
            darkModeCheckbox.checked = !darkModeCheckbox.checked;
            const isDark = darkModeCheckbox.checked;
            
            console.log('🌓 Switching to:', isDark ? 'dark' : 'light');
            
            // Apply dark mode with animation
            this.applyDarkMode(isDark, false);
            this.updateDarkModeUI(isDark, darkModeToggle);
            
            // Save preference
            localStorage.setItem('darkMode', isDark.toString());
            console.log('💾 Saved to localStorage:', isDark);
            
            // Show toast notification
            this.showModeChangeNotification(isDark);
            
            // Dispatch event for other components
            this.dispatchEvent('pc:darkmode:changed', { isDark });
        };
        
        darkModeToggle.addEventListener('click', toggleHandler);
        
        // Also allow clicking the checkbox directly
        darkModeCheckbox.addEventListener('change', (e) => {
            e.stopPropagation();
            const isDark = darkModeCheckbox.checked;
            
            console.log('☑️ Checkbox changed:', isDark);
            
            this.applyDarkMode(isDark, false);
            this.updateDarkModeUI(isDark, darkModeToggle);
            localStorage.setItem('darkMode', isDark.toString());
            this.showModeChangeNotification(isDark);
            this.dispatchEvent('pc:darkmode:changed', { isDark });
        });
        
        console.log('✅ Dark mode toggle initialized successfully');
    }
    
    /**
     * Apply dark mode to the page
     */
    applyDarkMode(isDark, isInitial = false) {
        const html = document.documentElement;
        const body = document.body;
        
        console.log('🎨 Applying mode:', isDark ? 'dark' : 'light', 'Initial:', isInitial);
        
        if (isDark) {
            html.classList.add('dark-mode');
            body.classList.add('dark-mode');
            console.log('🌙 Dark mode classes added');
        } else {
            html.classList.remove('dark-mode');
            body.classList.remove('dark-mode');
            console.log('☀️ Light mode classes removed');
        }
        
        // Log current classes for debugging
        console.log('📋 HTML classes:', html.className);
        console.log('📋 Body classes:', body.className);
        
        // Force a repaint
        void html.offsetHeight;
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark } }));
    }
    
    /**
     * Update dark mode UI elements
     */
    updateDarkModeUI(isDark, toggleBtn) {
        if (!toggleBtn) return;
        
        const buttonText = toggleBtn.querySelector('span');
        const buttonIcon = toggleBtn.querySelector('i');
        
        if (buttonText) {
            buttonText.textContent = isDark ? 'Dark Mode' : 'Light Mode';
            console.log('📝 Updated text:', buttonText.textContent);
        }
        
        if (buttonIcon) {
            buttonIcon.className = isDark ? 'fas fa-moon' : 'fas fa-sun';
            console.log('🎨 Updated icon:', buttonIcon.className);
        }
    }
    
    /**
     * Show mode change notification
     */
    showModeChangeNotification(isDark) {
        // Check if showToast is available
        if (typeof window.showToast === 'function') {
            const mode = isDark ? 'Dark' : 'Light';
            const icon = isDark ? '🌙' : '☀️';
            window.showToast('info', `${icon} ${mode} Mode`, `Switched to ${mode.toLowerCase()} mode`);
        } else {
            console.log('ℹ️ Toast function not available');
        }
    }
}

// Initialize ProCalculator when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.ProCalculator = new ProCalculatorCore();
    });
} else {
    window.ProCalculator = new ProCalculatorCore();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProCalculatorCore;
}