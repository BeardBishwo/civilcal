<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Calculator Analytics</h1>
                <p class="text-muted">Calculator usage statistics and trends.</p>
            </div>
        </div>
    </div>
</div>

<!-- Most Used Calculators -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 10 Most Used Calculators</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Calculator</th>
                                <th class="text-right">Usage Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($calculator_stats) && is_array($calculator_stats)): ?>
                                <?php foreach ($calculator_stats as $calc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($calc['calculator_type'] ?? 'Unknown'); ?></td>
                                        <td class="text-right font-weight-bold text-primary"><?php echo number_format($calc['usage_count'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center p-4 text-muted">No calculator usage data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
