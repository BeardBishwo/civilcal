<?php

/**
 * BLOG MANAGEMENT INTERFACE
 * Matches the 'Advertisements' and 'Pages' module design.
 */

// Set page variables
$page_title = 'Blog Management - Admin Panel';
$currentPage = 'blog';
$breadcrumbs = [
    ['title' => 'Admin', 'url' => app_base_url('/admin')],
    ['title' => 'Blog']
];

// Calculate stats
$totalPosts = count($posts);
$publishedPosts = count(array_filter($posts, fn($p) => $p['status'] === 'published'));
$draftPosts = count(array_filter($posts, fn($p) => $p['status'] === 'draft'));
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
                <div class="header-subtitle"><?php echo $totalPosts; ?> total • <?php echo $publishedPosts; ?> published articles</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/blog/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalPosts; ?></div>
                    <div class="stat-label">Total Posts</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $publishedPosts; ?></div>
                    <div class="stat-label">Published</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $draftPosts; ?></div>
                    <div class="stat-label">Drafts</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">-</div>
                    <div class="stat-label">Authors</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search posts..." id="page-search">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="status-filter" class="filter-compact" onchange="filterPosts()">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($posts)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-blog"></i>
                            <h3>No blog posts found</h3>
                            <p>Write your first article to share with your audience</p>
                            <a href="<?php echo app_base_url('/admin/blog/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create Post
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-checkbox">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th class="col-title">Article Title</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-author">Author</th>
                                        <th class="col-date">Date</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($posts as $post): ?>
                                        <tr data-status="<?php echo $post['status']; ?>" class="page-row">
                                            <td>
                                                <input type="checkbox" class="page-checkbox" value="<?php echo $post['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact"><?php echo htmlspecialchars($post['title']); ?></div>
                                                    <div class="page-slug-compact">/blog/<?php echo htmlspecialchars($post['slug']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                                    <i class="fas fa-<?php echo $post['status'] === 'published' ? 'check-circle' : 'edit'; ?>"></i>
                                                    <?php echo ucfirst($post['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="author-compact">
                                                    <i class="fas fa-user-circle"></i>
                                                    <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-compact">
                                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('/blog/' . $post['slug']); ?>" target="_blank" class="action-btn-icon" title="View">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <a href="<?php echo app_base_url('/admin/blog/edit/' . $post['id']); ?>" class="action-btn-icon edit-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="deletePost(<?php echo $post['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
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
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('page-search');
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.page-row').forEach(row => {
                    const title = row.querySelector('.page-title-compact').textContent.toLowerCase();
                    row.style.display = title.includes(term) ? '' : 'none';
                });
            });
        }
    });

    function filterPosts() {
        const status = document.getElementById('status-filter').value;
        document.querySelectorAll('.page-row').forEach(row => {
            if (!status || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function deletePost(id) {
        if (confirm('Are you sure you want to delete this post?')) {
            fetch(`<?php echo app_base_url('/admin/blog/delete/'); ?>${id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message);
            });
        }
    }
</script>

<style>
/* Design System Bridge */
:root {
    --gray-50: #f9fafb;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-900: #111827;
}

.admin-wrapper-container { max-width: 1400px; margin: 0 auto; padding: 1.5rem; }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; }

/* HEADER */
.compact-header { 
    display: flex; justify-content: space-between; align-items: center; padding: 2rem; 
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white;
}
.header-title { display: flex; align-items: center; gap: 1rem; }
.header-title h1 { margin: 0; font-size: 1.75rem; font-weight: 800; color: white; }
.header-title i { font-size: 1.5rem; }
.header-subtitle { opacity: 0.8; font-size: 0.9rem; margin-top: 0.25rem; }
.btn-compact { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; display: flex; align-items: center; gap: 0.5rem; backdrop-filter: blur(4px); }
.btn-compact:hover { background: rgba(255,255,255,0.3); color: white; }

/* STATS */
.compact-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; padding: 2rem; background: #f9fafb; border-bottom: 1px solid #eee; }
.stat-item { display: flex; align-items: center; gap: 1rem; background: white; padding: 1.25rem; border-radius: 10px; border: 1px solid #e5e7eb; }
.stat-icon { width: 3rem; height: 3rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; }
.stat-icon.primary { background: #4f46e5; }
.stat-icon.success { background: #10b981; }
.stat-icon.warning { background: #f59e0b; }
.stat-icon.info { background: #3b82f6; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1; }
.stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-top: 0.25rem; }

/* TOOLBAR */
.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-bottom: 1px solid #eee; }
.search-compact { position: relative; width: 350px; }
.search-compact i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.search-compact input { width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; }
.filter-compact { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; min-width: 150px; }

/* TABLE */
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th { background: #f9fafb; padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #6b7280; }
.table-compact td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.page-title-compact { font-weight: 700; color: #111827; font-size: 1rem; }
.page-slug-compact { font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem; font-family: 'JetBrains Mono', monospace; }

.status-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
.status-published { background: #d1fae5; color: #065f46; }
.status-draft { background: #fef3c7; color: #92400e; }

.author-compact { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #374151; font-weight: 500; }
.date-compact { font-size: 0.85rem; color: #6b7280; }

.actions-compact { display: flex; gap: 0.5rem; }
.action-btn-icon { width: 2.25rem; height: 2.25rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; color: #6b7280; transition: all 0.2s; background: white; }
.action-btn-icon:hover { background: #f9fafb; color: #111827; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.edit-btn:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.delete-btn:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

.empty-state-compact { text-align: center; padding: 5rem 2rem; }
.empty-state-compact i { font-size: 4rem; color: #e5e7eb; margin-bottom: 1.5rem; }
</style>
