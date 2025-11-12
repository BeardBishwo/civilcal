<?php
$page_title = 'Email Verification - EngiCal Pro';
require_once dirname(__DIR__, 4) . '/includes/header.php';
require_once dirname(__DIR__, 4) . '/includes/Security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if token is provided
$token = $_GET['token'] ?? '';
$verification_status = 'pending';
$verification_message = '';
$user_email = '';
$is_already_verified = false;

if (empty($token)) {
    $verification_status = 'error';
    $verification_message = 'No verification token provided';
} else {
    try {
        $pdo = get_db();
        if ($pdo) {
            // Check if verification token is valid
            $stmt = $pdo->prepare("
                SELECT ev.id, ev.user_id, ev.token, ev.email, ev.expires_at, ev.verified_at,
                       u.username, u.email_verified, u.is_active
                FROM email_verifications ev
                JOIN users u ON ev.user_id = u.id
                WHERE ev.token = ? AND ev.expires_at > NOW()
                ORDER BY ev.created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $verification_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($verification_data) {
                if ($verification_data['verified_at']) {
                    $verification_status = 'already';
                    $verification_message = 'Email already verified';
                    $user_email = $verification_data['email'];
                    $is_already_verified = true;
                } else {
                    // Verify the email
                    $pdo->beginTransaction();

                    // Update user's email verified status
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET email_verified = 1, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$verification_data['user_id']]);

                    // Mark verification token as used
                    $stmt = $pdo->prepare("
                        UPDATE email_verifications 
                        SET verified_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$verification_data['id']]);

                    // Log email verification
                    Security::logSecurityEvent('email_verified', [
                        'user_id' => $verification_data['user_id'],
                        'email' => $verification_data['email'],
                        'token' => $token,
                        'ip' => $_SERVER['REMOTE_ADDR']
                    ]);

                    $pdo->commit();

                    $verification_status = 'success';
                    $verification_message = 'Email verified successfully!';
                    $user_email = $verification_data['email'];

                    // Set session variables if user is logged in
                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $verification_data['user_id']) {
                        $_SESSION['email_verified'] = 1;
                    }

                }
            } else {
                // Check if token exists but is expired
                $stmt = $pdo->prepare("
                    SELECT token, expires_at, verified_at
                    FROM email_verifications
                    WHERE token = ?
                    LIMIT 1
                ");
                $stmt->execute([$token]);
                $token_check = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($token_check) {
                    if ($token_check['verified_at']) {
                        $verification_status = 'already';
                        $verification_message = 'This verification link has already been used.';
                    } elseif ($token_check['expires_at'] <= date('Y-m-d H:i:s')) {
                        $verification_status = 'expired';
                        $verification_message = 'This verification link has expired. Please request a new verification email.';
                    } else {
                        $verification_status = 'error';
                        $verification_message = 'Invalid verification token.';
                    }
                } else {
                    $verification_status = 'error';
                    $verification_message = 'Invalid verification token.';
                }
            }
        }
    } catch (Exception $e) {
        error_log('Email verification error: ' . $e->getMessage());
        $verification_status = 'error';
        $verification_message = 'Unable to verify email. Please try again.';
    }
}

$csrf_token = Security::generateCsrfToken();
?>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <?php if ($verification_status === 'success'): ?>
                <h1><i class="fas fa-check-circle"></i> Email Verified!</h1>
                <p class="auth-subtitle">Welcome to EngiCal Pro - Your email has been confirmed</p>
            <?php elseif ($verification_status === 'already'): ?>
                <h1><i class="fas fa-info-circle"></i> Already Verified</h1>
                <p class="auth-subtitle">Your email address is already verified</p>
            <?php elseif ($verification_status === 'expired'): ?>
                <h1><i class="fas fa-clock"></i> Verification Expired</h1>
                <p class="auth-subtitle">Your verification link has expired</p>
            <?php else: ?>
                <h1><i class="fas fa-exclamation-triangle"></i> Verification Failed</h1>
                <p class="auth-subtitle">There was a problem with your verification</p>
            <?php endif; ?>
        </div>

        <!-- Verification Content -->
        <div class="auth-form">
            <?php if ($verification_status === 'success'): ?>
                <!-- Success State -->
                <div class="verification-success">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Email Successfully Verified!</h2>
                    <p>Thank you for verifying your email address. Your EngiCal Pro account is now fully activated.</p>
                    
                    <?php if (!empty($user_email)): ?>
                    <div class="account-info">
                        <strong>Verified Email:</strong><br>
                        <span class="verified-email"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="next-steps">
                        <h3><i class="fas fa-rocket"></i> What's Next?</h3>
                        <ul>
                            <li>Access all professional engineering calculators</li>
                            <li>Save and manage your calculation history</li>
                            <li>Create favorites for quick access</li>
                            <li>Generate professional reports</li>
                            <li>Join thousands of engineers worldwide</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <a href="profile.php" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i>
                            Go to Dashboard
                        </a>
                        <a href="civil.php" class="btn btn-secondary">
                            <i class="fas fa-calculator"></i>
                            Try Calculators
                        </a>
                    </div>
                </div>

            <?php elseif ($verification_status === 'already'): ?>
                <!-- Already Verified State -->
                <div class="verification-already">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h2>Email Already Verified</h2>
                    <p>Good news! Your email address <strong><?php echo htmlspecialchars($user_email); ?></strong> has already been verified.</p>
                    
                    <div class="account-status">
                        <div class="status-item verified">
                            <i class="fas fa-check"></i>
                            <span>Email Verified</span>
                        </div>
                        <div class="status-item">
                            <i class="fas fa-user"></i>
                            <span>Account Active</span>
                        </div>
                        <div class="status-item">
                            <i class="fas fa-calculator"></i>
                            <span>Full Access</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="profile.php" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i>
                            Go to Dashboard
                        </a>
                    </div>
                </div>

            <?php elseif ($verification_status === 'expired'): ?>
                <!-- Expired State -->
                <div class="verification-expired">
                    <div class="warning-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h2>Verification Link Expired</h2>
                    <p>The verification link you used has expired. This is a security measure to protect your account.</p>
                    
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <div class="info-text">
                            <strong>Why links expire:</strong>
                            <ul>
                                <li>Security protection against unused links</li>
                                <li>Prevention of unauthorized access</li>
                                <li>Ensuring email addresses remain current</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php if (!empty($user_email)): ?>
                        <button class="btn btn-primary" onclick="resendVerification('<?php echo htmlspecialchars($user_email); ?>')">
                            <i class="fas fa-redo"></i>
                            Resend Verification Email
                        </button>
                        <?php endif; ?>
                        <a href="login.php" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i>
                            Back to Sign In
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Error State -->
                <div class="verification-error">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2>Verification Failed</h2>
                    <p><?php echo htmlspecialchars($verification_message); ?></p>
                    
                    <div class="troubleshooting">
                        <h3><i class="fas fa-tools"></i> Troubleshooting</h3>
                        <ul>
                            <li>Check if you have the correct verification link</li>
                            <li>Make sure the link hasn't been used before</li>
                            <li>Verify the link hasn't expired (24 hour limit)</li>
                            <li>Check your email for a newer verification link</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Back to Sign In
                        </a>
                        <a href="register.php" class="btn btn-secondary">
                            <i class="fas fa-user-plus"></i>
                            Create New Account
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <div class="footer-links">
                <p><a href="login.php" class="auth-link"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
                <p>Need help? <a href="contact.php" class="auth-link">Contact Support</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Resend Verification Result -->
<div id="resendResult" class="result-message" style="display: none;"></div>

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
    max-width: 600px;
}

.auth-header {
    text-align: center;
    padding: 40px 30px;
    color: white;
}

.auth-header.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.auth-header.already {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.auth-header.expired {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.auth-header.error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.auth-header h1 {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.auth-header i {
    font-size: 2.5rem;
}

.auth-subtitle {
    margin: 15px 0 0;
    font-size: 1rem;
    opacity: 0.9;
}

.auth-form {
    padding: 40px;
}

.verification-success, .verification-already, .verification-expired, .verification-error {
    text-align: center;
}

.success-icon, .info-icon, .warning-icon, .error-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.success-icon {
    color: #10b981;
}

.info-icon {
    color: #3b82f6;
}

.warning-icon {
    color: #f59e0b;
}

.error-icon {
    color: #ef4444;
}

.verification-success h2, .verification-already h2, .verification-expired h2, .verification-error h2 {
    color: #1f2937;
    margin: 0 0 15px;
}

.verification-success p, .verification-already p, .verification-expired p, .verification-error p {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0 0 25px;
    line-height: 1.6;
}

.account-info {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 10px;
    padding: 20px;
    margin: 25px 0;
}

.account-info strong {
    color: #0c4a6e;
    display: block;
    margin-bottom: 8px;
}

.verified-email {
    color: #0369a1;
    font-weight: 600;
    font-size: 1.1rem;
}

.next-steps {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 25px;
    margin: 25px 0;
    text-align: left;
}

.next-steps h3 {
    color: #1f2937;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.next-steps h3 i {
    color: #4f46e5;
}

.next-steps ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.next-steps li {
    color: #374151;
    margin: 10px 0;
    padding-left: 25px;
    position: relative;
    line-height: 1.5;
}

.next-steps li:before {
    content: "✓";
    color: #10b981;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.account-status {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 25px 0;
    flex-wrap: wrap;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: #f3f4f6;
    border-radius: 8px;
    color: #6b7280;
}

.status-item.verified {
    background: #d1fae5;
    color: #065f46;
}

.status-item i {
    color: inherit;
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
    text-align: left;
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
    content: "•";
    color: #d97706;
    position: absolute;
    left: 0;
}

.troubleshooting {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 25px;
    margin: 25px 0;
    text-align: left;
}

.troubleshooting h3 {
    color: #1f2937;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.troubleshooting h3 i {
    color: #6b7280;
}

.troubleshooting ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.troubleshooting li {
    color: #6b7280;
    margin: 8px 0;
    padding-left: 20px;
    position: relative;
    line-height: 1.5;
}

.troubleshooting li:before {
    content: "•";
    color: #9ca3af;
    position: absolute;
    left: 0;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin: 30px 0;
    flex-wrap: wrap;
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
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 15px 30px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
    padding: 15px 30px;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
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
    color: #4f46e5;
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
    
    .account-status {
        flex-direction: column;
        align-items: center;
    }
    
    .security-info {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .footer-links {
        gap: 10px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
function resendVerification(email) {
    fetch('api/resend_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('resendResult');
        
        if (data.success) {
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <strong>Verification Email Sent!</strong><br>
                A new verification email has been sent to ${email}. Please check your inbox and spam folder.
            `;
        } else {
            resultDiv.className = 'result-message error';
            resultDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <strong>Failed to Send Email</strong><br>
                ${data.error || 'Please try again later or contact support.'}
            `;
        }
        
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        console.error('Error:', error);
        const resultDiv = document.getElementById('resendResult');
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Network Error</strong><br>
            Please check your connection and try again.
        `;
        resultDiv.style.display = 'block';
    });
}
</script>

<?php
require_once dirname(__DIR__, 4) . '/includes/footer.php';
?>
