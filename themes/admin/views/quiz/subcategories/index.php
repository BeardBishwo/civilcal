<?php
// themes/admin/views/quiz/subcategories/index.php
?>
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-diagram-2-fill text-info me-2"></i> Sub-Categories
                    </h1>
                    <p class="text-muted small mb-0">Manage sections/topics within main categories.</p>
                </div>
                <button class="btn btn-info text-white rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubModal">
                    <i class="bi bi-plus-lg me-1"></i> New Section
                </button>
            </div>
            
            <!-- Quick Filter -->
            <div class="card border-0 shadow-sm bg-info-subtle">
                <div class="card-body py-2">
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <label class="fw-bold text-info small text-uppercase mb-0"><i class="bi bi-filter me-1"></i>Filter Parent:</label>
                        <select class="form-select form-select-sm w-auto rounded-pill border-info" name="parent_id" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $selectedParent == $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase fw-bold">
                                <th class="ps-4" style="width: 80px;">Order</th>
                                <th>Section Name</th>
                                <th>Parent Category</th>
                                <th class="text-center">Premium</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subCatSortable">
                            <?php if (empty($subCategories)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-collection fs-1 d-block mb-3 opacity-25"></i>
                                        No sub-categories found. Select a parent to filter or add one.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subCategories as $sub): ?>
                                    <tr class="sub-item" data-id="<?= $sub['id'] ?>">
                                        <td class="ps-4 text-center text-muted cursor-move handle">
                                            <i class="bi bi-grip-vertical fs-5"></i>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($sub['title']) ?></h6>
                                            <span class="badge bg-light text-secondary border small mt-1">
                                                <?= $sub['question_count'] ?? 0 ?> Qs
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                <i class="bi bi-folder2-open me-1"></i> <?= htmlspecialchars($sub['parent_title']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input premium-toggle" type="checkbox" role="switch" 
                                                        data-id="<?= $sub['id'] ?>" 
                                                        <?= $sub['is_premium'] ? 'checked' : '' ?>>
                                                </div>
                                                <?php if ($sub['is_premium']): ?>
                                                    <small class="text-warning fw-bold"><i class="bi bi-coin"></i> <?= $sub['unlock_price'] ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-link text-danger p-0 delete-btn" data-id="<?= $sub['id'] ?>">
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
            
            <?php if ($selectedParent): ?>
                <div class="mt-3 text-end text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Drag to reorder within <strong><?= htmlspecialchars($parents[array_search($selectedParent, array_column($parents, 'id'))]['title'] ?? 'this category') ?></strong>.
                </div>
            <?php else: ?>
                <div class="mt-3 text-end text-muted small">
                    <i class="bi bi-exclamation-circle text-warning me-1"></i> Filter by parent to enable reordering.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSubModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-diagram-2 me-2"></i>New Sub-Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSubForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Category</label>
                        <select class="form-select form-select-lg" name="parent_id" required>
                            <option value="" disabled selected>Select Parent...</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $selectedParent == $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Section Title</label>
                        <input type="text" class="form-control form-control-lg" name="title" required placeholder="e.g. Soil Properties">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cover Image (Optional)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image" id="subImage" placeholder="Select image...">
                            <button class="btn btn-outline-secondary" type="button" onclick="MediaManager.open('subImage')">
                                <i class="bi bi-image"></i> Browse
                            </button>
                        </div>
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_premium" id="subPremiumCheck">
                                <label class="form-check-label fw-bold" for="subPremiumCheck">Premium Section?</label>
                            </div>
                            <div class="ms-4 collapse show" id="subPremiumOptions">
                                <label class="form-label small text-muted text-uppercase fw-bold">Unlock Price</label>
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
                <button type="button" class="btn btn-info text-white px-4 rounded-pill" onclick="saveSubCategory()">Create Section</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
<?php if ($selectedParent): ?>
new Sortable(document.getElementById('subCatSortable'), {
    animation: 150,
    handle: '.handle',
    onEnd: function (evt) {
        var order = [];
        document.querySelectorAll('.sub-item').forEach(function(el) {
            order.push(el.getAttribute('data-id'));
        });
        
        fetch('<?= app_base_url('admin/quiz/subcategories/reorder') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order: order})
        });
    }
});
<?php endif; ?>

function saveSubCategory() {
    const form = document.getElementById('addSubForm');
    if(!form.checkValidity()) { form.reportValidity(); return; }
    
    const formData = new FormData(form);
    fetch('<?= app_base_url('admin/quiz/subcategories/store') ?>', {
        method: 'POST', body: formData
    }).then(r=>r.json()).then(d=>{
        if(d.status==='success') location.reload();
        else alert(d.message || 'Error');
    });
}

document.querySelectorAll('.premium-toggle').forEach(el => {
    el.addEventListener('change', function() {
        fetch('<?= app_base_url('admin/quiz/subcategories/toggle-premium') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${this.dataset.id}&val=${this.checked ? 1 : 0}`
        }).then(r=>r.json()).then(d=>{
            if(d.status!=='success') { this.checked=!this.checked; alert('Failed'); }
            else location.reload();
        });
    });
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if(confirm('Delete this section?')) {
            fetch('<?= app_base_url('admin/quiz/subcategories/delete/') ?>'+this.dataset.id, {method:'POST'})
            .then(r=>r.json()).then(d=>{
                if(d.status==='success') location.reload();
            });
        }
    });
});
</script>
