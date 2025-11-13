<?php
$page_title = 'Reset Password - EngiCal Pro';
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
require_once dirname(__DIR__, 4) . '/app/Services/Security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if token is provided
$token = $_GET['token'] ?? '';
$token_valid = false;
$token_error = '';
$user_email = '';

if (empty($token)) {
    $token_error = 'No reset token provided';
} else {
    try {
        $pdo = get_db();
        if ($pdo) {
            // Check if token is valid and not expired
            $stmt = $pdo->prepare("
                SELECT pr.id, pr.user_id, pr.token, pr.expires_at, pr.used_at,
                       u.username, u.email, u.full_name
                FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reset_data) {
                $token_valid = true;
                $user_email = $reset_data['email'];
            } else {
                // Check if token exists but is expired or already used
                $stmt = $pdo->prepare("
                    SELECT token, expires_at, used_at
                    FROM password_resets
                    WHERE token = ?
                    LIMIT 1
                ");
                $stmt->execute([$token]);
                $token_check = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($token_check) {
                    if ($token_check['used_at']) {
                        $token_error = 'This password reset link has already been used. Please request a new password reset.';
                    } elseif ($token_check['expires_at'] <= date('Y-m-d H:i:s')) {
                        $token_error = 'This password reset link has expired. Please request a new password reset.';
                    } else {
                        $token_error = 'Invalid reset token.';
                    }
                } else {
                    $token_error = 'Invalid reset token.';
                }
            }
        }
    } catch (Exception $e) {
        error_log('Token validation error: ' . $e->getMessage());
        $token_error = 'Unable to validate reset token. Please try again.';
    }
}

$csrf_token = Security::generateCsrfToken();
?>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h1><?php echo $token_valid ? 'Create New Password' : 'Invalid Reset Link'; ?></h1>
            <p class="auth-subtitle">
                <?php 
                if ($token_valid) {
                    echo 'Reset password for ' . htmlspecialchars($user_email);
                } else {
                    echo 'Password reset link is invalid or expired';
                }
                ?>
            </p>
        </div>

        <?php if ($token_valid): ?>
        <!-- Password Reset Form -->
        <form id="resetForm" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <!-- Form Fields -->
            <div class="form-section">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-field">
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-control" 
                               placeholder="Enter new password"
                               minlength="8"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <!-- Password Strength Meter -->
                    <div class="password-strength">
                        <div class="strength-bars">
                            <div class="strength-bar" id="strength1"></div>
                            <div class="strength-bar" id="strength2"></div>
                            <div class="strength-bar" id="strength3"></div>
                            <div class="strength-bar" id="strength4"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Password strength</div>
                        <div class="password-requirements" id="passwordReqs">
                            <div class="requirement" data-req="length">
                                <i class="fas fa-times"></i> At least 8 characters
                            </div>
                            <div class="requirement" data-req="uppercase">
                                <i class="fas fa-times"></i> One uppercase letter
                            </div>
                            <div class="requirement" data-req="lowercase">
                                <i class="fas fa-times"></i> One lowercase letter
                            </div>
                            <div class="requirement" data-req="number">
                                <i class="fas fa-times"></i> One number
                            </div>
                            <div class="requirement" data-req="special">
                                <i class="fas fa-times"></i> One special character
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-field">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control" 
                               placeholder="Confirm new password"
                               minlength="8"
                               required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordMatch" class="field-message"></div>
                </div>

                <!-- Security Notice -->
                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    <div class="info-text">
                        <strong>Password Requirements:</strong>
                        <ul>
                            <li>Minimum 8 characters long</li>
                            <li>Mix of uppercase and lowercase letters</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                            <li>Different from your current password</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg" id="resetBtn">
                    <span class="btn-text">
                        <i class="fas fa-key"></i>
                        Update Password
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Updating...
                    </span>
                </button>
            </div>

            <!-- Result Message -->
            <div id="resetResult" class="result-message" style="display: none;"></div>
        </form>

        <?php else: ?>
        <!-- Invalid Token Section -->
        <div class="auth-form">
            <div class="invalid-token">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Reset Link Invalid</h3>
                <p><?php echo htmlspecialchars($token_error); ?></p>
            </div>

            <div class="form-actions">
                <a href="<?php echo app_base_url('forgot-password'); ?>" class="btn btn-primary">
                    <i class="fas fa-redo"></i>
                    Request New Reset Link
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer Links -->
        <div class="auth-footer">
            <div class="footer-links">
                <p><a href="login_enhanced.php" class="auth-link"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
                <p>Don't have an account? <a href="register_enhanced.php" class="auth-link">Create Account</a></p>
            </div>
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
    max-width: 500px;
}

.auth-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
    color: #10b981;
}

.password-strength {
    margin-top: 15px;
}

.strength-bars {
    display: flex;
    gap: 4px;
    margin-bottom: 8px;
}

.strength-bar {
    height: 4px;
    flex: 1;
    background-color: #e5e7eb;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.strength-bar.active.weak {
    background-color: #ef4444;
}

.strength-bar.active.fair {
    background-color: #f59e0b;
}

.strength-bar.active.good {
    background-color: #3b82f6;
}

.strength-bar.active.strong {
    background-color: #10b981;
}

.strength-text {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 10px;
}

.password-requirements {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 8px;
}

.requirement {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    color: #6b7280;
}

.requirement.valid {
    color: #10b981;
}

.requirement i {
    width: 12px;
    text-align: center;
}

.requirement.valid i {
    color: #10b981;
}

.field-message {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.field-message.success {
    color: #10b981;
}

.field-message.error {
    color: #ef4444;
}

.security-info {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 10px;
    padding: 20px;
    margin: 25px 0;
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.security-info i {
    color: #0369a1;
    font-size: 1.5rem;
    margin-top: 2px;
    flex-shrink: 0;
}

.info-text {
    flex: 1;
}

.info-text strong {
    color: #0c4a6e;
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
    color: #0c4a6e;
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
    text-decoration: none;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 16px 40px;
    font-size: 1.1rem;
    width: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
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

.invalid-token {
    text-align: center;
    padding: 40px 20px;
}

.invalid-token i {
    font-size: 4rem;
    color: #ef4444;
    margin-bottom: 20px;
}

.invalid-token h3 {
    color: #991b1b;
    margin: 0 0 15px;
}

.invalid-token p {
    color: #6b7280;
    font-size: 1rem;
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
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.auth-link:hover {
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
    
    .password-requirements {
        grid-template-columns: 1fr;
    }
    
    .footer-links {
        gap: 10px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (<?php echo $token_valid ? 'true' : 'false'; ?>) {
        initializeResetForm();
    }
});

function initializeResetForm() {
    const passwordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthBars = [
        document.getElementById('strength1'),
        document.getElementById('strength2'),
        document.getElementById('strength3'),
        document.getElementById('strength4')
    ];
    const strengthText = document.getElementById('strengthText');
    const requirements = document.querySelectorAll('.requirement');
    const passwordMatch = document.getElementById('passwordMatch');

    // Password strength meter
    passwordInput.addEventListener('input', function() {
        updatePasswordStrength(this.value, strengthBars, strengthText, requirements);
        checkPasswordMatch();
    });

    // Confirm password matching
    confirmInput.addEventListener('input', checkPasswordMatch);

    // Password visibility toggles
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Form submission
    document.getElementById('resetForm').addEventListener('submit', handleResetSubmission);

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;

        if (confirmPassword.length === 0) {
            passwordMatch.innerHTML = '';
            passwordMatch.className = 'field-message';
            return;
        }

        if (password === confirmPassword) {
            passwordMatch.innerHTML = '<i class="fas fa-check"></i> Passwords match';
            passwordMatch.className = 'field-message success';
        } else {
            passwordMatch.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
            passwordMatch.className = 'field-message error';
        }
    }
}

function updatePasswordStrength(password, strengthBars, strengthText, requirements) {
    // Check requirements
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    // Update requirement indicators
    requirements.forEach(req => {
        const reqType = req.dataset.req;
        const icon = req.querySelector('i');
        if (checks[reqType]) {
            req.classList.add('valid');
            icon.className = 'fas fa-check';
        } else {
            req.classList.remove('valid');
            icon.className = 'fas fa-times';
        }
    });

    // Calculate strength score
    let score = 0;
    Object.values(checks).forEach(check => {
        if (check) score++;
    });

    // Update strength bars
    strengthBars.forEach((bar, index) => {
        bar.className = 'strength-bar';
        if (index < score) {
            bar.classList.add('active');
            if (score <= 2) bar.classList.add('weak');
            else if (score === 3) bar.classList.add('fair');
            else if (score === 4) bar.classList.add('good');
            else bar.classList.add('strong');
        }
    });

    // Update strength text
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['#ef4444', '#f59e0b', '#f59e0b', '#3b82f6', '#10b981'];

    strengthText.textContent = texts[score] || 'Password strength';
    strengthText.style.color = colors[score] || '#6b7280';
}

async function handleResetSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('resetBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('resetResult');
    
    // Validate passwords match
    const password = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = '<h3>Password Mismatch</h3><p>New password and confirmation do not match.</p>';
        resultDiv.style.display = 'block';
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-flex';
    resultDiv.style.display = 'none';
    
    try {
        // Collect form data
        const formData = new FormData(form);
        const payload = {
            token: formData.get('token'),
            new_password: formData.get('new_password'),
            confirm_password: formData.get('confirm_password')
        };
        
        // Send reset request
        const response = await fetch('/aec-calculator/api/reset_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Success
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Password Updated Successfully!</h3>
                <p>${result.message || 'Your password has been updated. You can now sign in with your new password.'}</p>
                <div style="margin-top: 20px;">
                    <a href="login_enhanced.php" class="auth-link" style="color: #065f46;">
                        <i class="fas fa-sign-in-alt"></i> Sign In with New Password
                    </a>
                </div>
            `;
            
            // Reset form
            form.reset();
            
            // Scroll to result
            resultDiv.scrollIntoView({ behavior: 'smooth' });
            
        } else {
            // Error
            resultDiv.className = 'result-message error';
            resultDiv.innerHTML = `
                <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Password Update Failed</h3>
                <p>${result.error || 'Unable to update password. Please try again.'}</p>
            `;
            
            resultDiv.style.display = 'block';
            resultDiv.scrollIntoView({ behavior: 'smooth' });
        }
        
    } catch (error) {
        // Network or server error
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <h3>Connection Error</h3>
            <p>Unable to connect to server. Please try again later.</p>
        `;
        
        console.error('Password reset error:', error);
        
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        btnText.style.display = 'inline-flex';
        btnLoading.style.display = 'none';
        resultDiv.style.display = 'block';
    }
}
</script>

<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
?>



