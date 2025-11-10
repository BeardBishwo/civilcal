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
    <link rel="icon" type="image/x-icon" href="<?= $viewHelper->themeUrl('assets/favicon.ico') ?>">
    
    <!-- ProCalculator Premium Theme Styles -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/auth.css') ?>">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
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
            background: url('<?= $viewHelper->themeUrl('assets/images/hero-pattern.svg') ?>') repeat;
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
            transition: all var(--pc-transition-fast);
        }

        .pc-requirement.met {
            color: var(--pc-success);
        }
        
        .pc-requirement.met i {
            color: var(--pc-success);
        }

        .pc-requirement i {
            width: 16px;
            text-align: center;
            color: var(--pc-error);
        }
        
        /* Password Strength Meter */
        .pc-password-strength {
            margin-top: var(--pc-spacing-lg);
            padding-top: var(--pc-spacing-md);
            border-top: 1px solid var(--pc-glass-border);
        }
        
        .pc-strength-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--pc-spacing-sm);
            font-size: 0.875rem;
        }
        
        .pc-strength-text {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        .pc-strength-text.weak {
            color: var(--pc-error);
        }
        
        .pc-strength-text.fair {
            color: #f59e0b;
        }
        
        .pc-strength-text.good {
            color: #3b82f6;
        }
        
        .pc-strength-text.strong {
            color: var(--pc-success);
        }
        
        .pc-strength-text.excellent {
            color: var(--pc-premium);
            font-weight: 700;
        }
        
        .pc-strength-bar {
            height: 8px;
            background: var(--pc-glass-border);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .pc-strength-fill {
            height: 100%;
            width: 0;
            transition: all var(--pc-transition-normal);
            border-radius: 4px;
            position: relative;
        }
        
        .pc-strength-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3));
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .pc-strength-fill.strength-0 {
            width: 0%;
            background: transparent;
        }
        
        .pc-strength-fill.strength-1 {
            width: 20%;
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }
        
        .pc-strength-fill.strength-2 {
            width: 40%;
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }
        
        .pc-strength-fill.strength-3 {
            width: 60%;
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }
        
        .pc-strength-fill.strength-4 {
            width: 80%;
            background: linear-gradient(90deg, #10b981, #059669);
        }
        
        .pc-strength-fill.strength-5 {
            width: 100%;
            background: linear-gradient(90deg, var(--pc-premium), var(--pc-gold));
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
        
        /* Premium Marketing Opt-in */
        .pc-marketing-optin {
            margin: var(--pc-spacing-xl) 0;
        }
        
        .pc-optin-label {
            display: flex;
            gap: var(--pc-spacing-md);
            cursor: pointer;
            padding: var(--pc-spacing-lg);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(168, 85, 247, 0.08));
            border: 2px solid transparent;
            border-radius: var(--pc-radius-lg);
            transition: all var(--pc-transition-normal);
            position: relative;
            overflow: hidden;
        }
        
        .pc-optin-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1));
            opacity: 0;
            transition: opacity var(--pc-transition-normal);
        }
        
        .pc-optin-label:hover {
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.15);
        }
        
        .pc-optin-label:hover::before {
            opacity: 1;
        }
        
        .pc-optin-checkbox {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .pc-custom-checkbox {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border: 2px solid var(--pc-glass-border);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pc-glass);
            transition: all var(--pc-transition-fast);
            position: relative;
            z-index: 1;
        }
        
        .pc-custom-checkbox i {
            color: white;
            font-size: 0.75rem;
            opacity: 0;
            transform: scale(0);
            transition: all var(--pc-transition-fast);
        }
        
        .pc-optin-checkbox:checked + .pc-custom-checkbox {
            background: linear-gradient(135deg, var(--pc-premium), var(--pc-gold));
            border-color: var(--pc-premium);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        .pc-optin-checkbox:checked + .pc-custom-checkbox i {
            opacity: 1;
            transform: scale(1);
        }
        
        .pc-optin-content {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .pc-optin-header {
            display: flex;
            align-items: center;
            gap: var(--pc-spacing-sm);
            margin-bottom: var(--pc-spacing-xs);
        }
        
        .pc-optin-icon {
            color: var(--pc-premium);
            font-size: 1.125rem;
        }
        
        .pc-optin-title {
            font-size: 1rem;
            color: var(--pc-text);
            font-weight: 600;
        }
        
        .pc-optin-description {
            color: var(--pc-text-secondary);
            font-size: 0.875rem;
            margin: var(--pc-spacing-xs) 0;
            line-height: 1.5;
        }
        
        .pc-optin-benefits {
            display: flex;
            flex-wrap: wrap;
            gap: var(--pc-spacing-sm);
            margin-top: var(--pc-spacing-sm);
        }
        
        .pc-benefit {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 12px;
            font-size: 0.75rem;
            color: var(--pc-premium);
            font-weight: 500;
        }
        
        .pc-benefit i {
            font-size: 0.625rem;
        }
        
        /* Premium Register Button */
        .pc-register-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--pc-premium), var(--pc-gold));
            border: none;
            padding: var(--pc-spacing-lg) var(--pc-spacing-xl);
            min-height: 72px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
            transition: all var(--pc-transition-normal);
        }
        
        .pc-register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .pc-register-btn:hover::before {
            left: 100%;
        }
        
        .pc-register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
        }
        
        .pc-register-btn:active {
            transform: translateY(0);
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
        
        .pc-register-btn:hover .pc-btn-arrow {
            transform: translateX(4px);
        }
        
        .pc-spinner-premium {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
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
        
        /* Toast Notification */
        .pc-toast-container {
            position: fixed;
            left: 20px;
            top: 20px;
            z-index: 99999;
            pointer-events: none;
        }
        
        .pc-toast {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.98), rgba(31, 41, 55, 0.98));
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 12px;
            min-width: 360px;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 16px;
            pointer-events: all;
            animation: toastSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        
        .pc-toast::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            animation: shimmer 3s infinite;
            pointer-events: none;
        }
        
        /* Success Toast - Green Theme */
        .pc-toast.toast-success {
            border-left: 4px solid #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.1));
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.3), 0 0 40px rgba(16, 185, 129, 0.15);
        }
        
        .pc-toast.toast-success::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981, #059669);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.6);
        }
        
        /* Error Toast - Red Theme */
        .pc-toast.toast-error {
            border-left: 4px solid #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.1));
            box-shadow: 0 20px 60px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.15);
        }
        
        .pc-toast.toast-error::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #ef4444, #dc2626);
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
        }
        
        /* Warning Toast - Orange Theme */
        .pc-toast.toast-warning {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.1));
            box-shadow: 0 20px 60px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.15);
        }
        
        .pc-toast.toast-warning::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #f59e0b, #d97706);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.6);
        }
        
        /* Info Toast - Blue Theme */
        .pc-toast.toast-info {
            border-left: 4px solid #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.1));
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.15);
        }
        
        .pc-toast.toast-info::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6, #2563eb);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
        }
        
        .pc-toast.toast-hide {
            animation: toastSlideOut 0.4s cubic-bezier(0.4, 0, 1, 1) forwards;
        }
        
        @keyframes toastSlideIn {
            from {
                transform: translateX(-120%) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }
        
        @keyframes toastSlideOut {
            from {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
            to {
                transform: translateX(-120%) scale(0.9);
                opacity: 0;
            }
        }
        
        .pc-toast-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.5rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .toast-success .pc-toast-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            animation: successPulse 2s ease-in-out infinite;
        }
        
        @keyframes successPulse {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4), 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            50% {
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4), 0 0 0 12px rgba(16, 185, 129, 0);
            }
        }
        
        .toast-error .pc-toast-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            animation: errorShake 0.5s ease-in-out;
        }
        
        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        
        .toast-warning .pc-toast-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            animation: warningBounce 0.6s ease-in-out;
        }
        
        @keyframes warningBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        
        .toast-info .pc-toast-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }
        
        .pc-toast-content {
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .pc-toast-title {
            font-weight: 700;
            color: white;
            font-size: 1rem;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .toast-success .pc-toast-title {
            color: #10b981;
        }
        
        .toast-error .pc-toast-title {
            color: #ef4444;
        }
        
        .toast-warning .pc-toast-title {
            color: #f59e0b;
        }
        
        .toast-info .pc-toast-title {
            color: #3b82f6;
        }
        
        .pc-toast-message {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .pc-toast-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.2s;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        
        .pc-toast-close:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: rotate(90deg);
        }
        
        /* Utility Classes */
        .pc-hidden {
            display: none !important;
        }
        
        .pc-btn-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--pc-spacing-sm);
        }
        
        .pc-btn-loading .pc-spinner-premium {
            animation: spin 0.8s linear infinite, scanPulse 2s ease-in-out infinite;
        }
        
        @keyframes scanPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
            }
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
    <div class="pc-register-container auth-container" id="main-content">
        <div class="pc-register-card pc-card auth-card">
            <!-- Header -->
            <div class="pc-register-header auth-header">
                <div class="pc-register-logo auth-logo">
                    <i class="fas fa-calculator"></i>
                </div>
                <h1 class="pc-register-title auth-title">Create Account</h1>
                <p class="pc-register-subtitle auth-subtitle">Join thousands of professional engineers</p>
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
            <form class="pc-premium-form auth-form" id="registerForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Personal Information -->
                <div class="pc-form-row">
                    <div class="pc-form-col">
                        <div class="pc-form-group form-group">
                            <label for="first_name" class="pc-label form-label">
                                <i class="fas fa-user me-2"></i>First Name
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="pc-input pc-form-control form-input" 
                                placeholder="Enter your first name"
                                required 
                                autocomplete="given-name"
                                aria-describedby="first_name-error"
                            >
                            <div id="first_name-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="pc-form-col">
                        <div class="pc-form-group form-group">
                            <label for="last_name" class="pc-label form-label">
                                <i class="fas fa-user me-2"></i>Last Name
                            </label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                class="pc-input pc-form-control form-input" 
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
                <div class="pc-form-group form-group">
                    <label for="username" class="pc-label form-label">
                        <i class="fas fa-at me-2"></i>Username
                    </label>
                    <div class="pc-username-check">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="pc-input pc-form-control form-input" 
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

                <!-- Professional Information -->
                <div class="pc-form-row">
                    <div class="pc-form-col">
                        <div class="pc-form-group form-group">
                            <label for="company" class="pc-label form-label">
                                <i class="fas fa-building me-2"></i>Company/Organization
                            </label>
                            <input 
                                type="text" 
                                id="company" 
                                name="company" 
                                class="pc-input pc-form-control form-input" 
                                placeholder="Your company name"
                                autocomplete="organization"
                                aria-describedby="company-error"
                            >
                            <div id="company-error" class="pc-error-message" role="alert" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="pc-form-col">
                        <div class="pc-form-group form-group">
                            <label for="profession" class="pc-label form-label">
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
                        <div class="pc-form-group form-group">
                            <label for="password_confirm" class="pc-label form-label">
                                <i class="fas fa-lock me-2"></i>Confirm Password
                            </label>
                            <div class="pc-password-input-container">
                                <input 
                                    type="password" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    class="pc-input pc-form-control form-input" 
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
                    
                    <!-- Password Strength Meter -->
                    <div class="pc-password-strength" id="password-strength">
                        <div class="pc-strength-label">
                            <span>Password Strength:</span>
                            <span id="strength-text" class="pc-strength-text">None</span>
                        </div>
                        <div class="pc-strength-bar">
                            <div class="pc-strength-fill" id="strength-fill"></div>
                        </div>
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
                <div class="pc-marketing-optin">
                    <label class="pc-optin-label">
                        <input type="checkbox" name="marketing_opt_in" id="marketing_opt_in" class="pc-optin-checkbox">
                        <span class="pc-custom-checkbox">
                            <i class="fas fa-check"></i>
                        </span>
                        <div class="pc-optin-content">
                            <div class="pc-optin-header">
                                <i class="fas fa-envelope-open-text pc-optin-icon"></i>
                                <strong class="pc-optin-title">Stay Updated</strong>
                            </div>
                            <p class="pc-optin-description">Receive product updates, engineering tips, and industry insights</p>
                            <div class="pc-optin-benefits">
                                <span class="pc-benefit"><i class="fas fa-star"></i> Exclusive tips</span>
                                <span class="pc-benefit"><i class="fas fa-lightbulb"></i> New features</span>
                                <span class="pc-benefit"><i class="fas fa-chart-line"></i> Industry trends</span>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="pc-form-group form-group">
                    <button type="submit" class="pc-btn pc-btn-premium pc-btn-lg pc-w-full pc-register-btn" id="registerBtn">
                        <span class="pc-btn-content">
                            <span class="pc-btn-icon">
                                <i class="fas fa-user-plus"></i>
                            </span>
                            <span class="pc-btn-text-main">
                                <span class="pc-btn-title">Create Professional Account</span>
                                <span class="pc-btn-subtitle">Join 10,000+ engineers worldwide</span>
                            </span>
                            <span class="pc-btn-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </span>
                        <span class="pc-btn-loading pc-hidden">
                            <span class="pc-spinner-premium"></span>
                            <span class="pc-loading-text">Creating your account<span class="pc-loading-dots"><span>.</span><span>.</span><span>.</span></span></span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="pc-login-link">
                <p>Already have an account? 
                    <a href="<?= $viewHelper->url('login') ?>" class="pc-login-cta">Sign in to your account</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div id="toast-container" class="pc-toast-container"></div>

    <!-- ProCalculator Premium Theme Scripts -->
    <script src="<?= $viewHelper->themeUrl('assets/js/procalculator-core.js') ?>"></script>
    <script src="<?= $viewHelper->themeUrl('assets/js/auth-enhanced.js') ?>"></script>
    
    <!-- Registration Page Specific Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password strength checker with live updates
            const passwordInput = document.getElementById('password');
            const requirements = {
                length: password => password.length >= 8,
                uppercase: password => /[A-Z]/.test(password),
                lowercase: password => /[a-z]/.test(password),
                number: password => /[0-9]/.test(password),
                special: password => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let metCount = 0;
                
                // Check each requirement
                Object.keys(requirements).forEach(req => {
                    const element = document.querySelector(`[data-requirement="${req}"]`);
                    const icon = element.querySelector('i');
                    const isMet = requirements[req](password);
                    
                    if (isMet) {
                        element.classList.add('met');
                        icon.className = 'fas fa-check';
                        metCount++;
                    } else {
                        element.classList.remove('met');
                        icon.className = 'fas fa-times';
                    }
                });
                
                // Update strength meter
                updateStrengthMeter(metCount, password);
            });
            
            function updateStrengthMeter(metCount, password) {
                const strengthFill = document.getElementById('strength-fill');
                const strengthText = document.getElementById('strength-text');
                
                // Remove all previous strength classes
                strengthFill.className = 'pc-strength-fill';
                strengthText.className = 'pc-strength-text';
                
                if (password.length === 0) {
                    strengthFill.classList.add('strength-0');
                    strengthText.textContent = 'None';
                } else if (metCount <= 1) {
                    strengthFill.classList.add('strength-1');
                    strengthText.textContent = 'Weak';
                    strengthText.classList.add('weak');
                } else if (metCount === 2) {
                    strengthFill.classList.add('strength-2');
                    strengthText.textContent = 'Fair';
                    strengthText.classList.add('fair');
                } else if (metCount === 3) {
                    strengthFill.classList.add('strength-3');
                    strengthText.textContent = 'Good';
                    strengthText.classList.add('good');
                } else if (metCount === 4) {
                    strengthFill.classList.add('strength-4');
                    strengthText.textContent = 'Strong';
                    strengthText.classList.add('strong');
                } else if (metCount === 5) {
                    strengthFill.classList.add('strength-5');
                    strengthText.textContent = 'Excellent';
                    strengthText.classList.add('excellent');
                }
            }
            
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
            
            function showError(fieldId, message) {
                const errorElement = document.getElementById(fieldId + '-error');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }
            
            function clearError(fieldId) {
                const errorElement = document.getElementById(fieldId + '-error');
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                }
            }

            // Social registration handlers
            document.getElementById('google-register').addEventListener('click', function(e) {
                e.preventDefault();
                showToast('info', 'Google Login', 'Google authentication coming soon!');
            });

            document.getElementById('linkedin-register').addEventListener('click', function(e) {
                e.preventDefault();
                showToast('info', 'LinkedIn Login', 'LinkedIn authentication coming soon!');
            });
            
            // Toast notification system
            function showToast(type, title, message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `pc-toast toast-${type}`;
                
                const iconMap = {
                    success: 'fa-check-circle',
                    error: 'fa-times-circle',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle'
                };
                
                toast.innerHTML = `
                    <div class="pc-toast-icon">
                        <i class="fas ${iconMap[type]}"></i>
                    </div>
                    <div class="pc-toast-content">
                        <div class="pc-toast-title">${title}</div>
                        <div class="pc-toast-message">${message}</div>
                    </div>
                    <button class="pc-toast-close" onclick="this.parentElement.classList.add('toast-hide'); setTimeout(() => this.parentElement.remove(), 400);">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                container.appendChild(toast);
                
                // Auto-dismiss after 3 seconds
                setTimeout(() => {
                    toast.classList.add('toast-hide');
                    setTimeout(() => toast.remove(), 400);
                }, 3000);
            }
            
            // Make showToast available globally for auth-enhanced.js
            window.showToast = showToast;
        });
    </script>
</body>
</html>
