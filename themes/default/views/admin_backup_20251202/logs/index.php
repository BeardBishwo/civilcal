<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>System Logs</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">View and manage system activity logs</p>
        </div>
    </div>
</div>

<!-- Log Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Error Logs</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['error_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Errors</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-triangle"></i> Critical Issues</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-history" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Activity Logs</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['activity_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Activities</div>
        <small style="color: #34d399; font-size: 0.75rem;"><i class="fas fa-history"></i> System Activities</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-exclamation-circle" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Warning Logs</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['warning_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Warnings</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-tachometer-alt"></i> System Performance</small>
    </div>
</div>

<!-- Recent Logs -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Log Entries</h2>
    <div class="admin-card-content">
        <div style="max-height: 300px; overflow-y: auto;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($logs)): ?>
                    <?php foreach (array_slice($logs, 0, 10) as $log): ?>
                        <li style="margin-bottom: 1rem; padding: 0.75rem; border-left: 3px solid <?php echo $log['level'] === 'error' ? '#f87171' : ($log['level'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; background: rgba(15, 23, 42, 0.5);">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                                <i class="fas <?php echo $log['level'] === 'error' ? 'fa-exclamation-triangle' : ($log['level'] === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"
                                   style="color: <?php echo $log['level'] === 'error' ? '#f87171' : ($log['level'] === 'warning' ? '#fbbf24' : '#34d399'); ?>;"></i>
                                <span style="color: #e5e7eb; font-size: 0.875rem; flex: 1;"><?php echo htmlspecialchars($log['message'] ?? ''); ?></span>
                                <small style="color: #9ca3af; font-size: 0.75rem;"><?php echo $log['timestamp'] ?? ''; ?></small>
                            </div>
                            <div style="color: #9ca3af; font-size: 0.75rem; margin-left: 2rem;"><?php echo htmlspecialchars($log['context'] ?? ''); ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No recent logs available</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Log Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Log Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/logs/download'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-download"></i>
            <span>Download Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logs/clear'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logs/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logs/view'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-eye"></i>
            <span>View All Logs</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>