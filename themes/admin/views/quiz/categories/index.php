<?php
/**
 * PREMIUM MAIN CATEGORIES MANAGEMENT
 * Professional, high-density layout with integrated creation form.
 */
$categories = $categories ?? [];
$stats = $stats ?? ['total' => 0, 'premium' => 0, 'total_questions' => 0];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1>Main Categories</h1>
                </div>
                <div class="header-subtitle"><?php echo $stats['total']; ?> Categories • <?php echo $stats['premium']; ?> Premium • <?php echo $stats['total_questions']; ?> Questions</div>
            </div>
            <!-- Stats in Header for Space Efficiency -->
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo $stats['total']; ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">PREMIUM</span>
                    <span class="value"><?php echo $stats['premium']; ?></span>
                </div>
            </div>
        </div>

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Create New Category</h5>
            <form id="addCategoryForm" class="creation-form">
                
                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 3; min-width: 200px;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Category Name" required>
                </div>
                
                <!-- Slug Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <i class="fas fa-link icon"></i>
                    <input type="text" name="slug" class="form-input-premium" placeholder="Slug (Optional)">
                </div>

                <!-- Image Input -->
                <div class="input-group-premium" style="flex: 2; min-width: 150px;">
                    <input type="text" name="image" id="catImage" class="form-input-premium" placeholder="Image URL" style="padding-left: 10px;">
                    <button type="button" class="btn-icon-inside" onclick="MediaManager.open('catImage')">
                        <i class="fas fa-image"></i>
                    </button>
                </div>

                <!-- Premium Toggle -->
                <div class="premium-toggle-group">
                    <label class="switch">
                        <input type="checkbox" name="is_premium" id="is_premium" onchange="togglePrice(this)">
                        <span class="slider round"></span>
                    </label>
                    <span class="toggle-label">PREMIUM</span>
                </div>

                <!-- Price Input -->
                <div id="price_input" class="input-group-premium" style="flex: 1; min-width: 80px; display:none;">
                    <input type="number" name="unlock_price" class="form-input-premium" placeholder="Coins" min="0">
                </div>

                <button type="button" onclick="saveCategory()" class="btn-create-premium">
                    <i class="fas fa-plus"></i> ADD
                </button>
            </form>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search categories..." id="category-search" onkeyup="filterCategories()">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint"><i class="fas fa-arrows-alt"></i> Drag handle to reorder</div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 60px;" class="text-center">ID</th>
                            <th style="width: 60px;" class="text-center">Order</th>
                            <th>Category Info</th>
                            <th class="text-center" style="width: 150px;">Questions</th>
                            <th class="text-center" style="width: 150px;">Premium</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categorySortable">
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="5" class="empty-cell">
                                <div class="empty-state-compact">
                                    <i class="fas fa-folder-open"></i>
                                    <h3>No categories found</h3>
                                    <p>Create your first root-level category above.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr class="category-item group" data-id="<?php echo $cat['id']; ?>">
                                    <td class="text-center align-middle">
                                        <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400"><?php echo $cat['id']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-500"><?php echo $cat['order_index']; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon">
                                                <?php if (!empty($cat['image_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($cat['image_path']); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-folder"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($cat['title']); ?></div>
                                                <div class="item-slug"><?php echo htmlspecialchars($cat['slug']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="metric-text"><?php echo $cat['question_count'] ?? 0; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="premium-control">
                                            <label class="switch scale-sm">
                                                <input type="checkbox" class="premium-toggle" data-id="<?php echo $cat['id']; ?>" <?php echo $cat['is_premium'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <?php if($cat['is_premium']): ?>
                                                <span class="price-tag"><i class="fas fa-coins"></i> <?php echo $cat['unlock_price']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <button onclick="editCategory(<?php echo $cat['id']; ?>)" class="action-btn-icon edit-btn" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button onclick="deleteCategory(<?php echo $cat['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
function togglePrice(el) {
    document.getElementById('price_input').style.display = el.checked ? 'block' : 'none';
}

async function saveCategory() {
    const form = document.getElementById('addCategoryForm');
    const title = form.querySelector('input[name="title"]').value;
    
    if(!title) { Swal.fire({ icon:'warning', title:'Missing Info', text:'Category Name is required.', timer:2000, showConfirmButton: false}); return; }

    const formData = new FormData(form);
    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/categories/store'); ?>', { method: 'POST', body: formData });
        const d = await response.json();
        if(d.status === 'success') {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Category Created' }).then(() => location.reload());
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Server Error', 'error'); }
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Delete Category?', text: "This will remove all sub-data.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#cbd5e1', confirmButtonText: 'Delete'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const res = await fetch('<?php echo app_base_url('admin/quiz/categories/delete/'); ?>' + id, {method:'POST'});
            const d = await res.json();
            if(d.status==='success') location.reload();
        }
    });
}

new Sortable(document.getElementById('categorySortable'), {
    animation: 150, handle: '.handle', ghostClass: 'bg-indigo-50',
    onEnd: function() {
        // 1. Get new order of IDs
        const rows = document.querySelectorAll('.category-item');
        const order = Array.from(rows).map(el => el.getAttribute('data-id'));
        
        // 2. Update visual Order indices immediately
        rows.forEach((row, index) => {
            // The Order column is the 3rd column (index 2)
            // We use querySelector to be safe or assuming strict structure
            const orderCell = row.querySelectorAll('td')[2].querySelector('span'); 
            if(orderCell) orderCell.innerText = index + 1;
        });

        // 3. Send to backend
        fetch('<?php echo app_base_url('admin/quiz/categories/reorder'); ?>', {
            method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({order: order})
        });
    }
});

document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const formData = new URLSearchParams();
        formData.append('id', this.dataset.id);
        formData.append('val', this.checked ? 1 : 0);
        await fetch('<?php echo app_base_url('admin/quiz/categories/toggle-premium'); ?>', {
           method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:formData
        });
        location.reload();
    });
});

function filterCategories() {
    const query = document.getElementById('category-search').value.toLowerCase();
    document.querySelectorAll('.category-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.indexOf(query) > -1 ? '' : 'none';
    });
}

function editCategory(id) {
    Swal.fire('Information', 'Inline editing coming soon.', 'info');
}
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Extracted from Users)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

.admin-wrapper-container {
    padding: 1rem;
    background: var(--admin-gray-50);
    min-height: calc(100vh - 70px);
}

.admin-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    overflow: hidden;
    padding-bottom: 2rem;
}

/* Header */
.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: flex; flex-direction: column; align-items: center;
    min-width: 80px;
}
.stat-pill.warning { background: rgba(252, 211, 77, 0.15); border-color: rgba(252, 211, 77, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Creation Toolbar (Single Row) */
.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem;
}
.creation-form {
    display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
}

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 40px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem; 
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.btn-icon-inside {
    position: absolute; right: 4px; top: 4px; bottom: 4px; width: 32px; 
    border: none; background: #f1f5f9; border-radius: 6px; color: #64748b; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.btn-icon-inside:hover { background: #e2e8f0; color: #4338ca; }

.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Switch Toggle */
.premium-toggle-group {
    display: flex; align-items: center; gap: 0.5rem; background: white; border: 1px solid #cbd5e1;
    height: 40px; padding: 0 0.75rem; border-radius: 8px;
}
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
.toggle-label { font-size: 0.7rem; font-weight: 700; color: #64748b; }

/* Filter Bar */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe;
}
.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem;
    border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af;
}
.drag-hint { font-size: 0.75rem; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 0.5rem; }

/* Table */
.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.table-compact td {
    padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.category-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon {
    width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center; overflow: hidden; color: #94a3b8;
}
.item-icon img { width: 100%; height: 100%; object-fit: cover; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-family: monospace; }

.metric-text { font-weight: 700; color: #64748b; font-size: 0.8rem; }

.premium-control { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.price-tag { font-size: 0.65rem; font-weight: 700; color: #d97706; display: flex; align-items: center; gap: 2px; }
.scale-sm { transform: scale(0.8); }

.handle { cursor: grab; color: #cbd5e1; }
.handle:hover { color: #667eea; }

.action-btn-icon {
    width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px;
    background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { transform: translateY(-1px); }
.edit-btn:hover { background: #667eea; color: white; border-color: #667eea; }
.delete-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }
.empty-state-compact p { font-size: 0.8rem; margin: 0; }

@media (max-width: 1024px) {
    .creation-form { flex-direction: column; align-items: stretch; }
    .input-group-premium { width: 100% !important; }
}
</style>
