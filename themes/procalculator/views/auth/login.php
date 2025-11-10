<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ProCalculator Premium</title>
    
    <!-- Meta tags for SEO and social sharing -->
    <meta name="description" content="Sign in to ProCalculator - The ultimate professional engineering calculator platform">
    <meta name="keywords" content="engineering calculator, professional tools, login, authentication">
    <meta name="author" content="ProCalculator Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Login - ProCalculator Premium">
    <meta property="og:description" content="Sign in to your ProCalculator account">
    <meta property="og:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Login - ProCalculator Premium">
    <meta property="twitter:description" content="Sign in to your ProCalculator account">
    <meta property="twitter:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $viewHelper->themeUrl('assets/favicon.ico') ?>">
    
    <!-- ProCalculator Premium Theme Styles -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/auth.css') ?>">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Additional CSS for login page -->
    <style>
        /* Login Page Specific Styles */
        .pc-login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pc-gradient-dark);
            position: relative;
            overflow: hidden;
        }

        .pc-login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?= $viewHelper->themeUrl('assets/images/hero-pattern.svg') ?>') repeat;
            opacity: 0.05;
            z-index: 0;
        }

        .pc-login-card {
            width: 100%;
            max-width: 450px;
            margin: var(--pc-spacing-lg);
            position: relative;
            z-index: 1;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--pc-glass-border);
            background: var(--pc-gradient-glass);
            border-radius: var(--pc-radius-2xl);
            padding: var(--pc-spacing-3xl);
            box-shadow: var(--pc-shadow-premium);
            animation: pc-card-appear 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes pc-card-appear {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .pc-login-header {
            text-align: center;
            margin-bottom: var(--pc-spacing-2xl);
        }

        .pc-login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--pc-spacing-lg);
            background: var(--pc-gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: var(--pc-shadow-premium);
        }

        .pc-login-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--pc-gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--pc-spacing-sm);
        }

        .pc-login-subtitle {
            color: var(--pc-text-secondary);
            font-size: 1rem;
            margin-bottom: 0;
        }

        .pc-social-login {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: var(--pc-spacing-md);
            margin-bottom: var(--pc-spacing-xl);
        }

        .pc-social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--pc-spacing-sm);
            padding: var(--pc-spacing-md);
            background: var(--pc-glass);
            border: 1px solid var(--pc-glass-border);
            border-radius: var(--pc-radius-md);
            color: var(--pc-text);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--pc-transition-normal);
            backdrop-filter: blur(10px);
        }

        .pc-social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--pc-premium);
            transform: translateY(-2px);
            box-shadow: var(--pc-shadow-premium);
        }

        .pc-divider {
            display: flex;
            align-items: center;
            margin: var(--pc-spacing-xl) 0;
            color: var(--pc-text-secondary);
            font-size: 0.875rem;
        }

        .pc-divider::before,
        .pc-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--pc-glass-border);
        }

        .pc-divider span {
            padding: 0 var(--pc-spacing-md);
        }

        .pc-demo-accounts {
            background: var(--pc-gradient-secondary);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-lg);
            margin-bottom: var(--pc-spacing-xl);
            text-align: center;
        }

        .pc-demo-accounts h4 {
            color: white;
            margin-bottom: var(--pc-spacing-md);
            font-size: 1rem;
        }

        .pc-demo-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: var(--pc-spacing-sm) var(--pc-spacing-md);
            border-radius: var(--pc-radius-sm);
            margin: var(--pc-spacing-xs);
            cursor: pointer;
            font-size: 0.75rem;
            transition: all var(--pc-transition-fast);
        }

        .pc-demo-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .pc-forgot-password {
            text-align: right;
            margin-top: var(--pc-spacing-sm);
        }

        .pc-forgot-password a {
            color: var(--pc-premium);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color var(--pc-transition-fast);
        }

        .pc-forgot-password a:hover {
            color: var(--pc-gold);
        }

        .pc-register-link {
            text-align: center;
            margin-top: var(--pc-spacing-xl);
            padding-top: var(--pc-spacing-lg);
            border-top: 1px solid var(--pc-glass-border);
        }

        .pc-register-link a {
            color: var(--pc-premium);
            text-decoration: none;
            font-weight: 600;
        }

        .pc-register-link a:hover {
            text-decoration: underline;
        }

        .pc-notification {
            position: fixed;
            top: var(--pc-spacing-lg);
            right: var(--pc-spacing-lg);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform var(--pc-transition-normal);
        }

        .pc-notification.pc-notification-show {
            transform: translateX(0);
        }

        .pc-notification-content {
            background: var(--pc-gradient-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--pc-glass-border);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-md) var(--pc-spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--pc-spacing-sm);
            box-shadow: var(--pc-shadow-premium);
            min-width: 300px;
        }

        .pc-notification-success {
            border-left: 4px solid var(--pc-success);
        }

        .pc-notification-error {
            border-left: 4px solid var(--pc-error);
        }

        .pc-notification-warning {
            border-left: 4px solid var(--pc-warning);
        }

        .pc-notification-info {
            border-left: 4px solid var(--pc-info);
        }

        .pc-password-strength {
            margin-top: var(--pc-spacing-xs);
        }

        .pc-strength-bar {
            height: 4px;
            background: var(--pc-glass-border);
            border-radius: 2px;
            overflow: hidden;
            margin-top: var(--pc-spacing-xs);
        }

        .pc-strength-fill {
            height: 100%;
            transition: all var(--pc-transition-normal);
            border-radius: 2px;
        }

        .pc-strength-weak { width: 25%; background: var(--pc-error); }
        .pc-strength-fair { width: 50%; background: var(--pc-warning); }
        .pc-strength-good { width: 75%; background: var(--pc-info); }
        .pc-strength-strong { width: 100%; background: var(--pc-success); }

        @media (max-width: 480px) {
            .pc-login-card {
                margin: var(--pc-spacing-md);
                padding: var(--pc-spacing-xl);
            }
            
            .pc-login-title {
                font-size: 2rem;
            }
            
            .pc-social-login {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="pc-login-container auth-container" id="main-content">
        <div class="pc-login-card pc-card auth-card">
            <!-- Header -->
            <div class="pc-login-header auth-header">
                <div class="pc-login-logo auth-logo">
                    <i class="fas fa-calculator"></i>
                </div>
                <h1 class="pc-login-title auth-title">Welcome Back</h1>
                <p class="pc-login-subtitle auth-subtitle">Sign in to your ProCalculator account</p>
            </div>

            <!-- Social Login Options -->
            <div class="pc-social-login">
                <a href="#" class="pc-social-btn pc-magnetic" id="google-login">
                    <i class="fab fa-google"></i>
                    <span>Google</span>
                </a>
                <a href="#" class="pc-social-btn pc-magnetic" id="linkedin-login">
                    <i class="fab fa-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
            </div>

            <div class="pc-divider">
                <span>Or continue with email</span>
            </div>

            <!-- Demo Accounts (for development) -->
            <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
            <div class="pc-demo-accounts">
                <h4>Quick Demo Access</h4>
                <button class="pc-demo-btn" data-email="engineer@procalculator.com" data-password="Engineer123!">
                    <i class="fas fa-bolt"></i> Demo User
                </button>
                <button class="pc-demo-btn" data-email="admin@procalculator.com" data-password="Admin123!">
                    <i class class="fas fa-crown"></i> Demo Admin
                </button>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form class="pc-premium-form auth-form" id="loginForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <div class="pc-form-group form-group">
                    <label for="email" class="pc-label form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                        <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="pc-input pc-form-control form-input" 
                        placeholder="Enter your email address"
                        required 
                        autocomplete="email"
                        aria-describedby="email-error"
                    >
                    <div id="email-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <div class="pc-form-group form-group">
                    <label for="password" class="pc-label form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="pc-password-input-container">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="pc-input pc-form-control form-input" 
                            placeholder="Enter your password"
                            required 
                            autocomplete="current-password"
                            minlength="6"
                            aria-describedby="password-error"
                        >
                        <button type="button" class="pc-password-toggle" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="password-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <div class="pc-form-group pc-flex pc-justify-between pc-items-center">
                    <label class="pc-checkbox-label">
                        <input type="checkbox" name="remember_me" class="pc-checkbox">
                        <span class="pc-checkmark"></span>
                        Remember me
                    </label>
                    <div class="pc-forgot-password">
                        <a href="<?= $viewHelper->url('forgot-password') ?>" class="pc-forgot-link">Forgot password?</a>
                    </div>
                </div>

                <div class="pc-form-group form-group">
                    <button type="submit" class="pc-btn pc-btn-premium pc-btn-lg pc-w-full pc-login-btn" id="loginBtn">
                        <span class="pc-btn-content">
                            <span class="pc-btn-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            <span class="pc-btn-text-main">
                                <span class="pc-btn-title">Sign In</span>
                                <span class="pc-btn-subtitle">Access your professional dashboard</span>
                            </span>
                            <span class="pc-btn-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </span>
                        <span class="pc-btn-loading pc-hidden">
                            <span class="pc-spinner-premium"></span>
                            <span class="pc-loading-text">Signing in<span class="pc-loading-dots"><span>.</span><span>.</span><span>.</span></span></span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <div class="pc-register-link">
                <p>Don't have an account? 
                    <a href="<?= $viewHelper->url('register') ?>" class="pc-register-cta">Create Professional Account</a>
                </p>
            </div>
        </div>
    </div>

    <!-- ProCalculator Premium Theme Scripts -->
    <script src="<?= $viewHelper->themeUrl('assets/js/procalculator-core.js') ?>"></script>
    <script src="<?= $viewHelper->themeUrl('assets/js/auth-enhanced.js') ?>"></script>
    
    <!-- Login Page Specific Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup demo login buttons
            document.querySelectorAll('.pc-demo-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const email = this.dataset.email;
                    const password = this.dataset.password;
                    
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                    
                    // Auto-submit form after filling
                    setTimeout(() => {
                        document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                    }, 500);
                });
            });

            // Password visibility toggle
            document.querySelectorAll('.pc-password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.parentNode.querySelector('input[type="password"], input[type="text"]');
                    const icon = this.querySelector('i');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.className = 'fas fa-eye-slash';
                    } else {
                        passwordInput.type = 'password';
                        icon.className = 'fas fa-eye';
                    }
                });
            });

            // Social login handlers
            document.getElementById('google-login').addEventListener('click', function(e) {
                e.preventDefault();
                // Google OAuth implementation
                console.log('Google login clicked');
            });

            document.getElementById('linkedin-login').addEventListener('click', function(e) {
                e.preventDefault();
                // LinkedIn OAuth implementation
                console.log('LinkedIn login clicked');
            });
        });

        // Enhanced form validation
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            } else {
                clearError('email');
            }

            // Password validation
            if (!password || password.length < 6) {
                showError('password', 'Password must be at least 6 characters');
                isValid = false;
            } else {
                clearError('password');
            }

            return isValid;
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            field.classList.add('pc-input-error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            
            field.classList.remove('pc-input-error');
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    </script>

    <style>
        /* Additional styles for form validation and accessibility */
        .pc-input-error {
            border-color: var(--pc-error) !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .pc-error-message {
            color: var(--pc-error);
            font-size: 0.75rem;
            margin-top: var(--pc-spacing-xs);
            display: none;
        }

        .pc-password-input-container {
            position: relative;
        }

        .pc-password-toggle {
            position: absolute;
            right: var(--pc-spacing-md);
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--pc-text-secondary);
            cursor: pointer;
            padding: var(--pc-spacing-xs);
            border-radius: var(--pc-radius-sm);
            transition: all var(--pc-transition-fast);
        }

        .pc-password-toggle:hover {
            color: var(--pc-text);
            background: var(--pc-glass);
        }

        .pc-checkbox-label {
            display: flex;
            align-items: center;
            gap: var(--pc-spacing-sm);
            cursor: pointer;
            font-size: 0.875rem;
        }

        .pc-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--pc-premium);
        }
        
        /* Premium Login Button */
        .pc-login-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--pc-premium), var(--pc-gold));
            border: none;
            padding: var(--pc-spacing-lg) var(--pc-spacing-xl);
            min-height: 72px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
            transition: all var(--pc-transition-normal);
        }
        
        .pc-login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .pc-login-btn:hover::before {
            left: 100%;
        }
        
        .pc-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
        }
        
        .pc-btn-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--pc-spacing-md);
            position: relative;
            z-index: 1;
        }
        
        .pc-btn-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .pc-btn-text-main {
            flex: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .pc-btn-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: white;
            letter-spacing: 0.3px;
        }
        
        .pc-btn-subtitle {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }
        
        .pc-btn-arrow {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: transform var(--pc-transition-fast);
        }
        
        .pc-login-btn:hover .pc-btn-arrow {
            transform: translateX(4px);
        }
        
        .pc-spinner-premium {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite, scanPulse 2s ease-in-out infinite;
            display: inline-block;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes scanPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
            }
        }
        
        .pc-loading-text {
            margin-left: var(--pc-spacing-sm);
            color: white;
            font-weight: 500;
        }
        
        .pc-loading-dots span {
            animation: dotBlink 1.4s infinite;
            opacity: 0;
        }
        
        .pc-loading-dots span:nth-child(1) {
            animation-delay: 0s;
        }
        
        .pc-loading-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .pc-loading-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes dotBlink {
            0%, 20% { opacity: 0; }
            40% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        .pc-btn-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--pc-spacing-sm);
        }
        
        .pc-hidden {
            display: none !important;
        }
    </style>
</body>
</html>
