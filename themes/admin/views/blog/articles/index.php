<?php
/**
 * PREMIUM BLOG ARTICLES LIST
 * Manage all traditional blog posts
 */
$articles = $articles ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-newspaper"></i>
                    <h1>Blog Articles</h1>
                </div>
                <div class="header-subtitle"><?php echo count($articles); ?> Traditional Blog Posts</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/blog/articles/create'); ?>" class="btn-create-premium">
                    <i class="fas fa-plus"></i> CREATE ARTICLE
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <?php if (empty($articles)): ?>
                <div class="table-wrapper">
                    <div class="empty-state-compact">
                        <i class="fas fa-newspaper"></i>
                        <h3>No Articles Yet</h3>
                        <p>Create your first blog article with rich content</p>
                        <a href="<?php echo app_base_url('admin/blog/articles/create'); ?>" class="btn-create-premium" style="margin-top: 1rem;">
                            <i class="fas fa-pen"></i> Write Article
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 80px;">Image</th>
                                <th>Title</th>
                                <th style="width: 150px;">Author</th>
                                <th style="width: 150px;">Category</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th style="width: 100px;" class="text-center">Views</th>
                                <th style="width: 150px;" class="text-center">Date</th>
                                <th style="width: 180px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $index => $article): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php if ($article['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 40px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="color: #cbd5e1;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="cell-title"><?php echo htmlspecialchars($article['title']); ?></div>
                                        <div class="cell-subtitle">
                                            <i class="fas fa-link"></i> <?php echo $article['slug']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php if ($article['category_name']): ?>
                                            <span class="badge-compact badge-primary">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.813rem;">Uncategorized</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $statusColors = [
                                            'published' => 'status-active',
                                            'draft' => 'status-inactive',
                                            'scheduled' => 'status-badge'
                                        ];
                                        $statusIcons = [
                                            'published' => 'fa-check-circle',
                                            'draft' => 'fa-edit',
                                            'scheduled' => 'fa-clock'
                                        ];
                                        ?>
                                        <span class="status-badge <?php echo $statusColors[$article['status']]; ?>">
                                            <i class="fas <?php echo $statusIcons[$article['status']]; ?>"></i>
                                            <?php echo ucfirst($article['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="stat-badge">
                                            <i class="fas fa-eye"></i> <?php echo number_format($article['view_count']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?php echo date('M d, Y', strtotime($article['created_at'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons-compact">
                                            <?php if ($article['status'] === 'published'): ?>
                                                <a href="<?php echo app_base_url('blog/' . $article['slug']); ?>" 
                                                   target="_blank" 
                                                   class="btn-action-compact btn-view" 
                                                   title="View Live">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo app_base_url('admin/blog/articles/edit/' . $article['id']); ?>" 
                                               class="btn-action-compact btn-edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteArticle(<?php echo $article['id']; ?>)" 
                                                    class="btn-action-compact btn-delete" 
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

<script>
function deleteArticle(id) {
    Swal.fire({
        title: 'Delete Article?',
        text: 'This action cannot be undone',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo app_base_url('admin/blog/articles/delete/'); ?>${id}`, {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Article has been deleted', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Failed to delete article', 'error');
                }
            });
        }
    });
}
</script>
