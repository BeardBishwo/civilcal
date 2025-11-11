/**
 * ProCalculator Authentication Enhanced
 * Handles login, register, and forgot password forms with API integration
 */

class ProCalculatorAuth {
    constructor(core) {
        this.core = core;
        this.forms = {
            login: null,
            register: null,
            forgot: null
        };
        this.basePath = this.getBasePath();
    }

    /**
     * Get base path from URL
     */
    getBasePath() {
        // Get the base path from the current URL
        // For http://localhost/bishwo_calculator/login it should return /bishwo_calculator
        const path = window.location.pathname;
        
        // Extract everything before /login, /register, /forgot-password, etc.
        const authPages = ['/login', '/register', '/forgot-password', '/reset-password', '/verify'];
        for (const page of authPages) {
            if (path.endsWith(page)) {
                return path.substring(0, path.length - page.length);
            }
        }
        
        // Fallback: get first path segment
        const segments = path.split('/').filter(s => s);
        return segments.length > 0 ? '/' + segments[0] : '';
    }

    /**
     * Initialize auth module
     */
    async init() {
        console.log('🔐 Initializing ProCalculator Auth');
        
        // Initialize forms
        this.initLoginForm();
        this.initRegisterForm();
        this.initForgotPasswordForm();
        
        // Initialize password strength checker
        this.initPasswordStrength();
        
        // Initialize real-time validation
        this.initRealTimeValidation();
        
        console.log('✅ ProCalculator Auth Ready');
    }

    /**
     * Initialize login form
     */
    initLoginForm() {
        const loginForm = document.getElementById('loginForm');
        if (!loginForm) return;
        
        this.forms.login = loginForm;
        
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if (!this.validateLoginForm()) {
                return;
            }
            
            const formData = new FormData(loginForm);
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            
            this.setButtonLoading(submitBtn, true);
            
            try {
                console.log('Login attempt - basePath:', this.basePath);
                console.log('Fetching:', `${this.basePath}/login`);
                
                const response = await fetch(`${this.basePath}/login`, {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    this.showNotification(data.message || 'Login successful!', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || `${this.basePath}/dashboard`;
                    }, 1500);
                } else {
                    this.showNotification(data.message || 'Invalid email or password', 'error');
                    this.setButtonLoading(submitBtn, false);
                }
            } catch (error) {
                console.error('Login error:', error);
                this.showNotification('An error occurred. Please try again.', 'error');
                this.setButtonLoading(submitBtn, false);
            }
        }, true);
    }

    /**
     * Initialize register form
     */
    initRegisterForm() {
        const registerForm = document.getElementById('registerForm');
        if (!registerForm) return;
        
        this.forms.register = registerForm;
        
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!this.validateRegisterForm()) {
                return;
            }
            
            const formData = new FormData(registerForm);
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            
            this.setButtonLoading(submitBtn, true);
            
            try {
                const response = await fetch(`${this.basePath}/register`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('Registration successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || `${this.basePath}/dashboard`;
                    }, 1000);
                } else {
                    this.showNotification(data.message || 'Registration failed', 'error');
                    this.setButtonLoading(submitBtn, false);
                }
            } catch (error) {
                console.error('Registration error:', error);
                this.showNotification('An error occurred. Please try again.', 'error');
                this.setButtonLoading(submitBtn, false);
            }
        });
    }

    /**
     * Initialize forgot password form
     */
    initForgotPasswordForm() {
        const forgotForm = document.getElementById('forgotForm');
        if (!forgotForm) return;
        
        this.forms.forgot = forgotForm;
        
        forgotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!this.validateForgotPasswordForm()) {
                return;
            }
            
            const formData = new FormData(forgotForm);
            const submitBtn = forgotForm.querySelector('button[type="submit"]');
            
            this.setButtonLoading(submitBtn, true);
            
            try {
                const response = await fetch(`${this.basePath}/forgot-password`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showSuccessState();
                } else {
                    this.showNotification(data.message || 'An error occurred', 'error');
                    this.setButtonLoading(submitBtn, false);
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                this.showNotification('An error occurred. Please try again.', 'error');
                this.setButtonLoading(submitBtn, false);
            }
        });
    }

    /**
     * Validate login form
     */
    validateLoginForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        let isValid = true;

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            this.showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        } else {
            this.clearFieldError('email');
        }

        // Password validation
        if (!password || password.length < 6) {
            this.showFieldError('password', 'Password must be at least 6 characters');
            isValid = false;
        } else {
            this.clearFieldError('password');
        }

        return isValid;
    }

    /**
     * Validate register form
     */
    validateRegisterForm() {
        const fields = {
            first_name: 'First name is required',
            last_name: 'Last name is required',
            email: 'Valid email is required',
            password: 'Password must be at least 8 characters',
            confirm_password: 'Passwords must match'
        };

        let isValid = true;

        // Validate each field
        for (const [field, message] of Object.entries(fields)) {
            const input = document.getElementById(field);
            if (!input) continue;

            const value = input.value.trim();

            if (field === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!value || !emailRegex.test(value)) {
                    this.showFieldError(field, message);
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            } else if (field === 'password') {
                if (!value || value.length < 8) {
                    this.showFieldError(field, message);
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            } else if (field === 'confirm_password') {
                const password = document.getElementById('password').value;
                if (value !== password) {
                    this.showFieldError(field, message);
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            } else {
                if (!value) {
                    this.showFieldError(field, message);
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            }
        }

        // Check terms acceptance
        const termsCheckbox = document.getElementById('terms');
        if (termsCheckbox && !termsCheckbox.checked) {
            this.showNotification('You must accept the terms and conditions', 'warning');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Validate forgot password form
     */
    validateForgotPasswordForm() {
        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email || !emailRegex.test(email)) {
            this.showFieldError('email', 'Please enter a valid email address');
            return false;
        }

        this.clearFieldError('email');
        return true;
    }

    /**
     * Show field error
     */
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(fieldId + '-error');

        if (field) {
            field.classList.add('pc-input-error');
        }

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Clear field error
     */
    clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(fieldId + '-error');

        if (field) {
            field.classList.remove('pc-input-error');
        }

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    /**
     * Set button loading state
     */
    setButtonLoading(button, loading) {
        if (!button) return;

        const btnContent = button.querySelector('.pc-btn-content');
        const btnLoading = button.querySelector('.pc-btn-loading');
        
        if (loading) {
            button.disabled = true;
            if (btnContent) btnContent.classList.add('pc-hidden');
            if (btnLoading) btnLoading.classList.remove('pc-hidden');
        } else {
            button.disabled = false;
            if (btnContent) btnContent.classList.remove('pc-hidden');
            if (btnLoading) btnLoading.classList.add('pc-hidden');
        }
    }

    /**
     * Show notification using toast system
     */
    showNotification(message, type = 'info') {
        // Use global toast function if available
        if (typeof window.showToast === 'function') {
            const titles = {
                success: '✓ Success',
                error: '✗ Error',
                warning: '⚠ Warning',
                info: 'ℹ Information'
            };
            window.showToast(type, titles[type] || 'Notification', message);
        } else {
            // Fallback to alert if toast not available
            alert(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Get notification icon
     */
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * Show success state for forgot password
     */
    showSuccessState() {
        const card = document.querySelector('.pc-forgot-card');
        if (!card) return;

        card.innerHTML = `
            <div class="pc-success-state">
                <div class="pc-email-icon" style="background: var(--pc-gradient-primary);">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="pc-forgot-title">Check Your Email</h1>
                <p class="pc-forgot-subtitle">We've sent a password reset link to your email address. Click the link in the email to reset your password.</p>
                <div class="pc-form-group" style="margin-top: var(--pc-spacing-xl);">
                    <a href="${this.basePath}/login" class="pc-btn pc-btn-primary pc-btn-lg pc-w-full">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Back to Sign In
                    </a>
                </div>
                <div class="pc-back-link">
                    <p class="pc-text-secondary">Didn't receive the email? Check your spam folder or 
                        <a href="#" onclick="location.reload()">try again</a>
                    </p>
                </div>
            </div>
        `;
    }

    /**
     * Initialize password strength checker
     */
    initPasswordStrength() {
        const passwordFields = document.querySelectorAll('input[type="password"][id="password"]');
        
        passwordFields.forEach(field => {
            field.addEventListener('input', (e) => {
                const strength = this.checkPasswordStrength(e.target.value);
                this.updatePasswordStrengthUI(strength);
            });
        });
    }

    /**
     * Check password strength
     */
    checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        return Math.min(strength, 4);
    }

    /**
     * Update password strength UI
     */
    updatePasswordStrengthUI(strength) {
        const strengthBar = document.querySelector('.pc-strength-fill');
        if (!strengthBar) return;

        const strengthClasses = ['pc-strength-weak', 'pc-strength-fair', 'pc-strength-good', 'pc-strength-strong'];
        strengthBar.className = 'pc-strength-fill';
        
        if (strength > 0) {
            strengthBar.classList.add(strengthClasses[strength - 1]);
        }
    }

    /**
     * Initialize real-time validation
     */
    initRealTimeValidation() {
        const emailInputs = document.querySelectorAll('input[type="email"]');
        
        emailInputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                const email = e.target.value.trim();
                if (email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        this.showFieldError(e.target.id, 'Please enter a valid email address');
                    } else {
                        this.clearFieldError(e.target.id);
                    }
                }
            });
        });
    }
}

// Auto-initialize if ProCalculatorCore is available
if (typeof ProCalculatorCore !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        const auth = new ProCalculatorAuth();
        auth.init();
    });
} else {
    // Standalone initialization
    document.addEventListener('DOMContentLoaded', () => {
        const auth = new ProCalculatorAuth();
        auth.init();
    });
}
