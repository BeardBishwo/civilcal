<div class="container">
    <h2><?= $mode === 'edit' ? 'Edit Page' : 'Create Page' ?></h2>
    <form action="/admin/content/save" method="post">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($page['slug'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Body (HTML)</label>
            <textarea name="body" class="form-control" rows="10"><?= htmlspecialchars($page['body'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="draft" <?= (($page['status'] ?? 'draft')==='draft')?'selected':'' ?>>Draft</option>
                <option value="published" <?= (($page['status'] ?? '')==='published')?'selected':'' ?>>Published</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save</button>
            <?php if (!empty($page['slug'])): ?>
            <a href="/admin/content/preview/<?= urlencode($page['slug']) ?>" target="_blank" class="btn btn-secondary">Preview</a>
            <?php endif; ?>
        </div>
    </form>
</div>