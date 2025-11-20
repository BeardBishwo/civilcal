<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <nav class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>Admin Panel</h2>
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

                <!-- User Management Section -->
                <div class="admin-nav-section">
                    <h3>Users</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/users'); ?>"
                   class="admin-nav-link <?php echo ($currentPage ?? '') === 'users' ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Content Management Section -->
                <div class="admin-nav-section">
                    <h3>Content</h3>
                    <ul class="admin-nav-links">
                        <li>
                            <a href="<?php echo app_base_url('/admin/users'); ?>"
                   class="admin-nav-link <?php echo ($currentPage ?? '') === 'modules' ? 'active' : ''; ?>">
                                <i class="fas fa-cubes"></i>
                                <span>Modules</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <h1><?php echo $page_title ?? $title ?? 'Admin Panel'; ?></h1>
                </div>
            </header>

            <!-- Page Content -->
            <?php echo $content ?? ''; ?>

        </main>
    </div>
</body>
</html>