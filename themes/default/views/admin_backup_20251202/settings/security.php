<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Security Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure security measures and protection settings</p>
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
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/security'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/performance'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-tachometer-alt"></i>
            <span>Performance</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Security Configuration</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <form method="POST" action="<?php echo app_base_url('/admin/settings/security/update'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-key" style="color: #4cc9f0;"></i>
                        Authentication Settings
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Minimum Password Length</label>
                        <input type="number" name="min_password_length" value="<?php echo htmlspecialchars($security_config['min_password_length'] ?? 8); ?>" min="6" max="128"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Require Strong Passwords</label>
                        <select name="require_strong_password" 
                                style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                            <option value="1" <?php echo ($security_config['require_strong_password'] ?? true) ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo ($security_config['require_strong_password'] ?? true) ? '' : 'selected'; ?>>No</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Login Attempts Limit</label>
                        <input type="number" name="max_login_attempts" value="<?php echo htmlspecialchars($security_config['max_login_attempts'] ?? 5); ?>" min="1" max="20"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Lockout Duration (minutes)</label>
                        <input type="number" name="login_lockout_duration" value="<?php echo htmlspecialchars($security_config['login_lockout_duration'] ?? 15); ?>" min="1" max="120"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" value="<?php echo htmlspecialchars($security_config['session_timeout'] ?? 30); ?>" min="5" max="120"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" 
                            style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                        <i class="fas fa-save"></i>
                        <span>Save Security Settings</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div>
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-shield-virus" style="color: #34d399;"></i>
                    Security Features
                </h3>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">Two-Factor Authentication</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="two_factor_auth" <?php echo ($security_config['two_factor_auth'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($security_config['two_factor_auth'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Enhanced security for user accounts</p>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">CSRF Protection</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="csrf_protection" <?php echo ($security_config['csrf_protection'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($security_config['csrf_protection'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Cross-site request forgery protection</p>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">XSS Protection</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="xss_protection" <?php echo ($security_config['xss_protection'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($security_config['xss_protection'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Cross-site scripting prevention</p>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">HSTS Enabled</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="hsts_enabled" <?php echo ($security_config['hsts_enabled'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($security_config['hsts_enabled'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">HTTP Strict Transport Security</p>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #f9fafb;">Secure Headers</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="secure_headers" <?php echo ($security_config['secure_headers'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($security_config['secure_headers'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Additional security headers</p>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-shield-alt" style="color: #fbbf24;"></i>
                    Security Status
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #9ca3af;">Last Security Check:</span>
                            <span style="color: #f9fafb;"><?php echo $security_status['last_check'] ?? 'Never'; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #9ca3af;">Vulnerabilities Found:</span>
                            <span style="color: <?php echo ($security_status['vulnerabilities'] ?? 0) > 0 ? '#f87171' : '#34d399'; ?>;"><?php echo $security_status['vulnerabilities'] ?? 0; ?></span>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #9ca3af;">Security Level:</span>
                            <span style="color: <?php echo $security_status['level'] === 'high' ? '#34d399' : ($security_status['level'] === 'medium' ? '#fbbf24' : '#f87171'); ?>;"><?php echo ucfirst($security_status['level'] ?? 'medium'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #9ca3af;">Next Check:</span>
                            <span style="color: #f9fafb;"><?php echo $security_status['next_check'] ?? 'Scheduled'; ?></span>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/settings/security/run-scan'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; text-decoration: none;">
                        <i class="fas fa-search"></i>
                        <span>Run Scan</span>
                    </a>
                    <a href="<?php echo app_base_url('/admin/settings/security/reports'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none;">
                        <i class="fas fa-file-alt"></i>
                        <span>Reports</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security Logs -->
<div class="admin-card">
    <h2 class="admin-card-title">Security Logs</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Timestamp</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Type</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">IP Address</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Message</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($security_logs)): ?>
                        <?php foreach (array_slice($security_logs, 0, 10) as $log): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo $log['timestamp'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo $log['type'] === 'critical' ? '#f87171' : ($log['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; 
                                          background: <?php echo $log['type'] === 'critical' ? 'rgba(248, 113, 113, 0.1)' : ($log['type'] === 'warning' ? 'rgba(251, 191, 36, 0.1)' : 'rgba(52, 211, 153, 0.1)'); ?>; 
                                          padding: 0.25rem 0.5rem; 
                                          border-radius: 4px; 
                                          font-size: 0.75rem;">
                                        <?php echo ucfirst(htmlspecialchars($log['type'] ?? 'info')); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo $log['ip_address'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars(substr($log['message'] ?? 'Security event recorded', 0, 50)).(strlen($log['message'] ?? '') > 50 ? '...' : ''); ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/settings/security/logs/'.($log['id'] ?? 0).'/view'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No security logs available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Security Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Security Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/settings/security/password-policy'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-lock"></i>
            <span>Password Policy</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/security/whitelist'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-globe"></i>
            <span>IP Whitelist</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/security/blacklist'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-ban"></i>
            <span>IP Blacklist</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/security/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-database"></i>
            <span>Backup Security</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/security/monitoring'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-chart-line"></i>
            <span>Security Monitoring</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/security/compliance'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-file-contract"></i>
            <span>Compliance</span>
        </a>
    </div>
</div>

<!-- Security Best Practices -->
<div class="admin-card">
    <h2 class="admin-card-title">Security Best Practices</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-key" style="color: #4cc9f0;"></i>
                Strong Passwords
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Enforce strong password policies with complexity requirements and regular updates.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-mobile-alt" style="color: #34d399;"></i>
                Two-Factor Authentication
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Enable 2FA for all admin accounts and encourage users to enable it too.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-shield" style="color: #fbbf24;"></i>
                Admin Access Control
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Limit admin access to trusted IP addresses and monitor all admin activity.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sync-alt" style="color: #22d3ee;"></i>
                Regular Updates
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Keep all software components up to date with security patches and updates.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>