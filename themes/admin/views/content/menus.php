<?php
// Remove the variable assignment approach and use the themes/admin layout system
$page_title = 'Manage Menus - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Menus']
];
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-bars"></i>
        Manage Menus
    </h1>
    <p class="page-description">Create and manage navigation menus for your website</p>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <a href="<?php echo app_base_url('admin/content/menus/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Menu
    </a>
</div>

<!-- Menus List -->
<div class="card">
    <div class="card-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Items</th>
                        <th>Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus ?? [] as $menu): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu['name']); ?></td>
                            <td><?php echo htmlspecialchars($menu['location']); ?></td>
                            <td><?php echo $menu['items_count']; ?></td>
                            <td><?php echo $menu['modified_at']; ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo app_base_url('admin/content/menus/edit/' . $menu['id']); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo app_base_url('admin/content/menus/delete/' . $menu['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this menu?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                    <a href="<?php echo app_base_url('admin/content/menus/preview/' . $menu['id']); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Menu Locations -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-map-marker-alt"></i>
            Menu Locations
        </h3>
    </div>
    <div class="card-content">
        <div class="menu-locations">
            <div class="menu-location">
                <div class="location-name">Header Menu</div>
                <div class="location-description">Appears at the top of the page</div>
                <div class="location-select">
                    <select class="form-control">
                        <option>Primary Navigation</option>
                        <option>Main Menu</option>
                        <option>Header Links</option>
                    </select>
                </div>
            </div>
            
            <div class="menu-location">
                <div class="location-name">Footer Menu</div>
                <div class="location-description">Appears at the bottom of the page</div>
                <div class="location-select">
                    <select class="form-control">
                        <option>Footer Links</option>
                        <option>Utility Menu</option>
                        <option>None</option>
                    </select>
                </div>
            </div>
            
            <div class="menu-location">
                <div class="location-name">Mobile Menu</div>
                <div class="location-description">Appears in mobile menu</div>
                <div class="location-select">
                    <select class="form-control">
                        <option>Main Menu</option>
                        <option>Mobile Navigation</option>
                        <option>None</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.menu-locations {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.menu-location {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
}

.location-name {
    font-weight: 600;
    color: var(--admin-gray-800);
}

.location-description {
    color: var(--admin-gray-600);
    font-size: 14px;
}

.location-select {
    align-self: flex-end;
    min-width: 200px;
}
</style>