<?php
// Enhanced page management interface with modern design
$page_title = 'Manage Pages - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Pages']
];

// Calculate stats from pages data
$totalPages = count($pages ?? []);
$publishedPages = count(array_filter($pages ?? [], fn($p) => $p['status'] === 'published'));
$draftPages = count(array_filter($pages ?? [], fn($p) => $p['status'] === 'draft'));
?>

<!-- Enhanced Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-header-main">
            <h1 class="page-title">
                <i class="fas fa-file-alt" aria-hidden="true"></i>
                Manage Pages
            </h1>
            <p class="page-description">Create, edit, and manage your website pages with advanced content management tools</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('admin/content/pages/create'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-plus" aria-hidden="true"></i>
                <span>Add New Page</span>
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-file-alt" aria-hidden="true"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $totalPages; ?></div>
            <div class="stat-label">Total Pages</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $publishedPages; ?></div>
            <div class="stat-label">Published</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-edit" aria-hidden="true"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $draftPages; ?></div>
            <div class="stat-label">Drafts</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-eye" aria-hidden="true"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value">1.2K</div>
            <div class="stat-label">Total Views</div>
        </div>
    </div>
</div>

<!-- Enhanced Toolbar -->
<div class="toolbar">
    <div class="toolbar-section">
        <div class="search-container">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon" aria-hidden="true"></i>
                <input
                    type="text"
                    id="page-search"
                    placeholder="Search pages by title, slug, or author..."
                    class="form-control search-input"
                    autocomplete="off"
                >
                <button type="button" class="search-clear" id="search-clear" style="display: none;">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="toolbar-section">
        <div class="filter-group">
            <select id="status-filter" class="form-control filter-select" aria-label="Filter by status">
                <option value="">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>

            <select id="sort-by" class="form-control filter-select" aria-label="Sort by">
                <option value="created_at">Newest First</option>
                <option value="title">Title A-Z</option>
                <option value="author">Author</option>
            </select>
        </div>

        <div class="view-toggle">
            <button type="button" class="btn btn-icon active" id="view-table" aria-label="Table view">
                <i class="fas fa-table" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-icon" id="view-grid" aria-label="Grid view">
                <i class="fas fa-th" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<!-- Pages Content -->
<div class="pages-container">
    <!-- Table View -->
    <div id="table-view" class="view-container active">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-list" aria-hidden="true"></i>
                    Pages List
                    <span class="card-subtitle"><?php echo $totalPages; ?> pages found</span>
                </div>
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="export-pages">
                        <i class="fas fa-download" aria-hidden="true"></i>
                        Export
                    </button>
                </div>
            </div>

            <div class="card-content">
                <?php if (empty($pages)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                        </div>
                        <h3 class="empty-state-title">No pages found</h3>
                        <p class="empty-state-description">Get started by creating your first page to showcase your content.</p>
                        <a href="<?php echo app_base_url('admin/content/pages/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus" aria-hidden="true"></i>
                            Create Your First Page
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" role="table" aria-label="Pages list">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="select-all" aria-label="Select all pages">
                                    </th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Created</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $page): ?>
                                    <tr data-page-id="<?php echo $page['id']; ?>">
                                        <td>
                                            <input type="checkbox" class="page-checkbox" value="<?php echo $page['id']; ?>" aria-label="Select page <?php echo htmlspecialchars($page['title']); ?>">
                                        </td>
                                        <td>
                                            <div class="page-title-cell">
                                                <div class="page-title"><?php echo htmlspecialchars($page['title']); ?></div>
                                                <div class="page-slug"><?php echo htmlspecialchars($page['slug']); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="page-slug-code"><?php echo htmlspecialchars($page['slug']); ?></code>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $page['status'] === 'published' ? 'success' : 'warning'; ?> status-badge">
                                                <i class="fas fa-<?php echo $page['status'] === 'published' ? 'check-circle' : 'edit'; ?>" aria-hidden="true"></i>
                                                <?php echo ucfirst($page['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="author-cell">
                                                <div class="author-avatar">
                                                    <i class="fas fa-user" aria-hidden="true"></i>
                                                </div>
                                                <span><?php echo htmlspecialchars($page['author']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="date-cell">
                                                <div class="date-main"><?php echo date('M j, Y', strtotime($page['created_at'])); ?></div>
                                                <div class="date-secondary"><?php echo date('H:i', strtotime($page['created_at'])); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <div class="action-buttons">
                                                    <a href="<?php echo app_base_url('admin/content/pages/edit/' . $page['id']); ?>"
                                                       class="btn btn-sm btn-outline-primary action-btn"
                                                       aria-label="Edit page <?php echo htmlspecialchars($page['title']); ?>">
                                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary action-btn"
                                                            onclick="togglePageStatus(<?php echo $page['id']; ?>)"
                                                            aria-label="<?php echo $page['status'] === 'published' ? 'Unpublish' : 'Publish'; ?> page <?php echo htmlspecialchars($page['title']); ?>">
                                                        <i class="fas fa-<?php echo $page['status'] === 'published' ? 'eye-slash' : 'eye'; ?>" aria-hidden="true"></i>
                                                        <span><?php echo $page['status'] === 'published' ? 'Unpublish' : 'Publish'; ?></span>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger action-btn delete-btn"
                                                            onclick="deletePage(<?php echo $page['id']; ?>, '<?php echo htmlspecialchars($page['title']); ?>')"
                                                            aria-label="Delete page <?php echo htmlspecialchars($page['title']); ?>">
                                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Showing <strong>1-<?php echo $totalPages; ?></strong> of <strong><?php echo $totalPages; ?></strong> pages
                        </div>
                        <nav aria-label="Pages pagination">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous page">
                                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                        Previous
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#" aria-label="Page 1">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Next page">
                                        Next
                                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Grid View (Optional) -->
    <div id="grid-view" class="view-container">
        <div class="pages-grid">
            <?php if (empty($pages)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                    </div>
                    <h3 class="empty-state-title">No pages found</h3>
                    <p class="empty-state-description">Get started by creating your first page to showcase your content.</p>
                    <a href="<?php echo app_base_url('admin/content/pages/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        Create Your First Page
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($pages as $page): ?>
                    <div class="page-card" data-page-id="<?php echo $page['id']; ?>">
                        <div class="page-card-header">
                            <div class="page-card-status">
                                <span class="badge badge-<?php echo $page['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <i class="fas fa-<?php echo $page['status'] === 'published' ? 'check-circle' : 'edit'; ?>" aria-hidden="true"></i>
                                    <?php echo ucfirst($page['status']); ?>
                                </span>
                            </div>
                            <div class="page-card-actions">
                                <button type="button" class="btn btn-sm btn-icon" aria-label="More actions">
                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="page-card-content">
                            <h3 class="page-card-title"><?php echo htmlspecialchars($page['title']); ?></h3>
                            <p class="page-card-slug"><?php echo htmlspecialchars($page['slug']); ?></p>
                            <div class="page-card-meta">
                                <span class="page-card-author">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                    <?php echo htmlspecialchars($page['author']); ?>
                                </span>
                                <span class="page-card-date">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    <?php echo date('M j, Y', strtotime($page['created_at'])); ?>
                                </span>
                            </div>
                        </div>

                        <div class="page-card-footer">
                            <a href="<?php echo app_base_url('admin/content/pages/edit/' . $page['id']); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                                Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePage(<?php echo $page['id']; ?>, '<?php echo htmlspecialchars($page['title']); ?>')">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (Hidden by default) -->
<div id="bulk-actions-bar" class="bulk-actions-bar" style="display: none;">
    <div class="bulk-actions-content">
        <div class="bulk-actions-info">
            <span id="selected-count">0</span> pages selected
        </div>
        <div class="bulk-actions-buttons">
            <button type="button" class="btn btn-sm btn-outline-success" id="bulk-publish">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                Publish
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" id="bulk-draft">
                <i class="fas fa-edit" aria-hidden="true"></i>
                Move to Draft
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="bulk-delete">
                <i class="fas fa-trash" aria-hidden="true"></i>
                Delete
            </button>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('page-search');
    const searchClear = document.getElementById('search-clear');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const title = row.querySelector('.page-title').textContent.toLowerCase();
            const slug = row.querySelector('.page-slug-code').textContent.toLowerCase();
            const author = row.querySelector('.author-cell span').textContent.toLowerCase();

            const isVisible = title.includes(searchTerm) ||
                            slug.includes(searchTerm) ||
                            author.includes(searchTerm);

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        searchClear.style.display = searchTerm ? 'block' : 'none';
        updatePaginationInfo(visibleCount);
    });

    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    });

    // Filter functionality
    document.getElementById('status-filter').addEventListener('change', function() {
        const status = this.value;
        tableRows.forEach(row => {
            if (!status) {
                row.style.display = '';
                return;
            }
            const rowStatus = row.querySelector('.status-badge').textContent.toLowerCase().trim();
            row.style.display = rowStatus.includes(status) ? '' : 'none';
        });
    });

    // View toggle
    const tableView = document.getElementById('table-view');
    const gridView = document.getElementById('grid-view');
    const viewTableBtn = document.getElementById('view-table');
    const viewGridBtn = document.getElementById('view-grid');

    viewTableBtn.addEventListener('click', function() {
        tableView.classList.add('active');
        gridView.classList.remove('active');
        viewTableBtn.classList.add('active');
        viewGridBtn.classList.remove('active');
    });

    viewGridBtn.addEventListener('click', function() {
        gridView.classList.add('active');
        tableView.classList.remove('active');
        viewGridBtn.classList.add('active');
        viewTableBtn.classList.remove('active');
    });

    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.page-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    // Individual checkbox functionality
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('page-checkbox')) {
            updateBulkActions();
        }
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.page-checkbox:checked');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        const selectedCount = document.getElementById('selected-count');

        if (checkedBoxes.length > 0) {
            bulkActionsBar.style.display = 'block';
            selectedCount.textContent = checkedBoxes.length;
        } else {
            bulkActionsBar.style.display = 'none';
        }
    }

    function updatePaginationInfo(visibleCount) {
        const paginationInfo = document.querySelector('.pagination-info strong');
        if (paginationInfo) {
            paginationInfo.textContent = `1-${visibleCount}`;
        }
    }
});

// Utility functions
function togglePageStatus(pageId) {
    if (confirm('Are you sure you want to change this page\'s status?')) {
        // Implement status toggle logic
        console.log('Toggle status for page:', pageId);
    }
}

function deletePage(pageId, pageTitle) {
    if (confirm(`Are you sure you want to delete "${pageTitle}"? This action cannot be undone.`)) {
        // Implement delete logic
        console.log('Delete page:', pageId);
    }
}
</script>

<style>
/* Enhanced Page Styles */
.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.page-header-main {
    flex: 1;
}

.page-header-actions {
    flex-shrink: 0;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.page-description {
    color: var(--admin-gray-600);
    font-size: 16px;
    max-width: 600px;
}

/* Enhanced Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--admin-shadow-lg);
}

.stat-header {
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--admin-gray-900);
    margin-bottom: 4px;
}

.stat-label {
    color: var(--admin-gray-600);
    font-size: 14px;
    font-weight: 500;
}

/* Enhanced Toolbar */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 24px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
}

.toolbar-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.search-container {
    position: relative;
    min-width: 300px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    padding-left: 40px !important;
    padding-right: 40px !important;
}

.search-icon {
    position: absolute;
    left: 12px;
    color: var(--admin-gray-400);
    z-index: 1;
}

.search-clear {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: var(--admin-gray-400);
    cursor: pointer;
    padding: 4px;
}

.filter-group {
    display: flex;
    gap: 12px;
}

.filter-select {
    min-width: 140px;
}

.view-toggle {
    display: flex;
    border: 1px solid var(--admin-gray-200);
    border-radius: 8px;
    overflow: hidden;
}

.view-toggle .btn {
    border: none;
    border-radius: 0;
    margin: 0;
}

.view-toggle .btn.active {
    background: var(--admin-primary);
    color: white;
}

/* Pages Container */
.pages-container {
    position: relative;
}

.view-container {
    display: none;
}

.view-container.active {
    display: block;
}

/* Enhanced Table */
.table {
    margin-bottom: 0;
}

.table th {
    background: var(--admin-gray-50);
    border-bottom: 2px solid var(--admin-gray-200);
    font-weight: 600;
    color: var(--admin-gray-700);
    padding: 16px 20px;
}

.table td {
    padding: 16px 20px;
    vertical-align: middle;
}

.page-title-cell {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.page-title {
    font-weight: 600;
    color: var(--admin-gray-900);
}

.page-slug {
    font-size: 12px;
    color: var(--admin-gray-500);
}

.page-slug-code {
    background: var(--admin-gray-100);
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 12px;
    color: var(--admin-gray-700);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

.author-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.date-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.date-main {
    font-weight: 500;
    color: var(--admin-gray-900);
}

.date-secondary {
    font-size: 12px;
    color: var(--admin-gray-500);
}

.table-actions {
    display: flex;
    gap: 8px;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.action-btn {
    white-space: nowrap;
}

.action-btn span {
    display: none;
}

@media (min-width: 768px) {
    .action-btn span {
        display: inline;
    }
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid var(--admin-gray-200);
}

.pagination-info {
    color: var(--admin-gray-600);
    font-size: 14px;
}

.pagination {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 4px;
}

.page-item.disabled .page-link {
    color: var(--admin-gray-400);
    pointer-events: none;
}

.page-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    color: var(--admin-gray-600);
    text-decoration: none;
    border: 1px solid var(--admin-gray-200);
    border-radius: 6px;
    transition: var(--transition);
}

.page-link:hover:not(.disabled) {
    background: var(--admin-gray-50);
    border-color: var(--admin-primary);
    color: var(--admin-primary);
}

.page-item.active .page-link {
    background: var(--admin-primary);
    border-color: var(--admin-primary);
    color: white;
}

/* Grid View */
.pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
}

.page-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.page-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--admin-shadow-lg);
}

.page-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--admin-gray-200);
    background: var(--admin-gray-50);
}

.page-card-status {
    flex: 1;
}

.page-card-actions {
    flex-shrink: 0;
}

.page-card-content {
    padding: 20px;
}

.page-card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--admin-gray-900);
    margin-bottom: 8px;
}

.page-card-slug {
    color: var(--admin-gray-500);
    font-size: 14px;
    margin-bottom: 16px;
    font-family: 'Monaco', 'Menlo', monospace;
}

.page-card-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.page-card-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--admin-gray-600);
}

.page-card-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--admin-gray-200);
    background: var(--admin-gray-50);
    display: flex;
    gap: 8px;
}

/* Bulk Actions */
.bulk-actions-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    border-radius: 12px;
    box-shadow: var(--admin-shadow-lg);
    border: 1px solid var(--admin-gray-200);
    z-index: 1000;
    min-width: 400px;
}

.bulk-actions-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    gap: 20px;
}

.bulk-actions-info {
    font-weight: 500;
    color: var(--admin-gray-700);
}

.bulk-actions-buttons {
    display: flex;
    gap: 8px;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--admin-gray-100);
    color: var(--admin-gray-400);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 24px;
}

.empty-state-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--admin-gray-900);
    margin-bottom: 8px;
}

.empty-state-description {
    color: var(--admin-gray-600);
    margin-bottom: 24px;
    max-width: 400px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .toolbar {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .toolbar-section {
        justify-content: center;
    }

    .search-container {
        min-width: auto;
        width: 100%;
    }
}

@media (max-width: 768px) {
    .page-header-content {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .pages-grid {
        grid-template-columns: 1fr;
    }

    .pagination-container {
        flex-direction: column;
        gap: 16px;
        align-items: center;
    }

    .bulk-actions-bar {
        left: 20px;
        right: 20px;
        transform: none;
        min-width: auto;
    }

    .bulk-actions-content {
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }
}

@media (max-width: 480px) {
    .toolbar {
        padding: 16px;
    }

    .filter-group {
        flex-direction: column;
        width: 100%;
    }

    .filter-select {
        min-width: auto;
    }

    .view-toggle {
        width: 100%;
    }

    .view-toggle .btn {
        flex: 1;
    }
}
</style>