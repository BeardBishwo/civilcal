/* 
 * Premium Theme JavaScript
 * 
 * Core JavaScript functionality for the Premium Calculator Theme
 * 
 * @package PremiumTheme
 * @version 1.0.0
 * @author Bishwo Team
 */

/**
 * Main Premium Theme Class
 */
class PremiumTheme {
    constructor() {
        this.settings = window.premiumTheme ? .settings || {};
        this.isDarkMode = this.settings.dark_mode_enabled || false;
        this.animationSpeed = this.settings.animation_speed || 'medium';
        this.calculatorSkin = this.settings.calculator_skin || 'premium-dark';
        this.animationsEnabled = this.settings.show_animations !== false;

        this.init();
    }

    /**
     * Initialize theme
     */
    init() {
        this.initTheme();
        this.initDarkMode();
        this.initAnimations();
        this.initCalculatorSkins();
        this.initThemeCustomizer();
        this.initThemeHooks();

        // Trigger theme ready event
        document.dispatchEvent(new CustomEvent('premiumTheme:ready'));
    }

    /**
     * Initialize theme base functionality
     */
    initTheme() {
        // Add theme classes to body
        document.body.classList.add('premium-theme');

        if (this.animationsEnabled) {
            document.body.classList.add('premium-animations');
        }

        if (this.isDarkMode) {
            document.body.classList.add('premium-dark-mode');
        }

        // Add calculator skin class
        document.body.classList.add(`calculator-skin-${this.calculatorSkin}`);

        // Add animation speed class
        document.body.classList.add(`animation-${this.animationSpeed}`);

        // Add typography style class
        const typographyStyle = this.settings.typography_style || 'modern';
        document.body.classList.add(`typography-${typographyStyle}`);
    }

    /**
     * Initialize dark mode functionality
     */
    initDarkMode() {
        if (this.settings.enable_premium_features && this.settings.dark_mode_enabled) {
            this.applyDarkMode(true);
        }

        // Listen for dark mode changes
        document.addEventListener('premiumTheme:toggleDarkMode', (e) => {
            this.toggleDarkMode();
        });
    }

    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
        this.isDarkMode = !this.isDarkMode;

        if (this.isDarkMode) {
            this.applyDarkMode(true);
        } else {
            this.applyDarkMode(false);
        }

        // Save preference
        this.saveSetting('dark_mode_enabled', this.isDarkMode);

        // Trigger event
        document.dispatchEvent(new CustomEvent('premiumTheme:darkModeChanged', {
            detail: { isDark: this.isDarkMode }
        }));
    }

    /**
     * Apply dark mode styles
     */
    applyDarkMode(enabled) {
        if (enabled) {
            document.body.classList.add('premium-dark-mode');
        } else {
            document.body.classList.remove('premium-dark-mode');
        }
    }

    /**
     * Initialize animations
     */
    initAnimations() {
        if (!this.animationsEnabled) {
            return;
        }

        // Add animation classes to elements
        this.addScrollAnimations();
        this.addHoverAnimations();
        this.addFormAnimations();
        this.addButtonAnimations();
    }

    /**
     * Add scroll-triggered animations
     */
    addScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe elements with animation classes
        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Add hover animations
     */
    addHoverAnimations() {
        document.addEventListener('mouseenter', (e) => {
            if (e.target.classList.contains('premium-hover-effect')) {
                e.target.classList.add('hover-effect');
            }
        }, true);

        document.addEventListener('mouseleave', (e) => {
            if (e.target.classList.contains('premium-hover-effect')) {
                e.target.classList.remove('hover-effect');
            }
        }, true);
    }

    /**
     * Add form animations
     */
    addFormAnimations() {
        const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');

        inputs.forEach(input => {
            // Focus animations
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                if (input.value === '') {
                    input.parentElement.classList.remove('focused');
                }
            });

            // Check if already has value on load
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
    }

    /**
     * Add button animations
     */
    addButtonAnimations() {
        const buttons = document.querySelectorAll('.btn-calc, .btn-primary');

        buttons.forEach(button => {
            // Ripple effect
            button.addEventListener('click', (e) => {
                this.createRippleEffect(e, button);
            });

            // Loading animation for submit buttons
            button.addEventListener('click', (e) => {
                if (button.type === 'submit') {
                    this.showButtonLoading(button);
                }
            });
        });
    }

    /**
     * Create ripple effect
     */
    createRippleEffect(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    /**
     * Show button loading state
     */
    showButtonLoading(button) {
        const originalText = button.textContent;
        button.disabled = true;
        button.innerHTML = '<span class="loading"></span> Calculating...';

        setTimeout(() => {
            button.disabled = false;
            button.textContent = originalText;
        }, 2000);
    }

    /**
     * Initialize calculator skins
     */
    initCalculatorSkins() {
        // Apply calculator skin
        this.applyCalculatorSkin(this.calculatorSkin);

        // Listen for skin changes
        document.addEventListener('premiumTheme:changeSkin', (e) => {
            this.changeCalculatorSkin(e.detail.skin);
        });
    }

    /**
     * Change calculator skin
     */
    changeCalculatorSkin(skin) {
        // Remove old skin class
        document.body.classList.forEach(cls => {
            if (cls.startsWith('calculator-skin-')) {
                document.body.classList.remove(cls);
            }
        });

        // Add new skin class
        document.body.classList.add(`calculator-skin-${skin}`);

        // Update internal state
        this.calculatorSkin = skin;

        // Save preference
        this.saveSetting('calculator_skin', skin);

        // Trigger event
        document.dispatchEvent(new CustomEvent('premiumTheme:skinChanged', {
            detail: { skin }
        }));
    }

    /**
     * Apply calculator skin
     */
    applyCalculatorSkin(skin) {
        // This will be called by changeCalculatorSkin
        // Additional skin-specific setup can be added here
    }

    /**
     * Initialize theme customizer
     */
    initThemeCustomizer() {
        if (!this.settings.enable_premium_features) {
            return;
        }

        this.createCustomizerPanel();
        this.initCustomizerEvents();
    }

    /**
     * Create customizer panel
     */
    createCustomizerPanel() {
        // Create customizer trigger button
        const customizerBtn = document.createElement('button');
        customizerBtn.id = 'premium-theme-customizer';
        customizerBtn.className = 'premium-customizer-btn';
        customizerBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            Customize
        `;

        document.body.appendChild(customizerBtn);
    }

    /**
     * Initialize customizer events
     */
    initCustomizerEvents() {
        const customizerBtn = document.getElementById('premium-theme-customizer');

        if (customizerBtn) {
            customizerBtn.addEventListener('click', () => {
                this.openCustomizer();
            });
        }
    }

    /**
     * Open customizer panel
     */
    openCustomizer() {
        // Create customizer panel
        const panel = document.createElement('div');
        panel.id = 'premium-customizer-panel';
        panel.className = 'premium-customizer-panel';
        panel.innerHTML = this.getCustomizerHTML();

        document.body.appendChild(panel);

        // Add event listeners
        this.initCustomizerPanelEvents(panel);
    }

    /**
     * Get customizer panel HTML
     */
    getCustomizerHTML() {
        return `
            <div class="customizer-header">
                <h3>Theme Customizer</h3>
                <button class="customizer-close">&times;</button>
            </div>
            <div class="customizer-content">
                <div class="customizer-section">
                    <h4>Calculator Skin</h4>
                    <div class="skin-options">
                        <button class="skin-option" data-skin="premium-dark">
                            <div class="skin-preview dark"></div>
                            <span>Premium Dark</span>
                        </button>
                        <button class="skin-option" data-skin="premium-light">
                            <div class="skin-preview light"></div>
                            <span>Premium Light</span>
                        </button>
                        <button class="skin-option" data-skin="professional-blue">
                            <div class="skin-preview blue"></div>
                            <span>Professional Blue</span>
                        </button>
                        <button class="skin-option" data-skin="modern-gray">
                            <div class="skin-preview gray"></div>
                            <span>Modern Gray</span>
                        </button>
                    </div>
                </div>
                <div class="customizer-section">
                    <h4>Settings</h4>
                    <label class="customizer-toggle">
                        <input type="checkbox" id="dark-mode-toggle" ${this.isDarkMode ? 'checked' : ''}>
                        <span>Dark Mode</span>
                    </label>
                    <label class="customizer-toggle">
                        <input type="checkbox" id="animations-toggle" ${this.animationsEnabled ? 'checked' : ''}>
                        <span>Enable Animations</span>
                    </label>
                </div>
            </div>
        `;
    }

    /**
     * Initialize customizer panel events
     */
    initCustomizerPanelEvents(panel) {
        // Close button
        panel.querySelector('.customizer-close').addEventListener('click', () => {
            panel.remove();
        });

        // Skin options
        panel.querySelectorAll('.skin-option').forEach(option => {
            option.addEventListener('click', () => {
                const skin = option.dataset.skin;
                this.changeCalculatorSkin(skin);
            });
        });

        // Settings toggles
        panel.querySelector('#dark-mode-toggle').addEventListener('change', (e) => {
            this.isDarkMode = e.target.checked;
            this.applyDarkMode(this.isDarkMode);
            this.saveSetting('dark_mode_enabled', this.isDarkMode);
        });

        panel.querySelector('#animations-toggle').addEventListener('change', (e) => {
            this.animationsEnabled = e.target.checked;
            if (this.animationsEnabled) {
                document.body.classList.add('premium-animations');
            } else {
                document.body.classList.remove('premium-animations');
            }
            this.saveSetting('show_animations', this.animationsEnabled);
        });

        // Close on outside click
        panel.addEventListener('click', (e) => {
            if (e.target === panel) {
                panel.remove();
            }
        });
    }

    /**
     * Initialize theme hooks
     */
    initThemeHooks() {
        // Listen for theme events
        document.addEventListener('premiumTheme:update', (e) => {
            this.updateSettings(e.detail);
        });
    }

    /**
     * Update theme settings
     */
    updateSettings(newSettings) {
        this.settings = {...this.settings, ...newSettings };

        // Update internal properties
        this.isDarkMode = this.settings.dark_mode_enabled;
        this.animationSpeed = this.settings.animation_speed;
        this.calculatorSkin = this.settings.calculator_skin;
        this.animationsEnabled = this.settings.show_animations;

        // Apply changes
        this.initTheme();
    }

    /**
     * Save setting
     */
    saveSetting(key, value) {
        // Send to server
        fetch('/api/theme/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ key, value })
        }).catch(err => console.log('Settings save failed:', err));
    }
}

/**
 * Global theme initialization function
 */
function premiumThemeInit() {
    // Initialize if not already done
    if (!window.premiumThemeInstance) {
        window.premiumThemeInstance = new PremiumTheme();
    }
}

/**
 * Dark mode toggle function (for external use)
 */
function togglePremiumDarkMode() {
    document.dispatchEvent(new CustomEvent('premiumTheme:toggleDarkMode'));
}

/**
 * Calculator skin change function (for external use)
 */
function changePremiumCalculatorSkin(skin) {
    document.dispatchEvent(new CustomEvent('premiumTheme:changeSkin', {
        detail: { skin }
    }));
}

// Export for external use
window.PremiumTheme = PremiumTheme;
window.premiumThemeInit = premiumThemeInit;
window.togglePremiumDarkMode = togglePremiumDarkMode;
window.changePremiumCalculatorSkin = changePremiumCalculatorSkin;

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', premiumThemeInit);
} else {
    premiumThemeInit();
}