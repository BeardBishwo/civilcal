<?php
$page_title = $page_title ?? 'Backup Settings';
$backup_settings = $backup_settings ?? [];
$backup_history = $backup_history ?? [];
$system_info = $system_info ?? [];
require_once __DIR__ . '/../../layouts/admin.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-download"></i> Backup Settings</h1>
        <p>Configure automated backup settings and manage system backups</p>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="createBackupNow()">
                <i class="fas fa-plus"></i> Create Backup Now
            </button>
            <button class="btn btn-secondary" onclick="restoreBackup()">
                <i class="fas fa-upload"></i> Restore Backup
            </button>
        </div>
    </div>

    <!-- Backup Statistics -->
    <div class="backup-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="stat-content">
                <h3>Total Backups</h3>
                <div class="stat-value"><?= count($backup_history) ?></div>
                <div class="stat-details">
                    <span><?= number_format(array_sum(array_column($backup_history, 'size')) / 1024 / 1024, 2) ?> MB total</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>Last Backup</h3>
                <div class="stat-value">
                    <?php if (!empty($backup_history)): ?>
                        <?= date('M j, H:i', strtotime($backup_history[0]['created_at'])) ?>
                    <?php else: ?>
                        Never
                    <?php endif; ?>
                </div>
                <div class="stat-details">
                    <span><?= !empty($backup_history) ? 'Just now' : 'No backups' ?></span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="stat-content">
                <h3>Storage Used</h3>
                <div class="stat-value"><?= number_format($system_info['backup_storage_used'] ?? 0, 1) ?>%</div>
                <div class="stat-details">
                    <span><?= number_format(($system_info['backup_storage_used'] ?? 0) * 10 / 100, 2) ?> GB of 10 GB</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>Auto Backup</h3>
                <div class="stat-value"><?= ($backup_settings['enabled'] ?? false) ? 'Enabled' : 'Disabled' ?></div>
                <div class="stat-details">
                    <span><?= $backup_settings['frequency'] ?? 'Not configured' ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="backup-settings-container">
        <!-- Backup Configuration -->
        <div class="settings-section">
            <h3>Backup Configuration</h3>
            <form method="POST" action="<?= app_base_url('/admin/settings/backup') ?>" id="backup-settings-form">
                <?php $this->csrfField(); ?>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="backup-enabled">
                            <input type="checkbox" id="backup-enabled" name="backup_enabled" 
                                   <?= ($backup_settings['enabled'] ?? false) ? 'checked' : '' ?>>
                            Enable Automatic Backups
                        </label>
                        <small class="form-text text-muted">
                            Automatically create backups at scheduled intervals
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="backup-frequency">Backup Frequency</label>
                        <select class="form-control" id="backup-frequency" name="backup_frequency">
                            <option value="daily" <?= ($backup_settings['frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="weekly" <?= ($backup_settings['frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="monthly" <?= ($backup_settings['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="backup-time">Backup Time</label>
                        <input type="time" class="form-control" id="backup-time" name="backup_time" 
                               value="<?= $backup_settings['time'] ?? '02:00' ?>">
                        <small class="form-text text-muted">
                            Time of day when backups should be created (24-hour format)
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="backup-retention">Retention Period (days)</label>
                        <input type="number" class="form-control" id="backup-retention" name="backup_retention" 
                               value="<?= $backup_settings['retention'] ?? 30 ?>" min="1" max="365">
                        <small class="form-text text-muted">
                            Number of days to keep backup files
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="backup-types">Backup Content</label>
                    <div class="backup-types">
                        <div class="backup-type-item">
                            <label>
                                <input type="checkbox" name="backup_types[]" value="database" 
                                       <?= in_array('database', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-database"></i>
                                Database
                            </label>
                        </div>
                        <div class="backup-type-item">
                            <label>
                                <input type="checkbox" name="backup_types[]" value="files" 
                                       <?= in_array('files', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-file"></i>
                                Application Files
                            </label>
                        </div>
                        <div class="backup-type-item">
                            <label>
                                <input type="checkbox" name="backup_types[]" value="uploads" 
                                       <?= in_array('uploads', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-upload"></i>
                                User Uploads
                            </label>
                        </div>
                        <div class="backup-type-item">
                            <label>
                                <input type="checkbox" name="backup_types[]" value="config" 
                                       <?= in_array('config', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-cog"></i>
                                Configuration Files
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="backup-compression">Compression Level</label>
                    <select class="form-control" id="backup-compression" name="backup_compression">
                        <option value="none" <?= ($backup_settings['compression'] ?? 'medium') === 'none' ? 'selected' : '' ?>>No Compression</option>
                        <option value="low" <?= ($backup_settings['compression'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low (Fast)</option>
                        <option value="medium" <?= ($backup_settings['compression'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium (Balanced)</option>
                        <option value="high" <?= ($backup_settings['compression'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High (Slow)</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Backup Settings
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="testBackupConfiguration()">
                        <i class="fas fa-play"></i> Test Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- Backup History -->
        <div class="backup-history-section">
            <div class="section-header">
                <h3>Backup History</h3>
                <div class="section-actions">
                    <button class="btn btn-sm btn-outline-danger" onclick="cleanupOldBackups()">
                        <i class="fas fa-trash"></i> Cleanup Old Backups
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshBackupHistory()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="backup-list">
                <?php if (!empty($backup_history)): ?>
                    <table class="backup-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backup_history as $backup): ?>
                                <tr>
                                    <td>
                                        <div class="backup-date">
                                            <?= date('M j, Y', strtotime($backup['created_at'])) ?>
                                            <small><?= date('H:i:s', strtotime($backup['created_at'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="backup-type">
                                            <?= htmlspecialchars($backup['type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= number_format($backup['size'] / 1024 / 1024, 2) ?> MB
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($backup['status']) ?>">
                                            <?= htmlspecialchars($backup['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $backup['duration'] ? number_format($backup['duration'], 2) . 's' : 'N/A' ?>
                                    </td>
                                    <td>
                                        <div class="backup-actions">
                                            <?php if ($backup['status'] === 'completed'): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" onclick="restoreFromBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-info" onclick="viewBackupDetails('<?= htmlspecialchars($backup['id']) ?>')" title="Details">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-backups">
                        <i class="fas fa-archive"></i>
                        <h4>No Backups Found</h4>
                        <p>Create your first backup to get started with data protection.</p>
                        <button class="btn btn-primary" onclick="createBackupNow()">
                            <i class="fas fa-plus"></i> Create First Backup
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- External Storage Settings -->
        <div class="storage-section">
            <h3>External Storage</h3>
            <div class="storage-options">
                <div class="storage-option">
                    <div class="storage-header">
                        <label>
                            <input type="radio" name="storage_type" value="local" 
                                   <?= ($backup_settings['storage_type'] ?? 'local') === 'local' ? 'checked' : '' ?>>
                            <i class="fas fa-hdd"></i>
                            Local Storage
                        </label>
                        <p class="storage-description">Store backups on the local server</p>
                    </div>
                </div>
                
                <div class="storage-option">
                    <div class="storage-header">
                        <label>
                            <input type="radio" name="storage_type" value="s3" 
                                   <?= ($backup_settings['storage_type'] ?? 'local') === 's3' ? 'checked' : '' ?>>
                            <i class="fab fa-aws"></i>
                            Amazon S3
                        </label>
                        <p class="storage-description">Store backups in Amazon S3 bucket</p>
                    </div>
                    <div class="storage-config" id="s3-config" style="display: <?= ($backup_settings['storage_type'] ?? 'local') === 's3' ? 'block' : 'none' ?>;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="s3-access-key">Access Key</label>
                                <input type="password" class="form-control" id="s3-access-key" name="s3_access_key" 
                                       value="<?= $backup_settings['s3_access_key'] ?? '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="s3-secret-key">Secret Key</label>
                                <input type="password" class="form-control" id="s3-secret-key" name="s3_secret_key" 
                                       value="<?= $backup_settings['s3_secret_key'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="s3-bucket">Bucket Name</label>
                                <input type="text" class="form-control" id="s3-bucket" name="s3_bucket" 
                                       value="<?= $backup_settings['s3_bucket'] ?? '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="s3-region">Region</label>
                                <select class="form-control" id="s3-region" name="s3_region">
                                    <option value="us-east-1" <?= ($backup_settings['s3_region'] ?? '') === 'us-east-1' ? 'selected' : '' ?>>US East (N. Virginia)</option>
                                    <option value="us-west-2" <?= ($backup_settings['s3_region'] ?? '') === 'us-west-2' ? 'selected' : '' ?>>US West (Oregon)</option>
                                    <option value="eu-west-1" <?= ($backup_settings['s3_region'] ?? '') === 'eu-west-1' ? 'selected' : '' ?>>EU (Ireland)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="storage-option">
                    <div class="storage-header">
                        <label>
                            <input type="radio" name="storage_type" value="ftp" 
                                   <?= ($backup_settings['storage_type'] ?? 'local') === 'ftp' ? 'checked' : '' ?>>
                            <i class="fas fa-server"></i>
                            FTP/SFTP
                        </label>
                        <p class="storage-description">Store backups on remote FTP/SFTP server</p>
                    </div>
                    <div class="storage-config" id="ftp-config" style="display: <?= ($backup_settings['storage_type'] ?? 'local') === 'ftp' ? 'block' : 'none' ?>;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ftp-host">Host</label>
                                <input type="text" class="form-control" id="ftp-host" name="ftp_host" 
                                       value="<?= $backup_settings['ftp_host'] ?? '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="ftp-port">Port</label>
                                <input type="number" class="form-control" id="ftp-port" name="ftp_port" 
                                       value="<?= $backup_settings['ftp_port'] ?? 21 ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ftp-username">Username</label>
                                <input type="text" class="form-control" id="ftp-username" name="ftp_username" 
                                       value="<?= $backup_settings['ftp_username'] ?? '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="ftp-password">Password</label>
                                <input type="password" class="form-control" id="ftp-password" name="ftp_password" 
                                       value="<?= $backup_settings['ftp_password'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeBackupSettings();
});

function initializeBackupSettings() {
    // Handle storage type selection
    document.querySelectorAll('input[name="storage_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.storage-config').forEach(config => {
                config.style.display = 'none';
            });
            
            const configId = this.value + '-config';
            const config = document.getElementById(configId);
            if (config) {
                config.style.display = 'block';
            }
        });
    });
}

function createBackupNow() {
    if (confirm('Are you sure you want to create a backup now? This may take several minutes.')) {
        showNotification('Starting backup creation...', 'info');
        
        fetch('<?= app_base_url('/admin/backup/create') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup creation started successfully', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotification('Failed to start backup: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error starting backup', 'error');
        });
    }
}

function restoreBackup() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.zip,.gz';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            if (confirm('Are you sure you want to restore from this backup? This will overwrite current data.')) {
                const formData = new FormData();
                formData.append('backup_file', file);
                
                showNotification('Starting backup restoration...', 'info');
                
                fetch('<?= app_base_url('/admin/backup/restore') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Backup restored successfully', 'success');
                        setTimeout(() => location.reload(), 3000);
                    } else {
                        showNotification('Failed to restore backup: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error restoring backup', 'error');
                });
            }
        }
    };
    input.click();
}

function downloadBackup(backupId) {
    window.open(`<?= app_base_url('/admin/backup/download') ?>/${backupId}`, '_blank');
}

function restoreFromBackup(backupId) {
    if (confirm('Are you sure you want to restore from this backup? This will overwrite current data and cannot be undone.')) {
        showNotification('Starting backup restoration...', 'info');
        
        fetch(`<?= app_base_url('/admin/backup/restore-from-id') ?>/${backupId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup restored successfully', 'success');
                setTimeout(() => location.reload(), 3000);
            } else {
                showNotification('Failed to restore backup: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error restoring backup', 'error');
        });
    }
}

function viewBackupDetails(backupId) {
    window.open(`<?= app_base_url('/admin/backup/details') ?>/${backupId}`, '_blank');
}

function deleteBackup(backupId) {
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        fetch(`<?= app_base_url('/admin/backup/delete') ?>/${backupId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to delete backup', 'error');
            }
        });
    }
}

function cleanupOldBackups() {
    if (confirm('Are you sure you want to delete old backups? This will remove backups older than the retention period.')) {
        fetch('<?= app_base_url('/admin/backup/cleanup') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Cleaned up ${data.deleted_count} old backups`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to cleanup old backups', 'error');
            }
        });
    }
}

function refreshBackupHistory() {
    location.reload();
}

function testBackupConfiguration() {
    showNotification('Testing backup configuration...', 'info');
    
    fetch('<?= app_base_url('/admin/backup/test') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= $this->csrfToken() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Backup configuration test passed', 'success');
        } else {
            showNotification('Backup configuration test failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing backup configuration', 'error');
    });
}

</script>

<style>
.backup-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #212529;
    margin-bottom: 5px;
}

.stat-details {
    font-size: 12px;
    color: #6c757d;
}

.backup-settings-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.settings-section,
.backup-history-section,
.storage-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.settings-section {
    grid-column: 1 / -1;
}

.backup-history-section {
    grid-column: 1;
    grid-row: 2;
}

.storage-section {
    grid-column: 2;
    grid-row: 2;
}

.settings-section h3,
.backup-history-section h3,
.storage-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-actions {
    display: flex;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.backup-types {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.backup-type-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
}

.backup-type-item label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    margin: 0;
}

.backup-type-item i {
    font-size: 16px;
    color: #007bff;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.backup-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.backup-table th,
.backup-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.backup-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.backup-date small {
    display: block;
    color: #6c757d;
    font-size: 11px;
}

.backup-type {
    background: #e9ecef;
    color: #495057;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-running {
    background: #cce5ff;
    color: #004085;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.backup-actions {
    display: flex;
    gap: 5px;
}

.empty-backups {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-backups i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
}

.empty-backups h4 {
    margin-bottom: 10px;
    color: #495057;
}

.storage-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.storage-option {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.storage-header {
    padding: 15px;
    background: #f8f9fa;
}

.storage-header label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    color: #495057;
    cursor: pointer;
    margin: 0;
}

.storage-header i {
    font-size: 16px;
    color: #007bff;
}

.storage-description {
    margin: 5px 0 0 0;
    color: #6c757d;
    font-size: 12px;
}

.storage-config {
    padding: 15px;
    background: white;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .backup-settings-container {
        grid-template-columns: 1fr;
    }
    
    .backup-history-section,
    .storage-section {
        grid-column: 1;
        grid-row: auto;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .backup-types {
        grid-template-columns: 1fr;
    }
    
    .backup-table {
        font-size: 12px;
    }
    
    .backup-table th,
    .backup-table td {
        padding: 8px;
    }
}
</style>