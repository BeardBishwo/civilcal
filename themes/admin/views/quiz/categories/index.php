<?php
/**
 * PREMIUM CATEGORIES MANAGEMENT INTERFACE
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
            <div class="header-actions">
                <button class="btn btn-primary btn-compact" onclick="toggleCreateForm()">
                    <i class="fas fa-plus"></i>
                    <span>Quick Add</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-th-list"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Total Categories</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['premium']; ?></div>
                    <div class="stat-label">Premium Items</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['total_questions']; ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
            </div>
        </div>

        <!-- Integrated Creation Form (Collapsible/Inline) -->
        <div id="create-category-form" class="creation-panel" style="display: none; border-bottom: 1px solid #e5e7eb; background: #fcfcfd;">
            <div class="p-4">
                <h5 class="mb-3 font-bold text-slate-700"><i class="fas fa-plus-circle mr-2 text-indigo-500"></i>Create Main Category</h5>
                <form id="addCategoryForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Category Name</label>
                        <input type="text" name="title" class="form-control-compact" placeholder="e.g. Civil Engineering" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cover Image URL</label>
                        <div class="flex gap-2">
                            <input type="text" name="image" id="catImage" class="form-control-compact" placeholder="Select image...">
                            <button type="button" class="btn btn-icon btn-sm" onclick="MediaManager.open('catImage')"><i class="fas fa-image"></i></button>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 pt-6">
                            <input type="checkbox" name="is_premium" id="is_premium" class="w-4 h-4">
                            <label for="is_premium" class="text-xs font-bold text-slate-500 uppercase">Is Premium?</label>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Price (Coins)</label>
                            <input type="number" name="unlock_price" class="form-control-compact" value="0" min="0">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="saveCategory()" style="flex: 2;">Save Category</button>
                        <button type="button" class="btn btn-icon" onclick="toggleCreateForm()" style="flex: 1;"><i class="fas fa-times"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search categories..." id="category-search" onkeyup="filterCategories()">
                </div>
                <div class="text-xs text-slate-400 font-medium">
                    <i class="fas fa-info-circle mr-1"></i> Drag and drop handle to reorder
                </div>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" title="Table View"><i class="fas fa-table"></i></button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">
            <div class="table-container">
                <?php if (empty($categories)): ?>
                    <div class="empty-state-compact">
                        <i class="fas fa-inbox"></i>
                        <h3>No categories found</h3>
                        <p>Get started by creating your first root-level category.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Category Info</th>
                                    <th class="text-center" style="width: 150px;">Structure</th>
                                    <th class="text-center" style="width: 150px;">Premium</th>
                                    <th class="text-center" style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categorySortable">
                                <?php foreach ($categories as $index => $cat): ?>
                                    <tr class="page-row category-item" data-id="<?php echo $cat['id']; ?>">
                                        <td class="text-center align-middle">
                                            <div class="handle cursor-grab text-slate-300 hover:text-indigo-500 py-4">
                                                <i class="fas fa-grip-vertical"></i>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="icon-box-lg shrink-0 overflow-hidden bg-slate-100 border border-slate-200" style="width: 48px; height: 48px; border-radius: 12px;">
                                                    <?php if (!empty($cat['image_path'])): ?>
                                                        <img src="<?php echo htmlspecialchars($cat['image_path']); ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                            <i class="fas fa-folder text-xl"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <div class="page-title-compact text-slate-800 font-bold"><?php echo htmlspecialchars($cat['title']); ?></div>
                                                    <div class="text-xs text-slate-400 font-medium italic"><?php echo htmlspecialchars($cat['slug']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="flex items-center justify-center" style="min-height: 48px;">
                                                <span class="badge-pill-fancy bg-indigo-50 text-indigo-700 border-indigo-100">
                                                    <?php echo $cat['question_count'] ?? 0; ?> Questions
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="flex flex-col items-center justify-center gap-1" style="min-height: 48px;">
                                                <div class="form-switch">
                                                    <input type="checkbox" class="premium-toggle" 
                                                           data-id="<?php echo $cat['id']; ?>" 
                                                           <?php echo $cat['is_premium'] ? 'checked' : ''; ?>>
                                                </div>
                                                <?php if($cat['is_premium']): ?>
                                                    <span class="text-[10px] font-bold text-amber-600 uppercase flex items-center gap-1">
                                                        <i class="fas fa-coins text-[8px]"></i> <?php echo $cat['unlock_price']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="actions-compact justify-center items-center" style="min-height: 48px;">
                                                <button onclick="editCategory(<?php echo $cat['id']; ?>)" class="action-btn-icon edit-btn" title="Edit Properties">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button onclick="deleteCategory(<?php echo $cat['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
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
</div>

<style>
    .admin-wrapper-container { max-width: 1400px; margin: 0 auto; padding: 1.5rem; background: #f8fafc; min-height: calc(100vh - 70px); }
    .admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
    
    .compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; }
    .header-title { display: flex; align-items: center; gap: 0.75rem; }
    .header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 800; }
    .header-subtitle { font-size: 0.8rem; opacity: 0.8; font-weight: 500; margin-top: 2px; }
    
    .compact-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 1.5rem 2rem; background: #fcfcfd; border-bottom: 1px solid #e5e7eb; }
    .stat-item { display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: white; border-radius: 12px; border: 1px solid #e2e8f0; transition: transform 0.2s; }
    .stat-item:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; }
    .stat-icon.primary { background: #4f46e5; }
    .stat-icon.warning { background: #f59e0b; }
    .stat-icon.info { background: #06b6d4; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1; }
    .stat-label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; background: white; }
    .search-compact { position: relative; min-width: 300px; }
    .search-compact i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .search-compact input { width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; outline: none; transition: 0.2s; }
    .search-compact input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    
    .table-compact { width: 100%; border-collapse: collapse; }
    .table-compact th { padding: 1rem; background: #f8fafc; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #e2e8f0; }
    .table-compact td { border-bottom: 1px solid #f1f5f9; padding: 0.5rem 1rem; }
    .page-row:hover { background: #f8fafc; }
    
    .badge-pill-fancy { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid transparent; }
    .actions-compact { display: flex; gap: 0.5rem; }
    .action-btn-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: white; color: #64748b; transition: 0.2s; cursor: pointer; font-size: 0.8rem; }
    .action-btn-icon:hover { transform: translateY(-1px); }
    .edit-btn:hover { background: #4f46e5; color: white; border-color: #4f46e5; }
    .delete-btn:hover { background: #ef4444; color: white; border-color: #ef4444; }
    
    .form-control-compact { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; outline: none; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
function toggleCreateForm() {
    const formPanel = document.getElementById('create-category-form');
    if (formPanel.style.display === 'none') {
        formPanel.style.display = 'block';
        // Add a small opacity animation if desired
        formPanel.style.opacity = '0';
        setTimeout(() => { formPanel.style.transition = 'opacity 0.3s'; formPanel.style.opacity = '1'; }, 10);
    } else {
        formPanel.style.display = 'none';
    }
}

// Initialize Sortable
new Sortable(document.getElementById('categorySortable'), {
    animation: 200,
    handle: '.handle',
    ghostClass: 'bg-indigo-50',
    onEnd: function() {
        const order = Array.from(document.querySelectorAll('.category-item')).map(el => el.getAttribute('data-id'));
        fetch('<?php echo app_base_url('admin/quiz/categories/reorder'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order: order })
        });
    }
});

async function saveCategory() {
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);

    try {
        const response = await fetch('<?php echo app_base_url('admin/quiz/categories/store'); ?>', {
            method: 'POST',
            body: formData
        });
        const d = await response.json();
        if(d.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Category Created',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Network error or invalid response', 'error');
    }
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete all sub-categories and linked syllabuses!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await fetch('<?php echo app_base_url('admin/quiz/categories/delete/'); ?>' + id, { method: 'POST' });
            const d = await response.json();
            if(d.status === 'success') location.reload();
            else Swal.fire('Error', 'Deletion failed', 'error');
        }
    });
}

document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const id = this.getAttribute('data-id');
        const val = this.checked ? 1 : 0;
        const formData = new URLSearchParams();
        formData.append('id', id);
        formData.append('val', val);

        await fetch('<?php echo app_base_url('admin/quiz/categories/toggle-premium'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
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
    // Placeholder for future edit functionality if needed, 
    // for now we stick to creation and deletion as per dashboard style
    Swal.fire('Info', 'Inline editing coming soon. Use Delete/Re-create for now.', 'info');
}
</script>
