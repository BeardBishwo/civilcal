<?php
// Remove the variable assignment approach and use the themes/admin layout system
$page_title = 'Content Management - Admin Panel';
$currentPage = 'content';
?>

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
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-file"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($pages ?? []); ?></div>
        <div class="stat-label">Pages</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($menus ?? []); ?></div>
        <div class="stat-label">Menus</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-images"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($media ?? []); ?></div>
        <div class="stat-label">Media Files</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-pen"></i>
            </div>
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
            <a href="<?php echo app_base_url('admin/content/pages'); ?>" class="quick-action-card">
                <div class="quick-action-icon primary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="quick-action-content">
                    <h4>Manage Pages</h4>
                </div>
            </a>

            <a href="<?php echo app_base_url('admin/content/menus'); ?>" class="quick-action-card">
                <div class="quick-action-icon success">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="quick-action-content">
                    <h4>Manage Menus</h4>
                </div>
            </a>

            <a href="<?php echo app_base_url('admin/content/media'); ?>" class="quick-action-card">
                <div class="quick-action-icon info">
                    <i class="fas fa-images"></i>
                </div>
                <div class="quick-action-content">
                    <h4>Media Library</h4>
                </div>
            </a>

            <a href="<?php echo app_base_url('admin/content/pages/create'); ?>" class="quick-action-card">
                <div class="quick-action-icon warning">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="quick-action-content">
                    <h4>New Page</h4>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Recent Content
        </h3>
        <div class="card-actions">
            <a href="<?php echo app_base_url('admin/content/pages'); ?>" class="btn btn-primary btn-sm">View All</a>
        </div>
    </div>
    <div class="card-content">
        <div class="table-responsive">
            <table class="table">
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