<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Calculator Analytics</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Calculator usage statistics and trends.</p>
</div>

<!-- Most Used Calculators -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Top 10 Most Used Calculators</h5>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem;">Calculator</th>
                    <th style="padding: 0.75rem; text-align: right; color: #9ca3af; font-size: 0.875rem;">Usage Count</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($calculator_stats) && is_array($calculator_stats)): ?>
                    <?php foreach ($calculator_stats as $calc): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($calc['calculator_type'] ?? 'Unknown'); ?></td>
                            <td style="padding: 0.75rem; text-align: right; color: #4cc9f0; font-weight: 600;"><?php echo number_format($calc['usage_count'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="padding: 2rem; text-align: center; color: #9ca3af;">No calculator usage data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
