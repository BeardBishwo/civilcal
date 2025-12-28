<?php
/**
 * MENU CREATION/EDITING INTERFACE
 */

$is_edit = $is_edit ?? false;
$menuData = $menu ?? [
    'id' => '',
    'name' => '',
    'location' => 'header',
    'items' => [],
    'is_active' => 1
];

$page_title = $is_edit ? 'Edit Menu - Admin Panel' : 'Create New Menu - Admin Panel';
$currentPage = 'content';

$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Menus', 'url' => app_base_url('admin/content/menus')],
    ['title' => $is_edit ? 'Edit Menu' : 'Create New Menu']
];
?>

<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>"></i>
                    <h1><?php echo $page_title; ?></h1>
                </div>
                <div class="header-subtitle">
                    <?php echo $is_edit ? 'Update your menu structure and links' : 'Create a new navigation menu for your site'; ?>
                </div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/content/menus'); ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Menus</span>
                </a>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="compact-action-bar">
            <div class="action-left">
                <div class="save-status" id="save-status">
                    <i class="fas fa-circle <?php echo $is_edit ? 'text-success' : 'text-warning'; ?>"></i>
                    <span><?php echo $is_edit ? 'All changes saved' : 'New menu'; ?></span>
                </div>
            </div>
            <div class="action-right">
                <button type="submit" form="menu-form" class="btn btn-primary btn-compact">
                    <i class="fas fa-save"></i>
                    <?php echo $is_edit ? 'Update Menu' : 'Save Menu'; ?>
                </button>
            </div>
        </div>

        <div class="create-content-single-column">
            <form id="menu-form" method="POST" action="<?php echo app_base_url('admin/content/menus/save'); ?>">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo $menuData['id']; ?>">
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="items" id="menu-items-json" value='<?php echo json_encode($menuData['items']); ?>'>

                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Menu Configuration</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="settings-grid" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group-modern">
                                <label for="menu-name" class="form-label required">Menu Name</label>
                                <input type="text" id="menu-name" name="name" class="form-control-modern" value="<?php echo htmlspecialchars($menuData['name']); ?>" required placeholder="e.g. Main Menu">
                            </div>

                            <div class="form-group-modern">
                                <label for="menu-location" class="form-label">Location</label>
                                <select id="menu-location" name="location" class="form-control-modern">
                                    <option value="header" <?php echo $menuData['location'] === 'header' ? 'selected' : ''; ?>>Header</option>
                                    <option value="footer" <?php echo $menuData['location'] === 'footer' ? 'selected' : ''; ?>>Footer</option>
                                    <option value="mobile" <?php echo $menuData['location'] === 'mobile' ? 'selected' : ''; ?>>Mobile</option>
                                </select>
                            </div>

                            <div class="form-group-modern">
                                <label class="form-label">Status</label>
                                <div class="toggle-switch-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="is_active" <?php echo $menuData['is_active'] ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="toggle-label">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items Builder -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Menu Items</h3>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMenuItem()">
                            <i class="fas fa-plus"></i> Add Link
                        </button>
                    </div>
                    <div class="card-body-clean">
                        <div id="menu-items-list" class="menu-builder-list">
                            <!-- Items will be injected here by JS -->
                        </div>
                        <div id="empty-menu-msg" class="empty-state-compact" style="display: none; padding: 2rem;">
                            <i class="fas fa-list-ul"></i>
                            <p>No menu items added yet. Click "Add Link" to start building your menu.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Reuse styles from create.php */
    .page-create-container { background: #f8f9fa; min-height: 100vh; padding-bottom: 5rem; }
    .compact-create-header { max-width: 960px; margin: 0 auto; padding: 2rem 1rem; display: flex; justify-content: space-between; align-items: center; }
    .header-title { display: flex; align-items: center; gap: 0.75rem; }
    .header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .header-title i { color: #4f46e5; font-size: 1.25rem; }
    .header-subtitle { color: #6b7280; font-size: 0.875rem; }
    
    .compact-action-bar { max-width: 960px; margin: 0 auto 2rem; background: white; padding: 0.75rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; border: 1px solid #e5e7eb; position: sticky; top: 1rem; z-index: 50; }
    
    .create-content-single-column { max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem; padding: 0 1rem; }
    .content-card { background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden; }
    .card-header-clean { padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 1rem; font-weight: 600; color: #111827; margin: 0; }
    .card-body-clean { padding: 1.5rem; }
    
    .form-group-modern { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
    .form-label.required::after { content: "*"; color: #ef4444; margin-left: 2px; }
    .form-control-modern { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s; }
    .form-control-modern:focus { outline: none; border-color: #4f46e5; ring: 2px #4f46e5; }

    /* Menu Builder Styles */
    .menu-builder-item { 
        display: grid; 
        grid-template-columns: auto 1fr 1.5fr 1fr auto; 
        gap: 1rem; 
        align-items: center; 
        padding: 1rem; 
        background: #f9fafb; 
        border: 1px solid #e5e7eb; 
        border-radius: 0.5rem; 
        margin-bottom: 0.75rem; 
        cursor: move;
    }
    .item-drag-handle { color: #9ca3af; cursor: grab; }
    .btn-remove-item { color: #ef4444; background: none; border: none; cursor: pointer; padding: 0.5rem; }
    .btn-remove-item:hover { background: #fee2e2; border-radius: 4px; }
    
    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 40px; height: 20px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
    input:checked + .slider { background-color: #48bb78; }
    input:checked + .slider:before { transform: translateX(20px); }
    .slider.round { border-radius: 20px; }
    .slider.round:before { border-radius: 50%; }
    .toggle-switch-wrapper { display: flex; align-items: center; gap: 0.5rem; }
    .toggle-label { font-size: 0.875rem; color: #6b7280; }
</style>

<script>
    let menuItems = <?php echo json_encode($menuData['items']); ?> || [];

    function renderMenuItems() {
        const list = document.getElementById('menu-items-list');
        const emptyMsg = document.getElementById('empty-menu-msg');
        
        list.innerHTML = '';
        
        if (menuItems.length === 0) {
            emptyMsg.style.display = 'block';
            return;
        }
        
        emptyMsg.style.display = 'none';
        
        menuItems.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'menu-builder-item';
            row.innerHTML = `
                <div class="item-drag-handle"><i class="fas fa-grip-vertical"></i></div>
                <div>
                    <input type="text" class="form-control-modern" placeholder="Label" value="${item.name || item.label || ''}" onchange="updateItem(${index}, 'name', this.value)">
                </div>
                <div>
                    <input type="text" class="form-control-modern" placeholder="URL" value="${item.url || ''}" onchange="updateItem(${index}, 'url', this.value)">
                </div>
                <div>
                    <input type="text" class="form-control-modern" placeholder="Icon (fa-icon)" value="${item.icon || ''}" onchange="updateItem(${index}, 'icon', this.value)">
                </div>
                <button type="button" class="btn-remove-item" onclick="removeItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            list.appendChild(row);
        });
        
        updateHiddenInput();
    }

    function addMenuItem() {
        menuItems.push({ name: '', url: '', icon: '' });
        renderMenuItems();
    }

    function removeItem(index) {
        menuItems.splice(index, 1);
        renderMenuItems();
    }

    function updateItem(index, key, value) {
        menuItems[index][key] = value;
        updateHiddenInput();
    }

    function updateHiddenInput() {
        document.getElementById('menu-items-json').value = JSON.stringify(menuItems);
    }

    document.addEventListener('DOMContentLoaded', renderMenuItems);
</script>
