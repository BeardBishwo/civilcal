<?php
/**
 * PREMIUM BLOG CATEGORIES MANAGEMENT
 * Manage hierarchies and category metadata
 */
$categories = $categories ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-folder-open"></i>
                    <h1>Blog Categories</h1>
                </div>
                <div class="header-subtitle">Organize your blog content</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 1.5rem; margin-top: 1.5rem;">
            
            <!-- Create/Edit Form Column -->
            <div class="sidebar-card-premium">
                <h5 class="sidebar-card-title" id="formTitle">
                    <i class="fas fa-plus-circle"></i> Add New Category
                </h5>
                <form id="categoryForm" onsubmit="handleCategorySubmit(event)">
                    <input type="hidden" id="categoryId" name="id">
                    
                    <div class="form-group mb-3">
                        <label class="form-label-sm">Name</label>
                        <input type="text" id="catName" name="name" class="form-input-premium" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label-sm">Slug (Optional)</label>
                        <input type="text" id="catSlug" name="slug" class="form-input-premium" placeholder="Auto-generated">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label-sm">Parent Category</label>
                        <select id="catParent" name="parent_id" class="form-input-premium">
                            <option value="">None</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label-sm">Description</label>
                        <textarea id="catDesc" name="description" rows="3" class="form-input-premium"></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label-sm">Banner Image</label>
                        <input type="text" id="catImage" name="image" class="form-input-premium" placeholder="Image URL">
                    </div>

                    <button type="submit" class="btn-create-premium" style="width: 100%;">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                    <button type="button" id="cancelBtn" onclick="resetForm()" class="btn-secondary-compact mt-2" style="width: 100%; display: none;">
                        Cancel
                    </button>
                </form>
            </div>

            <!-- Categories List Column -->
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-center">Slug</th>
                            <th class="text-center">Count</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-folder-open"></i>
                                        <p>No categories found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #1e293b;">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </div>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars(substr($cat['description'], 0, 50)) . (strlen($cat['description']) > 50 ? '...' : ''); ?>
                                    </td>
                                    <td class="text-center">
                                        <code style="font-size: 0.75rem; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;"><?php echo $cat['slug']; ?></code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-pill"><?php echo $cat['article_count']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons-compact">
                                            <button onclick='editCategory(<?php echo json_encode($cat); ?>)' class="btn-action-compact btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteCategory(<?php echo $cat['id']; ?>)" class="btn-action-compact btn-delete">
                                                <i class="fas fa-trash"></i>
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

<style>
.form-label-sm {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.35rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sidebar-card-premium {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    height: fit-content;
}
</style>

<script>
function handleCategorySubmit(e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const url = id 
        ? `<?php echo app_base_url('admin/blog/categories/update/'); ?>${id}`
        : '<?php echo app_base_url('admin/blog/categories/store'); ?>';
    
    const formData = new FormData(e.target);
    
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            Swal.fire('Error', 'Operation failed', 'error');
        }
    });
}

function editCategory(cat) {
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Category';
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catSlug').value = cat.slug;
    document.getElementById('catParent').value = cat.parent_id || '';
    document.getElementById('catDesc').value = cat.description;
    document.getElementById('catImage').value = cat.image;
    
    document.getElementById('cancelBtn').style.display = 'block';
}

function resetForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle"></i> Add New Category';
    document.getElementById('cancelBtn').style.display = 'none';
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the category.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo app_base_url('admin/blog/categories/delete/'); ?>${id}`, {method: 'POST'})
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
        }
    });
}
</script>
