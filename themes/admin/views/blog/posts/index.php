<?php
/**
 * PREMIUM BLOG POSTS LIST
 * Manage all auto-generated blog posts
 */
$posts = $posts ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-blog"></i>
                    <h1>Blog Posts</h1>
                </div>
                <div class="header-subtitle"><?php echo count($posts); ?> Auto-Generated Collection Posts</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/blog/posts/create'); ?>" class="btn-create-premium">
                    <i class="fas fa-plus"></i> CREATE NEW POST
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <?php if (empty($posts)): ?>
                <div class="table-wrapper">
                    <div class="empty-state-compact">
                        <i class="fas fa-blog"></i>
                        <h3>No Blog Posts Yet</h3>
                        <p>Create your first auto-generated blog post from your question bank</p>
                        <a href="<?php echo app_base_url('admin/blog/posts/create'); ?>" class="btn-create-premium" style="margin-top: 1rem;">
                            <i class="fas fa-magic"></i> Generate Blog Post
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 100px;" class="text-center">Type</th>
                                <th>Title</th>
                                <th style="width: 120px;" class="text-center">Questions</th>
                                <th style="width: 120px;" class="text-center">Views</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th style="width: 150px;" class="text-center">Created</th>
                                <th style="width: 180px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $index => $post): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $typeColors = [
                                            'popular' => 'badge-danger',
                                            'category' => 'badge-primary',
                                            'difficulty' => 'badge-warning',
                                            'recent' => 'badge-info',
                                            'featured' => 'badge-success'
                                        ];
                                        $typeIcons = [
                                            'popular' => 'fa-fire',
                                            'category' => 'fa-folder',
                                            'difficulty' => 'fa-chart-line',
                                            'recent' => 'fa-clock',
                                            'featured' => 'fa-star'
                                        ];
                                        ?>
                                        <span class="badge-compact <?php echo $typeColors[$post['type']] ?? 'badge-secondary'; ?>">
                                            <i class="fas <?php echo $typeIcons[$post['type']] ?? 'fa-file'; ?>"></i>
                                            <?php echo ucfirst($post['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="cell-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                        <div class="cell-subtitle">
                                            <i class="fas fa-link"></i> <?php echo $post['slug']; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="stat-badge">
                                            <i class="fas fa-list"></i> <?php echo $post['question_count']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="stat-badge">
                                            <i class="fas fa-eye"></i> <?php echo number_format($post['view_count']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($post['is_published']): ?>
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle"></i> Published
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-clock"></i> Draft
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons-compact">
                                            <a href="<?php echo app_base_url('blog/' . $post['slug']); ?>" 
                                               target="_blank" 
                                               class="btn-action-compact btn-view" 
                                               title="View Live">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="<?php echo app_base_url('admin/blog/posts/' . $post['id']); ?>" 
                                               class="btn-action-compact btn-edit" 
                                               title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="deletePost(<?php echo $post['id']; ?>)" 
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

<style>
.badge-compact {
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    font-size: 0.688rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-danger {
    background: linear-gradient(135deg, #f43f5e 0%, #dc2626 100%);
    color: white;
}

.badge-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.badge-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.cell-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
}

.cell-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-badge {
    background: #f1f5f9;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.813rem;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fef3c7;
    color: #92400e;
}

.action-buttons-compact {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-action-compact {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    text-decoration: none;
}

.btn-view {
    background: #ede9fe;
    color: #6366f1;
}

.btn-view:hover {
    background: #6366f1;
    color: white;
}

.btn-edit {
    background: #dbeafe;
    color: #3b82f6;
}

.btn-edit:hover {
    background: #3b82f6;
    color: white;
}

.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-delete:hover {
    background: #dc2626;
    color: white;
}
</style>

<script>
function deletePost(id) {
    Swal.fire({
        title: 'Delete Blog Post?',
        text: 'This action cannot be undone',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo app_base_url('admin/blog/posts/delete/'); ?>${id}`, {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Blog post has been deleted', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'Failed to delete post', 'error');
                }
            });
        }
    });
}
</script>
