<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-file-alt"></i> Analytics Reports</h1>
            <p class="page-description">Generate and download analytics reports.</p>
        </div>
        <div class="page-header-actions">
            <button onclick="window.location.reload()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>
</div>

<!-- Available Reports -->
<div class="reports-grid">
    <?php if (isset($available_reports) && is_array($available_reports)): ?>
        <?php foreach ($available_reports as $report): ?>
            <div class="report-card">
                <div class="report-card-header">
                    <h3 class="report-title"><?php echo htmlspecialchars($report['name'] ?? 'Report'); ?></h3>
                </div>
                <div class="report-card-content">
                    <p class="report-description"><?php echo htmlspecialchars($report['description'] ?? ''); ?></p>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-download"></i> Generate Report
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">No reports available</div>
    <?php endif; ?>
</div>