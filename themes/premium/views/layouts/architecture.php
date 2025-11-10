<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional $10,000 Architecture Calculator - Civil, Structural & MEP Engineering Tools">
    <meta name="keywords" content="architecture calculator, civil engineering, structural analysis, MEP, construction">
    <title><?php echo $this->getPageTitle() ?: 'Professional Architecture Calculator'; ?></title>
    
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Premium Theme CSS -->
    <link rel="stylesheet" href="/themes/premium/assets/css/premium-theme.css">
    <link rel="stylesheet" href="/themes/premium/assets/css/premium-calculator.css">
    <link rel="stylesheet" href="/themes/premium/assets/css/premium-animations.css">
    
    <!-- Architecture Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Theme Configuration -->
    <script>
        window.premiumTheme = {
            settings: <?php echo json_encode($themeSettings ?? []); ?>,
            company: <?php echo json_encode($companySettings ?? [
                'name' => 'Premium Architecture Firm',
                'logo' => '/themes/premium/assets/images/logo.png',
                'primary_color' => '#1a365d',
                'secondary_color' => '#4a90e2'
            ]); ?>
        };
        
        // Premium theme configuration
        const THEME_CONFIG = {
            version: '2.0.0',
            value: '$10,000',
            category: 'Architecture Professional',
            features: [
                'Real-time Calculations',
                '3D Visualization',
                'CAD Export',
                'Material Optimization',
                'Cost Estimation',
                'Code Compliance',
                'Report Generation'
            ]
        };
    </script>
    
    <?php $this->renderPartial('partials/head'); ?>
</head>
<body class="premium-theme architecture-theme <?php echo $bodyClass ?? ''; ?>">
    
    <!-- Loading Screen -->
    <div class="premium-loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="fas fa-building"></i>
            </div>
            <h2>Loading Architecture Calculator</h2>
            <div class="loading-progress">
                <div class="progress-bar"></div>
            </div>
            <p>Initializing professional tools...</p>
        </div>
    </div>
    
    <!-- Main Application Container -->
    <div id="premium-app" class="premium-app" style="opacity: 0;">
        
        <!-- Premium Navigation Header -->
        <header class="premium-header" role="banner">
            <div class="container">
                <div class="header-content">
                    <!-- Company Branding -->
                    <div class="logo-section">
                        <a href="/" class="logo-link">
                            <div class="logo-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="logo-text">
                                <h1 class="site-title"><?php echo $companyName ?? 'Architecture Pro'; ?></h1>
                                <span class="site-tagline">Professional Calculator Suite</span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Professional Navigation -->
                    <nav class="main-navigation" role="navigation">
                        <ul class="nav-menu">
                            <li class="nav-item">
                                <a href="/" class="nav-link">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle">
                                    <i class="fas fa-calculator"></i> Calculators
                                </a>
                                <div class="dropdown-menu">
                                    <a href="/calculators/civil" class="dropdown-item">
                                        <i class="fas fa-road"></i> Civil Engineering
                                    </a>
                                    <a href="/calculators/structural" class="dropdown-item">
                                        <i class="fas fa-building"></i> Structural Analysis
                                    </a>
                                    <a href="/calculators/mep" class="dropdown-item">
                                        <i class="fas fa-cogs"></i> MEP Systems
                                    </a>
                                    <a href="/calculators/estimation" class="dropdown-item">
                                        <i class="fas fa-chart-bar"></i> Cost Estimation
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="/projects" class="nav-link">
                                    <i class="fas fa-folder-open"></i> Projects
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/templates" class="nav-link">
                                    <i class="fas fa-layer-group"></i> Templates
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/reports" class="nav-link">
                                    <i class="fas fa-file-alt"></i> Reports
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- Premium Header Actions -->
                    <div class="header-actions">
                        <!-- Quick Calculator -->
                        <button class="quick-calc-btn" onclick="toggleQuickCalculator()" title="Quick Calculator">
                            <i class="fas fa-calculator"></i>
                        </button>
                        
                        <!-- Theme Toggle -->
                        <button class="theme-toggle-btn" onclick="togglePremiumDarkMode()" title="Toggle Dark Mode">
                            <i class="fas fa-moon"></i>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="user-menu">
                            <button class="user-menu-btn" onclick="toggleUserMenu()">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="user-dropdown" id="userDropdown">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name"><?php echo $userName ?? 'User'; ?></span>
                                        <span class="user-role">Architecture Professional</span>
                                    </div>
                                </div>
                                <hr>
                                <a href="/profile"><i class="fas fa-user"></i> Profile</a>
                                <a href="/settings"><i class="fas fa-cog"></i> Settings</a>
                                <a href="/subscription"><i class="fas fa-credit-card"></i> Subscription</a>
                                <a href="/admin" class="admin-link"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                                <hr>
                                <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Quick Calculator Panel -->
        <div class="quick-calculator-panel" id="quickCalculatorPanel" style="display: none;">
            <div class="panel-header">
                <h3>Quick Calculator</h3>
                <button onclick="toggleQuickCalculator()" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="panel-content">
                <div class="calc-tabs">
                    <button class="calc-tab active" data-target="quick-concrete">Concrete</button>
                    <button class="calc-tab" data-target="quick-steel">Steel</button>
                    <button class="calc-tab" data-target="quick-hvac">HVAC</button>
                </div>
                <div class="calc-sections">
                    <div class="calc-section active" id="quick-concrete">
                        <div class="input-group">
                            <input type="number" class="calculator-input" placeholder="Length (m)" id="qc-length">
                            <input type="number" class="calculator-input" placeholder="Width (m)" id="qc-width">
                            <input type="number" class="calculator-input" placeholder="Depth (m)" id="qc-depth">
                        </div>
                        <button class="btn btn-primary" onclick="calculateQuickConcrete()">Calculate</button>
                        <div class="result" id="qc-result" style="display: none;">
                            <p>Volume: <span id="qc-volume"></span> m³</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <main class="premium-main" role="main">
            <div class="container">
                <div class="content-wrapper">
                    
                    <!-- Breadcrumb Navigation -->
                    <nav class="breadcrumb" aria-label="Breadcrumb">
                        <div class="breadcrumb-nav">
                            <a href="/">Home</a>
                            <span class="breadcrumb-separator">/</span>
                            <a href="/calculators">Calculators</a>
                            <span class="breadcrumb-separator">/</span>
                            <span class="breadcrumb-current"><?php echo $breadcrumbTitle ?? 'Calculator'; ?></span>
                        </div>
                    </nav>
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="page-header-content">
                            <div class="page-title-section">
                                <h1 class="page-title"><?php echo $pageTitle ?? 'Architecture Calculator'; ?></h1>
                                <p class="page-description"><?php echo $pageDescription ?? 'Professional calculation tools for architecture and engineering projects.'; ?></p>
                            </div>
                            <div class="page-actions">
                                <button class="btn btn-outline" onclick="exportResults()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <button class="btn btn-primary" onclick="saveProject()">
                                    <i class="fas fa-save"></i> Save Project
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Premium Status Bar -->
                    <div class="premium-status-bar">
                        <div class="status-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Enterprise Security</span>
                        </div>
                        <div class="status-item">
                            <i class="fas fa-rocket"></i>
                            <span>Real-time Calculations</span>
                        </div>
                        <div class="status-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Advanced Analytics</span>
                        </div>
                        <div class="status-item">
                            <i class="fas fa-award"></i>
                            <span>Professional Grade</span>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="main-content">
                        <?php echo $content; ?>
                    </div>
                    
                </div>
            </div>
        </main>
        
        <!-- Premium Footer -->
        <footer class="premium-footer" role="contentinfo">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section company-info">
                        <div class="footer-logo">
                            <i class="fas fa-building"></i>
                            <h3>Architecture Pro</h3>
                        </div>
                        <p>Professional $10,000 premium theme for architecture firms. Advanced calculation tools, real-time analysis, and enterprise-grade features.</p>
                        <div class="footer-features">
                            <span class="feature-tag">🏗️ Civil Engineering</span>
                            <span class="feature-tag">🏢 Structural Analysis</span>
                            <span class="feature-tag">⚙️ MEP Systems</span>
                        </div>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Calculation Tools</h4>
                        <ul>
                            <li><a href="/calculators/concrete"><i class="fas fa-cube"></i> Concrete Calculator</a></li>
                            <li><a href="/calculators/steel"><i class="fas fa-hammer"></i> Steel Calculator</a></li>
                            <li><a href="/calculators/hvac"><i class="fas fa-wind"></i> HVAC Calculator</a></li>
                            <li><a href="/calculators/electrical"><i class="fas fa-bolt"></i> Electrical Calculator</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Resources</h4>
                        <ul>
                            <li><a href="/documentation"><i class="fas fa-book"></i> Documentation</a></li>
                            <li><a href="/tutorials"><i class="fas fa-play-circle"></i> Video Tutorials</a></li>
                            <li><a href="/templates"><i class="fas fa-layer-group"></i> Project Templates</a></li>
                            <li><a href="/api"><i class="fas fa-code"></i> API Reference</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4>Support & Services</h4>
                        <ul>
                            <li><a href="/support"><i class="fas fa-headset"></i> 24/7 Support</a></li>
                            <li><a href="/training"><i class="fas fa-graduation-cap"></i> Training</a></li>
                            <li><a href="/consulting"><i class="fas fa-handshake"></i> Consulting</a></li>
                            <li><a href="/contact"><i class="fas fa-envelope"></i> Contact</a></li>
                        </ul>
                        <div class="contact-info">
                            <p><i class="fas fa-phone"></i> +1 (555) 123-ARCH</p>
                            <p><i class="fas fa-envelope"></i> support@architecturepro.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <div class="footer-copyright">
                        <p>&copy; <?php echo date('Y'); ?> Architecture Pro. All rights reserved. | $10,000 Premium Theme</p>
                    </div>
                    <div class="footer-legal">
                        <a href="/privacy">Privacy Policy</a>
                        <a href="/terms">Terms of Service</a>
                        <a href="/cookies">Cookie Policy</a>
                        <a href="/license">License Agreement</a>
                    </div>
                </div>
            </div>
        </footer>
        
    </div>
    
    <!-- Floating Action Button -->
    <div class="fab-container">
        <button class="fab-main" onclick="toggleQuickMenu()">
            <i class="fas fa-plus"></i>
        </button>
        <div class="fab-menu" id="fabMenu" style="display: none;">
            <a href="/calculators" class="fab-item" title="New Calculator">
                <i class="fas fa-calculator"></i>
            </a>
            <a href="/projects/new" class="fab-item" title="New Project">
                <i class="fas fa-folder-plus"></i>
            </a>
            <a href="/templates" class="fab-item" title="Templates">
                <i class="fas fa-layer-group"></i>
            </a>
            <a href="/reports/new" class="fab-item" title="Generate Report">
                <i class="fas fa-file-plus"></i>
            </a>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()" style="display: none;">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Premium Theme JavaScript -->
    <script src="/themes/premium/assets/js/premium-theme.js"></script>
    <script src="/themes/premium/assets/js/calculator-engine.js"></script>
    
    <!-- Architecture-specific JavaScript -->
    <script>
        // Initialize the premium architecture theme
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen
            setTimeout(() => {
                const loadingScreen = document.getElementById('loadingScreen');
                const premiumApp = document.getElementById('premium-app');
                
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                        premiumApp.style.opacity = '1';
                    }, 500);
                }
            }, 2000);
            
            // Initialize calculator engine
            if (window.ArchitectureCalculatorEngine) {
                window.calcEngine = new ArchitectureCalculatorEngine();
            }
        });
        
        // Quick calculator functions
        function toggleQuickCalculator() {
            const panel = document.getElementById('quickCalculatorPanel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
        
        function calculateQuickConcrete() {
            const length = parseFloat(document.getElementById('qc-length').value) || 0;
            const width = parseFloat(document.getElementById('qc-width').value) || 0;
            const depth = parseFloat(document.getElementById('qc-depth').value) || 0;
            
            const volume = length * width * depth;
            document.getElementById('qc-volume').textContent = volume.toFixed(2);
            document.getElementById('qc-result').style.display = 'block';
        }
        
        function exportResults() {
            alert('Export functionality - PDF, Excel, CAD formats available');
        }
        
        function saveProject() {
            alert('Project saved successfully!');
        }
        
        function toggleQuickMenu() {
            const menu = document.getElementById('fabMenu');
            menu.style.display = menu.style.display === 'none' ? 'flex' : 'none';
        }
    </script>
    
    <!-- Custom Page Scripts -->
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php $this->renderPartial('partials/footer-scripts'); ?>
    
</body>
</html>
