<?php
// Content Management Dashboard View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i>
            Content Management
        </h1>
        <p class="page-description">Manage pages, menus, and media for your website</p>
    </div>

    <!-- Content Management Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file"></i>
            </div>
            <div class="stat-value">' . count($pages ?? []) . '</div>
            <div class="stat-label">Pages</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-bars"></i>
            </div>
            <div class="stat-value">' . count($menus ?? []) . '</div>
            <div class="stat-label">Menus</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-value">' . count($media ?? []) . '</div>
            <div class="stat-label">Media Files</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-pen"></i>
            </div>
            <div class="stat-value">12</div>
            <div class="stat-label">Drafts</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
        </div>
        <div class="card-content">
            <div class="quick-actions-grid">
                <a href="' . app_base_url('admin/content/pages') . '" class="quick-action-item">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="action-label">Manage Pages</div>
                </a>

                <a href="' . app_base_url('admin/content/menus') . '" class="quick-action-item">
                    <div class="action-icon">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="action-label">Manage Menus</div>
                </a>

                <a href="' . app_base_url('admin/content/media') . '" class="quick-action-item">
                    <div class="action-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="action-label">Media Library</div>
                </a>

                <a href="' . app_base_url('admin/content/pages/create') . '" class="quick-action-item">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-label">New Page</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Content -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history"></i>
                Recent Content
            </h3>
            <div class="card-actions">
                <a href="' . app_base_url('admin/content/pages') . '" class="btn btn-sm btn-primary">View All</a>
            </div>
        </div>
        <div class="card-content">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Home Page</td>
                            <td>Page</td>
                            <td><span class="badge badge-success">Published</span></td>
                            <td>Admin</td>
                            <td>Today</td>
                            <td>
                                <div class="table-actions">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>About Us</td>
                            <td>Page</td>
                            <td><span class="badge badge-success">Published</span></td>
                            <td>Editor</td>
                            <td>Yesterday</td>
                            <td>
                                <div class="table-actions">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Contact Form</td>
                            <td>Page</td>
                            <td><span class="badge badge-warning">Draft</span></td>
                            <td>Admin</td>
                            <td>2 days ago</td>
                            <td>
                                <div class="table-actions">
                                    <a href="#" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: var(--admin-gray-50);
    border-radius: 8px;
    text-decoration: none;
    color: var(--admin-gray-700);
    border: 2px solid transparent;
    transition: var(--transition);
    cursor: pointer;
}

.quick-action-item:hover {
    background: white;
    border-color: var(--admin-primary);
    color: var(--admin-primary);
    transform: translateY(-2px);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.action-label {
    font-weight: 500;
    font-size: 14px;
    text-align: center;
}

@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management']
];

$page_title = $page_title ?? 'Content Management - Admin Panel';
$currentPage = $currentPage ?? 'content';

// Include the layout
include __DIR__ . '/../layouts/main.php';
?>