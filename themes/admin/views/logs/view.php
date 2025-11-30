<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">View Log - <?php echo htmlspecialchars($filename); ?></h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">
        <a href="<?php echo app_base_url('/admin/logs'); ?>" style="color: #4cc9f0; text-decoration: none;">← Back to Logs</a>
    </p>
</div>

<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0;">Log Content</h5>
        <a href="<?php echo app_base_url('/admin/logs/download/' . urlencode($filename)); ?>" style="background: #4361ee; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">Download Log</a>
    </div>
    
    <div style="background: #0a0e27; border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem; overflow-x: auto;">
        <pre style="color: #f9fafb; font-family: 'Courier New', monospace; font-size: 0.875rem; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($content); ?></pre>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>