<div class="card">
    <div class="card-header">
        <h1 class="card-title"><i class="fas fa-plug"></i> Plugin Management</h1>
        <div>
            <button class="btn btn-primary" onclick="document.getElementById('pluginUpload').click()">
                <i class="fas fa-upload"></i> Upload Plugin
            </button>
            <input type="file" id="pluginUpload" style="display:none" accept=".zip">
        </div>
    </div>
    <div class="card-content">
        <p class="page-description">Extend functionality with plugins. Manage activation, deletion, and uploads.</p>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon success"><i class="fas fa-check"></i></div>
                    <div class="stat-change positive"><i class="fas fa-arrow-up"></i> Active</div>
                </div>
                <div class="stat-value"><?= isset($activePlugins) ? count($activePlugins) : 0 ?></div>
                <div class="stat-label">Active Plugins</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon info"><i class="fas fa-list"></i></div>
                    <div class="stat-change"><i class="fas fa-layer-group"></i></div>
                </div>
                <div class="stat-value"><?= isset($plugins) ? count($plugins) : 0 ?></div>
                <div class="stat-label">Total Plugins</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon warning"><i class="fas fa-file-archive"></i></div>
                    <div class="stat-change"><i class="fas fa-upload"></i></div>
                </div>
                <div class="stat-value">ZIP</div>
                <div class="stat-label">Install via Upload</div>
            </div>
        </div>
    </div>
</div>

<div class="table-container" style="margin-top: 20px;">
    <table class="table">
                        <thead>
                            <tr>
                                <th>Plugin Name</th>
                                <th>Version</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($plugins)): ?>
                                <?php foreach ($plugins as $plugin): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold"><?= htmlspecialchars($plugin['name'] ?? 'Unknown') ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($plugin['slug'] ?? '') ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($plugin['version'] ?? '1.0.0') ?></td>
                                        <td><?= htmlspecialchars($plugin['description'] ?? '') ?></td>
                                        <td>
                                            <?php if (!empty($plugin['is_active'])): ?>
                                                <span class="status-badge status-online">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-offline">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align:right;">
                                            <?php if (!empty($plugin['is_active'])): ?>
                                                <button class="btn btn-secondary btn-sm toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="disable">
                                                    <i class="fas fa-power-off"></i> Disable
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-primary btn-sm toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="enable">
                                                    <i class="fas fa-play"></i> Enable
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-danger btn-sm delete-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 32px; color: var(--admin-gray-600);">
                                        <i class="fas fa-plug fa-3x mb-3"></i>
                                        <p>No plugins installed.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload Plugin
    document.getElementById('pluginUpload').addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('plugin_zip', this.files[0]);
        
        const btn = document.querySelector('button[onclick*="pluginUpload"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        fetch('<?= app_base_url('admin/plugins/upload') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Plugin uploaded successfully', 'success');
                location.reload();
            } else {
                showToast('Error: ' + (data.message || 'Upload failed'), 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Upload failed', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            this.value = '';
        });
    });
    
    // Toggle Plugin
    document.querySelectorAll('.toggle-plugin').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.slug;
            const action = this.dataset.action;
            
            this.disabled = true;
            
            const formData = new FormData();
            formData.append('plugin', slug);
            formData.append('action', action);
            
            fetch('<?= app_base_url('admin/plugins/toggle') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Plugin ' + action + 'd', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + (data.message || 'Action failed'), 'error');
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Action failed', 'error');
                this.disabled = false;
            });
        });
    });
    
    // Delete Plugin
    document.querySelectorAll('.delete-plugin').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete this plugin?')) return;
            
            const slug = this.dataset.slug;
            this.disabled = true;
            
            fetch('<?= app_base_url('admin/plugins') ?>/' + encodeURIComponent(slug) + '/delete', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Plugin deleted', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + (data.message || 'Delete failed'), 'error');
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Delete failed', 'error');
                this.disabled = false;
            });
        });
    });
});

function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'notification-toast ' + (type === 'success' ? 'success' : 'error') + ' show';
    toast.innerHTML = '<div style="display:flex; align-items:center; gap:10px;"><i class="fas ' + (type==='success'?'fa-check-circle':'fa-times-circle') + '"></i><span>'+message+'</span></div>';
    document.body.appendChild(toast);
    setTimeout(function(){ toast.classList.remove('show'); setTimeout(function(){ document.body.removeChild(toast); }, 300); }, 2500);
}
</script>
