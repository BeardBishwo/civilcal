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

<!-- Log Statistics -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Error Logs</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;">Total Errors</h3>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['error_count'] ?? 0); ?></div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-triangle"></i> Critical Issues</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-bug" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 0.5rem;">Activity Logs</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['activity_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['activity_count'] ?? 0); ?></div>
        <small style="color: #34d399; font-size: 0.75rem;"><i class="fas fa-history"></i> System Activities</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['warning_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Performance Metrics</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['warning_count'] ?? 0); ?></div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-tachometer-alt"></i> System Performance</small>
    </div>
</div>

<!-- Recent Logs -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Activity</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-info-circle" style="color: #22d3ee;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem;">System backup completed successfully</span>
                <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">2 hours ago</small>
            </li>
            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-check-circle" style="color: #34d399;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem; margin: 0;">User authentication successful</span>
                <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">User logged in successfully</span>
                <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">4 hours ago</small>
            </li>
            <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-user-plus" style="color: #4cc9f0;"></i>
                <span style="color: #e5e7eb; font-size: 0.875rem;">New user registration completed</span>
                <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">Database optimization completed</span>
                <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">1 day ago</small>
            </li>
        </ul>
    </div>
</div>

<!-- Log Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Log Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/logs/download'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-download"></i>
            <span>Download Logs</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/logs/view'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-trash-alt"></i>
                <span>Clear Logs</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/logs/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <i class="fas fa-file-export"></i>
            <span>Export Logs</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>