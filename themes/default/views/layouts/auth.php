<!DOCTYPE html>
<html lang="en" class="auth-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Authentication - Bishwo Calculator</title>
    
    <!-- Security Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    <!-- CSP and Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo $base_url; ?>assets/images/applogo.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php
    // Load theme styles
    if (isset($theme) && method_exists($theme, 'loadThemeStyles')) {
        $theme->loadThemeStyles();
    }
    ?>
</head>
<body class="auth-body">
    <!-- Auth Background -->
    <div class="auth-background">
        <div class="bg-pattern"></div>
        <div class="bg-overlay"></div>
    </div>
    
    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Auth Side Panel -->
        <div class="auth-side-panel">
            <div class="panel-content">
                <div class="brand-section">
                    <img src="<?php echo $base_url; ?>assets/images/applogo.png" alt="Bishwo Calculator" class="brand-logo" onerror="this.style.display='none'">
                    <h1 class="brand-title">Bishwo Calculator</h1>
                    <p class="brand-subtitle">Professional Engineering Tools</p>
                </div>
                
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Precision Calculators</h3>
                            <p>Advanced engineering calculations for all disciplines</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Secure & Reliable</h3>
                            <p>Enterprise-grade security for your sensitive data</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Project Analytics</h3>
                            <p>Comprehensive reporting and analytics tools</p>
                        </div>
                    </div>
                </div>
                
                <div class="stats-section">
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Engineers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Calculators</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Auth Form Panel -->
        <div class="auth-form-panel">
            <div class="form-container">
                <!-- Form Header -->
                <div class="form-header">
                    <h2 class="form-title"><?php echo $auth_title ?? 'Welcome Back'; ?></h2>
                    <p class="form-subtitle"><?php echo $auth_subtitle ?? 'Sign in to your account to continue'; ?></p>
                </div>
                
                <!-- Auth Alerts -->
                <?php if (isset($_SESSION['auth_success'])): ?>
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $_SESSION['auth_success']; unset($_SESSION['auth_success']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['auth_error'])): ?>
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $_SESSION['auth_error']; unset($_SESSION['auth_error']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['auth_warning'])): ?>
                <div class="auth-alert auth-alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo $_SESSION['auth_warning']; unset($_SESSION['auth_warning']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['auth_info'])): ?>
                <div class="auth-alert auth-alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span><?php echo $_SESSION['auth_info']; unset($_SESSION['auth_info']); ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Main Content -->
                <div class="auth-content">
                    <?php echo $content ?? ''; ?>
                </div>
                
                <!-- Form Footer -->
                <div class="form-footer">
                    <div class="auth-links">
                        <?php if (isset($show_signup_link) && $show_signup_link): ?>
                            <p>Don't have an account? <a href="<?php echo $base_url; ?>auth/register.php" class="auth-link">Sign up</a></p>
                        <?php endif; ?>
                        
                        <?php if (isset($show_login_link) && $show_login_link): ?>
                            <p>Already have an account? <a href="<?php echo $base_url; ?>auth/login.php" class="auth-link">Sign in</a></p>
                        <?php endif; ?>
                        
                        <?php if (isset($show_forgot_password) && $show_forgot_password): ?>
                            <p><a href="<?php echo $base_url; ?>auth/forgot-password.php" class="auth-link">Forgot password?</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="back-to-home">
                <a href="<?php echo $base_url; ?>" class="home-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <!-- Security Footer -->
    <div class="auth-footer">
        <div class="footer-content">
            <div class="security-badges">
                <div class="security-badge">
                    <i class="fas fa-lock"></i>
                    <span>SSL Encrypted</span>
                </div>
                <div class="security-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>GDPR Compliant</span>
                </div>
                <div class="security-badge">
                    <i class="fas fa-certificate"></i>
                    <span>ISO 27001</span>
                </div>
            </div>
            
            <div class="footer-links">
                <a href="<?php echo $base_url; ?>privacy.php">Privacy Policy</a>
                <a href="<?php echo $base_url; ?>terms.php">Terms of Service</a>
                <a href="<?php echo $base_url; ?>contact.php">Support</a>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Bishwo Calculator. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <!-- Security Scripts -->
    <script>
        // Prevent right-click and developer tools
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        document.addEventListener('keydown', function(e) {
            // Prevent F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
            }
        });
        
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.auth-alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            });
        }, 5000);
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const inputs = form.querySelectorAll('input[required]');
                    let isValid = true;
                    
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            showFieldError(input, 'This field is required');
                            isValid = false;
                        } else {
                            clearFieldError(input);
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
            });
        });
        
        function showFieldError(field, message) {
            clearFieldError(field);
            field.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            
            field.parentNode.appendChild(errorDiv);
        }
        
        function clearFieldError(field) {
            field.classList.remove('error');
            const errorDiv = field.parentNode.querySelector('.field-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
        
        // Security: Logout on page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Optional: Implement auto-logout on tab switch
                console.log('Tab hidden - security check');
            }
        });
        
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    
    <style>
        /* Auth Theme Variables */
        :root {
            --auth-primary: #2563eb;
            --auth-primary-dark: #1d4ed8;
            --auth-primary-light: #3b82f6;
            --auth-secondary: #64748b;
            --auth-success: #10b981;
            --auth-warning: #f59e0b;
            --auth-error: #ef4444;
            --auth-info: #3b82f6;
            
            --auth-bg: #f8fafc;
            --auth-bg-light: #ffffff;
            --auth-bg-dark: #1e293b;
            
            --auth-text: #1e293b;
            --auth-text-light: #64748b;
            --auth-text-muted: #94a3b8;
            --auth-text-white: #ffffff;
            
            --auth-border: #e2e8f0;
            --auth-border-light: #f1f5f9;
            --auth-border-focus: var(--auth-primary);
            
            --auth-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --auth-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --auth-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dark {
            --auth-bg: #0f172a;
            --auth-bg-light: #1e293b;
            --auth-bg-dark: #020617;
            --auth-text: #f8fafc;
            --auth-text-light: #cbd5e1;
            --auth-text-muted: #94a3b8;
            --auth-border: #334155;
            --auth-border-light: #475569;
        }
        
        /* Auth Layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .auth-body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--auth-bg);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        .auth-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }
        
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, var(--auth-primary) 1px, transparent 1px),
                radial-gradient(circle at 75% 75%, var(--auth-primary-light) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            opacity: 0.03;
        }
        
        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--auth-primary) 0%, var(--auth-primary-dark) 100%);
            opacity: 0.9;
        }
        
        .auth-container {
            display: flex;
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            background: var(--auth-bg-light);
            box-shadow: var(--auth-shadow-xl);
            border-radius: 0;
            overflow: hidden;
        }
        
        /* Auth Side Panel */
        .auth-side-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--auth-primary) 0%, var(--auth-primary-dark) 100%);
            color: var(--auth-text-white);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-side-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        
        .panel-content {
            position: relative;
            z-index: 1;
        }
        
        .brand-section {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .brand-logo {
            height: 64px;
            width: auto;
            margin-bottom: 1rem;
        }
        
        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-subtitle {
            font-size: 1.125rem;
            opacity: 0.8;
            font-weight: 300;
        }
        
        .features-list {
            margin-bottom: 3rem;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }
        
        .feature-text h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .feature-text p {
            opacity: 0.8;
            line-height: 1.5;
        }
        
        .stats-section {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            background: linear-gradient(45deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Auth Form Panel */
        .auth-form-panel {
            flex: 1;
            background: var(--auth-bg-light);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .form-container {
            flex: 1;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--auth-text);
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: var(--auth-text-light);
            font-size: 1rem;
        }
        
        /* Auth Alerts */
        .auth-alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            font-size: 0.875rem;
            transition: opacity 0.3s;
        }
        
        .auth-alert-success {
            background: #dcfce7;
            color: #166534;
            border-color: var(--auth-success);
        }
        
        .auth-alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: var(--auth-error);
        }
        
        .auth-alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-color: var(--auth-warning);
        }
        
        .auth-alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-color: var(--auth-info);
        }
        
        /* Auth Content */
        .auth-content {
            flex: 1;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: var(--auth-text);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--auth-border);
            border-radius: 0.5rem;
            font-size: 1rem;
            color: var(--auth-text);
            background: var(--auth-bg-light);
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--auth-border-focus);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-input.error {
            border-color: var(--auth-error);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .field-error {
            color: var(--auth-error);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .form-input::placeholder {
            color: var(--auth-text-muted);
        }
        
        .form-input[type="password"] {
            font-family: 'JetBrains Mono', monospace;
        }
        
        /* Checkbox and Radio */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-input {
            width: 1.125rem;
            height: 1.125rem;
            accent-color: var(--auth-primary);
        }
        
        .checkbox-label {
            font-size: 0.875rem;
            color: var(--auth-text-light);
            cursor: pointer;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }
        
        .btn-primary {
            background: var(--auth-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--auth-primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--auth-shadow-lg);
        }
        
        .btn-secondary {
            background: var(--auth-bg-light);
            color: var(--auth-text);
            border: 1px solid var(--auth-border);
        }
        
        .btn-secondary:hover {
            background: var(--auth-bg);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--auth-text-muted);
            cursor: pointer;
            padding: 0.25rem;
        }
        
        .password-toggle:hover {
            color: var(--auth-text);
        }
        
        /* Form Footer */
        .form-footer {
            margin-top: 2rem;
            text-align: center;
        }
        
        .auth-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .auth-link {
            color: var(--auth-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .auth-link:hover {
            color: var(--auth-primary-dark);
        }
        
        /* Back to Home */
        .back-to-home {
            padding: 1.5rem 3rem;
            border-top: 1px solid var(--auth-border);
            background: var(--auth-bg);
        }
        
        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--auth-text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .home-link:hover {
            color: var(--auth-text);
        }
        
        /* Security Footer */
        .auth-footer {
            background: var(--auth-bg);
            border-top: 1px solid var(--auth-border);
            padding: 2rem;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--auth-text-muted);
            font-size: 0.875rem;
        }
        
        .security-badge i {
            color: var(--auth-success);
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: var(--auth-text-light);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: var(--auth-text);
        }
        
        .copyright {
            color: var(--auth-text-muted);
            font-size: 0.75rem;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .auth-container {
                margin: 1rem;
                border-radius: 1rem;
                overflow: hidden;
            }
            
            .auth-side-panel {
                display: none;
            }
            
            .auth-form-panel {
                flex: 1;
            }
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 2rem 1.5rem;
            }
            
            .brand-title {
                font-size: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
            
            .security-badges {
                gap: 1rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .back-to-home {
                padding: 1rem 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .auth-container {
                margin: 0.5rem;
                border-radius: 0.5rem;
            }
            
            .form-container {
                padding: 1.5rem 1rem;
            }
            
            .brand-section {
                margin-bottom: 2rem;
            }
            
            .brand-title {
                font-size: 1.75rem;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-container > * {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .form-header {
            animation-delay: 0.1s;
        }
        
        .auth-content {
            animation-delay: 0.2s;
        }
        
        .form-footer {
            animation-delay: 0.3s;
        }
    </style>
</body>
</html>
