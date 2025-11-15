<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pages</h2>
        <a href="/admin/content/page/create" class="btn btn-primary">New Page</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach (($pages ?? []) as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['slug'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['status'] ?? 'draft') ?></td>
                <td><?= htmlspecialchars($p['updated_at'] ?? '') ?></td>
                <td>
                    <a href="/admin/content/page/edit/<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="/admin/content/preview/<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-info" target="_blank">Preview</a>
                    <form action="/admin/content/publish" method="post" class="d-inline">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($p['slug']) ?>">
                        <button class="btn btn-sm btn-success" type="submit">Publish</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>