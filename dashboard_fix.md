# Dashboard UI Fix

## Issue
`http://localhost/Bishwo_Calculator/admin/configured-dashboard` shows blank page.

## Root Cause
`themes/admin/views/configured-dashboard.php` includes `require_once __DIR__ . '/../layouts/main.php'` but the layouts directory doesn't exist.

## Fix
Replace the include with a complete HTML structure:

```php
<?php
$page_title = $page_title ?? 'Configured Dashboard';
$dashboard_config = $dashboard_config ?? [];
$available_widgets = $available_widgets ?? [];
$menu_items = $menu_items ?? [];

// Start output buffering to capture content
ob_start();
?>

<!-- Complete HTML structure instead of layout include -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= app_base_url('themes/admin/assets/css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1><?= htmlspecialchars($page_title) ?></h1>
            </div>
            <div class="header-right">
                <a href="<?= app_base_url('/admin') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </header>

        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?= app_base_url('/admin') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?= app_base_url('/admin/users') ?>"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="<?= app_base_url('/admin/settings') ?>"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="<?= app_base_url('/admin/modules') ?>"><i class="fas fa-puzzle-piece"></i> Modules</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Admin Content -->
        <main class="admin-main">
            <?php
            // Output the buffered content (the dashboard HTML)
            $content = ob_get_clean();
            echo $content;
            ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= app_base_url('themes/admin/assets/js/admin.js') ?>"></script>
</body>
</html>
```

## Alternative Quick Fix
If you want to keep the original structure, create the missing layout file:

1. Create directory: `themes/admin/views/layouts/`
2. Create file: `themes/admin/views/layouts/main.php` with basic HTML structure
3. Or modify `configured-dashboard.php` to not require the layout

The issue is that the view expects a layout file that doesn't exist, causing a blank page.