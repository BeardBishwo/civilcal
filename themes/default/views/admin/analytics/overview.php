<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Analytics Dashboard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monitor system performance and user activity</p>
    </div>
</div>

<!-- Analytics Statistics -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">User Growth</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +15% this month</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['calculations_performed'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-bar"></i> Calculator Usage</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['calculations_performed'] ?? 0); ?></div>
        <small style="color: #fbbf24; font-size: 0.75rem;">Performance Metrics</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-rocket" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['api_requests'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System Performance</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Usage Analytics</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Monthly Activity</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Calculator Usage by Category</h3>
                <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <div style="height: 200px; display: flex; align-items: end; gap: 0.5rem; justify-content: center;">
                    <div style="width: 40px; background: #4cc9f0; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reports -->
<div class="admin-card">
    <h2 class="admin-card-title">Quick Reports</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/analytics/users'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-user-chart" style="font-size: 1.25rem; color: #34d399; margin-bottom: 0.5rem;">User Analytics</h3>
            <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-tachometer-alt"></i>
                <span>View Report</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/analytics/reports'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>