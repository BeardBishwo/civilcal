<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Content Management - Admin Panel'; ?></title>
    <link rel="stylesheet" href="<?php echo app_base_url('/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-layout">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <div class="admin-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-content">
                <div class="content-header">
                    <h1>Content Management</h1>
                    <p>Manage your website's content, pages, menus, and media files.</p>
                </div>

                <div class="content-grid">
                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="card-content">
                            <h3>Pages</h3>
                            <p>Create and manage website pages</p>
                        </div>
                        <div class="card-actions">
                            <a href="<?php echo app_base_url('/admin/content/pages'); ?>">
                                <button class="btn btn-primary">Manage Pages</button>
                            </a>
                        </div>

                        <div class="content-card">
                            <div class="card-icon">
                                <i class="fas fa-bars"></i>
                            </div>
                        </div>
                    </div>
                </div>
        </main>
    </div>
</body>

</html>