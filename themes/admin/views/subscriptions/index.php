<?php
// Subscriptions View - Compact Design
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-credit-card"></i>
                    <h1>Subscription Management</h1>
                </div>
                <div class="header-subtitle">Manage plans, track revenue, and monitor subscriber activity</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/subscriptions/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>Create New Plan</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 12.5% this month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-sync"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">$<?php echo number_format($stats['monthly_recurring'] ?? 0, 2); ?></div>
                    <div class="stat-label">Monthly Recurring</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 8.2% this month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['active_subscribers'] ?? 0); ?></div>
                    <div class="stat-label">Active Subscribers</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 5.7% this month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon danger">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $stats['conversion_rate'] ?? 0; ?>%</div>
                    <div class="stat-label">Conversion Rate</div>
                    <div class="stat-trend text-danger">
                        <i class="fas fa-arrow-down"></i> 1.3% this month
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <!-- Subscription Plans -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-layer-group text-primary"></i> Subscription Plans
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
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
                                <?php if (empty($plans)): ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state-compact py-5">
                                                <i class="fas fa-layer-group text-muted fa-2x mb-3"></i>
                                                <p class="text-muted">No subscription plans found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($plans as $plan): ?>
                                        <tr>
                                            <td>
                                                <div class="font-medium text-dark">
                                                    <?php echo htmlspecialchars($plan['name']); ?>
                                                </div>
                                                <div class="text-xs text-muted mt-1">
                                                    <?php echo htmlspecialchars($plan['description']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($plan['price_monthly'] > 0): ?>
                                                    <div class="font-medium text-dark">$<?php echo number_format($plan['price_monthly'], 2); ?></div>
                                                <?php else: ?>
                                                    <div class="font-medium text-success">Free</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($plan['price_yearly'] > 0): ?>
                                                    <div class="font-medium text-dark">$<?php echo number_format($plan['price_yearly'], 2); ?></div>
                                                    <?php if ($plan['price_monthly'] > 0): ?>
                                                        <div class="text-xs text-success">
                                                            Save $<?php echo number_format(($plan['price_monthly'] * 12) - $plan['price_yearly'], 2); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div class="font-medium text-success">Free</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="font-medium text-dark"><?php echo number_format($plan['subscribers']); ?></div>
                                                <div class="text-xs text-muted">
                                                    <?php 
                                                    $total_active = $stats['active_subscribers'] > 0 ? $stats['active_subscribers'] : 1;
                                                    echo round(($plan['subscribers'] / $total_active) * 100, 1); 
                                                    ?>% of total
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($plan['is_active']): ?>
                                                    <span class="badge-pill bg-green-100 text-green-800 text-xs">Active</span>
                                                <?php else: ?>
                                                    <span class="badge-pill bg-gray-100 text-gray-800 text-xs">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php foreach (array_slice($plan['features'], 0, 3) as $feature): ?>
                                                        <span class="badge-pill bg-gray-100 text-gray-600 text-xs"><?php echo htmlspecialchars($feature); ?></span>
                                                    <?php endforeach; ?>
                                                    <?php if (count($plan['features']) > 3): ?>
                                                        <span class="badge-pill bg-gray-100 text-gray-600 text-xs">+<?php echo count($plan['features']) - 3; ?> more</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                <a href="<?php echo app_base_url('/admin/subscriptions/edit/' . $plan['id']); ?>" 
                                                   class="btn btn-light btn-compact btn-sm" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                    <?php if ($plan['is_active']): ?>
                                                        <button class="btn btn-danger btn-compact btn-sm" title="Disable">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-success btn-compact btn-sm" title="Enable">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
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

            <!-- Recent Transactions -->
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-history text-primary"></i> Recent Transactions
                    </div>
                    <button class="btn btn-light btn-compact btn-sm">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
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
                                <?php if (empty($transactions)): ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state-compact py-5">
                                                <i class="fas fa-file-invoice-dollar text-muted fa-2x mb-3"></i>
                                                <p class="text-muted">No recent transactions.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($transactions as $txn): ?>
                                        <tr>
                                            <td>
                                                <div class="font-medium text-dark"><?php echo htmlspecialchars($txn['id']); ?></div>
                                            </td>
                                            <td>
                                                <div class="font-medium text-dark"><?php echo htmlspecialchars($txn['user']); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge-pill bg-blue-100 text-blue-800 text-xs text-uppercase"><?php echo htmlspecialchars($txn['plan']); ?></span>
                                            </td>
                                            <td>
                                                <div class="font-medium text-dark">$<?php echo number_format($txn['amount'], 2); ?></div>
                                            </td>
                                            <td>
                                                <div class="text-dark"><?php echo date('M j, Y', strtotime($txn['date'])); ?></div>
                                                <div class="text-xs text-muted"><?php echo date('g:i A', strtotime($txn['date'])); ?></div>
                                            </td>
                                            <td>
                                                <?php if ($txn['status'] === 'completed'): ?>
                                                    <span class="badge-pill bg-green-100 text-green-800 text-xs">Completed</span>
                                                <?php elseif ($txn['status'] === 'pending'): ?>
                                                    <span class="badge-pill bg-yellow-100 text-yellow-800 text-xs">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge-pill bg-red-100 text-red-800 text-xs">Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-compact btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
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
    </div>
</div>


<style>
    /* ========================================
       SHARED STYLES (Compact Admin Theme)
       ======================================== */
    
    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* HEADER */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left { flex: 1; }
    
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
    }

    .header-title i { font-size: 1.5rem; opacity: 0.9; }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
        color: rgba(255,255,255,0.9);
    }

    /* STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #fbfbfc;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.warning { background: #ed8936; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.danger { background: #f56565; }

    .stat-info { flex: 1; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.2; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; }
    .stat-trend { font-size: 0.7rem; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem; }
    .text-success { color: #48bb78 !important; }
    .text-danger { color: #f56565 !important; }

    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-compact:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .btn-light { background: white; color: #374151; border: 1px solid #d1d5db; }
    .btn-light:hover { background: #f3f4f6; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5a67d8; }
    .btn-danger { background: #f56565; color: white; }
    .btn-danger:hover { background: #e53e3e; }
    .btn-success { background: #48bb78; color: white; }
    .btn-success:hover { background: #38a169; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .mb-4 { margin-bottom: 1.5rem; }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 55px;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* TABLE */
    .table-container { padding: 0; }
    .table-wrapper { overflow-x: auto; }
    
    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: var(--admin-gray-50, #f8f9fa);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--admin-gray-700, #374151);
        border-bottom: 2px solid var(--admin-gray-200, #e5e7eb);
        white-space: nowrap;
    }

    .table-compact td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        vertical-align: top;
    }

    .table-compact tbody tr:hover { background: var(--admin-gray-50, #f8f9fa); }
    
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    .text-muted { color: #6b7280 !important; }
    .text-primary { color: #667eea !important; }
    .text-dark { color: #1f2937; }
    .font-medium { font-weight: 500; }
    
    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-weight: 600;
        line-height: 1;
    }
    .bg-green-100 { background: #f0fff4; color: #38a169; }
    .bg-gray-100 { background: #f7fafc; color: #4a5568; }
    .bg-blue-100 { background: #ebf8ff; color: #3182ce; }
    .bg-yellow-100 { background: #fffff0; color: #d69e2e; }
    .bg-red-100 { background: #fff5f5; color: #e53e3e; }
    
    .d-flex { display: flex; }
    .gap-1 { gap: 0.25rem; }
    .gap-2 { gap: 0.5rem; }
    .flex-wrap { flex-wrap: wrap; }
    
    .empty-state-compact { text-align: center; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .mb-3 { margin-bottom: 1rem; }

    /* Modal Tweaks */
    .modal-content { border-radius: 12px; }
    .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
    }
    /* EXTENDED MODAL STYLES */
    .compact-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .compact-input {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e5e7eb;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .compact-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Custom Input Group for Guaranteed Alignment */
    .compact-input-group {
        display: flex;
        align-items: stretch;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s;
        background: white;
    }

    .compact-input-group:focus-within {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .compact-addon-fixed {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-weight: 500;
        padding: 0 0.75rem;
        font-size: 1rem;
    }

    .compact-input-field {
        border: none;
        padding: 0.6rem 0.5rem;
        flex: 1;
        font-size: 0.95rem;
        color: #1f2937;
        width: 100%;
        outline: none;
        background: transparent;
        font-weight: 600;
    }

    .form-section-container {
        background: #f8fafc;
        border: 1px dashed #cbd5e1 !important;
        transition: all 0.2s;
    }
    
    .form-section-container:hover {
        border-color: #94a3b8 !important;
        background: #f1f5f9;
    }

    /* Custom Switch Style */
    .form-switch .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .form-switch .form-check-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
    }
    
    .text-xs { font-size: 0.75rem; }
    .me-2 { margin-right: 0.5rem; }
    .mb-1 { margin-bottom: 0.25rem; }
    .ms-1 { margin-left: 0.25rem; }
</style>

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
        fetch('<?php echo app_base_url('/admin/subscriptions/create-plan'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Plan created successfully!', 'success');
                // Close modal
                if(typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(document.getElementById('createPlanModal')).hide();
                } else {
                    // Fallback for no bootstrap object
                    document.getElementById('createPlanModal').classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
                // Reload page to show new plan
                location.reload();
            } else {
                showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while creating the plan.', 'error');
        });
    });
});
</script>