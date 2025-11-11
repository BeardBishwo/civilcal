<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ProCalculator Premium</title>
    
    <!-- Meta tags for SEO and social sharing -->
    <meta name="description" content="Reset your ProCalculator account password - Professional engineering calculator platform">
    <meta name="keywords" content="engineering calculator, professional tools, password reset, forgot password">
    <meta name="author" content="ProCalculator Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Forgot Password - ProCalculator Premium">
    <meta property="og:description" content="Reset your ProCalculator account password">
    <meta property="og:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Forgot Password - ProCalculator Premium">
    <meta property="twitter:description" content="Reset your ProCalculator account password">
    <meta property="twitter:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $viewHelper->themeUrl('assets/favicon.ico') ?>">
    
    <!-- ProCalculator Premium Theme Styles -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/auth.css') ?>">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Additional CSS for forgot password page -->
    <style>
        /* Forgot Password Page Specific Styles */
        .pc-forgot-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pc-gradient-dark);
            position: relative;
            overflow: hidden;
            padding: var(--pc-spacing-lg) 0;
        }

        .pc-forgot-container::before {
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

        .pc-forgot-card {
            width: 100%;
            max-width: 500px;
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

        .pc-forgot-header {
            text-align: center;
            margin-bottom: var(--pc-spacing-2xl);
        }

        .pc-forgot-logo {
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

        .pc-forgot-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--pc-gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--pc-spacing-sm);
        }

        .pc-forgot-subtitle {
            color: var(--pc-text-secondary);
            font-size: 1rem;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .pc-security-info {
            background: var(--pc-gradient-secondary);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-lg);
            margin-bottom: var(--pc-spacing-xl);
            text-align: center;
        }

        .pc-security-info h4 {
            color: white;
            margin-bottom: var(--pc-spacing-sm);
            font-size: 1rem;
        }

        .pc-security-info p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            margin: 0;
        }

        .pc-back-link {
            text-align: center;
            margin-top: var(--pc-spacing-xl);
            padding-top: var(--pc-spacing-lg);
            border-top: 1px solid var(--pc-glass-border);
        }

        .pc-back-link a {
            color: var(--pc-premium);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: var(--pc-spacing-sm);
            transition: all var(--pc-transition-fast);
        }

        .pc-back-link a:hover {
            text-decoration: underline;
            transform: translateX(-2px);
        }

        .pc-email-icon {
            background: var(--pc-gradient-primary);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--pc-spacing-lg);
            font-size: 1.5rem;
            color: white;
            box-shadow: var(--pc-shadow-premium);
        }

        @media (max-width: 480px) {
            .pc-forgot-card {
                margin: var(--pc-spacing-md);
                padding: var(--pc-spacing-xl);
            }
            
            .pc-forgot-title {
                font-size: 2rem;
            }
        }
        
        /* Premium Email Input with Animations */
        .pc-email-input-wrapper {
            position: relative;
            margin-bottom: var(--pc-spacing-xl);
        }
        
        .pc-input-container {
            position: relative;
            margin-top: var(--pc-spacing-sm);
        }
        
        .pc-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pc-premium);
            font-size: 1.125rem;
            z-index: 2;
            transition: all var(--pc-transition-normal);
        }
        
        .pc-premium-input {
            padding-left: 48px !important;
            padding-right: 16px !important;
            border: 2px solid var(--pc-glass-border) !important;
            background: var(--pc-glass) !important;
            transition: all var(--pc-transition-normal) !important;
            position: relative;
            z-index: 1;
        }
        
        .pc-premium-input:focus {
            border-color: var(--pc-premium) !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }
        
        .pc-premium-input:focus + .pc-input-underline {
            transform: scaleX(1);
        }
        
        .pc-premium-input:focus ~ .pc-input-icon {
            color: var(--pc-gold);
            transform: translateY(-50%) scale(1.1);
        }
        
        .pc-input-underline {
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--pc-premium), var(--pc-gold));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 2px;
        }
        
        /* Email Particles Animation */
        .pc-email-particles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
            border-radius: var(--pc-radius-md);
        }
        
        .pc-premium-input:focus ~ .pc-email-particles::before,
        .pc-premium-input:focus ~ .pc-email-particles::after {
            content: '';
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--pc-premium);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 2s ease-out;
        }
        
        .pc-premium-input:focus ~ .pc-email-particles::before {
            left: 20%;
            animation-delay: 0.2s;
        }
        
        .pc-premium-input:focus ~ .pc-email-particles::after {
            left: 70%;
            animation-delay: 0.5s;
        }
        
        @keyframes particleFloat {
            0% {
                bottom: 0;
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            100% {
                bottom: 100%;
                opacity: 0;
                transform: translateY(-20px) scale(0.5);
            }
        }
        
        /* Premium Forgot Button */
        .pc-forgot-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: var(--pc-spacing-lg) var(--pc-spacing-xl);
            min-height: 72px;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
            transition: all var(--pc-transition-normal);
        }
        
        .pc-forgot-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .pc-forgot-btn:hover::before {
            left: 100%;
        }
        
        .pc-forgot-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }
        
        .pc-pulse-icon {
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        /* Loading Animations */
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
        
        .pc-forgot-btn:hover .pc-btn-arrow {
            transform: translateX(4px);
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
</head>
<body>
    <div class="pc-forgot-container" id="main-content">
        <div class="pc-forgot-card pc-card">
            <!-- Header -->
            <div class="pc-forgot-header">
                <div class="pc-forgot-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="pc-forgot-title">Reset Password</h1>
                <p class="pc-forgot-subtitle">Enter your email address and we'll send you a link to reset your password</p>
            </div>

            <!-- Security Information -->
            <div class="pc-security-info">
                <h4><i class="fas fa-shield-alt me-2"></i>Secure Password Reset</h4>
                <p>Your password reset link will be secure and expire in 1 hour for your protection</p>
            </div>

            <!-- Forgot Password Form -->
            <form class="pc-premium-form" id="forgotForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <div class="pc-form-group pc-email-input-wrapper">
                    <label for="email" class="pc-label pc-floating-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <div class="pc-input-container">
                        <span class="pc-input-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="pc-input pc-form-control pc-premium-input" 
                            placeholder="Enter your registered email address"
                            required 
                            autocomplete="email"
                            aria-describedby="email-error"
                        >
                        <span class="pc-input-underline"></span>
                        <span class="pc-email-particles"></span>
                    </div>
                    <div id="email-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <div class="pc-form-group">
                    <button type="submit" class="pc-btn pc-btn-premium pc-btn-lg pc-w-full pc-forgot-btn" id="forgotBtn">
                        <span class="pc-btn-content">
                            <span class="pc-btn-icon pc-pulse-icon">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <span class="pc-btn-text-main">
                                <span class="pc-btn-title">Send Reset Link</span>
                                <span class="pc-btn-subtitle">We'll email you a secure reset link</span>
                            </span>
                            <span class="pc-btn-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </span>
                        <span class="pc-btn-loading pc-hidden">
                            <span class="pc-spinner-premium"></span>
                            <span class="pc-loading-text">Sending reset link<span class="pc-loading-dots"><span>.</span><span>.</span><span>.</span></span></span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Email Icon -->
            <div class="pc-email-icon">
                <i class="fas fa-inbox"></i>
            </div>

            <!-- Back to Login Link -->
            <div class="pc-back-link">
                <a href="<?= $viewHelper->url('login') ?>" class="pc-back-cta">
                    <i class="fas fa-arrow-left"></i>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- ProCalculator Premium Theme Scripts -->
    <script src="/assets/themes/procalculator/js/procalculator-core.js"></script>
    <script src="<?= $viewHelper->themeUrl('assets/js/auth-enhanced.js') ?>"></script>
    
    <!-- Forgot Password Page Specific Script -->
    <script>
        const basePath = "<?= $viewHelper->url('') ?>";
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handler
            document.getElementById('forgotForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    return;
                }
                
                const form = e.target;
                const submitBtn = document.getElementById('forgotBtn');
                const email = document.getElementById('email').value;
                
                // Show loading state
                setButtonLoading(submitBtn, true);
                
                try {
                    // Simulate API call
                    const response = await simulatePasswordReset(email);
                    
                    if (response.success) {
                        showSuccessMessage();
                        form.reset();
                    } else {
                        showError('email', response.message);
                    }
                } catch (error) {
                    showError('email', 'An error occurred. Please try again.');
                } finally {
                    setButtonLoading(submitBtn, false);
                }
            });

            // Simulate password reset API call
            function simulatePasswordReset(email) {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        if (email && email.includes('@')) {
                            resolve({ success: true, message: 'Password reset email sent!' });
                        } else {
                            resolve({ success: false, message: 'Please enter a valid email address.' });
                        }
                    }, 2000);
                });
            }

            function showSuccessMessage() {
                const card = document.querySelector('.pc-forgot-card');
                card.innerHTML = `
                    <div class="pc-success-state">
                        <div class="pc-email-icon" style="background: var(--pc-gradient-primary);">
                            <i class="fas fa-check"></i>
                        </div>
                        <h1 class="pc-forgot-title">Check Your Email</h1>
                        <p class="pc-forgot-subtitle">We've sent a password reset link to your email address. Click the link in the email to reset your password.</p>
                        <div class="pc-form-group" style="margin-top: var(--pc-spacing-xl);">
                            <a href="${basePath}/login" class="pc-btn pc-btn-primary pc-btn-lg pc-w-full">
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

            function setButtonLoading(button, loading) {
                if (loading) {
                    button.disabled = true;
                    button.querySelector('.pc-btn-text').classList.add('pc-hidden');
                    button.querySelector('.pc-btn-loading').classList.remove('pc-hidden');
                } else {
                    button.disabled = false;
                    button.querySelector('.pc-btn-text').classList.remove('pc-hidden');
                    button.querySelector('.pc-btn-loading').classList.add('pc-hidden');
                }
            }

            function validateForm() {
                const email = document.getElementById('email').value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!email) {
                    showError('email', 'Email address is required');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError('email', 'Please enter a valid email address');
                    return false;
                }
                
                clearError('email');
                return true;
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
        });
    </script>
</body>
</html>
