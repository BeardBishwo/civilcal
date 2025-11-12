<!DOCTYPE html>
<html lang="en" class="pc-theme-dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Logged Out - ProCalculator</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ProCalculator Premium Theme CSS -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/animations.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/header-footer.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/dark-theme.css') ?>">

    <style>
        /* Premium Logout Page Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: linear-gradient(135deg, 
                #0a0e27 0%, 
                #1a1f3a 25%,
                #0f1629 50%,
                #1e2139 75%,
                #0a0e27 100%
            );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Particles */
        .logout-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(99, 102, 241, 0.4);
            border-radius: 50%;
            animation: particleFloat 8s infinite ease-in-out;
        }

        @keyframes particleFloat {
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

        /* Premium Logout Container */
        .logout-container {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 600px;
            text-align: center;
            animation: containerFadeIn 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes containerFadeIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Success Icon with Pulse Animation */
        .logout-icon-wrapper {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto 3rem;
            animation: iconEntry 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s backwards;
        }

        @keyframes iconEntry {
            from {
                opacity: 0;
                transform: scale(0) rotate(-180deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .logout-icon-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 160px;
            height: 160px;
            background: linear-gradient(135deg, 
                rgba(16, 185, 129, 0.2), 
                rgba(5, 150, 105, 0.15)
            );
            border-radius: 50%;
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% {
                box-shadow: 
                    0 0 0 0 rgba(16, 185, 129, 0.7),
                    0 0 0 10px rgba(16, 185, 129, 0.4),
                    0 0 0 20px rgba(16, 185, 129, 0.2),
                    0 0 60px rgba(16, 185, 129, 0.3);
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                box-shadow: 
                    0 0 0 10px rgba(16, 185, 129, 0.5),
                    0 0 0 30px rgba(16, 185, 129, 0.3),
                    0 0 0 50px rgba(16, 185, 129, 0.1),
                    0 0 80px rgba(16, 185, 129, 0.2);
                transform: translate(-50%, -50%) scale(1.05);
            }
        }

        .logout-icon {
            position: relative;
            z-index: 2;
            font-size: 80px;
            color: #10b981;
            text-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
            animation: iconRotate 2s ease-in-out infinite;
        }

        @keyframes iconRotate {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(10deg) scale(1.1);
            }
        }

        /* Premium Content */
        .logout-content {
            background: linear-gradient(135deg, 
                rgba(17, 24, 39, 0.95), 
                rgba(31, 41, 55, 0.9)
            );
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 32px;
            padding: 4rem 3rem;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 80px rgba(16, 185, 129, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: contentSlideUp 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.5s backwards;
        }

        @keyframes contentSlideUp {
            from {
                opacity: 0;
                transform: translateY(60px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(16, 185, 129, 0.1), 
                transparent
            );
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            100% {
                left: 100%;
            }
        }

        .logout-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #10b981, #059669, #10b981);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: gradientShift 3s ease-in-out infinite, textFadeIn 1s ease-out 0.7s backwards;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-message {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2.5rem;
            line-height: 1.8;
            animation: textFadeIn 1s ease-out 0.9s backwards;
        }

        .logout-user-name {
            color: #10b981;
            font-weight: 600;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        /* Premium Action Buttons */
        .logout-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: buttonsFadeIn 1s ease-out 1.1s backwards;
        }

        @keyframes buttonsFadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-btn {
            padding: 1rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .logout-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .logout-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .logout-btn span {
            position: relative;
            z-index: 1;
        }

        .logout-btn i {
            position: relative;
            z-index: 1;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .logout-btn:hover i {
            transform: translateX(5px);
        }

        .logout-btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            box-shadow: 
                0 10px 30px rgba(99, 102, 241, 0.3),
                0 0 40px rgba(99, 102, 241, 0.2);
        }

        .logout-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 15px 40px rgba(99, 102, 241, 0.4),
                0 0 60px rgba(99, 102, 241, 0.3);
        }

        .logout-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .logout-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Security Notice */
        .security-notice {
            margin-top: 3rem;
            padding: 1.5rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: noticeFadeIn 1s ease-out 1.3s backwards;
        }

        @keyframes noticeFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .security-notice i {
            font-size: 1.5rem;
            color: #3b82f6;
        }

        .security-notice-text {
            flex: 1;
            text-align: left;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .logout-content {
                padding: 3rem 2rem;
            }

            .logout-title {
                font-size: 2rem;
            }

            .logout-message {
                font-size: 1rem;
            }

            .logout-actions {
                flex-direction: column;
            }

            .logout-btn {
                width: 100%;
                justify-content: center;
            }

            .logout-icon-wrapper {
                width: 120px;
                height: 120px;
            }

            .logout-icon-bg {
                width: 120px;
                height: 120px;
            }

            .logout-icon {
                font-size: 60px;
            }
        }

        /* Auto redirect countdown */
        .redirect-countdown {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            animation: countdownFadeIn 1s ease-out 1.5s backwards;
        }

        @keyframes countdownFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .countdown-number {
            color: #10b981;
            font-weight: 700;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Animated Background Particles -->
    <div class="logout-particles" id="particles"></div>

    <!-- Premium Logout Container -->
    <div class="logout-container">
        <!-- Success Icon -->
        <div class="logout-icon-wrapper">
            <div class="logout-icon-bg"></div>
            <i class="fas fa-check-circle logout-icon"></i>
        </div>

        <!-- Logout Content -->
        <div class="logout-content">
            <h1 class="logout-title">Successfully Logged Out</h1>
            
            <p class="logout-message">
                Goodbye, <span class="logout-user-name"><?= htmlspecialchars($userName ?? 'User') ?></span>!<br>
                Your session has been securely terminated. Thank you for using ProCalculator.
            </p>

            <!-- Action Buttons -->
            <div class="logout-actions">
                <a href="<?= $viewHelper->url('login') ?>" class="logout-btn logout-btn-primary">
                    <span>Sign In Again</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="<?= $viewHelper->url('') ?>" class="logout-btn logout-btn-secondary">
                    <span>Go to Homepage</span>
                    <i class="fas fa-home"></i>
                </a>
            </div>

            <!-- Auto Redirect Countdown -->
            <div class="redirect-countdown">
                Redirecting to homepage in <span class="countdown-number" id="countdown">5</span> seconds...
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <i class="fas fa-shield-alt"></i>
                <div class="security-notice-text">
                    <strong>Security Tip:</strong> Always log out from shared or public computers. 
                    Your data is safe and all active sessions have been terminated.
                </div>
            </div>
        </div>
    </div>

    <!-- Premium JavaScript -->
    <script>
        // Create animated particles
        const particlesContainer = document.getElementById('particles');
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random starting position
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            
            // Random animation delay and duration
            particle.style.animationDelay = Math.random() * 8 + 's';
            particle.style.animationDuration = (Math.random() * 4 + 6) + 's';
            
            // Random movement
            particle.style.setProperty('--tx', (Math.random() - 0.5) * 200 + 'px');
            particle.style.setProperty('--ty', (Math.random() - 0.5) * 200 + 'px');
            
            particlesContainer.appendChild(particle);
        }

        // Auto redirect countdown
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?= $viewHelper->url('') ?>';
            }
        }, 1000);

        // Prevent back button after logout
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, '', window.location.href);
        };
    </script>
</body>
</html>
