<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Calculations History</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">View and manage all calculation records</p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Calculations</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['week_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">This Week</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['unique_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Unique Users</div>
    </div>
</div>

<!-- Calculations Table -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Recent Calculations</h5>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">User</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Calculator Type</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($calculations)): ?>
                    <?php foreach ($calculations as $calc): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;"><?php echo htmlspecialchars($calc['username'] ?? $calc['email'] ?? 'Anonymous'); ?></td>
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;"><?php echo htmlspecialchars($calc['calculator_type'] ?? 'N/A'); ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af; font-size: 0.875rem;"><?php echo isset($calc['created_at']) ? date('M d, Y H:i', strtotime($calc['created_at'])) : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: #9ca3af;">No calculations found</td>
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
