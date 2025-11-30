<?php
$currentUser = $_SESSION['user'] ?? [
    'full_name' => 'Admin',
    'role' => 'admin'
];
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
?>

<aside class="admin-sidebar" id="admin-sidebar">

    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-calculator-alt"></i>
            <span class="sidebar-logo-text"><?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Admin Panel')); ?></span>
        </div>
        <button class="sidebar-toggle" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-menu nav-menu">
        <ul>
            <li class="nav-item <?php echo strpos($currentPath, '/admin/dashboard') !== false || $currentPath === app_base_url('/admin') ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/dashboard'); ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/users') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/users'); ?>" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/calculations') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/calculations'); ?>" class="nav-link">
                    <i class="fas fa-calculator"></i>
                    <span>Calculations</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/calculators') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/calculators'); ?>" class="nav-link">
                    <i class="fas fa-calculator"></i>
                    <span>Calculators</span>
                </a>
            </li>

            <!-- Analytics Submenu -->
            <li class="nav-item has-submenu <?php echo strpos($currentPath, '/admin/analytics') !== false ? 'active' : ''; ?>">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                    <i class="nav-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu">
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/analytics/overview'); ?>" class="nav-link"><i class="fas fa-circle"></i> Overview</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/analytics/users'); ?>" class="nav-link"><i class="fas fa-circle"></i> Users</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" class="nav-link"><i class="fas fa-circle"></i> Calculators</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" class="nav-link"><i class="fas fa-circle"></i> Performance</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/analytics/reports'); ?>" class="nav-link"><i class="fas fa-circle"></i> Reports</a></li>
                </ul>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/content') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/content'); ?>" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Content</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/activity') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/activity'); ?>" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Activity</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/audit-logs') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/audit-logs'); ?>" class="nav-link">
                    <i class="fas fa-search"></i>
                    <span>Audit Logs</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/email-manager') !== false || strpos($currentPath, '/admin/email') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="nav-link">
                    <i class="fas fa-envelope"></i>
                    <span>Email Manager</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/subscriptions') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/subscriptions'); ?>" class="nav-link">
                    <i class="fas fa-credit-card"></i>
                    <span>Subscriptions</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/widgets') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="nav-link">
                    <i class="fas fa-cube"></i>
                    <span>Widgets</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/premium-themes') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/premium-themes'); ?>" class="nav-link">
                    <i class="fas fa-star"></i>
                    <span>Premium Themes</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/modules') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/modules'); ?>" class="nav-link">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Modules</span>
                </a>
            </li>

            <!-- Settings Section -->
            <li class="menu-divider">
                <span>Configuration</span>
            </li>

            <li class="nav-item has-submenu <?php echo strpos($currentPath, '/admin/settings') !== false ? 'active' : ''; ?>">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="nav-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu">
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/settings/general'); ?>" class="nav-link"><i class="fas fa-circle"></i> General</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/settings/email'); ?>" class="nav-link"><i class="fas fa-circle"></i> Email</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/settings/security'); ?>" class="nav-link"><i class="fas fa-circle"></i> Security</a></li>
                </ul>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/themes') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/themes'); ?>" class="nav-link">
                    <i class="fas fa-palette"></i>
                    <span>Themes</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/plugins') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/plugins'); ?>" class="nav-link">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Plugins</span>
                </a>
            </li>

            <!-- System Section -->
            <li class="menu-divider">
                <span>System</span>
            </li>

            <!-- Debug Submenu -->
            <li class="nav-item has-submenu <?php echo strpos($currentPath, '/admin/debug') !== false ? 'active' : ''; ?>">
                <a href="#" class="nav-link">
                    <i class="fas fa-bug"></i>
                    <span>Debug</span>
                    <i class="nav-arrow fas fa-chevron-right"></i>
                </a>
                <ul class="nav-submenu">
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/debug'); ?>" class="nav-link"><i class="fas fa-circle"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/debug/error-logs'); ?>" class="nav-link"><i class="fas fa-circle"></i> Error Logs</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/debug/tests'); ?>" class="nav-link"><i class="fas fa-circle"></i> Tests</a></li>
                    <li class="nav-item"><a href="<?php echo app_base_url('/admin/debug/live-errors'); ?>" class="nav-link"><i class="fas fa-circle"></i> Live Errors</a></li>
                </ul>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/backup') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/backup'); ?>" class="nav-link">
                    <i class="fas fa-database"></i>
                    <span>Backup</span>
                </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/system-status') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/system-status'); ?>" class="nav-link">
                    <i class="fas fa-heartbeat"></i>
                    <span>System Status</span>
                </a>
            </li>

            <!-- Help -->
            <li class="menu-divider">
                <span>Support</span>
            </li>

            <li class="nav-item <?php echo strpos($currentPath, '/admin/help') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/help'); ?>" class="nav-link">
                    <i class="fas fa-question-circle"></i>
                    <span>Help Center</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin'); ?></div>
                <div class="sidebar-user-role"><?php echo htmlspecialchars(ucfirst($currentUser['role'] ?? 'admin')); ?></div>
            </div>
        </div>
    </div>

</aside>