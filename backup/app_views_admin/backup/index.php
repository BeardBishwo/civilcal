<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Backup Management - Admin Panel'; ?></title>
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
                    <h1>Backup Management</h1>
                    <p>Create, restore, and manage application backups.</p>
                </div>

                <div class="backup-controls">
                    <button class="btn btn-success">
                        <i class="fas fa-plus"></i> Create New Backup
                    </button>
                </div>

                <div class="backup-list">
                    <h3>Available Backups</h3>
                    <?php if (!empty($backups)): ?>
                        <table class="backup-table">
                            <thead>
                                <tr>
                                    <th>Backup Name</th>
                                    <th>Size</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($backup['name']); ?></td>
                                        <td><?php echo htmlspecialchars($backup['formatted_size'] ?? 'Unknown'); ?></td>
                                        <td><?php echo htmlspecialchars($backup['date'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <a href="<?php echo app_base_url('/admin/backup/download/'); ?>' + <?php echo htmlspecialchars(json_encode($backup['name'])); ?>">
                                                <button class="btn btn-info">
                                                    <i class="fas fa-download"></i> Download
                                            </a>
                                            <button class="btn btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-backups">No backups available. Create your first backup.</p>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                </div>
        </main>
    </div>
</body>

</html>