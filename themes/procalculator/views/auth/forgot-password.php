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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- ProCalculator Premium Theme Styles -->
    <link rel="stylesheet" href="/themes/procalculator/assets/css/procalculator-premium.css">
    <link rel="stylesheet" href="/themes/procalculator/assets/css/auth.css">
    
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
            background: url('/themes/procalculator/assets/images/hero-pattern.svg') repeat;
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
    </style>
</head>
<body>
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="pc-skip-to-content">Skip to main content</a>
    
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
                
                <div class="pc-form-group">
                    <label for="email" class="pc-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="pc-input pc-form-control" 
                        placeholder="Enter your registered email address"
                        required 
                        autocomplete="email"
                        aria-describedby="email-error"
                    >
                    <div id="email-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <div class="pc-form-group">
                    <button type="submit" class="pc-btn pc-btn-primary pc-btn-lg pc-w-full" id="forgotBtn">
                        <span class="pc-btn-text">
                            <i class="fas fa-paper-plane me-2"></i>
                            Send Reset Link
                        </span>
                        <span class="pc-btn-loading pc-hidden">
                            <span class="pc-spinner"></span>
                            Sending reset link...
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
                <a href="/login" class="pc-back-cta">
                    <i class="fas fa-arrow-left"></i>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- ProCalculator Premium Theme Scripts -->
    <script src="/themes/procalculator/assets/js/procalculator-core.js"></script>
    <script src="/themes/procalculator/assets/js/auth-enhanced.js"></script>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <!-- Forgot Password Page Specific Script -->
    <script>
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
                            <a href="/login" class="pc-btn pc-btn-primary pc-btn-lg pc-w-full">
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
