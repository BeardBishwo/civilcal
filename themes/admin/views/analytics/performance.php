<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Performance Analytics</h1>
                <p class="text-muted">System performance metrics and monitoring.</p>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row">
    <?php if (isset($performance_metrics) && is_array($performance_metrics)): ?>
        <?php foreach ($performance_metrics as $key => $value): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h2 class="mb-1 text-info"><?php echo htmlspecialchars($value); ?></h2>
                        <p class="text-muted mb-0"><?php echo ucwords(str_replace('_', ' ', $key)); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
