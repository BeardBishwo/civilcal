<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Menu Management</h1>
                <p class="text-muted">Manage your website's primary navigation menu.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Primary Menu Structure</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Define your menu items as a JSON array. Each item should have a <code>label</code>, <code>url</code>, and optional <code>icon</code>.
                </div>
                
                <form action="/admin/content/menus/save" method="post">
                    <div class="form-group mb-3">
                        <label class="form-label">Menu Items (JSON)</label>
                        <textarea name="items" rows="15" class="form-control code-editor" style="font-family: monospace;"><?= htmlspecialchars(json_encode($items ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Menu
                        </button>
                        <a href="/" target="_blank" class="btn btn-secondary">
                            <i class="fas fa-external-link-alt"></i> Preview Site
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
