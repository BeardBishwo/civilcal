<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Backup Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage system backups and recovery options</p>
        </div>
    </div>
</div>

<!-- Backup Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-database" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Backups</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_backups'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Stored</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> All Valid</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-hdd" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Storage Used</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['storage_used'] ?? '0 MB'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">of <?php echo $stats['storage_limit'] ?? '1 GB'; ?></div>
        <small style="color: <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'Almost Full' : 'Plenty Space'; ?>
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Last Backup</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['last_backup'] ?? 'Never'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">ago</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-sync"></i> Automated</small>
    </div>
</div>

<!-- Backup Status -->
<div class="admin-card">
    <h2 class="admin-card-title">Backup Status</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #4cc9f0;"></i>
                Database Backup
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Up to Date</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Size:</span>
                <span style="color: #f9fafb;"><?php echo $stats['db_size'] ?? '0 MB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-folder" style="color: #34d399;"></i>
                File Backup
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Up to Date</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Size:</span>
                <span style="color: #f9fafb;"><?php echo $stats['files_size'] ?? '0 MB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #fbbf24;"></i>
                Complete Backup
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Up to Date</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Size:</span>
                <span style="color: #f9fafb;"><?php echo $stats['complete_size'] ?? '0 MB'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Available Backups -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Backups</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Name</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Date</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Type</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Size</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($backups)): ?>
                        <?php foreach ($backups as $backup): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($backup['name'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $backup['date'] ?? ''; ?></td>
                                <td style="padding: 0.75rem;"><?php echo ucfirst($backup['type'] ?? 'full'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $backup['size'] ?? '0 MB'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                        <i class="fas fa-check-circle"></i> Valid
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/backup/restore/'.($backup['id'] ?? 0)); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-undo"></i>
                                        <span>Restore</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/backup/download/'.($backup['id'] ?? 0)); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-download"></i>
                                        <span>Download</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/backup/delete/'.($backup['id'] ?? 0)); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No backups available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Backup Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Backup Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/backup/create/database'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-database"></i>
            <span>Create DB Backup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/backup/create/files'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-folder"></i>
            <span>Create Files Backup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/backup/create/complete'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-shield-alt"></i>
            <span>Create Full Backup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/backup/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-upload"></i>
            <span>Upload Backup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/backup/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>