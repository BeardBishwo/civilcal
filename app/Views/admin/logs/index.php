<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">System Logs</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">View and manage system log files</p>
</div>

<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Log Files</h5>
    
    <?php if (!empty($logFiles)): ?>
        <div style="display: grid; gap: 1rem;">
            <?php foreach ($logFiles as $logFile): ?>
                <div style="background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #f9fafb; font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($logFile['name']); ?></div>
                        <div style="color: #9ca3af; font-size: 0.875rem;">
                            Size: <?php echo number_format($logFile['size'] / 1024, 2); ?> KB | 
                            Modified: <?php echo date('M d, Y H:i', $logFile['modified']); ?>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo app_base_url('/admin/logs/download/' . urlencode($logFile['name'])); ?>" style="color: #4cc9f0; text-decoration: none; font-size: 0.875rem; margin-right: 1rem;">Download</a>
                        <a href="<?php echo app_base_url('/admin/logs/view/' . urlencode($logFile['name'])); ?>" style="color: #4cc9f0; text-decoration: none; font-size: 0.875rem;">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: #9ca3af; text-align: center; padding: 2rem;">No log files found</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
