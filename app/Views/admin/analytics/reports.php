<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Analytics Reports</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Generate and download analytics reports.</p>
</div>

<!-- Available Reports -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <?php if (isset($available_reports) && is_array($available_reports)): ?>
        <?php foreach ($available_reports as $report): ?>
            <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
                <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.75rem 0;"><?php echo htmlspecialchars($report['name'] ?? 'Report'); ?></h5>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0 0 1.5rem 0;"><?php echo htmlspecialchars($report['description'] ?? ''); ?></p>
                <button style="background: #4361ee; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; width: 100%;">
                    <i class="fas fa-download"></i> Generate Report
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
