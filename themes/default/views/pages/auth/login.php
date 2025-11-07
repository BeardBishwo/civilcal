<?php
/**
 * Login Page Content
 * Integrates with the existing auth.php layout
 */

// Set page metadata
$page_title = 'Welcome Back';
$auth_title = 'Welcome Back';
$auth_subtitle = 'Sign in to your Bishwo Calculator account';
$show_signup_link = true;
$show_forgot_password = true;
?>

<div class="login-form">
    <form id="loginForm" method="POST" action="/auth/login" autocomplete="on">
        <div class="form-section">
            <h3>Account Login</h3>
            
            <div class="form-group">
                <label for="loginEmail" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="loginEmail" 
                    name="email" 
                    class="form-input" 
                    placeholder="engineer@example.com" 
                    required 
                    autocomplete="email"
                >
                <div class="field-help">Enter your registered email address</div>
            </div>

            <div class="form-group">
                <label for="loginPassword" class="form-label">Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="loginPassword" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter your password" 
                        required 
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="field-help">
                    <a href="/forgot-password" class="auth-link">Forgot password?</a>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="rememberMe" 
                        name="remember_me" 
                        class="checkbox-input"
                    >
                    <label for="rememberMe" class="checkbox-label">Remember me for 30 days</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </div>
    </form>

    <!-- Social Login Section -->
    <div class="social-section">
        <div class="separator">
            <span>Or continue with</span>
        </div>
        
        <div class="social-buttons">
            <button type="button" class="btn btn-social btn-google" onclick="socialLogin('google')">
                <i class="fab fa-google"></i>
                <span>Google</span>
            </button>
            <button type="button" class="btn btn-social btn-linkedin" onclick="socialLogin('linkedin')">
                <i class="fab fa-linkedin"></i>
                <span>LinkedIn</span>
            </button>
        </div>
    </div>
</div>

<!-- Additional Login Help -->
<div class="login-help">
    <div class="help-item">
        <div class="help-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="help-content">
            <h4>Secure Login</h4>
            <p>Your account is protected with enterprise-grade security</p>
        </div>
    </div>
    
    <div class="help-item">
        <div class="help-icon">
            <i class="fas fa-key"></i>
        </div>
        <div class="help-content">
            <h4>Password Tips</h4>
            <p>Use a strong password with letters, numbers, and symbols</p>
        </div>
    </div>
</div>

<?php
// Include the auth layout
include 'themes/default/views/layouts/auth.php';
?>
