/**
 * Enhanced Authentication JavaScript
 * Provides functionality for login, register, and password reset pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize authentication features
    initAuth();
});

/**
 * Initialize all authentication functionality
 */
function initAuth() {
    // Set up form handlers
    setupFormHandlers();

    // Set up password functionality
    setupPasswordFeatures();

    // Set up real-time validation
    setupValidation();
}

/**
 * Setup form submission handlers
 */
function setupFormHandlers() {
    // Login Form Handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin(this);
        });
    }

    // Registration Form Handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegistration(this);
        });
    }

    // Forgot Password Form Handler
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handlePasswordReset(this);
        });
    }
}

/**
 * Setup password-related features
 */
function setupPasswordFeatures() {
    // Password strength monitoring
    const passwordInput = document.getElementById('registerPassword');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }

    // Password confirmation monitoring
    const confirmPasswordInput = document.getElementById('confirmPassword');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }
}

/**
 * Setup real-time validation
 */
function setupValidation() {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateEmail(this);
        });

        input.addEventListener('input', function() {
            clearFieldError(this.id);
        });
    });

    // Name validation
    const nameInputs = document.querySelectorAll('input[type="text"]');
    nameInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateName(this);
        });
    });
}

/**
 * Handle login form submission
 */
function handleLogin(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    // Clear previous errors
    clearFormErrors();

    // Validation
    if (!email) {
        showFieldError('loginEmail', 'Email is required');
        return;
    }

    if (!isValidEmail(email)) {
        showFieldError('loginEmail', 'Please enter a valid email address');
        return;
    }

    if (!password) {
        showFieldError('loginPassword', 'Password is required');
        return;
    }

    // Show loading state
    setButtonLoading(submitBtn, true, 'Signing In...');

    // Simulate login process
    setTimeout(() => {
        simulateLogin(email, password, form, submitBtn);
    }, 1500);
}

/**
 * Handle registration form submission
 */
function handleRegistration(form) {
    const submitBtn = form.querySelector('button[type="submit"]');

    // Get form data
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const profession = document.getElementById('profession').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;

    // Clear previous errors
    clearFormErrors();

    // Validation
    if (!firstName) {
        showFieldError('firstName', 'First name is required');
        return;
    }

    if (!lastName) {
        showFieldError('lastName', 'Last name is required');
        return;
    }

    if (!email) {
        showFieldError('registerEmail', 'Email is required');
        return;
    }

    if (!isValidEmail(email)) {
        showFieldError('registerEmail', 'Please enter a valid email address');
        return;
    }

    if (!profession) {
        showFieldError('profession', 'Please select your profession');
        return;
    }

    if (!password) {
        showFieldError('registerPassword', 'Password is required');
        return;
    }

    if (password.length < 8) {
        showFieldError('registerPassword', 'Password must be at least 8 characters long');
        return;
    }

    if (password !== confirmPassword) {
        showFieldError('confirmPassword', 'Passwords do not match');
        return;
    }

    if (!agreeTerms) {
        showFieldError('agreeTerms', 'You must agree to the terms and conditions');
        return;
    }

    // Show loading state
    setButtonLoading(submitBtn, true, 'Creating Account...');

    // Simulate registration process
    setTimeout(() => {
        simulateRegistration({
            firstName,
            lastName,
            email,
            password,
            profession
        }, form, submitBtn);
    }, 2000);
}

/**
 * Handle password reset form submission
 */
function handlePasswordReset(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const email = document.getElementById('resetEmail').value.trim();

    // Clear previous errors
    clearFormErrors();

    // Validation
    if (!email) {
        showFieldError('resetEmail', 'Email is required');
        return;
    }

    if (!isValidEmail(email)) {
        showFieldError('resetEmail', 'Please enter a valid email address');
        return;
    }

    // Show loading state
    setButtonLoading(submitBtn, true, 'Sending Reset Link...');

    // Simulate password reset process
    setTimeout(() => {
        simulatePasswordReset(email, form, submitBtn);
    }, 1500);
}

/**
 * Simulate login process
 */
function simulateLogin(email, password, form, submitBtn) {
    // For demo purposes - simulate successful login
    // In real implementation, make actual API call here

    if (email && password.length >= 1) {
        // Success
        showAlert('Login successful! Redirecting to dashboard...', 'success');

        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 2000);
    } else {
        // Failure
        showAlert('Invalid email or password. Please try again.', 'error');
        setButtonLoading(submitBtn, false);
    }
}

/**
 * Simulate registration process
 */
function simulateRegistration(userData, form, submitBtn) {
    // For demo purposes - simulate successful registration
    // In real implementation, make actual API call here

    showAlert('Account created successfully! Welcome to Bishwo Calculator!', 'success');

    setTimeout(() => {
        window.location.href = '/dashboard';
    }, 2500);
}

/**
 * Simulate password reset process
 */
function simulatePasswordReset(email, form, submitBtn) {
    // For demo purposes - simulate successful password reset
    // In real implementation, make actual API call here

    showAlert('Password reset instructions sent to your email!', 'success');

    setTimeout(() => {
        // Clear the form
        form.reset();

        // Redirect to login after a delay
        setTimeout(() => {
            window.location.href = '/login';
        }, 3000);
    }, 1000);
}

/**
 * Social login handlers
 */
function socialLogin(provider) {
    showAlert(`Redirecting to ${provider}...`, 'info');

    // Simulate social login
    setTimeout(() => {
        showAlert('Social login successful! Redirecting...', 'success');
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 1500);
    }, 1000);
}

/**
 * Password visibility toggle
 */
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling.querySelector('i');

    if (field.type === 'password') {
        field.type = 'text';
        button.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        button.className = 'fas fa-eye';
    }
}

/**
 * Check password strength
 */
function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('passwordStrength');
    if (!strengthIndicator) return;

    const strengthBar = strengthIndicator.querySelector('.strength-bar');
    const strengthText = strengthIndicator.querySelector('.strength-text');

    if (!password) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'strength-bar';
        strengthText.textContent = 'Password strength: Not set';
        return;
    }

    let strength = 0;
    let strengthLabel = '';
    let strengthColor = '';

    // Length check
    if (password.length >= 8) strength++;

    // Character variety checks
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;

    // Determine strength level
    switch (strength) {
        case 0:
        case 1:
            strengthLabel = 'Very Weak';
            strengthColor = '#ef4444';
            break;
        case 2:
            strengthLabel = 'Weak';
            strengthColor = '#f97316';
            break;
        case 3:
            strengthLabel = 'Fair';
            strengthColor = '#eab308';
            break;
        case 4:
            strengthLabel = 'Good';
            strengthColor = '#22c55e';
            break;
        case 5:
            strengthLabel = 'Strong';
            strengthColor = '#16a34a';
            break;
    }

    // Update UI
    const percentage = (strength / 5) * 100;
    strengthBar.style.width = percentage + '%';
    strengthBar.style.backgroundColor = strengthColor;
    strengthText.textContent = `Password strength: ${strengthLabel}`;

    // Add visual feedback
    if (strength >= 3) {
        strengthText.style.color = strengthColor;
    } else {
        strengthText.style.color = 'var(--auth-text-muted)';
    }
}

/**
 * Check password match
 */
function checkPasswordMatch() {
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const matchIndicator = document.getElementById('passwordMatch');

    if (!matchIndicator) return;

    if (!confirmPassword) {
        matchIndicator.textContent = '';
        return;
    }

    if (password === confirmPassword) {
        matchIndicator.innerHTML = '<span style="color: var(--auth-success)"><i class="fas fa-check"></i> Passwords match</span>';
    } else {
        matchIndicator.innerHTML = '<span style="color: var(--auth-error)"><i class="fas fa-times"></i> Passwords do not match</span>';
    }
}

/**
 * Validate email format
 */
function validateEmail(input) {
    const email = input.value.trim();

    if (email && !isValidEmail(email)) {
        input.classList.add('error');
        showFieldError(input.id, 'Please enter a valid email address');
        return false;
    } else {
        input.classList.remove('error');
        clearFieldError(input.id);
        return true;
    }
}

/**
 * Validate name format
 */
function validateName(input) {
    const name = input.value.trim();

    if (name && name.length < 2) {
        input.classList.add('error');
        showFieldError(input.id, 'Name must be at least 2 characters long');
        return false;
    } else {
        input.classList.remove('error');
        clearFieldError(input.id);
        return true;
    }
}

/**
 * Check if email is valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Show field error
 */
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

    // Remove existing error
    clearFieldError(fieldId);

    field.classList.add('error');
    field.parentNode.appendChild(errorDiv);
}

/**
 * Clear field error
 */
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.classList.remove('error');
}

/**
 * Clear all form errors
 */
function clearFormErrors() {
    document.querySelectorAll('.field-error').forEach(error => error.remove());
    document.querySelectorAll('.form-input.error').forEach(input => input.classList.remove('error'));
}

/**
 * Set button loading state
 */
function setButtonLoading(button, loading, loadingText) {
    if (loading) {
        button.classList.add('loading');
        button.disabled = true;
        button.setAttribute('data-original-text', button.innerHTML);
        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
    } else {
        button.classList.remove('loading');
        button.disabled = false;
        button.innerHTML = button.getAttribute('data-original-text');
    }
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlert = document.querySelector('.auth-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `auth-alert auth-alert-${type}`;
    alertDiv.innerHTML = `<i class="fas fa-${getAlertIcon(type)}"></i><span>${message}</span>`;

    // Insert alert
    const formHeader = document.querySelector('.form-header');
    if (formHeader) {
        formHeader.parentNode.insertBefore(alertDiv, formHeader.nextSibling);
    } else {
        const form = document.querySelector('form');
        if (form) {
            form.insertBefore(alertDiv, form.firstChild);
        }
    }

    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.style.opacity = '0';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        }
    }, 5000);
}

/**
 * Get alert icon based on type
 */
function getAlertIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}