<?php
$page_title = 'Reset Password - Civil Calculator';
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = \App\Services\Security::generateCsrfToken();
?>

<style>
    /* Ultra-Premium Forgot Password Page Styles */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --accent: #dc2626;
        --success: #10b981;
        --error: #ef4444;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow-x: hidden;
    }

    /* Ultra-Premium Container */
    .ultra-premium-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    /* Animated Particle Background */
    .particles {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: float-particle 20s infinite;
    }

    @keyframes float-particle {
        0%, 100% {
            transform: translate(0, 0) scale(1);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translate(var(--tx), var(--ty)) scale(0);
            opacity: 0;
        }
    }

    /* Mesh Gradient Overlay */
    .mesh-gradient {
        position: absolute;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(at 20% 30%, rgba(99, 102, 241, 0.3) 0px, transparent 50%),
            radial-gradient(at 80% 70%, rgba(139, 92, 246, 0.3) 0px, transparent 50%),
            radial-gradient(at 50% 50%, rgba(220, 38, 38, 0.2) 0px, transparent 50%);
        filter: blur(60px);
        z-index: 0;
    }

    /* Main Content */
    .forgot-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 600px;
        animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ultra-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(30px);
        border-radius: 32px;
        padding: 50px;
        box-shadow: 
            0 50px 100px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
    }

    .ultra-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #dc2626, #6366f1);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: 0% 0%; }
        100% { background-position: 200% 0%; }
    }

    .card-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .header-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 3rem;
        color: white;
        box-shadow: 
            0 20px 40px rgba(99, 102, 241, 0.4),
            0 0 0 8px rgba(99, 102, 241, 0.1);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 
                0 20px 40px rgba(99, 102, 241, 0.4),
                0 0 0 8px rgba(99, 102, 241, 0.1);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 
                0 25px 50px rgba(99, 102, 241, 0.5),
                0 0 0 12px rgba(99, 102, 241, 0.15);
        }
    }

    .card-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 12px;
    }

    .card-header p {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.6;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 30px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .input-group {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 16px 20px 16px 55px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8fafc;
    }

    .form-input:focus {
        outline: none;
        border-color: #6366f1;
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }

    .input-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .form-input:focus ~ .input-icon {
        color: #6366f1;
        transform: translateY(-50%) scale(1.1);
    }

    .field-hint {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .field-hint i {
        color: #3b82f6;
    }

    /* Security Info Box */
    .security-info {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #bae6fd;
        border-radius: 16px;
        padding: 25px;
        margin: 30px 0;
    }

    .security-info h4 {
        color: #0c4a6e;
        font-size: 1rem;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
    }

    .security-info h4 i {
        color: #0369a1;
        font-size: 1.2rem;
    }

    .security-info ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 10px;
    }

    .security-info li {
        color: #0c4a6e;
        font-size: 0.9rem;
        padding-left: 25px;
        position: relative;
        line-height: 1.5;
    }

    .security-info li:before {
        content: "✓";
        color: #059669;
        font-weight: bold;
        font-size: 1.1rem;
        position: absolute;
        left: 0;
    }

    /* Ultra-Premium Button */
    .ultra-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        background-size: 200% 100%;
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 
            0 10px 30px rgba(99, 102, 241, 0.4),
            0 0 0 0 rgba(99, 102, 241, 0.5);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .ultra-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .ultra-btn:hover {
        background-position: 100% 0;
        transform: translateY(-3px);
        box-shadow: 
            0 15px 40px rgba(99, 102, 241, 0.5),
            0 0 0 4px rgba(99, 102, 241, 0.2);
    }

    .ultra-btn:hover::before {
        left: 100%;
    }

    .ultra-btn:active {
        transform: translateY(-1px);
    }

    .ultra-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-loading {
        display: none;
    }

    .btn-loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Result Message */
    .result-message {
        margin: 20px 0;
        padding: 16px;
        border-radius: 12px;
        font-size: 0.95rem;
        animation: slideDown 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .result-message.success {
        background: #d1fae5;
        color: #065f46;
        border: 2px solid #10b981;
    }

    .result-message.error {
        background: #fee2e2;
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    .result-message.info {
        background: #dbeafe;
        color: #1e40af;
        border: 2px solid #3b82f6;
    }

    .result-message i {
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Footer Links */
    .card-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e2e8f0;
    }

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-links a {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .footer-links a:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    .footer-links a i {
        font-size: 0.9rem;
    }

    /* Help Section */
    .help-section {
        margin-top: 30px;
        animation: fadeInUp 0.8s ease 0.2s backwards;
    }

    .help-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .help-card h3 {
        margin: 0 0 25px;
        color: #374151;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
    }

    .help-card h3 i {
        color: #6366f1;
        font-size: 1.5rem;
    }

    .help-items {
        display: grid;
        gap: 25px;
    }

    .help-item {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .help-item h4 {
        margin: 0 0 12px;
        color: #1f2937;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .help-item h4 i {
        color: #3b82f6;
        font-size: 1rem;
    }

    .help-item ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 8px;
    }

    .help-item li {
        color: #6b7280;
        font-size: 0.9rem;
        padding-left: 20px;
        position: relative;
        line-height: 1.5;
    }

    .help-item li:before {
        content: "•";
        color: #9ca3af;
        position: absolute;
        left: 0;
        font-size: 1.2rem;
    }

    /* Responsive Design */
    @media (max-width: 640px) {
        .ultra-card {
            padding: 35px 25px;
        }

        .card-header h1 {
            font-size: 2rem;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .help-card {
            padding: 25px 20px;
        }
    }
</style>

<div class="ultra-premium-container">
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>
    
    <!-- Mesh Gradient -->
    <div class="mesh-gradient"></div>

    <div class="forgot-wrapper">
        <div class="ultra-card">
            <div class="card-header">
                <div class="header-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Reset Password</h1>
                <p>Enter your email address and we'll send you a secure link to reset your password</p>
            </div>

            <form id="forgotForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="Enter your registered email"
                            autocomplete="email"
                            required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    <div class="field-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>We'll send a secure password reset link to this email</span>
                    </div>
                </div>

                <div class="security-info">
                    <h4>
                        <i class="fas fa-shield-alt"></i>
                        Password Reset Security
                    </h4>
                    <ul>
                        <li>Reset link expires in 1 hour</li>
                        <li>Link can only be used once</li>
                        <li>Email verification may be required</li>
                        <li>New password must meet security requirements</li>
                    </ul>
                </div>

                <div id="forgotResult" class="result-message" style="display: none;"></div>

                <button type="submit" class="ultra-btn" id="forgotBtn">
                    <span class="btn-content">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send Reset Link</span>
                    </span>
                    <span class="btn-loading">
                        <i class="fas fa-spinner"></i>
                        <span>Sending...</span>
                    </span>
                </button>
            </form>

            <div class="card-footer">
                <div class="footer-links">
                    <a href="<?php echo app_base_url('login'); ?>">
                        <i class="fas fa-arrow-left"></i>
                        Back to Sign In
                    </a>
                    <a href="<?php echo app_base_url('register'); ?>">
                        Don't have an account? Create one
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="help-section">
            <div class="help-card">
                <h3>
                    <i class="fas fa-question-circle"></i>
                    Need Help?
                </h3>
                
                <div class="help-items">
                    <div class="help-item">
                        <h4>
                            <i class="fas fa-envelope"></i>
                            Didn't receive email?
                        </h4>
                        <ul>
                            <li>Check your spam/junk folder</li>
                            <li>Verify the email address is correct</li>
                            <li>Wait a few minutes and try again</li>
                            <li>Contact support if issues persist</li>
                        </ul>
                    </div>
                    
                    <div class="help-item">
                        <h4>
                            <i class="fas fa-shield-alt"></i>
                            Account Security
                        </h4>
                        <ul>
                            <li>Reset links expire after 1 hour</li>
                            <li>Only use official Civil Calculator emails</li>
                            <li>Never share your reset link</li>
                            <li>Contact support if you didn't request reset</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Create particle animation
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const startX = Math.random() * 100;
        const startY = Math.random() * 100;
        const endX = startX + (Math.random() - 0.5) * 100;
        const endY = startY - Math.random() * 100;
        
        particle.style.left = startX + '%';
        particle.style.top = startY + '%';
        particle.style.setProperty('--tx', endX + 'vw');
        particle.style.setProperty('--ty', endY + 'vh');
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (15 + Math.random() * 10) + 's';
        
        particlesContainer.appendChild(particle);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    document.getElementById('forgotForm').addEventListener('submit', handleForgotSubmission);
});

async function handleForgotSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('forgotBtn');
    const btnContent = submitBtn.querySelector('.btn-content');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('forgotResult');
    
    // Show loading state
    submitBtn.disabled = true;
    btnContent.style.display = 'none';
    btnLoading.style.display = 'flex';
    resultDiv.style.display = 'none';
    
    try {
        const formData = new FormData(form);
        const payload = {
            email: formData.get('email')
        };
        
        // Send forgot password request
        const response = await fetch('<?php echo app_base_url('api/forgot-password'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Reset Email Sent!</strong><br>
                    <small>Check your inbox for password reset instructions.</small>
                </div>
            `;
            form.reset();
        } else {
            // For security, show generic message
            resultDiv.className = 'result-message info';
            resultDiv.innerHTML = `
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Request Processed</strong><br>
                    <small>If the email exists in our system, you will receive reset instructions shortly.</small>
                </div>
            `;
        }
        
        resultDiv.style.display = 'flex';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
    } catch (error) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Connection Error</strong><br>
                <small>Unable to connect to server. Please try again later.</small>
            </div>
        `;
        resultDiv.style.display = 'flex';
        console.error('Forgot password error:', error);
    } finally {
        submitBtn.disabled = false;
        btnContent.style.display = 'flex';
        btnLoading.style.display = 'none';
    }
}
</script>

<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
?>
