<?php
// Dashboard View - Compact Design
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-tachometer-alt"></i>
                    <h1>Dashboard Overview</h1>
                </div>
                <div class="header-subtitle">Welcome back! Here's what's happening with your platform today.</div>
            </div>
            <div class="header-actions">
                <button onclick="location.reload()" class="btn btn-light btn-compact">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 12.5% vs last month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 8.2% vs last month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></div>
                    <div class="stat-label">Calculations This Month</div>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 15.3% vs last month
                    </div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['active_modules'] ?? 0); ?></div>
                    <div class="stat-label">Active Modules</div>
                    <div class="stat-trend text-muted">
                        <i class="fas fa-minus"></i> Stable
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <div class="compact-grid">
                
                <!-- Left Column -->
                <div class="grid-col-8">
                    
                    <!-- Quick Actions -->
                     <div class="page-card-compact mb-4">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-bolt text-warning"></i> Quick Actions
                            </div>
                        </div>
                        <div class="card-content-compact">
                            <div class="quick-actions-grid-compact">
                                <a href="<?php echo app_base_url('/admin/users/create'); ?>" class="quick-action-btn">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <span>Add User</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/content/pages'); ?>" class="quick-action-btn">
                                    <i class="fas fa-file-alt text-success"></i>
                                    <span>New Page</span>
                                </a>
                                <button onclick="createBackup()" class="quick-action-btn">
                                    <i class="fas fa-download text-info"></i>
                                    <span>Backup</span>
                                </button>
                                <button onclick="checkSystemHealth()" class="quick-action-btn">
                                    <i class="fas fa-heartbeat text-danger"></i>
                                    <span>Health</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="page-card-compact">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-clock text-primary"></i> Recent Activity
                            </div>
                            <a href="<?php echo app_base_url('/admin/activity'); ?>" class="text-xs font-medium text-primary hover:underline">View All</a>
                        </div>
                        <div class="table-container">
                            <div class="table-wrapper">
                                <table class="table-compact">
                                    <tbody>
                                        <tr>
                                            <td width="40" class="text-center"><i class="fas fa-user-plus text-success"></i></td>
                                            <td>
                                                <div class="font-medium text-dark">New user registered</div>
                                                <div class="text-xs text-muted">john.doe@example.com</div>
                                            </td>
                                            <td class="text-right text-xs text-muted">5 mins ago</td>
                                        </tr>
                                        <tr>
                                            <td width="40" class="text-center"><i class="fas fa-calculator text-primary"></i></td>
                                            <td>
                                                <div class="font-medium text-dark">Concrete calculator used</div>
                                                <div class="text-xs text-muted">Anonymous user</div>
                                            </td>
                                            <td class="text-right text-xs text-muted">12 mins ago</td>
                                        </tr>
                                        <tr>
                                            <td width="40" class="text-center"><i class="fas fa-cog text-gray-400"></i></td>
                                            <td>
                                                <div class="font-medium text-dark">System settings updated</div>
                                                <div class="text-xs text-muted">admin@example.com</div>
                                            </td>
                                            <td class="text-right text-xs text-muted">1 hour ago</td>
                                        </tr>
                                        <tr>
                                            <td width="40" class="text-center"><i class="fas fa-puzzle-piece text-warning"></i></td>
                                            <td>
                                                <div class="font-medium text-dark">Analytics module activated</div>
                                                <div class="text-xs text-muted">admin@example.com</div>
                                            </td>
                                            <td class="text-right text-xs text-muted">2 hours ago</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="grid-col-4">
                    
                    <!-- Revenue Widget -->
                    <div class="page-card-compact mb-4">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-dollar-sign text-success"></i> Revenue
                            </div>
                            <a href="<?php echo app_base_url('/admin/subscriptions'); ?>" class="text-xs font-medium text-primary hover:underline">Details</a>
                        </div>
                        <div class="card-content-compact">
                            <div class="text-center py-3">
                                <div class="text-3xl font-bold text-dark">$2,450</div>
                                <div class="text-xs text-muted uppercase tracking-wide">Monthly Revenue</div>
                            </div>
                             <div class="table-container">
                                <table class="table-compact w-100">
                                    <tbody>
                                        <tr>
                                            <td class="text-sm">Active Subs</td>
                                            <td class="text-right font-medium">47</td>
                                        </tr>
                                         <tr>
                                            <td class="text-sm">New This Month</td>
                                            <td class="text-right font-medium text-success">+12</td>
                                        </tr>
                                    </tbody>
                                </table>
                             </div>
                        </div>
                    </div>

                    <!-- Error Monitoring -->
                     <div class="page-card-compact mb-4">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-exclamation-triangle text-danger"></i> System Health
                            </div>
                             <a href="<?php echo app_base_url('/admin/error-logs'); ?>" class="text-xs font-medium text-primary hover:underline">Logs</a>
                        </div>
                        <div class="card-content-compact">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-sm text-dark">Error Rate</span>
                                <span class="text-sm font-medium text-danger">1.2%</span>
                            </div>
                            <div class="progress-bar-compact bg-gray-100 rounded-full h-2 mb-3">
                                <div class="bg-red-500 h-2 rounded-full" style="width: 1.2%"></div>
                            </div>
                             <div class="grid-3-cols text-center">
                                <div>
                                    <div class="text-lg font-bold text-dark">12</div>
                                    <div class="text-xs text-muted">Errors</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-danger">4</div>
                                    <div class="text-xs text-muted">Critical</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-warning">8</div>
                                    <div class="text-xs text-muted">Warnings</div>
                                </div>
                             </div>
                        </div>
                    </div>
                    
                    <!-- Calculator Usage -->
                    <div class="page-card-compact">
                         <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-chart-pie text-info"></i> Top Calculators
                            </div>
                        </div>
                        <div class="table-container">
                             <table class="table-compact">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-sm font-medium">Concrete Volume</span>
                                                <span class="text-xs text-muted">35%</span>
                                            </div>
                                            <div class="progress-bar-compact bg-gray-100 rounded-full h-1 mt-1">
                                                <div class="bg-blue-500 h-1 rounded-full" style="width: 35%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-sm font-medium">Electrical Load</span>
                                                <span class="text-xs text-muted">25%</span>
                                            </div>
                                            <div class="progress-bar-compact bg-gray-100 rounded-full h-1 mt-1">
                                                <div class="bg-blue-400 h-1 rounded-full" style="width: 25%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="text-sm font-medium">Beam Design</span>
                                                <span class="text-xs text-muted">20%</span>
                                            </div>
                                            <div class="progress-bar-compact bg-gray-100 rounded-full h-1 mt-1">
                                                <div class="bg-blue-300 h-1 rounded-full" style="width: 20%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
        background: white;
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
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    .header-title i { font-size: 1.25rem; color: #1f2937; }

    .header-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
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
    .stat-icon.info { background: #4299e1; }

    .stat-info { flex: 1; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.2; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; }
    .stat-trend { font-size: 0.7rem; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem; }
    .text-success { color: #48bb78 !important; }
    .text-danger { color: #f56565 !important; }
    .text-muted { color: #6b7280 !important; }
    .text-primary { color: #667eea !important; }
    .text-warning { color: #ed8936 !important; }
    .text-info { color: #4299e1 !important; }
    .text-dark { color: #1f2937 !important; }

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
    
    .btn-light { background: white; color: #374151; border: 1px solid #d1d5db; }
    .btn-light:hover { background: #f3f4f6; }

    /* LAYOUT GRID */
    .compact-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .grid-col-8 { flex: 0 0 100%; max-width: 100%; }
    .grid-col-4 { flex: 0 0 100%; max-width: 100%; }
    
    @media (min-width: 992px) {
        .grid-col-8 { flex: 0 0 calc(66.666% - 0.75rem); max-width: calc(66.666% - 0.75rem); }
        .grid-col-4 { flex: 0 0 calc(33.333% - 0.75rem); max-width: calc(33.333% - 0.75rem); }
    }

    /* CARDS */
    .analytics-content-body { padding: 2rem; }
    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px;
        overflow: hidden;
    }
    .mb-4 { margin-bottom: 1.5rem; }

    .card-header-compact {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 48px;
    }

    .header-title-sm {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-content-compact { padding: 1.25rem; }

    /* TABLES */
    .table-container { padding: 0; }
    .table-wrapper { overflow-x: auto; }
    .table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .table-compact td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-gray-200, #e5e7eb); vertical-align: middle; }
    .table-compact tbody tr:last-child td { border-bottom: none; }
    
    /* UTILS */
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    .text-3xl { font-size: 1.875rem; }
    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .uppercase { text-transform: uppercase; }
    .tracking-wide { letter-spacing: 0.025em; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .w-100 { width: 100%; }
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .grid-3-cols { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    
    /* QUICK ACTIONS */
    .quick-actions-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
    }
    
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1rem;
        background: #f8f9fa;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        text-decoration: none;
        color: #4b5563;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .quick-action-btn:hover {
        background: white;
        border-color: #667eea;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .quick-action-btn i { font-size: 1.5rem; margin-bottom: 0.75rem; }
    .quick-action-btn span { font-weight: 500; font-size: 0.875rem; }

</style>

<script>
    // System health check simulation
    function checkSystemHealth() {
        showNotification('System health check running... All services are operational.', 'success');
    }
    
    // Backup simulation
    function createBackup() {
        showConfirmModal('Start Backup', 'Start system backup?', () => {
             showNotification('Backup started in background.', 'info');
        });
    }
</script>