<?php
// themes/admin/views/quiz/categories/index.php
?>
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-layers-fill text-primary me-2"></i> Main Categories
                </h1>
                <p class="text-muted small mb-0">Manage root-level syllabus nodes (Papers/Streams).</p>
            </div>
            <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-lg me-1"></i> New Category
            </button>
        </div>

        <!-- Category List -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase fw-bold">
                                <th class="ps-4" style="width: 80px;">Order</th>
                                <th style="width: 80px;">Icon</th>
                                <th>Category Name</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Premium</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categorySortable">
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                        No categories found. Create one to get started.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="category-item" data-id="<?= $cat['id'] ?>">
                                        <!-- Drag Handle -->
                                        <td class="ps-4 text-center text-muted cursor-move handle">
                                            <i class="bi bi-grip-vertical fs-5"></i>
                                        </td>
                                        
                                        <!-- Icon/Image -->
                                        <td>
                                            <?php if (!empty($cat['image_path'])): ?>
                                                <img src="<?= htmlspecialchars($cat['image_path']) ?>" class="rounded-3 shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-3 bg-primary-subtle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-folder-fill fs-5"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
    
                                        <!-- Title & Meta -->
                                        <td>
                                            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($cat['title']) ?></h6>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <small class="text-muted fst-italic"><?= htmlspecialchars($cat['slug']) ?></small>
                                                <span class="badge bg-light text-secondary border">
                                                    <?= $cat['question_count'] ?? 0 ?> Qs
                                                </span>
                                            </div>
                                        </td>
    
                                        <!-- Type -->
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                                                <?= strtoupper($cat['type']) ?>
                                            </span>
                                        </td>
    
                                        <!-- Premium Toggle -->
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input premium-toggle" type="checkbox" role="switch" 
                                                        data-id="<?= $cat['id'] ?>" 
                                                        <?= $cat['is_premium'] ? 'checked' : '' ?>>
                                                </div>
                                                <?php if ($cat['is_premium']): ?>
                                                    <span class="badge bg-warning text-dark border border-warning-subtle rounded-pill small">
                                                        <i class="bi bi-coin me-1"></i> <?= $cat['unlock_price'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
    
                                        <!-- Actions -->
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-link text-danger p-0 delete-btn" data-id="<?= $cat['id'] ?>">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 text-end text-muted small">
                <i class="bi bi-info-circle me-1"></i> Drag and drop rows to reorder categories.
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Main Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" class="form-control form-control-lg" name="title" required placeholder="e.g. Civil Engineering">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cover Image</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image" id="catImage" placeholder="Select image...">
                            <button class="btn btn-outline-secondary" type="button" onclick="MediaManager.open('catImage')">
                                <i class="bi bi-image"></i> Browse
                            </button>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_premium" id="isPremiumCheck">
                                <label class="form-check-label fw-bold" for="isPremiumCheck">Is Premium Content?</label>
                            </div>
                            
                            <div class="ms-4 collapse show" id="premiumOptions">
                                <label class="form-label small text-muted text-uppercase fw-bold">Unlock Price (Coins)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-warning text-dark border-warning"><i class="bi bi-coin"></i></span>
                                    <input type="number" class="form-control" name="unlock_price" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill" onclick="saveCategory()">Create Category</button>
            </div>
        </div>
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
// Initialize Sortable
new Sortable(document.getElementById('categorySortable'), {
    animation: 150,
    handle: '.handle',
    onEnd: function (evt) {
        var order = [];
        document.querySelectorAll('.category-item').forEach(function(el) {
            order.push(el.getAttribute('data-id'));
        });
        
        // Save Order
        fetch('<?= app_base_url('admin/quiz/categories/reorder') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order: order})
        });
    }
});

// Save Category
function saveCategory() {
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);

    fetch('<?= app_base_url('admin/quiz/categories/store') ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') location.reload();
        else alert(data.message || 'Error');
    });
}

// Toggle Premium
document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', function() {
        fetch('<?= app_base_url('admin/quiz/categories/toggle-premium') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${this.dataset.id}&val=${this.checked ? 1 : 0}`
        })
        .then(r => r.json())
        .then(d => {
            if(d.status !== 'success') {
                this.checked = !this.checked;
                alert('Update failed');
            } else {
                location.reload(); // Reload to update UI badges/prices visibility
            }
        });
    });
});

// Delete
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if(confirm('Delete this category and ALL sub-contents?')) {
            const id = this.dataset.id;
            fetch('<?= app_base_url('admin/quiz/categories/delete/') ?>' + id, {method: 'POST'}) // Check route format
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') location.reload();
                else alert('Delete failed');
            });
        }
    });
});
</script>
