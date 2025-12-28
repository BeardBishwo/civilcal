<?php $this->layout('layouts/app', ['title' => $title]); ?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="font-weight-bold text-primary mb-1">🏆 Hall of Fame</h1>
            <p class="text-muted">Top performers for the current session.</p>
        </div>
        <div class="col-md-4 text-md-right">
             <div class="btn-group shadow-sm">
                <a href="?period=weekly" class="btn btn-<?php echo $current_period == 'weekly' ? 'primary' : 'outline-primary'; ?>">Weekly</a>
                <a href="?period=monthly" class="btn btn-<?php echo $current_period == 'monthly' ? 'primary' : 'outline-primary'; ?>">Monthly</a>
                <a href="?period=yearly" class="btn btn-<?php echo $current_period == 'yearly' ? 'primary' : 'outline-primary'; ?>">Yearly</a>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-4 py-3 border-0">Rank</th>
                            <th class="py-3 border-0">Student</th>
                            <th class="py-3 border-0">Score</th>
                            <th class="py-3 border-0">Accuracy</th>
                            <th class="py-3 border-0">Tests</th>
                            <th class="pr-4 py-3 border-0 text-right">Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankings)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No records found for this period. Be the first to take a test!</td></tr>
                        <?php else: ?>
                            <?php foreach ($rankings as $rank): ?>
                                <?php 
                                    $rankClass = '';
                                    if ($rank['calculated_rank'] == 1) $rankClass = 'text-warning'; // Gold
                                    elseif ($rank['calculated_rank'] == 2) $rankClass = 'text-secondary'; // Silver
                                    elseif ($rank['calculated_rank'] == 3) $rankClass = 'text-danger'; // Bronze
                                ?>
                            <tr class="<?php echo ($rank['user_id'] == ($_SESSION['user_id'] ?? 0)) ? 'bg-light-primary' : ''; ?>">
                                <td class="pl-4 align-middle font-weight-bold <?php echo $rankClass; ?>" style="font-size: 1.2rem;">
                                    <?php if ($rank['calculated_rank'] == 1): ?>👑 <?php endif; ?>
                                    #<?php echo $rank['calculated_rank']; ?>
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($rank['full_name']); ?></div>
                                    <small class="text-muted">@<?php echo htmlspecialchars($rank['username']); ?></small>
                                </td>
                                <td class="align-middle font-weight-bold text-primary">
                                    <?php echo number_format($rank['total_score'], 0); ?>
                                </td>
                                <td class="align-middle">
                                    <div class="progress" style="height: 6px; width: 60px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $rank['accuracy_avg']; ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo number_format($rank['accuracy_avg'], 1); ?>%</small>
                                </td>
                                <td class="align-middle"><?php echo $rank['tests_taken']; ?></td>
                                <td class="pr-4 align-middle text-right">
                                     <?php if($rank['trend'] > 0): ?>
                                        <span class="badge badge-success-soft text-success"><i class="fas fa-arrow-up"></i> <?php echo $rank['trend']; ?></span>
                                    <?php elseif($rank['trend'] < 0): ?>
                                        <span class="badge badge-danger-soft text-danger"><i class="fas fa-arrow-down"></i> <?php echo abs($rank['trend']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
