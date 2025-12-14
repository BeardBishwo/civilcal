<?php
/**
 * PREMIUM BACKUP SETTINGS INTERFACE
 * Matching the design of other admin pages
 */

$page_title = $page_title ?? 'Backup Settings';
$backup_settings = $backup_settings ?? [];
$backup_history = $backup_history ?? [];
$system_info = $system_info ?? [];

// Calculate stats
$totalBackups = count($backup_history);
$totalSize = $totalBackups > 0 ? array_sum(array_column($backup_history, 'size')) / 1024 / 1024 : 0;
$lastBackup = !empty($backup_history) ? $backup_history[0]['created_at'] : null;
$autoBackupEnabled = $backup_settings['enabled'] ?? false;
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-database"></i>
                    <h1>Backup & Restore</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalBackups; ?> backups • <?php echo number_format($totalSize, 2); ?> MB total</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-secondary btn-compact" onclick="restoreBackup()">
                    <i class="fas fa-upload"></i>
                    <span>Restore Backup</span>
                </button>
                <button class="btn btn-primary btn-compact" onclick="createBackupNow()">
                    <i class="fas fa-plus"></i>
                    <span>Create Backup Now</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-archive"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalBackups; ?></div>
                    <div class="stat-label">Total Backups</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $lastBackup ? date('M j, H:i', strtotime($lastBackup)) : 'Never'; ?></div>
                    <div class="stat-label">Last Backup</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($totalSize, 1); ?> MB</div>
                    <div class="stat-label">Storage Used</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon <?php echo $autoBackupEnabled ? 'success' : 'warning'; ?>">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $autoBackupEnabled ? 'Enabled' : 'Disabled'; ?></div>
                    <div class="stat-label">Auto Backup</div>
                </div>
            </div>
        </div>

        <!-- Backup Configuration -->
        <div class="settings-card">
            <div class="settings-card-header">
                <i class="fas fa-cog"></i>
                <h3>Backup Configuration</h3>
            </div>
            <div class="settings-card-body">
                <form method="POST" action="<?= app_base_url('/admin/settings/backup') ?>" id="backup-settings-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="form-row-compact">
                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="backup_enabled" 
                                       <?= ($backup_settings['enabled'] ?? false) ? 'checked' : '' ?>>
                                <span>Enable Automatic Backups</span>
                            </label>
                            <small>Automatically create backups at scheduled intervals</small>
                        </div>
                        <div class="form-group-compact">
                            <label>Backup Frequency</label>
                            <select class="form-control-compact" name="backup_frequency">
                                <option value="daily" <?= ($backup_settings['frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                                <option value="weekly" <?= ($backup_settings['frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                <option value="monthly" <?= ($backup_settings['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-compact">
                        <div class="form-group-compact">
                            <label>Backup Time</label>
                            <input type="time" class="form-control-compact" name="backup_time" 
                                   value="<?= $backup_settings['time'] ?? '02:00' ?>">
                            <small>Time of day when backups should be created (24-hour format)</small>
                        </div>
                        <div class="form-group-compact">
                            <label>Retention Period (days)</label>
                            <input type="number" class="form-control-compact" name="backup_retention" 
                                   value="<?= $backup_settings['retention'] ?? 30 ?>" min="1" max="365">
                            <small>Number of days to keep backup files</small>
                        </div>
                    </div>

                    <div class="form-group-compact">
                        <label>Backup Content</label>
                        <div class="backup-types-grid">
                            <label class="backup-type-checkbox">
                                <input type="checkbox" name="backup_types[]" value="database" 
                                       <?= in_array('database', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-database"></i>
                                <span>Database</span>
                            </label>
                            <label class="backup-type-checkbox">
                                <input type="checkbox" name="backup_types[]" value="files" 
                                       <?= in_array('files', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-file"></i>
                                <span>Application Files</span>
                            </label>
                            <label class="backup-type-checkbox">
                                <input type="checkbox" name="backup_types[]" value="uploads" 
                                       <?= in_array('uploads', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-upload"></i>
                                <span>User Uploads</span>
                            </label>
                            <label class="backup-type-checkbox">
                                <input type="checkbox" name="backup_types[]" value="config" 
                                       <?= in_array('config', $backup_settings['types'] ?? ['database']) ? 'checked' : '' ?>>
                                <i class="fas fa-cog"></i>
                                <span>Configuration</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row-compact">
                        <div class="form-group-compact">
                            <label>Compression Level</label>
                            <select class="form-control-compact" name="backup_compression">
                                <option value="none" <?= ($backup_settings['compression'] ?? 'medium') === 'none' ? 'selected' : '' ?>>No Compression</option>
                                <option value="low" <?= ($backup_settings['compression'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low (Fast)</option>
                                <option value="medium" <?= ($backup_settings['compression'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium (Balanced)</option>
                                <option value="high" <?= ($backup_settings['compression'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High (Slow)</option>
                            </select>
                        </div>
                        <div class="form-group-compact">
                            <label>Storage Location</label>
                            <select class="form-control-compact" name="storage_type">
                                <option value="local" <?= ($backup_settings['storage_type'] ?? 'local') === 'local' ? 'selected' : '' ?>>Local Storage</option>
                                <option value="s3" <?= ($backup_settings['storage_type'] ?? 'local') === 's3' ? 'selected' : '' ?>>Amazon S3</option>
                                <option value="ftp" <?= ($backup_settings['storage_type'] ?? 'local') === 'ftp' ? 'selected' : '' ?>>FTP/SFTP</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions-compact">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="testBackupConfiguration()">
                            <i class="fas fa-play"></i> Test Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Backup History -->
        <div class="settings-card">
            <div class="settings-card-header">
                <i class="fas fa-history"></i>
                <h3>Backup History</h3>
                <div class="header-actions-inline">
                    <button class="btn btn-sm btn-outline-danger" onclick="cleanupOldBackups()">
                        <i class="fas fa-trash"></i> Cleanup Old
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshBackupHistory()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="settings-card-body">
                <?php if (!empty($backup_history)): ?>
                    <div class="table-wrapper">
                        <table class="table-compact">
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
                                            <div class="date-info">
                                                <div class="date-primary"><?= date('M j, Y', strtotime($backup['created_at'])) ?></div>
                                                <div class="date-secondary"><?= date('H:i:s', strtotime($backup['created_at'])) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($backup['type']) ?></span>
                                        </td>
                                        <td><?= number_format($backup['size'] / 1024 / 1024, 2) ?> MB</td>
                                        <td>
                                            <?php if ($backup['status'] === 'completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($backup['status'] === 'running'): ?>
                                                <span class="badge bg-info">Running</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $backup['duration'] ? number_format($backup['duration'], 2) . 's' : 'N/A' ?></td>
                                        <td>
                                            <div class="actions-compact">
                                                <?php if ($backup['status'] === 'completed'): ?>
                                                    <button class="action-btn-icon" onclick="downloadBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="action-btn-icon" onclick="restoreFromBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Restore">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button class="action-btn-icon delete-btn" onclick="deleteBackup('<?= htmlspecialchars($backup['id']) ?>')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-archive"></i>
                        <h3>No Backups Found</h3>
                        <p>Create your first backup to get started with data protection.</p>
                        <button class="btn btn-primary" onclick="createBackupNow()">
                            <i class="fas fa-plus"></i> Create First Backup
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
function createBackupNow() {
    showConfirmModal('Create Backup', 'Are you sure you want to create a backup now? This may take several minutes.', () => {
        showLoading('Creating backup...');
        fetch('<?= app_base_url('/admin/backup/create') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Backup creation started successfully', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotification('Failed to start backup: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showNotification('Error starting backup', 'error');
            console.error(error);
        });
    });
}

function restoreBackup() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.zip,.gz';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            showConfirmModal('Restore Backup', 'Are you sure you want to restore from this backup? This will overwrite current data.', () => {
                const formData = new FormData();
                formData.append('backup_file', file);
                
                showLoading('Restoring backup...');
                fetch('<?= app_base_url('/admin/backup/restore') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showNotification('Backup restored successfully', 'success');
                        setTimeout(() => location.reload(), 3000);
                    } else {
                        showNotification('Failed to restore backup: ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showNotification('Error restoring backup', 'error');
                    console.error(error);
                });
            });
        }
    };
    input.click();
}

function downloadBackup(backupId) {
    window.open(`<?= app_base_url('/admin/backup/download') ?>/${backupId}`, '_blank');
}

function restoreFromBackup(backupId) {
    showConfirmModal('Restore Backup', 'Are you sure you want to restore from this backup? This will overwrite current data and cannot be undone.', () => {
        showLoading('Restoring backup...');
        fetch(`<?= app_base_url('/admin/backup/restore-from-id') ?>/${backupId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Backup restored successfully', 'success');
                setTimeout(() => location.reload(), 3000);
            } else {
                showNotification('Failed to restore backup: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showNotification('Error restoring backup', 'error');
            console.error(error);
        });
    });
}

function deleteBackup(backupId) {
    showConfirmModal('Delete Backup', 'Are you sure you want to delete this backup? This action cannot be undone.', () => {
        fetch(`<?= app_base_url('/admin/backup/delete') ?>/${backupId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
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
    });
}

function cleanupOldBackups() {
    showConfirmModal('Cleanup Backups', 'Are you sure you want to delete old backups? This will remove backups older than the retention period.', () => {
        showLoading('Cleaning up...');
        fetch('<?= app_base_url('/admin/backup/cleanup') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification(`Cleaned up ${data.deleted_count || 0} old backups`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to cleanup old backups', 'error');
            }
        });
    });
}

function refreshBackupHistory() {
    location.reload();
}

function testBackupConfiguration() {
    showLoading('Testing configuration...');
    fetch('<?= app_base_url('/admin/backup/test') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Backup configuration test passed', 'success');
        } else {
            showNotification('Backup configuration test failed: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showNotification('Error testing backup configuration', 'error');
        console.error(error);
    });
}
</script>

<style>
/* Enhanced Settings Card */
.settings-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 2rem;
}

.settings-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
}

.settings-card-header i {
    font-size: 1.5rem;
    opacity: 0.9;
}

.settings-card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    flex: 1;
}

.settings-card-body {
    padding: 2rem;
}

/* Enhanced Form Groups */
.form-group-compact {
    margin-bottom: 1.75rem;
}

.form-group-compact label {
    display: block;
    margin-bottom: 0.625rem;
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9375rem;
}

.form-row-compact {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
}

.form-control-compact {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9375rem;
    transition: all 0.2s;
    background: white;
}

.form-control-compact:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group-compact small {
    display: block;
    margin-top: 0.5rem;
    color: #718096;
    font-size: 0.8125rem;
    line-height: 1.4;
}

/* Enhanced Checkbox Label */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.5rem 0;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    border-radius: 4px;
}

.checkbox-label span {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9375rem;
}

/* Backup Types Grid */
.backup-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 0.75rem;
}

.backup-type-checkbox {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.backup-type-checkbox:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.backup-type-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.backup-type-checkbox i {
    color: #667eea;
    font-size: 1.25rem;
}

.backup-type-checkbox span {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9375rem;
}

/* Form Actions */
.form-actions-compact {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #e2e8f0;
}

.form-actions-compact .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}

/* Header Actions Inline */
.header-actions-inline {
    display: flex;
    gap: 0.75rem;
    margin-left: auto;
}

.header-actions-inline .btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 6px;
}

/* Enhanced Table Wrapper */
.table-wrapper {
    margin-top: 1.5rem;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.table-compact {
    width: 100%;
    border-collapse: collapse;
}

.table-compact thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table-compact th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 700;
    color: #2d3748;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
}

.table-compact td {
    padding: 1.25rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.9375rem;
}

.table-compact tbody tr {
    transition: background 0.2s;
}

.table-compact tbody tr:hover {
    background: #f8f9fa;
}

/* Date Info */
.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-primary {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9375rem;
}

.date-secondary {
    font-size: 0.8125rem;
    color: #718096;
}

/* Enhanced Badges */
.badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 600;
    display: inline-block;
}

.bg-success {
    background: #d4edda;
    color: #155724;
}

.bg-info {
    background: #d1ecf1;
    color: #0c5460;
}

.bg-danger {
    background: #f8d7da;
    color: #721c24;
}

.bg-light {
    background: #f8f9fa;
    color: #495057;
}

/* Enhanced Actions */
.actions-compact {
    display: flex;
    gap: 0.5rem;
}

.action-btn-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    background: white;
    color: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn-icon:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}

.action-btn-icon.delete-btn {
    color: #e53e3e;
    border-color: #feb2b2;
}

.action-btn-icon.delete-btn:hover {
    background: #e53e3e;
    color: white;
    border-color: #e53e3e;
}

/* Enhanced Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #718096;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    display: block;
    color: #cbd5e0;
}

.empty-state h3 {
    margin-bottom: 0.75rem;
    color: #2d3748;
    font-size: 1.5rem;
    font-weight: 700;
}

.empty-state p {
    margin-bottom: 2rem;
    color: #718096;
    font-size: 1rem;
}

.empty-state .btn {
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .settings-card-header {
        padding: 1.25rem 1.5rem;
    }
    
    .settings-card-body {
        padding: 1.5rem;
    }
    
    .form-row-compact {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    
    .backup-types-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions-compact {
        flex-direction: column;
    }
    
    .form-actions-compact .btn {
        width: 100%;
    }
}
</style>
