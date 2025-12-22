<?php
$page_title = 'Sign In - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';

// Safe Session Start
\App\Services\Security::startSession();

// Get CSRF Token
$csrf_token = \App\Services\Security::generateCsrfToken();

// Captcha Script
if (\App\Services\SettingsService::get('captcha_on_login') == '1') {
    $recaptcha = new \App\Services\RecaptchaService();
    $captchaScript = $recaptcha->getScript();
} else {
    $captchaScript = '';
}
?>
<?php echo $captchaScript; ?>

<style>
    /* Ultra-Premium Login Page Styles */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --accent: #dc2626;
        --success: #10b981;
        --error: #ef4444;
        --dark-bg: #0f172a;
        --card-bg: #1e293b;
        --text-primary: #f1f5f9;
        --text-secondary: #94a3b8;
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
        padding: 20px;
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
    .login-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 1200px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    /* Left Side - Illustration */
    .login-illustration {
        animation: fadeInLeft 1s ease;
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .illustration-content {
        text-align: center;
        color: white;
    }

    .brand-logo {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 30px;
        background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    .brand-logo i {
        font-size: 5rem;
        filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
    }

    .brand-tagline {
        font-size: 1.8rem;
        font-weight: 300;
        margin-bottom: 50px;
        opacity: 0.95;
        line-height: 1.6;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 40px;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 25px;
        transition: all 0.3s ease;
        animation: fadeInUp 0.8s ease backwards;
    }

    .feature-card:nth-child(1) { animation-delay: 0.2s; }
    .feature-card:nth-child(2) { animation-delay: 0.3s; }
    .feature-card:nth-child(3) { animation-delay: 0.4s; }
    .feature-card:nth-child(4) { animation-delay: 0.5s; }

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

    .feature-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-5px);
    }

    .feature-card i {
        font-size: 2rem;
        margin-bottom: 15px;
        display: block;
    }

    .feature-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .feature-card p {
        font-size: 0.9rem;
        opacity: 0.9;
        line-height: 1.5;
    }

    /* Right Side - Login Form */
    .login-card-wrapper {
        animation: fadeInRight 1s ease;
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
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
    }

    /* Social Login */
    .social-login {
        margin-bottom: 30px;
    }

    .social-btn {
        width: 100%;
        padding: 16px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: white;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-weight: 600;
        font-size: 1rem;
        color: #1e293b;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .social-btn:hover {
        border-color: #6366f1;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.15);
    }

    .social-btn i {
        font-size: 1.4rem;
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 30px 0;
        color: #94a3b8;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
    }

    .divider span {
        padding: 0 20px;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 24px;
    }

    #loginForm .form-label {
        display: block;
        font-weight: 700;
        color: #0f172a !important; /* Override global header styles */
        margin-bottom: 12px;
        font-size: 0.875rem;
        letter-spacing: 0.3px;
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

    .password-toggle {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .password-toggle:hover {
        color: #6366f1;
        background: #f1f5f9;
    }

    /* Form Options */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0 30px;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #6366f1;
    }

    .checkbox-wrapper label {
        cursor: pointer;
        font-size: 0.95rem;
        color: #475569;
        user-select: none;
    }

    .forgot-link {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .forgot-link:hover {
        color: #4f46e5;
        text-decoration: underline;
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
        justify-content: center;
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

    /* Trust Indicators */
    .trust-indicators {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #e2e8f0;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #64748b;
        padding: 8px 16px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
    }

    .trust-badge i {
        color: #10b981;
        font-size: 1rem;
    }

    /* Footer */
    .card-footer {
        text-align: center;
        margin-top: 30px;
        color: #64748b;
        font-size: 0.95rem;
    }

    .card-footer a {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .card-footer a:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* Result Message */
    .result-message {
        margin: 20px 0;
        padding: 16px;
        border-radius: 12px;
        font-size: 0.95rem;
        animation: slideDown 0.3s ease;
        display: flex;
        align-items: center;
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

    /* Responsive Design */
    @media (max-width: 1024px) {
        .login-wrapper {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .login-illustration {
            order: 2;
        }

        .login-card-wrapper {
            order: 1;
        }

        .feature-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .ultra-card {
            padding: 35px 25px;
        }

        .card-header h1 {
            font-size: 2rem;
        }

        .brand-logo {
            font-size: 2.5rem;
        }

        .brand-logo i {
            font-size: 3rem;
        }

        .brand-tagline {
            font-size: 1.3rem;
        }

        .feature-grid {
            grid-template-columns: 1fr;
        }

        .trust-indicators {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<div class="ultra-premium-container">
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>
    
    <!-- Mesh Gradient -->
    <div class="mesh-gradient"></div>

    <div class="login-wrapper">
        <!-- Left Side - Illustration -->
        <div class="login-illustration">
            <div class="illustration-content">
                <div class="brand-logo">
                    <i class="fas fa-calculator"></i>
                    <span><?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro')); ?></span>
                </div>
                <p class="brand-tagline">
                    The most powerful engineering calculation platform trusted by professionals worldwide
                </p>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Bank-Level Security</h4>
                        <p>Your data is protected with enterprise-grade encryption</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-rocket"></i>
                        <h4>Lightning Fast</h4>
                        <p>Instant calculations powered by cutting-edge technology</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h4>10,000+ Engineers</h4>
                        <p>Join thousands of professionals using our platform</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h4>Global Access</h4>
                        <p>Work from anywhere, on any device, anytime</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-card-wrapper">
            <div class="ultra-card">
                <div class="card-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to continue your engineering journey</p>
                </div>

                <?php if (\App\Services\SettingsService::get('google_login_enabled') == '1'): ?>
                <!-- Social Login -->
                <div class="social-login">
                    <a href="<?php echo app_base_url('user/login/google'); ?>" class="social-btn" style="text-decoration: none;">
                        <i class="fab fa-google" style="color: #DB4437;"></i>
                        <span>Continue with Google</span>
                    </a>
                </div>

                <div class="divider">
                    <span>or sign in with email</span>
                </div>
                <?php endif; ?>

                <form id="loginForm" action="<?php echo app_base_url('login'); ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <div class="form-group">
                        <label class="form-label" for="username_email">Email or Username</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                id="username_email" 
                                name="username" 
                                class="form-input" 
                                placeholder="Enter your email or username"
                                autocomplete="username"
                                required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required>
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha Widget -->
                    <?php if (\App\Services\SettingsService::get('captcha_on_login') == '1'): 
                        $recaptcha = new \App\Services\RecaptchaService();
                        echo $recaptcha->getWidget();
                    endif; ?>

                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember_me" name="remember_me">
                            <label for="remember_me">Remember me</label>
                        </div>
                        <a href="<?php echo app_base_url('forgot-password'); ?>" class="forgot-link">
                            Forgot password?
                        </a>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="result-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div id="loginResult" class="result-message" style="display: none;"></div>

                    <button type="submit" class="ultra-btn" id="loginBtn">
                        <span class="btn-content">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Sign In</span>
                        </span>
                        <span class="btn-loading">
                            <i class="fas fa-spinner"></i>
                            <span> Signing In...</span>
                        </span>
                    </button>

                    <div class="trust-indicators">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>SSL Secured</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-lock"></i>
                            <span>256-bit Encrypted</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>GDPR Compliant</span>
                        </div>
                    </div>
                </form>

                <div class="card-footer">
                    <p>Don't have an account? <a href="<?php echo app_base_url('register'); ?>">Create one now</a></p>
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
    
    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Form submission
    document.getElementById('loginForm').addEventListener('submit', handleLoginSubmission);
});

async function handleLoginSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('loginBtn');
    const btnContent = submitBtn.querySelector('.btn-content');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('loginResult');
    
    // Show loading state
    submitBtn.disabled = true;
    btnContent.style.display = 'none';
    btnLoading.style.display = 'flex';
    resultDiv.style.display = 'none';
    
    try {
        const formData = new FormData(form);
        const payload = {
            username_email: formData.get('username'),
            password: formData.get('password'),
            remember_me: formData.get('remember_me') ? 1 : 0,
            csrf_token: formData.get('csrf_token')
        };
        
        const response = await fetch('<?php echo app_base_url('login'); ?>', {
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
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> <span>Login successful! Redirecting...</span>';
            resultDiv.style.display = 'flex';
            
            setTimeout(() => {
                window.location.href = result.redirect_url || '/';
            }, 1000);
        } else {
            resultDiv.className = 'result-message error';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> <span>' + (result.error || 'Invalid credentials') + '</span>';
            resultDiv.style.display = 'flex';
        }
    } catch (error) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>Connection error. Please try again.</span>';
        resultDiv.style.display = 'flex';
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