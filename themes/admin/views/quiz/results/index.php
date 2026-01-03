<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-chart-bar"></i>
                    <h1>Results & Analytics</h1>
                </div>
                <div class="header-subtitle"><?php echo $total; ?> attempts recorded</div>
            </div>
            <div class="header-actions">
                <!-- <button class="btn btn-outline-primary btn-compact">
                    <i class="fas fa-download"></i>
                    <span>Export</span>
                </button> -->
            </div>
        </div>

        <!-- Advanced Analytics Stats -->
        <div class="compact-stats-grid">
            <div class="compact-stat-card">
                <div class="stat-icon combined-purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats['total_attempts'] ?? 0); ?></div>
                    <div class="stat-label">Total Attempts</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats['completed_attempts'] ?? 0); ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-info">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo ($stats['avg_score'] ?? 0); ?>%</div>
                    <div class="stat-label">Average Score</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-warning">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo ($stats['highest_score'] ?? 0); ?>%</div>
                    <div class="stat-label">Highest Score</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
             <!-- Top Performers List -->
             <div class="col-lg-12">
                <div class="compact-card h-100">
                    <div class="compact-card-header">
                        <div class="header-title">Top Performers</div>
                    </div>
                    <div class="compact-card-body p-0">
                         <?php if (empty($top_performers)): ?>
                            <div class="p-3 text-center text-muted">No performers yet.</div>
                         <?php else: ?>
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">User</th>
                                        <th style="width: 25%;">Avg Score</th>
                                        <th style="width: 25%;">Attempts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_performers as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--admin-info); color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight:700;">
                                                    <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                                </div>
                                                <div class="page-info">
                                                    <div class="page-title-compact"><?php echo htmlspecialchars($user['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success"><?php echo number_format($user['avg_score'], 1); ?>%</span></td>
                                        <td><?php echo $user['attempts']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                         <?php endif; ?>
                    </div>
                </div>
             </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form method="GET" action="" class="d-flex align-items-center gap-2" id="filter-form">
                    <div class="search-compact">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search user or exam..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Content -->
        <div class="pages-content">
            <div id="results-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($attempts)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-chart-line"></i>
                            <h3>No activity found</h3>
                            <p>Results will appear here when users start taking exams.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title" style="width: 25%;">User</th>
                                        <th class="col-title" style="width: 25%;">Exam</th>
                                        <th class="col-status">Score</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-date">Date Started</th>
                                        <th class="col-date">Date Completed</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attempts as $row): ?>
                                        <tr>
                                            <td>
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-info); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight:700;">
                                                        <?php echo strtoupper(substr($row['username'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact"><?php echo htmlspecialchars($row['username']); ?></div>
                                                        <div class="page-slug-compact"><?php echo htmlspecialchars($row['email']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact" style="font-size: 0.9rem;"><?php echo htmlspecialchars($row['exam_title']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: var(--admin-primary); font-size: 1rem;">
                                                    <?php echo number_format($row['score'], 2); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                    $statusClass = 'secondary';
                                                    if ($row['status'] == 'completed') $statusClass = 'active';
                                                    elseif ($row['status'] == 'in_progress') $statusClass = 'warning';
                                                ?>
                                                <span class="status-badge status-<?php echo $statusClass; ?>">
                                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 4px;"></i>
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-compact" style="font-size: 0.8125rem;">
                                                    <?php echo date('Y-m-d H:i', strtotime($row['started_at'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-compact" style="font-size: 0.8125rem;">
                                                    <?php echo $row['completed_at'] ? date('Y-m-d H:i', strtotime($row['completed_at'])) : '-'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <button class="action-btn-icon" title="View Details (Coming Soon)" disabled style="opacity: 0.5; cursor: not-allowed;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                         <!-- Pagination -->
                         <?php if ($total > $limit): ?>
                            <div class="pagination-compact">
                                <div class="pagination-info">
                                    Showing <?php echo count($attempts); ?> of <?php echo $total; ?> entries
                                </div>
                                <div class="pagination-controls">
                                    <?php 
                                        $totalPages = ceil($total / $limit);
                                        $page = $page ?? 1;
                                    ?>
                                    
                                    <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                       class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                                       <?php echo $page <= 1 ? 'onclick="return false;"' : ''; ?>>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                           class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo $_GET['search'] ?? ''; ?>" 
                                       class="page-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
                                       <?php echo $page >= $totalPages ? 'onclick="return false;"' : ''; ?>>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
