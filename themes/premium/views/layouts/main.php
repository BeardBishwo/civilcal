<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->getPageTitle() ?: 'Premium Calculator'; ?></title>
    
    <!-- Premium Theme CSS -->
    <link rel="stylesheet" href="/themes/premium/assets/css/premium-theme.css">
    <link rel="stylesheet" href="/themes/premium/assets/css/premium-calculator.css">
    
    <!-- Theme Configuration -->
    <script>
        window.premiumTheme = {
            settings: <?php echo json_encode($themeSettings ?? []); ?>
        };
    </script>
    
    <?php $this->renderPartial('partials/head'); ?>
</head>
<body class="premium-theme <?php echo $bodyClass ?? ''; ?>">
    
    <!-- Main Application Container -->
    <div id="premium-app" class="premium-app">
        
        <!-- Navigation Header -->
        <header class="premium-header" role="banner">
            <div class="container">
                <div class="header-content">
                    <div class="logo-section">
                        <h1 class="site-title">
                            <a href="/" class="logo-link">
                                <?php echo $siteName ?? 'Premium Calculator'; ?>
                            </a>
                        </h1>
                    </div>
                    
                    <nav class="main-navigation" role="navigation">
                        <ul class="nav-menu">
                            <li><a href="/">Home</a></li>
                            <li><a href="/calculators">Calculators</a></li>
                            <li><a href="/modules">Modules</a></li>
                            <li><a href="/about">About</a></li>
                            <li><a href="/contact">Contact</a></li>
                        </ul>
                    </nav>
                    
                    <div class="header-actions">
                        <button class="theme-toggle-btn" onclick="togglePremiumDarkMode()" title="Toggle Dark Mode">
                            <span class="toggle-icon">🌙</span>
                        </button>
                        
                        <div class="user-menu">
                            <button class="user-menu-btn" onclick="toggleUserMenu()">
                                <span class="user-avatar">👤</span>
                            </button>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="/profile">Profile</a>
                                <a href="/settings">Settings</a>
                                <a href="/admin" class="admin-link">Admin Panel</a>
                                <hr>
                                <a href="/logout">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Main Content Area -->
        <main class="premium-main" role="main">
            <div class="container">
                <div class="content-wrapper">
                    
                    <!-- Breadcrumb Navigation -->
                    <nav class="breadcrumb" aria-label="Breadcrumb">
                        <?php echo $this->renderBreadcrumb(); ?>
                    </nav>
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title"><?php echo $pageTitle ?? 'Calculator'; ?></h1>
                        <?php if (isset($pageDescription)): ?>
                            <p class="page-description"><?php echo $pageDescription; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="main-content">
                        <?php echo $content; ?>
                    </div>
                    
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="premium-footer" role="contentinfo">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h3>Premium Calculator</h3>
                        <p>Professional calculation tools for engineering and construction.</p>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="/calculators">All Calculators</a></li>
                            <li><a href="/modules/civil">Civil Engineering</a></li>
                            <li><a href="/modules/structural">Structural</a></li>
                            <li><a href="/modules/mep">MEP</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Support</h4>
                        <ul>
                            <li><a href="/help">Help Center</a></li>
                            <li><a href="/documentation">Documentation</a></li>
                            <li><a href="/contact">Contact Us</a></li>
                            <li><a href="/feedback">Feedback</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Connect</h4>
                        <div class="social-links">
                            <a href="#" aria-label="Facebook">📘</a>
                            <a href="#" aria-label="Twitter">🐦</a>
                            <a href="#" aria-label="LinkedIn">💼</a>
                            <a href="#" aria-label="YouTube">📺</a>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> Premium Calculator. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="/privacy">Privacy Policy</a>
                        <a href="/terms">Terms of Service</a>
                        <a href="/cookies">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </footer>
        
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Processing...</p>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()" style="display: none;">
        ↑
    </button>
    
    <!-- Premium Theme JavaScript -->
    <script src="/themes/premium/assets/js/premium-theme.js"></script>
    
    <!-- Custom Page Scripts -->
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline Scripts -->
    <script>
        // Global utility functions
        
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu.contains(event.target)) {
                document.getElementById('userDropdown').classList.remove('show');
            }
        });
        
        // Initialize theme when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof premiumThemeInit === 'function') {
                premiumThemeInit();
            }
        });
    </script>
    
    <?php $this->renderPartial('partials/footer-scripts'); ?>
    
</body>
</html>
