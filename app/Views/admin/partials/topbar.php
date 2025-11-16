<?php
$currentUser = $_SESSION['user'] ?? [
    'full_name' => 'Admin',
    'email' => 'admin@engicalc.com'
];
?>

<nav class="admin-topbar">
    
    <!-- Mobile Sidebar Toggle -->
    <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Search -->
    <div class="topbar-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search settings, users, modules..." id="adminSearch">
    </div>
    
    <!-- Topbar Actions -->
    <div class="topbar-actions">
        
        <!-- View Site -->
        <a href="<?php echo app_base_url('/'); ?>" target="_blank" class="topbar-btn" title="View Site">
            <i class="fas fa-external-link-alt"></i>
        </a>
        
        <!-- Notifications -->
        <div class="topbar-dropdown">
            <button class="topbar-btn" id="notificationToggle">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
            <div class="dropdown-menu notification-dropdown" id="notificationDropdown">
                <div class="dropdown-header">
                    <h6>Notifications</h6>
                    <a href="<?php echo app_base_url('/admin/notifications'); ?>">View All</a>
                </div>
                <div class="dropdown-body">
                    <div class="notification-item">
                        <i class="fas fa-user-plus text-success"></i>
                        <div class="notification-content">
                            <div class="notification-title">New user registered</div>
                            <div class="notification-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <div class="notification-content">
                            <div class="notification-title">High memory usage detected</div>
                            <div class="notification-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-database text-info"></i>
                        <div class="notification-content">
                            <div class="notification-title">Database backup completed</div>
                            <div class="notification-time">1 day ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Dropdown -->
        <div class="topbar-dropdown">
            <button class="topbar-profile" id="profileToggle">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <span class="profile-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin'); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu profile-dropdown" id="profileDropdown">
                <div class="dropdown-header">
                    <div class="profile-info">
                        <div class="profile-info-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin'); ?></div>
                        <div class="profile-info-email"><?php echo htmlspecialchars($currentUser['email'] ?? 'admin@engicalc.com'); ?></div>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo app_base_url('/user/profile'); ?>" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        My Profile
                    </a>
                    <a href="<?php echo app_base_url('/admin/settings'); ?>" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        Admin Settings
                    </a>
                    <a href="<?php echo app_base_url('/user/settings'); ?>" class="dropdown-item">
                        <i class="fas fa-sliders-h"></i>
                        Account Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo app_base_url('/logout'); ?>" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        
    </div>
    
</nav>
