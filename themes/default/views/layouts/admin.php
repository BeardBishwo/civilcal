<!DOCTYPE html>
<html lang="en" class="admin-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Admin Panel - Bishwo Calculator</title>
    
    <!-- Admin Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Administrative panel for Bishwo Calculator management system">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo $base_url; ?>assets/images/applogo.png">
    
    <!-- Admin Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php
    // Load theme styles
    if (isset($theme) && method_exists($theme, 'loadThemeStyles')) {
        $theme->loadThemeStyles();
    }
    
    // Load admin-specific styles
    if (isset($theme) && method_exists($theme, 'loadAdminStyles')) {
        $theme->loadAdminStyles();
    }
    ?>
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <img src="<?php echo $base_url; ?>assets/images/applogo.png" alt="Bishwo Calculator" onerror="this.style.display='none'">
                <h2>Admin Panel</h2>
            </div>
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-section">
                    <span class="section-title">Content Management</span>
                </li>
                
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle" onclick="toggleDropdown(this)">
                        <i class="fas fa-calculator"></i>
                        <span>Calculators</span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $base_url; ?>admin/calculators/" class="dropdown-item">All Calculators</a></li>
                        <li><a href="<?php echo $base_url; ?>admin/calculators/civil.php" class="dropdown-item">Civil Engineering</a></li>
                        <li><a href="<?php echo $base_url; ?>admin/calculators/electrical.php" class="dropdown-item">Electrical</a></li>
                        <li><a href="<?php echo $base_url; ?>admin/calculators/hvac.php" class="dropdown-item">HVAC</a></li>
                        <li><a href="<?php echo $base_url; ?>admin/calculators/plumbing.php" class="dropdown-item">Plumbing</a></li>
                        <li><a href="<?php echo $base_url; ?>admin/calculators/structural.php" class="dropdown-item">Structural</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/categories.php" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/pages.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </li>
                
                <li class="nav-section">
                    <span class="section-title">User Management</span>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/roles.php" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Roles & Permissions</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/activity.php" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Activity Log</span>
                    </a>
                </li>
                
                <li class="nav-section">
                    <span class="section-title">System</span>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/themes.php" class="nav-link">
                        <i class="fas fa-palette"></i>
                        <span>Themes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/backup.php" class="nav-link">
                        <i class="fas fa-database"></i>
                        <span>Backup & Restore</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/logs.php" class="nav-link">
                        <i class="fas fa-file-medical"></i>
                        <span>System Logs</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/analytics.php" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                
                <li class="nav-section">
                    <span class="section-title">Tools</span>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/maintenance.php" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>admin/updates.php" class="nav-link">
                        <i class="fas fa-download"></i>
                        <span>Updates</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>modules/estimation/" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>View Site</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <!-- Admin Main Content -->
    <main class="admin-main">
        <!-- Admin Header -->
        <header class="admin-header">
            <div class="header-left">
                <button class="mobile-sidebar-toggle" onclick="toggleMobileSidebar()" aria-label="Toggle mobile sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="breadcrumb">
                    <nav aria-label="Breadcrumb">
                        <ol class="breadcrumb-list">
                            <li class="breadcrumb-item">
                                <a href="<?php echo $base_url; ?>admin/dashboard.php">Admin</a>
                            </li>
                            <?php if (isset($breadcrumb)): ?>
                                <?php foreach ($breadcrumb as $item): ?>
                                    <li class="breadcrumb-item">
                                        <?php if (isset($item['url'])): ?>
                                            <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
                                        <?php else: ?>
                                            <span><?php echo $item['title']; ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="header-right">
                <!-- Quick Actions -->
                <div class="header-actions">
                    <button class="action-btn" onclick="clearCache()" title="Clear Cache">
                        <i class="fas fa-broom"></i>
                    </button>
                    
                    <button class="action-btn" onclick="viewSite()" title="View Site">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </div>
                
                <!-- User Menu -->
                <div class="user-menu">
                    <button class="user-toggle" onclick="toggleUserMenu(this)">
                        <div class="user-avatar">
                            <img src="<?php echo $base_url; ?>assets/images/profile.png" alt="Admin" onerror="this.style.display='none'">
                            <span>A</span>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator'; ?></span>
                            <span class="user-role"><?php echo isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Super Admin'; ?></span>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <div class="user-dropdown">
                        <a href="<?php echo $base_url; ?>admin/profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <a href="<?php echo $base_url; ?>admin/settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo $base_url; ?>admin/logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Admin Content Area -->
        <div class="admin-content">
            <!-- Page Header -->
            <?php if (isset($page_header) && $page_header): ?>
            <div class="page-header">
                <div class="page-title">
                    <h1><?php echo $page_title ?? 'Page Title'; ?></h1>
                    <?php if (isset($page_description)): ?>
                        <p class="page-description"><?php echo $page_description; ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($page_actions) && $page_actions): ?>
                <div class="page-actions">
                    <?php echo $page_actions; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Alerts -->
            <?php if (isset($_SESSION['admin_success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></span>
                <button class="alert-close" onclick="closeAlert(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></span>
                <button class="alert-close" onclick="closeAlert(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_warning'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo $_SESSION['admin_warning']; unset($_SESSION['admin_warning']); ?></span>
                <button class="alert-close" onclick="closeAlert(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_info'])): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <span><?php echo $_SESSION['admin_info']; unset($_SESSION['admin_info']); ?></span>
                <button class="alert-close" onclick="closeAlert(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Main Content -->
            <div class="content-wrapper">
                <?php echo $content ?? ''; ?>
            </div>
        </div>
    </main>
    
    <!-- Admin Scripts -->
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.querySelector('.admin-main');
            
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed');
        }
        
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('mobile-open');
        }
        
        // Dropdown Toggle
        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            const isOpen = dropdown.style.display === 'block';
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.style.display = 'none';
            });
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.classList.remove('active');
            });
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdown.style.display = 'block';
                button.classList.add('active');
            }
        }
        
        // User Menu Toggle
        function toggleUserMenu(button) {
            const dropdown = button.nextElementSibling;
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.nav-item.dropdown') && 
                !event.target.closest('.user-menu')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.classList.remove('active');
                });
            }
        });
        
        // Quick Actions
        function clearCache() {
            // Simulate cache clearing
            showAlert('success', 'Cache cleared successfully');
        }
        
        function viewSite() {
            window.open('<?php echo $base_url; ?>', '_blank');
        }
        
        // Alert Functions
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = `
                <i class="fas fa-${getAlertIcon(type)}"></i>
                <span>${message}</span>
                <button class="alert-close" onclick="closeAlert(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            const contentWrapper = document.querySelector('.admin-content');
            contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    closeAlert(alertDiv.querySelector('.alert-close'));
                }
            }, 5000);
        }
        
        function getAlertIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }
        
        function closeAlert(button) {
            const alert = button.closest('.alert');
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }
        
        // Keyboard Shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl + B: Toggle sidebar
            if (event.ctrlKey && event.key === 'b') {
                event.preventDefault();
                toggleSidebar();
            }
            
            // Ctrl + /: Focus search (if exists)
            if (event.ctrlKey && event.key === '/') {
                event.preventDefault();
                const searchInput = document.querySelector('.admin-search input');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
        
        // Initialize Admin Panel
        document.addEventListener('DOMContentLoaded', function() {
            // Set active nav item
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // Auto-close mobile sidebar when clicking outside
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('adminSidebar');
                const toggleBtn = document.querySelector('.mobile-sidebar-toggle');
                
                if (window.innerWidth <= 768 && 
                    sidebar.classList.contains('mobile-open') && 
                    !sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });
    </script>
    
    <style>
        /* Admin Theme Variables */
        :root {
            --admin-primary: #4f46e5;
            --admin-primary-dark: #4338ca;
            --admin-primary-light: #6366f1;
            --admin-secondary: #64748b;
            --admin-success: #10b981;
            --admin-warning: #f59e0b;
            --admin-error: #ef4444;
            --admin-info: #3b82f6;
            
            --admin-bg: #f8fafc;
            --admin-bg-light: #ffffff;
            --admin-bg-dark: #1e293b;
            
            --admin-text: #1e293b;
            --admin-text-light: #64748b;
            --admin-text-muted: #94a3b8;
            
            --admin-border: #e2e8f0;
            --admin-border-light: #f1f5f9;
            
            --admin-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --admin-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Admin Layout */
        .admin-body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--admin-bg);
            color: var(--admin-text);
            overflow-x: hidden;
        }
        
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: var(--admin-bg-light);
            border-right: 1px solid var(--admin-border);
            box-shadow: var(--admin-shadow);
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            border-bottom: 1px solid var(--admin-border);
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .admin-logo img {
            height: 32px;
            width: auto;
        }
        
        .admin-logo h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--admin-text);
            margin: 0;
        }
        
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--admin-text-light);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .sidebar-toggle:hover {
            background: var(--admin-bg);
            color: var(--admin-text);
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }
        
        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-section {
            padding: 1rem 1.5rem 0.5rem;
        }
        
        .section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .nav-item {
            margin: 0.25rem 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--admin-text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
        }
        
        .nav-link:hover {
            background: var(--admin-bg);
            color: var(--admin-text);
        }
        
        .nav-link.active {
            background: var(--admin-primary);
            color: white;
        }
        
        .nav-link i {
            font-size: 1rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .dropdown-toggle {
            cursor: pointer;
        }
        
        .dropdown-icon {
            margin-left: auto;
            transition: transform 0.2s;
        }
        
        .dropdown-toggle.active .dropdown-icon {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            display: none;
            list-style: none;
            margin: 0;
            padding: 0.5rem 0;
            background: var(--admin-bg);
        }
        
        .dropdown-item {
            display: block;
            padding: 0.5rem 1.5rem 0.5rem 3.5rem;
            color: var(--admin-text-light);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background: var(--admin-bg-light);
            color: var(--admin-text);
        }
        
        .admin-main {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .admin-header {
            background: var(--admin-bg-light);
            border-bottom: 1px solid var(--admin-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .mobile-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--admin-text-light);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-list {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 0.5rem;
        }
        
        .breadcrumb-item {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 0.5rem;
            color: var(--admin-text-muted);
        }
        
        .breadcrumb-item a {
            color: var(--admin-text-light);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: var(--admin-text);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: none;
            border: 1px solid var(--admin-border);
            color: var(--admin-text-light);
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
        }
        
        .action-btn:hover {
            background: var(--admin-bg);
            color: var(--admin-text);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .user-toggle:hover {
            background: var(--admin-bg);
        }
        
        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--admin-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-name {
            font-weight: 500;
            color: var(--admin-text);
            font-size: 0.875rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--admin-text-muted);
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--admin-bg-light);
            border: 1px solid var(--admin-border);
            border-radius: 0.375rem;
            box-shadow: var(--admin-shadow-lg);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--admin-text);
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .dropdown-item:hover {
            background: var(--admin-bg);
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--admin-border);
            margin: 0.5rem 0;
        }
        
        .admin-content {
            padding: 2rem;
            max-width: 100%;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        
        .page-title h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--admin-text);
            margin: 0 0 0.5rem 0;
        }
        
        .page-description {
            color: var(--admin-text-light);
            font-size: 1rem;
            margin: 0;
        }
        
        .page-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            transition: opacity 0.3s;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-color: var(--admin-success);
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: var(--admin-error);
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-color: var(--admin-warning);
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-color: var(--admin-info);
        }
        
        .alert-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            margin-left: auto;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: background 0.2s;
        }
        
        .alert-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .content-wrapper {
            background: var(--admin-bg-light);
            border-radius: 0.5rem;
            box-shadow: var(--admin-shadow);
            overflow: hidden;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .mobile-sidebar-toggle {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .admin-header {
                padding: 1rem;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-info {
                display: none;
            }
        }
    </style>
</body>
</html>
