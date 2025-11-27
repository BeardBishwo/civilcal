<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Analytics Overview - Bishwo Calculator';
// Remove the require_once for header.php
?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-line"></i> Analytics Overview</h1>
            <p class="page-description">Comprehensive analytics and insights for your platform.</p>
        </div>
        <div class="page-header-actions">
            <button onclick="window.location.reload()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
            <a href="<?php echo app_base_url('/admin/analytics/reports'); ?>" class="btn btn-primary">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-period">All Time</div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div class="stat-label">Total Users</div>
        <?php
        $growth = $stats['user_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-period">Last 30 Days</div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div class="stat-label">Active Users (30d)</div>
        <?php
        $growth = $stats['active_user_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-period">All Time</div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div class="stat-label">Total Calculations</div>
        <?php
        $growth = $stats['calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-period">This Month</div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></div>
        <div class="stat-label">Monthly Calculations</div>
        <?php
        $growth = $stats['monthly_calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area"></i>
            User Activity Trends
        </h3>
    </div>
    <div class="card-content">
        <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
            <canvas id="userActivityChart" style="width: 100%; height: 350px;"></canvas>
        </div>
    </div>
</div>

<!-- Top Performing Calculators -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-trophy"></i>
            Top Performing Calculators
        </h3>
    </div>
    <div class="card-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Calculator</th>
                        <th>Uses</th>
                        <th>Success Rate</th>
                        <th>Avg. Time</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_calculators)): ?>
                        <?php foreach ($top_calculators as $calc): ?>
                            <tr>
                                <td>
                                    <div class="table-cell-content">
                                        <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>"></i>
                                        <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                    </div>
                                </td>
                                <td><?php echo number_format($calc['uses'] ?? 0); ?></td>
                                <td><?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%</td>
                                <td><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                                <td>
                                    <span class="trend <?php echo ($calc['trend'] ?? 0) >= 0 ? 'positive' : 'negative'; ?>">
                                        <i class="fas fa-<?php echo ($calc['trend'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                        <?php echo abs($calc['trend'] ?? 0); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No calculator data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-compass"></i>
            Detailed Analytics
        </h3>
    </div>
    <div class="card-content">
        <div class="quick-actions-grid">
            <a href="<?php echo app_base_url('/admin/analytics/users'); ?>" class="quick-action-card">
                <div class="quick-action-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-action-content">
                    <h4>User Analytics</h4>
                    <p>Detailed user metrics and behavior</p>
                </div>
            </a>

            <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" class="quick-action-card">
                <div class="quick-action-icon success">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="quick-action-content">
                    <h4>Calculator Analytics</h4>
                    <p>Performance and usage statistics</p>
                </div>
            </a>

            <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" class="quick-action-card">
                <div class="quick-action-icon warning">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="quick-action-content">
                    <h4>Performance</h4>
                    <p>System performance metrics</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Chart.js Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if data is available
    if (typeof initAnalyticsCharts === 'function') {
        initAnalyticsCharts(<?php echo json_encode($charts ?? []); ?>);
    }
});
</script>