<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Calculator Analytics - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
// Remove the require_once for header.php
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-calculator"></i>
                    <h1>Calculator Analytics</h1>
                </div>
                <div class="header-subtitle">Detailed calculator usage and performance metrics</div>
            </div>
            <div class="header-actions">
                <button onclick="window.location.reload()" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); margin-right: 0.5rem;">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
                <a href="<?php echo app_base_url('/admin/analytics/reports'); ?>" class="btn btn-primary btn-compact" style="background: white; color: #667eea;">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <!-- Total Calculations -->
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($calculator_stats['total_calculations'] ?? 0); ?></div>
                    <div class="stat-label">Total Calculations</div>
                    <?php
                    $growth = $calculator_stats['calculation_growth'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($calculator_stats['success_rate'] ?? 0, 2); ?>%</div>
                    <div class="stat-label">Success Rate</div>
                    <?php
                    $growth = $calculator_stats['success_rate_growth'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Avg Response -->
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $calculator_stats['avg_response_time'] ?? '0ms'; ?></div>
                    <div class="stat-label">Avg. Response Time</div>
                    <?php
                    $growth = $calculator_stats['response_time_trend'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Monthly Calculations -->
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($calculator_stats['monthly_calculations'] ?? 0); ?></div>
                    <div class="stat-label">Monthly Calculations</div>
                    <?php
                    $growth = $calculator_stats['monthly_calculation_growth'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="analytics-content-body">

            <!-- Usage Chart -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-chart-area text-primary"></i>
                        Calculator Usage Patterns (Last 90 Days)
                    </div>
                </div>
                <div class="card-content-compact">
                    <div style="height: 350px; position: relative;">
                        <canvas id="calculatorUsageChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Performing Calculators -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-trophy text-warning"></i>
                        Top Performing Calculators
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
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
                                                <div class="flex-align-center">
                                                    <div class="stat-icon-sm">
                                                        <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>"></i>
                                                    </div>
                                                    <span class="font-medium"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($calc['uses'] ?? 0); ?></td>
                                            <td>
                                                <span class="status-badge status-published">
                                                    <?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%
                                                </span>
                                            </td>
                                            <td><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                                            <td>
                                                <span class="<?php echo ($calc['trend'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?> flex-align-center" style="gap: 0.25rem;">
                                                    <i class="fas <?php echo ($calc['trend'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                                    <?php echo abs($calc['trend'] ?? 0); ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('/admin/calculators/' . ($calc['id'] ?? 0) . '/edit'); ?>" class="action-btn-icon" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo app_base_url('/admin/analytics/calculators/' . ($calc['id'] ?? 0) . '/details'); ?>" class="action-btn-icon" title="Details">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state-compact">
                                                <i class="fas fa-info-circle"></i>
                                                <p>No calculator data available</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid-2-cols mb-4">
                <!-- Fastest Calculators -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-bolt text-success"></i>
                            Fastest Calculators
                        </div>
                    </div>
                    <div class="card-content-compact p-0">
                        <ul class="top-list">
                            <?php if (!empty($calculator_stats['fastest_calcs'])): ?>
                                <?php foreach (array_slice($calculator_stats['fastest_calcs'], 0, 5) as $calc): ?>
                                    <li class="top-list-item">
                                        <div class="top-item-info">
                                            <div class="top-item-name"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></div>
                                        </div>
                                        <div class="status-badge status-published">
                                            <?php echo $calc['avg_time'] ?? '0ms'; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="p-3 text-center text-muted">No data available</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Slowest Calculators -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-hourglass-half text-warning"></i>
                            Slowest Calculators
                        </div>
                    </div>
                    <div class="card-content-compact p-0">
                        <ul class="top-list">
                            <?php if (!empty($calculator_stats['slowest_calcs'])): ?>
                                <?php foreach (array_slice($calculator_stats['slowest_calcs'], 0, 5) as $calc): ?>
                                    <li class="top-list-item">
                                        <div class="top-item-info">
                                            <div class="top-item-name"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></div>
                                        </div>
                                        <div class="status-badge status-draft">
                                            <?php echo $calc['avg_time'] ?? '0ms'; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="p-3 text-center text-muted">No data available</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Category Performance -->
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-folder-open text-info"></i>
                        Category Performance
                    </div>
                </div>
                <div class="card-content-compact">
                    <div class="category-grid">
                        <?php if (!empty($calculator_stats['by_category'])): ?>
                            <?php foreach ($calculator_stats['by_category'] as $category): ?>
                                <div class="category-item-card">
                                    <div class="cat-header">
                                        <h5><?php echo htmlspecialchars($category['name'] ?? 'Unknown Category'); ?></h5>
                                        <span class="badge-pill"><?php echo number_format($category['count'] ?? 0); ?> calcs</span>
                                    </div>
                                    <div class="cat-stats">
                                        <div class="cat-stat">
                                            <span class="label">Success</span>
                                            <span class="value text-success"><?php echo number_format($category['success_rate'] ?? 0, 1); ?>%</span>
                                        </div>
                                        <div class="cat-stat">
                                            <span class="label">Time</span>
                                            <span class="value"><?php echo $category['avg_time'] ?? '0ms'; ?></span>
                                        </div>
                                    </div>
                                    <div class="progress-bar-compact">
                                        <div class="progress-fill" style="width: <?php echo $category['percentage'] ?? 0; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data text-center w-100">No category data available</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

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

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
        color: rgba(255,255,255,0.9);
    }

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

    /* STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #fff;
    }

    .stat-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .stat-icon-sm {
        width: 2rem;
        height: 2rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--admin-gray-100, #f3f4f6);
        color: var(--admin-gray-600, #4b5563);
        flex-shrink: 0;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.warning { background: #ed8936; }
    .stat-icon.info { background: #4299e1; }

    .stat-info { flex: 1; }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.25rem;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .text-success { color: #48bb78; }
    .text-danger { color: #f56565; }
    .text-warning { color: #ed8936; }
    .text-primary { color: #667eea; }
    .text-info { color: #4299e1; }
    .text-muted { color: #9ca3af; }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content-compact {
        padding: 1.25rem;
        flex: 1;
    }
    
    .mb-4 { margin-bottom: 1.5rem; }
    .p-0 { padding: 0 !important; }
    
    .grid-2-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    /* TABLES */
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
        vertical-align: middle;
        color: #4b5563;
    }

    .table-compact tbody tr:last-child td {
        border-bottom: none;
    }
    
    .flex-align-center {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .font-medium { font-weight: 500; color: #1f2937; }
    
    .actions-compact {
        display: flex;
        gap: 0.25rem;
    }

    .action-btn-icon {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        text-decoration: none;
    }

    .action-btn-icon:hover {
        background: #f3f4f6;
        color: #111827;
    }

    /* TOP LIST */
    .top-list {
        display: flex;
        flex-direction: column;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .top-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.1s;
    }
    
    .top-list-item:hover { background: #f8f9fa; }
    .top-list-item:last-child { border-bottom: none; }
    
    .top-item-name {
        font-weight: 500;
        color: #374151;
    }
    
    /* STATUS BADGE */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-published { background: rgba(72, 187, 120, 0.1); color: #48bb78; }
    .status-draft { background: rgba(237, 137, 54, 0.1); color: #ed8936; }
    
    /* CATEGORY GRID */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }
    
    .category-item-card {
        background: #f9fafb;
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
    }
    
    .cat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .cat-header h5 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
    }
    
    .badge-pill {
        background: white;
        border: 1px solid #e5e7eb;
        padding: 0.125rem 0.5rem;
        border-radius: 99px;
        font-size: 0.7rem;
        font-weight: 500;
        color: #6b7280;
    }
    
    .cat-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
    }
    
    .cat-stat {
        display: flex;
        flex-direction: column;
    }
    
    .cat-stat .label { color: #9ca3af; font-size: 0.7rem; }
    .cat-stat .value { font-weight: 600; color: #4b5563; }
    
    .progress-bar-compact {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #667eea;
        border-radius: 3px;
    }
    
    .empty-state-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        color: #9ca3af;
    }
    
    .empty-state-compact i { font-size: 2rem; margin-bottom: 0.5rem; }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .grid-2-cols { grid-template-columns: 1fr; }
    }
    
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        
        .compact-stats {
            grid-template-columns: 1fr;
            padding: 1.25rem;
        }
        
        .analytics-content-body { padding: 1.25rem; }
    }
</style>