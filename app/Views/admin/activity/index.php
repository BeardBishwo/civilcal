<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Activity Logs'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #718096;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 2rem;
            color: #1a202c;
            font-weight: 700;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.875rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .activity-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 1.25rem;
            color: #1a202c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        tr:hover {
            background: #f7fafc;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .pagination {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-info {
            color: #718096;
            font-size: 0.875rem;
        }

        .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }

        .page-btn {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .page-btn:hover {
            background: #f7fafc;
        }

        .page-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Activity Logs</h1>
            <p>Monitor system and user activities in real-time</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Today</h3>
                <div class="value"><?php echo number_format($stats['today'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <h3>This Week</h3>
                <div class="value"><?php echo number_format($stats['week'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <h3>This Month</h3>
                <div class="value"><?php echo number_format($stats['month'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Activities</h3>
                <div class="value"><?php echo number_format($stats['total'] ?? 0); ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="/admin/activity">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" name="q" placeholder="Search activities..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level">
                            <option value="">All Levels</option>
                            <option value="INFO" <?php echo ($level ?? '') === 'INFO' ? 'selected' : ''; ?>>Info</option>
                            <option value="SUCCESS" <?php echo ($level ?? '') === 'SUCCESS' ? 'selected' : ''; ?>>Success</option>
                            <option value="WARNING" <?php echo ($level ?? '') === 'WARNING' ? 'selected' : ''; ?>>Warning</option>
                            <option value="ERROR" <?php echo ($level ?? '') === 'ERROR' ? 'selected' : ''; ?>>Error</option>
                        </select>
                    </div>
                    <div class="form-group" style="justify-content: flex-end;">
                        <label>&nbsp;</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="/admin/activity" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Activity Table -->
        <div class="activity-table">
            <div class="table-header">
                <h2>Recent Activities</h2>
                <a href="/admin/activity/export<?php echo !empty($dateFilter) ? '?date=' . urlencode($dateFilter) : ''; ?>" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>

            <?php if (!empty($activities)): ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($activity['user'], 0, 1)); ?>
                                </div>
                                <span><?php echo htmlspecialchars($activity['user']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($activity['action']); ?></td>
                        <td>
                            <?php 
                            $details = is_array($activity['details']) ? $activity['details'] : [];
                            if (!empty($details)) {
                                echo '<small>' . htmlspecialchars(substr(json_encode($details), 0, 50)) . '...</small>';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($activity['timestamp']); ?></td>
                        <td>
                            <?php 
                            $levelClass = 'badge-info';
                            $levelText = strtolower($activity['level'] ?? 'info');
                            if ($levelText === 'success') $levelClass = 'badge-success';
                            elseif ($levelText === 'warning') $levelClass = 'badge-warning';
                            elseif ($levelText === 'error') $levelClass = 'badge-danger';
                            ?>
                            <span class="badge <?php echo $levelClass; ?>">
                                <?php echo htmlspecialchars(ucfirst($levelText)); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <div class="pagination-info">
                    Showing <?php echo (($page - 1) * $perPage) + 1; ?> to <?php echo min($page * $perPage, $total); ?> of <?php echo $total; ?> activities
                </div>
                <div class="pagination-controls">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>" class="page-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    $totalPages = ceil($total / $perPage);
                    for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): 
                    ?>
                        <a href="?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>" 
                           class="page-btn <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>&level=<?php echo urlencode($level ?? ''); ?>&q=<?php echo urlencode($q ?? ''); ?>&date=<?php echo urlencode($dateFilter ?? ''); ?>" class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p><strong>No activities found</strong></p>
                <p>There are no activities matching your filters.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
