<?php
// Remove the variable assignment approach and use the themes/admin layout system
$page_title = 'Manage Pages - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Pages']
];
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-alt"></i>
        Manage Pages
    </h1>
    <p class="page-description">Create, edit, and manage website pages</p>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <a href="<?php echo app_base_url('admin/content/pages/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Page
    </a>
    <div class="toolbar-actions">
        <div class="search-box">
            <input type="text" placeholder="Search pages..." class="form-control">
            <i class="fas fa-search"></i>
        </div>
    </div>
</div>

<!-- Pages List -->
<div class="card">
    <div class="card-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages ?? [] as $page): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($page['title']); ?></td>
                            <td><?php echo htmlspecialchars($page['slug']); ?></td>
                            <td><?php echo htmlspecialchars($page['author']); ?></td>
                            <td><span class="badge badge-<?php echo $page['status'] === 'published' ? 'success' : 'warning'; ?>"><?php echo ucfirst($page['status']); ?></span></td>
                            <td><?php echo $page['created_at']; ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo app_base_url('admin/content/pages/edit/' . $page['id']); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo app_base_url('admin/content/pages/delete/' . $page['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this page?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <a href="#" class="page-link">Previous</a>
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link">Next</a>
        </div>
    </div>
</div>