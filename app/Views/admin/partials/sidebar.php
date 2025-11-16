<?php
$currentUser = $_SESSION['user'] ?? [
    'full_name' => 'Admin',
    'role' => 'admin'
];
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
?>

<aside class="admin-sidebar" id="adminSidebar">
    
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-calculator-alt"></i>
            <span class="sidebar-logo-text">EC Admin</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Sidebar Menu -->
    <nav class="sidebar-menu">
        <ul>
            <li class="<?php echo strpos($currentPath, '/admin/dashboard') !== false || $currentPath === '/admin' ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/dashboard'); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/users') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/users'); ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/calculations') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/calculations'); ?>">
                    <i class="fas fa-calculator"></i>
                    <span>Calculations</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/modules') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/modules'); ?>">
                    <i class="fas fa-cubes"></i>
                    <span>Modules</span>
                </a>
            </li>
            
            <!-- Settings Section -->
            <li class="menu-divider">
                <span>Configuration</span>
            </li>
            
            <li class="has-submenu <?php echo strpos($currentPath, '/admin/settings') !== false ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?php echo app_base_url('/admin/settings/general'); ?>"><i class="fas fa-circle"></i> General</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/application'); ?>"><i class="fas fa-circle"></i> Application</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/users'); ?>"><i class="fas fa-circle"></i> Users</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/security'); ?>"><i class="fas fa-circle"></i> Security</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/email'); ?>"><i class="fas fa-circle"></i> Email</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/api'); ?>"><i class="fas fa-circle"></i> API</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/performance'); ?>"><i class="fas fa-circle"></i> Performance</a></li>
                    <li><a href="<?php echo app_base_url('/admin/settings/advanced'); ?>"><i class="fas fa-circle"></i> Advanced</a></li>
                </ul>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/themes') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/themes'); ?>">
                    <i class="fas fa-palette"></i>
                    <span>Themes</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/plugins') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/plugins'); ?>">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Plugins</span>
                </a>
            </li>
            
            <!-- System Section -->
            <li class="menu-divider">
                <span>System</span>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/logs') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/logs'); ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Logs</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/backup') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/backup'); ?>">
                    <i class="fas fa-database"></i>
                    <span>Backup</span>
                </a>
            </li>
            
            <li class="<?php echo strpos($currentPath, '/admin/system-status') !== false ? 'active' : ''; ?>">
                <a href="<?php echo app_base_url('/admin/system-status'); ?>">
                    <i class="fas fa-heartbeat"></i>
                    <span>System Status</span>
                </a>
            </li>
            
            <!-- Help -->
            <li class="menu-divider">
                <span>Support</span>
            </li>
            
            <li>
                <a href="<?php echo app_base_url('/help'); ?>" target="_blank">
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
