<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Performance Analytics</h1>
            <p class="page-description">System performance metrics and monitoring.</p>
        </div>
        <div class="page-header-actions">
            <button onclick="window.location.reload()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="stats-grid">
    <?php if (isset($performance_metrics) && is_array($performance_metrics)): ?>
        <?php foreach ($performance_metrics as $key => $value): ?>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon info">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo htmlspecialchars($value); ?></div>
                <div class="stat-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">No performance metrics available</div>
    <?php endif; ?>
</div>

<!-- Additional Performance Details -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i>
            Detailed Performance Metrics
        </h3>
    </div>
    <div class="card-content">
        <div class="performance-details">
            <?php if (isset($performance_metrics) && is_array($performance_metrics)): ?>
                <div class="metrics-grid">
                    <?php foreach ($performance_metrics as $key => $value): ?>
                        <div class="metric-item">
                            <div class="metric-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></div>
                            <div class="metric-value"><?php echo htmlspecialchars($value); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data text-center">No detailed performance data available</div>
            <?php endif; ?>
        </div>
    </div>
</div>