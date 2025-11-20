<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Content Management</h1>
                <p class="text-muted">Manage static pages and content.</p>
            </div>
            <a href="/admin/content/page/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Page
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($pages ?? []) as $p): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?= htmlspecialchars($p['title'] ?? '') ?></div>
                                    </td>
                                    <td><code><?= htmlspecialchars($p['slug'] ?? '') ?></code></td>
                                    <td>
                                        <?php if (($p['status'] ?? 'draft') === 'published'): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['updated_at'] ?? '') ?></td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a href="/admin/content/page/edit/<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/content/preview/<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-info" target="_blank" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="/admin/content/publish" method="post" class="d-inline">
                                                <input type="hidden" name="slug" value="<?= htmlspecialchars($p['slug']) ?>">
                                                <button class="btn btn-sm btn-success" type="submit" title="Publish">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pages)): ?>
                                <tr>
                                    <td colspan="5" class="text-center p-5 text-muted">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <p>No pages found. Create your first page!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
