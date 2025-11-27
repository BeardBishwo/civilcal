<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-cube"></i> Widget Management</h1>
            <p class="page-description">Manage widgets for the Bishwo Calculator system</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/widgets/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create New Widget
            </a>
            <a href="<?php echo app_base_url('/admin/widgets/setup'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-cog"></i> Setup Database Tables
            </a>
        </div>
    </div>
</div>

<!-- Status Information -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-code"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($available_classes ?? []); ?></div>
        <div class="stat-label">Widget Classes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count(array_filter($widgets ?? [], fn($w) => $w->isEnabled())); ?></div>
        <div class="stat-label">Active Widgets</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-cubes"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($widgets ?? []); ?></div>
        <div class="stat-label">Total Widgets</div>
    </div>
</div>

<!-- Widgets List -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-th-large"></i>
            Installed Widgets
        </h5>
    </div>
    
    <div class="card-content">
        <?php if (empty($widgets)): ?>
            <div class="empty-state">
                <i class="fas fa-cube fa-3x text-gray-300"></i>
                <h3 class="mt-3">No widgets found</h3>
                <p class="text-gray-500">Get started by creating your first widget.</p>
                <a href="<?php echo app_base_url('/admin/widgets/create'); ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Create Widget
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($widgets as $widget): ?>
                    <div class="card <?php echo $widget->isEnabled() ? '' : 'border-secondary'; ?>">
                        <div class="card-header">
                            <h6 class="card-title"><?php echo htmlspecialchars($widget->getTitle()); ?></h6>
                            <div class="card-actions">
                                <a href="<?php echo app_base_url('/admin/widgets/edit/'); ?><?php echo $widget->getId(); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/widgets/preview/'); ?><?php echo $widget->getId(); ?>" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-content">
                            <p class="text-gray-600"><?php echo htmlspecialchars($widget->getDescription()); ?></p>
                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="badge bg-info"><?php echo htmlspecialchars($widget->getType()); ?></span>
                                <span class="badge <?php echo $widget->isEnabled() ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $widget->isEnabled() ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="flex gap-2">
                                <form method="post" action="<?php echo app_base_url('/admin/widgets/toggle/'); ?><?php echo $widget->getId(); ?>" class="flex-1">
                                    <button type="submit" class="btn btn-sm w-full <?php echo $widget->isEnabled() ? 'btn-warning' : 'btn-success'; ?>">
                                        <i class="fas <?php echo $widget->isEnabled() ? 'fa-pause' : 'fa-play'; ?>"></i>
                                        <?php echo $widget->isEnabled() ? 'Disable' : 'Enable'; ?>
                                    </button>
                                </form>
                                
                                <form method="post" action="<?php echo app_base_url('/admin/widgets/delete/'); ?><?php echo $widget->getId(); ?>" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this widget?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-full">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Available Widget Classes -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-code"></i>
            Available Widget Classes
        </h5>
    </div>
    
    <div class="card-content">
        <?php if (empty($available_classes ?? [])): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-code fa-2x"></i>
                <p class="mt-2">No widget classes available</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($available_classes ?? [] as $className): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <code class="text-sm"><?php echo htmlspecialchars($className); ?></code>
                        <a href="<?php echo app_base_url('/admin/widgets/create'); ?>?class=<?php echo urlencode($className); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Create
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Widget management interface loaded');
});
</script>