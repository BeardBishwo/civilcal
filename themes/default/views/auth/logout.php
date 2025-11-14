<?php
/**
 * Logout Success Page
 * Shows a confirmation message after successful logout
 */

$pageTitle = 'Logged Out - Civil Calculator';
$metaDescription = 'You have been successfully logged out of Civil Calculator.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $metaDescription; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo app_base_url('themes/default/assets/images/favicon.ico'); ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/auth.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .logout-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .logout-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        
        .logout-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 20px;
        }
        
        .logout-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .logout-message {
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .logout-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #3730a3;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        
        .security-note {
            margin-top: 30px;
            padding: 15px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #92400e;
        }
        
        .auto-redirect-notice {
            margin-top: 20px;
            padding: 15px;
            background: #e0f2fe;
            border: 1px solid #0288d1;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #01579b;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        
        .auto-redirect-notice i {
            color: #0288d1;
        }
        
        .auto-redirect-notice strong {
            color: #01579b;
            font-weight: 700;
        }
        
        .cancel-redirect {
            background: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .cancel-redirect:hover {
            background: #d32f2f;
        }
        
        @media (max-width: 480px) {
            .logout-card {
                padding: 30px 20px;
            }
            
            .logout-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .auto-redirect-notice {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .cancel-redirect {
                width: 100%;
                padding: 10px;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="logout-title">Successfully Logged Out</h1>
            
            <p class="logout-message">
                <?php if (isset($_SESSION['logout_message'])): ?>
                    <?php echo htmlspecialchars($_SESSION['logout_message']); ?>
                <?php else: ?>
                    You have been safely logged out of your account.
                <?php endif; ?>
                <br>
                Thank you for using Civil Calculator!
            </p>
            
            <div class="logout-actions">
                <a href="<?php echo app_base_url('login'); ?>" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Again
                </a>
                
                <a href="<?php echo app_base_url(''); ?>" class="btn btn-secondary">
                    <i class="fas fa-home"></i>
                    Go Home
                </a>
            </div>
            
            <div class="security-note">
                <i class="fas fa-shield-alt"></i>
                <strong>Security Tip:</strong> For your security, close your browser if you're on a shared computer.
            </div>
            
            <div class="auto-redirect-notice" id="redirectNotice">
                <i class="fas fa-clock"></i>
                <span>Redirecting to homepage in <strong id="countdown">5</strong> seconds...</span>
                <button onclick="cancelRedirect()" class="cancel-redirect">Cancel</button>
            </div>
        </div>
    </div>
    
    <script>
        // Clear any cached data
        if ('caches' in window) {
            caches.keys().then(function(names) {
                names.forEach(function(name) {
                    caches.delete(name);
                });
            });
        }
        
        // Clear session storage
        if (typeof(Storage) !== "undefined") {
            sessionStorage.clear();
        }
        
        // Auto-redirect with countdown
        let countdownTime = 5;
        let redirectTimer;
        let countdownInterval;
        
        function startRedirectCountdown() {
            const countdownElement = document.getElementById('countdown');
            const redirectNotice = document.getElementById('redirectNotice');
            
            // Show the redirect notice
            redirectNotice.style.display = 'block';
            
            // Update countdown every second
            countdownInterval = setInterval(function() {
                countdownTime--;
                countdownElement.textContent = countdownTime;
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = '<?php echo app_base_url(''); ?>';
                }
            }, 1000);
            
            // Set the redirect timer
            redirectTimer = setTimeout(function() {
                window.location.href = '<?php echo app_base_url(''); ?>';
            }, 5000);
        }
        
        function cancelRedirect() {
            clearTimeout(redirectTimer);
            clearInterval(countdownInterval);
            document.getElementById('redirectNotice').style.display = 'none';
        }
        
        // Start countdown after 2 seconds
        setTimeout(startRedirectCountdown, 2000);
    </script>
</body>
</html>

<?php
// Clear logout message after displaying
if (isset($_SESSION['logout_message'])) {
    unset($_SESSION['logout_message']);
}
if (isset($_SESSION['logout_user'])) {
    unset($_SESSION['logout_user']);
}
?>
