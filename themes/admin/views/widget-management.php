<?php
$page_title = $page_title ?? 'Widget Management';
$widgets = $widgets ?? [];
$available_widgets = $available_widgets ?? [];
$menu_items = $menu_items ?? [];
$widget_categories = $widget_categories ?? [];
require_once __DIR__ . '/../layouts/main.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-cubes"></i> Widget Management</h1>
        <p>Manage dashboard widgets and their configurations</p>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openCreateWidgetModal()">
                <i class="fas fa-plus"></i> Create Widget
            </button>
            <button class="btn btn-secondary" onclick="importWidget()">
                <i class="fas fa-upload"></i> Import Widget
            </button>
        </div>
    </div>

    <!-- Widget Statistics -->
    <div class="widget-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-cube"></i>
            </div>
            <div class="stat-content">
                <h3>Total Widgets</h3>
                <div class="stat-value"><?= count($widgets) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>Active Widgets</h3>
                <div class="stat-value"><?= count(array_filter($widgets, fn($w) => $w['active'] ?? false)) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <div class="stat-content">
                <h3>Categories</h3>
                <div class="stat-value"><?= count($widget_categories) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-download"></i>
            </div>
            <div class="stat-content">
                <h3>Downloads</h3>
                <div class="stat-value"><?= array_sum(array_column($widgets, 'downloads')) ?></div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="widget-controls">
        <div class="search-bar">
            <input type="text" id="widget-search" placeholder="Search widgets..." onkeyup="filterWidgets()">
            <button class="btn btn-secondary" onclick="clearSearch()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="filter-controls">
            <select id="category-filter" onchange="filterWidgets()">
                <option value="">All Categories</option>
                <?php foreach ($widget_categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="status-filter" onchange="filterWidgets()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="system">System</option>
            </select>
            <select id="sort-by" onchange="sortWidgets()">
                <option value="name">Name</option>
                <option value="created">Created Date</option>
                <option value="downloads">Downloads</option>
                <option value="rating">Rating</option>
            </select>
        </div>
    </div>

    <!-- Widget Grid -->
    <div class="widget-grid" id="widget-grid">
        <?php if (!empty($widgets)): ?>
            <?php foreach ($widgets as $widget): ?>
                <div class="widget-card" data-widget-id="<?= htmlspecialchars($widget['id']) ?>" 
                     data-category="<?= htmlspecialchars($widget['category'] ?? '') ?>"
                     data-status="<?= htmlspecialchars($widget['status'] ?? 'inactive') ?>"
                     data-name="<?= htmlspecialchars($widget['name'] ?? '') ?>"
                     data-downloads="<?= htmlspecialchars($widget['downloads'] ?? 0) ?>"
                     data-rating="<?= htmlspecialchars($widget['rating'] ?? 0) ?>">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-<?= htmlspecialchars($widget['icon'] ?? 'cube') ?>"></i>
                        </div>
                        <div class="widget-status">
                            <span class="status-indicator status-<?= htmlspecialchars($widget['status'] ?? 'inactive') ?>"></span>
                        </div>
                    </div>
                    <div class="widget-content">
                        <h3><?= htmlspecialchars($widget['name'] ?? 'Untitled Widget') ?></h3>
                        <p class="widget-description"><?= htmlspecialchars($widget['description'] ?? 'No description available') ?></p>
                        <div class="widget-meta">
                            <span class="widget-category"><?= htmlspecialchars($widget['category_name'] ?? 'Uncategorized') ?></span>
                            <span class="widget-version">v<?= htmlspecialchars($widget['version'] ?? '1.0.0') ?></span>
                        </div>
                        <div class="widget-stats">
                            <span class="widget-stat">
                                <i class="fas fa-download"></i> <?= number_format($widget['downloads'] ?? 0) ?>
                            </span>
                            <span class="widget-stat">
                                <i class="fas fa-star"></i> <?= number_format($widget['rating'] ?? 0, 1) ?>
                            </span>
                        </div>
                    </div>
                    <div class="widget-actions">
                        <button class="btn btn-sm btn-primary" onclick="configureWidget('<?= htmlspecialchars($widget['id']) ?>')">
                            <i class="fas fa-cog"></i> Configure
                        </button>
                        <button class="btn btn-sm btn-<?= ($widget['active'] ?? false) ? 'warning' : 'success' ?>" 
                                onclick="toggleWidget('<?= htmlspecialchars($widget['id']) ?>')">
                            <i class="fas fa-<?= ($widget['active'] ?? false) ? 'pause' : 'play' ?>"></i> 
                            <?= ($widget['active'] ?? false) ? 'Disable' : 'Enable' ?>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" onclick="toggleDropdown('dropdown-<?= htmlspecialchars($widget['id']) ?>')">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu" id="dropdown-<?= htmlspecialchars($widget['id']) ?>">
                                <a href="#" class="dropdown-item" onclick="editWidget('<?= htmlspecialchars($widget['id']) ?>')">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="#" class="dropdown-item" onclick="duplicateWidget('<?= htmlspecialchars($widget['id']) ?>')">
                                    <i class="fas fa-copy"></i> Duplicate
                                </a>
                                <a href="#" class="dropdown-item" onclick="exportWidget('<?= htmlspecialchars($widget['id']) ?>')">
                                    <i class="fas fa-download"></i> Export
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-danger" onclick="deleteWidget('<?= htmlspecialchars($widget['id']) ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-cube"></i>
                <h3>No Widgets Found</h3>
                <p>Get started by creating your first widget or importing existing ones.</p>
                <button class="btn btn-primary" onclick="openCreateWidgetModal()">
                    <i class="fas fa-plus"></i> Create Your First Widget
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Available Widgets Marketplace -->
    <div class="widget-marketplace">
        <div class="marketplace-header">
            <h3>Widget Marketplace</h3>
            <p>Discover and install widgets from the community</p>
            <button class="btn btn-outline-primary" onclick="refreshMarketplace()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        <div class="marketplace-grid">
            <?php foreach ($available_widgets as $widget): ?>
                <div class="marketplace-widget">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-<?= htmlspecialchars($widget['icon'] ?? 'cube') ?>"></i>
                        </div>
                        <div class="widget-badge">
                            <?php if ($widget['featured'] ?? false): ?>
                                <span class="badge badge-warning">Featured</span>
                            <?php endif; ?>
                            <?php if ($widget['premium'] ?? false): ?>
                                <span class="badge badge-success">Premium</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="widget-content">
                        <h3><?= htmlspecialchars($widget['name'] ?? 'Untitled Widget') ?></h3>
                        <p class="widget-description"><?= htmlspecialchars($widget['description'] ?? 'No description available') ?></p>
                        <div class="widget-meta">
                            <span class="widget-author">by <?= htmlspecialchars($widget['author'] ?? 'Unknown') ?></span>
                            <span class="widget-version">v<?= htmlspecialchars($widget['version'] ?? '1.0.0') ?></span>
                        </div>
                        <div class="widget-stats">
                            <span class="widget-stat">
                                <i class="fas fa-download"></i> <?= number_format($widget['downloads'] ?? 0) ?>
                            </span>
                            <span class="widget-stat">
                                <i class="fas fa-star"></i> <?= number_format($widget['rating'] ?? 0, 1) ?>
                            </span>
                            <span class="widget-stat">
                                <i class="fas fa-dollar-sign"></i> <?= $widget['price'] ?? 'Free' ?>
                            </span>
                        </div>
                    </div>
                    <div class="widget-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="previewWidget('<?= htmlspecialchars($widget['id']) ?>')">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="installWidget('<?= htmlspecialchars($widget['id']) ?>')">
                            <i class="fas fa-download"></i> Install
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Create Widget Modal -->
<div class="modal fade" id="createWidgetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Widget</h5>
                <button type="button" class="close" onclick="closeModal('createWidgetModal')">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-widget-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="widget-name">Widget Name *</label>
                            <input type="text" class="form-control" id="widget-name" name="name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="widget-category">Category *</label>
                            <select class="form-control" id="widget-category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($widget_categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="widget-description">Description *</label>
                        <textarea class="form-control" id="widget-description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="widget-icon">Icon</label>
                            <select class="form-control" id="widget-icon" name="icon">
                                <option value="cube">Cube</option>
                                <option value="chart-bar">Chart Bar</option>
                                <option value="chart-line">Chart Line</option>
                                <option value="users">Users</option>
                                <option value="cog">Settings</option>
                                <option value="database">Database</option>
                                <option value="server">Server</option>
                                <option value="clock">Clock</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="widget-version">Version</label>
                            <input type="text" class="form-control" id="widget-version" name="version" value="1.0.0">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="widget-type">Type</label>
                            <select class="form-control" id="widget-type" name="type">
                                <option value="dashboard">Dashboard</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="modal">Modal</option>
                                <option value="standalone">Standalone</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="widget-template">Template</label>
                        <select class="form-control" id="widget-template" name="template">
                            <option value="">Select Template (Optional)</option>
                            <option value="stats">Statistics Widget</option>
                            <option value="chart">Chart Widget</option>
                            <option value="table">Table Widget</option>
                            <option value="form">Form Widget</option>
                            <option value="custom">Custom Widget</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createWidgetModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createWidget()">Create Widget</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeWidgetManagement();
});

function initializeWidgetManagement() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        }
    });
}

function filterWidgets() {
    const searchTerm = document.getElementById('widget-search').value.toLowerCase();
    const categoryFilter = document.getElementById('category-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    
    const widgets = document.querySelectorAll('.widget-card');
    
    widgets.forEach(widget => {
        const name = widget.dataset.name.toLowerCase();
        const category = widget.dataset.category;
        const status = widget.dataset.status;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        widget.style.display = matchesSearch && matchesCategory && matchesStatus ? 'block' : 'none';
    });
}

function sortWidgets() {
    const sortBy = document.getElementById('sort-by').value;
    const grid = document.getElementById('widget-grid');
    const widgets = Array.from(grid.querySelectorAll('.widget-card'));
    
    widgets.sort((a, b) => {
        let aValue, bValue;
        
        switch (sortBy) {
            case 'name':
                aValue = a.dataset.name.toLowerCase();
                bValue = b.dataset.name.toLowerCase();
                return aValue.localeCompare(bValue);
            case 'downloads':
                aValue = parseInt(a.dataset.downloads) || 0;
                bValue = parseInt(b.dataset.downloads) || 0;
                return bValue - aValue;
            case 'rating':
                aValue = parseFloat(a.dataset.rating) || 0;
                bValue = parseFloat(b.dataset.rating) || 0;
                return bValue - aValue;
            default:
                return 0;
        }
    });
    
    widgets.forEach(widget => grid.appendChild(widget));
}

function clearSearch() {
    document.getElementById('widget-search').value = '';
    filterWidgets();
}

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const isVisible = dropdown.style.display === 'block';
    
    // Hide all dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Show current dropdown if it was hidden
    if (!isVisible) {
        dropdown.style.display = 'block';
    }
}

function openCreateWidgetModal() {
    document.getElementById('createWidgetModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function createWidget() {
    const form = document.getElementById('create-widget-form');
    const formData = new FormData(form);
    
    fetch('<?= app_base_url('/admin/widgets/create') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Widget created successfully', 'success');
            closeModal('createWidgetModal');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Failed to create widget: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error creating widget', 'error');
    });
}

function configureWidget(widgetId) {
    window.location.href = `<?= app_base_url('/admin/widgets/configure') ?>/${widgetId}`;
}

function toggleWidget(widgetId) {
    fetch(`<?= app_base_url('/admin/widgets/toggle') ?>/${widgetId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Widget status updated', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to update widget status', 'error');
        }
    });
}

function editWidget(widgetId) {
    window.location.href = `<?= app_base_url('/admin/widgets/edit') ?>/${widgetId}`;
}

function duplicateWidget(widgetId) {
    if (confirm('Are you sure you want to duplicate this widget?')) {
        fetch(`<?= app_base_url('/admin/widgets/duplicate') ?>/${widgetId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Widget duplicated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to duplicate widget', 'error');
            }
        });
    }
}

function exportWidget(widgetId) {
    window.open(`<?= app_base_url('/admin/widgets/export') ?>/${widgetId}`, '_blank');
}

function deleteWidget(widgetId) {
    if (confirm('Are you sure you want to delete this widget? This action cannot be undone.')) {
        fetch(`<?= app_base_url('/admin/widgets/delete') ?>/${widgetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Widget deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to delete widget', 'error');
            }
        });
    }
}

function importWidget() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json,.zip';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('widget_file', file);
            
            fetch('<?= app_base_url('/admin/widgets/import') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Widget imported successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Failed to import widget: ' + data.message, 'error');
                }
            });
        }
    };
    input.click();
}

function previewWidget(widgetId) {
    window.open(`<?= app_base_url('/admin/widgets/preview') ?>/${widgetId}`, '_blank');
}

function installWidget(widgetId) {
    fetch(`<?= app_base_url('/admin/widgets/install') ?>/${widgetId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Widget installed successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Failed to install widget: ' + data.message, 'error');
        }
    });
}

function refreshMarketplace() {
    showNotification('Refreshing widget marketplace...', 'info');
    setTimeout(() => location.reload(), 1000);
}
</script>

<style>
.widget-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #212529;
}

.widget-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-bar input {
    min-width: 300px;
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.filter-controls {
    display: flex;
    gap: 10px;
}

.filter-controls select {
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.widget-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.widget-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.widget-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.widget-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.status-active {
    background: #28a745;
}

.status-indicator.status-inactive {
    background: #6c757d;
}

.status-indicator.status-system {
    background: #ffc107;
}

.widget-content {
    padding: 15px;
}

.widget-content h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    color: #212529;
}

.widget-description {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 15px;
    line-height: 1.4;
}

.widget-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.widget-category {
    background: #e9ecef;
    color: #495057;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
}

.widget-version {
    color: #6c757d;
    font-size: 12px;
}

.widget-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.widget-stat {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #6c757d;
    font-size: 12px;
}

.widget-actions {
    padding: 15px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 10px;
    align-items: center;
}

.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: none;
    border: none;
    padding: 5px;
}

.dropdown-menu {
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 1000;
    min-width: 150px;
    display: none;
}

.dropdown-item {
    display: block;
    padding: 8px 12px;
    color: #212529;
    text-decoration: none;
    font-size: 14px;
}

.dropdown-item:hover {
    background: #f8f9fa;
}

.dropdown-item.text-danger {
    color: #dc3545;
}

.dropdown-divider {
    height: 1px;
    background: #e9ecef;
    margin: 4px 0;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    grid-column: 1 / -1;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #495057;
}

.widget-marketplace {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 1px solid #e9ecef;
}

.marketplace-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.marketplace-header h3 {
    margin: 0;
    font-size: 24px;
    color: #212529;
}

.marketplace-header p {
    margin: 5px 0 0 0;
    color: #6c757d;
}

.marketplace-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.marketplace-widget {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.marketplace-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.widget-badge {
    display: flex;
    gap: 5px;
}

.badge {
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}

.badge-warning {
    background: #ffc107;
    color: #212529;
}

.badge-success {
    background: #28a745;
    color: white;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1050;
}

.modal-dialog {
    position: relative;
    max-width: 800px;
    margin: 50px auto;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 20px;
    color: #212529;
}

.close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 20px;
    max-height: 60vh;
    overflow-y: auto;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
}

@media (max-width: 768px) {
    .widget-controls {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .search-bar input {
        min-width: auto;
        width: 100%;
    }
    
    .filter-controls {
        flex-wrap: wrap;
    }
    
    .widget-grid {
        grid-template-columns: 1fr;
    }
    
    .marketplace-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>