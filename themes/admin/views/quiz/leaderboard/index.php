<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-trophy"></i>
                    <h1>Leaderboard</h1>
                </div>
                <div class="header-subtitle">Top performers for <?php echo ucfirst($current_period); ?></div>
            </div>
            <div class="header-actions">
                <div class="btn-group btn-group-compact" role="group">
                    <a href="?period=weekly" class="btn btn-<?php echo $current_period == 'weekly' ? 'primary' : 'outline-secondary'; ?> btn-sm">Weekly</a>
                    <a href="?period=monthly" class="btn btn-<?php echo $current_period == 'monthly' ? 'primary' : 'outline-secondary'; ?> btn-sm">Monthly</a>
                    <a href="?period=yearly" class="btn btn-<?php echo $current_period == 'yearly' ? 'primary' : 'outline-secondary'; ?> btn-sm">Yearly</a>
                </div>
            </div>
        </div>

        <!-- Leaderboard Content -->
        <div class="pages-content">
            <div id="leaderboard-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($rankings)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-trophy"></i>
                            <h3>No rankings yet</h3>
                            <p>Leaderboard will update once users complete exams in this period.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title" style="width: 10%;">Rank</th>
                                        <th class="col-title" style="width: 30%;">User</th>
                                        <th class="col-status">Score</th>
                                        <th class="col-status">Tests</th>
                                        <th class="col-status">Accuracy</th>
                                        <th class="col-status">Trend</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rankings as $rank): ?>
                                        <tr class="<?php echo $rank['calculated_rank'] <= 3 ? 'rank-top-3' : ''; ?>">
                                            <td>
                                                <div class="rank-badge <?php echo 'rank-' . $rank['calculated_rank']; ?>" style="font-weight: 700; font-size: 1.1rem; color: var(--admin-gray-800); display: flex; align-items: center; gap: 5px;">
                                                    <?php 
                                                        if ($rank['calculated_rank'] == 1) echo '<i class="fas fa-crown text-warning"></i>';
                                                        elseif ($rank['calculated_rank'] == 2) echo '<i class="fas fa-crown" style="color: #C0C0C0;"></i>';
                                                        elseif ($rank['calculated_rank'] == 3) echo '<i class="fas fa-crown" style="color: #CD7F32;"></i>';
                                                        else echo '<span style="color: var(--admin-gray-400); font-size: 0.9rem;">#</span>';
                                                    ?>
                                                    <?php echo $rank['calculated_rank']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-primary-soft); color: var(--admin-primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight:700;">
                                                        <?php echo strtoupper(substr($rank['username'] ?? 'U', 0, 1)); ?>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact"><?php echo htmlspecialchars($rank['full_name']); ?></div>
                                                        <div class="page-slug-compact">@<?php echo htmlspecialchars($rank['username']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: var(--admin-primary); font-size: 1rem;">
                                                    <?php echo number_format($rank['total_score'], 0); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.9rem; color: var(--admin-gray-700);">
                                                    <?php echo $rank['tests_taken']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress-compact" style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="font-weight: 600; font-size: 0.85rem; width: 35px;"><?php echo number_format($rank['accuracy_avg'], 0); ?>%</div>
                                                    <div style="flex: 1; height: 6px; background: var(--admin-gray-200); border-radius: 3px; width: 60px;">
                                                        <div style="width: <?php echo $rank['accuracy_avg']; ?>%; height: 100%; background: var(--admin-success); border-radius: 3px;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($rank['trend'] > 0): ?>
                                                    <span class="text-success small" style="display: flex; align-items: center; gap: 2px; font-weight: 600;"><i class="fas fa-arrow-up"></i> <?php echo $rank['trend']; ?></span>
                                                <?php elseif($rank['trend'] < 0): ?>
                                                    <span class="text-danger small" style="display: flex; align-items: center; gap: 2px; font-weight: 600;"><i class="fas fa-arrow-down"></i> <?php echo abs($rank['trend']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <button class="action-btn-icon delete-btn" title="Ban from Leaderboard">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
