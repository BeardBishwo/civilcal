<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-graduation-cap"></i>
                    <h1>Quiz Dashboard</h1>
                </div>
                <div class="header-subtitle">Performance Overview • <?php echo number_format($stats['active_exams'] ?? 0); ?> active exams</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/quiz/exams/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>New Exam</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['total_questions'] ?? 0); ?></div>
                    <div class="stat-label">Question Bank</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['active_exams'] ?? 0); ?></div>
                    <div class="stat-label">Active Exams</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['total_attempts'] ?? 0); ?></div>
                    <div class="stat-label">Total Attempts</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">--</div>
                    <div class="stat-label">Today's Traffic</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Area -->
        <div class="pages-content">
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <div class="compact-toolbar" style="border-bottom: 1px solid var(--admin-gray-100); background: var(--admin-gray-50);">
                        <div class="toolbar-left">
                            <h3 style="margin:0; font-size: 1rem; font-weight: 600; color: var(--admin-gray-800);">Recent Exam Attempts</h3>
                        </div>
                        <div class="toolbar-right">
                            <a href="<?php echo app_base_url('admin/quiz/analytics'); ?>" class="btn btn-sm btn-outline-primary">
                                <span>View All</span>
                                <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                            </a>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <?php if (empty($recent_attempts)): ?>
                            <div class="empty-state-compact" style="padding: 3rem;">
                                <i class="fas fa-clock"></i>
                                <h3>No recent activity</h3>
                                <p>Attempts will appear here as users take exams.</p>
                            </div>
                        <?php else: ?>
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title">User</th>
                                        <th class="col-title">Exam</th>
                                        <th class="col-status">Score</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-date">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_attempts as $attempt): ?>
                                        <tr>
                                            <td>
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--admin-info); color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight:600;">
                                                        <?php echo strtoupper(substr($attempt['email'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact" style="font-size: 0.875rem;"><?php echo htmlspecialchars($attempt['email']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact" style="font-size: 0.875rem; font-weight:500;"><?php echo htmlspecialchars($attempt['exam_title']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: var(--admin-primary);"><?php echo number_format($attempt['score'], 1); ?>%</div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $attempt['status'] == 'completed' ? 'active' : 'warning'; ?>" style="font-size: 0.75rem;">
                                                    <i class="fas fa-<?php echo $attempt['status'] == 'completed' ? 'check-circle' : 'clock'; ?>"></i>
                                                    <?php echo ucfirst($attempt['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-compact" style="font-size: 0.8125rem;">
                                                    <?php echo date('M j, H:i', strtotime($attempt['started_at'])); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
