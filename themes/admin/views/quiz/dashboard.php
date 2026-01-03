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
                            <div class="empty-state-compact" style="padding: 4rem;">
                                <i class="fas fa-history fs-1 mb-3 opacity-25"></i>
                                <h3>No recent activity discovered</h3>
                                <p class="text-muted">Attempts will appear here automatically as participants engage with exams.</p>
                            </div>
                        <?php else: ?>
                            <table class="table-compact premium-table">
                                <thead>
                                    <tr>
                                        <th class="col-title ps-4">Participant</th>
                                        <th class="col-title">Target Exam</th>
                                        <th class="col-status text-center">Score Result</th>
                                        <th class="col-status text-center">Current Status</th>
                                        <th class="col-date pe-4">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_attempts as $attempt): ?>
                                        <tr class="activity-row">
                                            <td class="ps-4">
                                                <div class="user-info-compact d-flex align-items-center gap-3">
                                                    <div class="avatar-shimmer" style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, var(--admin-primary), var(--admin-info)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                                        <?php echo strtoupper(substr($attempt['email'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($attempt['email']); ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem;">Student ID: #<?= rand(1000, 9999) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="exam-info">
                                                    <div class="fw-semibold text-primary" style="font-size: 0.9rem;"><?php echo htmlspecialchars($attempt['exam_title']); ?></div>
                                                    <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-tag me-1"></i> Technical Mock</div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="score-pill <?= $attempt['score'] >= 40 ? 'pass' : 'fail' ?>">
                                                    <?php echo number_format($attempt['score'], 1); ?>%
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill px-3 py-2 <?= $attempt['status'] == 'completed' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning border-warning-subtle' ?> border" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">
                                                    <i class="fas fa-<?= $attempt['status'] == 'completed' ? 'check-double' : 'spinner fa-spin'; ?> me-1"></i>
                                                    <?php echo strtoupper($attempt['status']); ?>
                                                </span>
                                            </td>
                                            <td class="pe-4">
                                                <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">
                                                    <i class="far fa-clock me-1"></i> <?php echo date('M j, g:i A', strtotime($attempt['started_at'])); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <style>
                                .premium-table { border-collapse: separate; border-spacing: 0 8px !important; margin-top: -8px; }
                                .activity-row { transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
                                .activity-row:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); background: white !important; }
                                .score-pill { display: inline-block; padding: 4px 12px; border-radius: 8px; font-weight: 800; font-size: 0.9rem; }
                                .score-pill.pass { background: rgba(16, 185, 129, 0.1); color: #10b981; }
                                .score-pill.fail { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
                                .avatar-shimmer { position: relative; overflow: hidden; }
                                .avatar-shimmer::after {
                                    content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
                                    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
                                    transform: rotate(45deg); animation: shimmer 3s infinite;
                                }
                                @keyframes shimmer { 0% { transform: translate(-30%, -30%) rotate(45deg); } 100% { transform: translate(30%, 30%) rotate(45deg); } }
                            </style>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
