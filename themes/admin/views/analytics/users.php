<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'User Analytics - Bishwo Calculator';
// Remove the require_once for header.php
?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-users"></i> User Analytics</h1>
            <p class="page-description">Detailed user metrics and growth analysis.</p>
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
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon primary">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-period">Total</div>
                </div>
                <div class="stat-value"><?php echo number_format($role_data['count'] ?? 0); ?></div>
                <div class="stat-label"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?> Users</div>
                <?php
                $growth = $role_data['growth'] ?? 0;
                $is_positive = $growth >= 0;
                ?>
                <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
                    <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <?php echo abs($growth); ?>% from last month
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-period">This Month</div>
        </div>
        <div class="stat-value"><?php echo number_format($user_stats['new_this_month'] ?? 0); ?></div>
        <div class="stat-label">New This Month</div>
        <?php
        $growth = $user_stats['new_users_growth'] ?? 0;
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
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-period">Active (30d)</div>
        </div>
        <div class="stat-value"><?php echo number_format($user_stats['active_users'] ?? 0); ?></div>
        <div class="stat-label">Active Users (30d)</div>
        <?php
        $growth = $user_stats['active_user_growth'] ?? 0;
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
            <div class="stat-period">Growth Rate</div>
        </div>
        <div class="stat-value"><?php echo number_format($user_stats['growth_rate'] ?? 0, 2); ?>%</div>
        <div class="stat-label">User Growth Rate</div>
        <?php
        $growth = $user_stats['growth_trend'] ?? 0;
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
            <i class="fas fa-chart-line"></i>
            User Growth Patterns (Last 90 Days)
        </h3>
    </div>
    <div class="card-content">
        <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
            <canvas id="userGrowthChart" style="width: 100%; height: 350px;"></canvas>
        </div>
    </div>
</div>

<!-- User Demographics -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            User Demographics
        </h3>
    </div>
    <div class="card-content">
        <div class="demographics-grid">
            <div class="demographics-card">
                <h4 class="demographics-title">
                    <i class="fas fa-user-tag"></i>
                    By Role
                </h4>
                <div class="demographics-content">
                    <?php if (!empty($user_stats['by_role'])): ?>
                        <?php foreach ($user_stats['by_role'] as $role_data): ?>
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?></span>
                                    <span><?php echo number_format($role_data['count'] ?? 0); ?> (<?php echo number_format($role_data['percentage'] ?? 0, 1); ?>%)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $role_data['percentage'] ?? 0; ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">No demographics data available</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="demographics-card">
                <h4 class="demographics-title">
                    <i class="fas fa-globe-americas"></i>
                    Geographic Distribution
                </h4>
                <div class="demographics-content">
                    <?php if (!empty($user_stats['by_location'])): ?>
                        <?php foreach ($user_stats['by_location'] as $location_data): ?>
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span><?php echo htmlspecialchars($location_data['country'] ?? 'Unknown'); ?></span>
                                    <span><?php echo number_format($location_data['count'] ?? 0); ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $location_data['percentage'] ?? 0; ?>%; background: #34d399;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">No location data available</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Active Users -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-crown"></i>
            Top Active Users
        </h3>
    </div>
    <div class="card-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Calculations</th>
                        <th>Last Active</th>
                        <th>Account Age</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_users)): ?>
                        <?php foreach ($top_users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($user['username'] ?? 'Unknown User'); ?></div>
                                            <div class="user-role"><?php echo ucfirst($user['role'] ?? 'user'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($user['calculations'] ?? 0); ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['last_active'] ?? 'now')); ?></td>
                                <td><?php echo $user['account_age'] ?? 'N/A'; ?> days</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No active users data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if data is available
    if (typeof initUserAnalyticsCharts === 'function') {
        initUserAnalyticsCharts(<?php echo json_encode($growth_data ?? []); ?>);
    }
});
</script>