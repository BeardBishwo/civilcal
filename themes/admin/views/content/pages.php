<?php
// Pages Management View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i>
            Manage Pages
        </h1>
        <p class="page-description">Create, edit, and manage website pages</p>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <a href="' . app_base_url('admin/content/pages/create') . '" class="btn btn-primary">
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
                <table class="admin-table">
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
                        ' . implode('', array_map(function($page) {
                            return '<tr>
                                <td>' . htmlspecialchars($page['title']) . '</td>
                                <td>' . htmlspecialchars($page['slug']) . '</td>
                                <td>' . htmlspecialchars($page['author']) . '</td>
                                <td><span class="badge badge-' . ($page['status'] === 'published' ? 'success' : 'warning') . '">' . ucfirst($page['status']) . '</span></td>
                                <td>' . $page['created_at'] . '</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="' . app_base_url('admin/content/pages/edit/' . $page['id']) . '" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="' . app_base_url('admin/content/pages/delete/' . $page['id']) . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Are you sure you want to delete this page?\')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>';
                        }, $pages ?? [])) . '
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
</div>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Pages']
];

$page_title = $page_title ?? 'Manage Pages - Admin Panel';
$currentPage = $currentPage ?? 'content';

// Include the layout
include __DIR__ . '/../layouts/main.php';
?>