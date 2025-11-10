<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - ProCalculator Premium</title>
    
    <!-- Meta tags for SEO and social sharing -->
    <meta name="description" content="Create your ProCalculator account - Professional engineering calculator platform">
    <meta name="keywords" content="engineering calculator, professional tools, registration, account creation">
    <meta name="author" content="ProCalculator Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Create Account - ProCalculator Premium">
    <meta property="og:description" content="Join thousands of professional engineers using ProCalculator">
    <meta property="og:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Create Account - ProCalculator Premium">
    <meta property="twitter:description" content="Join thousands of professional engineers using ProCalculator">
    <meta property="twitter:image" content="/themes/procalculator/assets/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- ProCalculator Premium Theme Styles -->
    <link rel="stylesheet" href="/themes/procalculator/assets/css/procalculator-premium.css">
    <link rel="stylesheet" href="/themes/procalculator/assets/css/auth.css">
    
    <!-- Additional CSS for registration page -->
    <style>
        /* Registration Page Specific Styles */
        .pc-register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pc-gradient-dark);
            position: relative;
            overflow-y: auto;
            padding: var(--pc-spacing-lg) 0;
        }

        .pc-register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/themes/procalculator/assets/images/hero-pattern.svg') repeat;
            opacity: 0.03;
            z-index: 0;
        }

        .pc-register-card {
            width: 100%;
            max-width: 600px;
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

        .pc-register-header {
            text-align: center;
            margin-bottom: var(--pc-spacing-2xl);
        }

        .pc-register-logo {
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

        .pc-register-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--pc-gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--pc-spacing-sm);
        }

        .pc-register-subtitle {
            color: var(--pc-text-secondary);
            font-size: 1rem;
            margin-bottom: 0;
        }

        .pc-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--pc-spacing-lg);
        }

        .pc-form-col {
            flex: 1;
        }

        .pc-password-requirements {
            background: var(--pc-gradient-glass);
            border: 1px solid var(--pc-glass-border);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-md);
            margin-top: var(--pc-spacing-sm);
            font-size: 0.875rem;
        }

        .pc-requirement {
            display: flex;
            align-items: center;
            gap: var(--pc-spacing-sm);
            margin-bottom: var(--pc-spacing-xs);
            color: var(--pc-text-secondary);
        }

        .pc-requirement.met {
            color: var(--pc-success);
        }

        .pc-requirement i {
            width: 16px;
            text-align: center;
        }

        .pc-terms-agreement {
            background: var(--pc-gradient-glass);
            border: 1px solid var(--pc-glass-border);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-md);
            margin: var(--pc-spacing-lg) 0;
        }

        .pc-terms-agreement label {
            display: flex;
            align-items: flex-start;
            gap: var(--pc-spacing-sm);
            cursor: pointer;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .pc-terms-agreement input[type="checkbox"] {
            margin-top: 2px;
            accent-color: var(--pc-premium);
        }

        .pc-professional-info {
            background: var(--pc-gradient-secondary);
            border-radius: var(--pc-radius-md);
            padding: var(--pc-spacing-lg);
            margin-bottom: var(--pc-spacing-xl);
            text-align: center;
        }

        .pc-professional-info h4 {
            color: white;
            margin-bottom: var(--pc-spacing-sm);
            font-size: 1rem;
        }

        .pc-professional-info p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            margin: 0;
        }

        .pc-social-register {
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

        .pc-login-link {
            text-align: center;
            margin-top: var(--pc-spacing-xl);
            padding-top: var(--pc-spacing-lg);
            border-top: 1px solid var(--pc-glass-border);
        }

        .pc-login-link a {
            color: var(--pc-premium);
            text-decoration: none;
            font-weight: 600;
        }

        .pc-login-link a:hover {
            text-decoration: underline;
        }

        .pc-username-check {
            position: relative;
            margin-top: var(--pc-spacing-xs);
        }

        .pc-username-status {
            font-size: 0.75rem;
            margin-top: var(--pc-spacing-xs);
            display: flex;
            align-items: center;
            gap: var(--pc-spacing-xs);
        }

        .pc-username-available {
            color: var(--pc-success);
        }

        .pc-username-taken {
            color: var(--pc-error);
        }

        .pc-username-checking {
            color: var(--pc-info);
        }

        @media (max-width: 768px) {
            .pc-form-row {
                grid-template-columns: 1fr;
                gap: var(--pc-spacing-md);
            }
            
            .pc-register-card {
                margin: var(--pc-spacing-md);
                padding: var(--pc-spacing-xl);
            }
            
            .pc-register-title {
                font-size: 2rem;
            }
            
            .pc-social-register {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .pc-register-card {
                margin: var(--pc-spacing-sm);
                padding: var(--pc-spacing-lg);
            }
        }
    </style>
</head>
<body>
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="pc-skip-to-content">Skip to main content</a>
    
    <div class="pc-register-container" id="main-content">
        <div class="pc-register-card pc-card">
            <!-- Header -->
            <div class="pc-register-header">
                <div class="pc-register-logo">
                    <i class="fas fa-calculator"></i>
                </div>
                <h1 class="pc-register-title">Create Account</h1>
                <p class="pc-register-subtitle">Join thousands of professional engineers</p>
            </div>

            <!-- Professional Benefits -->
            <div class="pc-professional-info">
                <h4><i class="fas fa-crown me-2"></i>Professional Account Benefits</h4>
                <p>Unlimited calculations, advanced features, priority support, and premium templates</p>
            </div>

            <!-- Social Registration Options -->
            <div class="pc-social-register">
                <a href="#" class="pc-social-btn pc-magnetic" id="google-register">
                    <i class="fab fa-google"></i>
                    <span>Google</span>
                </a>
                <a href="#" class="pc-social-btn pc-magnetic" id="linkedin-register">
                    <i class="fab fa-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
            </div>

            <div class="pc-divider">
                <span>Or create account with email</span>
            </div>

            <!-- Registration Form -->
            <form class="pc-premium-form" id="registerForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Personal Information -->
                <div class="pc-form-row">
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="first_name" class="pc-label">
                                <i class="fas fa-user me-2"></i>First Name
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="pc-input pc-form-control" 
                                placeholder="Enter your first name"
                                required 
                                autocomplete="given-name"
                                aria-describedby="first_name-error"
                            >
                            <div id="first_name-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="last_name" class="pc-label">
                                <i class="fas fa-user me-2"></i>Last Name
                            </label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                class="pc-input pc-form-control" 
                                placeholder="Enter your last name"
                                required 
                                autocomplete="family-name"
                                aria-describedby="last_name-error"
                            >
                            <div id="last_name-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="pc-form-group">
                    <label for="username" class="pc-label">
                        <i class="fas fa-at me-2"></i>Username
                    </label>
                    <div class="pc-username-check">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="pc-input pc-form-control" 
                            placeholder="Choose a unique username"
                            required 
                            autocomplete="username"
                            minlength="3"
                            maxlength="20"
                            pattern="[a-zA-Z0-9_]+"
                            aria-describedby="username-error username-status"
                        >
                        <div id="username-status" class="pc-username-status" aria-live="polite"></div>
                    </div>
                    <div id="username-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                    <small class="pc-form-text">3-20 characters, letters, numbers, and underscores only</small>
                </div>

                <div class="pc-form-group">
                    <label for="email" class="pc-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="pc-input pc-form-control" 
                        placeholder="Enter your email address"
                        required 
                        autocomplete="email"
                        aria-describedby="email-error"
                    >
                    <div id="email-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <!-- Professional Information -->
                <div class="pc-form-row">
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="company" class="pc-label">
                                <i class="fas fa-building me-2"></i>Company/Organization
                            </label>
                            <input 
                                type="text" 
                                id="company" 
                                name="company" 
                                class="pc-input pc-form-control" 
                                placeholder="Your company name"
                                autocomplete="organization"
                                aria-describedby="company-error"
                            >
                            <div id="company-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="profession" class="pc-label">
                                <i class="fas fa-briefcase me-2"></i>Profession
                            </label>
                            <select id="profession" name="profession" class="pc-input pc-select pc-form-control" aria-describedby="profession-error">
                                <option value="">Select your profession</option>
                                <option value="civil-engineer">Civil Engineer</option>
                                <option value="structural-engineer">Structural Engineer</option>
                                <option value="electrical-engineer">Electrical Engineer</option>
                                <option value="mechanical-engineer">Mechanical Engineer</option>
                                <option value="hvac-engineer">HVAC Engineer</option>
                                <option value="plumbing-engineer">Plumbing Engineer</option>
                                <option value="fire-protection-engineer">Fire Protection Engineer</option>
                                <option value="architect">Architect</option>
                                <option value="contractor">Contractor</option>
                                <option value="consultant">Consultant</option>
                                <option value="project-manager">Project Manager</option>
                                <option value="other">Other</option>
                            </select>
                            <div id="profession-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="pc-form-row">
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="password" class="pc-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="pc-password-input-container">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="pc-input pc-form-control" 
                                    placeholder="Create a strong password"
                                    required 
                                    autocomplete="new-password"
                                    minlength="8"
                                    aria-describedby="password-error password-requirements"
                                >
                                <button type="button" class="pc-password-toggle" aria-label="Show password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="pc-form-col">
                        <div class="pc-form-group">
                            <label for="password_confirm" class="pc-label">
                                <i class="fas fa-lock me-2"></i>Confirm Password
                            </label>
                            <div class="pc-password-input-container">
                                <input 
                                    type="password" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    class="pc-input pc-form-control" 
                                    placeholder="Confirm your password"
                                    required 
                                    autocomplete="new-password"
                                    aria-describedby="password_confirm-error"
                                >
                                <button type="button" class="pc-password-toggle" aria-label="Show password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password_confirm-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="pc-password-requirements" id="password-requirements">
                    <div class="pc-requirement" data-requirement="length">
                        <i class="fas fa-times"></i>
                        <span>At least 8 characters long</span>
                    </div>
                    <div class="pc-requirement" data-requirement="uppercase">
                        <i class="fas fa-times"></i>
                        <span>Contains uppercase letter</span>
                    </div>
                    <div class="pc-requirement" data-requirement="lowercase">
                        <i class="fas fa-times"></i>
                        <span>Contains lowercase letter</span>
                    </div>
                    <div class="pc-requirement" data-requirement="number">
                        <i class="fas fa-times"></i>
                        <span>Contains a number</span>
                    </div>
                    <div class="pc-requirement" data-requirement="special">
                        <i class="fas fa-times"></i>
                        <span>Contains a special character</span>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="pc-terms-agreement">
                    <label>
                        <input type="checkbox" name="terms_accepted" required aria-describedby="terms-error">
                        <div>
                            I agree to the <a href="/terms" target="_blank" style="color: var(--pc-premium);">Terms of Service</a> 
                            and <a href="/privacy" target="_blank" style="color: var(--pc-premium);">Privacy Policy</a>
                            <br>
                            <small class="pc-text-secondary">By creating an account, you agree to our professional use policies</small>
                        </div>
                    </label>
                    <div id="terms-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                </div>

                <!-- Marketing Opt-in -->
                <div class="pc-form-group">
                    <label class="pc-checkbox-label">
                        <input type="checkbox" name="marketing_opt_in">
                        <span class="pc-checkmark"></span>
                        <div>
                            <strong>Stay Updated</strong>
                            <br>
                            <small class="pc-text-secondary">Receive product updates, engineering tips, and industry insights</small>
                        </div>
                    </label>
                </div>

                <div class="pc-form-group">
                    <button type="submit" class="pc-btn pc-btn-primary pc-btn-lg pc-w-full" id="registerBtn">
                        <span class="pc-btn-text">
                            <i class="fas fa-user-plus me-2"></i>
                            Create Professional Account
                        </span>
                        <span class="pc-btn-loading pc-hidden">
                            <span class="pc-spinner"></span>
                            Creating account...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="pc-login-link">
                <p>Already have an account? 
                    <a href="/login" class="pc-login-cta">Sign in to your account</a>
                </p>
            </div>
        </div>
    </div>

    <!-- ProCalculator Premium Theme Scripts -->
    <script src="/themes/procalculator/assets/js/procalculator-core.js"></script>
    <script src="/themes/procalculator/assets/js/auth-enhanced.js"></script>
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <!-- Registration Page Specific Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggles
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

            // Username availability check
            const usernameInput = document.getElementById('username');
            const usernameStatus = document.getElementById('username-status');
            let usernameTimeout;

            usernameInput.addEventListener('input', function() {
                clearTimeout(usernameTimeout);
                const username = this.value.trim();
                
                // Clear previous status
                usernameStatus.innerHTML = '';
                usernameStatus.className = 'pc-username-status';
                
                if (username.length < 3) {
                    return;
                }
                
                // Show checking status
                usernameStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking availability...';
                usernameStatus.className = 'pc-username-status pc-username-checking';
                
                // Debounce the API call
                usernameTimeout = setTimeout(() => {
                    checkUsernameAvailability(username);
                }, 500);
            });

            function checkUsernameAvailability(username) {
                // This would typically make an API call to check username availability
                // For demo purposes, we'll simulate some usernames as taken
                const takenUsernames = ['admin', 'test', 'demo', 'user', 'procalculator'];
                
                setTimeout(() => {
                    if (takenUsernames.includes(username.toLowerCase())) {
                        usernameStatus.innerHTML = '<i class="fas fa-times"></i> Username is already taken';
                        usernameStatus.className = 'pc-username-status pc-username-taken';
                        document.getElementById('username').setCustomValidity('Username is already taken');
                    } else {
                        usernameStatus.innerHTML = '<i class="fas fa-check"></i> Username is available';
                        usernameStatus.className = 'pc-username-status pc-username-available';
                        document.getElementById('username').setCustomValidity('');
                    }
                }, 1000);
            }

            // Password strength checker
            const passwordInput = document.getElementById('password');
            const requirements = document.querySelectorAll('.pc-requirement');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                checkPasswordRequirements(password);
            });

            function checkPasswordRequirements(password) {
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };

                requirements.forEach((met, key) => {
                    const requirement = document.querySelector(`[data-requirement="${key}"]`);
                    const icon = requirement.querySelector('i');
                    
                    if (met) {
                        requirement.classList.add('met');
                        icon.className = 'fas fa-check';
                    } else {
                        requirement.classList.remove('met');
                        icon.className = 'fas fa-times';
                    }
                });
            }

            // Password confirmation checker
            const confirmInput = document.getElementById('password_confirm');
            confirmInput.addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirm = this.value;
                
                if (confirm && password !== confirm) {
                    showError('password_confirm', 'Passwords do not match');
                } else {
                    clearError('password_confirm');
                }
            });

            // Social registration handlers
            document.getElementById('google-register').addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Google registration clicked');
            });

            document.getElementById('linkedin-register').addEventListener('click', function(e) {
                e.preventDefault();
                console.log('LinkedIn registration clicked');
            });
        });
    </script>
</body>
</html>
