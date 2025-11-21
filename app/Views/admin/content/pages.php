<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Pages Management - Admin Panel'; ?></title>
    <link rel="stylesheet" href="<?php echo app_base_url('/assets/css/admin.css'); ?>">
</head>

<body class="admin-layout">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <div class="admin-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-content">
                <div class="content-header">
                    <h1>Pages Management</h1>
                    <p>Create, edit, and manage your website pages.</p>
                </div>

                <div class="page-list">
                    <table class="page-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pages)): ?>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($page['title']); ?></td>
                                        <td><?php echo htmlspecialchars($page['slug']); ?></td>
                                        <td><?php echo htmlspecialchars($page['status']); ?></td>
                                        <td>
                                            <button class="btn btn-primary">Edit</button>
                                            <button class="btn btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-pages">No pages created yet.</p>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </main>
    </div>
</body>

</html>