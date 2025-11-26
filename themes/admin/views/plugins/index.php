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
        <div id="uploadDropZone" style="border: 2px dashed var(--admin-border); border-radius: 12px; padding: 24px; display: flex; align-items: center; justify-content: center; gap: 12px; margin: 16px 0; background: var(--admin-gray-50);">
            <i class="fas fa-file-archive" style="color: var(--admin-primary);"></i>
            <span>Drag & drop plugin ZIP here or click Upload Plugin</span>
        </div>
        <div id="uploadProgress" style="display:none; margin-top: 8px;">
            <div style="height: 10px; background: var(--admin-gray-200); border-radius: 6px; overflow: hidden;">
                <div id="uploadBar" style="height: 10px; width: 0%; background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-dark));"></div>
            </div>
            <div id="uploadPercent" style="margin-top: 6px; font-size: 12px; color: var(--admin-gray-600);">0%</div>
        </div>
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
        <div style="display:flex; gap:12px; align-items:center; margin: 12px 0;">
            <input id="pluginSearch" class="form-control" placeholder="Search plugins by name or slug" style="max-width: 320px;">
            <select id="pluginStatusFilter" class="form-control" style="max-width: 200px;">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
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
                                        <td style="text-align:right; display:flex; gap:8px; justify-content:flex-end;">
                                            <button class="btn btn-secondary btn-sm view-plugin" data-slug="<?= htmlspecialchars($plugin['slug']) ?>">
                                                <i class="fas fa-info-circle"></i> Details
                                            </button>
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
    var meta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = meta ? meta.getAttribute('content') : '';
    // Upload Plugin
    document.getElementById('pluginUpload').addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('plugin_zip', this.files[0]);
        
        const btn = document.querySelector('button[onclick*="pluginUpload"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= app_base_url('admin/plugins/upload') ?>', true);
        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        var progress = document.getElementById('uploadProgress');
        var bar = document.getElementById('uploadBar');
        var pct = document.getElementById('uploadPercent');
        progress.style.display = 'block';
        xhr.upload.onprogress = function(e){
            if(e.lengthComputable){
                var p = Math.round((e.loaded/e.total)*100);
                bar.style.width = p+'%';
                pct.textContent = p+'%';
            }
        };
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4){
                btn.disabled = false;
                btn.innerHTML = originalText;
                document.getElementById('pluginUpload').value = '';
                progress.style.display = 'none';
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        showToast('Plugin uploaded successfully', 'success');
                        location.reload();
                    } else {
                        showToast('Error: ' + (data.message || 'Upload failed'), 'error');
                    }
                } catch(err){
                    showToast('Upload failed', 'error');
                }
            }
        };
        xhr.send(formData);
    });
    var drop = document.getElementById('uploadDropZone');
    drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.style.background = 'var(--admin-gray-100)'; });
    drop.addEventListener('dragleave', function(e){ drop.style.background = 'var(--admin-gray-50)'; });
    drop.addEventListener('drop', function(e){ e.preventDefault(); drop.style.background = 'var(--admin-gray-50)'; var files = e.dataTransfer.files; if (!files || files.length===0) return; var file = files[0]; if (!/\.zip$/i.test(file.name)) { showToast('Please drop a .zip file', 'error'); return; } var input = document.getElementById('pluginUpload'); input.files = files; var event = new Event('change'); input.dispatchEvent(event); });
    
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
                headers: {'X-CSRF-Token': csrfToken},
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
            
                fetch('<?= app_base_url('admin/plugins/delete') ?>/' + encodeURIComponent(slug), {
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

    document.querySelectorAll('.view-plugin').forEach(btn => {
        btn.addEventListener('click', function(){
            var slug = this.dataset.slug;
            fetch('<?= app_base_url('admin/plugins/details') ?>/' + encodeURIComponent(slug), {
                method: 'GET',
                headers: {'Accept':'application/json'}
            })
            .then(r=>r.json())
            .then(data=>{ if(data.success){ openDetailsModal(data.data); } else { showToast('Details failed', 'error'); } })
            .catch(()=>{ showToast('Details failed', 'error'); });
        });
    });

    var search = document.getElementById('pluginSearch');
    var status = document.getElementById('pluginStatusFilter');
    function applyFilter(){
        var q = (search.value||'').toLowerCase();
        var st = status.value;
        document.querySelectorAll('table.table tbody tr').forEach(function(row){
            var name = row.querySelector('td:nth-child(1) .font-weight-bold')?.textContent.toLowerCase() || '';
            var slug = row.querySelector('td:nth-child(1) small')?.textContent.toLowerCase() || '';
            var isActive = row.querySelector('.status-online') !== null;
            var matchText = !q || name.includes(q) || slug.includes(q);
            var matchStatus = st==='all' || (st==='active' && isActive) || (st==='inactive' && !isActive);
            row.style.display = (matchText && matchStatus) ? '' : 'none';
        });
    }
    search.addEventListener('input', applyFilter);
    status.addEventListener('change', applyFilter);
    applyFilter();
});

function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'notification-toast ' + (type === 'success' ? 'success' : 'error') + ' show';
    toast.innerHTML = '<div style="display:flex; align-items:center; gap:10px;"><i class="fas ' + (type==='success'?'fa-check-circle':'fa-times-circle') + '"></i><span>'+message+'</span></div>';
    document.body.appendChild(toast);
    setTimeout(function(){ toast.classList.remove('show'); setTimeout(function(){ document.body.removeChild(toast); }, 300); }, 2500);
}

function openDetailsModal(data){
    var overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.right = '0';
    overlay.style.bottom = '0';
    overlay.style.background = 'rgba(0,0,0,0.5)';
    overlay.style.zIndex = '4000';
    var modal = document.createElement('div');
    modal.className = 'card';
    modal.style.maxWidth = '640px';
    modal.style.margin = '80px auto';
    var header = document.createElement('div');
    header.className = 'card-header';
    var title = document.createElement('div');
    title.className = 'card-title';
    title.innerHTML = '<i class="fas fa-info-circle"></i> Plugin Details';
    var closeBtn = document.createElement('button');
    closeBtn.className = 'btn btn-secondary';
    closeBtn.textContent = 'Close';
    closeBtn.onclick = function(){ document.body.removeChild(overlay); };
    header.appendChild(title);
    header.appendChild(closeBtn);
    var content = document.createElement('div');
    content.className = 'card-content';
    var html = '<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">';
    html += '<div><div class="form-label">Name</div><div>'+escapeHtml(data.name||'')+'</div></div>';
    html += '<div><div class="form-label">Version</div><div>'+escapeHtml(data.version||'')+'</div></div>';
    html += '<div style="grid-column:1 / -1"><div class="form-label">Description</div><div>'+escapeHtml(data.description||'')+'</div></div>';
    html += '<div><div class="form-label">Slug</div><div>'+escapeHtml(data.slug||'')+'</div></div>';
    html += '<div><div class="form-label">Author</div><div>'+escapeHtml(data.author||'')+'</div></div>';
    html += '</div>';
    content.innerHTML = html;
    modal.appendChild(header);
    modal.appendChild(content);
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g,function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]); }); }
</script>
