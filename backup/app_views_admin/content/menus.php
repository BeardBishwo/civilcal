<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Menus Management - Admin Panel'; ?></title>
    <link rel="stylesheet" href="<?php echo app_base_url('/assets/css/admin.css'); ?>">
</head>

<body class="admin-layout">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <div class="admin-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-content">
                <div class="content-header">
                    <h1>Menus Management</h1>
                    <p>Create and manage navigation menus for your website.</p>
                </div>

                <div class="menu-list">
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th>Menu Name</th>
                                <th>Location</th>
                                <th>Items Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($menus)): ?>
                                <?php foreach ($menus as $menu): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($menu['name']); ?></td>
                                        <td><?php echo htmlspecialchars($menu['location']); ?></td>
                                        <td><?php echo htmlspecialchars($menu['items_count']); ?></td>
                                        <td>
                                            <button class="btn btn-primary">Edit</button>
                                            <button class="btn btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-menus">No menus created yet.</p>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </main>
    </div>
</body>

</html>