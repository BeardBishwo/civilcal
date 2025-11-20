<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title"><?= $mode === 'edit' ? 'Edit Page' : 'Create Page' ?></h1>
                <p class="text-muted"><?= $mode === 'edit' ? 'Update existing page content.' : 'Create a new static page.' ?></p>
            </div>
            <a href="/admin/content" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Pages
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="/admin/content/save" method="post">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="form-label">Page Title</label>
                                <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($page['title'] ?? '') ?>" placeholder="Enter page title" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Content (HTML)</label>
                                <textarea name="body" class="form-control" rows="15" style="font-family: monospace;"><?= htmlspecialchars($page['body'] ?? '') ?></textarea>
                                <small class="text-muted">You can use standard HTML tags.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Publishing</h5>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Slug (URL)</label>
                                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" placeholder="auto-generated-from-title">
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="draft" <?= (($page['status'] ?? 'draft')==='draft')?'selected':'' ?>>Draft</option>
                                            <option value="published" <?= (($page['status'] ?? '')==='published')?'selected':'' ?>>Published</option>
                                        </select>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Save Page
                                        </button>
                                        <?php if (!empty($page['slug'])): ?>
                                            <a href="/admin/content/preview/<?= urlencode($page['slug']) ?>" target="_blank" class="btn btn-info btn-block">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
