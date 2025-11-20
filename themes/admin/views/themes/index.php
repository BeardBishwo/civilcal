<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Theme Management</h1>
                <p class="text-muted">Manage and customize the look and feel of your application.</p>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('themeUpload').click()">
                <i class="fas fa-upload"></i> Upload Theme
            </button>
            <input type="file" id="themeUpload" style="display: none" accept=".zip">
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($themes as $theme): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 <?= $theme['is_active'] ? 'border-primary' : '' ?>">
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
                        <?php if ($theme['is_active']): ?>
                            <span class="badge badge-primary">Active</span>
                        <?php endif; ?>
                    </h5>
                    <p class="card-text text-muted small">
                        v<?= htmlspecialchars($theme['version'] ?? '1.0.0') ?> by <?= htmlspecialchars($theme['author'] ?? 'Unknown') ?>
                    </p>
                    <p class="card-text"><?= htmlspecialchars($theme['description'] ?? '') ?></p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <div class="d-grid gap-2">
                        <?php if ($theme['is_active']): ?>
                            <a href="/admin/themes/customize" class="btn btn-outline-primary">
                                <i class="fas fa-paint-brush"></i> Customize
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary activate-theme" data-id="<?= $theme['id'] ?>">
                                <i class="fas fa-check"></i> Activate
                            </button>
                        <?php endif; ?>
                        
                        <div class="btn-group">
                            <a href="/admin/themes/preview/<?= $theme['id'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Preview</a>
                            <?php if (!$theme['is_active']): ?>
                                <button class="btn btn-sm btn-outline-danger delete-theme" data-id="<?= $theme['id'] ?>">Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activate Theme
    document.querySelectorAll('.activate-theme').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Activating...';
            
            const formData = new FormData();
            formData.append('theme_id', id);
            
            fetch('/admin/themes/activate', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check"></i> Activate';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Activation failed');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check"></i> Activate';
            });
        });
    });

    // Upload Theme
    document.getElementById('themeUpload').addEventListener('change', function(e) {
        if (this.files.length === 0) return;
        
        const formData = new FormData();
        formData.append('theme_zip', this.files[0]);
        
        const btn = document.querySelector('button[onclick*="themeUpload"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        fetch('/admin/themes/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Theme uploaded successfully!');
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
});
</script>
