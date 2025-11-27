<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Calculator Analytics - Bishwo Calculator';
// Remove the require_once for header.php
?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-calculator"></i> Calculator Analytics</h1>
            <p class="page-description">Detailed calculator usage and performance metrics.</p>
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

<!-- Calculator Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-period">All Time</div>
        </div>
        <div class="stat-value"><?php echo number_format($calculator_stats['total_calculations'] ?? 0); ?></div>
        <div class="stat-label">Total Calculations</div>
        <?php
        $growth = $calculator_stats['calculation_growth'] ?? 0;
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
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-period">Success Rate</div>
        </div>
        <div class="stat-value"><?php echo number_format($calculator_stats['success_rate'] ?? 0, 2); ?>%</div>
        <div class="stat-label">Success Rate</div>
        <?php
        $growth = $calculator_stats['success_rate_growth'] ?? 0;
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
                <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-period">Avg. Response</div>
        </div>
        <div class="stat-value"><?php echo $calculator_stats['avg_response_time'] ?? '0ms'; ?></div>
        <div class="stat-label">Avg. Response Time</div>
        <?php
        $growth = $calculator_stats['response_time_trend'] ?? 0;
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
        <div class="stat-value"><?php echo number_format($calculator_stats['monthly_calculations'] ?? 0); ?></div>
        <div class="stat-label">Monthly Calculations</div>
        <?php
        $growth = $calculator_stats['monthly_calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <div class="stat-trend <?php echo $is_positive ? 'positive' : 'negative'; ?>">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </div>
    </div>
</div>

<!-- Calculator Usage Chart -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area"></i>
            Calculator Usage Patterns (Last 90 Days)
        </h3>
    </div>
    <div class="card-content">
        <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
            <canvas id="calculatorUsageChart" style="width: 100%; height: 350px;"></canvas>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($calculator_stats['top_calculators'])): ?>
                        <?php foreach ($calculator_stats['top_calculators'] as $calc): ?>
                            <tr>
                                <td>
                                    <div class="table-cell-content">
                                        <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>"></i>
                                        <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                    </div>
                                </td>
                                <td><?php echo number_format($calc['uses'] ?? 0); ?></td>
                                <td class="success"><?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%</td>
                                <td><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                                <td>
                                    <span class="trend <?php echo ($calc['trend'] ?? 0) >= 0 ? 'positive' : 'negative'; ?>">
                                        <i class="fas <?php echo ($calc['trend'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                        <?php echo abs($calc['trend'] ?? 0); ?>%
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?php echo app_base_url('/admin/calculators/' . ($calc['id'] ?? 0) . '/edit'); ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i>
                                            <span>Edit</span>
                                        </a>
                                        <a href="<?php echo app_base_url('/admin/analytics/calculators/' . ($calc['id'] ?? 0) . '/details'); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-chart-bar"></i>
                                            <span>Details</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No calculator data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Calculator Performance Analysis -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-rocket"></i>
            Performance Analysis
        </h3>
    </div>
    <div class="card-content">
        <div class="performance-grid">
            <div class="performance-card">
                <h4 class="performance-title">
                    <i class="fas fa-bolt"></i>
                    Fastest Calculators
                </h4>
                <ul class="performance-list">
                    <?php if (!empty($calculator_stats['fastest_calcs'])): ?>
                        <?php foreach (array_slice($calculator_stats['fastest_calcs'], 0, 5) as $calc): ?>
                            <li class="performance-item">
                                <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                                <span class="performance-value success"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-data">No data available</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="performance-card">
                <h4 class="performance-title">
                    <i class="fas fa-hourglass-half"></i>
                    Slowest Calculators
                </h4>
                <ul class="performance-list">
                    <?php if (!empty($calculator_stats['slowest_calcs'])): ?>
                        <?php foreach (array_slice($calculator_stats['slowest_calcs'], 0, 5) as $calc): ?>
                            <li class="performance-item">
                                <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                                <span class="performance-value warning"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-data">No data available</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="performance-card">
                <h4 class="performance-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Highest Error Rate
                </h4>
                <ul class="performance-list">
                    <?php if (!empty($calculator_stats['highest_error_calcs'])): ?>
                        <?php foreach (array_slice($calculator_stats['highest_error_calcs'], 0, 5) as $calc): ?>
                            <li class="performance-item">
                                <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                                <span class="performance-value danger"><?php echo number_format($calc['error_rate'] ?? 0, 2); ?>%</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-data">No data available</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Category Performance -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-folder-open"></i>
            Category Performance
        </h3>
    </div>
    <div class="card-content">
        <div class="category-performance">
            <?php if (!empty($calculator_stats['by_category'])): ?>
                <?php foreach ($calculator_stats['by_category'] as $category): ?>
                    <div class="category-item">
                        <div class="category-header">
                            <h5><?php echo htmlspecialchars($category['name'] ?? 'Unknown Category'); ?></h5>
                            <span><?php echo number_format($category['count'] ?? 0); ?> calculations</span>
                        </div>
                        <div class="category-stats">
                            <div class="stat-item">
                                <span>Success Rate</span>
                                <span class="success"><?php echo number_format($category['success_rate'] ?? 0, 2); ?>%</span>
                            </div>
                            <div class="stat-item">
                                <span>Avg Time</span>
                                <span><?php echo $category['avg_time'] ?? '0ms'; ?></span>
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $category['percentage'] ?? 0; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">No category data available</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if data is available
    if (typeof initCalculatorAnalyticsCharts === 'function') {
        initCalculatorAnalyticsCharts(<?php echo json_encode($usage_data ?? []); ?>);
    }
});
</script>