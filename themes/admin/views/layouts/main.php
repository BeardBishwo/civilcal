<?php

/**
 * Admin Main Layout
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?> - Bishwo Calculator</title>
    <link rel="stylesheet" href="<?= app_base_url('public/assets/css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="admin-body">
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="header-left">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="logo">
                <i class="fas fa-calculator"></i>
                Bishwo Calculator
            </h1>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <button id="notificationToggle" class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div id="notificationDropdown" class="dropdown-menu">
                    <div class="dropdown-header">
                        Notifications (3)
                    </div>
                    <div class="dropdown-item">
                        New user registered
                    </div>
                </div>
                <div class="user-menu">
                    <button id="profileToggle" class="profile-btn">
                        <img src="<?= app_base_url('public/uploads/avatars/default.jpg') ?>" alt="Profile" class="avatar">
                        <span><?= $_SESSION['username'] ?? 'Admin' ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div id="profileDropdown" class="dropdown-menu">
                        <div class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </div>
                        <div class="dropdown-item">
                            <i class="fas fa-cog"></i> Settings
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="<?= app_base_url('/logout') ?>" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Admin Sidebar -->
    <aside id="adminSidebar" class="admin-sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?= app_base_url('/admin') ?>" class="<?= $currentPage == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <i class="fas fa-cog"></i>
                        Settings
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= app_base_url('/admin/settings/general') ?>">General</a></li>
                        <li><a href="<?= app_base_url('/admin/settings/email') ?>">Email</a></li>
                        <li><a href="<?= app_base_url('/admin/settings/security') ?>">Security</a></li>
                    </ul>
                </li>
                <li>
                    <a href="<?= app_base_url('/admin/users') ?>" class="<?= $currentPage == 'users' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                </li>
                <li>
                    <a href="<?= app_base_url('/admin/modules') ?>" class="<?= $currentPage == 'modules' ? 'active' : '' ?>">
                        <i class="fas fa-puzzle-piece"></i>
                        Modules
                    </a>
                </li>
                <li>
                    <a href="<?= app_base_url('/admin/themes') ?>">
                        <i class="fas fa-palette"></i>
                        Themes
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Admin Main Content -->
    <main class="admin-main">
        <?= $content ?? '' ?>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="<?= app_base_url('public/assets/js/admin.js?v=' . time()) ?>"></script>
</body>

</html>