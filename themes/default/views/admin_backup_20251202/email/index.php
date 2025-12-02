<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Email Manager</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage email settings, templates, and communications</p>
        </div>
    </div>
</div>

<!-- Email Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-envelope" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Sent</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_sent'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +18% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Delivered</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['delivered'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Successful</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check"></i> High Success</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-times-circle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Failed</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['failed'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Delivery Issues</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-triangle"></i> Low Rate</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-percentage" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Success Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['success_rate'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Delivery</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Stable</small>
    </div>
</div>

<!-- Email Configuration -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Configuration</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-cog" style="color: #4cc9f0;"></i>
                    SMTP Settings
                </h4>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Host:</strong> <span style="color: #f9fafb;"><?php echo $email_config['smtp_host'] ?? 'Not Configured'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Port:</strong> <span style="color: #f9fafb;"><?php echo $email_config['smtp_port'] ?? 'Not Configured'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Encryption:</strong> <span style="color: #f9fafb;"><?php echo $email_config['encryption'] ?? 'None'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Status:</strong> 
                        <span style="color: <?php echo $email_config['is_active'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $email_config['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div>
                <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-server" style="color: #34d399;"></i>
                    Default Settings
                </h4>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 6px;">
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>From Name:</strong> <span style="color: #f9fafb;"><?php echo $email_config['from_name'] ?? 'System'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>From Email:</strong> <span style="color: #f9fafb;"><?php echo $email_config['from_email'] ?? 'noreply@example.com'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Reply To:</strong> <span style="color: #f9fafb;"><?php echo $email_config['reply_to'] ?? 'support@example.com'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Charset:</strong> <span style="color: #f9fafb;"><?php echo $email_config['charset'] ?? 'UTF-8'; ?></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Emails -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Emails</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Subject</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">To</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Date</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_emails)): ?>
                        <?php foreach (array_slice($recent_emails, 0, 10) as $email): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars(substr($email['subject'] ?? 'No Subject', 0, 40)).(strlen($email['subject'] ?? '') > 40 ? '...' : ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($email['to'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $email['date'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $email['status'] === 'sent' ? 'success' : ($email['status'] === 'failed' ? 'error' : 'warning'); ?>" 
                                          style="background: rgba(<?php echo $email['status'] === 'sent' ? '52, 211, 153, 0.1' : ($email['status'] === 'failed' ? '248, 113, 113, 0.1' : '251, 191, 36, 0.1'); ?>); 
                                                 border: 1px solid rgba(<?php echo $email['status'] === 'sent' ? '52, 211, 153, 0.3' : ($email['status'] === 'failed' ? '248, 113, 113, 0.3' : '251, 191, 36, 0.3'); ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst($email['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/email/'.($email['id'] ?? 0).'/view'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/email/'.($email['id'] ?? 0).'/resend'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                        <i class="fas fa-redo"></i>
                                        <span>Resend</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No recent emails found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Email Templates -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Templates</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-plus" style="color: #4cc9f0;"></i>
                Welcome Email
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Template for new user registrations</p>
            <a href="<?php echo app_base_url('/admin/email/templates/welcome'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-forgot-password" style="color: #34d399;"></i>
                Password Reset
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Template for password recovery</p>
            <a href="<?php echo app_base_url('/admin/email/templates/password-reset'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bell" style="color: #fbbf24;"></i>
                Notifications
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">General notification templates</p>
            <a href="<?php echo app_base_url('/admin/email/templates/notifications'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-invoice" style="color: #22d3ee;"></i>
                System Alerts
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">System maintenance and alerts</p>
            <a href="<?php echo app_base_url('/admin/email/templates/alerts'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
        </div>
    </div>
</div>

<!-- Email Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/email/test'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-paper-plane"></i>
            <span>Send Test Email</span>
        </a>

        <a href="<?php echo app_base_url('/admin/email/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-alt"></i>
            <span>Manage Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/email/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-cog"></i>
            <span>Email Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/email/logs'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-history"></i>
            <span>Email Logs</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>