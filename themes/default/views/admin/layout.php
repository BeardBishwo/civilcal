<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #0f172a;
            color: #f9fafb;
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(102, 126, 234, 0.2);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }

        .admin-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.3);
        }

        .admin-sidebar-header h2 {
            color: #4cc9f0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .admin-user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.3);
            margin-bottom: 1rem;
        }

        .admin-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #4cc9f0, #34d399);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f9fafb;
            font-weight: bold;
        }

        .admin-user-info {
            flex: 1;
        }

        .admin-user-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .admin-user-role {
            font-size: 0.8rem;
            color: #34d399;
        }

        .admin-sidebar-nav {
            flex: 1;
            padding: 1rem 0;
        }

        .admin-nav-section {
            margin-bottom: 1.5rem;
        }

        .admin-nav-section h3 {
            color: #9ca3af;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            padding: 0 1.5rem;
            font-weight: 600;
        }

        .admin-nav-links {
            list-style: none;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.2s ease;
            color: #9ca3af;
        }

        .admin-nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #f9fafb;
        }

        .admin-nav-link.active {
            background: rgba(102, 126, 234, 0.15);
            color: #4cc9f0;
        }

        .admin-nav-link i {
            width: 20px;
            text-align: center;
        }

        .admin-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .admin-header-left h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #f9fafb;
            margin-bottom: 1rem;
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease;
        }

        .admin-card:hover {
            transform: translateY(-2px);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .status-success {
            color: #34d399;
        }

        .status-warning {
            color: #fbbf24;
        }

        .status-error {
            color: #f87171;
        }

        .admin-breadcrumb {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .admin-breadcrumb a {
            color: #4cc9f0;
            text-decoration: none;
        }

        .admin-breadcrumb a:hover {
            text-decoration: underline;
        }

        .admin-breadcrumb .active {
            color: #f9fafb;
        }

        .admin-submenu {
            display: none;
            padding-left: 2.5rem;
        }

        .admin-nav-link.expanded + .admin-submenu {
            display: block;
        }

        .admin-submenu .admin-nav-link {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
        }

        .admin-toggle-menu {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: rgba(102, 126, 234, 0.2);
            color: #f9fafb;
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 6px;
            padding: 0.5rem;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
                padding: 1rem;
            }

            .admin-toggle-menu {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile menu toggle button -->
    <button class="admin-toggle-menu" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <nav class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <h2>Admin Panel</h2>
            </div>

            <!-- User Profile Section -->
            <div class="admin-user-profile">
                <div class="admin-user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="admin-user-info">
                    <div class="admin-user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin User'); ?></div>
                    <div class="admin-user-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></div>
                </div>
            </div>

            <div class="admin-sidebar-nav">
                <!-- Dashboard Section -->
                <div class="admin-nav-section">
                    <h3>Main</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/dashboard'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Management Section -->
                <div class="admin-nav-section">
                    <h3>Management</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/users'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'users' ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/modules'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'modules' ? 'active' : ''; ?>">
                                <i class="fas fa-cubes"></i>
                                <span>Modules</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/calculators'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'calculators' ? 'active' : ''; ?>">
                                <i class="fas fa-calculator"></i>
                                <span>Calculators</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/calculations'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'calculations' ? 'active' : ''; ?>">
                                <i class="fas fa-list"></i>
                                <span>Calculations</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Content Section -->
                <div class="admin-nav-section">
                    <h3>Content</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/content'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'content' ? 'active' : ''; ?>">
                                <i class="fas fa-file-alt"></i>
                                <span>Content Manager</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/email'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'email' ? 'active' : ''; ?>">
                                <i class="fas fa-envelope"></i>
                                <span>Email Manager</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Customization Section -->
                <div class="admin-nav-section">
                    <h3>Customization</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/themes'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'themes' ? 'active' : ''; ?>">
                                <i class="fas fa-palette"></i>
                                <span>Themes</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/premium-themes'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'premium-themes' ? 'active' : ''; ?>">
                                <i class="fas fa-crown"></i>
                                <span>Premium Themes</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/plugins'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'plugins' ? 'active' : ''; ?>">
                                <i class="fas fa-plug"></i>
                                <span>Plugins</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Analytics Section -->
                <div class="admin-nav-section">
                    <h3>Analytics</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/analytics'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'analytics' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line"></i>
                                <span>Overview</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/analytics/users'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'analytics-users' ? 'active' : ''; ?>">
                                <i class="fas fa-users-cog"></i>
                                <span>User Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'analytics-calculators' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-bar"></i>
                                <span>Calculator Stats</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'analytics-performance' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line"></i>
                                <span>Performance</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- System Section -->
                <div class="admin-nav-section">
                    <h3>System</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/settings'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'settings' ? 'active' : ''; ?>">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/logs'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'logs' ? 'active' : ''; ?>">
                                <i class="fas fa-file-alt"></i>
                                <span>System Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/error-logs'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'error-logs' ? 'active' : ''; ?>">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Error Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/activity'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'activity' ? 'active' : ''; ?>">
                                <i class="fas fa-history"></i>
                                <span>Activity Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/audit-logs'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'audit-logs' ? 'active' : ''; ?>">
                                <i class="fas fa-shield-alt"></i>
                                <span>Audit Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/backup'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'backup' ? 'active' : ''; ?>">
                                <i class="fas fa-database"></i>
                                <span>Backup</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/system-status'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'system-status' ? 'active' : ''; ?>">
                                <i class="fas fa-heartbeat"></i>
                                <span>System Status</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo app_base_url('/admin/setup/checklist'); ?>"
                               class="admin-nav-link <?php echo ($currentPage ?? '') === 'setup-checklist' ? 'active' : ''; ?>">
                                <i class="fas fa-tasks"></i>
                                <span>Setup Checklist</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Logout Section -->
                <div class="admin-nav-section">
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/logout'); ?>"
                               class="admin-nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="admin-main">
            <!-- Breadcrumb Navigation -->
            <?php if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0): ?>
                <div class="admin-breadcrumb">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($index > 0): ?> &gt; <?php endif; ?>
                        <?php if (isset($breadcrumb['url']) && $index < count($breadcrumbs) - 1): ?>
                            <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>">
                                <?php echo htmlspecialchars($breadcrumb['name']); ?>
                            </a>
                        <?php else: ?>
                            <span class="active"><?php echo htmlspecialchars($breadcrumb['name']); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <header class="admin-header">
                <div class="admin-header-left">
                    <h1><?php echo $page_title ?? $title ?? 'Admin Panel'; ?></h1>
                </div>
            </header>

            <!-- Page Content -->
            <?php echo $content ?? ''; ?>

        </main>
    </div>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('adminSidebar');

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = menuToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>