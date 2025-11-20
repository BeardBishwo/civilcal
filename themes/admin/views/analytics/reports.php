<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Analytics Reports</h1>
                <p class="text-muted">Generate and download analytics reports.</p>
            </div>
        </div>
    </div>
</div>

<!-- Available Reports -->
<div class="row">
    <?php if (isset($available_reports) && is_array($available_reports)): ?>
        <?php foreach ($available_reports as $report): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($report['name'] ?? 'Report'); ?></h5>
                        <p class="card-text text-muted mb-4"><?php echo htmlspecialchars($report['description'] ?? ''); ?></p>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-download mr-2"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
