<?php
// Plugins Management Interface - Restyled to match Pages UI
$page_title = 'Plugin Management - Admin Panel';
$currentPage = 'plugins';
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-plug"></i>
                    <h1>Plugins</h1>
                </div>
                <div class="header-subtitle">Manage and extend functionality</div>
            </div>
            <div class="header-actions">
                <button onclick="document.getElementById('pluginUpload').click()" class="btn btn-primary btn-compact">
                    <i class="fas fa-upload"></i>
                    <span>Upload Plugin</span>
                </button>
                <input type="file" id="pluginUpload" style="display:none" accept=".zip">
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <?php
        $totalPlugins = count($plugins ?? []);
        $activeCount = count($activePlugins ?? []);
        $inactiveCount = $totalPlugins - $activeCount;
        ?>
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-plug"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalPlugins; ?></div>
                    <div class="stat-label">Total Plugins</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeCount; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveCount; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
        </div>

        <!-- Upload Progress Area -->
        <div id="uploadProgress" style="display:none; padding: 1rem 2rem; border-bottom: 1px solid var(--admin-gray-200);">
            <div style="height: 10px; background: var(--admin-gray-200); border-radius: 6px; overflow: hidden;">
                <div id="uploadBar" style="height: 10px; width: 0%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
            </div>
            <div id="uploadPercent" style="margin-top: 6px; font-size: 12px; color: var(--admin-gray-600); text-align: center;">0%</div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search plugins..." id="pluginSearch">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="pluginStatusFilter" class="filter-compact">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="toolbar-right">
                <!-- Additional tools if needed -->
            </div>
        </div>

        <!-- Plugins Content Area -->
        <div class="pages-content">
            <div class="table-container">
                <?php if (empty($plugins)): ?>
                    <div class="empty-state-compact">
                        <i class="fas fa-plug"></i>
                        <h3>No plugins installed</h3>
                        <p>Upload a plugin ZIP to get started</p>
                        <button onclick="document.getElementById('pluginUpload').click()" class="btn btn-primary">
                            <i class="fas fa-upload"></i>
                            Upload Plugin
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th class="col-title">Plugin Name</th>
                                    <th class="col-status">Version</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-description">Description</th>
                                    <th class="col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plugins as $plugin): ?>
                                    <tr class="plugin-row">
                                        <td>
                                            <div class="page-info">
                                                <div class="page-title-compact plugin-name"><?php echo htmlspecialchars($plugin['name'] ?? 'Unknown'); ?></div>
                                                <div class="page-slug-compact plugin-slug"><?php echo htmlspecialchars($plugin['slug'] ?? ''); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="time-compact"><?php echo htmlspecialchars($plugin['version'] ?? '1.0.0'); ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($plugin['is_active'])): ?>
                                                <span class="status-badge status-active plugin-status-badge">active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive plugin-status-badge">inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="time-compact">
                                                <?php echo htmlspecialchars($plugin['description'] ?? ''); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="actions-compact">
                                                <button class="action-btn-icon info-btn view-plugin" data-slug="<?php echo htmlspecialchars($plugin['slug']); ?>" title="Details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <?php if (!empty($plugin['is_active'])): ?>
                                                    <button class="action-btn-icon warning-btn toggle-plugin" 
                                                            data-slug="<?php echo htmlspecialchars($plugin['slug']); ?>" 
                                                            data-action="disable"
                                                            title="Disable">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="action-btn-icon primary-btn toggle-plugin" 
                                                            data-slug="<?php echo htmlspecialchars($plugin['slug']); ?>" 
                                                            data-action="enable" 
                                                            title="Enable">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <button class="action-btn-icon delete-btn delete-plugin" 
                                                        data-slug="<?php echo htmlspecialchars($plugin['slug']); ?>" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = meta ? meta.getAttribute('content') : '';
    
    // Search functionality
    const searchInput = document.getElementById('pluginSearch');
    const searchClear = document.getElementById('search-clear');
    const filterSelect = document.getElementById('pluginStatusFilter');

    function applyFilter() {
        var q = (searchInput.value || '').toLowerCase();
        var st = filterSelect.value;
        
        document.querySelectorAll('.plugin-row').forEach(function(row) {
            var name = row.querySelector('.plugin-name')?.textContent.toLowerCase() || '';
            var slug = row.querySelector('.plugin-slug')?.textContent.toLowerCase() || '';
            var statusText = row.querySelector('.plugin-status-badge')?.textContent.toLowerCase() || '';
            
            var matchText = !q || name.includes(q) || slug.includes(q);
            var matchStatus = st === 'all' || (statusText.includes(st));
            
            row.style.display = (matchText && matchStatus) ? '' : 'none';
        });

        searchClear.style.display = q ? 'block' : 'none';
    }

    searchInput.addEventListener('input', applyFilter);
    filterSelect.addEventListener('change', applyFilter);
    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        applyFilter();
    });

    // Upload Plugin
    document.getElementById('pluginUpload').addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('plugin_zip', this.files[0]);
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo app_base_url('admin/plugins/upload'); ?>', true);
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
                document.getElementById('pluginUpload').value = '';
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        showNotification('Plugin uploaded successfully', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        progress.style.display = 'none';
                        showNotification('Error: ' + (data.message || 'Upload failed'), 'error');
                    }
                } catch(err){
                    progress.style.display = 'none';
                    showNotification('Upload failed', 'error');
                }
            }
        };
        xhr.send(formData);
    });
    
    // Toggle Plugin
    document.querySelectorAll('.toggle-plugin').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.slug;
            const action = this.dataset.action;
            const icon = this.querySelector('i');
            const originalClass = icon.className;
            
            icon.className = 'fas fa-spinner fa-spin';
            this.disabled = true;
            
            const formData = new FormData();
            formData.append('plugin', slug);
            formData.append('action', action);
            
            fetch('<?php echo app_base_url('admin/plugins/toggle'); ?>', {
                method: 'POST',
                headers: {'X-CSRF-Token': csrfToken},
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Plugin ' + action + 'd successfully', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification('Error: ' + (data.message || 'Action failed'), 'error');
                    icon.className = originalClass;
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Action failed', 'error');
                icon.className = originalClass;
                this.disabled = false;
            });
        });
    });
    
    // Delete Plugin
    // Confirm Delete Plugin
    document.querySelectorAll('.delete-plugin').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.slug;
            showConfirmModal('Delete Plugin', 'Are you sure you want to delete this plugin? This cannot be undone.', () => {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-spinner fa-spin';
                this.disabled = true;
                
                fetch('<?php echo app_base_url('admin/plugins/delete'); ?>/' + encodeURIComponent(slug), {
                    method: 'POST',
                    headers: {'X-CSRF-Token': csrfToken}
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Plugin deleted successfully', 'success');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        showNotification('Error: ' + (data.message || 'Delete failed'), 'error');
                        icon.className = 'fas fa-trash';
                        this.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Delete failed', 'error');
                    icon.className = 'fas fa-trash';
                    this.disabled = false;
                });
            });
        });
    });

    // View Details
    document.querySelectorAll('.view-plugin').forEach(btn => {
        btn.addEventListener('click', function(){
            var slug = this.dataset.slug;
            fetch('<?php echo app_base_url('admin/plugins/details'); ?>/' + encodeURIComponent(slug), {
                method: 'GET',
                headers: {'Accept':'application/json'}
            })
            .then(r=>r.json())
            .then(data=>{ if(data.success){ openDetailsModal(data.data); } else { showNotification('Details failed', 'error'); } })
            .catch(()=>{ showNotification('Details failed', 'error'); });
        });
    });
});

function openDetailsModal(data){
    var overlay = document.createElement('div');
    overlay.className = 'premium-modal-overlay';
    
    var modal = document.createElement('div');
    modal.className = 'premium-modal';
    
    var header = document.createElement('div');
    header.className = 'premium-modal-header';
    header.innerHTML = `
        <div class="premium-modal-icon"><i class="fas fa-puzzle-piece"></i></div>
        <h3>Plugin Details</h3>
    `;
    
    var body = document.createElement('div');
    body.className = 'premium-modal-body';
    body.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div><div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Name</div><div style="font-weight:600;">${escapeHtml(data.name||'')}</div></div>
            <div><div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Version</div><div style="font-weight:600;">${escapeHtml(data.version||'')}</div></div>
            <div style="grid-column:1 / -1"><div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Description</div><div>${escapeHtml(data.description||'')}</div></div>
            <div><div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Slug</div><code style="background:#f3f4f6; padding:2px 6px; border-radius:4px;">${escapeHtml(data.slug||'')}</code></div>
            <div><div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Author</div><div>${escapeHtml(data.author||'')}</div></div>
        </div>
    `;
    
    var footer = document.createElement('div');
    footer.className = 'premium-modal-footer';
    
    var closeBtn = document.createElement('button');
    closeBtn.className = 'btn-cancel';
    closeBtn.innerHTML = 'Close';
    closeBtn.onclick = function(){ overlay.remove(); };
    
    footer.appendChild(closeBtn);
    modal.appendChild(header);
    modal.appendChild(body);
    modal.appendChild(footer);
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g,function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]); }); }
</script>
