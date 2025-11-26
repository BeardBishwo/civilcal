<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
        <div>
            <h1 class="page-title">Plugin Management</h1>
            <p class="page-description">Extend functionality with plugins and manage their status.</p>
        </div>
        <div>
            <button id="uploadTrigger" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--admin-gray-200);border-radius:10px;background:var(--admin-white);cursor:pointer;font-weight:600;color:var(--admin-gray-800);">
                <i class="fas fa-upload" style="color:var(--admin-primary);"></i>
                <span>Upload Plugin</span>
            </button>
            <input type="file" id="pluginUpload" style="display:none" accept=".zip">
        </div>
    </div>
</div>

<?php if (!empty($plugins)): ?>
<div class="stats-grid">
    <?php foreach ($plugins as $plugin): ?>
        <div class="card">
            <div class="card-content">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="stat-icon info" style="font-size:18px;width:44px;height:44px;">
                            <i class="fas fa-plug"></i>
                        </div>
                        <div>
                            <div class="card-title" style="margin:0;gap:8px;">
                                <span><?= htmlspecialchars($plugin['name'] ?? 'Unknown') ?></span>
                            </div>
                            <div style="color:var(--admin-gray-600);font-size:12px;">
                                <?= htmlspecialchars($plugin['slug'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($plugin['is_active'])): ?>
                            <span style="display:inline-block;padding:6px 10px;border-radius:20px;background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);font-size:12px;">Active</span>
                        <?php else: ?>
                            <span style="display:inline-block;padding:6px 10px;border-radius:20px;background:rgba(107,114,128,0.08);color:#6b7280;border:1px solid rgba(107,114,128,0.2);font-size:12px;">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="margin-top:12px;color:var(--admin-gray-700);font-size:14px;line-height:1.5;">
                    <?= htmlspecialchars($plugin['description'] ?? '') ?>
                </div>
                <div style="margin-top:12px;color:var(--admin-gray-600);font-size:13px;">
                    Version: <?= htmlspecialchars($plugin['version'] ?? '1.0.0') ?>
                </div>
            </div>
            <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php if (!empty($plugin['is_active'])): ?>
                        <button class="toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="disable" style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(245,158,11,0.3);border-radius:10px;background:rgba(245,158,11,0.08);color:#b45309;cursor:pointer;">
                            <i class="fas fa-power-off"></i>
                            <span>Disable</span>
                        </button>
                    <?php else: ?>
                        <button class="toggle-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" data-action="enable" style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(16,185,129,0.3);border-radius:10px;background:rgba(16,185,129,0.08);color:#065f46;cursor:pointer;">
                            <i class="fas fa-play"></i>
                            <span>Enable</span>
                        </button>
                    <?php endif; ?>
                    <button class="delete-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>" style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(239,68,68,0.3);border-radius:10px;background:rgba(239,68,68,0.08);color:#991b1b;cursor:pointer;">
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-content" style="text-align:center;">
        <i class="fas fa-plug" style="font-size:36px;color:var(--admin-primary);"></i>
        <div style="margin-top:12px;color:var(--admin-gray-700);">No plugins installed.</div>
    </div>
    <div class="card-footer" style="text-align:center;">
        <button id="emptyUploadTrigger" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid var(--admin-gray-200);border-radius:10px;background:var(--admin-white);cursor:pointer;font-weight:600;color:var(--admin-gray-800);">
            <i class="fas fa-upload" style="color:var(--admin-primary);"></i>
            <span>Upload Plugin</span>
        </button>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var trigger = document.getElementById('uploadTrigger');
    var emptyTrigger = document.getElementById('emptyUploadTrigger');
    var uploadInput = document.getElementById('pluginUpload');
    if (trigger) trigger.addEventListener('click', function(){ uploadInput.click(); });
    if (emptyTrigger) emptyTrigger.addEventListener('click', function(){ uploadInput.click(); });

    uploadInput.addEventListener('change', function() {
        if (this.files.length === 0) return;
        var formData = new FormData();
        formData.append('plugin_zip', this.files[0]);
        var btn = trigger || emptyTrigger;
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        fetch('/admin/plugins/upload', { method: 'POST', body: formData })
            .then(function(r){ return r.json(); })
            .then(function(data){ if (data.success) { location.reload(); } else { alert('Error: ' + data.message); } })
            .catch(function(){ alert('Upload failed'); })
            .finally(function(){ btn.disabled = false; btn.innerHTML = originalText; uploadInput.value = ''; });
    });

    document.querySelectorAll('.toggle-plugin').forEach(function(btn){
        btn.addEventListener('click', function(){
            var slug = this.dataset.slug;
            var action = this.dataset.action;
            this.disabled = true;
            var formData = new FormData();
            formData.append('plugin', slug);
            formData.append('action', action);
            fetch('/admin/plugins/toggle', { method: 'POST', body: formData })
                .then(function(r){ return r.json(); })
                .then(function(data){ if (data.success) { location.reload(); } else { alert('Error: ' + data.message); btn.disabled = false; } })
                .catch(function(){ alert('Action failed'); btn.disabled = false; });
        });
    });

    document.querySelectorAll('.delete-plugin').forEach(function(btn){
        btn.addEventListener('click', function(){
            if (!confirm('Are you sure you want to delete this plugin?')) return;
            var slug = this.dataset.slug;
            this.disabled = true;
            fetch('/admin/plugins/' + slug + '/delete', { method: 'POST' })
                .then(function(r){ return r.json(); })
                .then(function(data){ if (data.success) { location.reload(); } else { alert('Error: ' + data.message); btn.disabled = false; } })
                .catch(function(){ alert('Delete failed'); btn.disabled = false; });
        });
    });
});
</script>
