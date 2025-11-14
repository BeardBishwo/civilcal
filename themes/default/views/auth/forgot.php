<?php
$page_title = 'Reset Password - Civil Calculator';
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
require_once dirname(__DIR__, 4) . '/app/Services/Security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = Security::generateCsrfToken();
?>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h1>Reset Password</h1>
            <p class="auth-subtitle">Enter your email to receive password reset instructions</p>
        </div>

        <form id="forgotForm" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <!-- Email Input -->
            <div class="form-section">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Enter your registered email address"
                           autocomplete="email"
                           required>
                    <div class="field-message">We'll send a secure password reset link to this email</div>
                </div>

                <!-- Security Notice -->
                <div class="security-info">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-text">
                        <strong>Password Reset Security:</strong>
                        <ul>
                            <li>Reset link expires in 1 hour</li>
                            <li>Link can only be used once</li>
                            <li>Email verification may be required</li>
                            <li>New password must meet security requirements</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg" id="forgotBtn">
                    <span class="btn-text">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Link
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Sending...
                    </span>
                </button>
            </div>

            <!-- Result Message -->
            <div id="forgotResult" class="result-message" style="display: none;"></div>
        </form>

        <!-- Additional Options -->
        <div class="auth-footer">
            <div class="footer-links">
                <p><a href="<?php echo app_base_url('login'); ?>" class="auth-link"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
                <p>Don't have an account? <a href="<?php echo app_base_url('register'); ?>" class="auth-link">Create Account</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Help Section -->
<div class="help-section">
    <div class="help-card">
        <h3><i class="fas fa-question-circle"></i> Need Help?</h3>
        
        <div class="help-items">
            <div class="help-item">
                <h4><i class="fas fa-envelope"></i> Didn't receive email?</h4>
                <ul>
                    <li>Check your spam/junk folder</li>
                    <li>Verify the email address is correct</li>
                    <li>Wait a few minutes and try again</li>
                    <li>Contact support if issues persist</li>
                </ul>
            </div>
            
            <div class="help-item">
                <h4><i class="fas fa-shield-alt"></i> Account Security</h4>
                <ul>
                    <li>Reset links expire after 1 hour</li>
                    <li>Only use official Civil Calculator emails</li>
                    <li>Never share your reset link</li>
                    <li>Contact support if you didn't request reset</li>
                </ul>
            </div>
        </div>
        
        <div class="contact-info">
            <p><i class="fas fa-headset"></i> <strong>Still need help?</strong></p>
            <p>Contact our support team at <a href="mailto:support@civilcalculator.com">support@civilcalculator.com</a></p>
        </div>
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
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.field-message {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.field-message i {
    color: #3b82f6;
}

.security-info {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b;
    border-radius: 10px;
    padding: 20px;
    margin: 25px 0;
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.security-info i {
    color: #d97706;
    font-size: 1.5rem;
    margin-top: 2px;
    flex-shrink: 0;
}

.info-text {
    flex: 1;
}

.info-text strong {
    color: #92400e;
    font-size: 0.9rem;
    display: block;
    margin-bottom: 8px;
}

.info-text ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-text li {
    color: #92400e;
    font-size: 0.85rem;
    margin: 4px 0;
    padding-left: 15px;
    position: relative;
}

.info-text li:before {
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
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 16px 40px;
    font-size: 1.1rem;
    width: 100%;
    justify-content: center;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
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

.result-message.info {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #3b82f6;
}

.auth-footer {
    text-align: center;
    padding: 30px;
    background-color: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.auth-link {
    color: #dc2626;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.auth-link:hover {
    text-decoration: underline;
}

/* Help Section */
.help-section {
    margin-top: 30px;
    max-width: 450px;
    margin-left: auto;
    margin-right: auto;
}

.help-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.help-card h3 {
    margin: 0 0 20px;
    color: #374151;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.help-card h3 i {
    color: #dc2626;
}

.help-items {
    margin-bottom: 25px;
}

.help-item {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.help-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.help-item h4 {
    margin: 0 0 12px;
    color: #1f2937;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.help-item h4 i {
    color: #3b82f6;
    font-size: 0.9rem;
}

.help-item ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.help-item li {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 6px 0;
    padding-left: 15px;
    position: relative;
    line-height: 1.4;
}

.help-item li:before {
    content: "•";
    color: #9ca3af;
    position: absolute;
    left: 0;
}

.contact-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}

.contact-info p {
    margin: 5px 0;
    color: #374151;
    font-size: 0.9rem;
}

.contact-info a {
    color: #dc2626;
    text-decoration: none;
    font-weight: 600;
}

.contact-info a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .auth-container {
        padding: 10px;
    }
    
    .auth-form {
        padding: 25px;
    }
    
    .security-info {
        flex-direction: column;
        text-align: center;
    }
    
    .help-card {
        padding: 20px;
    }
    
    .footer-links {
        gap: 10px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeForgotForm();
});

function initializeForgotForm() {
    // Form submission
    document.getElementById('forgotForm').addEventListener('submit', handleForgotSubmission);
}

async function handleForgotSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('forgotBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('forgotResult');
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-flex';
    resultDiv.style.display = 'none';
    
    try {
        // Collect form data
        const formData = new FormData(form);
        const payload = {
            email: formData.get('email')
        };
        
        // Send forgot password request (using direct endpoint to bypass routing issues)
        const response = await fetch('<?php echo app_base_url('direct_forgot_password.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Success - show email sent message
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Reset Email Sent!</h3>
                <p>${result.message || 'If the email exists in our system, you will receive password reset instructions shortly.'}</p>
                <p><small>Check your inbox (and spam folder) for the password reset link.</small></p>
                <div style="margin-top: 15px;">
                    <a href="<?php echo app_base_url('login'); ?>" class="auth-link" style="color: #065f46;">
                        <i class="fas fa-arrow-left"></i> Back to Sign In
                    </a>
                </div>
            `;
            
            // Clear form
            form.reset();
            
        } else {
            // Error - but don't reveal if email exists for security
            resultDiv.className = 'result-message info';
            resultDiv.innerHTML = `
                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Request Processed</h3>
                <p>If the email address exists in our system, you will receive password reset instructions shortly.</p>
                <p><small>Don't forget to check your spam/junk folder.</small></p>
            `;
        }
        
    } catch (error) {
        // Network or server error
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <h3>Connection Error</h3>
            <p>Unable to connect to server. Please try again later.</p>
        `;
        
        console.error('Forgot password error:', error);
        
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

<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
?>



