/**
 * Bishwo Calculator - Theme Toggle System
 * Dark/Light theme switching with localStorage persistence
 */

class ThemeToggle {
    constructor() {
        // State
        this.state = {
            currentTheme: 'light',
            isInitialized: false,
            isTransitioning: false
        };

        // DOM elements cache
        this.elements = {
            toggleButton: null,
            themeIcon: null,
            htmlElement: null
        };

        // Configuration
        this.config = {
            storageKey: 'bishwo_admin_theme',
            defaultTheme: 'light',
            transitionDuration: 300,
            debugMode: true
        };

        // Initialize the system
        this.init();
    }

    /**
     * Initialize the theme toggle system
     */
    init() {
        if (this.state.isInitialized) {
            this.log('Theme toggle system already initialized');
            return;
        }

        this.log('🌓 Initializing theme toggle system...');

        // Cache DOM elements
        this.cacheElements();

        // Check if we have required elements
        if (!this.elements.toggleButton) {
            this.error('Theme toggle button not found. System will not initialize.');
            return;
        }

        // Set up event listeners when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }

        this.state.isInitialized = true;
        this.log('✅ Theme toggle system initialized successfully');
    }

    /**
     * Cache DOM elements for better performance
     */
    cacheElements() {
        this.elements = {
            toggleButton: document.getElementById('themeToggle'),
            themeIcon: document.getElementById('themeIcon'),
            htmlElement: document.documentElement
        };

        this.log('📋 Cached DOM elements:', {
            toggleButton: !!this.elements.toggleButton,
            themeIcon: !!this.elements.themeIcon,
            htmlElement: !!this.elements.htmlElement
        });
    }

    /**
     * Set up event listeners and load initial theme
     */
    setup() {
        this.log('🔧 Setting up theme toggle system...');

        // Set up click handler for theme button
        this.setupClickHandler();

        // Load saved theme or use default
        this.loadTheme();

        this.log('✅ Theme toggle system setup complete');
    }

    /**
     * Set up theme toggle button click handler
     */
    setupClickHandler() {
        if (!this.elements.toggleButton) return;

        this.elements.toggleButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleTheme();
        });

        this.log('✅ Theme toggle button click handler attached');
    }

    /**
     * Toggle between dark and light themes
     */
    toggleTheme() {
        if (this.state.isTransitioning) {
            this.log('Theme transition already in progress, skipping...');
            return;
        }

        this.state.isTransitioning = true;
        const newTheme = this.state.currentTheme === 'light' ? 'dark' : 'light';

        this.log(`🌗 Toggling theme from ${this.state.currentTheme} to ${newTheme}...`);

        // Update UI immediately
        this.updateThemeUI(newTheme);

        // Save to localStorage
        this.saveTheme(newTheme);

        // Update icon
        this.updateThemeIcon(newTheme);

        // Show visual feedback
        this.showThemeChangeFeedback(newTheme);

        setTimeout(() => {
            this.state.isTransitioning = false;
        }, this.config.transitionDuration);
    }

    /**
     * Load theme from localStorage or use default
     */
    loadTheme() {
        try {
            const savedTheme = localStorage.getItem(this.config.storageKey);

            if (savedTheme && ['light', 'dark'].includes(savedTheme)) {
                this.state.currentTheme = savedTheme;
                this.log(`📦 Loaded saved theme: ${savedTheme}`);
            } else {
                this.state.currentTheme = this.config.defaultTheme;
                this.log(`📦 Using default theme: ${this.config.defaultTheme}`);
            }

            // Apply the loaded theme
            this.updateThemeUI(this.state.currentTheme);
            this.updateThemeIcon(this.state.currentTheme);

        } catch (error) {
            this.error('Failed to load theme from localStorage:', error);
            // Fallback to default theme
            this.state.currentTheme = this.config.defaultTheme;
            this.updateThemeUI(this.state.currentTheme);
            this.updateThemeIcon(this.state.currentTheme);
        }
    }

    /**
     * Save theme to localStorage
     */
    saveTheme(theme) {
        try {
            localStorage.setItem(this.config.storageKey, theme);
            this.state.currentTheme = theme;
            this.log(`💾 Saved theme to localStorage: ${theme}`);
        } catch (error) {
            this.error('Failed to save theme to localStorage:', error);
        }
    }

    /**
     * Update theme UI by applying appropriate CSS classes
     */
    updateThemeUI(theme) {
        // Remove both classes first
        this.elements.htmlElement.classList.remove('light-theme', 'dark-theme');

        // Add the appropriate class
        this.elements.htmlElement.classList.add(`${theme}-theme`);

        this.log(`🎨 Applied ${theme} theme to HTML element`);
    }

    /**
     * Update theme icon based on current theme
     */
    updateThemeIcon(theme) {
        if (!this.elements.themeIcon) return;

        const iconClass = theme === 'dark' ? 'fa-sun' : 'fa-moon';
        this.elements.themeIcon.className = `fas ${iconClass}`;

        this.log(`🌞 Updated theme icon to: ${iconClass}`);
    }

    /**
     * Show visual feedback when theme changes
     */
    showThemeChangeFeedback(theme) {
        const message = theme === 'dark' ? 'Dark mode enabled' : 'Light mode enabled';
        const toast = document.createElement('div');
        toast.className = 'theme-feedback-toast';
        toast.textContent = message;

        // Add toast to body
        document.body.appendChild(toast);

        // Remove after animation
        setTimeout(() => {
            toast.remove();
        }, 2000);

        this.log(`📢 Showing theme change feedback: ${message}`);
    }

    /**
     * Log messages with debug info
     */
    log(...args) {
        if (this.config.debugMode) {
            console.log('🌓 ThemeToggle:', ...args);
        }
    }

    /**
     * Log error messages
     */
    error(...args) {
        console.error('❌ ThemeToggle Error:', ...args);
    }
}

// Initialize the theme toggle system
const themeToggle = new ThemeToggle();

// Make it globally available for debugging and external access
window.themeToggle = themeToggle;

// Export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeToggle;
}