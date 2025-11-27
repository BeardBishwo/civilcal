<style>
.subscription-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.subscription-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.stat-title {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-value {
    color: #1f2937;
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.stat-trend.up {
    color: #10b981;
}

.stat-trend.down {
    color: #ef4444;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.beautiful-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.beautiful-table th {
    background: #f9fafb;
    padding: 1rem 1.5rem;
    text-align: left;
    font-weight: 600;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}

.beautiful-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}

.beautiful-table tr:last-child td {
    border-bottom: none;
}

.beautiful-table tr:hover td {
    background: #f9fafb;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.success {
    background: #dcfce7;
    color: #166534;
}

.status-badge.warning {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.danger {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.info {
    background: #dbeafe;
    color: #1e40af;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-action.edit {
    background: #eff6ff;
    color: #3b82f6;
    border-color: #bfdbfe;
}

.btn-action.edit:hover {
    background: #dbeafe;
    border-color: #93c5fd;
}

.btn-action.delete {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fecaca;
}

.btn-action.delete:hover {
    background: #fee2e2;
    border-color: #fca5a5;
}

.btn-action.view {
    background: #f0fdf4;
    color: #10b981;
    border-color: #bbf7d0;
}

.btn-action.view:hover {
    background: #dcfce7;
    border-color: #86efac;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.plan-features {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.plan-feature {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}
</style>

<div class="subscription-header">
    <h1>💳 Subscription Management</h1>
    <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0 0 0; font-size: 1.1rem;">Manage subscription plans, track revenue, and monitor subscriber activity</p>
</div>

<!-- Stats Cards -->
<div class="subscription-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-title">Total Revenue</div>
        <div class="stat-value">$<?= number_format($stats['total_revenue'], 2) ?></div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i>
            <span>12.5% this month</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
            <i class="fas fa-sync"></i>
        </div>
        <div class="stat-title">Monthly Recurring</div>
        <div class="stat-value">$<?= number_format($stats['monthly_recurring'], 2) ?></div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i>
            <span>8.2% this month</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-title">Active Subscribers</div>
        <div class="stat-value"><?= number_format($stats['active_subscribers']) ?></div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i>
            <span>5.7% this month</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-title">Conversion Rate</div>
        <div class="stat-value"><?= $stats['conversion_rate'] ?>%</div>
        <div class="stat-trend down">
            <i class="fas fa-arrow-down"></i>
            <span>1.3% this month</span>
        </div>
    </div>
</div>

<!-- Subscription Plans Section -->
<div class="section-header">
    <h2 class="section-title">Subscription Plans</h2>
    <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
        <i class="fas fa-plus-circle"></i> Create New Plan
    </button>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 2rem;">
    <table class="beautiful-table">
        <thead>
            <tr>
                <th>Plan Name</th>
                <th>Price (Monthly)</th>
                <th>Price (Yearly)</th>
                <th>Subscribers</th>
                <th>Status</th>
                <th>Features</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans as $plan): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($plan['name']) ?></div>
                        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;"><?= htmlspecialchars($plan['description']) ?></div>
                    </td>
                    <td>
                        <?php if ($plan['price_monthly'] > 0): ?>
                            <div style="font-weight: 600; color: #1f2937;">$<?= number_format($plan['price_monthly'], 2) ?></div>
                        <?php else: ?>
                            <div style="font-weight: 600; color: #10b981;">Free</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($plan['price_yearly'] > 0): ?>
                            <div style="font-weight: 600; color: #1f2937;">$<?= number_format($plan['price_yearly'], 2) ?></div>
                            <?php if ($plan['price_monthly'] > 0): ?>
                                <div style="font-size: 0.75rem; color: #10b981;">
                                    Save $<?= number_format(($plan['price_monthly'] * 12) - $plan['price_yearly'], 2) ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="font-weight: 600; color: #10b981;">Free</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #1f2937;"><?= number_format($plan['subscribers']) ?></div>
                        <div style="font-size: 0.75rem; color: #6b7280;">
                            <?= round(($plan['subscribers'] / $stats['active_subscribers']) * 100, 1) ?>% of total
                        </div>
                    </td>
                    <td>
                        <?php if ($plan['is_active']): ?>
                            <span class="status-badge success">Active</span>
                        <?php else: ?>
                            <span class="status-badge danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="plan-features">
                            <?php foreach (array_slice($plan['features'], 0, 3) as $feature): ?>
                                <span class="plan-feature"><?= htmlspecialchars($feature) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($plan['features']) > 3): ?>
                                <span class="plan-feature">+<?= count($plan['features']) - 3 ?> more</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ($plan['is_active']): ?>
                                <button class="btn-action delete">
                                    <i class="fas fa-times"></i> Disable
                                </button>
                            <?php else: ?>
                                <button class="btn-action view">
                                    <i class="fas fa-check"></i> Enable
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Recent Transactions Section -->
<div class="section-header">
    <h2 class="section-title">Recent Transactions</h2>
    <button class="btn-primary">
        <i class="fas fa-download"></i> Export Data
    </button>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">
    <table class="beautiful-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $txn): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($txn['id']) ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #1f2937;"><?= htmlspecialchars($txn['user']) ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #1f2937;"><?= htmlspecialchars($txn['plan']) ?></div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: #1f2937;">$<?= number_format($txn['amount'], 2) ?></div>
                    </td>
                    <td>
                        <div style="color: #6b7280;"><?= date('M j, Y', strtotime($txn['date'])) ?></div>
                        <div style="font-size: 0.75rem; color: #9ca3af;"><?= date('g:i A', strtotime($txn['date'])) ?></div>
                    </td>
                    <td>
                        <?php if ($txn['status'] === 'completed'): ?>
                            <span class="status-badge success">Completed</span>
                        <?php elseif ($txn['status'] === 'pending'): ?>
                            <span class="status-badge warning">Pending</span>
                        <?php else: ?>
                            <span class="status-badge danger">Failed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action view">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; border: none;">
                <h5 class="modal-title" style="font-weight: 600;">Create New Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: white; font-size: 1.5rem;"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <form id="createPlanForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-weight: 500; color: #374151;">Plan Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g., Professional Plan" required style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-weight: 500; color: #374151;">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Describe what this plan offers..." required style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 500; color: #374151;">Monthly Price ($)</label>
                            <input type="number" class="form-control" name="price_monthly" step="0.01" min="0" placeholder="0.00" required style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 500; color: #374151;">Yearly Price ($)</label>
                            <input type="number" class="form-control" name="price_yearly" step="0.01" min="0" placeholder="0.00" required style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-weight: 500; color: #374151;">Features (one per line)</label>
                            <textarea class="form-control" name="features" rows="4" placeholder="e.g., 
Unlimited calculations
All calculators
Priority support
Export features" required style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked style="width: 1.25rem; height: 1.25rem;">
                        <label class="form-check-label" for="isActive" style="font-weight: 500; color: #374151; margin-left: 0.5rem;">Make this plan active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border: none; padding: 1.5rem; background: #f9fafb; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500;">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePlanBtn" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-save"></i> Create Plan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('savePlanBtn').addEventListener('click', function() {
        const form = document.getElementById('createPlanForm');
        const formData = new FormData(form);
        
        // Convert features textarea to array
        const featuresText = formData.get('features');
        const featuresArray = featuresText.split('\n').filter(feature => feature.trim() !== '');
        formData.set('features', JSON.stringify(featuresArray));
        
        // Submit via AJAX
        fetch('/admin/subscriptions/create-plan', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Plan created successfully!');
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('createPlanModal')).hide();
                // Reload page to show new plan
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the plan.');
        });
    });
});
</script>