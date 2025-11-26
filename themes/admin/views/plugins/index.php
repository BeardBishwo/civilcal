<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Plugin Management</h1>
                <p class="text-muted">Extend functionality with plugins.</p>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('pluginUpload').click()">
                <i class="fas fa-upload"></i> Upload Plugin
            </button>
            <input type="file" id="pluginUpload" style="display: none" accept=".zip">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Plugin Name</th>
                                <th>Version</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
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
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if (!empty($plugin['is_active'])): ?>
                                                <button class="btn btn-sm btn-warning toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="disable">
                                                    <i class="fas fa-power-off"></i> Disable
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-success toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="enable">
                                                    <i class="fas fa-play"></i> Enable
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-danger delete-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center p-5 text-muted">
                                        <i class="fas fa-plug fa-3x mb-3"></i>
                                        <p>No plugins installed.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload Plugin
    document.getElementById('pluginUpload').addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('plugin_zip', this.files[0]);
        
        // Show loading
        const btn = document.querySelector('button[onclick*="pluginUpload"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        fetch('/admin/plugins/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Plugin uploaded successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Upload failed');
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
            
            fetch('/admin/plugins/toggle', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Action failed');
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
            
            fetch('/admin/plugins/' + slug + '/delete', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Delete failed');
                this.disabled = false;
            });
        });
    });
});
</script>
