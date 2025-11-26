<div class="card">
    <div class="card-header">
        <h1 class="card-title"><i class="fas fa-palette"></i> Theme Management</h1>
        <div>
            <button class="btn btn-primary" onclick="document.getElementById('themeUpload').click()">
                <i class="fas fa-upload"></i> Upload Theme
            </button>
            <input type="file" id="themeUpload" style="display:none" accept=".zip">
        </div>
    </div>
    <div class="card-content">
        <p class="page-description">Manage and customize the look and feel. Upload, activate, preview and delete themes.</p>
        <div id="uploadDropZone" style="border: 2px dashed var(--admin-border); border-radius: 12px; padding: 24px; display: flex; align-items: center; justify-content: center; gap: 12px; margin: 16px 0; background: var(--admin-gray-50);">
            <i class="fas fa-file-archive" style="color: var(--admin-primary);"></i>
            <span>Drag & drop theme ZIP here or click Upload Theme</span>
        </div>
        <div id="uploadProgress" style="display:none; margin-top: 8px;">
            <div style="height: 10px; background: var(--admin-gray-200); border-radius: 6px; overflow: hidden;">
                <div id="uploadBar" style="height: 10px; width: 0%; background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-dark));"></div>
            </div>
            <div id="uploadPercent" style="margin-top: 6px; font-size: 12px; color: var(--admin-gray-600);">0%</div>
        </div>
        <div style="display:flex; gap:12px; align-items:center; margin: 12px 0;">
            <input id="themeSearch" class="form-control" placeholder="Search themes by name" style="max-width: 320px;">
            <select id="themeStatusFilter" class="form-control" style="max-width: 200px;">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
<?php foreach ($themes as $theme): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100" style="border: 1px solid var(--admin-border);">
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                <?php if (!empty($theme['screenshot'])): ?>
                    <img src="<?= htmlspecialchars($theme['screenshot']) ?>" alt="<?= htmlspecialchars($theme['name']) ?>" class="img-fluid" style="max-height: 100%;">
                <?php else: ?>
                    <i class="fas fa-palette fa-4x text-muted"></i>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($theme['name']) ?>
                    <?php if ($theme['is_active'] ?? false): ?>
                        <span class="status-badge status-online">Active</span>
                    <?php else: ?>
                        <span class="status-badge status-offline">Inactive</span>
                    <?php endif; ?>
                </h5>
                <p class="card-text text-muted small">v<?= htmlspecialchars($theme['version'] ?? '1.0.0') ?> by <?= htmlspecialchars($theme['author'] ?? 'Unknown') ?></p>
                <div style="display:flex; gap:8px;">
                    <button class="btn btn-secondary btn-sm view-theme" data-slug="<?= htmlspecialchars($theme['slug'] ?? $theme['id']) ?>"><i class="fas fa-info-circle"></i> Details</button>
                    <a href="<?= app_base_url('/admin/themes/' . $theme['id'] . '/preview') ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-eye"></i> Preview</a>
                    <?php if (!($theme['is_active'] ?? false)): ?>
                        <button class="btn btn-primary btn-sm activate-theme" data-id="<?= $theme['id'] ?>"><i class="fas fa-check"></i> Activate</button>
                        <button class="btn btn-danger btn-sm delete-theme" data-id="<?= $theme['id'] ?>"><i class="fas fa-trash"></i> Delete</button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm deactivate-theme" data-id="<?= $theme['id'] ?>"><i class="fas fa-stop"></i> Deactivate</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
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

                fetch('<?= app_base_url('/admin/themes/activate') ?>', {
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

                fetch('<?= app_base_url('/admin/themes/deactivate') ?>', {
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
            xhr.open('POST', '<?= app_base_url('/admin/themes/upload') ?>', true);
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
                fetch('<?= app_base_url('/admin/themes/delete') ?>', {
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
                fetch('<?= app_base_url('/admin/themes/details') ?>/' + encodeURIComponent(key), { method:'GET', headers:{'Accept':'application/json'} })
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
            document.querySelectorAll('.row .card').forEach(function(card){
                var name = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                var isActive = card.querySelector('.status-online') !== null;
                var matchText = !q || name.includes(q);
                var matchStatus = st==='all' || (st==='active' && isActive) || (st==='inactive' && !isActive);
                card.parentElement.style.display = (matchText && matchStatus) ? '' : 'none';
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
    toast.innerHTML = '<div style="display:flex; align-items:center; gap:10px;"><i class="fas ' + (type==='success'?'fa-check-circle':'fa-times-circle') + '"></i><span>'+message+'</span></div>';
    document.body.appendChild(toast);
    setTimeout(function(){ toast.classList.remove('show'); setTimeout(function(){ document.body.removeChild(toast); }, 300); }, 2500);
}
function openDetailsModal(data){
    var overlay = document.createElement('div'); overlay.style.position='fixed'; overlay.style.top='0'; overlay.style.left='0'; overlay.style.right='0'; overlay.style.bottom='0'; overlay.style.background='rgba(0,0,0,0.5)'; overlay.style.zIndex='4000';
    var modal = document.createElement('div'); modal.className='card'; modal.style.maxWidth='640px'; modal.style.margin='80px auto';
    var header = document.createElement('div'); header.className='card-header'; var title=document.createElement('div'); title.className='card-title'; title.innerHTML='<i class="fas fa-info-circle"></i> Theme Details'; var closeBtn=document.createElement('button'); closeBtn.className='btn btn-secondary'; closeBtn.textContent='Close'; closeBtn.onclick=function(){ document.body.removeChild(overlay); };
    header.appendChild(title); header.appendChild(closeBtn);
    var content=document.createElement('div'); content.className='card-content';
    var html='<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">';
    html += '<div><div class="form-label">Name</div><div>'+escapeHtml(data.name||'')+'</div></div>';
    html += '<div><div class="form-label">Version</div><div>'+escapeHtml(data.version||'')+'</div></div>';
    html += '<div style="grid-column:1 / -1"><div class="form-label">Description</div><div>'+escapeHtml(data.description||'')+'</div></div>';
    html += '<div><div class="form-label">Author</div><div>'+escapeHtml(data.author||'')+'</div></div>';
    html += '</div>';
    content.innerHTML=html; modal.appendChild(header); modal.appendChild(content); overlay.appendChild(modal); document.body.appendChild(overlay);
}
function escapeHtml(s){ return String(s).replace(/[&<>"']/g,function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]); }); }
</script>
