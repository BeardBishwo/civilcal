<?php
$page_title = $page_title ?? 'Menu Customization';
$menu_items = $menu_items ?? [];
$available_modules = $available_modules ?? [];
$menu_config = $menu_config ?? [];
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-bars"></i> Menu Customization</h1>
        <p>Drag and drop to customize your admin menu structure</p>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="resetToDefault()">
                <i class="fas fa-undo"></i> Reset to Default
            </button>
            <button class="btn btn-primary" onclick="saveMenuConfiguration()">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    <div class="menu-customization-container">
        <!-- Available Menu Items -->
        <div class="menu-panel">
            <div class="panel-header">
                <h3>Available Menu Items</h3>
                <div class="panel-controls">
                    <input type="text" id="menu-search" placeholder="Search menu items..." onkeyup="filterMenuItems()">
                    <select id="category-filter" onchange="filterMenuItems()">
                        <option value="">All Categories</option>
                        <option value="dashboard">Dashboard</option>
                        <option value="users">Users</option>
                        <option value="content">Content</option>
                        <option value="settings">Settings</option>
                        <option value="modules">Modules</option>
                        <option value="system">System</option>
                    </select>
                </div>
            </div>
            <div class="panel-content">
                <div class="menu-items-list" id="available-items">
                    <?php foreach ($menu_items as $key => $item): ?>
                        <div class="menu-item" data-key="<?= htmlspecialchars($key) ?>" data-category="<?= htmlspecialchars($item['category'] ?? 'other') ?>">
                            <div class="menu-item-content">
                                <div class="menu-item-icon">
                                    <i class="fas fa-<?= htmlspecialchars($item['icon'] ?? 'circle') ?>"></i>
                                </div>
                                <div class="menu-item-info">
                                    <h4><?= htmlspecialchars($item['label'] ?? $key) ?></h4>
                                    <p><?= htmlspecialchars($item['description'] ?? 'Menu item description') ?></p>
                                </div>
                                <div class="menu-item-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="previewMenuItem('<?= htmlspecialchars($key) ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if (!empty($item['submenu'])): ?>
                                <div class="submenu-items">
                                    <?php foreach ($item['submenu'] as $subkey => $submenu): ?>
                                        <div class="submenu-item" data-key="<?= htmlspecialchars($subkey) ?>" data-parent="<?= htmlspecialchars($key) ?>">
                                            <i class="fas fa-<?= htmlspecialchars($submenu['icon'] ?? 'angle-right') ?>"></i>
                                            <span><?= htmlspecialchars($submenu['label']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Current Menu Structure -->
        <div class="menu-panel">
            <div class="panel-header">
                <h3>Current Menu Structure</h3>
                <div class="panel-controls">
                    <button class="btn btn-sm btn-secondary" onclick="expandAll()">
                        <i class="fas fa-expand"></i> Expand All
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="collapseAll()">
                        <i class="fas fa-compress"></i> Collapse All
                    </button>
                </div>
            </div>
            <div class="panel-content">
                <div class="menu-structure" id="current-menu">
                    <?php if (!empty($menu_config['structure'])): ?>
                        <?php // Menu structure will be rendered via JavaScript ?>
                    <?php else: ?>
                        <div class="empty-menu">
                            <i class="fas fa-plus-circle"></i>
                            <p>Drag menu items from the left panel to build your menu</p>
                            <button class="btn btn-outline-primary" onclick="loadDefaultMenu()">
                                Load Default Menu
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Settings -->
    <div class="menu-settings">
        <h3>Menu Settings</h3>
        <div class="settings-grid">
            <div class="setting-group">
                <label for="menu-style">Menu Style</label>
                <select id="menu-style" class="form-control">
                    <option value="sidebar" <?= ($menu_config['style'] ?? 'sidebar') === 'sidebar' ? 'selected' : '' ?>>Sidebar</option>
                    <option value="horizontal" <?= ($menu_config['style'] ?? 'sidebar') === 'horizontal' ? 'selected' : '' ?>>Horizontal</option>
                    <option value="compact" <?= ($menu_config['style'] ?? 'sidebar') === 'compact' ? 'selected' : '' ?>>Compact</option>
                </select>
            </div>
            <div class="setting-group">
                <label for="menu-position">Menu Position</label>
                <select id="menu-position" class="form-control">
                    <option value="left" <?= ($menu_config['position'] ?? 'left') === 'left' ? 'selected' : '' ?>>Left</option>
                    <option value="right" <?= ($menu_config['position'] ?? 'left') === 'right' ? 'selected' : '' ?>>Right</option>
                    <option value="top" <?= ($menu_config['position'] ?? 'left') === 'top' ? 'selected' : '' ?>>Top</option>
                </select>
            </div>
            <div class="setting-group">
                <label for="menu-color">Menu Color</label>
                <input type="color" id="menu-color" class="form-control" value="<?= $menu_config['color'] ?? '#007bff' ?>">
            </div>
            <div class="setting-group">
                <label for="menu-animation">Animation</label>
                <select id="menu-animation" class="form-control">
                    <option value="none" <?= ($menu_config['animation'] ?? 'slide') === 'none' ? 'selected' : '' ?>>None</option>
                    <option value="slide" <?= ($menu_config['animation'] ?? 'slide') === 'slide' ? 'selected' : '' ?>>Slide</option>
                    <option value="fade" <?= ($menu_config['animation'] ?? 'slide') === 'fade' ? 'selected' : '' ?>>Fade</option>
                    <option value="bounce" <?= ($menu_config['animation'] ?? 'slide') === 'bounce' ? 'selected' : '' ?>>Bounce</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="menu-preview-section">
        <h3>Menu Preview</h3>
        <div class="preview-container">
            <div class="preview-menu" id="preview-menu">
                <!-- Preview will be dynamically generated -->
            </div>
        </div>
    </div>
</div>

<!-- Menu Item Editor Modal -->
<div class="modal fade" id="menuItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu Item</h5>
                <button type="button" class="close" onclick="closeModal('menuItemModal')">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="menu-item-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-group">
                        <label for="item-label">Label *</label>
                        <input type="text" class="form-control" id="item-label" name="label" required>
                    </div>
                    <div class="form-group">
                        <label for="item-url">URL *</label>
                        <input type="text" class="form-control" id="item-url" name="url" required>
                    </div>
                    <div class="form-group">
                        <label for="item-icon">Icon</label>
                        <select class="form-control" id="item-icon" name="icon">
                            <option value="">No Icon</option>
                            <option value="dashboard">Dashboard</option>
                            <option value="users">Users</option>
                            <option value="cog">Settings</option>
                            <option value="file-alt">Content</option>
                            <option value="puzzle-piece">Modules</option>
                            <option value="server">System</option>
                            <option value="chart-bar">Analytics</option>
                            <option value="envelope">Messages</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item-badge">Badge</label>
                        <input type="text" class="form-control" id="item-badge" name="badge" placeholder="e.g., New, 5, Hot">
                    </div>
                    <div class="form-group">
                        <label for="item-target">Target</label>
                        <select class="form-control" id="item-target" name="target">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                            <option value="_parent">Parent Frame</option>
                            <option value="_top">Top Frame</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="item-visible" name="visible" checked>
                            Visible in menu
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="item-require-auth" name="require_auth" checked>
                            Require authentication
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('menuItemModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMenuItem()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeMenuBuilder();
    updatePreview();
});

function initializeMenuBuilder() {
    // Initialize drag and drop for available items
    const availableItems = document.getElementById('available-items');
    const currentMenu = document.getElementById('current-menu');
    
    // Make menu items draggable
    document.querySelectorAll('.menu-item').forEach(item => {
        item.draggable = true;
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    // Setup drop zones
    currentMenu.addEventListener('dragover', handleDragOver);
    currentMenu.addEventListener('drop', handleDrop);
    
    // Initialize sortable for current menu
    if (typeof Sortable !== 'undefined') {
        new Sortable(currentMenu, {
            group: 'menus',
            animation: 150,
            ghostClass: 'sortable-ghost',
            handle: '.menu-drag-handle',
            onEnd: function(evt) {
                updatePreview();
            }
        });
    }
    
    // Setup settings listeners
    document.querySelectorAll('.menu-settings select, .menu-settings input').forEach(input => {
        input.addEventListener('change', updatePreview);
    });
}

function handleDragStart(e) {
    e.dataTransfer.effectAllowed = 'copy';
    e.dataTransfer.setData('menuKey', e.target.dataset.key);
    e.target.classList.add('dragging');
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
}

function handleDrop(e) {
    e.preventDefault();
    const menuKey = e.dataTransfer.getData('menuKey');
    const menuItem = document.querySelector(`[data-key="${menuKey}"]`);
    
    if (menuItem) {
        addMenuItemToMenu(menuKey);
    }
}

function addMenuItemToMenu(menuKey) {
    const menuItem = document.querySelector(`[data-key="${menuKey}"]`);
    if (!menuItem) return;
    
    // Clone the menu item for the current menu
    const clonedItem = menuItem.cloneNode(true);
    clonedItem.classList.add('in-menu');
    
    // Add drag handle
    const dragHandle = document.createElement('div');
    dragHandle.className = 'menu-drag-handle';
    dragHandle.innerHTML = '<i class="fas fa-grip-vertical"></i>';
    clonedItem.insertBefore(dragHandle, clonedItem.firstChild);
    
    // Add edit and remove buttons
    const actionsDiv = document.createElement('div');
    actionsDiv.className = 'menu-item-actions in-menu';
    actionsDiv.innerHTML = `
        <button class="btn btn-sm btn-outline-primary" onclick="editMenuItem('${menuKey}')">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="removeMenuItem('${menuKey}')">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    const existingActions = clonedItem.querySelector('.menu-item-actions');
    if (existingActions) {
        existingActions.replaceWith(actionsDiv);
    } else {
        clonedItem.appendChild(actionsDiv);
    }
    
    // Add to current menu
    document.getElementById('current-menu').appendChild(clonedItem);
    updatePreview();
}

function removeMenuItem(menuKey) {
    const menuItem = document.querySelector(`#current-menu [data-key="${menuKey}"]`);
    if (menuItem && confirm('Are you sure you want to remove this menu item?')) {
        menuItem.remove();
        updatePreview();
    }
}

function editMenuItem(menuKey) {
    // Open modal with current menu item data
    const menuItem = document.querySelector(`#current-menu [data-key="${menuKey}"]`);
    if (menuItem) {
        // Populate modal fields
        document.getElementById('item-label').value = menuItem.querySelector('h4').textContent;
        // ... populate other fields
        
        // Open modal
        document.getElementById('menuItemModal').style.display = 'block';
    }
}

function previewMenuItem(menuKey) {
    const menuItem = document.querySelector(`[data-key="${menuKey}"]`);
    if (menuItem) {
        const url = menuItem.querySelector('h4').textContent;
        window.open(url, '_blank');
    }
}

function filterMenuItems() {
    const searchTerm = document.getElementById('menu-search').value.toLowerCase();
    const categoryFilter = document.getElementById('category-filter').value;
    
    document.querySelectorAll('#available-items .menu-item').forEach(item => {
        const label = item.querySelector('h4').textContent.toLowerCase();
        const category = item.dataset.category;
        
        const matchesSearch = label.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        item.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
    });
}

function expandAll() {
    document.querySelectorAll('.submenu-items').forEach(submenu => {
        submenu.style.display = 'block';
    });
}

function collapseAll() {
    document.querySelectorAll('.submenu-items').forEach(submenu => {
        submenu.style.display = 'none';
    });
}

function resetToDefault() {
    if (confirm('Are you sure you want to reset to the default menu configuration? This will overwrite your current settings.')) {
        loadDefaultMenu();
    }
}

function loadDefaultMenu() {
    fetch('<?= app_base_url('/admin/menu/load-default') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification('Failed to load default menu', 'error');
        }
    });
}

function saveMenuConfiguration() {
    const menuStructure = getMenuStructure();
    const menuSettings = getMenuSettings();
    
    const config = {
        structure: menuStructure,
        settings: menuSettings
    };
    
    fetch('<?= app_base_url('/admin/menu/save-configuration') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Menu configuration saved successfully', 'success');
        } else {
            showNotification('Failed to save menu configuration', 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving menu configuration', 'error');
    });
}

function getMenuStructure() {
    const menuItems = [];
    document.querySelectorAll('#current-menu .menu-item').forEach(item => {
        const menuItem = {
            key: item.dataset.key,
            label: item.querySelector('h4').textContent,
            url: item.dataset.url || '#',
            icon: item.dataset.icon || 'circle',
            visible: true,
            require_auth: true
        };
        
        // Add submenu items if any
        const submenuItems = [];
        item.querySelectorAll('.submenu-item').forEach(subitem => {
            submenuItems.push({
                key: subitem.dataset.key,
                label: subitem.textContent.trim(),
                url: subitem.dataset.url || '#',
                icon: subitem.dataset.icon || 'angle-right'
            });
        });
        
        if (submenuItems.length > 0) {
            menuItem.submenu = submenuItems;
        }
        
        menuItems.push(menuItem);
    });
    
    return menuItems;
}

function getMenuSettings() {
    return {
        style: document.getElementById('menu-style').value,
        position: document.getElementById('menu-position').value,
        color: document.getElementById('menu-color').value,
        animation: document.getElementById('menu-animation').value
    };
}

function updatePreview() {
    const previewMenu = document.getElementById('preview-menu');
    const menuStructure = getMenuStructure();
    const menuSettings = getMenuSettings();
    
    let previewHTML = '<div class="preview-menu-container">';
    
    menuStructure.forEach(item => {
        previewHTML += `
            <div class="preview-menu-item">
                <i class="fas fa-${item.icon}"></i>
                <span>${item.label}</span>
                ${item.badge ? `<span class="preview-badge">${item.badge}</span>` : ''}
            </div>
        `;
    });
    
    previewHTML += '</div>';
    previewMenu.innerHTML = previewHTML;
    
    // Apply styles to preview
    previewMenu.style.backgroundColor = menuSettings.color;
    previewMenu.className = `preview-menu style-${menuSettings.style} position-${menuSettings.position}`;
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function saveMenuItem() {
    // Save menu item changes
    const formData = new FormData(document.getElementById('menu-item-form'));
    
    fetch('<?= app_base_url('/admin/menu/save-item') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Menu item saved successfully', 'success');
            closeModal('menuItemModal');
            updatePreview();
        } else {
            showNotification('Failed to save menu item', 'error');
        }
    });
}
</script>

<style>
.menu-customization-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.menu-panel {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.panel-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-header h3 {
    margin: 0;
    font-size: 18px;
    color: #212529;
}

.panel-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.panel-controls input,
.panel-controls select {
    padding: 5px 10px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
}

.panel-content {
    padding: 20px;
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
}

.menu-items-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.menu-item {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    cursor: grab;
    transition: all 0.3s ease;
}

.menu-item:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.menu-item.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.menu-item.in-menu {
    background: white;
    border-color: #007bff;
}

.menu-item-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.menu-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.menu-item-info {
    flex: 1;
}

.menu-item-info h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #212529;
}

.menu-item-info p {
    margin: 0;
    font-size: 12px;
    color: #6c757d;
}

.menu-item-actions {
    display: flex;
    gap: 5px;
}

.menu-item-actions.in-menu {
    flex-direction: column;
    gap: 5px;
}

.menu-drag-handle {
    color: #6c757d;
    cursor: grab;
    margin-right: 10px;
}

.menu-drag-handle:hover {
    color: #007bff;
}

.submenu-items {
    margin-top: 10px;
    padding-left: 55px;
    display: none;
}

.submenu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    margin-bottom: 5px;
    cursor: pointer;
}

.submenu-item:hover {
    background: #f8f9fa;
}

.menu-structure {
    min-height: 300px;
    border: 2px dashed #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.menu-structure:not(:empty) {
    border-style: solid;
    border-color: #007bff;
}

.empty-menu {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-menu i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
}

.menu-settings {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.menu-settings h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.setting-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.setting-group label {
    font-weight: 500;
    color: #495057;
}

.menu-preview-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.menu-preview-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.preview-container {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}

.preview-menu {
    min-height: 100px;
    border-radius: 4px;
    padding: 10px;
}

.preview-menu-container {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.preview-menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
    white-space: nowrap;
}

.preview-badge {
    background: #dc3545;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
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
    max-width: 500px;
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
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
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

.sortable-ghost {
    opacity: 0.4;
}

@media (max-width: 768px) {
    .menu-customization-container {
        grid-template-columns: 1fr;
    }
    
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .panel-controls {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
}
</style>
