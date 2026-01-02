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
                        <div class="settings-grid" style="display: grid; grid-template-columns: 2fr 1fr auto auto; gap: 1.5rem; align-items: end;">
                            <div class="form-group-modern">
                                <label for="menu-name" class="form-label required">Menu Name</label>
                                <input type="text" id="menu-name" name="name" class="form-control-modern" value="<?php echo htmlspecialchars($menuData['name']); ?>" required placeholder="e.g. Main Menu">
                            </div>

                            <div class="form-group-modern">
                                <label for="menu-location" class="form-label">Location</label>
                                <select id="menu-location" name="location" class="form-control-modern">
                                    <optgroup label="Header Areas">
                                        <option value="top_header" <?php echo $menuData['location'] === 'top_header' ? 'selected' : ''; ?>>Top Notification Bar</option>
                                        <option value="header" <?php echo $menuData['location'] === 'header' ? 'selected' : ''; ?>>Main Header Navigation</option>
                                        <option value="mobile" <?php echo $menuData['location'] === 'mobile' ? 'selected' : ''; ?>>Mobile Menu</option>
                                    </optgroup>
                                    <optgroup label="Footer Columns">
                                        <option value="footer_1" <?php echo $menuData['location'] === 'footer_1' ? 'selected' : ''; ?>>Footer Column 1 (Left)</option>
                                        <option value="footer_2" <?php echo $menuData['location'] === 'footer_2' ? 'selected' : ''; ?>>Footer Column 2</option>
                                        <option value="footer_3" <?php echo $menuData['location'] === 'footer_3' ? 'selected' : ''; ?>>Footer Column 3</option>
                                        <option value="footer_4" <?php echo $menuData['location'] === 'footer_4' ? 'selected' : ''; ?>>Footer Column 4 (Right)</option>
                                    </optgroup>
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

                            <div class="form-group-modern">
                                <label class="form-label">Show Name</label>
                                <div class="toggle-switch-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" name="show_name" <?php echo ($menuData['show_name'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="toggle-label">Visible</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items Builder -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Menu Items</h3>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="uploadItem()">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem('link')">
                                <i class="fas fa-link"></i> Add Link
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="addItem('text')">
                                <i class="fas fa-font"></i> Add Text
                            </button>
                        </div>
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
        grid-template-columns: auto 1fr 1.5fr 1fr 60px auto; 
        gap: 0.75rem; 
        align-items: center; 
        padding: 0.75rem 1rem; 
        background: #f9fafb; 
        border: 1px solid #e5e7eb; 
        border-radius: 0.5rem; 
        margin-bottom: 0.75rem; 
        cursor: move;
    }
    .item-drag-handle { color: #9ca3af; cursor: grab; padding: 0.5rem; }
    .btn-remove-item { color: #ef4444; background: none; border: none; cursor: pointer; padding: 0.5rem; }
    .btn-remove-item:hover { background: #fee2e2; border-radius: 4px; }

    /* Text Item Specific */
    .menu-builder-item.text-block {
        grid-template-columns: auto 1fr auto auto;
    }
    .text-editor-container { grid-column: 2; width: 100%; }
    .text-editor-container .tox-tinymce { border-radius: 8px; border: 1px solid #d1d5db !important; }
    
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

    /* Small Switch for items */
    .switch.sm { width: 34px; height: 18px; }
    .switch.sm .slider:before { height: 14px; width: 14px; left: 2px; bottom: 2px; }
    .switch.sm input:checked + .slider:before { transform: translateX(16px); }
    .input-group-modern { display: flex; position: relative; }
    .input-group-modern .form-control-modern { border-top-right-radius: 0; border-bottom-right-radius: 0; }
    .btn-input-append { 
        padding: 0 0.75rem; 
        background: #f3f4f6; 
        border: 1px solid #d1d5db; 
        border-left: none; 
        border-top-right-radius: 0.5rem; 
        border-bottom-right-radius: 0.5rem; 
        cursor: pointer; 
        color: #4b5563; 
        transition: all 0.2s;
    }
    .btn-input-append:hover { background: #e5e7eb; color: #1f2937; }

    /* Drag and Drop styles */
    .sortable-ghost {
        opacity: 0.4;
        background: #e5e7eb !important;
        border: 2px dashed #4f46e5 !important;
    }
    .sortable-chosen {
        background: #fff !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    let menuItems = <?php echo json_encode($menuData['items']); ?> || [];

    function renderMenuItems() {
        const list = document.getElementById('menu-items-list');
        const emptyMsg = document.getElementById('empty-menu-msg');
        
        // Destroy existing tinymce instances to prevent memory leaks and ghost editors
        menuItems.forEach((item, index) => {
            if (item.type === 'text' && tinymce.get(`item-text-${index}`)) {
                tinymce.get(`item-text-${index}`).remove();
            }
        });

        list.innerHTML = '';
        
        if (menuItems.length === 0) {
            emptyMsg.style.display = 'block';
            return;
        }
        
        emptyMsg.style.display = 'none';
        
        menuItems.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = `menu-builder-item ${item.type === 'text' ? 'text-block' : 'link-block'}`;
            
            if (item.type === 'text') {
                row.innerHTML = `
                    <div class="item-drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <div class="text-editor-container">
                        <textarea id="item-text-${index}" class="menu-item-content">${item.content || ''}</textarea>
                    </div>
                    <div class="item-status-toggle" title="Toggle Visibility">
                        <label class="switch sm">
                            <input type="checkbox" ${item.is_active !== false ? 'checked' : ''} onchange="updateItem(${index}, 'is_active', this.checked)">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <button type="button" class="btn-remove-item" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            } else {
                row.innerHTML = `
                    <div class="item-drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <div>
                        <input type="text" class="form-control-modern" placeholder="Label" value="${item.name || item.label || ''}" onchange="updateItem(${index}, 'name', this.value)">
                    </div>
                    <div class="input-group-modern">
                        <input type="text" id="item-url-${index}" class="form-control-modern" placeholder="URL or Image paths" value="${item.url || ''}" onchange="updateItem(${index}, 'url', this.value)">
                        <button type="button" class="btn-input-append" onclick="browseUrl(${index})" title="Browse Media">
                            <i class="fas fa-images"></i>
                        </button>
                    </div>
                    <div>
                        <input type="text" class="form-control-modern" placeholder="Icon (fa-icon)" value="${item.icon || ''}" onchange="updateItem(${index}, 'icon', this.value)">
                    </div>
                    <div class="item-status-toggle" title="Toggle Visibility">
                        <label class="switch sm">
                            <input type="checkbox" ${item.is_active !== false ? 'checked' : ''} onchange="updateItem(${index}, 'is_active', this.checked)">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <button type="button" class="btn-remove-item" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            }
            row.setAttribute('data-id', index);
            list.appendChild(row);

            // Initialize TinyMCE for text blocks
            if (item.type === 'text') {
                initItemEditor(`item-text-${index}`, index);
            }
        });
        
        updateHiddenInput();
        initializeSortable();
    }

    function initializeSortable() {
        const list = document.getElementById('menu-items-list');
        if (!list) return;

        Sortable.create(list, {
            animation: 150,
            handle: '.item-drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function() {
                reorderItems();
            }
        });
    }

    function reorderItems() {
        const list = document.getElementById('menu-items-list');
        const rows = list.querySelectorAll('.menu-builder-item');
        const newOrder = [];
        
        rows.forEach(row => {
            const index = row.getAttribute('data-id');
            newOrder.push(menuItems[index]);
        });
        
        menuItems = newOrder;
        // We don't render again to avoid losing focus/input state, but we update the hidden input
        updateHiddenInput();
        
        // Update data-id attributes for future reorders
        rows.forEach((row, i) => {
            row.setAttribute('data-id', i);
        });
    }

    function initItemEditor(id, index) {
        setTimeout(() => {
            tinymce.init({
                selector: `#${id}`,
                height: 200,
                menubar: false,
                plugins: 'advlist autolink lists link image charmap preview anchor code fullscreen media table help wordcount',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media-library | code',
                setup: function (editor) {
                    editor.ui.registry.addButton('media-library', {
                        text: 'Media',
                        icon: 'image',
                        onAction: function () {
                            MediaModal.open(function(url) {
                                editor.insertContent(`<img src="${url}" style="max-width:100%; height:auto;" />`);
                            });
                        }
                    });
                    editor.on('change', function () {
                        updateItem(index, 'content', editor.getContent());
                    });
                },
                content_style: 'body { font-family:Inter,sans-serif; font-size:14px }'
            });
        }, 100);
    }

    function addItem(type = 'link') {
        if (type === 'text') {
            menuItems.push({ type: 'text', content: '', is_active: true });
        } else {
            menuItems.push({ type: 'link', name: '', url: '', icon: '', is_active: true });
        }
        renderMenuItems();
    }

    function uploadItem() {
        if (typeof MediaModal !== 'undefined') {
            MediaModal.open(function(url) {
                // If the URL is absolute from our storage, make it relative
                const relativeUrl = url.replace(window.location.origin, '').replace(/^\/Bishwo_Calculator\//, '');
                menuItems.push({ type: 'link', name: '', url: relativeUrl, icon: '', is_active: true });
                renderMenuItems();
            });
        } else {
            alert('Media Manager is not available.');
        }
    }

    function browseUrl(index) {
        if (typeof MediaModal !== 'undefined') {
            MediaModal.open(function(url) {
                const relativeUrl = url.replace(window.location.origin, '').replace(/^\/Bishwo_Calculator\//, '');
                const input = document.getElementById(`item-url-${index}`);
                if (input) {
                    // Append if there's already something, otherwise set
                    if (input.value) {
                         input.value += ', ' + relativeUrl;
                    } else {
                         input.value = relativeUrl;
                    }
                    updateItem(index, 'url', input.value);
                    renderMenuItems();
                }
            });
        }
    }

    function removeItem(index) {
        if (menuItems[index].type === 'text' && tinymce.get(`item-text-${index}`)) {
            tinymce.get(`item-text-${index}`).remove();
        }
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

<?php include __DIR__ . '/../partials/media_modal.php'; ?>
