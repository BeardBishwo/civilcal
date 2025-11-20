<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Subscription Management</h1>
                <p class="text-muted">Manage plans, subscribers, and billing.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i class="fas fa-plus"></i> Create Plan
            </button>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Revenue</h6>
                        <h2 class="mb-0">$<?= number_format($stats['total_revenue'], 2) ?></h2>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Monthly Recurring</h6>
                        <h2 class="mb-0">$<?= number_format($stats['monthly_recurring'], 2) ?></h2>
                    </div>
                    <i class="fas fa-sync fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Active Subscribers</h6>
                        <h2 class="mb-0"><?= number_format($stats['active_subscribers']) ?></h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Conversion Rate</h6>
                        <h2 class="mb-0"><?= $stats['conversion_rate'] ?>%</h2>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Subscription Plans</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price (Monthly)</th>
                                <th>Price (Yearly)</th>
                                <th>Subscribers</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($plan['name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($plan['description']) ?></small>
                                    </td>
                                    <td>$<?= number_format($plan['price_monthly'], 2) ?></td>
                                    <td>$<?= number_format($plan['price_yearly'], 2) ?></td>
                                    <td><?= number_format($plan['subscribers']) ?></td>
                                    <td>
                                        <?php if ($plan['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $txn): ?>
                                <tr>
                                    <td><?= htmlspecialchars($txn['id']) ?></td>
                                    <td><?= htmlspecialchars($txn['user']) ?></td>
                                    <td><?= htmlspecialchars($txn['plan']) ?></td>
                                    <td>$<?= number_format($txn['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($txn['date']) ?></td>
                                    <td>
                                        <?php if ($txn['status'] === 'completed'): ?>
                                            <span class="badge badge-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= ucfirst($txn['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
