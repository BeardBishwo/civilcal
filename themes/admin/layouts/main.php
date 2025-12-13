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
    <link rel="stylesheet" href="<?php echo app_base_url('themes/admin/assets/css/admin.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo function_exists('csrf_token') ? csrf_token() : ($_SESSION['csrf_token'] ?? ''); ?>">
    <script>
        (function() {
            var origFetch = window.fetch;
            window.fetch = function(input, init) {
                init = init || {};
                var method = String(init.method || 'GET').toUpperCase();
                if (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
                    // Prefer token from hidden input field (most up-to-date), fallback to meta tag
                    var inputToken = document.querySelector('input[name="csrf_token"]')?.value;
                    var metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    var token = inputToken || metaToken || '';

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

        /* Premium Modal Styles (Global) */
        .premium-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.2s ease;
        }

        .premium-modal {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 90%;
            animation: slideUp 0.3s ease;
            overflow: hidden;
        }

        .premium-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .premium-modal-icon {
            font-size: 32px;
            opacity: 0.9;
        }

        .premium-modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .premium-modal-body {
            padding: 32px 24px;
            font-size: 15px;
            line-height: 1.6;
            color: #374151;
        }

        .premium-modal-body p {
            margin: 0;
        }

        .premium-modal-footer {
            padding: 20px 24px;
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .premium-modal-footer button {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: white;
            color: #6b7280;
            border: 1px solid #d1d5db !important;
        }

        .btn-cancel:hover {
            background: #f3f4f6;
            border-color: #9ca3af !important;
        }

        .btn-confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-confirm:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Premium Notification Styles (Global) */
        .premium-notification {
            position: fixed;
            top: 24px;
            right: 24px;
            background: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10001;
            min-width: 320px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
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
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <a href="<?php echo app_base_url('admin'); ?>" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <?php if (!empty($logo)): ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="sidebar-logo-img" style="max-height: 35px; max-width: 100%;">
                        <?php else: ?>
                            <i class="sidebar-logo-icon fas fa-calculator"></i>
                        <?php endif; ?>
                        <span class="logo-text"><?php echo htmlspecialchars($site_name); ?></span>
                    </a>
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
                            <li><a href="<?php echo app_base_url('admin/settings/general'); ?>">General</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/email'); ?>">Email</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/security'); ?>">Security</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/backup'); ?>">Backup</a></li>
                            <li><a href="<?php echo app_base_url('admin/settings/payments'); ?>">Payment Gateway</a></li>
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
                        </a>
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
    <script src="<?php echo app_base_url('themes/admin/assets/js/admin.js?v=' . time()); ?>"></script>
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

        // Premium Notification System (Global)
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `premium-notification ${type}`;
            
            const iconMap = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            };
            
            notification.innerHTML = `
                <i class="fas fa-${iconMap[type] || 'info-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => notification.classList.add('show'), 10);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Premium Confirm Modal (Global)
        function showConfirmModal(title, message, onConfirm, onCancel = null) {
            const modal = document.createElement('div');
            modal.className = 'premium-modal-overlay';
            modal.innerHTML = `
                <div class="premium-modal">
                    <div class="premium-modal-header">
                        <i class="fas fa-exclamation-triangle premium-modal-icon"></i>
                        <h3>${title}</h3>
                    </div>
                    <div class="premium-modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="premium-modal-footer">
                        <button class="btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn-confirm">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            modal.querySelector('.btn-cancel').onclick = () => {
                modal.remove();
                if (onCancel) onCancel();
            };
            
            modal.querySelector('.btn-confirm').onclick = () => {
                modal.remove();
                onConfirm();
            };
            
            modal.onclick = (e) => {
                if (e.target === modal) {
                    modal.remove();
                    if (onCancel) onCancel();
                }
            };
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

    <script>
        // Enterprise Notification System
        (function() {
            let notificationData = [];
            
            // Fetch notifications
            async function fetchNotifications() {
                try {
                    const response = await fetch('<?= app_base_url('/notifications') ?>');
                    const data = await response.json();
                    
                    if (data.success) {
                        notificationData = data.notifications;
                        renderNotifications();
                        updateUnreadCount();
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            }

            // Fetch unread count
            async function fetchUnreadCount() {
                try {
                    const response = await fetch('<?= app_base_url('/notifications/unread-count') ?>');
                    const data = await response.json();
                    
                    if (data.success) {
                        updateBadge(data.count);
                    }
                } catch (error) {
                    console.error('Error fetching unread count:', error);
                }
            }

            // Render notifications
            function renderNotifications() {
                const listEl = document.querySelector('.notification-list');
                if (!listEl) return;

                if (notificationData.length === 0) {
                    listEl.innerHTML = '<div class="empty">No notifications</div>';
                    return;
                }

                listEl.innerHTML = notificationData.map(notif => `
                    <div class="notification-item ${notif.is_read == 0 ? 'unread' : ''}" data-id="${notif.id}">
                        <div class="notification-icon ${getIconClass(notif.type)}">
                            <i class="fas ${notif.icon || 'fa-bell'}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notif.title}</div>
                            <div class="notification-message">${notif.message}</div>
                            <div class="notification-time">${formatTime(notif.created_at)}</div>
                        </div>
                        ${notif.is_read == 0 ? '<div class="unread-dot"></div>' : ''}
                    </div>
                `).join('');

                // Add click handlers
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.dataset.id;
                        markAsRead(id);
                    });
                });
            }

            // Get icon class based on type
            function getIconClass(type) {
                const classes = {
                    'system': 'icon-system',
                    'user_action': 'icon-user',
                    'email': 'icon-email',
                    'alert': 'icon-alert',
                    'info': 'icon-info'
                };
                return classes[type] || 'icon-info';
            }

            // Format time
            function formatTime(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = Math.floor((now - date) / 1000); // seconds

                if (diff < 60) return 'Just now';
                if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
                
                return date.toLocaleDateString();
            }

            // Update badge
            function updateBadge(count) {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'flex' : 'none';
                }
            }

            // Update unread count from current data
            function updateUnreadCount() {
                const unreadCount = notificationData.filter(n => n.is_read == 0).length;
                updateBadge(unreadCount);
            }

            // Mark as read
            async function markAsRead(id) {
                try {
                    const response = await fetch(`<?= app_base_url('/notifications/') ?>${id}/read`, {
                        method: 'POST'
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update local data
                        const notif = notificationData.find(n => n.id == id);
                        if (notif) notif.is_read = 1;
                        renderNotifications();
                        updateUnreadCount();
                    }
                } catch (error) {
                    console.error('Error marking as read:', error);
                }
            }

            // Mark all as read
            async function markAllAsRead() {
                try {
                    const response = await fetch('<?= app_base_url('/notifications/mark-all-read') ?>', {
                        method: 'POST'
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        notificationData.forEach(n => n.is_read = 1);
                        renderNotifications();
                        updateUnreadCount();
                        showNotification('All notifications marked as read', 'success');
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                // Fetch initial notifications
                fetchNotifications();

                // Toggle dropdown
                const toggleBtn = document.getElementById('notificationToggle');
                const dropdown = document.getElementById('notificationDropdown');
                
                if (toggleBtn && dropdown) {
                    toggleBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Bell clicked, dropdown before:', dropdown.classList.contains('show'));
                        dropdown.classList.toggle('show');
                        console.log('Bell clicked, dropdown after:', dropdown.classList.contains('show'));
                        console.log('Dropdown display:', window.getComputedStyle(dropdown).display);
                        console.log('Dropdown opacity:', window.getComputedStyle(dropdown).opacity);
                        if (dropdown.classList.contains('show')) {
                            fetchNotifications();
                        }
                    });
                }

                // Mark all as read button
                const markAllBtn = document.getElementById('markAllRead');
                if (markAllBtn) {
                    markAllBtn.addEventListener('click', markAllAsRead);
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (dropdown && !dropdown.contains(e.target) && e.target !== toggleBtn) {
                        dropdown.classList.remove('show');
                    }
                });

                // Poll for new notifications every 30 seconds
                setInterval(fetchUnreadCount, 30000);
            });
        })();
    </script>

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
</body>

</html>