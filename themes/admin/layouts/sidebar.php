<?php
/**
 * Admin Sidebar Layout Component
 * Reusable sidebar for admin panel
 */

// Get current user and permissions
$currentUser = $_SESSION['user'] ?? null;
$userRole = $currentUser['role'] ?? 'user';
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Menu items configuration
$menuItems = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'url' => '/admin/dashboard',
        'submenu' => [
            'overview' => ['title' => 'Overview', 'url' => '/admin/dashboard'],
            'complex' => ['title' => 'Complex Dashboard', 'url' => '/admin/dashboard/complex'],
            'configured' => ['title' => 'Configured Dashboard', 'url' => '/admin/dashboard/configured'],
            'performance' => ['title' => 'Performance Dashboard', 'url' => '/admin/dashboard/performance']
        ]
    ],
    'users' => [
        'title' => 'User Management',
        'icon' => 'fas fa-users',
        'url' => '/admin/users',
        'submenu' => [
            'all-users' => ['title' => 'All Users', 'url' => '/admin/users'],
            'create-user' => ['title' => 'Create User', 'url' => '/admin/users/create'],
            'user-roles' => ['title' => 'User Roles', 'url' => '/admin/users/roles'],
            'permissions' => ['title' => 'Permissions', 'url' => '/admin/users/permissions']
        ]
    ],
    'modules' => [
        'title' => 'Modules',
        'icon' => 'fas fa-cube',
        'url' => '/admin/modules',
        'submenu' => [
            'installed' => ['title' => 'Installed Modules', 'url' => '/admin/modules'],
            'install' => ['title' => 'Install Module', 'url' => '/admin/modules/install'],
            'configure' => ['title' => 'Configure Module', 'url' => '/admin/modules/configure'],
            'marketplace' => ['title' => 'Module Marketplace', 'url' => '/admin/modules/marketplace']
        ]
    ],
    'settings' => [
        'title' => 'Settings',
        'icon' => 'fas fa-cog',
        'url' => '/admin/settings',
        'submenu' => [
            'general' => ['title' => 'General Settings', 'url' => '/admin/settings'],
            'backup' => ['title' => 'Backup Settings', 'url' => '/admin/settings/backup'],
            'advanced' => ['title' => 'Advanced Settings', 'url' => '/admin/settings/advanced'],
            'security' => ['title' => 'Security Settings', 'url' => '/admin/settings/security'],
            'email' => ['title' => 'Email Settings', 'url' => '/admin/settings/email']
        ]
    ],
    'system' => [
        'title' => 'System',
        'icon' => 'fas fa-server',
        'url' => '/admin/system',
        'submenu' => [
            'status' => ['title' => 'System Status', 'url' => '/admin/system/status'],
            'logs' => ['title' => 'System Logs', 'url' => '/admin/system/logs'],
            'cache' => ['title' => 'Cache Management', 'url' => '/admin/system/cache'],
            'maintenance' => ['title' => 'Maintenance Mode', 'url' => '/admin/system/maintenance']
        ]
    ],
    'themes' => [
        'title' => 'Themes',
        'icon' => 'fas fa-palette',
        'url' => '/admin/themes',
        'submenu' => [
            'manage' => ['title' => 'Manage Themes', 'url' => '/admin/themes'],
            'customize' => ['title' => 'Customize Theme', 'url' => '/admin/themes/customize'],
            'builder' => ['title' => 'Theme Builder', 'url' => '/admin/themes/builder']
        ]
    ],
    'widgets' => [
        'title' => 'Widgets',
        'icon' => 'fas fa-th-large',
        'url' => '/admin/widgets',
        'submenu' => [
            'manage' => ['title' => 'Manage Widgets', 'url' => '/admin/widgets'],
            'create' => ['title' => 'Create Widget', 'url' => '/admin/widgets/create'],
            'marketplace' => ['title' => 'Widget Marketplace', 'url' => '/admin/widgets/marketplace']
        ]
    ],
    'menu' => [
        'title' => 'Menu Management',
        'icon' => 'fas fa-bars',
        'url' => '/admin/menu',
        'submenu' => [
            'manage' => ['title' => 'Manage Menus', 'url' => '/admin/menu'],
            'customize' => ['title' => 'Menu Customization', 'url' => '/admin/menu/customize'],
            'builder' => ['title' => 'Menu Builder', 'url' => '/admin/menu/builder']
        ]
    ],
    'analytics' => [
        'title' => 'Analytics',
        'icon' => 'fas fa-chart-line',
        'url' => '/admin/analytics',
        'submenu' => [
            'dashboard' => ['title' => 'Analytics Dashboard', 'url' => '/admin/analytics'],
            'reports' => ['title' => 'Reports', 'url' => '/admin/reports'],
            'generate' => ['title' => 'Generate Report', 'url' => '/admin/reports/generate']
        ]
    ],
    'backup' => [
        'title' => 'Backup & Restore',
        'icon' => 'fas fa-database',
        'url' => '/admin/backup',
        'submenu' => [
            'manage' => ['title' => 'Backup Management', 'url' => '/admin/backup'],
            'create' => ['title' => 'Create Backup', 'url' => '/admin/backup/create'],
            'restore' => ['title' => 'Restore Backup', 'url' => '/admin/backup/restore']
        ]
    ]
];

// Function to check if menu item is active
function isMenuItemActive($url, $currentPath) {
    return strpos($currentPath, $url) === 0;
}

// Function to check if submenu item is active
function isSubmenuItemActive($url, $currentPath) {
    return $currentPath === $url || strpos($currentPath, $url) === 0;
}
?>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="/themes/admin/assets/images/admin-logo.png" alt="Admin Logo" class="sidebar-logo-img">
            <span class="sidebar-logo-text">Admin Panel</span>
        </div>
        <button class="sidebar-toggle" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <?php foreach ($menuItems as $key => $item): ?>
                <?php 
                $isActive = isMenuItemActive($item['url'], $currentPath);
                $hasSubmenu = !empty($item['submenu']);
                ?>
                
                <li class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
                    <?php if ($hasSubmenu): ?>
                        <a href="#" class="nav-link has-submenu" data-toggle="submenu">
                            <i class="<?php echo htmlspecialchars($item['icon']); ?> nav-icon"></i>
                            <span class="nav-text"><?php echo htmlspecialchars($item['title']); ?></span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </a>
                        
                        <ul class="nav-submenu">
                            <?php foreach ($item['submenu'] as $subKey => $subItem): ?>
                                <?php 
                                $isSubActive = isSubmenuItemActive($subItem['url'], $currentPath);
                                ?>
                                <li class="nav-subitem <?php echo $isSubActive ? 'active' : ''; ?>">
                                    <a href="<?php echo htmlspecialchars($subItem['url']); ?>" class="nav-sublink">
                                        <span class="nav-subtext"><?php echo htmlspecialchars($subItem['title']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($item['url']); ?>" class="nav-link">
                            <i class="<?php echo htmlspecialchars($item['icon']); ?> nav-icon"></i>
                            <span class="nav-text"><?php echo htmlspecialchars($item['title']); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">
                <img src="<?php echo htmlspecialchars($currentUser['avatar'] ?? '/uploads/avatars/default.png'); ?>" 
                     alt="User Avatar" class="user-avatar-img">
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin User'); ?></div>
                <div class="user-role"><?php echo htmlspecialchars(ucfirst($userRole)); ?></div>
            </div>
        </div>
        
        <div class="sidebar-actions">
            <a href="/admin/profile" class="sidebar-action-btn" title="Profile">
                <i class="fas fa-user"></i>
            </a>
            <a href="/admin/settings" class="sidebar-action-btn" title="Settings">
                <i class="fas fa-cog"></i>
            </a>
            <a href="/logout" class="sidebar-action-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Mobile Sidebar Toggle -->
<button class="mobile-sidebar-toggle" id="mobile-sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>