<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <title><?= htmlspecialchars($title ?? 'ProCalculator - Premium Engineering Platform') ?></title>
    
    <!-- Premium Meta Tags -->
    <meta name="description" content="<?= htmlspecialchars($description ?? 'Ultra-premium engineering calculator platform with $100K quality design and professional features') ?>">
    <meta name="author" content="Bishwo Calculator Team">
    <meta name="theme-color" content="#1a1a2e">
    <meta name="msapplication-TileColor" content="#1a1a2e">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="/themes/procalculator/assets/favicon.ico">
    <link rel="apple-touch-icon" href="/themes/procalculator/assets/apple-touch-icon.png">
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Premium Stylesheets -->
    <?php foreach (['css/procalculator-premium.css', 'css/glassmorphism.css', 'css/animations.css', 'css/responsive.css'] as $style): ?>
        <link rel="stylesheet" href="/themes/procalculator/<?= htmlspecialchars($style) ?>">
    <?php endforeach; ?>
    
    <!-- Page-specific styles -->
    <?php if (isset($additional_styles)): ?>
        <?php foreach ($additional_styles as $style): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="procalculator-theme premium-dark-mode">
    <!-- Premium Loading Screen -->
    <div id="premium-loader" class="premium-loader">
        <div class="loader-content">
            <div class="loader-logo">
                <div class="logo-icon premium-gradient">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
            <div class="loader-text">
                <h2>ProCalculator</h2>
                <p>Loading Premium Experience...</p>
            </div>
            <div class="loader-progress">
                <div class="progress-bar premium-gradient"></div>
            </div>
        </div>
    </div>

    <!-- Premium Header -->
    <header class="procalculator-header glassmorphism-header" role="banner">
        <div class="header-container">
            <!-- Brand Section -->
            <div class="header-brand">
                <a href="/dashboard" class="brand-link premium-hover" aria-label="ProCalculator Home">
                    <div class="brand-logo premium-gradient">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="brand-text">
                        <h1 class="brand-title">ProCalculator</h1>
                        <span class="brand-subtitle">Premium</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="header-navigation" role="navigation" aria-label="Main navigation">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="/calculators" class="nav-link dropdown-toggle">
                            <i class="fas fa-calculator"></i>
                            <span>Calculators</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <div class="dropdown-menu glassmorphism-dropdown">
                            <div class="dropdown-section">
                                <h4>Engineering Categories</h4>
                                <div class="dropdown-grid">
                                    <a href="/calculators/civil" class="dropdown-item">
                                        <i class="fas fa-building"></i>
                                        <span>Civil Engineering</span>
                                    </a>
                                    <a href="/calculators/electrical" class="dropdown-item">
                                        <i class="fas fa-bolt"></i>
                                        <span>Electrical</span>
                                    </a>
                                    <a href="/calculators/plumbing" class="dropdown-item">
                                        <i class="fas fa-faucet"></i>
                                        <span>Plumbing</span>
                                    </a>
                                    <a href="/calculators/hvac" class="dropdown-item">
                                        <i class="fas fa-wind"></i>
                                        <span>HVAC</span>
                                    </a>
                                    <a href="/calculators/fire" class="dropdown-item">
                                        <i class="fas fa-fire-extinguisher"></i>
                                        <span>Fire Safety</span>
                                    </a>
                                    <a href="/calculators/structural" class="dropdown-item">
                                        <i class="fas fa-drafting-compass"></i>
                                        <span>Structural</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="/history" class="nav-link">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/favorites" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span>Favorites</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/export" class="nav-link">
                            <i class="fas fa-download"></i>
                            <span>Export</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Actions -->
            <div class="header-actions">
                <!-- Search -->
                <div class="header-search">
                    <button class="search-toggle premium-btn-icon" aria-label="Toggle search">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-panel glassmorphism-panel">
                        <form class="search-form" role="search">
                            <div class="search-input-group">
                                <input type="text" class="search-input" placeholder="Search calculators, history..." aria-label="Search">
                                <button type="submit" class="search-submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="header-notifications">
                    <button class="notification-toggle premium-btn-icon" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="notification-panel glassmorphism-panel">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <button class="mark-all-read">Mark all read</button>
                        </div>
                        <div class="notification-list">
                            <div class="notification-item unread">
                                <i class="fas fa-info-circle"></i>
                                <div class="notification-content">
                                    <p>New calculator module available</p>
                                    <span class="notification-time">2 minutes ago</span>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <i class="fas fa-check-circle"></i>
                                <div class="notification-content">
                                    <p>Calculation completed successfully</p>
                                    <span class="notification-time">1 hour ago</span>
                                </div>
                            </div>
                            <div class="notification-item">
                                <i class="fas fa-cog"></i>
                                <div class="notification-content">
                                    <p>System maintenance scheduled</p>
                                    <span class="notification-time">1 day ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="header-user">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-profile dropdown">
                            <button class="user-toggle premium-btn-icon" aria-label="User menu">
                                <div class="user-avatar">
                                    <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '/themes/procalculator/assets/images/default-avatar.png') ?>" 
                                         alt="User avatar" class="avatar-image">
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                                    <span class="user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? 'Engineer') ?></span>
                                </div>
                                <i class="fas fa-chevron-down dropdown-icon"></i>
                            </button>
                            <div class="user-menu glassmorphism-dropdown">
                                <div class="user-menu-section">
                                    <div class="user-menu-item">
                                        <i class="fas fa-user"></i>
                                        <a href="/profile">My Profile</a>
                                    </div>
                                    <div class="user-menu-item">
                                        <i class="fas fa-cog"></i>
                                        <a href="/settings">Settings</a>
                                    </div>
                                    <div class="user-menu-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <a href="/security">Security</a>
                                    </div>
                                    <div class="user-menu-item">
                                        <i class="fas fa-question-circle"></i>
                                        <a href="/help">Help & Support</a>
                                    </div>
                                </div>
                                <div class="user-menu-divider"></div>
                                <div class="user-menu-section">
                                    <div class="user-menu-item">
                                        <i class="fas fa-moon"></i>
                                        <button class="theme-toggle">Dark Mode</button>
                                    </div>
                                    <div class="user-menu-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <a href="/logout">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="/auth/login" class="btn btn-outline premium-btn">
                                <i class="fas fa-sign-in-alt"></i>
                                Login
                            </a>
                            <a href="/auth/register" class="btn btn-primary premium-btn">
                                <i class="fas fa-user-plus"></i>
                                Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle premium-btn-icon" aria-label="Toggle mobile menu">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav glassmorphism-panel">
            <div class="mobile-nav-header">
                <h3>Menu</h3>
                <button class="mobile-nav-close" aria-label="Close mobile menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="mobile-nav-menu" role="navigation">
                <ul class="mobile-nav-list">
                    <li class="mobile-nav-item">
                        <a href="/dashboard" class="mobile-nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="/calculators" class="mobile-nav-link">
                            <i class="fas fa-calculator"></i>
                            <span>Calculators</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="/history" class="mobile-nav-link">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="/favorites" class="mobile-nav-link">
                            <i class="fas fa-star"></i>
                            <span>Favorites</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="/export" class="mobile-nav-link">
                            <i class="fas fa-download"></i>
                            <span>Export</span>
                        </a>
                    </li>
                </ul>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mobile-nav-divider"></div>
                    <ul class="mobile-nav-list">
                        <li class="mobile-nav-item">
                            <a href="/profile" class="mobile-nav-link">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="mobile-nav-item">
                            <a href="/settings" class="mobile-nav-link">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li class="mobile-nav-item">
                            <a href="/logout" class="mobile-nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                <?php else: ?>
                    <div class="mobile-nav-divider"></div>
                    <ul class="mobile-nav-list">
                        <li class="mobile-nav-item">
                            <a href="/auth/login" class="mobile-nav-link">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        </li>
                        <li class="mobile-nav-item">
                            <a href="/auth/register" class="mobile-nav-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="main-content" role="main">
        <!-- Page-specific content will be rendered here -->
