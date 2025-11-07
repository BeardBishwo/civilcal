/**
 * Default Theme JavaScript
 * Handles theme-specific functionality and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme functionality
    initializeTheme();
    initializeAnimations();
    initializeTooltips();
    initializeModals();
    initializeCalculatorHelpers();
});

/**
 * Initialize theme-specific functionality
 */
function initializeTheme() {
    // Add fade-in animation to page content
    const mainContent = document.querySelector('.content-area');
    if (mainContent) {
        mainContent.classList.add('fade-in');
    }

    // Add slide-in animation to sidebar
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.add('slide-in-left');
    }

    // Initialize dropdown hover effects
    initializeDropdownHovers();

    // Initialize smooth scrolling
    initializeSmoothScrolling();
}

/**
 * Initialize dropdown hover effects
 */
function initializeDropdownHovers() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        const trigger = dropdown.querySelector('.dropdown-toggle');

        if (menu && trigger) {
            let hoverTimeout;

            dropdown.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                dropdown.classList.add('show');
                menu.classList.add('show');
            });

            dropdown.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    dropdown.classList.remove('show');
                    menu.classList.remove('show');
                }, 100);
            });
        }
    });
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initializeSmoothScrolling() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Initialize animations
 */
function initializeAnimations() {
    // Add animation delays to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Initialize intersection observer for scroll animations
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.card, .alert, .btn').forEach(el => {
            observer.observe(el);
        });
    }
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize Bootstrap modals
 */
function initializeModals() {
    const modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(modal => {
        new bootstrap.Modal(modal);
    });
}

/**
 * Initialize calculator helper functions
 */
function initializeCalculatorHelpers() {
    // Add loading states to forms
    const forms = document.querySelectorAll('form[data-calculator]');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            this.classList.add('loading');
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Calculating...';
            }
        });
    });

    // Add input formatting helpers
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Format number with thousand separators
            const value = this.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                this.value = parseFloat(value).toLocaleString();
            }
        });
    });
}

/**
 * Utility functions for theme
 */
const ThemeUtils = {
    /**
     * Show loading overlay
     */
    showLoading: function(element) {
        element.classList.add('loading');
    },

    /**
     * Hide loading overlay
     */
    hideLoading: function(element) {
        element.classList.remove('loading');
    },

    /**
     * Show success message
     */
    showSuccess: function(message) {
        this.showAlert(message, 'success');
    },

    /**
     * Show error message
     */
    showError: function(message) {
        this.showAlert(message, 'danger');
    },

    /**
     * Show alert message
     */
    showAlert: function(message, type) {
        const alertContainer = document.getElementById('alertContainer') || this.createAlertContainer();
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alertDiv);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    },

    /**
     * Create alert container if it doesn't exist
     */
    createAlertContainer: function() {
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
        return container;
    },

    /**
     * Format number for display
     */
    formatNumber: function(number, decimals = 2) {
        return parseFloat(number).toLocaleString(undefined, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    },

    /**
     * Validate email format
     */
    isValidEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Debounce function
     */
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
    }
};

/**
 * Calculator specific utilities
 */
const CalculatorUtils = {
    /**
     * Perform calculation with error handling
     */
    calculate: function(formData, calculationFunction) {
        try {
            const result = calculationFunction(formData);
            return {
                success: true,
                result: result
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    },

    /**
     * Display calculation result
     */
    displayResult: function(result, container) {
        if (result.success) {
            container.innerHTML = `
                <div class="calculator-result">
                    <h5><i class="fas fa-check-circle me-2"></i>Calculation Result</h5>
                    <p class="mb-0"><strong>${ThemeUtils.formatNumber(result.result)}</strong></p>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                    <p class="mb-0">${result.error}</p>
                </div>
            `;
        }
    },

    /**
     * Clear calculation results
     */
    clearResults: function(container) {
        container.innerHTML = '';
    }
};

/**
 * AJAX helper functions
 */
const AjaxUtils = {
    /**
     * Make AJAX request with loading state
     */
    request: function(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const finalOptions = {...defaultOptions, ...options };

        return fetch(url, finalOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                ThemeUtils.showError('An error occurred: ' + error.message);
                throw error;
            });
    },

    /**
     * Submit form via AJAX
     */
    submitForm: function(form, url) {
        const formData = new FormData(form);

        return this.request(url, {
            method: 'POST',
            body: formData
        });
    }
};

/**
 * Form validation helpers
 */
const ValidationUtils = {
    /**
     * Validate required fields
     */
    validateRequired: function(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    },

    /**
     * Validate numeric fields
     */
    validateNumeric: function(form) {
        const numericFields = form.querySelectorAll('input[type="number"]');
        let isValid = true;

        numericFields.forEach(field => {
            if (field.value && isNaN(field.value)) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }
};

// Export utilities to global scope for use in other scripts
window.ThemeUtils = ThemeUtils;
window.CalculatorUtils = CalculatorUtils;
window.AjaxUtils = AjaxUtils;
window.ValidationUtils = ValidationUtils;