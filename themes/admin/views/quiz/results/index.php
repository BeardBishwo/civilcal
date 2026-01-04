<?php
/**
 * PREMIUM RESULTS & ANALYTICS DASHBOARD
 * Modern, high-density results hub with user performance insights.
 */
$attempts = $attempts ?? [];
$top_performers = $top_performers ?? [];
$stats = $stats ?? ['total_attempts' => 0, 'completed_attempts' => 0, 'avg_score' => 0, 'highest_score' => 0, 'pass_rate' => 0];
$total = $total ?? 0;
$page = $page ?? 1;
$limit = $limit ?? 20;
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-chart-line"></i>
                    <h1>Results Hub</h1>
                </div>
                <div class="header-subtitle"><?php echo number_format($total); ?> Global Attempts • <?php echo $stats['pass_rate']; ?>% Completion Rate</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">ATTEMPTS</span>
                    <span class="value"><?php echo number_format($stats['total_attempts']); ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">AVG SCORE</span>
                    <span class="value"><?php echo $stats['avg_score']; ?></span>
                </div>
            </div>
        </div>

        <!-- Premium Stats Grid -->
        <div class="compact-stats-grid">
            <div class="compact-stat-card">
                <div class="stat-icon combined-purple">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats['total_attempts']); ?></div>
                    <div class="stat-label">Total Ingestion</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-success">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats['completed_attempts']); ?></div>
                    <div class="stat-label">Status: Completed</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-info">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['avg_score']; ?></div>
                    <div class="stat-label">System Average</div>
                </div>
            </div>
            <div class="compact-stat-card">
                <div class="stat-icon combined-warning">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['highest_score']; ?></div>
                    <div class="stat-label">Peak Performance</div>
                </div>
            </div>
        </div>

        <!-- Performance Spotlight -->
        <div class="spotlight-section">
            <div class="section-title">
                <i class="fas fa-trophy"></i> Top Performers Spotlight
            </div>
            <div class="spotlight-grid">
                <?php if (empty($top_performers)): ?>
                    <div class="empty-spotlight">Zero peak performers detected.</div>
                <?php else: ?>
                    <div class="table-container p-0 border-0 shadow-none">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>User Engineer</th>
                                    <th class="text-center">Average Marks</th>
                                    <th class="text-center">Total Missions</th>
                                    <th class="text-center">Standing</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_performers as $idx => $user): ?>
                                <tr>
                                    <td>
                                        <div class="item-info">
                                            <div class="user-avatar-sm">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($user['username']); ?></div>
                                                <div class="item-slug">Veteran Ingestor</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="performance-metric success"><?php echo number_format($user['avg_score'], 1); ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="metric-text"><?php echo $user['attempts']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if($idx == 0): ?>
                                            <span class="rank-badge gold"><i class="fas fa-medal"></i> #1</span>
                                        <?php else: ?>
                                            <span class="rank-badge">#<?php echo $idx + 1; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="compact-toolbar mt-4">
            <div class="toolbar-left">
                <form method="GET" action="" class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Filter by user, exam or email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </form>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint"><i class="fas fa-filter"></i> High-Density Feed</div>
            </div>
        </div>

        <!-- Results Feed -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>User Identifier</th>
                            <th>Mission Profile</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Status</th>
                            <th>Chronology</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attempts)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-inbox"></i>
                                        <h3>No activity data staged</h3>
                                        <p>Global exam results will populate here in real-time.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attempts as $row): ?>
                                <tr>
                                    <td>
                                        <div class="item-info">
                                            <div class="user-avatar-main">
                                                <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($row['username']); ?></div>
                                                <div class="item-slug"><?php echo htmlspecialchars($row['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mission-profile">
                                            <div class="mission-title"><?php echo htmlspecialchars($row['exam_title']); ?></div>
                                            <div class="mission-ref">ID: #<?php echo $row['exam_id']; ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="score-orb <?php echo ($row['score'] >= 1.0 || $row['score'] >= 40) ? 'pass' : 'fail'; ?>">
                                            <?php echo number_format($row['score'], 1); ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php 
                                            $statusClass = 'neutral';
                                            if ($row['status'] == 'completed') $statusClass = 'success';
                                            elseif ($row['status'] == 'in_progress') $statusClass = 'warning';
                                        ?>
                                        <span class="status-pill-premium <?php echo $statusClass; ?>">
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="chrono-info">
                                            <div class="time-main"><i class="far fa-clock"></i> <?php echo date('M d, H:i', strtotime($row['started_at'])); ?></div>
                                            <div class="time-sub">Completed: <?php echo $row['completed_at'] ? date('H:i', strtotime($row['completed_at'])) : '---'; ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="action-btn-icon" title="View Full Analysis">
                                            <i class="fas fa-eye" style="color: var(--premium-primary);"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <?php if ($total > $limit): ?>
                <div class="pagination-premium">
                    <div class="pagi-info">
                        Showing <strong><?php echo count($attempts); ?></strong> of <?php echo number_format($total); ?> Missions
                    </div>
                    <div class="pagi-controls">
                        <?php $totalPages = ceil($total / $limit); ?>
                        <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" class="pagi-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" class="pagi-btn <?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i == 2 || $i == $totalPages - 1): ?>
                                <span class="pagi-dots">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" class="pagi-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ========================================
   PREMIUM ANALYTICS DASHBOARD STYLES
   ======================================== */
:root {
    --premium-primary: #6366f1;
    --premium-secondary: #7c3aed;
    --premium-success: #10b981;
    --premium-warning: #f59e0b;
    --premium-danger: #ef4444;
    --premium-glass: rgba(255, 255, 255, 0.95);
}

.admin-wrapper-container { padding: 1rem; background: #f8fafc; min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; padding-bottom: 2rem; }

/* Global Font Override for Premium Feel */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.admin-wrapper-container *:not(i):not([class*="fa-"]) { font-family: 'Outfit', sans-serif; }

/* Header Architect */
.compact-header { 
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; 
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; 
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: white; letter-spacing: -0.5px; }
.header-subtitle { font-size: 0.85rem; opacity: 0.85; margin-top: 4px; font-weight: 500; }

.stat-pill { 
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; 
    padding: 0.5rem 1.25rem; display: flex; flex-direction: column; align-items: center; min-width: 90px;
}
.stat-pill.success { background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-pill .value { font-size: 1.15rem; font-weight: 800; line-height: 1.1; }

/* Stats Grid Architect */
.compact-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 1.5rem 2rem; background: #f1f5f9; }
.compact-stat-card { 
    background: white; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; 
    display: flex; align-items: center; gap: 1rem; transition: 0.2s;
}
.compact-stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border-color: #6366f1; }
.stat-icon { 
    width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
}
.combined-purple { background: #f5f3ff; color: #6366f1; }
.combined-success { background: #f0fdf4; color: #10b981; }
.combined-info { background: #eff6ff; color: #3b82f6; }
.combined-warning { background: #fffbeb; color: #f59e0b; }

.stat-value { font-size: 1.35rem; font-weight: 800; color: #1e293b; line-height: 1.1; }
.stat-label { font-size: 0.75rem; font-weight: 600; color: #64748b; margin-top: 2px; }

/* Spotlight Section */
.spotlight-section { padding: 2rem; }
.section-title { font-size: 0.85rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.spotlight-grid { background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }

/* Table Architect */
.table-container { padding: 0 2rem; }
.table-wrapper { border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th { 
    background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: 0.7rem; 
    font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;
}
.table-compact td { padding: 1rem 1.5rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.table-compact tr:last-child td { border-bottom: none; }
.table-compact tr:hover { background: #fdfdfd; }

/* Avatar System */
.user-avatar-sm { 
    width: 32px; height: 32px; border-radius: 8px; background: #6366f1; color: white; 
    display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;
}
.user-avatar-main { 
    width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
    color: white; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 800;
}

/* Item Info Pattern */
.item-info { display: flex; align-items: center; gap: 1rem; }
.item-title { font-weight: 700; color: #1e293b; font-size: 0.95rem; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-weight: 500; }

/* Mission Profile */
.mission-title { font-weight: 700; color: #475569; font-size: 0.9rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mission-ref { font-size: 0.7rem; color: #cbd5e1; font-family: monospace; }

/* Score & Status */
.score-orb { 
    display: inline-flex; align-items: center; justify-content: center; min-width: 45px; height: 32px; 
    border-radius: 8px; font-weight: 800; font-size: 0.95rem; padding: 0 10px;
}
.score-orb.pass { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
.score-orb.fail { background: #fff1f2; color: #e11d48; border: 1px solid #fecaca; }

.status-pill-premium { 
    font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; 
    display: inline-flex; align-items: center; gap: 4px;
}
.status-pill-premium.success { background: #10b981; color: white; }
.status-pill-premium.warning { background: #f59e0b; color: white; }
.status-pill-premium.neutral { background: #94a3b8; color: white; }

/* Chrono Info */
.time-main { font-size: 0.85rem; font-weight: 700; color: #475569; }
.time-sub { font-size: 0.7rem; color: #94a3b8; font-weight: 500; margin-top: 2px; }

/* Rank Badges */
.rank-badge { font-weight: 800; color: #94a3b8; font-size: 0.85rem; }
.rank-badge.gold { color: #f59e0b; background: #fff7ed; padding: 4px 10px; border-radius: 8px; border: 1px solid #fed7aa; }

/* Performance Metric */
.performance-metric { font-weight: 800; font-size: 1rem; }
.performance-metric.success { color: #10b981; }

/* Toolbar architect */
.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #eff6ff; border-top: 1px solid #bfdbfe; border-bottom: 1px solid #bfdbfe; }
.search-compact { position: relative; width: 100%; max-width: 400px; }
.search-compact i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.9rem; }
.search-compact input { 
    width: 100%; height: 40px; padding: 0 1rem 0 2.5rem; font-size: 0.9rem; border: 1px solid #bfdbfe; 
    border-radius: 10px; outline: none; background: white; transition: 0.2s;
}
.search-compact input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
.drag-hint { font-size: 0.75rem; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }

/* Pagination Premium */
.pagination-premium { 
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 0;
}
.pagi-info { font-size: 0.85rem; color: #64748b; }
.pagi-controls { display: flex; gap: 0.5rem; align-items: center; }
.pagi-btn { 
    width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; 
    background: white; border: 1px solid #e2e8f0; color: #64748b; font-weight: 700; text-decoration: none; transition: 0.2s;
}
.pagi-btn:hover:not(.disabled) { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }
.pagi-btn.active { background: #6366f1; border-color: #6366f1; color: white; }
.pagi-btn.disabled { opacity: 0.5; cursor: not-allowed; }
.pagi-dots { color: #94a3b8; font-weight: 700; padding: 0 4px; }

/* Action Buttons */
.action-btn-icon { 
    width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; 
    color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.action-btn-icon:not(:disabled):hover { background: #6366f1; color: white; border-color: #6366f1; transform: translateY(-1px); }

/* Empty States */
.empty-state-compact { padding: 4rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 3rem; opacity: 0.3; margin-bottom: 1.5rem; }
.empty-state-compact h3 { font-size: 1.25rem; font-weight: 800; color: #64748b; margin-bottom: 0.5rem; }
.empty-state-compact p { font-size: 0.9rem; }
</style>
