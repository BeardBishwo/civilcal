<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Billing & Subscriptions</h2>
            <p class="text-muted mb-0">Manage subscription plans and payments</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i class="bi bi-plus-circle me-2"></i>Create Plan
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-2"></i>Export Reports
            </button>
        </div>
    </div>

    <!-- Billing Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">$' . number_format($stats['total_revenue'], 2) . '</div>
                            <small class="stat-label">All-time revenue</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monthly Recurring
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">$' . number_format($stats['monthly_recurring'], 2) . '</div>
                            <small class="stat-label">MRR</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up-arrow fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Subscribers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . number_format($stats['active_subscribers']) . '</div>
                            <small class="stat-label">Paying customers</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Conversion Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . $stats['conversion_rate'] . '%</div>
                            <small class="stat-label">Free to paid</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-percent fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Plans -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Plans</h6>
                </div>
                <div class="card-body">
                    <div class="row">';
                    
                    foreach ($plans as $plan) {
                        $content .= '
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card plan-card h-100 ' . ($plan['name'] === 'Professional' ? 'border-primary' : '') . '">
                                <div class="card-header text-center">
                                    <h5 class="card-title mb-1">' . htmlspecialchars($plan['name']) . '</h5>
                                    <div class="price">
                                        <span class="h2 text-primary">$' . number_format($plan['price_monthly'], 2) . '</span>
                                        <span class="text-muted">/month</span>
                                    </div>
                                    <div class="text-muted">or $' . number_format($plan['price_yearly'], 2) . '/year</div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">' . htmlspecialchars($plan['description']) . '</p>
                                    <ul class="list-unstyled">';
                                    
                                    foreach ($plan['features'] as $feature) {
                                        $content .= '<li class="mb-2"><i class="bi bi-check text-success me-2"></i>' . htmlspecialchars($feature) . '</li>';
                                    }
                                    
                                    $content .= '
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h5 mb-0">' . number_format($plan['subscribers']) . '</div>
                                            <small class="text-muted">Subscribers</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h5 mb-0 ' . ($plan['is_active'] ? 'text-success' : 'text-secondary') . '">
                                                ' . ($plan['is_active'] ? 'Active' : 'Inactive') . '
                                            </div>
                                            <small class="text-muted">Status</small>
                                        </div>
                                    </div>
                                    <div class="btn-group w-100 mt-3">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                        <button class="btn ' . ($plan['is_active'] ? 'btn-outline-warning' : 'btn-outline-success') . ' btn-sm">
                                            <i class="bi ' . ($plan['is_active'] ? 'bi-pause' : 'bi-play') . ' me-1"></i>
                                            ' . ($plan['is_active'] ? 'Disable' : 'Enable') . '
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    
                    $content .= '
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            foreach ($transactions as $transaction) {
                                $statusClass = $transaction['status'] === 'completed' ? 'bg-success' : 
                                             ($transaction['status'] === 'pending' ? 'bg-warning' : 'bg-danger');
                                
                                $content .= '
                                <tr>
                                    <td><code>' . $transaction['id'] . '</code></td>
                                    <td>' . htmlspecialchars($transaction['user']) . '</td>
                                    <td><span class="badge bg-info">' . $transaction['plan'] . '</span></td>
                                    <td><strong>$' . number_format($transaction['amount'], 2) . '</strong></td>
                                    <td>
                                        <span class="badge ' . $statusClass . '">' . ucfirst($transaction['status']) . '</span>
                                    </td>
                                    <td>' . date('M j, Y g:i A', strtotime($transaction['date'])) . '</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Send Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Refund">
                                                <i class="bi bi-arrow-counterclockwise"></i>
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
    </div>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createPlanForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Plan Name</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g., Professional, Enterprise">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" class="form-control" name="description" required placeholder="Brief description of the plan">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Monthly Price ($)</label>
                                <input type="number" class="form-control" name="price_monthly" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Yearly Price ($)</label>
                                <input type="number" class="form-control" name="price_yearly" step="0.01" min="0" required>
                                <small class="form-text text-muted">Automatically calculated as monthly * 10 (20% discount)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Features</label>
                        <div id="featuresContainer">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="Add a feature">
                                <button type="button" class="btn btn-outline-danger remove-feature" disabled>
                                    <i class="bi bi-dash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addFeature">
                            <i class="bi bi-plus me-1"></i>Add Feature
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="planActive" checked>
                            <label class="form-check-label" for="planActive">Activate plan immediately</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createPlanForm" class="btn btn-primary">Create Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add/remove features
document.getElementById("addFeature").addEventListener("click", function() {
    const container = document.getElementById("featuresContainer");
    const newFeature = document.createElement("div");
    newFeature.className = "input-group mb-2";
    newFeature.innerHTML = `
        <input type="text" class="form-control" name="features[]" placeholder="Add a feature">
        <button type="button" class="btn btn-outline-danger remove-feature">
            <i class="bi bi-dash"></i>
        </button>
    `;
    container.appendChild(newFeature);
    
    // Enable remove buttons when there are multiple features
    document.querySelectorAll(".remove-feature").forEach(btn => {
        btn.disabled = document.querySelectorAll(".remove-feature").length === 1;
    });
});

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("remove-feature")) {
        e.target.parentElement.remove();
        
        // Disable remove button if only one feature remains
        const removeButtons = document.querySelectorAll(".remove-feature");
        if (removeButtons.length === 1) {
            removeButtons[0].disabled = true;
        }
    }
});

// Auto-calculate yearly price
document.querySelector("input[name=\'price_monthly\']").addEventListener("input", function() {
    const monthly = parseFloat(this.value) || 0;
    const yearly = monthly * 10; // 20% discount
    document.querySelector("input[name=\'price_yearly\']").value = yearly.toFixed(2);
});

// Create plan form
document.getElementById("createPlanForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("/admin/subscriptions/create-plan", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Plan created successfully!");
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
