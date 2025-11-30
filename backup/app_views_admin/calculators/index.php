<?php
$content = '
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Calculators Management</h2>
            <p class="text-muted mb-0">Manage all calculators, formulas, and categories</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCalculatorModal">
                <i class="bi bi-plus-circle me-2"></i>Add Calculator
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-2"></i>Export List
            </button>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm active">All Categories</button>
                        <button class="btn btn-outline-secondary btn-sm">Civil</button>
                        <button class="btn btn-outline-secondary btn-sm">Electrical</button>
                        <button class="btn btn-outline-secondary btn-sm">Structural</button>
                        <button class="btn btn-outline-secondary btn-sm">HVAC</button>
                        <button class="btn btn-outline-secondary btn-sm">Plumbing</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculators Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Calculators</h6>
            <div class="d-flex">
                <input type="text" class="form-control form-control-sm me-2" placeholder="Search calculators..." id="calculatorSearch">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="calculatorsTable">
                    <thead class="table-light">
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
                    <tbody>';
                    
                    foreach ($calculators as $calculator) {
                        $content .= '
                        <tr>
                            <td>
                                <div class="fw-bold">' . htmlspecialchars($calculator['name']) . '</div>
                                <small class="text-muted">ID: ' . $calculator['id'] . '</small>
                            </td>
                            <td>
                                <span class="badge bg-info">' . htmlspecialchars($categories[$calculator['category']] ?? $calculator['category']) . '</span>
                            </td>
                            <td>' . htmlspecialchars($calculator['description']) . '</td>
                            <td>
                                <span class="badge ' . ($calculator['status'] == 'active' ? 'bg-success' : 'bg-secondary') . '">
                                    ' . ucfirst($calculator['status']) . '
                                </span>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold">' . number_format($calculator['usage_count']) . '</div>
                                    <small class="text-muted">uses</small>
                                </div>
                            </td>
                            <td>' . date('M j, Y', strtotime($calculator['created_at'])) . '</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="View Formula">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn ' . ($calculator['status'] == 'active' ? 'btn-outline-warning' : 'btn-outline-success') . '" title="' . ($calculator['status'] == 'active' ? 'Deactivate' : 'Activate') . '">
                                        <i class="bi ' . ($calculator['status'] == 'active' ? 'bi-pause' : 'bi-play') . '"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>';
                    }
                    
                    $content .= '
                    </tbody>
                </table>
            </div>
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
                                <select class="form-select" name="category" required>';
                                
                                foreach ($categories as $key => $value) {
                                    $content .= '<option value="' . $key . '">' . $value . '</option>';
                                }
                                
                                $content .= '
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
                                <textarea class="form-control" name="inputs" rows="4" required placeholder=\'[{"name": "length", "label": "Length", "type": "number", "unit": "m"}]\'></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Output Fields (JSON)</label>
                                <textarea class="form-control" name="outputs" rows="4" required placeholder=\'[{"name": "volume", "label": "Volume", "unit": "m³"}]\'></textarea>
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
    
    fetch("/admin/calculators/add", {
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
';

include __DIR__ . '/../../layouts/admin.php';
?>
