<?php
// Activity Logs View - Compact Design
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-history"></i>
                    <h1>Activity Logs</h1>
                </div>
                <div class="header-subtitle">Track user activities and system events in real-time</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/activity/export'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-download"></i>
                    <span>Export Logs</span>
                </a>
            </div>
        </div>
        
        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['today'] ?? 0); ?></div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['week'] ?? 0); ?></div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['month'] ?? 0); ?></div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
                    <div class="stat-label">Total Activities</div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <!-- Filters -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-filter text-primary"></i> Filter Options
                    </div>
                </div>
                <div class="card-content-compact">
                    <form method="GET" action="<?php echo app_base_url('/admin/activity'); ?>">
                        <div class="grid-4-cols">
                            <div class="form-group">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($dateFilter ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Level</label>
                                <select class="form-control" name="level">
                                    <option value="">All Levels</option>
                                    <option value="INFO" <?php echo ($level === 'INFO' ? 'selected' : ''); ?>>INFO</option>
                                    <option value="WARNING" <?php echo ($level === 'WARNING' ? 'selected' : ''); ?>>WARNING</option>
                                    <option value="ERROR" <?php echo ($level === 'ERROR' ? 'selected' : ''); ?>>ERROR</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Search</label>
                                <input type="text" name="q" class="form-control" placeholder="Search activities..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Per Page</label>
                                <select class="form-control" name="per_page">
                                    <option value="25" <?php echo ($perPage == 25 ? 'selected' : ''); ?>>25</option>
                                    <option value="50" <?php echo ($perPage == 50 ? 'selected' : ''); ?>>50</option>
                                    <option value="100" <?php echo ($perPage == 100 ? 'selected' : ''); ?>>100</option>
                                    <option value="200" <?php echo ($perPage == 200 ? 'selected' : ''); ?>>200</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions d-flex justify-content-end gap-2 mt-3">
                             <a class="btn btn-light btn-compact" href="<?php echo app_base_url('/admin/activity'); ?>">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                            <button class="btn btn-primary btn-compact" type="submit">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Table -->
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-list text-primary"></i> Activity Records
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th width="160">Timestamp</th>
                                    <th width="150">User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th width="140">IP Address</th>
                                    <th width="100">Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activities)): ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state-compact py-5">
                                                <i class="fas fa-inbox text-muted fa-2x mb-3"></i>
                                                <p class="text-muted">No activities found matching criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td class="font-mono text-xs text-muted">
                                                <?php echo htmlspecialchars($activity['timestamp'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-circle-sm bg-light text-muted">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <span class="font-medium text-dark"><?php echo htmlspecialchars($activity['user'] ?? 'System'); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($activity['action'] ?? ''); ?></td>
                                            <td>
                                                <?php if (!empty($activity['details'])): ?>
                                                    <div class="code-snippet">
                                                        <?php echo htmlspecialchars(json_encode($activity['details'], JSON_UNESCAPED_SLASHES)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-sm text-muted"><?php echo htmlspecialchars($activity['ip_address'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php 
                                                    $lvl = strtolower($activity['level'] ?? 'info');
                                                    $badgeClass = 'bg-info text-white';
                                                    if ($lvl === 'error') $badgeClass = 'bg-danger text-white';
                                                    elseif ($lvl === 'warning') $badgeClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge-pill <?php echo $badgeClass; ?> text-xs">
                                                    <?php echo strtoupper($activity['level'] ?? 'INFO'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($activities)): ?>
                <div class="card-footer-compact d-flex justify-content-between align-items-center">
                    <div class="text-sm text-muted">
                        Showing <?php echo count($activities); ?> of <?php echo number_format($total ?? 0); ?>
                    </div>
                    <div class="pagination-compact">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-light btn-compact btn-sm" 
                               href="<?php echo app_base_url('/admin/activity'); ?>?page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light btn-compact btn-sm disabled" disabled>
                                <i class="fas fa-chevron-left"></i> Prev
                            </button>
                        <?php endif; ?>
                        
                        <?php if (count($activities) >= $perPage): ?>
                            <a class="btn btn-light btn-compact btn-sm" 
                               href="<?php echo app_base_url('/admin/activity'); ?>?page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light btn-compact btn-sm disabled" disabled>
                                Next <i class="fas fa-chevron-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
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
    .stat-icon.info { background: #4299e1; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.warning { background: #ed8936; }

    .stat-info { flex: 1; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.2; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; }

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
    
    .card-footer-compact {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content-compact { padding: 1.5rem; }
    
    /* FORM & GRID */
    .grid-4-cols {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    
    .form-group { margin-bottom: 0; }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
    }
    
    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-actions { display: flex; align-items: center; }
    .justify-content-end { justify-content: flex-end; }
    .gap-2 { gap: 0.5rem; }
    .mt-3 { margin-top: 1rem; }
    
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
    .font-mono { font-family: monospace; }
    .font-medium { font-weight: 500; }
    
    .avatar-circle-sm {
        width: 24px; height: 24px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem;
    }

    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
        line-height: 1;
    }
    .bg-info { background: #4299e1; }
    .bg-danger { background: #f56565; }
    .bg-warning { background: #ed8936; }
    .bg-success { background: #48bb78; }
    .text-white { color: white; }
    
    .code-snippet {
        font-family: monospace;
        font-size: 0.75rem;
        background: #f9fafb;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        color: #4b5563;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }
    
    .empty-state-compact { text-align: center; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .mb-3 { margin-bottom: 1rem; }
    
    .disabled { pointer-events: none; opacity: 0.6; }

    /* Responsive */
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        .grid-4-cols { grid-template-columns: 1fr; }
        .table-compact th, .table-compact td { padding: 0.5rem; }
        .compact-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>
<?php
/* End of Activity Logs View */
?>