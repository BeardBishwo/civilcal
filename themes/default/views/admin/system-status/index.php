<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>System Status</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monitor system health and performance metrics</p>
    </div>
</div>

<!-- System Health Overview -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-heartbeat" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Overall System Health</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">System Uptime</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['uptime_percentage'] ?? 0, 1); ?>%</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Excellent</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <i class="fas fa-server" style="font-size: 1.5rem; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['cpu_usage'] ?? 0, 1); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Server Resources</p>
        </div>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Memory Usage</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Database Status</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['memory_usage'] ?? 0, 1); ?>%</div>
        <small style="color: #f87171; font-size: 0.75rem;">Storage Health</small>
    </div>
</div>

<!-- Component Status -->
<div class="admin-card">
    <h2 class="admin-card-title">System Components</h2>
    <div class="admin-grid">
        <div class="admin-card">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Web Server</h3>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Database Server</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['database_status'] ?? 100); ?>%</div>
            <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Operational</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Cache System</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">File System</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Email System</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">API Services</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Session Management</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Security Services</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">User Authentication</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Backup System</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">External Services</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Payment Gateway</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Monitoring Tools</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Analytics Tracking</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Logging System</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"></div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="admin-card">
    <h2 class="admin-card-title">System Information</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">PHP Version</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Server Environment</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">System Maintenance</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/system-status/refresh'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh Status</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/system-status/diagnostics'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-stethoscope"></i>
                        <span>Run Diagnostics</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/system-status/optimize'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-tools"></i>
                    <span>System Tools</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>