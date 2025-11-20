<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Backup Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Create and manage system backups</p>
    </div>
</div>

<!-- Backup Statistics -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-hdd" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Backups</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_backups'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Latest Backup</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Backup Status: Healthy</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <i class="fas fa-cloud-download-alt" style="font-size: 1.5rem; color: #34d399; margin-bottom: 0.5rem;">Storage Usage</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Database Backups</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;"><?php echo number_format($stats['database_backups'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Backup Storage</p>
        </div>
    </div>
</div>

<!-- Recent Backups -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Backups</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: #34d399;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem; margin: 0;">Full system backup completed</li>
            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-database" style="color: #22d3ee;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem; margin: 0;">Database backup created</li>
            <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-file-archive" style="color: #fbbf24;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem; margin: 0;">File system backup completed</li>
        </ul>
    </div>
</div>

<!-- Backup Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Backup Operations</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/backup/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-plus-circle"></i>
            <span>Create New Backup</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/backup/restore'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-undo-alt"></i>
                <span>Restore System</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/backup/schedule'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <i class="fas fa-clock"></i>
            <span>Schedule Backups</span>
            </a>
        </div>
    </div>
</div>

<!-- Backup Settings -->
<div class="admin-card">
    <h2 class="admin-card-title">Backup Configuration</h2>
    <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Automated backup settings</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>