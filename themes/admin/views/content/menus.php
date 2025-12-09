<?php

/**
 * OPTIMIZED MENUS MANAGEMENT INTERFACE
 * Compact, User-Friendly Layout with Enhanced UX
 */

// Set page variables
$page_title = 'Menus Management - Admin Panel';
$currentPage = 'content';
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Menus']
];

// Sample menus data for demonstration
// Menus data is now passed from the controller as $menus

// Calculate stats
$totalMenus = count($menus);
$activeMenus = count(array_filter($menus, fn($m) => $m['status'] === 'active'));
$totalMenuItems = array_sum(array_column($menus, 'items_count'));
?>

<!-- Optimized Admin Container -->
<div class="menus-container">
    <div class="menus-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-bars"></i>
                    <h1>Menus</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalMenus; ?> menus • <?php echo $activeMenus; ?> active • <?php echo $totalMenuItems; ?> items</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/content/menus/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>New Menu</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalMenus; ?></div>
                    <div class="stat-label">Total Menus</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeMenus; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalMenuItems; ?></div>
                    <div class="stat-label">Menu Items</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Locations</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search menus..." id="menu-search">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="status-filter" class="filter-compact">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select id="location-filter" class="filter-compact">
                    <option value="">All Locations</option>
                    <option value="header">Header</option>
                    <option value="footer">Footer</option>
                    <option value="mobile">Mobile</option>
                </select>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" data-view="table" title="Table View">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" data-view="cards" title="Cards View">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menus Content Area -->
        <div class="menus-content">

            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            Menu List
                        </h3>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-outline-secondary" id="export-menus">
                                <i class="fas fa-download"></i>
                                Export
                            </button>
                        </div>
                    </div>
                    <div class="card-content-compact">
                        <?php if (empty($menus)): ?>
                            <div class="empty-state-compact">
                                <i class="fas fa-bars"></i>
                                <h3>No menus found</h3>
                                <p>Create your first menu to get started</p>
                                <a href="<?php echo app_base_url('admin/content/menus/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Create Menu
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
                                            <th class="col-name">Menu Name</th>
                                            <th class="col-location">Location</th>
                                            <th class="col-items">Items</th>
                                            <th class="col-status">Status</th>
                                            <th class="col-modified">Modified</th>
                                            <th class="col-actions">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($menus as $menu): ?>
                                            <tr data-menu-id="<?php echo $menu['id']; ?>" class="menu-row">
                                                <td>
                                                    <input type="checkbox" class="menu-checkbox" value="<?php echo $menu['id']; ?>">
                                                </td>
                                                <td>
                                                    <div class="menu-info">
                                                        <div class="menu-name"><?php echo htmlspecialchars($menu['name']); ?></div>
                                                        <div class="menu-location-text"><?php echo htmlspecialchars($menu['location']); ?> Menu</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="location-badge">
                                                        <i class="fas fa-<?php echo $menu['location'] === 'header' ? 'arrow-up' : ($menu['location'] === 'footer' ? 'arrow-down' : 'mobile-alt'); ?>"></i>
                                                        <?php echo ucfirst($menu['location']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="items-count">
                                                        <span class="count-number"><?php echo $menu['items_count']; ?></span>
                                                        <span class="count-label">items</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $menu['status']; ?>">
                                                        <i class="fas fa-<?php echo $menu['status'] === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                                        <?php echo ucfirst($menu['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="date-compact">
                                                        <?php echo date('M j', strtotime($menu['modified_at'])); ?>
                                                        <span class="time-compact"><?php echo date('H:i', strtotime($menu['modified_at'])); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="actions-compact">
                                                        <button class="action-btn-icon preview-btn"
                                                            onclick="previewMenu(<?php echo $menu['id']; ?>)"
                                                            title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="<?php echo app_base_url('admin/content/menus/edit/' . $menu['id']); ?>"
                                                            class="action-btn-icon edit-btn"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="action-btn-icon toggle-btn"
                                                            onclick="toggleMenuStatus(<?php echo $menu['id']; ?>)"
                                                            title="<?php echo $menu['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $menu['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                        <button class="action-btn-icon delete-btn"
                                                            onclick="deleteMenu(<?php echo $menu['id']; ?>, '<?php echo htmlspecialchars($menu['name']); ?>')"
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

            <!-- Cards View -->
            <div id="cards-view" class="view-section">
                <div class="cards-grid-compact">
                    <?php if (empty($menus)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-bars"></i>
                            <h3>No menus found</h3>
                            <p>Create your first menu to get started</p>
                            <a href="<?php echo app_base_url('admin/content/menus/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create Menu
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($menus as $menu): ?>
                            <div class="menu-card-compact" data-menu-id="<?php echo $menu['id']; ?>">
                                <div class="card-header-compact">
                                    <div class="card-status">
                                        <span class="status-badge status-<?php echo $menu['status']; ?>">
                                            <i class="fas fa-<?php echo $menu['status'] === 'active' ? 'check-circle' : 'pause-circle'; ?>"></i>
                                        </span>
                                    </div>
                                    <div class="card-actions">
                                        <button class="action-btn-icon" onclick="previewMenu(<?php echo $menu['id']; ?>)" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn-icon" onclick="toggleMenuStatus(<?php echo $menu['id']; ?>)" title="Toggle Status">
                                            <i class="fas fa-<?php echo $menu['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-content-compact">
                                    <h3 class="card-title-compact"><?php echo htmlspecialchars($menu['name']); ?></h3>
                                    <div class="menu-meta-compact">
                                        <div class="meta-row">
                                            <span class="meta-label">Location:</span>
                                            <span class="meta-value">
                                                <i class="fas fa-<?php echo $menu['location'] === 'header' ? 'arrow-up' : ($menu['location'] === 'footer' ? 'arrow-down' : 'mobile-alt'); ?>"></i>
                                                <?php echo ucfirst($menu['location']); ?>
                                            </span>
                                        </div>
                                        <div class="meta-row">
                                            <span class="meta-label">Items:</span>
                                            <span class="meta-value"><?php echo $menu['items_count']; ?> items</span>
                                        </div>
                                        <div class="meta-row">
                                            <span class="meta-label">Modified:</span>
                                            <span class="meta-value"><?php echo date('M j, Y', strtotime($menu['modified_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer-compact">
                                    <a href="<?php echo app_base_url('admin/content/menus/edit/' . $menu['id']); ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteMenu(<?php echo $menu['id']; ?>, '<?php echo htmlspecialchars($menu['name']); ?>')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Menu Locations Section -->
        <div class="compact-card locations-section">
            <div class="card-header-compact">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Menu Locations
                </h3>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-secondary" id="manage-locations">
                        <i class="fas fa-cog"></i>
                        Manage
                    </button>
                </div>
            </div>
            <div class="card-content-compact">
                <div class="locations-grid">
                    <div class="location-card">
                        <div class="location-icon header">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="location-info">
                            <h4 class="location-name">Header Menu</h4>
                            <p class="location-description">Appears at the top of the page</p>
                            <div class="location-select">
                                <select class="form-control-compact">
                                    <option value="">Select Menu</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option value="<?php echo $menu['id']; ?>" <?php echo $menu['location'] === 'header' ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($menu['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="location-card">
                        <div class="location-icon footer">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="location-info">
                            <h4 class="location-name">Footer Menu</h4>
                            <p class="location-description">Appears at the bottom of the page</p>
                            <div class="location-select">
                                <select class="form-control-compact">
                                    <option value="">Select Menu</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option value="<?php echo $menu['id']; ?>" <?php echo $menu['location'] === 'footer' ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($menu['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="location-card">
                        <div class="location-icon mobile">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="location-info">
                            <h4 class="location-name">Mobile Menu</h4>
                            <p class="location-description">Appears in mobile menu</p>
                            <div class="location-select">
                                <select class="form-control-compact">
                                    <option value="">Select Menu</option>
                                    <?php foreach ($menus as $menu): ?>
                                        <option value="<?php echo $menu['id']; ?>" <?php echo $menu['location'] === 'mobile' ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($menu['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Bulk Actions Bar -->
<div id="bulk-actions-float" class="bulk-actions-float" style="display: none;">
    <div class="bulk-actions-content">
        <span class="selected-count"><span id="bulk-count">0</span> selected</span>
        <div class="bulk-buttons">
            <button class="btn btn-sm btn-success" id="bulk-activate">
                <i class="fas fa-check"></i>
                Activate
            </button>
            <button class="btn btn-sm btn-warning" id="bulk-deactivate">
                <i class="fas fa-pause"></i>
                Deactivate
            </button>
            <button class="btn btn-sm btn-danger" id="bulk-delete">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </div>
        <button class="bulk-close" onclick="clearBulkSelection()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Menu Preview Modal -->
<div id="menu-preview-modal" class="preview-modal-compact" style="display: none;">
    <div class="preview-backdrop" onclick="closePreviewModal()"></div>
    <div class="preview-content-compact">
        <div class="preview-header-compact">
            <h3 class="preview-title">Menu Preview</h3>
            <div class="preview-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editFromPreview()">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="closePreviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="preview-body-compact">
            <iframe id="preview-iframe" src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<script>
    // Enhanced JavaScript for Optimized Interface
    document.addEventListener('DOMContentLoaded', function() {
        initializeMenuManager();
    });

    function initializeMenuManager() {
        // Search functionality with debouncing
        const searchInput = document.getElementById('menu-search');
        const searchClear = document.getElementById('search-clear');

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });

        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        });

        // Filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            performFilter('status', this.value);
        });

        document.getElementById('location-filter').addEventListener('change', function() {
            performFilter('location', this.value);
        });

        // View toggle
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                switchView(this.dataset.view);
            });
        });

        // Bulk selection
        document.getElementById('select-all').addEventListener('change', function() {
            toggleAllSelection(this.checked);
        });

        // Individual checkbox handlers
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('menu-checkbox')) {
                updateBulkActions();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'f':
                        e.preventDefault();
                        searchInput.focus();
                        break;
                    case 'a':
                        e.preventDefault();
                        document.getElementById('select-all').checked = true;
                        document.getElementById('select-all').dispatchEvent(new Event('change'));
                        break;
                }
            }

            if (e.key === 'Escape') {
                closePreviewModal();
                clearBulkSelection();
            }
        });

        // Location assignment handlers
        document.querySelectorAll('.location-select select').forEach(select => {
            select.addEventListener('change', function() {
                const location = this.closest('.location-card').querySelector('.location-name').textContent.toLowerCase().replace(' ', '-');
                const menuId = this.value;
                if (menuId) {
                    assignMenuToLocation(location, menuId);
                }
            });
        });
    }

    function performSearch(query) {
        const searchTerm = query.toLowerCase().trim();
        const menuRows = document.querySelectorAll('.menu-row');
        const searchClear = document.getElementById('search-clear');

        let visibleCount = 0;
        menuRows.forEach(row => {
            const name = row.querySelector('.menu-name').textContent.toLowerCase();
            const location = row.querySelector('.menu-location-text').textContent.toLowerCase();

            const isVisible = name.includes(searchTerm) || location.includes(searchTerm);

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Also filter cards view
        const menuCards = document.querySelectorAll('.menu-card-compact');
        menuCards.forEach(card => {
            const name = card.querySelector('.card-title-compact').textContent.toLowerCase();
            const isVisible = name.includes(searchTerm);
            card.style.display = isVisible ? '' : 'none';
        });

        searchClear.style.display = searchTerm ? 'block' : 'none';
        updateResultsCount(visibleCount);
    }

    function performFilter(type, value) {
        const menuRows = document.querySelectorAll('.menu-row');
        let visibleCount = 0;

        menuRows.forEach(row => {
            if (!value) {
                row.style.display = '';
                visibleCount++;
                return;
            }

            let rowValue;
            if (type === 'status') {
                rowValue = row.querySelector('.status-badge').textContent.toLowerCase().trim();
            } else if (type === 'location') {
                rowValue = row.querySelector('.location-badge').textContent.toLowerCase().trim();
            }

            const isVisible = rowValue.includes(value);

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        updateResultsCount(visibleCount);
    }

    function switchView(view) {
        // Update view buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        // Show/hide view sections
        document.querySelectorAll('.view-section').forEach(section => {
            section.classList.toggle('active', section.id === `${view}-view`);
        });
    }

    function toggleAllSelection(checked) {
        const checkboxes = document.querySelectorAll('.menu-checkbox');
        checkboxes.forEach(cb => cb.checked = checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
        const bulkBar = document.getElementById('bulk-actions-float');
        const bulkCount = document.getElementById('bulk-count');

        if (checkedBoxes.length > 0) {
            bulkBar.style.display = 'block';
            bulkCount.textContent = checkedBoxes.length;

            setTimeout(() => bulkBar.classList.add('visible'), 10);
        } else {
            bulkBar.classList.remove('visible');
            setTimeout(() => bulkBar.style.display = 'none', 300);
        }
    }

    function clearBulkSelection() {
        document.getElementById('select-all').checked = false;
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    function updateResultsCount(count) {
        // Update results display if needed
        console.log(`Showing ${count} menus`);
    }

    // Preview functionality
    function previewMenu(menuId) {
        const modal = document.getElementById('menu-preview-modal');
        const iframe = document.getElementById('preview-iframe');

        // In a real implementation, this would load the actual menu preview
        iframe.src = `/menus/preview/${menuId}`;

        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('visible'), 10);
    }

    function closePreviewModal() {
        const modal = document.getElementById('menu-preview-modal');
        modal.classList.remove('visible');
        setTimeout(() => {
            modal.style.display = 'none';
            document.getElementById('preview-iframe').src = '';
        }, 300);
    }

    function editFromPreview() {
        closePreviewModal();
        // Focus back to editor
        window.location.href = '/admin/content/menus/edit/' + getCurrentPreviewId();
    }

    // Action functions
    function toggleMenuStatus(menuId) {
        if (confirm('Are you sure you want to change this menu\'s status?')) {
            // Implement status toggle logic
            console.log('Toggle status for menu:', menuId);
            showNotification('Menu status updated successfully', 'success');
        }
    }

    function deleteMenu(menuId, menuName) {
        if (confirm(`Are you sure you want to delete "${menuName}"? This action cannot be undone.`)) {
            // Implement delete logic
            console.log('Delete menu:', menuId);
            showNotification('Menu deleted successfully', 'success');
        }
    }

    function assignMenuToLocation(location, menuId) {
        // Implement menu assignment logic
        console.log(`Assign menu ${menuId} to ${location} location`);
        showNotification(`Menu assigned to ${location} location`, 'success');
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('visible'), 10);
        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function getCurrentPreviewId() {
        const iframe = document.getElementById('preview-iframe');
        return iframe.src.split('/').pop();
    }
</script>

<style>
    /* ========================================
   OPTIMIZED MENUS CONTAINER
   ======================================== */

    .menus-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .menus-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* ========================================
   COMPACT HEADER & STATS (Reuse from page creation)
   ======================================== */

    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left {
        flex: 1;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.8;
        margin: 0;
    }

    .header-actions {
        flex-shrink: 0;
    }

    .btn-compact {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 500;
    }

    /* ========================================
   COMPACT STATS
   ======================================== */

    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .stat-icon.primary {
        background: #667eea;
    }

    .stat-icon.success {
        background: #48bb78;
    }

    .stat-icon.info {
        background: #4299e1;
    }

    .stat-icon.warning {
        background: #ed8936;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 500;
    }

    /* ========================================
   COMPACT TOOLBAR
   ======================================== */

    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        gap: 1rem;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .search-compact {
        position: relative;
        min-width: 250px;
        flex: 1;
        max-width: 350px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--admin-gray-400, #9ca3af);
        font-size: 0.875rem;
    }

    .search-compact input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.5rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s ease;
    }

    .search-compact input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--admin-gray-400, #9ca3af);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .search-clear:hover {
        background: var(--admin-gray-100, #f3f4f6);
        color: var(--admin-gray-600, #6b7280);
    }

    .filter-compact {
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        min-width: 120px;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .view-controls {
        display: flex;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        overflow: hidden;
    }

    .view-btn {
        padding: 0.625rem;
        border: none;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .view-btn:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    .view-btn.active {
        background: #667eea;
        color: white;
    }

    /* ========================================
   MENUS CONTENT
   ======================================== */

    .menus-content {
        padding: 2rem;
        min-height: 400px;
    }

    .view-section {
        display: none;
    }

    .view-section.active {
        display: block;
    }

    /* ========================================
   COMPACT CARDS (Reuse styles)
   ======================================== */

    .compact-card {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        margin-bottom: 2rem;
    }

    .compact-card:last-child {
        margin-bottom: 0;
    }

    .compact-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
    }

    .card-content-compact {
        padding: 1.5rem;
    }

    .card-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* ========================================
   TABLE STYLES
   ======================================== */

    .table-wrapper {
        overflow-x: auto;
    }

    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: var(--admin-gray-50, #f8f9fa);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--admin-gray-700, #374151);
        border-bottom: 2px solid var(--admin-gray-200, #e5e7eb);
        white-space: nowrap;
    }

    .table-compact td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        vertical-align: middle;
    }

    .table-compact tbody tr:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    .col-checkbox {
        width: 40px;
    }

    .col-name {
        min-width: 200px;
    }

    .col-location {
        width: 120px;
    }

    .col-items {
        width: 80px;
    }

    .col-status {
        width: 100px;
    }

    .col-modified {
        width: 100px;
    }

    .col-actions {
        width: 180px;
    }

    /* Menu Info */
    .menu-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .menu-name {
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1.2;
    }

    .menu-location-text {
        font-size: 0.75rem;
        color: var(--admin-gray-500, #6b7280);
    }

    /* Location Badge */
    .location-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        white-space: nowrap;
    }

    /* Items Count */
    .items-count {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .count-number {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1;
    }

    .count-label {
        font-size: 0.75rem;
        color: var(--admin-gray-500, #6b7280);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-active {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .status-inactive {
        background: rgba(237, 137, 54, 0.1);
        color: #ed8936;
    }

    /* Date */
    .date-compact {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
        font-size: 0.875rem;
    }

    .time-compact {
        font-size: 0.75rem;
        color: var(--admin-gray-500, #6b7280);
    }

    /* Actions */
    .actions-compact {
        display: flex;
        gap: 0.25rem;
        justify-content: flex-end;
    }

    .action-btn-icon {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .action-btn-icon:hover {
        transform: translateY(-1px);
    }

    .preview-btn:hover {
        background: #4299e1;
        color: white;
        border-color: #4299e1;
    }

    .edit-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .toggle-btn:hover {
        background: #ed8936;
        color: white;
        border-color: #ed8936;
    }

    .delete-btn:hover {
        background: #f56565;
        color: white;
        border-color: #f56565;
    }

    /* ========================================
   CARDS VIEW
   ======================================== */

    .cards-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }

    .menu-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .menu-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
    }

    .card-actions {
        display: flex;
        gap: 0.25rem;
    }

    .card-content-compact {
        padding: 1rem;
    }

    .card-title-compact {
        font-size: 1rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        margin: 0 0 0.75rem 0;
        line-height: 1.3;
    }

    .menu-meta-compact {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .meta-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 500;
    }

    .meta-value {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: var(--admin-gray-700, #374151);
    }

    .card-footer-compact {
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
        display: flex;
        gap: 0.5rem;
    }

    /* ========================================
   LOCATIONS SECTION
   ======================================== */

    .locations-section {
        margin-top: 2rem;
    }

    .locations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .location-card {
        display: flex;
        gap: 1rem;
        padding: 1.5rem;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .location-card:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .location-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .location-icon.header {
        background: #667eea;
    }

    .location-icon.footer {
        background: #48bb78;
    }

    .location-icon.mobile {
        background: #ed8936;
    }

    .location-info {
        flex: 1;
    }

    .location-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        margin: 0 0 0.5rem 0;
    }

    .location-description {
        color: var(--admin-gray-600, #6b7280);
        font-size: 0.875rem;
        margin: 0 0 1rem 0;
    }

    .location-select {
        margin-top: auto;
    }

    /* ========================================
   EMPTY STATE
   ======================================== */

    .empty-state-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-compact i {
        font-size: 3rem;
        color: var(--admin-gray-400, #9ca3af);
        margin-bottom: 1rem;
    }

    .empty-state-compact h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        margin: 0 0 0.5rem 0;
    }

    .empty-state-compact p {
        color: var(--admin-gray-600, #6b7280);
        margin: 0 0 1.5rem 0;
    }

    /* ========================================
   BULK ACTIONS FLOAT (Reuse styles)
   ======================================== */

    .bulk-actions-float {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .bulk-actions-float.visible {
        opacity: 1;
        visibility: visible;
    }

    .bulk-actions-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
    }

    .selected-count {
        font-weight: 500;
        color: var(--admin-gray-700, #374151);
    }

    .bulk-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .bulk-close {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .bulk-close:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    /* ========================================
   PREVIEW MODAL (Reuse styles)
   ======================================== */

    .preview-modal-compact {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .preview-modal-compact.visible {
        opacity: 1;
        visibility: visible;
    }

    .preview-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .preview-content-compact {
        position: relative;
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 1200px;
        height: 80%;
        max-height: 800px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .preview-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
    }

    .preview-title {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
    }

    .preview-actions {
        display: flex;
        gap: 0.5rem;
    }

    .preview-body-compact {
        flex: 1;
        overflow: hidden;
    }

    #preview-iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* ========================================
   NOTIFICATIONS (Reuse styles)
   ======================================== */

    .notification {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        z-index: 1100;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .notification.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
    }

    .notification-success {
        border-left: 4px solid #48bb78;
    }

    .notification-info {
        border-left: 4px solid #4299e1;
    }

    /* ========================================
   RESPONSIVE DESIGN
   ======================================== */

    @media (max-width: 1024px) {
        .menus-container {
            padding: 0.5rem;
        }

        .compact-header {
            padding: 1rem 1.5rem;
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .toolbar-left {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .search-compact {
            min-width: auto;
            max-width: none;
        }

        .locations-grid {
            grid-template-columns: 1fr;
        }

        .cards-grid-compact {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .compact-stats {
            grid-template-columns: repeat(2, 1fr);
            padding: 1rem 1.5rem;
        }

        .compact-toolbar {
            padding: 0.75rem 1.5rem;
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .menus-content {
            padding: 1.5rem;
        }

        .table-compact {
            font-size: 0.75rem;
        }

        .table-compact th,
        .table-compact td {
            padding: 0.5rem 0.75rem;
        }

        .col-actions {
            width: 140px;
        }

        .actions-compact {
            flex-direction: column;
            gap: 0.125rem;
        }

        .action-btn-icon {
            width: 1.75rem;
            height: 1.75rem;
            font-size: 0.625rem;
        }

        .cards-grid-compact {
            grid-template-columns: 1fr;
        }

        .bulk-actions-content {
            flex-direction: column;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
        }

        .bulk-buttons {
            justify-content: center;
        }

        .preview-content-compact {
            width: 95%;
            height: 90%;
        }
    }

    @media (max-width: 480px) {
        .compact-stats {
            grid-template-columns: 1fr;
        }

        .stat-item {
            padding: 0.75rem;
        }

        .table-wrapper {
            overflow-x: scroll;
        }

        .table-compact {
            min-width: 600px;
        }

        .bulk-actions-float {
            left: 1rem;
            right: 1rem;
            transform: none;
        }

        .location-card {
            flex-direction: column;
            text-align: center;
        }
    }

    /* ========================================
   ACCESSIBILITY
   ======================================== */

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Focus styles */
    .action-btn-icon:focus,
    .view-btn:focus,
    .form-control-compact:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }

    /* High contrast mode */
    @media (prefers-contrast: high) {
        .compact-card {
            border-width: 2px;
        }

        .action-btn-icon {
            border-width: 2px;
        }

        .status-badge {
            border: 1px solid currentColor;
        }
    }
</style>