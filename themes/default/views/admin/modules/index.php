<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Modules Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and configure all available modules</p>
</div>
</div>

<!-- Modules Grid -->
<div class="admin-grid">
    <?php foreach ($modules as $module): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><?php echo htmlspecialchars($module['name']); ?></h2>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;"><?php echo htmlspecialchars($module['description']); ?></p>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <span style="color: #9ca3af;">Status:</span>
                <span class="status-<?php echo $module['status'] === 'active' ? 'success' : 'error'; ?>">
                <?php echo ucfirst($module['status']); ?>
            </div>
            
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <a href="<?php echo app_base_url("/admin/modules/{$module['slug']}/settings"); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; text-decoration: none; margin-top: 1rem;">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Quick Actions</h2>
    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <div style="display: flex; gap: 1rem;">
            <a href="<?php echo app_base_url('/admin/modules'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Modules</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>