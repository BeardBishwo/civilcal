<!DOCTYPE html>
<html lang="en" class="admin-theme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - Bishwo Calculator</title>

    <!-- Admin Styles -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/admin/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo function_exists('csrf_token') ? csrf_token() : ($_SESSION['csrf_token'] ?? ''); ?>">
    <script>
    (function(){
      var meta = document.querySelector('meta[name="csrf-token"]');
      var token = meta ? meta.getAttribute('content') : '';
      var origFetch = window.fetch;
      window.fetch = function(input, init){
        init = init || {};
        var method = String(init.method || 'GET').toUpperCase();
        if (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE'){
          var headers = init.headers || {};
          if (typeof Headers !== 'undefined' && headers instanceof Headers){
            headers.set('X-CSRF-Token', token);
          } else if (Array.isArray(headers)){
            headers.push(['X-CSRF-Token', token]);
          } else {
            headers['X-CSRF-Token'] = token;
          }
          init.headers = headers;
          if (!init.credentials) init.credentials = 'same-origin';
        }
        return origFetch(input, init);
      };
    })();
    </script>

    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <style>
        :root {
            --admin-primary: #4f46e5;
            --admin-primary-dark: #3730a3;
            --admin-secondary: #10b981;
            --admin-danger: #ef4444;
            --admin-warning: #f59e0b;
            --admin-info: #3b82f6;
            --admin-success: #10b981;
            --admin-dark: #1f2937;
            --admin-light: #f8fafc;
            --admin-border: #e5e7eb;
            --admin-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    <?php $currentUser = function_exists('current_user') ? current_user() : null; ?>
</head>

<body class="admin-body">
    <!-- Admin Wrapper -->
    <div id="admin-wrapper" class="admin-wrapper">

        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="sidebar-logo-icon fas fa-calculator"></i>
                    <span class="logo-text"><?php echo htmlspecialchars(class_exists('\App\Services\SettingsService') ? \App\Services\SettingsService::get('site_name', 'Admin Panel') : 'Admin Panel'); ?></span>
                </div>
                <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <!-- Dashboard -->
                    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/admin') ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/dashboard'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/dashboard'); ?>">Overview</a></li>
                            <li><a href="<?php echo app_base_url('admin/configured-dashboard'); ?>">Configured Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/performance-dashboard'); ?>">Performance Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/dashboard_complex'); ?>">Analytics Dashboard</a></li>
                        </ul>
                    </li>

                    <!-- Users -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/users'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <span class="nav-text">Users</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/users'); ?>">All Users</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/create'); ?>">Add New</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/roles'); ?>">Roles</a></li>
                        </ul>
                    </li>

                    <!-- Analytics -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/analytics') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/analytics'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <span class="nav-text">Analytics</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/analytics/overview'); ?>">Overview</a></li>
                            <li><a href="<?php echo app_base_url('admin/analytics/users'); ?>">User Analytics</a></li>
                            <li><a href="<?php echo app_base_url('admin/analytics/calculators'); ?>">Calculator Usage</a></li>
                        </ul>
                    </li>

                    <!-- Content -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/content') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/content'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <span class="nav-text">Content</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/content/pages'); ?>">Pages</a></li>
                            <li><a href="<?php echo app_base_url('admin/content/menus'); ?>">Menus</a></li>
                            <li><a href="<?php echo app_base_url('admin/menu-customization'); ?>">Menu Customization</a></li>
                            <li><a href="<?php echo app_base_url('admin/content/media'); ?>">Media</a></li>
                        </ul>
                    </li>

                    <!-- Modules -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/modules') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/modules'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-puzzle-piece"></i>
                            <span class="nav-text">Modules</span>
                        </a>
                    </li>

                    <!-- Themes -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/themes') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/themes'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-palette"></i>
                            <span class="nav-text">Themes</span>
                        </a>
                    </li>

                    <!-- Settings -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/settings'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <span class="nav-text">Settings</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/settings/application'); ?>">Application</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/general'); ?>">General</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/email'); ?>">Email</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/security'); ?>">Security</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/backup'); ?>">Backup</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/advanced'); ?>">Advanced</a></li>
                        </ul>
                    </li>

                    <!-- Calculations -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/calculations') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/calculations'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-calculator"></i>
                            <span class="nav-text">Calculations</span>
                        </a>
                    </li>

                    <!-- Calculators -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/calculators') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/calculators'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <span class="nav-text">Calculators</span>
                        </a>
                    </li>

                    <!-- Widgets -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/widgets') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/widgets'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-th-large"></i>
                            <span class="nav-text">Widgets</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/widgets'); ?>">All Widgets</a></li>
                            <li><a href="<?php echo app_base_url('admin/widgets/create'); ?>">Add Widget</a></li>
                            <li><a href="<?php echo app_base_url('admin/widgets/settings'); ?>">Settings</a></li>
                        </ul>
                    </li>

                    <!-- Activity Logs -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/activity') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/activity'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <span class="nav-text">Activity Logs</span>
                        </a>
                    </li>

                    <!-- Audit Logs -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/audit-logs') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/audit-logs'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <span class="nav-text">Audit Logs</span>
                        </a>
                    </li>

                    <!-- Email Manager -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/email-manager') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/email-manager'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-envelope"></i>
                            <span class="nav-text">Email Manager</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/email-manager'); ?>">Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/email-manager/threads'); ?>">Threads</a></li>
                            <li><a href="<?php echo app_base_url('admin/email-manager/templates'); ?>">Templates</a></li>
                        </ul>
                    </li>

                    <!-- Subscriptions -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/subscriptions') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/subscriptions'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <span class="nav-text">Subscriptions</span>
                        </a>
                    </li>

                    <!-- Premium Themes -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/premium-themes') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/premium-themes'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-gem"></i>
                            <span class="nav-text">Premium Themes</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/premium-themes'); ?>">All Premium</a></li>
                            <li><a href="<?php echo app_base_url('admin/premium-themes/marketplace'); ?>">Marketplace</a></li>
                            <li><a href="<?php echo app_base_url('admin/premium-themes/create'); ?>">Create New</a></li>
                        </ul>
                    </li>

                    <!-- System -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/system') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/system-status'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-server"></i>
                            <span class="nav-text">System</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/system-status'); ?>">System Status</a></li>
                            <li><a href="<?php echo app_base_url('admin/widget-management'); ?>">Widget Management</a></li>
                            <li><a href="<?php echo app_base_url('admin/menu-customization'); ?>">Menu Customization</a></li>
                        </ul>
                    </li>

                    <!-- Debug & Testing -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/debug') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/debug'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-bug"></i>
                            <span class="nav-text">Debug</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/debug'); ?>">Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/debug/error-logs'); ?>">Error Logs</a></li>
                            <li><a href="<?php echo app_base_url('admin/debug/tests'); ?>">System Tests</a></li>
                            <li><a href="<?php echo app_base_url('admin/debug/live-errors'); ?>">Live Monitor</a></li>
                        </ul>
                    </li>

                    <!-- Backup -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/backup') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/backup'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <span class="nav-text">Backup</span>
                        </a>
                    </li>

                    <!-- Plugins -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/plugins') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/plugins'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-plug"></i>
                            <span class="nav-text">Plugins</span>
                        </a>
                    </li>
                </ul>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    <div class="system-status">
                        <div class="status-item">
                            <span class="status-dot status-success"></span>
                            <span class="status-text">System Healthy</span>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main id="admin-main" class="admin-main">

            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button id="mobile-sidebar-toggle" class="mobile-sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb">
                        <span class="breadcrumb-item">
                            <i class="fas fa-home"></i>
                            <a href="<?php echo app_base_url('admin'); ?>">Admin</a>
                        </span>
                        <?php if (isset($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $crumb): ?>
                                <span class="breadcrumb-divider">/</span>
                                <span class="breadcrumb-item">
                                    <?php if (isset($crumb['url'])): ?>
                                        <a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['title']; ?></a>
                                    <?php else: ?>
                                        <?php echo $crumb['title']; ?>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="header-right">
                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <button class="btn btn-icon" title="System Health" onclick="window.location.href='<?php echo app_base_url('admin/system-status'); ?>'">
                            <i class="fas fa-heartbeat"></i>
                        </button>
                        <button class="btn btn-icon" title="Backup" onclick="window.location.href='<?php echo app_base_url('admin/backup'); ?>'">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-icon" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>

                    <!-- User Menu -->
                    <div class="user-menu">
                        <div class="user-avatar" onclick="toggleUserDropdown()">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($currentUser['first_name'] ?? 'Admin'); ?>&background=4f46e5&color=fff" alt="Avatar">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="user-dropdown" class="user-dropdown">
                            <div class="dropdown-header">
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="dropdown-menu">
                                <a href="<?php echo app_base_url('profile'); ?>" class="dropdown-item">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <a href="<?php echo app_base_url('admin/settings'); ?>" class="dropdown-item">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo app_base_url('/'); ?>" class="dropdown-item">
                                    <i class="fas fa-external-link-alt"></i> View Site
                                </a>
                                <a href="<?php echo app_base_url('logout'); ?>" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="admin-content">
                <?php echo $content ?? ''; ?>
            </div>

        </main>

    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Loading...</span>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification-toast" class="notification-toast"></div>

    <!-- Admin Scripts -->
    <script src="<?php echo app_base_url('themes/admin/assets/js/admin.js'); ?>"></script>

    <!-- Page Specific Scripts -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Global Chart.js Helper Functions -->
    <script>
        // Global Chart.js configuration
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6b7280';
        Chart.defaults.borderColor = '#e5e7eb';

        // Helper function to create charts
        function createChart(ctx, type, data, options = {}) {
            return new Chart(ctx, {
                type: type,
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    ...options
                }
            });
        }

        // Helper function for chart colors
        function getChartColors(count = 6) {
            const colors = [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6',
                '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1', '#14b8a6'
            ];
            return colors.slice(0, count);
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const toast = document.getElementById('notification-toast');
            toast.className = `notification-toast notification-${type}`;
            toast.textContent = message;
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Loading overlay
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loading-overlay').style.display = 'none';
        }

        // User dropdown toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('user-dropdown');
            const userAvatar = document.querySelector('.user-avatar');
            
            if (!userAvatar.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>

</body>

</html>
