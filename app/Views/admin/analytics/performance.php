<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Performance Analytics</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">System performance metrics and monitoring.</p>
</div>

<!-- Performance Metrics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
    <?php if (isset($performance_metrics) && is_array($performance_metrics)): ?>
        <?php foreach ($performance_metrics as $key => $value): ?>
            <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($value); ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo ucwords(str_replace('_', ' ', $key)); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
