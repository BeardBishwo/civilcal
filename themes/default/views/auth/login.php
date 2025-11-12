<?php
$page_title = 'Sign In - EngiCal Pro';
require_once dirname(__DIR__, 4) . '/includes/functions.php';

// Start session early to ensure CSRF token availability
init_secure_session();

// Minimal security setup without full header
require_once dirname(__DIR__, 4) . '/includes/Security.php';
$csrf_token = Security::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="icon" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your EngiCal Pro account</p>
        </div>

        <form id="loginForm" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <!-- Login Fields -->
            <div class="form-section">
                <div class="form-group">
                    <label for="username_email">Username or Email</label>
                    <input type="text" 
                           id="username_email" 
                           name="username_email" 
                           class="form-control" 
                           placeholder="Enter your username or email"
                           autocomplete="username"
                           required>
                    <div class="field-message">Enter your username or email address</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="checkbox-item remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Remember me for 30 days</span>
                    </label>
                    
                    <a href="forgot.php" class="forgot-link">Forgot your password?</a>
                </div>
            </div>

            <!-- Security Features Info -->
            <div class="security-notice">
                <i class="fas fa-shield-alt"></i>
                <div class="security-text">
                    <strong>Your account is protected with:</strong>
                    <ul>
                        <li>SSL encryption</li>
                        <li>Failed login attempt protection</li>
                        <li>IP-based security monitoring</li>
                        <li>Two-factor authentication available</li>
                    </ul>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Signing In...
                    </span>
                </button>
            </div>

            <!-- Login Result -->
            <div id="loginResult" class="result-message" style="display: none;"></div>
        </form>

        <!-- Registration Link -->
        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php" class="auth-link">Create Professional Account</a></p>
            <p class="footer-note">
                <i class="fas fa-info-circle"></i>
                Join thousands of engineers using EngiCal Pro
            </p>
        </div>
    </div>
</div>

<!-- Quick Login Demo Section -->
<div class="demo-section">
    <div class="demo-card">
        <h3><i class="fas fa-rocket"></i> Quick Demo Access</h3>
        <p>Try the enhanced authentication system with these demo accounts:</p>
        
        <div class="demo-accounts">
            <div class="demo-account">
                <strong>Engineer Demo:</strong>
                <div class="demo-credentials">
                    <code>engineer@engicalpro.com</code>
                    <code>Engineer123!</code>
                    <button class="btn btn-sm demo-login" data-email="engineer@engicalpro.com" data-password="Engineer123!">
                        <i class="fas fa-bolt"></i> Quick Login
                    </button>
                </div>
            </div>
            
            <div class="demo-account">
                <strong>Admin Demo:</strong>
                <div class="demo-credentials">
                    <code>admin@engicalpro.com</code>
                    <code>password</code>
                    <button class="btn btn-sm demo-login" data-email="admin@engicalpro.com" data-password="password">
                        <i class="fas fa-bolt"></i> Quick Login
                    </button>
                </div>
            </div>
        </div>
        
        <p class="demo-note">
            <small><i class="fas fa-info-circle"></i> These are demo credentials for testing purposes only.</small>
        </p>
    </div>
</div>

<!-- CSS Styles -->
<style>
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.auth-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
    max-width: 450px;
}

.auth-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    text-align: center;
    padding: 40px 30px;
}

.auth-header h1 {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 700;
}

.auth-subtitle {
    margin: 10px 0 0;
    font-size: 1rem;
    opacity: 0.9;
}

.auth-form {
    padding: 40px;
}

.form-section {
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.field-message {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 5px;
}

.password-field {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 1rem;
    padding: 5px;
}

.password-toggle:hover {
    color: #4f46e5;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    flex-wrap: wrap;
    gap: 15px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.remember-me input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.remember-me .checkmark {
    height: 18px;
    width: 18px;
    background-color: #fff;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.remember-me input[type="checkbox"]:checked ~ .checkmark {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.remember-me input[type="checkbox"]:checked ~ .checkmark:after {
    display: block;
}

.remember-me .checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 5px;
    top: 1px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.remember-me .checkbox-text {
    font-size: 0.9rem;
    color: #374151;
}

.forgot-link {
    color: #4f46e5;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.forgot-link:hover {
    text-decoration: underline;
}

.security-notice {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 10px;
    padding: 20px;
    margin: 25px 0;
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.security-notice i {
    color: #0369a1;
    font-size: 1.5rem;
    margin-top: 2px;
    flex-shrink: 0;
}

.security-text {
    flex: 1;
}

.security-text strong {
    color: #0c4a6e;
    font-size: 0.9rem;
    display: block;
    margin-bottom: 8px;
}

.security-text ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.security-text li {
    color: #0c4a6e;
    font-size: 0.85rem;
    margin: 4px 0;
    padding-left: 15px;
    position: relative;
}

.security-text li:before {
    content: "✓";
    color: #059669;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.form-actions {
    text-align: center;
    margin: 30px 0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 16px 40px;
    font-size: 1.1rem;
    width: 100%;
    justify-content: center;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 0.875rem;
}

.result-message {
    margin: 20px 0;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
}

.result-message.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.result-message.error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.result-message.warning {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}

.auth-footer {
    text-align: center;
    padding: 30px;
    background-color: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.auth-footer p {
    color: #374151; /* Darker text color */
    margin: 0 0 10px 0;
}

.auth-link {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 600;
}

.auth-link:hover {
    text-decoration: underline;
}

.footer-note {
    margin-top: 15px;
    font-size: 0.9rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Demo Section */
.demo-section {
    margin-top: 30px;
    max-width: 450px;
    margin-left: auto;
    margin-right: auto;
}

.demo-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.demo-card h3 {
    margin: 0 0 15px;
    color: #374151;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.demo-card h3 i {
    color: #4f46e5;
}

.demo-card p {
    margin: 0 0 20px;
    color: #6b7280;
    font-size: 0.9rem;
}

.demo-accounts {
    margin-bottom: 15px;
}

.demo-account {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.demo-account:last-child {
    margin-bottom: 0;
}

.demo-account strong {
    color: #1e293b;
    display: block;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.demo-credentials {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.demo-credentials code {
    background: #1e293b;
    color: #f1f5f9;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-family: 'Courier New', monospace;
}

.demo-login {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.demo-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}

.demo-note {
    margin-top: 15px;
    text-align: center;
}

.demo-note small {
    color: #6b7280;
}

@media (max-width: 768px) {
    .auth-container {
        padding: 10px;
    }
    
    .auth-form {
        padding: 25px;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .demo-credentials {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .demo-credentials code {
        width: 100%;
    }
    
    .security-notice {
        flex-direction: column;
        text-align: center;
    }
    
    .demo-card {
        padding: 20px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeLoginForm();
});

function initializeLoginForm() {
    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Form submission
    document.getElementById('loginForm').addEventListener('submit', handleLoginSubmission);

    // Demo login buttons
    document.querySelectorAll('.demo-login').forEach(button => {
        button.addEventListener('click', function() {
            // Fill form fields with demo credentials
            document.getElementById('username_email').value = this.dataset.email;
            document.getElementById('password').value = this.dataset.password;
            
            // Submit the form
            document.getElementById('loginForm').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        });
    });
}

async function handleLoginSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('loginBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('loginResult');
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-flex';
    resultDiv.style.display = 'none';
    
    try {
        // Collect form data
        const formData = new FormData(form);
        const payload = {
            username_email: formData.get('username_email'),
            password: formData.get('password'),
            remember_me: formData.get('remember_me') ? 1 : 0,
            csrf_token: formData.get('csrf_token')
        };
        
        // Send login request
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': payload.csrf_token
            },
            credentials: 'include',
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
                if (result.success) {
            // Success
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Login Successful!</h3>
                <p>${result.message || 'Welcome back! Redirecting to dashboard...'}</p>
            `;
            
            // If email verification is required, show a message and don't redirect
            if (result.email_verification_required) {
                resultDiv.className = 'result-message warning';
                resultDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <h3>Email Verification Required</h3>
                    <p>${result.message || 'Please check your email to verify your account before signing in.'} <a href="verify.php" style="color: #4f46e5;">Check verification status</a></p>
                `;
                resultDiv.style.display = 'block';
            } else {
                // Update header immediately using returned user payload (avoid cookie timing issues)
                try {
                    if (window.applyHeaderUser && result.user) {
                        // prefer explicit is_admin flag from API if available
                        const isAdmin = !!result.is_admin || (result.user && result.user.role && result.user.role.toLowerCase() === 'admin');
                        window.applyHeaderUser(result.user, isAdmin);
                    } else if (window.refreshHeaderFromServer) {
                        // fallback to server refresh
                        await window.refreshHeaderFromServer();
                    }
                } catch (e) {
                    console.warn('Header update failed', e);
                }

                // Redirect after short delay so user sees header update
                setTimeout(() => {
                    window.location.href = result.redirect_url || 'profile.php';
                }, 500);
            }
                } else {
            // Error
            let errorMessage = result.error || 'Login failed';
            
            // Show specific error for email verification
            if (result.email_verification_required) {
                errorMessage += ' <a href="verify.php" style="color: #4f46e5;">Resend verification email</a>';
                resultDiv.className = 'result-message warning';
            } else {
                resultDiv.className = 'result-message error';
                } 
            
            resultDiv.innerHTML = `
                <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Login Failed</h3>
                <p>${errorMessage}</p>
            `;
            
            if (result.attempts_remaining !== undefined) {
                resultDiv.innerHTML += `<p><small>Attempts remaining: ${result.attempts_remaining}</small></p>`;
            }
        }
        
    } catch (error) {
        // Network or server error
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <h3>Connection Error</h3>
            <p>Unable to connect to server. Please check your internet connection and try again.</p>
        `;
        
        console.error('Login error:', error);
        
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        btnText.style.display = 'inline-flex';
        btnLoading.style.display = 'none';
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

</body>
</html>
