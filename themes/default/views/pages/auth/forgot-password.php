<?php
/**
 * Forgot Password Page Content
 * Integrates with the existing auth.php layout
 */

// Set page metadata
$page_title = 'Reset Password';
$auth_title = 'Reset Password';
$auth_subtitle = 'Enter your email to receive a password reset link';
$show_login_link = true;
$show_signup_link = true;
$show_forgot_password = false;
?>

<div class="forgot-password-form">
    <form id="forgotPasswordForm" method="POST" action="/auth/forgot-password" autocomplete="on">
        <div class="form-section">
            <div class="form-group">
                <label for="resetEmail" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="resetEmail" 
                    name="email" 
                    class="form-input" 
                    placeholder="engineer@example.com" 
                    required 
                    autocomplete="email"
                >
                <div class="field-help">We'll send reset instructions to this email</div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
                Send Reset Link
            </button>
        </div>
    </form>
</div>

<!-- Password Reset Help Section -->
<div class="reset-help-section">
    <h4>What happens next?</h4>
    
    <div class="help-steps">
        <div class="help-step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h5>Check Your Email</h5>
                <p>We'll send a secure link to reset your password</p>
            </div>
        </div>
        
        <div class="help-step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h5>Click the Link</h5>
                <p>Click the secure link in the email to continue</p>
            </div>
        </div>
        
        <div class="help-step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h5>Create New Password</h5>
                <p>Choose a strong new password for your account</p>
            </div>
        </div>
    </div>
    
    <div class="security-notes">
        <div class="security-item">
            <div class="security-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="security-text">
                <h5>Secure Reset</h5>
                <p>Reset links expire after 1 hour for security</p>
            </div>
        </div>
        
        <div class="security-item">
            <div class="security-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="security-text">
                <h5>Email Delivery</h5>
                <p>Check your spam folder if you don't see the email</p>
            </div>
        </div>
        
        <div class="security-item">
            <div class="security-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="security-text">
                <h5>Need Help?</h5>
                <p>Contact our support team for assistance</p>
            </div>
        </div>
    </div>
</div>

<!-- Alternative Help -->
<div class="alternative-help">
    <h4>Still need help?</h4>
    <div class="help-options">
        <div class="help-option">
            <div class="option-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="option-content">
                <h5>Call Support</h5>
                <p>+1 (555) 123-4567</p>
                <span class="availability">Mon-Fri 9AM-6PM EST</span>
            </div>
        </div>
        
        <div class="help-option">
            <div class="option-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="option-content">
                <h5>Live Chat</h5>
                <p>Chat with our support team</p>
                <span class="availability">Available 24/7</span>
            </div>
        </div>
        
        <div class="help-option">
            <div class="option-icon">
                <i class="fas fa-envelope-open"></i>
            </div>
            <div class="option-content">
                <h5>Email Support</h5>
                <p>support@bishwocalculator.com</p>
                <span class="availability">Response within 24 hours</span>
            </div>
        </div>
    </div>
</div>

<?php
// Include the auth layout
include 'themes/default/views/layouts/auth.php';
?>
