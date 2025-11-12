<?php
/**
 * ProCalculator Header Partial
 * Navigation and header component
 */
?>
<!-- Premium Header -->
<header class="procalculator-header glassmorphism-header" role="banner">
    <div class="header-container">
        <!-- Brand Section -->
        <div class="header-brand">
            <a href="<?= $viewHelper->url('') ?>" class="brand-link premium-hover" aria-label="ProCalculator Home">
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
                    <a href="<?= $viewHelper->url('dashboard') ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="<?= $viewHelper->url('calculators') ?>" class="nav-link dropdown-toggle">
                        <i class="fas fa-calculator"></i>
                        <span>Calculators</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <div class="dropdown-menu glassmorphism-dropdown">
                        <div class="dropdown-section">
                            <h4>Engineering Categories</h4>
                            <div class="dropdown-grid">
                                <a href="<?= $viewHelper->url('calculator/civil') ?>" class="dropdown-item">
                                    <i class="fas fa-building"></i>
                                    <span>Civil Engineering</span>
                                </a>
                                <a href="<?= $viewHelper->url('calculator/electrical') ?>" class="dropdown-item">
                                    <i class="fas fa-bolt"></i>
                                    <span>Electrical</span>
                                </a>
                                <a href="<?= $viewHelper->url('calculator/plumbing') ?>" class="dropdown-item">
                                    <i class="fas fa-faucet"></i>
                                    <span>Plumbing</span>
                                </a>
                                <a href="<?= $viewHelper->url('calculator/hvac') ?>" class="dropdown-item">
                                    <i class="fas fa-wind"></i>
                                    <span>HVAC</span>
                                </a>
                                <a href="<?= $viewHelper->url('calculator/fire') ?>" class="dropdown-item">
                                    <i class="fas fa-fire-extinguisher"></i>
                                    <span>Fire Safety</span>
                                </a>
                                <a href="<?= $viewHelper->url('calculator/structural') ?>" class="dropdown-item">
                                    <i class="fas fa-drafting-compass"></i>
                                    <span>Structural</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- User Actions -->
        <div class="header-actions">
            <!-- Search -->
            <div class="header-search">
                <button class="search-toggle premium-btn-icon" aria-label="Toggle search">
                </button>
                <div class="search-panel glassmorphism-panel">
                    <form class="search-form" role="search">
                        <div class="search-input-group">
                            <input type="text" class="search-input" placeholder="Search calculators, history..." aria-label="Search">
                            <button type="submit" class="search-submit">
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($user)): ?>
            <!-- Theme Toggle (Guest Visible) -->
            <div class="header-theme-toggle">
                <button class="theme-toggle-btn premium-btn-icon dark-mode-toggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Add theme toggle for logged-in users as well -->
            <?php if (!empty($user)): ?>
            <div class="header-theme-toggle">
                <button class="theme-toggle-btn premium-btn-icon dark-mode-toggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            <?php endif; ?>

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
                    <div class="user-profile-dropdown">
                        <button class="user-profile-toggle" aria-label="User menu" aria-expanded="false">
                            <div class="user-avatar-wrapper">
                                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '/themes/procalculator/assets/images/default-avatar.png') ?>" 
                                     alt="<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>" 
                                     class="user-avatar-img">
                                <div class="user-status-indicator online"></div>
                            </div>
                            <div class="user-info-text">
                                <span class="user-display-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                                <span class="user-display-role"><?= ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'User')) ?></span>
                            </div>
                            <i class="fas fa-chevron-down user-dropdown-arrow"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="user-dropdown-menu glassmorphism-dropdown">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-avatar">
                                    <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '/themes/procalculator/assets/images/default-avatar.png') ?>" 
                                         alt="<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>">
                                </div>
                                <div class="user-dropdown-info">
                                    <h4><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></h4>
                                    <p><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
                                    <span class="user-badge"><?= ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'User')) ?></span>
                                </div>
                            </div>
                            
                            <div class="user-dropdown-divider"></div>
                            
                            <div class="user-dropdown-section">
                                <a href="<?= $viewHelper->url('profile') ?>" class="user-dropdown-item">
                                    <i class="fas fa-user-circle"></i>
                                    <span>My Profile</span>
                                </a>
                                <a href="<?= $viewHelper->url('profile') ?>" class="user-dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <a href="<?= $viewHelper->url('profile') ?>" class="user-dropdown-item">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Security</span>
                                </a>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="<?= $viewHelper->url('admin') ?>" class="user-dropdown-item">
                                    <i class="fas fa-crown"></i>
                                    <span>Admin Panel</span>
                                </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="user-dropdown-divider"></div>
                            
                            <div class="user-dropdown-section">
                                <button class="user-dropdown-item theme-toggle-btn dark-mode-toggle" aria-label="Toggle theme">
                                    <i class="fas fa-moon"></i>
                                    <span>Dark Mode</span>
                                </button>
                                <a href="<?= $viewHelper->url('contact') ?>" class="user-dropdown-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Help & Support</span>
                                </a>
                            </div>
                            
                            <div class="user-dropdown-divider"></div>
                            
                            <div class="user-dropdown-section">
                                <a href="<?= $viewHelper->url('logout') ?>" class="user-dropdown-item logout-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="<?= $viewHelper->url('login') ?>" class="btn btn-outline premium-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                        <a href="<?= $viewHelper->url('register') ?>" class="btn btn-primary premium-btn">
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
                    <a href="<?= $viewHelper->url('dashboard') ?>" class="mobile-nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?= $viewHelper->url('calculators') ?>" class="mobile-nav-link">
                        <i class="fas fa-calculator"></i>
                        <span>Calculators</span>
                    </a>
                </li>
            </ul>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mobile-nav-divider"></div>
                <ul class="mobile-nav-list">
                    <li class="mobile-nav-item">
                        <a href="<?= $viewHelper->url('profile') ?>" class="mobile-nav-link">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?= $viewHelper->url('profile') ?>" class="mobile-nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?= $viewHelper->url('logout') ?>" class="mobile-nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            <?php else: ?>
                <div class="mobile-nav-divider"></div>
                <ul class="mobile-nav-list">
                    <li class="mobile-nav-item">
                        <a href="<?= $viewHelper->url('login') ?>" class="mobile-nav-link">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a href="<?= $viewHelper->url('register') ?>" class="mobile-nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
    </div>
</header>