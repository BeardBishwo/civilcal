/**
 * Premium Theme JavaScript - Architecture Professional Edition
 * $1,500 Premium Value JavaScript Functionality
 * ===================================================
 * 
 * Advanced features including:
 * - Interactive design tools and widgets
 * - Real-time calculation feedback
 * - Professional animations and transitions
 * - Project browser and gallery functionality
 * - Advanced form validation
 * - Theme management system
 */

(function() {
  'use strict';

  // Premium Theme Configuration
  const PremiumTheme = {
    config: {
      animationSpeed: 'medium', // slow, medium, fast
      darkMode: false,
      showAnimations: true,
      calculatorSkin: 'premium-dark',
      typographyStyle: 'modern'
    },

    // Initialize the theme
    init: function() {
      this.loadConfig();
      this.setupEventListeners();
      this.initializeComponents();
      this.setupAnimations();
      this.initializeCalculator();
      this.setupProjectBrowser();
      this.setupVisualization();
      this.setupThemeToggle();
      this.setupLoadingStates();
      this.setupFormValidation();
      console.log('🏗️ Premium Architecture Theme Initialized');
    },

    // Load configuration from localStorage and theme settings
    loadConfig: function() {
      const saved = localStorage.getItem('premiumThemeConfig');
      if (saved) {
        this.config = { ...this.config, ...JSON.parse(saved) };
      }

      // Apply theme configuration
      if (window.premiumTheme && window.premiumTheme.settings) {
        this.config = { ...this.config, ...window.premiumTheme.settings };
      }

      this.applyConfig();
    },

    // Apply configuration to the theme
    applyConfig: function() {
      const root = document.documentElement;
      
      // Dark mode
      if (this.config.darkMode) {
        root.setAttribute('data-theme', 'dark');
        document.body.classList.add('dark-mode');
      } else {
        root.setAttribute('data-theme', 'light');
        document.body.classList.remove('dark-mode');
      }

      // Animation speed
      const speedMap = {
        'slow': '750ms',
        'medium': '300ms',
        'fast': '150ms'
      };
      root.style.setProperty('--animation-speed', speedMap[this.config.animationSpeed] || '300ms');

      // Calculator skin
      document.body.setAttribute('data-calculator-skin', this.config.calculatorSkin);

      // Typography
      document.body.setAttribute('data-typography', this.config.typographyStyle);
    },

    // Save configuration
    saveConfig: function() {
      localStorage.setItem('premiumThemeConfig', JSON.stringify(this.config));
    },

    // Setup global event listeners
    setupEventListeners: function() {
      // Window scroll events
      window.addEventListener('scroll', this.handleScroll.bind(this));
      
      // Window resize events
      window.addEventListener('resize', this.handleResize.bind(this));
      
      // Keyboard shortcuts
      document.addEventListener('keydown', this.handleKeyboard.bind(this));
      
      // Click outside to close dropdowns
      document.addEventListener('click', this.handleClickOutside.bind(this));

      // Handle browser back/forward
      window.addEventListener('popstate', this.handlePopState.bind(this));
    },

    // Initialize all premium components
    initializeComponents: function() {
      this.initializeCards();
      this.initializeButtons();
      this.initializeForms();
      this.initializeNavigation();
      this.initializeLoading();
      this.initializeModals();
      this.initializeTooltips();
    },

    // Setup animations based on configuration
    setupAnimations: function() {
      if (!this.config.showAnimations) {
        return;
      }

      // Intersection Observer for scroll animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
          }
        });
      }, observerOptions);

      // Observe elements that should animate
      document.querySelectorAll('.card, .input-group, .result-item').forEach(el => {
        observer.observe(el);
      });
    },

    // Initialize calculator functionality
    initializeCalculator: function() {
      const calculatorContainers = document.querySelectorAll('.calculator-container');
      
      calculatorContainers.forEach(container => {
        this.setupCalculatorTabs(container);
        this.setupCalculatorInputs(container);
        this.setupCalculatorValidation(container);
        this.setupRealTimeCalculation(container);
        this.setupCalculatorResults(container);
      });
    },

    // Setup calculator tabs
    setupCalculatorTabs: function(container) {
      const tabs = container.querySelectorAll('.calculator-tab');
      const sections = container.querySelectorAll('.calculator-section');

      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          const targetId = tab.getAttribute('data-target');
          
          // Remove active class from all tabs and sections
          tabs.forEach(t => t.classList.remove('active'));
          sections.forEach(s => s.classList.remove('active'));
          
          // Add active class to clicked tab and target section
          tab.classList.add('active');
          const targetSection = container.querySelector(targetId);
          if (targetSection) {
            targetSection.classList.add('active');
            this.animateSection(targetSection);
          }
        });
      });
    },

    // Setup calculator input handling
    setupCalculatorInputs: function(container) {
      const inputs = container.querySelectorAll('.calculator-input');
      
      inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('input', this.debounce((e) => {
          this.validateInput(e.target);
        }, 300));

        // Focus effects
        input.addEventListener('focus', (e) => {
          e.target.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', (e) => {
          e.target.parentElement.classList.remove('focused');
        });

        // Enter key navigation
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            this.calculate(container);
          }
        });
      });
    },

    // Setup real-time calculation
    setupRealTimeCalculation: function(container) {
      const inputs = container.querySelectorAll('.calculator-input');
      
      inputs.forEach(input => {
        input.addEventListener('input', this.debounce(() => {
          this.calculate(container);
        }, 500));
      });
    },

    // Setup project browser functionality
    setupProjectBrowser: function() {
      const projectCards = document.querySelectorAll('.project-card');
      
      projectCards.forEach(card => {
        card.addEventListener('click', (e) => {
          e.preventDefault();
          this.openProjectCard(card);
        });

        // Add hover effects
        card.addEventListener('mouseenter', () => {
          card.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', () => {
          card.style.transform = 'translateY(0) scale(1)';
        });
      });
    },

    // Open project card
    openProjectCard: function(card) {
      const projectId = card.getAttribute('data-project-id');
      
      // Add loading state
      card.classList.add('loading');
      
      // Simulate loading (replace with actual API call)
      setTimeout(() => {
        card.classList.remove('loading');
        
        // Show project details modal or navigate
        this.showProjectDetails(projectId);
      }, 1000);
    },

    // Show project details
    showProjectDetails: function(projectId) {
      // Create modal or navigate to project page
      const modal = this.createModal(`Project Details`, `
        <div class="project-details">
          <h3>Project ${projectId}</h3>
          <p>Detailed project information would be loaded here.</p>
          <div class="modal-actions">
            <button class="btn btn-primary" onclick="PremiumTheme.closeModal()">Close</button>
          </div>
        </div>
      `);
      
      this.showModal(modal);
    },

    // Setup visualization controls
    setupVisualization: function() {
      const vizContainers = document.querySelectorAll('.visualization-container');
      
      vizContainers.forEach(container => {
        const controls = container.querySelectorAll('.viz-control-btn');
        
        controls.forEach(control => {
          control.addEventListener('click', (e) => {
            e.preventDefault();
            const action = control.getAttribute('data-action');
            this.handleVisualizationAction(container, action);
          });
        });
      });
    },

    // Handle visualization actions
    handleVisualizationAction: function(container, action) {
      const placeholder = container.querySelector('.visualization-placeholder');
      
      // Add visual feedback
      container.classList.add('processing');
      
      // Simulate visualization change
      setTimeout(() => {
        container.classList.remove('processing');
        
        // Update placeholder based on action
        const iconMap = {
          'rotate': '🔄',
          'zoom-in': '🔍+',
          'zoom-out': '🔍-',
          'reset': '🏗️'
        };
        
        if (placeholder) {
          placeholder.textContent = iconMap[action] || '🏗️';
        }
      }, 500);
    },

    // Setup theme toggle functionality
    setupThemeToggle: function() {
      const toggleButtons = document.querySelectorAll('.theme-toggle-btn');
      
      toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
          this.toggleDarkMode();
        });
      });
    },

    // Toggle dark mode
    toggleDarkMode: function() {
      this.config.darkMode = !this.config.darkMode;
      this.applyConfig();
      this.saveConfig();
      
      // Add transition effect
      document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
      
      // Update toggle button icon
      const toggleButtons = document.querySelectorAll('.theme-toggle-btn .toggle-icon');
      toggleButtons.forEach(icon => {
        icon.textContent = this.config.darkMode ? '☀️' : '🌙';
      });
    },

    // Setup loading states
    setupLoadingStates: function() {
      // Global loading overlay
      this.createLoadingOverlay();
      
      // Button loading states
      document.addEventListener('click', (e) => {
        if (e.target.matches('[data-loading]')) {
          this.setButtonLoading(e.target, true);
        }
      });
    },

    // Create loading overlay
    createLoadingOverlay: function() {
      const overlay = document.createElement('div');
      overlay.className = 'premium-loading-overlay';
      overlay.innerHTML = `
        <div class="loading-content">
          <div class="loading-spinner premium"></div>
          <p>Processing...</p>
        </div>
      `;
      overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
      `;
      
      document.body.appendChild(overlay);
      this.loadingOverlay = overlay;
    },

    // Show loading overlay
    showLoading: function(message = 'Processing...') {
      const content = this.loadingOverlay.querySelector('.loading-content p');
      if (content) {
        content.textContent = message;
      }
      this.loadingOverlay.style.display = 'flex';
    },

    // Hide loading overlay
    hideLoading: function() {
      this.loadingOverlay.style.display = 'none';
    },

    // Set button loading state
    setButtonLoading: function(button, loading) {
      if (loading) {
        button.setAttribute('data-original-text', button.textContent);
        button.textContent = 'Loading...';
        button.disabled = true;
        button.classList.add('loading');
      } else {
        button.textContent = button.getAttribute('data-original-text');
        button.disabled = false;
        button.classList.remove('loading');
      }
    },

    // Setup form validation
    setupFormValidation: function() {
      const forms = document.querySelectorAll('form');
      
      forms.forEach(form => {
        form.addEventListener('submit', (e) => {
          if (!this.validateForm(form)) {
            e.preventDefault();
          }
        });
      });
    },

    // Validate form
    validateForm: function(form) {
      let isValid = true;
      const inputs = form.querySelectorAll('.calculator-input, .form-input');
      
      inputs.forEach(input => {
        if (!this.validateInput(input)) {
          isValid = false;
        }
      });
      
      return isValid;
    },

    // Validate individual input
    validateInput: function(input) {
      const value = input.value.trim();
      const type = input.getAttribute('type') || input.getAttribute('data-type');
      const required = input.hasAttribute('required');
      
      let isValid = true;
      let errorMessage = '';
      
      // Required validation
      if (required && !value) {
        isValid = false;
        errorMessage = 'This field is required';
      }
      
      // Type-specific validation
      if (value && type) {
        switch (type) {
          case 'number':
            if (isNaN(value) || value < 0) {
              isValid = false;
              errorMessage = 'Please enter a valid positive number';
            }
            break;
          
          case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
              isValid = false;
              errorMessage = 'Please enter a valid email address';
            }
            break;
          
          case 'percentage':
            if (isNaN(value) || value < 0 || value > 100) {
              isValid = false;
              errorMessage = 'Please enter a value between 0 and 100';
            }
            break;
        }
      }
      
      // Apply validation styles
      this.applyInputValidation(input, isValid, errorMessage);
      
      return isValid;
    },

    // Apply input validation styles
    applyInputValidation: function(input, isValid, errorMessage) {
      input.classList.remove('error', 'success');
      input.setAttribute('aria-invalid', !isValid);
      
      if (errorMessage) {
        input.classList.add('error');
        this.showInputError(input, errorMessage);
      } else if (input.value.trim()) {
        input.classList.add('success');
        this.hideInputError(input);
      }
    },

    // Show input error
    showInputError: function(input, message) {
      this.hideInputError(input);
      
      const errorElement = document.createElement('div');
      errorElement.className = 'input-error';
      errorElement.textContent = message;
      errorElement.style.cssText = `
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
      `;
      
      input.parentElement.appendChild(errorElement);
    },

    // Hide input error
    hideInputError: function(input) {
      const errorElement = input.parentElement.querySelector('.input-error');
      if (errorElement) {
        errorElement.remove();
      }
    },

    // Calculate results
    calculate: function(container) {
      const inputs = container.querySelectorAll('.calculator-input');
      let results = {};
      
      // Collect input values
      inputs.forEach(input => {
        const name = input.getAttribute('name') || input.getAttribute('id');
        const value = parseFloat(input.value) || 0;
        if (name) {
          results[name] = value;
        }
      });
      
      // Perform calculation based on container type
      if (container.classList.contains('calculator-civil')) {
        results = this.calculateCivil(results);
      } else if (container.classList.contains('calculator-structural')) {
        results = this.calculateStructural(results);
      } else if (container.classList.contains('calculator-mep')) {
        results = this.calculateMEP(results);
      }
      
      // Update results display
      this.updateResults(container, results);
    },

    // Civil engineering calculations
    calculateCivil: function(inputs) {
      const results = {};
      
      if (inputs.area && inputs.unit) {
        // Simple area conversion example
        const areaInSqM = inputs.area;
        const areaInSqFt = areaInSqM * 10.764; // 1 sq m = 10.764 sq ft
        
        results.area_sqm = areaInSqM;
        results.area_sqft = areaInSqFt;
        results.perimeter = Math.sqrt(areaInSqM) * 4; // Assuming square
      }
      
      return results;
    },

    // Structural calculations
    calculateStructural: function(inputs) {
      const results = {};
      
      if (inputs.length && inputs.width && inputs.height) {
        // Simple beam calculation example
        const length = inputs.length;
        const width = inputs.width;
        const height = inputs.height;
        
        results.cross_sectional_area = length * width;
        results.moment_of_inertia = (length * Math.pow(height, 3)) / 12;
        results.weight_per_meter = results.cross_sectional_area * 7850 / 1000000; // Steel density
      }
      
      return results;
    },

    // MEP calculations
    calculateMEP: function(inputs) {
      const results = {};
      
      if (inputs.power && inputs.voltage) {
        // Power calculation
        const power = inputs.power; // kW
        const voltage = inputs.voltage; // V
        
        results.current = (power * 1000) / voltage; // Amperes
        results.energy_per_hour = power; // kWh
        results.energy_per_day = power * 24; // kWh/day
      }
      
      return results;
    },

    // Update results display
    updateResults: function(container, results) {
      const resultsContainer = container.querySelector('.results-container');
      if (!resultsContainer || !Object.keys(results).length) return;
      
      const resultsList = resultsContainer.querySelector('.results-list') || 
                          this.createResultsList(resultsContainer);
      
      // Clear existing results
      resultsList.innerHTML = '';
      
      // Add new results
      Object.entries(results).forEach(([key, value]) => {
        const resultItem = this.createResultItem(key, value);
        resultsList.appendChild(resultItem);
      });
      
      // Animate results
      this.animateResults(resultsList);
    },

    // Create results list
    createResultsList: function(container) {
      let resultsList = container.querySelector('.results-list');
      if (!resultsList) {
        resultsList = document.createElement('div');
        resultsList.className = 'results-list';
        container.appendChild(resultsList);
      }
      return resultsList;
    },

    // Create result item
    createResultItem: function(key, value) {
      const item = document.createElement('div');
      item.className = 'result-item';
      
      const labelMap = {
        'area_sqm': 'Area (sq m)',
        'area_sqft': 'Area (sq ft)',
        'perimeter': 'Perimeter',
        'cross_sectional_area': 'Cross-sectional Area',
        'moment_of_inertia': 'Moment of Inertia',
        'weight_per_meter': 'Weight per Meter',
        'current': 'Current',
        'energy_per_hour': 'Energy per Hour',
        'energy_per_day': 'Energy per Day'
      };
      
      const label = labelMap[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
      const formattedValue = typeof value === 'number' ? value.toFixed(2) : value;
      
      item.innerHTML = `
        <span class="result-label">${label}</span>
        <span class="result-value">${formattedValue}</span>
      `;
      
      return item;
    },

    // Animate results
    animateResults: function(resultsList) {
      const items = resultsList.querySelectorAll('.result-item');
      items.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          item.style.transition = 'all 0.3s ease';
          item.style.opacity = '1';
          item.style.transform = 'translateY(0)';
        }, index * 100);
      });
    },

    // Scroll handling
    handleScroll: function() {
      const header = document.querySelector('.premium-header');
      const backToTop = document.getElementById('backToTop');
      
      if (window.scrollY > 100) {
        header && header.classList.add('scrolled');
        backToTop && (backToTop.style.display = 'block');
      } else {
        header && header.classList.remove('scrolled');
        backToTop && (backToTop.style.display = 'none');
      }
    },

    // Resize handling
    handleResize: function() {
      // Recalculate layouts if needed
      this.updateGridLayouts();
    },

    // Keyboard shortcuts
    handleKeyboard: function(e) {
      // Ctrl/Cmd + D for dark mode toggle
      if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        this.toggleDarkMode();
      }
      
      // Escape to close modals
      if (e.key === 'Escape') {
        this.closeAllModals();
      }
      
      // Ctrl/Cmd + K for calculator focus
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const firstInput = document.querySelector('.calculator-input');
        firstInput && firstInput.focus();
      }
    },

    // Click outside handling
    handleClickOutside: function(e) {
      // Close dropdowns
      const dropdowns = document.querySelectorAll('.user-dropdown');
      dropdowns.forEach(dropdown => {
        if (!dropdown.closest('.user-menu').contains(e.target)) {
          dropdown.classList.remove('show');
        }
      });
    },

    // Pop state handling
    handlePopState: function() {
      // Handle browser navigation
    },

    // Create modal
    createModal: function(title, content) {
      const modal = document.createElement('div');
      modal.className = 'premium-modal';
      modal.innerHTML = `
        <div class="modal-backdrop" onclick="PremiumTheme.closeModal()"></div>
        <div class="modal-content">
          <div class="modal-header">
            <h2>${title}</h2>
            <button class="modal-close" onclick="PremiumTheme.closeModal()">&times;</button>
          </div>
          <div class="modal-body">
            ${content}
          </div>
        </div>
      `;
      
      // Add modal styles if not exists
      if (!document.querySelector('#premium-modal-styles')) {
        const styles = document.createElement('style');
        styles.id = 'premium-modal-styles';
        styles.textContent = `
          .premium-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
          }
          .modal-content {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-2xl);
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            z-index: 1;
          }
          .modal-header {
            padding: var(--space-6);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
          }
          .modal-header h2 {
            margin: 0;
            color: var(--text-primary);
          }
          .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-tertiary);
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            transition: all var(--duration-fast) ease;
          }
          .modal-close:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
          }
          .modal-body {
            padding: var(--space-6);
          }
        `;
        document.head.appendChild(styles);
      }
      
      return modal;
    },

    // Show modal
    showModal: function(modal) {
      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';
      
      // Animate in
      const content = modal.querySelector('.modal-content');
      content.style.transform = 'scale(0.8) translateY(20px)';
      content.style.opacity = '0';
      
      setTimeout(() => {
        content.style.transition = 'all 0.3s ease';
        content.style.transform = 'scale(1) translateY(0)';
        content.style.opacity = '1';
      }, 10);
    },

    // Close modal
    closeModal: function() {
      const modal = document.querySelector('.premium-modal');
      if (modal) {
        const content = modal.querySelector('.modal-content');
        content.style.transform = 'scale(0.8) translateY(20px)';
        content.style.opacity = '0';
        
        setTimeout(() => {
          modal.remove();
          document.body.style.overflow = '';
        }, 300);
      }
    },

    // Close all modals
    closeAllModals: function() {
      const modals = document.querySelectorAll('.premium-modal');
      modals.forEach(modal => this.closeModal());
    },

    // Utility functions
    debounce: function(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },

    animateSection: function(section) {
      const elements = section.querySelectorAll('.input-group, .result-item');
      elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          el.style.transition = 'all 0.3s ease';
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, index * 100);
      });
    },

    updateGridLayouts: function() {
      // Update any dynamic grid layouts
    },

    // Component initialization methods
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
          const x = e.clientX - rect.left - size / 2;
          const y = e.clientY - rect.top - size / 2;
          
          ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            animation: ripple 0.6s linear;
            pointer-events: none;
          `;
          
          this.style.position = 'relative';
          this.style.overflow = 'hidden';
          this.appendChild(ripple);
          
          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      });
    },

    initializeForms: function() {
      // Add floating label effect
      const inputs = document.querySelectorAll('.form-input, .calculator-input');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
          if (!this.value) {
            this.parentElement.classList.remove('focused');
          }
        });
      });
    },

    initializeNavigation: function() {
      // Mobile menu toggle
      const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
      const nav = document.querySelector('.main-navigation');
      
      if (mobileMenuBtn && nav) {
        mobileMenuBtn.addEventListener('click', () => {
          nav.classList.toggle('mobile-open');
        });
      }
    },

    initializeLoading: function() {
      // Global loading state management
      window.showGlobalLoading = () => this.showLoading();
      window.hideGlobalLoading = () => this.hideLoading();
    },

    initializeModals: function() {
      // Modal close on backdrop click
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-backdrop')) {
          this.closeModal();
        }
      });
    },

    initializeTooltips: function() {
      // Simple tooltip implementation
      const tooltipElements = document.querySelectorAll('[data-tooltip]');
      tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
          const tooltip = document.createElement('div');
          tooltip.className = 'tooltip';
          tooltip.textContent = e.target.getAttribute('data-tooltip');
          tooltip.style.cssText = `
            position: absolute;
            background: var(--text-primary);
            color: var(--bg-primary);
            padding: var(--space-2) var(--space-3);
            border-radius: var(--radius-md);
            font-size: var(--text-sm);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
          `;
          
          document.body.appendChild(tooltip);
          
          const rect = e.target.getBoundingClientRect();
          tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
          tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
          
          e.target._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', (e) => {
          if (e.target._tooltip) {
            e.target._tooltip.remove();
            delete e.target._tooltip;
          }
        });
      });
    },

    // Setup calculator validation
    setupCalculatorValidation: function(container) {
      const inputs = container.querySelectorAll('.calculator-input');
      inputs.forEach(input => {
        input.addEventListener('blur', () => {
          this.validateInput(input);
        });
      });
    },

    // Setup calculator results
    setupCalculatorResults: function(container) {
      // Results will be handled by individual calculator functions
    }
  };

  // Make PremiumTheme globally available
  window.PremiumTheme = PremiumTheme;

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => PremiumTheme.init());
  } else {
    PremiumTheme.init();
  }

  // Global utility functions for theme management
  window.togglePremiumDarkMode = function() {
    PremiumTheme.toggleDarkMode();
  };

  window.premiumThemeInit = function() {
    PremiumTheme.init();
  };

})();

// Premium CSS animations and transitions
(function() {
  const style = document.createElement('style');
  style.textContent = `
    /* Premium Loading Spinner */
    .loading-spinner.premium {
      width: 60px;
      height: 60px;
      border: 4px solid var(--border-color);
      border-top: 4px solid var(--primary-color);
      border-radius: 50%;
      animation: premium-spin 1s linear infinite;
      position: relative;
    }
    
    .loading-spinner.premium::after {
      content: '';
      position: absolute;
      top: 10px;
      left: 10px;
      right: 10px;
      bottom: 10px;
      border: 2px solid transparent;
      border-top: 2px solid var(--secondary-color);
      border-radius: 50%;
      animation: premium-spin 1.5s linear infinite reverse;
    }
    
    @keyframes premium-spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    /* Premium Hover Effects */
    .card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-2xl);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(26, 54, 93, 0.3);
    }
    
    /* Premium Focus States */
    .calculator-input:focus {
      transform: scale(1.02);
      box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.2);
    }
    
    /* Premium Animations */
    .animate-slide-up {
      animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .animate-scale-in {
      animation: scaleIn 0.3s ease-out;
    }
    
    @keyframes scaleIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    
    /* Ripple effect */
    @keyframes ripple {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
  `;
  
  document.head.appendChild(style);
})();
