<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Email Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage email templates and notifications</p>
    </div>
</div>

<!-- Email Statistics -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-envelope" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Emails Sent Today</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['emails_sent'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Successful Deliveries</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <i class="fas fa-inbox" style="font-size: 1.5rem; color: #34d399; margin-bottom: 0.5rem;">Email Templates</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['templates'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['pending_emails'] ?? 0); ?></div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-edit"></i> Manage Templates</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;">Pending Emails</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                    <i class="fas fa-clock" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 0.5rem;">Email Queue</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['failed_emails'] ?? 0); ?></div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 0.5rem;">Failed Deliveries</small>
    </div>
</div>

<!-- Email Templates -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Templates</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
            <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 1rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: #f9fafb; margin-bottom: 0.5rem;">Welcome Email</h4>
                <p style="color: #9ca3af; font-size: 0.75rem;">Template for new user registration</p>
                <a href="<?php echo app_base_url('/admin/email/edit'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.5rem;">Edit Template</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Email Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Configuration</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/email/send-test'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send Test Email</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/email/templates'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-cog"></i>
                    <span>Email Settings</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/email/templates/create'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Template</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>