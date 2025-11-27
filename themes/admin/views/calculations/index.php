<div class="page-header">
    <h1 class="page-title"><i class="fas fa-history"></i> Calculations History</h1>
    <p class="page-description">View and manage all calculation records</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-calculator"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div class="stat-label">Total Calculations</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['week_count'] ?? 0); ?></div>
        <div class="stat-label">This Week</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['unique_users'] ?? 0); ?></div>
        <div class="stat-label">Unique Users</div>
    </div>
</div>

<!-- Calculations Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-table"></i>
            Recent Calculations
        </h5>
    </div>
    
    <div class="card-content" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Calculator Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($calculations)): ?>
                        <?php foreach ($calculations as $calc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($calc['username'] ?? $calc['email'] ?? 'Anonymous'); ?></td>
                                <td><?php echo htmlspecialchars($calc['calculator_type'] ?? 'N/A'); ?></td>
                                <td><?php echo isset($calc['created_at']) ? date('M d, Y H:i', strtotime($calc['created_at'])) : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: var(--admin-gray-500);">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block; color: var(--admin-gray-300);"></i>
                                No calculations found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>