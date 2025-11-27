<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-calculator"></i> Calculators Management</h1>
            <p class="page-description">Manage all calculators, formulas, and categories</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCalculatorModal">
                <i class="fas fa-plus-circle"></i> Add Calculator
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-download"></i> Export List
            </button>
        </div>
    </div>
</div>

<!-- Categories Filter -->
<div class="card">
    <div class="card-content">
        <div class="category-filters">
            <button class="btn btn-primary btn-sm active">All Categories</button>
            <button class="btn btn-outline-secondary btn-sm">Civil</button>
            <button class="btn btn-outline-secondary btn-sm">Electrical</button>
            <button class="btn btn-outline-secondary btn-sm">Structural</button>
            <button class="btn btn-outline-secondary btn-sm">HVAC</button>
            <button class="btn btn-outline-secondary btn-sm">Plumbing</button>
        </div>
    </div>
</div>

<!-- Calculators Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-table"></i>
            All Calculators
        </h5>
        <div class="card-actions">
            <input type="text" class="form-control form-control-sm" placeholder="Search calculators..." id="calculatorSearch">
            <select class="form-select form-select-sm">
                <option>All Status</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
        </div>
    </div>
    
    <div class="card-content" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Usage</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calculators as $calculator): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($calculator['name']); ?></div>
                                <small class="text-muted">ID: <?php echo $calculator['id']; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($categories[$calculator['category']] ?? $calculator['category']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($calculator['description']); ?></td>
                            <td>
                                <span class="badge <?php echo $calculator['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo ucfirst($calculator['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold"><?php echo number_format($calculator['usage_count']); ?></div>
                                    <small class="text-muted">uses</small>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($calculator['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="View Formula">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn <?php echo $calculator['status'] == 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?>" title="<?php echo $calculator['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fas <?php echo $calculator['status'] == 'active' ? 'fa-pause' : 'fa-play'; ?>"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Calculator Modal -->
<div class="modal fade" id="addCalculatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCalculatorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Calculator Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <?php foreach ($categories as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Formula</label>
                        <input type="text" class="form-control" name="formula" placeholder="e.g., length * width * height" required>
                        <small class="form-text text-muted">Enter the mathematical formula using input variables</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Input Fields (JSON)</label>
                                <textarea class="form-control" name="inputs" rows="4" required placeholder='[{"name": "length", "label": "Length", "type": "number", "unit": "m"}]'></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Output Fields (JSON)</label>
                                <textarea class="form-control" name="outputs" rows="4" required placeholder='[{"name": "volume", "label": "Volume", "unit": "m³"}]'></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addCalculatorForm" class="btn btn-primary">Add Calculator</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("addCalculatorForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("<?php echo app_base_url('/admin/calculators/add'); ?>", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    });
});
</script>