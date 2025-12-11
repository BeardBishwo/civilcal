<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'User Analytics - Bishwo Calculator';
// Remove the require_once for header.php
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-users"></i>
                    <h1>User Analytics</h1>
                </div>
                <div class="header-subtitle">Detailed user metrics and growth analysis</div>
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
            <!-- New This Month -->
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($user_stats['new_this_month'] ?? 0); ?></div>
                    <div class="stat-label">New This Month</div>
                    <?php
                    $growth = $user_stats['new_users_growth'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($user_stats['active_users'] ?? 0); ?></div>
                    <div class="stat-label">Active Users (30d)</div>
                    <?php
                    $growth = $user_stats['active_user_growth'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Growth Rate -->
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($user_stats['growth_rate'] ?? 0, 2); ?>%</div>
                    <div class="stat-label">User Growth Rate</div>
                    <?php
                    $growth = $user_stats['growth_trend'] ?? 0;
                    $is_positive = $growth >= 0;
                    ?>
                    <div class="stat-trend <?php echo $is_positive ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo abs($growth); ?>%
                    </div>
                </div>
            </div>

            <!-- Total Users (Sum up role counts or use first role as proxy if total not provided directly) -->
             <?php 
             $totalUsers = 0;
             if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])) {
                 foreach ($user_stats['by_role'] as $r) $totalUsers += ($r['count'] ?? 0);
             }
             ?>
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-trend text-muted">
                        Total Registered
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="analytics-content-body">

            <!-- Growth Chart -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-chart-line text-primary"></i>
                        User Growth Patterns (Last 90 Days)
                    </div>
                </div>
                <div class="card-content-compact">
                    <div style="height: 350px; position: relative;">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Demographics Grid -->
            <div class="grid-2-cols mb-4">
                <!-- By Role -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-user-tag text-info"></i>
                            Users By Role
                        </div>
                    </div>
                    <div class="card-content-compact">
                        <?php if (!empty($user_stats['by_role'])): ?>
                            <div class="category-grid single-col">
                                <?php foreach ($user_stats['by_role'] as $role_data): ?>
                                    <div class="progress-item mb-3">
                                        <div class="flex-between mb-1">
                                            <span class="font-medium text-capitalize"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?></span>
                                            <span class="text-muted small"><?php echo number_format($role_data['count'] ?? 0); ?> (<?php echo number_format($role_data['percentage'] ?? 0, 1); ?>%)</span>
                                        </div>
                                        <div class="progress-bar-compact">
                                            <div class="progress-fill" style="width: <?php echo $role_data['percentage'] ?? 0; ?>%; background: #4299e1;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-compact p-0">
                                <p>No role data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- By Location -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-globe-americas text-success"></i>
                            Geographic Distribution
                        </div>
                    </div>
                    <div class="card-content-compact">
                        <?php if (!empty($user_stats['by_location'])): ?>
                            <div class="category-grid single-col">
                                <?php foreach ($user_stats['by_location'] as $location_data): ?>
                                    <div class="progress-item mb-3">
                                        <div class="flex-between mb-1">
                                            <span class="font-medium"><?php echo htmlspecialchars($location_data['country'] ?? 'Unknown'); ?></span>
                                            <span class="text-muted small"><?php echo number_format($location_data['count'] ?? 0); ?></span>
                                        </div>
                                        <div class="progress-bar-compact">
                                            <div class="progress-fill" style="width: <?php echo $location_data['percentage'] ?? 0; ?>%; background: #48bb78;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-compact p-0">
                                <p>No location data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Top Active Users -->
            <div class="page-card-compact">
                 <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-crown text-warning"></i>
                        Top Active Users
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
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
                                                <div class="flex-align-center">
                                                    <div class="user-avatar-sm">
                                                        <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span class="font-medium"><?php echo htmlspecialchars($user['username'] ?? 'Unknown User'); ?></span>
                                                        <span class="text-muted small" style="font-size: 0.75rem;"><?php echo ucfirst($user['role'] ?? 'user'); ?></span>
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
                                        <td colspan="5" class="text-center py-4">
                                            <div class="empty-state-compact">
                                                <i class="fas fa-info-circle"></i>
                                                <p>No active users data available</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    if (typeof initUserAnalyticsCharts === 'function') {
        initUserAnalyticsCharts(<?php echo json_encode($growth_data ?? []); ?>);
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
    .text-capitalize { text-transform: capitalize; }

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
    .mb-3 { margin-bottom: 1rem; }
    .mb-1 { margin-bottom: 0.25rem; }
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
    
    /* PROGRESS BARS & DEMOGRAPHICS */
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
    
    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .small { font-size: 0.8rem; }
    
    .single-col {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    /* USER AVATAR */
    .user-avatar-sm {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
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