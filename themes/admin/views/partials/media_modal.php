<!-- Media Manager Modal -->
<div class="modal fade" id="mediaManagerModal" tabindex="-1" aria-labelledby="mediaManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="height: 80vh;">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaManagerModalLabel">Media Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column">
                
                <!-- Toolbar -->
                <div class="media-toolbar p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-upload-input').click()">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                        <input type="file" id="modal-upload-input" style="display:none" onchange="MediaModal.handleUpload(this.files)">
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" class="form-control form-control-sm" placeholder="Search..." id="modal-media-search" style="width: 200px;">
                    </div>
                </div>

                <!-- Content Area -->
                <div class="media-container d-flex flex-grow-1" style="overflow: hidden;">
                    <!-- Grid -->
                    <div class="media-grid flex-grow-1 p-3" id="modal-media-grid" style="overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; align-content: start;">
                        <!-- JS injected items -->
                        <div class="text-center w-100 p-5">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                 <div class="text-muted small" id="modal-status-text"></div>
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-media-item {
        position: relative;
        padding-top: 100%;
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.2s;
    }
    .modal-media-item:hover { border-color: var(--admin-primary); }
    .modal-media-item img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .modal-media-item.selected {
        border-color: var(--admin-primary);
        box-shadow: 0 0 0 2px var(--admin-primary);
    }
</style>

<script>
window.MediaModal = {
    modal: null,
    callback: null,
    page: 1,
    isLoading: false,

    init: function() {
        if (!this.modal) {
            this.modal = new bootstrap.Modal(document.getElementById('mediaManagerModal'));
            
            // Search listener
            let timer;
            document.getElementById('modal-media-search').addEventListener('input', (e) => {
                clearTimeout(timer);
                timer = setTimeout(() => this.loadMedia(1, e.target.value), 500);
            });
        }
    },

    open: function(callback) {
        this.init();
        this.callback = callback;
        this.loadMedia(1);
        this.modal.show();
    },

    loadMedia: function(page = 1, search = '') {
        this.isLoading = true;
        const grid = document.getElementById('modal-media-grid');
        if (page === 1) grid.innerHTML = '<div class="text-center w-100 p-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';

        fetch(`<?php echo app_base_url('/admin/api/media'); ?>?page=${page}&search=${search}`)
            .then(r => r.json())
            .then(data => {
                grid.innerHTML = '';
                if (data.data.length === 0) {
                    grid.innerHTML = '<div class="text-center w-100 p-5 text-muted">No media found.</div>';
                    return;
                }

                data.data.forEach(item => {
                    const el = document.createElement('div');
                    el.className = 'modal-media-item';
                    el.title = item.filename;
                    
                    if (item.is_image) {
                        el.innerHTML = `<img src="${item.url}" loading="lazy">`;
                    } else {
                        el.innerHTML = `<div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fas fa-file fa-2x"></i></div>`;
                    }

                    el.onclick = () => this.selectItem(item);
                    grid.appendChild(el);
                });

                document.getElementById('modal-status-text').innerText = `Showing ${data.data.length} items`;
            })
            .catch(err => {
                console.error(err);
                grid.innerHTML = '<div class="text-danger p-3">Error loading media.</div>';
            })
            .finally(() => this.isLoading = false);
    },

    handleUpload: function(files) {
        if (!files.length) return;
        
        const fd = new FormData();
        fd.append('file', files[0]);
        fd.append('csrf_token', window.appConfig.csrfToken); // Ensure csrf_token is available

        const grid = document.getElementById('modal-media-grid');
        // Show uploading state
        const originalContent = grid.innerHTML;
        grid.innerHTML = '<div class="text-center w-100 p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br>Uploading...</div>';

        fetch('<?php echo app_base_url('/admin/api/media/upload'); ?>', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                // If success, just select it immediately
                this.selectItem(res.data);
            } else {
                alert('Upload failed: ' + (res.message || 'Unknown error'));
                this.loadMedia(1); // Reload to restore grid
            }
        })
        .catch(err => {
            alert('Upload error');
            this.loadMedia(1);
        });
    },

    selectItem: function(item) {
        if (this.callback) {
            this.callback(item.url, item);
        }
        this.modal.hide();
    }
};
</script>
