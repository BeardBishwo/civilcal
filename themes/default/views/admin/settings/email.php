<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Email Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure email delivery and SMTP settings</p>
        </div>
    </div>
</div>

<!-- Settings Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-cog"></i>
            <span>General</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/email'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/security'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/performance'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-tachometer-alt"></i>
            <span>Performance</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Email Configuration</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <form method="POST" action="<?php echo app_base_url('/admin/settings/email/update'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Email Transport Method</label>
                    <select name="mail_transport" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="smtp" <?php echo ($config['mail_transport'] ?? 'smtp') === 'smtp' ? 'selected' : ''; ?>>SMTP</option>
                        <option value="sendmail" <?php echo ($config['mail_transport'] ?? 'smtp') === 'sendmail' ? 'selected' : ''; ?>>Sendmail</option>
                        <option value="mail" <?php echo ($config['mail_transport'] ?? 'smtp') === 'mail' ? 'selected' : ''; ?>>PHP Mail</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">SMTP Host</label>
                    <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($config['smtp_host'] ?? 'smtp.gmail.com'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">SMTP Port</label>
                    <input type="number" name="smtp_port" value="<?php echo htmlspecialchars($config['smtp_port'] ?? '587'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">SMTP Encryption</label>
                    <select name="smtp_encryption" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="" <?php echo ($config['smtp_encryption'] ?? '') === '' ? 'selected' : ''; ?>>None</option>
                        <option value="tls" <?php echo ($config['smtp_encryption'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($config['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">SMTP Username</label>
                    <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($config['smtp_username'] ?? ''); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default From Name</label>
                    <input type="text" name="from_name" value="<?php echo htmlspecialchars($config['from_name'] ?? 'Bishwo Calculator'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default From Email</label>
                    <input type="email" name="from_email" value="<?php echo htmlspecialchars($config['from_email'] ?? 'noreply@bishwocalculator.com'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" 
                            style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                        <i class="fas fa-save"></i>
                        <span>Save Email Settings</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div>
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-key" style="color: #4cc9f0;"></i>
                    SMTP Password
                </h3>
                <form method="POST" action="<?php echo app_base_url('/admin/settings/email/update-password'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div style="margin-bottom: 1rem;">
                        <input type="password" name="smtp_password" placeholder="Enter SMTP password" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" 
                                style="flex: 1; padding: 0.75rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                            <i class="fas fa-lock"></i>
                            <span>Set Password</span>
                        </button>
                        <a href="<?php echo app_base_url('/admin/settings/email/generate-app-password'); ?>" 
                           style="flex: 1; padding: 0.75rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                            <i class="fas fa-plus"></i>
                            <span>App Password</span>
                        </a>
                    </div>
                </form>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-server" style="color: #34d399;"></i>
                    Server Status
                </h3>
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">SMTP Connection:</span>
                        <span style="color: <?php echo $smtp_status['connected'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $smtp_status['connected'] ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Last Test:</span>
                        <span style="color: #f9fafb;"><?php echo $smtp_status['last_test'] ?? 'Never'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #9ca3af;">Messages Sent:</span>
                        <span style="color: #f9fafb;"><?php echo $smtp_status['messages_sent'] ?? 0; ?></span>
                    </div>
                </div>
                
                <a href="<?php echo app_base_url('/admin/settings/email/test-connection'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; text-decoration: none; width: 100%; text-align: center;">
                    <i class="fas fa-network-wired"></i>
                    <span>Test Connection</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Email Templates -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Templates</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-plus" style="color: #4cc9f0;"></i>
                Welcome Email
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Sent to new users upon registration</p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/settings/email/templates/welcome/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; color: #4cc9f0; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/settings/email/templates/welcome/preview'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; color: #34d399; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-eye"></i>
                    <span>Preview</span>
                </a>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-forgot-password" style="color: #34d399;"></i>
                Password Reset
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Password reset and recovery emails</p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/settings/email/templates/password-reset/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; color: #4cc9f0; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/settings/email/templates/password-reset/preview'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; color: #34d399; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-eye"></i>
                    <span>Preview</span>
                </a>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bell" style="color: #fbbf24;"></i>
                Notification
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">System notifications and alerts</p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/settings/email/templates/notification/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; color: #4cc9f0; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/settings/email/templates/notification/preview'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; color: #34d399; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-eye"></i>
                    <span>Preview</span>
                </a>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-invoice" style="color: #22d3ee;"></i>
                System Alerts
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Critical system alerts and errors</p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/settings/email/templates/alerts/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; color: #4cc9f0; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/settings/email/templates/alerts/preview'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; color: #34d399; text-decoration: none; font-size: 0.875rem; text-align: center;">
                    <i class="fas fa-eye"></i>
                    <span>Preview</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Email Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/settings/email/send-test'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-paper-plane"></i>
            <span>Send Test Email</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/email/logs'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-history"></i>
            <span>Email Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/email/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-alt"></i>
            <span>Manage Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/email/providers'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cloud"></i>
            <span>Email Providers</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/email/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Advanced Settings</span>
        </a>
    </div>
</div>

<!-- Security Tips -->
<div class="admin-card">
    <h2 class="admin-card-title">Email Security Best Practices</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #4cc9f0;"></i>
                Use App Passwords
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Use application-specific passwords for email providers that support them, rather than your main account password.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-lock" style="color: #34d399;"></i>
                Enable SSL/TLS
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Always use encrypted connections (SSL or TLS) when sending emails through SMTP for security.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
                SPF Records
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Configure SPF records on your domain to improve email deliverability and prevent spoofing.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-spam" style="color: #22d3ee;"></i>
                Monitor Delivery
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Regularly check email logs and delivery rates to ensure messages reach their intended recipients.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>