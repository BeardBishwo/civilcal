<?php $this->layout('admin/layouts/app', ['title' => $page_title]); ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $page_title; ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <div class="btn-group">
                <a href="?period=weekly" class="btn btn-<?php echo $current_period == 'weekly' ? 'primary' : 'light'; ?>">Weekly</a>
                <a href="?period=monthly" class="btn btn-<?php echo $current_period == 'monthly' ? 'primary' : 'light'; ?>">Monthly</a>
                <a href="?period=yearly" class="btn btn-<?php echo $current_period == 'yearly' ? 'primary' : 'light'; ?>">Yearly</a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Rankings: <?php echo ucfirst($current_period); ?> (<?php echo $current_value; ?>)</h6>
            <div class="dropdown no-arrow">
                 <!-- Filters could go here -->
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">Rank</th>
                            <th>User</th>
                            <th>Score</th>
                            <th>Tests Taken</th>
                            <th>Accuracy</th>
                            <th>Trend</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankings)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No data found for this period.</td></tr>
                        <?php else: ?>
                            <?php foreach ($rankings as $rank): ?>
                            <tr>
                                <td class="text-center font-weight-bold">
                                    #<?php echo $rank['calculated_rank']; ?>
                                </td>
                                <td>
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($rank['full_name']); ?></div>
                                    <small class="text-muted">@<?php echo htmlspecialchars($rank['username']); ?></small>
                                </td>
                                <td class="font-weight-bold text-success">
                                    <?php echo number_format($rank['total_score'], 1); ?>
                                </td>
                                <td><?php echo $rank['tests_taken']; ?></td>
                                <td>
                                    <?php echo number_format($rank['accuracy_avg'], 1); ?>%
                                </td>
                                <td class="text-center">
                                    <!-- Simple Trend UI -->
                                    <?php if($rank['trend'] > 0): ?>
                                        <span class="text-success small"><i class="fas fa-arrow-up"></i> <?php echo $rank['trend']; ?></span>
                                    <?php elseif($rank['trend'] < 0): ?>
                                        <span class="text-danger small"><i class="fas fa-arrow-down"></i> <?php echo abs($rank['trend']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" type="button" title="Ban from Leaderboard"><i class="fas fa-ban"></i></button>
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
