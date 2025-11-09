/**
 * Bishwo Calculator - Installation Wizard
 * JavaScript for Interactive Features
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializePasswordStrength();
    initializeFormValidation();
    initializeAJAXHandlers();
    initializeProgressTracking();
    initializeEmailConfig();
    initializeTooltips();
    initializeAnimations();
});

/**
 * Password Strength Checker
 */
function initializePasswordStrength() {
    const passwordInput = document.getElementById('admin_pass');
    const confirmPasswordInput = document.getElementById('admin_pass_confirm');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');

    if (!passwordInput) return;

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updatePasswordStrengthUI(strengthBar, strengthText, strength);
    });

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            validatePasswordMatch();
        });
    }
}

function calculatePasswordStrength(password) {
    let score = 0;
    let feedback = [];

    // Length check
    if (password.length >= 8) {
        score += 20;
    } else {
        feedback.push('Use at least 8 characters');
    }

    // Character variety checks
    if (/[a-z]/.test(password)) score += 15;
    else feedback.push('Add lowercase letters');

    if (/[A-Z]/.test(password)) score += 15;
    else feedback.push('Add uppercase letters');

    if (/[0-9]/.test(password)) score += 15;
    else feedback.push('Add numbers');

    if (/[^A-Za-z0-9]/.test(password)) score += 15;
    else feedback.push('Add special characters');

    // Bonus for length beyond 12
    if (password.length > 12) score += 20;

    return {
        score: Math.min(score, 100),
        feedback: feedback
    };
}

function updatePasswordStrengthUI(bar, text, strength) {
    if (!bar || !text) return;

    // Update progress bar
    bar.style.width = strength.score + '%';

    // Update color and text
    if (strength.score < 30) {
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Weak password';
        text.className = 'text-danger';
    } else if (strength.score < 60) {
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Fair password';
        text.className = 'text-warning';
    } else if (strength.score < 80) {
        bar.className = 'progress-bar bg-info';
        text.textContent = 'Good password';
        text.className = 'text-info';
    } else {
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Strong password';
        text.className = 'text-success';
    }
}

function validatePasswordMatch() {
    const password = document.getElementById('admin_pass').value;
    const confirmPassword = document.getElementById('admin_pass_confirm').value;
    const confirmInput = document.getElementById('admin_pass_confirm');

    if (!confirmInput) return;

    if (confirmPassword && password !== confirmPassword) {
        confirmInput.setCustomValidity('Passwords do not match');
        confirmInput.classList.add('is-invalid');
    } else {
        confirmInput.setCustomValidity('');
        confirmInput.classList.remove('is-invalid');
    }
}

/**
 * Form Validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[id$="Form"]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input[required], input[type="email"]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('was-validated')) {
                    validateField(this);
                }
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], input[type="email"]');

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    let isValid = true;
    let message = '';

    // Required field check
    if (field.hasAttribute('required') && !field.value.trim()) {
        isValid = false;
        message = 'This field is required';
    }

    // Email validation
    if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            isValid = false;
            message = 'Please enter a valid email address';
        }
    }

    // Database host validation
    if (field.name === 'db_host' && field.value) {
        const hostRegex = /^[a-zA-Z0-9.-]+$/;
        if (!hostRegex.test(field.value)) {
            isValid = false;
            message = 'Please enter a valid host name';
        }
    }

    // Port validation
    if (field.name.includes('port') && field.value) {
        const port = parseInt(field.value);
        if (isNaN(port) || port < 1 || port > 65535) {
            isValid = false;
            message = 'Please enter a valid port number (1-65535)';
        }
    }

    // Update field validation state
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        removeFieldError(field);
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        showFieldError(field, message);
    }

    return isValid;
}

function showFieldError(field, message) {
    removeFieldError(field);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;

    field.parentNode.appendChild(errorDiv);
}

function removeFieldError(field) {
    const existingError = field.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * AJAX Handlers
 */
function initializeAJAXHandlers() {
    // Database connection test
    const testButton = document.getElementById('testDbConnection');
    if (testButton) {
        testButton.addEventListener('click', testDatabaseConnection);
    }

    // Email SMTP test
    const testEmailButton = document.getElementById('testSmtpConnection');
    if (testEmailButton) {
        testEmailButton.addEventListener('click', testSmtpConnection);
    }

    // Form submission loading states
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                setButtonLoading(submitButton, true);
            }
        });
    });
}

async function testDatabaseConnection() {
    const button = document.getElementById('testDbConnection');
    const form = document.getElementById('databaseForm');
    const formData = new FormData(form);

    setButtonLoading(button, true);

    try {
        const response = await fetch('ajax/test-db.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Database connection successful!', 'success');
        } else {
            showAlert('Database connection failed: ' + result.message, 'danger');
        }
    } catch (error) {
        showAlert('Connection test failed. Please check your settings.', 'danger');
    } finally {
        setButtonLoading(button, false);
    }
}

async function testSmtpConnection() {
    const button = document.getElementById('testSmtpConnection');
    const form = document.getElementById('emailForm');
    const formData = new FormData(form);

    setButtonLoading(button, true);

    try {
        const response = await fetch('ajax/test-smtp.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showAlert('SMTP connection successful!', 'success');
        } else {
            showAlert('SMTP connection failed: ' + result.message, 'danger');
        }
    } catch (error) {
        showAlert('SMTP test failed. Please check your settings.', 'danger');
    } finally {
        setButtonLoading(button, false);
    }
}

/**
 * Progress Tracking
 */
function initializeProgressTracking() {
    // Smooth scrolling for step navigation
    const stepLinks = document.querySelectorAll('.step-item a');
    stepLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetStep = this.getAttribute('href').replace('#', '');
            navigateToStep(targetStep);
        });
    });
}

function navigateToStep(step) {
    // Save current step to session storage for persistence
    sessionStorage.setItem('currentStep', step);

    // Add loading animation
    const content = document.querySelector('.install-content');
    if (content) {
        content.style.opacity = '0.5';
        setTimeout(() => {
            window.location.href = `index.php?step=${step}`;
        }, 200);
    }
}

/**
 * Email Configuration
 */
function initializeEmailConfig() {
    const smtpToggle = document.getElementById('smtp_enabled');
    const smtpConfig = document.querySelector('.smtp-fields');
    const smtpHidden = document.getElementById('smtp_enabled_hidden');

    if (!smtpToggle || !smtpConfig) return;

    smtpToggle.addEventListener('change', function() {
        const isEnabled = this.checked;
        smtpConfig.style.display = isEnabled ? 'block' : 'none';
        if (smtpHidden) {
            smtpHidden.value = isEnabled ? '1' : '0';
        }

        if (isEnabled) {
            // Focus first SMTP field
            setTimeout(() => {
                const firstField = smtpConfig.querySelector('input');
                if (firstField) {
                    firstField.focus();
                }
            }, 100);
        }
    });

    // Initialize SMTP config visibility
    if (smtpToggle.checked) {
        smtpConfig.style.display = 'block';
    }

    // Skip email setup handler
    const skipButton = document.querySelector('button[name="skip_email"]');
    if (skipButton) {
        skipButton.addEventListener('click', function(e) {
            if (!confirm('Skip email configuration? You can configure it later from admin settings.')) {
                e.preventDefault();
                return false;
            }
        });
    }
}

/**
 * Tooltips and Help
 */
function initializeTooltips() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Help icons and expandable sections
    const helpTriggers = document.querySelectorAll('[data-help]');
    helpTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const helpId = this.getAttribute('data-help');
            const helpContent = document.getElementById(helpId);
            if (helpContent) {
                helpContent.classList.toggle('show');
            }
        });
    });
}

/**
 * Animations and Effects
 */
function initializeAnimations() {
    // Fade in animations for step content
    const stepContent = document.querySelector('.install-content > *');
    if (stepContent) {
        stepContent.style.opacity = '0';
        stepContent.style.transform = 'translateY(20px)';

        setTimeout(() => {
            stepContent.style.transition = 'all 0.5s ease';
            stepContent.style.opacity = '1';
            stepContent.style.transform = 'translateY(0)';
        }, 100);
    }

    // Hover effects for feature cards
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animate progress steps
    const stepItems = document.querySelectorAll('.step-item');
    stepItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';

        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Utility Functions
 */
function setButtonLoading(button, loading) {
    if (loading) {
        button.classList.add('loading');
        button.disabled = true;
        button.setAttribute('data-original-text', button.textContent);
        button.textContent = 'Loading...';
    } else {
        button.classList.remove('loading');
        button.disabled = false;
        const originalText = button.getAttribute('data-original-text');
        if (originalText) {
            button.textContent = originalText;
        }
    }
}

function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.install-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} install-alert`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;

    // Insert at top of content
    const content = document.querySelector('.install-content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

function showSuccessAnimation() {
    const successIcon = document.querySelector('.success-animation i');
    if (successIcon) {
        successIcon.style.animation = 'bounceIn 1s ease-out';
    }
}

function updateProgressBar(percentage) {
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    // Enter key on form submission
    if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'submit') {
        const form = e.target.closest('form');
        if (form) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.click();
            }
        }
    }

    // Escape key to close modals/dropdowns
    if (e.key === 'Escape') {
        const dropdowns = document.querySelectorAll('.show');
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Auto-save form data to session storage
function autoSaveForm() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                sessionStorage.setItem('installFormData_' + form.id, JSON.stringify(data));
            });
        });
    });
}

// Load saved form data
function loadSavedFormData() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const savedData = sessionStorage.getItem('installFormData_' + form.id);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = data[key] === '1' || data[key] === 'true';
                        } else {
                            input.value = data[key];
                        }
                    }
                });
            } catch (e) {
                console.log('Error loading saved form data:', e);
            }
        }
    });
}

// Initialize auto-save
autoSaveForm();
loadSavedFormData();