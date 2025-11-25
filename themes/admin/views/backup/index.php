<?php
// Backup Management View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-database"></i>
            Backup Management
        </h1>
        <p class="page-description">Create, manage, and restore backups of your application data</p>
    </div>

    <!-- Backup Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cog"></i>
                Backup Actions
            </h3>
        </div>
        <div class="card-content">
            <div class="backup-options">
                <div class="form-group">
                    <label class="form-label">Backup Name (optional)</label>
                    <input type="text" id="backup-name" class="form-control" placeholder="Enter backup name (default: current timestamp)">
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="checkbox" id="include-database" checked>
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Include Database</span>
                    </label>
                    
                    <label class="checkbox-item">
                        <input type="checkbox" id="include-files" checked>
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Include Files</span>
                    </label>
                </div>
                
                <button id="create-backup" class="btn btn-primary">
                    <i class="fas fa-download"></i>
                    Create New Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                Available Backups
            </h3>
            <div class="card-actions">
                <button class="btn btn-sm btn-secondary" onclick="refreshBackupList()">
                    <i class="fas fa-sync"></i>
                    Refresh
                </button>
            </div>
        </div>
        <div class="card-content">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="backup-list">
                        ' . implode('', array_map(function($backup) {
                            return '<tr>
                                <td>' . htmlspecialchars($backup['name']) . '</td>
                                <td>' . ($backup['size_formatted'] ?? $backup['size'] ?? 'Unknown') . '</td>
                                <td>' . $backup['date'] . '</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="' . app_base_url('admin/backup/download/' . urlencode($backup['name'])) . '" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning" onclick="restoreBackup(\'' . urlencode($backup['name']) . '\')">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup(\'' . urlencode($backup['name']) . '\')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>';
                        }, $backups ?? [])) . '
                    </tbody>
                </table>
            </div>
            
            ' . (empty($backups ?? []) ? '<div class="empty-state">
                <i class="fas fa-database fa-3x"></i>
                <h3>No Backups Found</h3>
                <p>Create your first backup using the form above</p>
            </div>' : '') . '
        </div>
    </div>

    <!-- Backup Settings -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cog"></i>
                Backup Settings
            </h3>
        </div>
        <div class="card-content">
            <div class="form-group">
                <label class="form-label">Maximum Backup Size (MB)</label>
                <input type="number" id="max-backup-size" class="form-control" value="1024" min="100" max="10240">
                <small class="form-text">Set the maximum size for backup files (100MB - 10GB)</small>
            </div>
            
            <button id="save-backup-settings" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Settings
            </button>
        </div>
    </div>
    
    <!-- Scheduled Backups -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clock"></i>
                Scheduled Backups
            </h3>
        </div>
        <div class="card-content">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Schedule Frequency</label>
                    <select id="schedule-frequency" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Retention Days</label>
                    <input type="number" id="retention-days" class="form-control" value="7" min="1">
                </div>
            </div>
            
            <button id="schedule-backup" class="btn btn-success">
                <i class="fas fa-calendar-check"></i>
                Schedule Backup
            </button>
        </div>
    </div>
</div>

<script>
let backupInProgress = false;

document.getElementById("create-backup").addEventListener("click", createBackup);
document.getElementById("schedule-backup").addEventListener("click", scheduleBackup);
document.getElementById("save-backup-settings").addEventListener("click", saveBackupSettings);

async function createBackup() {
    if (backupInProgress) return;
    
    backupInProgress = true;
    const createBtn = document.getElementById("create-backup");
    const originalText = createBtn.innerHTML;
    createBtn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Creating...\';
    createBtn.disabled = true;

    try {
        const response = await fetch("' . app_base_url('/admin/backup/create') . '", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                name: document.getElementById("backup-name").value || null,
                include_database: document.getElementById("include-database").checked,
                include_files: document.getElementById("include-files").checked
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showMessage("Backup created successfully: " + result.path, "success");
            refreshBackupList();
        } else {
            showMessage("Error: " + result.message, "error");
        }
    } catch (error) {
        showMessage("Error creating backup: " + error.message, "error");
    } finally {
        backupInProgress = false;
        createBtn.innerHTML = originalText;
        createBtn.disabled = false;
    }
}

async function deleteBackup(backupName) {
    if (!confirm("Are you sure you want to delete this backup? This action cannot be undone.")) {
        return;
    }
    
    try {
        const response = await fetch("' . app_base_url('/admin/backup/delete/') . '" + encodeURIComponent(backupName), {
            method: "POST"
        });

        const result = await response.json();
        
        if (result.success) {
            showMessage("Backup deleted successfully", "success");
            refreshBackupList();
        } else {
            showMessage("Error: " + result.message, "error");
        }
    } catch (error) {
        showMessage("Error deleting backup: " + error.message, "error");
    }
}

async function restoreBackup(backupName) {
    if (!confirm("Are you sure you want to restore from this backup? This will overwrite current data.")) {
        return;
    }
    
    try {
        const response = await fetch("' . app_base_url('/admin/backup/restore/') . '" + encodeURIComponent(backupName), {
            method: "POST"
        });

        const result = await response.json();
        
        if (result.success) {
            showMessage("Backup restored successfully", "success");
        } else {
            showMessage("Error: " + result.message, "error");
        }
    } catch (error) {
        showMessage("Error restoring backup: " + error.message, "error");
    }
}

async function scheduleBackup() {
    try {
        const response = await fetch("' . app_base_url('/admin/backup/schedule') . '", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                schedule: document.getElementById("schedule-frequency").value,
                retention: parseInt(document.getElementById("retention-days").value)
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showMessage("Backup scheduled successfully", "success");
        } else {
            showMessage("Error: " + result.message, "error");
        }
    } catch (error) {
        showMessage("Error scheduling backup: " + error.message, "error");
    }
}

async function saveBackupSettings() {
    try {
        const maxSize = parseInt(document.getElementById("max-backup-size").value);
        
        // Validate input
        if (isNaN(maxSize) || maxSize < 100 || maxSize > 10240) {
            showMessage("Please enter a valid maximum backup size between 100 and 10240 MB", "error");
            return;
        }
        
        // Send settings to the server
        const response = await fetch("' . app_base_url('/admin/backup/settings') . '", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ max_backup_size: maxSize })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, "success");
        } else {
            showMessage("Error: " + result.message, "error");
        }
    } catch (error) {
        showMessage("Error saving backup settings: " + error.message, "error");
    }
}

async function refreshBackupList() {
    try {
        const response = await fetch(window.location.href);
        const html = await response.text();
        
        // Simple refresh - in a real app you would fetch JSON and update the table
        location.reload();
    } catch (error) {
        showMessage("Error refreshing backup list: " + error.message, "error");
    }
}

function showMessage(message, type) {
    // Create a temporary message element
    const messageEl = document.createElement("div");
    messageEl.className = "alert alert-" + type;
    messageEl.textContent = message;
    messageEl.style.cssText = "position:fixed; top:20px; right:20px; padding:15px; border-radius:5px; z-index:9999;";
    
    if (type === "success") {
        messageEl.style.backgroundColor = "#d4edda";
        messageEl.style.color = "#155724";
        messageEl.style.border = "1px solid #c3e6cb";
    } else {
        messageEl.style.backgroundColor = "#f8d7da";
        messageEl.style.color = "#721c24";
        messageEl.style.border = "1px solid #f5c6cb";
    }
    
    document.body.appendChild(messageEl);
    
    // Remove after 5 seconds
    setTimeout(() => {
        document.body.removeChild(messageEl);
    }, 5000);
}
</script>

<style>
.backup-options {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.checkbox-group {
    display: flex;
    gap: 20px;
    align-items: center;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--admin-gray-500);
}

.empty-state i {
    margin-bottom: 16px;
    opacity: 0.5;
}

.alert {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Backup Management']
];

$page_title = $page_title ?? 'Backup Management - Admin Panel';
$currentPage = $currentPage ?? 'backup';

// Include the layout
include __DIR__ . '/../../layouts/main.php';
?>