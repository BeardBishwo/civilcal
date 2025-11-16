<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Database Backup</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Create and manage database backups</p>
</div>

<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 1.5rem;">
    <button style="background: #4361ee; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
        <i class="fas fa-plus"></i> Create New Backup
    </button>
</div>

<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Existing Backups</h5>
    
    <?php if (!empty($backups)): ?>
        <div style="display: grid; gap: 1rem;">
            <?php foreach ($backups as $backup): ?>
                <div style="background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #f9fafb; font-weight: 600; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($backup['name']); ?></div>
                        <div style="color: #9ca3af; font-size: 0.875rem;">
                            Size: <?php echo number_format($backup['size'] / 1024 / 1024, 2); ?> MB | 
                            Created: <?php echo date('M d, Y H:i', $backup['created']); ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="#" style="color: #4cc9f0; text-decoration: none; font-size: 0.875rem;">Download</a>
                        <a href="#" style="color: #ef4444; text-decoration: none; font-size: 0.875rem;">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: #9ca3af; text-align: center; padding: 2rem;">No backups found</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
