<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Widget Management') ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/widgets.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>📊 Widget Management</h1>
            <p>Manage widgets for the Bishwo Calculator system</p>
        </div>

        <!-- Breadcrumbs -->
        <?php if (!empty($breadcrumbs)): ?>
            <nav class="breadcrumbs">
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                    <?php if (!$loop->last): ?> > <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <!-- Status Information -->
        <div class="status-section">
            <h3>System Status</h3>
            <div class="status-cards">
                <div class="status-card">
                    <h4>Widget Classes</h4>
                    <div class="status-number"><?= count($available_classes ?? []) ?></div>
                </div>
                <div class="status-card">
                    <h4>Active Widgets</h4>
                    <div class="status-number"><?= count(array_filter($widgets ?? [], fn($w) => $w->isEnabled())) ?></div>
                </div>
                <div class="status-card">
                    <h4>Total Widgets</h4>
                    <div class="status-number"><?= count($widgets ?? []) ?></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-section">
            <a href="/admin/widgets/create" class="btn btn-primary">➕ Create New Widget</a>
            <a href="/admin/widgets/setup" class="btn btn-secondary">⚙️ Setup Database Tables</a>
        </div>

        <!-- Widgets List -->
        <div class="widgets-list">
            <h3>Installed Widgets</h3>
            
            <?php if (empty($widgets)): ?>
                <div class="no-widgets">
                    <p>No widgets found. <a href="/admin/widgets/create">Create your first widget</a></p>
                </div>
            <?php else: ?>
                <div class="widgets-grid">
                    <?php foreach ($widgets as $widget): ?>
                        <div class="widget-item <?= $widget->isEnabled() ? 'enabled' : 'disabled' ?>">
                            <div class="widget-header">
                                <h4><?= htmlspecialchars($widget->getTitle()) ?></h4>
                                <div class="widget-actions">
                                    <a href="/admin/widgets/edit/<?= $widget->getId() ?>" class="btn btn-sm">Edit</a>
                                    <a href="/admin/widgets/preview/<?= $widget->getId() ?>" class="btn btn-sm">Preview</a>
                                </div>
                            </div>
                            <div class="widget-content">
                                <p><?= htmlspecialchars($widget->getDescription()) ?></p>
                                <div class="widget-meta">
                                    <span class="widget-type"><?= htmlspecialchars($widget->getType()) ?></span>
                                    <span class="widget-status <?= $widget->isEnabled() ? 'status-active' : 'status-inactive' ?>">
                                        <?= $widget->isEnabled() ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="widget-footer">
                                <div class="footer-actions">
                                    <form method="post" action="/admin/widgets/toggle/<?= $widget->getId() ?>" style="display: inline;">
                                        <button type="submit" class="btn btn-sm <?= $widget->isEnabled() ? 'btn-warning' : 'btn-success' ?>">
                                            <?= $widget->isEnabled() ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="/admin/widgets/delete/<?= $widget->getId() ?>" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Available Widget Classes -->
        <div class="available-classes">
            <h3>Available Widget Classes</h3>
            <div class="classes-list">
                <?php foreach ($available_classes ?? [] as $className): ?>
                    <div class="class-item">
                        <code><?= htmlspecialchars($className) ?></code>
                        <a href="/admin/widgets/create?class=<?= urlencode($className) ?>" class="btn btn-sm">Create Widget</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Add any necessary JavaScript for widget management
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh widget preview when needed
            console.log('Widget management interface loaded');
        });
    </script>
</body>
</html>
