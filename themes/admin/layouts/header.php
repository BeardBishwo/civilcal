<?php
/**
 * Admin Header Layout Component
 * Reusable header for admin panel
 */

// Get current user and system info
$currentUser = $_SESSION['user'] ?? null;
$systemInfo = [
    'version' => '1.0.0',
    'environment' => $_ENV['APP_ENV'] ?? 'production',
    'notifications' => getAdminNotifications($currentUser['id'] ?? 0),
    'pending_tasks' => getPendingAdminTasks()
];

// Helper functions (would normally be in a separate file)
function getAdminNotifications($userId) {
    // Mock notifications - in real app, this would query database
    return [
        ['id' => 1, 'type' => 'info', 'message' => 'System update available', 'time' => '2 hours ago'],
        ['id' => 2, 'type' => 'warning', 'message' => 'High memory usage detected', 'time' => '5 hours ago'],
        ['id' => 3, 'type' => 'success', 'message' => 'Backup completed successfully', 'time' => '1 day ago']
    ];
}

function getPendingAdminTasks() {
    // Mock pending tasks - in real app, this would query database
    return [
        'user_approvals' => 3,
        'module_updates' => 2,
        'security_issues' => 0,
        'system_alerts' => 1
    ];
}

function formatNotificationTime($time) {
    return htmlspecialchars($time);
}

function getNotificationIcon($type) {
    $icons = [
        'info' => 'fas fa-info-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-circle'
    ];
    return $icons[$type] ?? $icons['info'];
}
?>

<!-- Admin Header -->
<header class="admin-header">
    <div class="admin-header-content">
        <!-- Left Section -->
        <div class="admin-header-left">
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Breadcrumb -->
            <nav class="admin-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/admin">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <?php
                    // Generate breadcrumb based on current path
                    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $pathParts = explode('/', trim($currentPath, '/'));
                    
                    // Remove 'admin' from path parts if present
                    if ($pathParts[0] === 'admin') {
                        array_shift($pathParts);
                    }
                    
                    $breadcrumbPath = '/admin';
                    foreach ($pathParts as $index => $part) {
                        if (empty($part)) continue;
                        
                        $breadcrumbPath .= '/' . $part;
                        $isLast = $index === count($pathParts) - 1;
                        
                        echo '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '">';
                        
                        if (!$isLast) {
                            echo '<a href="' . htmlspecialchars($breadcrumbPath) . '">';
                        }
                        
                        echo '<span>' . htmlspecialchars(ucfirst(str_replace('-', ' ', $part))) . '</span>';
                        
                        if (!$isLast) {
                            echo '</a>';
                        }
                        
                        echo '</li>';
                    }
                    ?>
                </ol>
            </nav>
        </div>

        <!-- Center Section - Search -->
        <div class="admin-header-center">
            <div class="admin-header-search">
                <div class="search-container">
                    <input type="text" 
                           class="search-input" 
                           placeholder="Search users, modules, settings..." 
                           id="admin-search">
                    <button class="search-btn" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Search Results Dropdown -->
                <div class="search-results" id="search-results" style="display: none;">
                    <div class="search-results-header">
                        <span>Search Results</span>
                        <button class="search-close" id="search-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-results-content" id="search-results-content">
                        <!-- Results will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="admin-header-right">
            <!-- Quick Actions -->
            <div class="admin-header-actions">
                <!-- System Status -->
                <div class="header-action-item" id="system-status-indicator">
                    <button class="action-btn" title="System Status">
                        <i class="fas fa-server"></i>
                        <span class="status-indicator online"></span>
                    </button>
                </div>

                <!-- Notifications -->
                <div class="header-action-item" id="notifications-dropdown">
                    <button class="action-btn" title="Notifications" id="notifications-btn">
                        <i class="fas fa-bell"></i>
                        <?php if (!empty($systemInfo['notifications'])): ?>
                            <span class="notification-badge">
                                <?php echo count($systemInfo['notifications']); ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div class="notifications-dropdown" id="notifications-menu">
                        <div class="notifications-header">
                            <h3>Notifications</h3>
                            <a href="/admin/notifications" class="view-all-link">View All</a>
                        </div>
                        
                        <div class="notifications-list">
                            <?php if (!empty($systemInfo['notifications'])): ?>
                                <?php foreach ($systemInfo['notifications'] as $notification): ?>
                                    <div class="notification-item <?php echo htmlspecialchars($notification['type']); ?>">
                                        <div class="notification-icon">
                                            <i class="<?php echo getNotificationIcon($notification['type']); ?>"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-message">
                                                <?php echo htmlspecialchars($notification['message']); ?>
                                            </div>
                                            <div class="notification-time">
                                                <?php echo formatNotificationTime($notification['time']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-notifications">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No new notifications</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notifications-footer">
                            <a href="/admin/notifications/mark-all-read" class="mark-all-read-btn">
                                Mark all as read
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="header-action-item" id="user-dropdown">
                    <button class="user-menu-btn" id="user-menu-btn">
                        <div class="user-avatar-small">
                            <img src="<?php echo htmlspecialchars($currentUser['avatar'] ?? '/uploads/avatars/default.png'); ?>" 
                                 alt="User Avatar" class="user-avatar-img">
                        </div>
                        <div class="user-info-small">
                            <div class="user-name-small"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin'); ?></div>
                            <div class="user-role-small"><?php echo htmlspecialchars(ucfirst($currentUser['role'] ?? 'user')); ?></div>
                        </div>
                        <i class="fas fa-chevron-down user-menu-arrow"></i>
                    </button>
                    
                    <!-- User Dropdown Menu -->
                    <div class="user-dropdown-menu" id="user-menu">
                        <div class="user-dropdown-header">
                            <div class="user-avatar-large">
                                <img src="<?php echo htmlspecialchars($currentUser['avatar'] ?? '/uploads/avatars/default.png'); ?>" 
                                     alt="User Avatar" class="user-avatar-img">
                            </div>
                            <div class="user-details">
                                <div class="user-name-large"><?php echo htmlspecialchars($currentUser['name'] ?? 'Admin User'); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($currentUser['email'] ?? 'admin@example.com'); ?></div>
                                <div class="user-role-large"><?php echo htmlspecialchars(ucfirst($currentUser['role'] ?? 'user')); ?></div>
                            </div>
                        </div>
                        
                        <div class="user-dropdown-menu-items">
                            <a href="/admin/profile" class="menu-item">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                            <a href="/admin/settings" class="menu-item">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                            <a href="/admin/activity" class="menu-item">
                                <i class="fas fa-history"></i>
                                <span>Activity Log</span>
                            </a>
                            <div class="menu-divider"></div>
                            <a href="/help" class="menu-item">
                                <i class="fas fa-question-circle"></i>
                                <span>Help & Support</span>
                            </a>
                            <a href="/documentation" class="menu-item">
                                <i class="fas fa-book"></i>
                                <span>Documentation</span>
                            </a>
                            <div class="menu-divider"></div>
                            <a href="/logout" class="menu-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info Bar -->
    <div class="system-info-bar">
        <div class="system-info-left">
            <span class="system-version">v<?php echo htmlspecialchars($systemInfo['version']); ?></span>
            <span class="system-env <?php echo htmlspecialchars($systemInfo['environment']); ?>">
                <?php echo htmlspecialchars(strtoupper($systemInfo['environment'])); ?>
            </span>
        </div>
        
        <div class="system-info-right">
            <div class="system-metric">
                <i class="fas fa-users"></i>
                <span><?php echo htmlspecialchars($systemInfo['pending_tasks']['user_approvals'] ?? 0); ?> Pending</span>
            </div>
            <div class="system-metric">
                <i class="fas fa-cube"></i>
                <span><?php echo htmlspecialchars($systemInfo['pending_tasks']['module_updates'] ?? 0); ?> Updates</span>
            </div>
            <div class="system-metric">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($systemInfo['pending_tasks']['security_issues'] ?? 0); ?> Issues</span>
            </div>
        </div>
    </div>
</header>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <span>Loading...</span>
    </div>
</div>

<!-- Notification Toast -->
<div class="notification-toast" id="notification-toast"></div>