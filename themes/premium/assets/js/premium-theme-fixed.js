/**
 * Premium Theme JavaScript - Architecture Professional Edition
 * $1,500 Premium Value JavaScript Functionality
 * ===================================================
 */

(function() {
  'use strict';

  const PremiumTheme = {
    config: {
      animationSpeed: 'medium',
      darkMode: false,
      showAnimations: true,
      calculatorSkin: 'premium-dark',
      typographyStyle: 'modern'
    },

    init: function() {
      this.loadConfig();
      this.setupEventListeners();
      this.initializeComponents();
      console.log('🏗️ Premium Architecture Theme Initialized');
    },

    loadConfig: function() {
      const saved = localStorage.getItem('premiumThemeConfig');
      if (saved) {
        this.config = { ...this.config, ...JSON.parse(saved) };
      }
      this.applyConfig();
    },

    applyConfig: function() {
      const root = document.documentElement;
      if (this.config.darkMode) {
        root.setAttribute('data-theme', 'dark');
        document.body.classList.add('dark-mode');
      } else {
        root.setAttribute('data-theme', 'light');
        document.body.classList.remove('dark-mode');
      }
    },

    setupEventListeners: function() {
      window.addEventListener('scroll', this.handleScroll.bind(this));
      window.addEventListener('resize', this.handleResize.bind(this));
    },

    initializeComponents: function() {
      this.initializeCards();
      this.initializeButtons();
      this.setupThemeToggle();
    },

    setupThemeToggle: function() {
      const toggleButtons = document.querySelectorAll('.theme-toggle-btn');
      toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
          this.toggleDarkMode();
        });
      });
    },

    toggleDarkMode: function() {
      this.config.darkMode = !this.config.darkMode;
      this.applyConfig();
      localStorage.setItem('premiumThemeConfig', JSON.stringify(this.config));
    },

    handleScroll: function() {
      const header = document.querySelector('.premium-header');
      if (header) {
        if (window.scrollY > 100) {
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      }
    },

    handleResize: function() {
      // Handle resize events
    },

    initializeCards: function() {
      const cards = document.querySelectorAll('.card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-4px)';
        });
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
        });
      });
    },

    initializeButtons: function() {
      const buttons = document.querySelectorAll('.btn');
      buttons.forEach(button => {
        button.addEventListener('click', function(e) {
          // Add ripple effect
          const ripple = document.createElement('span');
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            width: ${size}px;
            height: ${size}px;
            left: ${e.clientX - rect.left - size / 2}px;
            top: ${e.clientY - rect.top - size / 2}px;
            animation: ripple 0.6s linear;
            pointer-events: none;
          `;
          this.style.position = 'relative';
          this.style.overflow = 'hidden';
          this.appendChild(ripple);
          setTimeout(() => ripple.remove(), 600);
        });
      });
    }
  };

  // Make globally available
  if (typeof window !== 'undefined') {
    window.PremiumTheme = PremiumTheme;
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => PremiumTheme.init());
    } else {
      PremiumTheme.init();
    }
    window.togglePremiumDarkMode = function() {
      PremiumTheme.toggleDarkMode();
    };
  }

})();
