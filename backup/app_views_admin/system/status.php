<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">System Status</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Monitor system health and diagnostics</p>
</div>

<!-- System Health Checks -->
<div style="display: grid; gap: 1.5rem; margin-bottom: 2rem;">
    <?php foreach ($system_health as $check => $status): ?>
        <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0;"><?php echo ucfirst($check); ?></h3>
                <span style="padding: 0.25rem 0.75rem; background: <?php echo $status['status'] === 'ok' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)'; ?>; border-radius: 9999px; font-size: 0.75rem; color: <?php echo $status['status'] === 'ok' ? '#34d399' : '#ef4444'; ?>; font-weight: 600;">
                    <?php echo strtoupper($status['status']); ?>
                </span>
            </div>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;"><?php echo htmlspecialchars($status['message']); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<!-- System Information -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">System Information</h5>
    
    <div style="display: grid; gap: 1rem;">
        <div style="display: flex; justify-content: space-between; padding: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
            <span style="color: #9ca3af; font-size: 0.875rem;">PHP Version</span>
            <span style="color: #f9fafb; font-size: 0.875rem; font-weight: 600;"><?php echo $php_version; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
            <span style="color: #9ca3af; font-size: 0.875rem;">Server Software</span>
            <span style="color: #f9fafb; font-size: 0.875rem; font-weight: 600;"><?php echo $server_software; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
            <span style="color: #9ca3af; font-size: 0.875rem;">Database Version</span>
            <span style="color: #f9fafb; font-size: 0.875rem; font-weight: 600;"><?php echo $database_version; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 0.75rem;">
            <span style="color: #9ca3af; font-size: 0.875rem;">Disk Space</span>
            <span style="color: #f9fafb; font-size: 0.875rem; font-weight: 600;">
                <?php echo number_format(($disk_space['total'] - $disk_space['free']) / 1024 / 1024 / 1024, 2); ?> GB / 
                <?php echo number_format($disk_space['total'] / 1024 / 1024 / 1024, 2); ?> GB 
                (<?php echo $disk_space['percent_used']; ?>%)
            </span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
