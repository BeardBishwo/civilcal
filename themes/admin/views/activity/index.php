<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-history"></i> Activity Logs</h1>
            <p class="page-description">Track user activities and system events</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/activity/export'); ?>" class="btn btn-outline-primary">
                <i class="fas fa-download"></i> Export Logs
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['today'] ?? 0); ?></div>
        <div class="stat-label">Today's Activities</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['week'] ?? 0); ?></div>
        <div class="stat-label">This Week</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['month'] ?? 0); ?></div>
        <div class="stat-label">This Month</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon secondary">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div class="stat-label">Total Activities</div>
    </div>
</div>

<!-- Filters -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-filter"></i>
            Filter Options
        </h5>
    </div>
    <div class="card-content">
        <form method="GET" action="<?php echo app_base_url('/admin/activity'); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($dateFilter ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <select class="form-select" name="level">
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
                    <select class="form-select" name="per_page">
                        <option value="25" <?php echo ($perPage == 25 ? 'selected' : ''); ?>>25</option>
                        <option value="50" <?php echo ($perPage == 50 ? 'selected' : ''); ?>>50</option>
                        <option value="100" <?php echo ($perPage == 100 ? 'selected' : ''); ?>>100</option>
                        <option value="200" <?php echo ($perPage == 200 ? 'selected' : ''); ?>>200</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="<?php echo app_base_url('/admin/activity'); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Activity Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-list"></i>
            Activity Records
        </h5>
    </div>
    <div class="card-content p-0">
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="150">Timestamp</th>
                        <th width="120">User</th>
                        <th width="150">Action</th>
                        <th>Details</th>
                        <th width="120">IP Address</th>
                        <th width="100">Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-8">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No activities found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td>
                                    <code><?php echo htmlspecialchars($activity['timestamp'] ?? ''); ?></code>
                                </td>
                                <td><?php echo htmlspecialchars($activity['user'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars($activity['action'] ?? ''); ?></td>
                                <td>
                                    <?php if (!empty($activity['details'])): ?>
                                        <pre class="text-xs"><?php echo htmlspecialchars(json_encode($activity['details'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)); ?></pre>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($activity['ip_address'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge <?php echo strtolower($activity['level'] ?? 'info') === 'error' ? 'bg-danger' : (strtolower($activity['level'] ?? 'info') === 'warning' ? 'bg-warning' : 'bg-info'); ?>">
                                        <?php echo strtoupper($activity['level'] ?? 'INFO'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($activities)): ?>
            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        Showing <?php echo count($activities); ?> of <?php echo number_format($total ?? 0); ?> activities
                    </div>
                    <div class="btn-group">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/activity'); ?>?page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php if (count($activities) >= $perPage): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/activity'); ?>?page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>