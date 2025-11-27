<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-palette"></i> Theme Management</h1>
            <p class="page-description">Manage and customize the look and feel. Upload, activate, preview and delete themes.</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-primary" onclick="document.getElementById('themeUpload').click()">
                <i class="fas fa-upload"></i> Upload Theme
            </button>
            <input type="file" id="themeUpload" style="display:none" accept=".zip">
        </div>
    </div>
</div>

<!-- Theme Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-palette"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
        <div class="stat-label">Total Themes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
        <div class="stat-label">Active Themes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-pause-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo ($stats['total'] ?? 0) - ($stats['active'] ?? 0); ?></div>
        <div class="stat-label">Inactive Themes</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['updates'] ?? 0; ?></div>
        <div class="stat-label">Updates Available</div>
    </div>
</div>

<div class="card">
    <div class="card-content">
        <div id="uploadDropZone" class="upload-drop-zone">
            <i class="fas fa-file-archive"></i>
            <span>Drag & drop theme ZIP here or click Upload Theme</span>
        </div>
        <div id="uploadProgress" class="upload-progress" style="display:none;">
            <div class="progress-bar">
                <div id="uploadBar" class="progress-fill"></div>
            </div>
            <div id="uploadPercent" class="progress-percent">0%</div>
        </div>
        <div class="toolbar">
            <div class="toolbar-left">
                <input id="themeSearch" class="form-control" placeholder="Search themes by name">
            </div>
            <div class="toolbar-right">
                <select id="themeStatusFilter" class="form-control">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="themes-grid">
<?php if (!empty($themes)): ?>
    <?php foreach ($themes as $theme): ?>
        <div class="theme-card" data-theme="<?php echo htmlspecialchars($theme['slug'] ?? $theme['id']); ?>" data-status="<?php echo ($theme['is_active'] ?? false) ? 'active' : 'inactive'; ?>">
            <div class="theme-preview">
                <?php if (!empty($theme['screenshot'])): ?>
                    <img src="<?php echo htmlspecialchars($theme['screenshot']); ?>" alt="<?php echo htmlspecialchars($theme['name']); ?>" class="theme-screenshot">
                <?php else: ?>
                    <div class="theme-placeholder">
                        <i class="fas fa-palette fa-3x"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="theme-content">
                <div class="theme-header">
                    <h3 class="theme-title"><?php echo htmlspecialchars($theme['name']); ?></h3>
                    <?php if ($theme['is_active'] ?? false): ?>
                        <span class="status-badge status-active">Active</span>
                    <?php else: ?>
                        <span class="status-badge status-inactive">Inactive</span>
                    <?php endif; ?>
                </div>
                <p class="theme-meta">
                    v<?php echo htmlspecialchars($theme['version'] ?? '1.0.0'); ?> by <?php echo htmlspecialchars($theme['author'] ?? 'Unknown'); ?>
                </p>
                <p class="theme-description"><?php echo htmlspecialchars($theme['description'] ?? 'No description available'); ?></p>
                <div class="theme-actions">
                    <button class="btn btn-secondary btn-sm view-theme" data-slug="<?php echo htmlspecialchars($theme['slug'] ?? $theme['id']); ?>">
                        <i class="fas fa-info-circle"></i> Details
                    </button>
                    <a href="<?php echo app_base_url('/admin/themes/' . ($theme['id'] ?? '') . '/preview'); ?>" class="btn btn-secondary btn-sm" target="_blank">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                    <?php if (!($theme['is_active'] ?? false)): ?>
                        <button class="btn btn-primary btn-sm activate-theme" data-id="<?php echo $theme['id'] ?? ''; ?>">
                            <i class="fas fa-check"></i> Activate
                        </button>
                        <button class="btn btn-danger btn-sm delete-theme" data-id="<?php echo $theme['id'] ?? ''; ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm deactivate-theme" data-id="<?php echo $theme['id'] ?? ''; ?>">
                            <i class="fas fa-stop"></i> Deactivate
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-palette"></i>
        </div>
        <h3>No Themes Found</h3>
        <p>No themes are currently available. Upload a theme to get started.</p>
        <button class="btn btn-primary" onclick="document.getElementById('themeUpload').click()">
            <i class="fas fa-upload"></i> Upload Your First Theme
        </button>
    </div>
<?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = meta ? meta.getAttribute('content') : '';

        // Activate Theme
        document.querySelectorAll('.activate-theme').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Activating...';

                const formData = new FormData();
                formData.append('theme_id', id);

                fetch('<?php echo app_base_url('/admin/themes/activate'); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Theme activated', 'success');
                            location.reload();
                        } else {
                            showToast('Error: ' + (data.message || 'Activation failed'), 'error');
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-check"></i> Activate';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Activation failed', 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-check"></i> Activate';
                    });
            });
        });

        // Deactivate Theme
        document.querySelectorAll('.deactivate-theme').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deactivating...';

                const formData = new FormData();
                formData.append('theme_id', id);

                fetch('<?php echo app_base_url('/admin/themes/deactivate'); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Theme deactivated', 'success');
                            location.reload();
                        } else {
                            showToast('Error: ' + (data.message || 'Deactivation failed'), 'error');
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-stop"></i> Deactivate';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Deactivation failed', 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-stop"></i> Deactivate';
                    });
            });
        });

        // Upload Theme with progress
        document.getElementById('themeUpload').addEventListener('change', function(e) {
            if (this.files.length === 0) return;
            const formData = new FormData();
            formData.append('theme_zip', this.files[0]);
            const btn = document.querySelector('button[onclick*="themeUpload"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo app_base_url('/admin/themes/upload'); ?>', true);
            xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            var progress = document.getElementById('uploadProgress');
            var bar = document.getElementById('uploadBar');
            var pct = document.getElementById('uploadPercent');
            progress.style.display = 'block';
            xhr.upload.onprogress = function(e){ if(e.lengthComputable){ var p = Math.round((e.loaded/e.total)*100); bar.style.width = p+'%'; pct.textContent = p+'%'; } };
            xhr.onreadystatechange = function(){ if(xhr.readyState===4){ btn.disabled=false; btn.innerHTML=originalText; document.getElementById('themeUpload').value=''; progress.style.display='none'; try{ var data=JSON.parse(xhr.responseText); if(data.success){ showToast('Theme uploaded successfully', 'success'); location.reload(); } else { showToast('Error: ' + (data.message||'Upload failed'), 'error'); } } catch(err){ showToast('Upload failed', 'error'); } } };
            xhr.send(formData);
        });
        var drop = document.getElementById('uploadDropZone');
        drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.style.background='var(--admin-gray-100)'; });
        drop.addEventListener('dragleave', function(){ drop.style.background='var(--admin-gray-50)'; });
        drop.addEventListener('drop', function(e){ e.preventDefault(); drop.style.background='var(--admin-gray-50)'; var files=e.dataTransfer.files; if(!files||files.length===0) return; var file=files[0]; if(!/\.zip$/i.test(file.name)){ showToast('Please drop a .zip file', 'error'); return; } var input=document.getElementById('themeUpload'); input.files=files; var event=new Event('change'); input.dispatchEvent(event); });

        // Delete Theme
        document.querySelectorAll('.delete-theme').forEach(btn => {
            btn.addEventListener('click', function(){
                if(!confirm('Delete this theme?')) return;
                const id = this.dataset.id;
                this.disabled = true;
                const formData = new FormData();
                formData.append('theme_id', id);
                fetch('<?php echo app_base_url('/admin/themes/delete'); ?>', {
                    method: 'POST',
                    headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-Token': csrfToken},
                    body: formData
                }).then(r=>r.json()).then(data=>{
                    if(data.success){ showToast('Theme deleted', 'success'); location.reload(); } else { showToast('Error: ' + (data.message||'Delete failed'), 'error'); this.disabled = false; }
                }).catch(()=>{ showToast('Delete failed', 'error'); this.disabled=false; });
            });
        });

        // Details modal
        document.querySelectorAll('.view-theme').forEach(btn => {
            btn.addEventListener('click', function(){
                var key = this.dataset.slug;
                fetch('<?php echo app_base_url('/admin/themes/details'); ?>/' + encodeURIComponent(key), { method:'GET', headers:{'Accept':'application/json'} })
                .then(r=>r.json()).then(data=>{ if(data.success){ openDetailsModal(data.data); } else { showToast('Details failed', 'error'); } })
                .catch(()=>{ showToast('Details failed', 'error'); });
            });
        });

        // Search/filter
        var search = document.getElementById('themeSearch');
        var status = document.getElementById('themeStatusFilter');
        function applyFilter(){
            var q = (search.value||'').toLowerCase();
            var st = status.value;
            document.querySelectorAll('.themes-grid .theme-card').forEach(function(card){
                var name = card.querySelector('.theme-title')?.textContent.toLowerCase() || '';
                var isActive = card.querySelector('.status-active') !== null;
                var matchText = !q || name.includes(q);
                var matchStatus = st==='all' || (st==='active' && isActive) || (st==='inactive' && !isActive);
                card.style.display = (matchText && matchStatus) ? '' : 'none';
            });
        }
        search.addEventListener('input', applyFilter);
        status.addEventListener('change', applyFilter);
        applyFilter();
    });
</script>

<script>
function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'notification-toast ' + (type === 'success' ? 'success' : 'error') + ' show';
    toast.innerHTML = '<div class="toast-content"><i class="fas ' + (type==='success'?'fa-check-circle':'fa-times-circle') + '"></i><span>'+message+'</span></div>';
    document.body.appendChild(toast);
    setTimeout(function(){ toast.classList.remove('show'); setTimeout(function(){ document.body.removeChild(toast); }, 300); }, 2500);
}

function openDetailsModal(data){
    var overlay = document.createElement('div'); 
    overlay.className = 'modal-overlay';
    
    var modal = document.createElement('div'); 
    modal.className = 'modal-card';
    
    var header = document.createElement('div'); 
    header.className = 'modal-header';
    
    var title = document.createElement('div'); 
    title.className = 'modal-title';
    title.innerHTML = '<i class="fas fa-info-circle"></i> Theme Details';
    
    var closeBtn = document.createElement('button'); 
    closeBtn.className = 'btn btn-icon';
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    closeBtn.onclick = function(){ document.body.removeChild(overlay); };
    
    header.appendChild(title); 
    header.appendChild(closeBtn);
    
    var content = document.createElement('div'); 
    content.className = 'modal-content';
    
    var html = '<div class="theme-details-grid">';
    html += '<div class="detail-item"><div class="detail-label">Name</div><div class="detail-value">'+escapeHtml(data.name||'')+'</div></div>';
    html += '<div class="detail-item"><div class="detail-label">Version</div><div class="detail-value">'+escapeHtml(data.version||'')+'</div></div>';
    html += '<div class="detail-item"><div class="detail-label">Author</div><div class="detail-value">'+escapeHtml(data.author||'')+'</div></div>';
    html += '<div class="detail-item"><div class="detail-label">Description</div><div class="detail-value">'+escapeHtml(data.description||'')+'</div></div>';
    html += '</div>';
    
    content.innerHTML = html;
    modal.appendChild(header); 
    modal.appendChild(content); 
    overlay.appendChild(modal); 
    document.body.appendChild(overlay);
}

function escapeHtml(s){ 
    return String(s).replace(/[&<>"']/g,function(c){ 
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]); 
    }); 
}
</script>

<style>
/* Theme Management Styles */
.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.theme-card {
    border: 1px solid var(--admin-border);
    border-radius: 12px;
    overflow: hidden;
    background: white;
    transition: var(--transition);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.theme-card:hover {
    box-shadow: var(--admin-shadow);
    transform: translateY(-2px);
}

.theme-preview {
    height: 200px;
    background: var(--admin-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-screenshot {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.theme-placeholder {
    color: var(--admin-gray-400);
}

.theme-content {
    padding: 20px;
}

.theme-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.theme-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--admin-gray-800);
}

.theme-meta {
    margin: 0 0 16px 0;
    color: var(--admin-gray-600);
    font-size: 14px;
}

.theme-description {
    margin: 0 0 16px 0;
    color: var(--admin-gray-600);
    line-height: 1.5;
    font-size: 14px;
}

.theme-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.theme-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    padding: 6px 12px;
}

.upload-drop-zone {
    border: 2px dashed var(--admin-border);
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 16px 0;
    background: var(--admin-gray-50);
    transition: background 0.2s;
}

.upload-drop-zone:hover {
    background: var(--admin-gray-100);
}

.upload-progress {
    margin: 16px 0;
}

.progress-bar {
    height: 10px;
    background: var(--admin-gray-200);
    border-radius: 6px;
    overflow: hidden;
}

.progress-fill {
    height: 10px;
    width: 0%;
    background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-dark));
    transition: width 0.3s;
}

.progress-percent {
    margin-top: 6px;
    font-size: 12px;
    color: var(--admin-gray-600);
    text-align: center;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 4000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-card {
    background: white;
    border-radius: 12px;
    max-width: 640px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--admin-border);
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--admin-gray-800);
}

.modal-content {
    padding: 20px;
    max-height: calc(80vh - 80px);
    overflow-y: auto;
}

.theme-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 12px;
    color: var(--admin-gray-500);
    margin-bottom: 4px;
    font-weight: 500;
}

.detail-value {
    font-size: 14px;
    color: var(--admin-gray-800);
}

@media (max-width: 768px) {
    .themes-grid {
        grid-template-columns: 1fr;
    }
    
    .theme-details-grid {
        grid-template-columns: 1fr;
    }
    
    .theme-actions {
        flex-direction: column;
    }
    
    .theme-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>