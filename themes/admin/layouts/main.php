<!DOCTYPE html>
<?php
$site_meta = get_site_meta();
$logo = $site_meta['logo'] ?? '';
$favicon = $site_meta['favicon'] ?? '';
$site_name = $site_meta['title'] ?? 'Admin Panel';
?>
<html lang="en" class="admin-theme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - <?php echo htmlspecialchars($site_name); ?></title>
    <?php if (!empty($favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <?php endif; ?>

    <!-- Admin Styles -->
    <link rel="stylesheet" href="<?php echo \App\Helpers\Asset::url('themes/admin/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Helpers\Asset::url('public/assets/css/global-notifications.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo function_exists('csrf_token') ? csrf_token() : ($_SESSION['csrf_token'] ?? ''); ?>">
    <script>
        window.appConfig = {
            baseUrl: '<?php echo app_base_url(); ?>',
            apiBase: '<?php echo app_base_url('api'); ?>',
            csrfToken: '<?php echo function_exists('csrf_token') ? csrf_token() : ($_SESSION['csrf_token'] ?? ''); ?>'
        };

        (function() {
            var origFetch = window.fetch;
            window.fetch = function(input, init) {
                init = init || {};
                var method = String(init.method || 'GET').toUpperCase();
                if (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
                    // Prefer token from hidden input field (most up-to-date), fallback to appConfig or meta tag
                    var inputToken = document.querySelector('input[name="csrf_token"]')?.value;
                    var configToken = window.appConfig.csrfToken;
                    var metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    var token = inputToken || configToken || metaToken || '';

                    var headers = init.headers || {};
                    if (typeof Headers !== 'undefined' && headers instanceof Headers) {
                        headers.set('X-CSRF-Token', token);
                    } else if (Array.isArray(headers)) {
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

    <!-- Bootstrap 5 JS Bundle (Required for Modals, Dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

        /* Dark Theme Variables */
        :root.dark-theme {
            --admin-primary: #6366f1;
            --admin-primary-dark: #4f46e5;
            --admin-secondary: #10b981;
            --admin-danger: #f87171;
            --admin-warning: #fbbf24;
            --admin-info: #60a5fa;
            --admin-success: #34d399;
            --admin-dark: #f8fafc;
            --admin-light: #1f2937;
            --admin-border: #374151;
            --admin-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            --admin-gray-50: #111827;
            --admin-gray-100: #1f2937;
            --admin-gray-200: #374151;
            --admin-gray-300: #4b5563;
            --admin-gray-600: #9ca3af;
            --admin-gray-700: #d1d5db;
            --admin-gray-800: #f3f4f6;
            --admin-gray-900: #f9fafb;
            --admin-white: #0f172a;
        }

        /* Light Theme Variables (default) */
        :root.light-theme {
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
            --admin-gray-50: #f9fafb;
            --admin-gray-100: #f3f4f6;
            --admin-gray-200: #e5e7eb;
            --admin-gray-300: #d1d5db;
            --admin-gray-600: #4b5563;
            --admin-gray-700: #374151;
            --admin-gray-800: #1f2937;
            --admin-gray-900: #111827;
            --admin-white: #ffffff;
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 20px;
            width: 350px;
            background: white;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            box-shadow: var(--admin-shadow);
            z-index: 1000;
            display: none;
            overflow: hidden;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--admin-dark);
        }

        .notification-header .view-all {
            color: var(--admin-primary);
            font-size: 14px;
            text-decoration: none;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .notification-item {
            padding: 12px 16px;
            display: flex;
            gap: 12px;
            border-bottom: 1px solid var(--admin-border);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: var(--admin-gray-50);
        }

        .notification-item.unread {
            background-color: var(--admin-gray-50);
            border-left: 3px solid var(--admin-primary);
        }

        .notification-icon {
            font-size: 18px;
            padding-top: 2px;
        }

        .notification-content {
            flex: 1;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .notification-title {
            margin: 0;
            font-weight: 600;
            color: var(--admin-gray-800);
            font-size: 14px;
        }

        .notification-time {
            font-size: 12px;
            color: var(--admin-gray-500);
        }

        .notification-message {
            color: var(--admin-gray-700);
            font-size: 13px;
            line-height: 1.4;
        }

        .notification-badge {
            background-color: var(--admin-danger);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: 600;
            min-width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            position: absolute;
            top: -8px;
            right: -8px;
        }

        .notification-footer {
            padding: 12px 16px;
            border-top: 1px solid var(--admin-border);
            text-align: center;
        }

        .notification-footer button {
            padding: 6px 12px;
            font-size: 13px;
        }

        .loading {
            padding: 12px 16px;
            text-align: center;
            color: var(--admin-gray-500);
            font-size: 13px;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: var(--admin-gray-500);
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: 380px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .notification-dropdown.show {
            display: block !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        .notification-header {
            padding: 16px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--admin-gray-800);
        }

        .notification-header .view-all {
            color: var(--admin-primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .notification-header .view-all:hover {
            text-decoration: underline;
        }

        /* Quick actions needs position relative for dropdown positioning */
        .quick-actions {
            position: relative;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn.btn-icon {
            position: relative;
        }

        /* Enhanced Notification Toast Styles */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 500px;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease-in-out;
            font-family: 'Inter', sans-serif;
        }

        /* Ensure notification button is always visible and interactive */
        #notificationToggle {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            cursor: pointer !important;
            position: relative;
            z-index: 1001;
            padding: 8px 12px !important;
            background: var(--admin-primary) !important;
            color: white !important;
            border-radius: 8px !important;
            border: none !important;
            transition: all 0.2s ease !important;
        }

        /* Theme Toggle Button Styles */
        #themeToggle {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            cursor: pointer !important;
            position: relative;
            z-index: 1001;
            padding: 8px 12px !important;
            background: var(--admin-gray-800) !important;
            color: white !important;
            border-radius: 8px !important;
            border: none !important;
            transition: all 0.2s ease !important;
            margin-left: 8px !important;
            min-width: 40px !important;
            height: 40px !important;
        }

        /* Ensure theme toggle button is always visible */
        .theme-toggle-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
        }

        /* Fallback for theme toggle button */
        #themeToggle.fallback-visible {
            background: #4f46e5 !important;
            color: white !important;
            border: 2px solid #3730a3 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        #themeToggle:hover {
            background: var(--admin-gray-900) !important;
            transform: translateY(-1px) !important;
        }

        #themeToggle:active {
            transform: translateY(0) !important;
        }

        #notificationToggle:hover {
            background: var(--admin-primary-dark) !important;
            transform: translateY(-1px) !important;
        }

        #notificationToggle:active {
            transform: translateY(0) !important;
        }

        /* Ensure notification dropdown has proper z-index */
        #notificationDropdown {
            z-index: 10000 !important;
        }

        /* Fix for notification items */
        .notification-item {
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .notification-item:hover {
            background-color: var(--admin-gray-50) !important;
            transform: translateX(2px) !important;
        }

        .notification-toast.show {
            transform: translateX(0);
        }

        .notification-toast .toast-content {
            display: flex;
            align-items: center;
            padding: 16px;
            background: white;
            border-radius: 8px;
        }

        .notification-toast .toast-icon {
            font-size: 20px;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .notification-toast .toast-message {
            flex: 1;
            font-size: 14px;
            line-height: 1.4;
            color: var(--admin-gray-800);
        }

        .notification-toast .toast-close {
            background: none;
            border: none;
            color: var(--admin-gray-400);
            cursor: pointer;
            padding: 4px;
            margin-left: 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .notification-toast .toast-close:hover {
            background: var(--admin-gray-100);
            color: var(--admin-gray-600);
        }

        /* Toast type variations */
        .notification-toast.notification-success {
            border-left: 4px solid var(--admin-success);
        }

        .notification-toast.notification-success .toast-icon {
            color: var(--admin-success);
        }

        .notification-toast.notification-error {
            border-left: 4px solid var(--admin-danger);
        }

        .notification-toast.notification-error .toast-icon {
            color: var(--admin-danger);
        }

        .notification-toast.notification-warning {
            border-left: 4px solid var(--admin-warning);
        }

        .notification-toast.notification-warning .toast-icon {
            color: var(--admin-warning);
        }

        .notification-toast.notification-info {
            border-left: 4px solid var(--admin-info);
        }

        .notification-toast.notification-info .toast-icon {
            color: var(--admin-info);
        }

        .notification-toast.notification-system {
            border-left: 4px solid var(--admin-primary);
        }

        .notification-toast.notification-system .toast-icon {
            color: var(--admin-primary);
        }

        /* Loading and error states */
        .notification-list .loading {
            text-align: center;
            padding: 20px;
            color: var(--admin-gray-500);
            font-size: 14px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .notification-list .error {
            text-align: center;
            padding: 20px;
            color: var(--admin-danger);
            animation: shake 0.5s ease;
        }

        .notification-list .error button {
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        /* Smooth animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Apply animations to notification dropdown */
        .notification-dropdown {
            animation: slideDown 0.3s ease-out !important;
            transition: all 0.3s ease !important;
        }

        /* Apply animations to notification items */
        .notification-item {
            animation: fadeIn 0.3s ease-out !important;
            transition: all 0.3s ease !important;
        }

        /* Apply animations to toast notifications */
        .notification-toast {
            animation: slideIn 0.3s ease-out !important;
            transition: all 0.3s ease !important;
        }

        /* Smooth hover effects */
        .notification-item:hover {
            transform: translateX(2px) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }

        /* Smooth theme toggle button */
        #themeToggle {
            transition: all 0.3s ease !important;
        }

        #themeToggle:hover {
            transform: scale(1.05) !important;
        }

        /* Smooth notification button */
        #notificationToggle:hover {
            transform: scale(1.05) !important;
        }

        /* Theme Feedback Toast */
        .theme-feedback-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--admin-primary);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: var(--admin-shadow-lg);
            z-index: 2000;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-out 1.5s forwards;
            transition: all 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }


        .premium-notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .premium-notification i {
            font-size: 20px;
        }

        .premium-notification.success {
            border-left: 4px solid #10b981;
        }

        .premium-notification.success i {
            color: #10b981;
        }

        .premium-notification.error {
            border-left: 4px solid #ef4444;
        }

        .premium-notification.error i {
            color: #ef4444;
        }

        .premium-notification.warning {
            border-left: 4px solid #f59e0b;
        }

        .premium-notification.warning i {
            color: #f59e0b;
        }

        .premium-notification.info {
            border-left: 4px solid #3b82f6;
        }

        .premium-notification.info i {
            color: #3b82f6;
        }

        .premium-notification span {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    <?php $currentUser = current_user(); ?>
</head>

<body class="admin-body">


    <!-- Admin Wrapper -->
    <div id="admin-wrapper" class="admin-wrapper">

        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar">
            <div class="sidebar-header" style="padding: 0; display: flex; align-items: stretch; height: 70px;">
                <div class="sidebar-logo" style="flex: 1; padding: 0; margin: 0; display: flex; justify-content: center; align-items: center; background: transparent;">
                    <a href="<?php echo app_base_url('admin'); ?>" style="text-decoration: none; color: inherit; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                        <?php if (!empty($logo)): ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="sidebar-logo-img" style="height: 100%; width: auto; max-width: 100%; object-fit: contain; padding: 5px;">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="sidebar-logo-icon fas fa-calculator"></i>
                                <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
                <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle sidebar" style="margin-right: 15px; height: 100%; display: flex; align-items: center; background: transparent; border: none; color: inherit; cursor: pointer;">
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
                        </a>
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
                            <li><a href="<?php echo app_base_url('admin/users/inactive'); ?>">Inactive</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/banned'); ?>">Banned</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/admins'); ?>">Admins</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/create'); ?>">Add New</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/roles'); ?>">Roles</a></li>
                            <li><a href="<?php echo app_base_url('admin/users/logs/logins'); ?>">Login Logs</a></li>
                            <li><a href="<?php echo app_base_url('admin/security/ip-restrictions'); ?>">IP Access Control</a></li>
                            <li><a href="<?php echo app_base_url('admin/security/alerts'); ?>">Security Alerts</a></li>
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
                            <li><a href="<?php echo app_base_url('admin/content/media'); ?>">Media</a></li>
                        </ul>
                    </li>

                    <!-- Sponsorships (B2B) -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/sponsors') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('/admin/sponsors'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-handshake"></i>
                            <span class="nav-text">Sponsorships</span>
                        </a>
                    </li>

                    <!-- Blog System -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/blog') !== false ? 'active' : ''; ?>">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="nav-icon fas fa-blog"></i>
                            <span class="nav-text">Blog System</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/blog/articles'); ?>">Articles</a></li>
                            <li><a href="<?php echo app_base_url('admin/blog/posts'); ?>">Collections</a></li>
                            <li><a href="<?php echo app_base_url('admin/blog/categories'); ?>">Categories</a></li>
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
                            <li><a href="<?php echo app_base_url('admin/settings/general'); ?>">General</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/email'); ?>">Email</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/security'); ?>">Security</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/google'); ?>">Google Login</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/recaptcha'); ?>">Recaptcha</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/payments'); ?>">Payment Gateway</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/economy'); ?>"><i class="fas fa-coins text-warning"></i> Economy</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/firebase'); ?>"><i class="fas fa-fire text-danger"></i> Firebase</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/quiz-modes'); ?>"><i class="fas fa-gamepad text-info"></i> Quiz Modes</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/advanced'); ?>">Advanced</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/permalinks'); ?>"><i class="fas fa-link"></i> Permalinks</a></li>
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

                    <!-- Quiz System -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/quiz') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/quiz'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <span class="nav-text">Quiz System</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('admin/quiz/dashboard'); ?>">Dashboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/syllabus'); ?>">Syllabus Tree</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/courses'); ?>">Courses</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/education-levels'); ?>">Education Levels</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/categories'); ?>">Main Categories</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/subcategories'); ?>">Sub-Categories</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/topics'); ?>">Topics</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/position-levels'); ?>">Position Levels</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/exams'); ?>">Exam Manager</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/daily'); ?>">Daily Quest Scheduler</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/blueprints'); ?>">Exam Blueprints</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/word-bank'); ?>"><i class="fas fa-book text-info"></i> Word Bank (Admin)</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/questions'); ?>">Question Bank</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/import'); ?>">Import Questions</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/analytics'); ?>">Results & Analytics</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/leaderboard'); ?>">Leaderboard</a></li>
                            <li><a href="<?php echo app_base_url('admin/contest'); ?>"><i class="fas fa-trophy text-warning"></i> Contest Engine</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/reports'); ?>"><i class="fas fa-flag text-danger"></i> Reported Questions</a></li>
                            <li><a href="<?php echo app_base_url('admin/quiz/settings'); ?>">Settings</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/economy'); ?>"><i class="fas fa-coins text-warning"></i> Economy Settings</a></li>
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

                    <!-- Marketplace -->
                    <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/marketplace') !== false ? 'active' : ''; ?>">
                        <a href="<?php echo app_base_url('admin/marketplace'); ?>" class="nav-link">
                            <i class="nav-icon fas fa-shopping-basket"></i>
                            <span class="nav-text">Marketplace</span>
                        </a>
                    </li>


                    <!-- Support & Resources -->
                    <li class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/help') !== false || strpos($_SERVER['REQUEST_URI'], '/developers') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/system-status') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/backup') !== false) ? 'active' : ''; ?>">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="nav-icon fas fa-life-ring"></i>
                            <span class="nav-text">Support & Resources</span>
                            <i class="nav-arrow fas fa-chevron-right"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li><a href="<?php echo app_base_url('help'); ?>"><i class="fas fa-question-circle"></i> Help Center</a></li>
                            <li><a href="<?php echo app_base_url('developers'); ?>"><i class="fas fa-code"></i> Developer API</a></li>
                            <li><a href="<?php echo app_base_url('admin/system-status'); ?>"><i class="fas fa-server"></i> System Status</a></li>
                            <li><a href="<?php echo app_base_url('admin/backup'); ?>"><i class="fas fa-database"></i> Backup & Restore</a></li>
                        </ul>
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

            <!-- Bottom Sidebar Toggle -->
            <button id="sidebar-toggle-bottom" class="sidebar-toggle-bottom" title="Toggle Sidebar">
                <i class="fas fa-angle-double-left"></i>
            </button>
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
                            <a href="<?php echo app_base_url('admin'); ?>"><?php echo htmlspecialchars($site_name); ?></a>
                        </span>
                        <?php if (isset($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $crumb): ?>
                                <span class="breadcrumb-divider">/</span>
                                <span class="breadcrumb-item">
                                    <?php if (!empty($crumb['url'])): ?>
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
                        <button class="btn btn-icon" title="Global Search" onclick="toggleSearch()">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-icon" title="System Health" onclick="window.location.href='<?php echo app_base_url('admin/system-status'); ?>'">
                            <i class="fas fa-heartbeat"></i>
                        </button>
                        <button class="btn btn-icon" title="Backup" onclick="window.location.href='<?php echo app_base_url('admin/backup'); ?>'">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-icon" title="Notifications" id="notificationToggle">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notificationBadge">0</span>
                        </button>
                        <!-- Theme Toggle Button -->
                        <button class="btn btn-icon theme-toggle-btn" title="Toggle Theme" id="themeToggle">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </div>

                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="notification-dropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <a href="<?php echo app_base_url('admin/notifications'); ?>" class="view-all">View All</a>
                        </div>
                        <div class="notification-list">
                            <div class="loading">Loading notifications...</div>
                        </div>
                        <div class="notification-footer">
                            <button id="markAllRead" class="btn btn-sm btn-outline-primary">Mark All as Read</button>
                        </div>
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
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

    <!-- Media Modal Partial -->
    <?php include BASE_PATH . '/themes/admin/views/partials/media_modal.php'; ?>

    <script>
        // Initialize TinyMCE globally
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '.rich-editor',
                height: 300,
                menubar: false,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media-library | code',

                // Custom Button for Media Library
                setup: function(editor) {
                    editor.ui.registry.addButton('media-library', {
                        text: 'Select Image',
                        icon: 'image',
                        onAction: function() {
                            // Open Media Modal
                            MediaModal.open(function(url) {
                                editor.insertContent(`<img src="${url}" style="max-width:100%; height:auto;" />`);
                            });
                        }
                    });
                },

                // Disable default file picker in favor of custom button (optional, but cleaner)
                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                content_style: 'body { font-family:Inter,sans-serif; font-size:14px }'
            });
        });
    </script>

    <script src="<?php echo app_base_url('themes/admin/assets/js/admin.js?v=' . time()); ?>"></script>
    <script src="<?php echo app_base_url('public/assets/js/global-notifications.js'); ?>"></script>
    <script src="<?php echo app_base_url('themes/admin/assets/js/notification-fixed.js'); ?>"></script>
    <script src="<?php echo app_base_url('themes/admin/assets/js/theme-toggle.js'); ?>"></script>

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

    <script>
        // Fallback notification click handler
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                const btn = document.getElementById("notificationToggle");
                const dropdown = document.getElementById("notificationDropdown");

                if (btn && !btn.onclick) {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();
                        if (dropdown) {
                            const isVisible = dropdown.classList.contains("show");
                            dropdown.classList.toggle("show");
                            console.log("Dropdown toggled: " + (dropdown.classList.contains("show") ? "open" : "closed"));
                        }
                    });
                    console.log("✅ Fallback click handler attached");
                }
            }, 500);
        });
    </script>



    <!-- Global Notifications System -->
    <script src="<?php echo app_base_url('public/assets/js/global-notifications.js'); ?>"></script>

    <!-- Theme Toggle Fallback Initialization -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                const themeBtn = document.getElementById("themeToggle");
                const themeIcon = document.getElementById("themeIcon");

                if (themeBtn) {
                    // Ensure button is visible
                    themeBtn.classList.add("fallback-visible");
                    themeBtn.style.display = "inline-block";
                    themeBtn.style.visibility = "visible";
                    themeBtn.style.opacity = "1";

                    // Add basic click handler if no JS loaded
                    if (!themeBtn.onclick) {
                        themeBtn.addEventListener("click", function(e) {
                            e.preventDefault();
                            const currentIcon = themeIcon.className;
                            if (currentIcon.includes("fa-moon")) {
                                themeIcon.className = "fas fa-sun";
                                document.documentElement.classList.add("dark-theme");
                                document.documentElement.classList.remove("light-theme");
                            } else {
                                themeIcon.className = "fas fa-moon";
                                document.documentElement.classList.add("light-theme");
                                document.documentElement.classList.remove("dark-theme");
                            }
                            console.log("✅ Fallback theme toggle handler attached");
                        });
                    }
                    console.log("✅ Theme toggle button initialized");
                } else {
                    console.error("❌ Theme toggle button not found");
                }
            }, 1000);
        });
    </script>
    <!-- Global Search Overlay -->
    <div id="search-overlay" class="search-overlay">
        <div class="search-container">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="global-search-input" placeholder="Type to search..." autocomplete="off">
                <i class="fas fa-spinner fa-spin search-spinner" style="display:none;"></i>
            </div>
            <div id="search-results" class="search-results"></div>
            <div class="search-footer">
                Press ESC to close
            </div>
        </div>
        <button class="close-search" onclick="toggleSearch()"><i class="fas fa-times"></i></button>
    </div>

    <style>
        /* Search Overlay Styles */
        .search-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            z-index: 99999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            align-items: flex-start;
            justify-content: center;
            padding-top: 100px;
        }

        .search-overlay.open {
            display: flex;
            opacity: 1;
        }

        .search-container {
            width: 100%;
            max-width: 600px;
            background: transparent;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .search-overlay.open .search-container {
            transform: translateY(0);
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
            background: var(--admin-white);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 20px;
            font-size: 1.2rem;
            color: var(--admin-gray-400);
        }

        .search-input-wrapper .search-spinner {
            position: absolute;
            right: 20px;
            font-size: 1.2rem;
            color: var(--admin-primary);
        }

        #global-search-input {
            width: 100%;
            padding: 1.5rem 1.5rem 1.5rem 3.5rem;
            font-size: 1.2rem;
            border: none;
            outline: none;
            background: transparent;
            color: var(--admin-gray-800);
        }

        .search-results {
            background: var(--admin-white);
            border-radius: 12px;
            overflow: hidden;
            max-height: 50vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--admin-gray-200);
            cursor: pointer;
            text-decoration: none;
            color: var(--admin-gray-800);
            transition: background 0.2s;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: var(--admin-gray-50);
        }

        .search-result-icon {
            width: 32px;
            height: 32px;
            background: var(--admin-gray-100);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--admin-gray-600);
        }

        .search-result-info {
            display: flex;
            flex-direction: column;
        }

        .search-result-title {
            font-weight: 500;
            font-size: 1rem;
        }

        .search-result-type {
            font-size: 0.75rem;
            color: var(--admin-gray-500);
            text-transform: uppercase;
            font-weight: 600;
        }

        .close-search {
            position: absolute;
            top: 30px;
            right: 30px;
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--admin-gray-600);
            transition: transform 0.2s;
        }

        .close-search:hover {
            transform: rotate(90deg);
            color: var(--admin-danger);
        }

        .search-footer {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
    </style>

    <script>
        function toggleSearch() {
            const overlay = document.getElementById('search-overlay');
            const input = document.getElementById('global-search-input');

            if (overlay.classList.contains('open')) {
                overlay.classList.remove('open');
                setTimeout(() => overlay.style.display = 'none', 300);
                document.body.style.overflow = '';
            } else {
                overlay.style.display = 'flex';
                // Trigger reflow
                overlay.offsetHeight;
                overlay.classList.add('open');
                setTimeout(() => input.focus(), 100);
                document.body.style.overflow = 'hidden';
            }
        }

        // Search Logic
        const searchInput = document.getElementById('global-search-input');
        const resultsContainer = document.getElementById('search-results');
        const searchSpinner = document.querySelector('.search-spinner');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    resultsContainer.style.display = 'none';
                    resultsContainer.innerHTML = '';
                    return;
                }

                searchSpinner.style.display = 'block';

                searchTimeout = setTimeout(() => {
                    fetch('<?php echo app_base_url('/admin/api/search?q='); ?>' + encodeURIComponent(query))
                        .then(r => r.json())
                        .then(data => {
                            searchSpinner.style.display = 'none';
                            resultsContainer.innerHTML = '';

                            if (data.length > 0) {
                                resultsContainer.style.display = 'block';
                                data.forEach(item => {
                                    resultsContainer.innerHTML += `
                                        <a href="${item.url}" class="search-result-item">
                                            <div class="search-result-icon"><i class="fas ${item.icon}"></i></div>
                                            <div class="search-result-info">
                                                <span class="search-result-title">${item.title}</span>
                                                <span class="search-result-type">${item.type}</span>
                                            </div>
                                        </a>
                                    `;
                                });
                            } else {
                                resultsContainer.style.display = 'block';
                                resultsContainer.innerHTML = '<div style="padding:1.5rem; text-align:center; color:var(--admin-gray-500);">No results found</div>';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            searchSpinner.style.display = 'none';
                        });
                }, 300);
            });
        }

        document.addEventListener('keydown', (e) => {
            const overlay = document.getElementById('search-overlay');
            if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) {
                toggleSearch();
            }
            // CMD+K or CTRL+K
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                toggleSearch();
            }
        });
    </script>
    <script>
        // Sidebar Auto-Scroll to Active Item
        document.addEventListener('DOMContentLoaded', function() {
            const activeItem = document.querySelector('.nav-item.active');
            if (activeItem) {
                setTimeout(() => {
                    activeItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                }, 300);
            }
        });
    </script>
    <!-- Firebase Realtime Engine -->
    <?php include BASE_PATH . '/themes/default/views/partials/firebase_core.php'; ?>
</body>

</html>