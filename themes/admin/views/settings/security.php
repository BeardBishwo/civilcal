<?php

/**
 * Security Settings Page - Premium Beautiful Design
 * Features: 4 organized sections, gradient headers, advanced spacing
 */
?>

<style>
    .security-settings-container {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .security-header {
        margin-bottom: 2.5rem;
        color: white;
        animation: slideDown 0.6s ease-out;
    }

    .security-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #00d4ff, #0099ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .security-header p {
        font-size: 1rem;
        color: #cbd5e0;
        margin-bottom: 0;
    }

    .security-form {
        max-width: 1200px;
        margin: 0 auto;
    }

    .security-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
        border-left: 5px solid #667eea;
    }

    .security-section:nth-child(2) {
        border-left-color: #f093fb;
    }

    .security-section:nth-child(3) {
        border-left-color: #4facfe;
    }

    .security-section:nth-child(4) {
        border-left-color: #fa709a;
    }

    .security-section:hover {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        transform: translateY(-4px);
    }

    .section-header {
        padding: 1.8rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-header.auth-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
    }

    .section-header.password-section {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-bottom: none;
    }

    .section-header.session-section {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-bottom: none;
    }

    .section-header.access-section {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border-bottom: none;
    }

    .section-header.auth-section,
    .section-header.password-section,
    .section-header.session-section,
    .section-header.access-section {
        color: white;
    }

    .section-icon {
        font-size: 2rem;
        line-height: 1;
    }

    .section-title-group h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
        color: white;
    }

    .section-title-group p {
        font-size: 0.9rem;
        opacity: 0.95;
        margin-bottom: 0;
    }

    .section-body {
        padding: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.6rem;
        letter-spacing: 0.3px;
    }

    .form-control,
    .form-select {
        padding: 0.9rem 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: inherit;
        background-color: #f8f9fa;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-check {
        padding: 1.2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .form-check:hover {
        background: linear-gradient(135deg, #eef2f7 0%, #e2e8f0 100%);
        border-left-color: #764ba2;
    }

    .form-check-input {
        width: 1.3rem;
        height: 1.3rem;
        margin-top: 0.3rem;
        cursor: pointer;
        accent-color: #667eea;
    }

    .form-check-label {
        font-weight: 600;
        color: #2d3748;
        margin-left: 0.5rem;
        cursor: pointer;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .form-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.4rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
        margin-top: 2rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 0.9rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        letter-spacing: 0.3px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
    }

    .settings-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        margin: 1.5rem 0;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .security-settings-container {
            padding: 1rem;
        }

        .security-header h1 {
            font-size: 1.8rem;
        }

        .section-header {
            padding: 1.2rem 1.5rem;
        }

        .section-body {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .button-group {
            flex-direction: column;
            justify-content: stretch;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<div class="admin-content">
    <div class="security-settings-container">
        <div class="security-header">
            <h1>🔒 Security & Access Control</h1>
            <p>Protect your system with advanced security configurations</p>
        </div>

        <form action="<?php echo app_base_url('/admin/settings/update'); ?>" method="POST" class="security-form ajax-form" id="securitySettingsForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Authentication Section -->
        <div class="security-section">
            <div class="section-header auth-section">
                <span class="section-icon">🔐</span>
                <div class="section-title-group">
                    <h2>Authentication</h2>
                    <p>Two-factor authentication and access verification</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="enable_2fa" name="enable_2fa" value="1" <?= ($settings['enable_2fa'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="enable_2fa">Enable Two-Factor Authentication (2FA)</label>
                    <div class="form-text">Require 2FA for all admin accounts. Adds extra security layer to prevent unauthorized access.</div>
                </div>

                <div class="settings-divider"></div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="force_https" name="force_https" value="1" <?= ($settings['force_https'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="force_https">Force HTTPS Connection</label>
                    <div class="form-text">Redirect all HTTP traffic to HTTPS for encrypted communications.</div>
                </div>
            </div>
        </div>

        <!-- Password Policy Section -->
        <div class="security-section">
            <div class="section-header password-section">
                <span class="section-icon">🔑</span>
                <div class="section-title-group">
                    <h2>Password Policy</h2>
                    <p>Define password requirements and complexity standards</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="password_min_length" class="form-label">Minimum Password Length</label>
                        <input type="number" class="form-control" id="password_min_length" name="password_min_length" placeholder="8" value="<?= htmlspecialchars($settings['password_min_length'] ?? '8') ?>" min="6">
                        <div class="form-text">Minimum characters required for passwords (6-20).</div>
                    </div>
                    <div class="form-group">
                        <label for="password_complexity" class="form-label">Password Complexity Level</label>
                        <select class="form-select" id="password_complexity" name="password_complexity">
                            <option value="low" <?= ($settings['password_complexity'] ?? '') == 'low' ? 'selected' : '' ?>>Low - Letters & Numbers</option>
                            <option value="medium" <?= ($settings['password_complexity'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium - Mixed Case & Numbers</option>
                            <option value="high" <?= ($settings['password_complexity'] ?? '') == 'high' ? 'selected' : '' ?>>High - Special Characters Required</option>
                        </select>
                        <div class="form-text">Higher complexity improves security but may reduce usability.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Management Section -->
        <div class="security-section">
            <div class="section-header session-section">
                <span class="section-icon">⏱️</span>
                <div class="section-title-group">
                    <h2>Session Management</h2>
                    <p>Control session timeout and login attempts</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="session_timeout" class="form-label">Session Timeout (Minutes)</label>
                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" placeholder="120" value="<?= htmlspecialchars($settings['session_timeout'] ?? '120') ?>" min="5">
                        <div class="form-text">Users will be logged out after this period of inactivity.</div>
                    </div>
                    <div class="form-group">
                        <label for="max_login_attempts" class="form-label">Maximum Login Attempts</label>
                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" placeholder="5" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" min="3">
                        <div class="form-text">Lock account after this many failed attempts (3-10).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Control Section -->
        <div class="security-section">
            <div class="section-header access-section">
                <span class="section-icon">🌐</span>
                <div class="section-title-group">
                    <h2>Access Control</h2>
                    <p>Restrict admin access to specific IP addresses</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="ip_whitelist_enabled" name="ip_whitelist_enabled" value="1" <?= ($settings['ip_whitelist_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="ip_whitelist_enabled">Enable IP Whitelisting</label>
                    <div class="form-text">Only allow admin access from specific IP addresses. Recommended for sensitive deployments.</div>
                </div>
                
                <div class="form-row full">
                    <div class="form-group">
                        <label for="ip_whitelist" class="form-label">IP Whitelist</label>
                        <textarea class="form-control" id="ip_whitelist" name="ip_whitelist" rows="4" placeholder="Enter one IP address per line&#10;Example:&#10;192.168.1.100&#10;10.0.0.0/8"><?= htmlspecialchars($settings['ip_whitelist'] ?? '') ?></textarea>
                        <div class="form-text">Enter allowed IP addresses, one per line. You can use CIDR notation (e.g., 192.168.1.0/24) for IP ranges.</div>
                    </div>
                </div>
                
                <div class="settings-divider"></div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="admin_ip_notification" name="admin_ip_notification" value="1" <?= ($settings['admin_ip_notification'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="admin_ip_notification">Notify Admin on New IP Login</label>
                    <div class="form-text">Send email notification when admin logs in from a new IP address.</div>
                </div>
            </div>
        </div>
        
        <!-- Security Logging Section -->
        <div class="security-section">
            <div class="section-header" style="background: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%); border-bottom: none; color: #333;">
                <span class="section-icon">📝</span>
                <div class="section-title-group">
                    <h2>Security Logging</h2>
                    <p>Monitor and log security events</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="log_failed_logins" name="log_failed_logins" value="1" <?= ($settings['log_failed_logins'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="log_failed_logins">Log Failed Login Attempts</label>
                    <div class="form-text">Record all failed login attempts for security analysis.</div>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="log_admin_activity" name="log_admin_activity" value="1" <?= ($settings['log_admin_activity'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="log_admin_activity">Log Admin Activity</label>
                    <div class="form-text">Track all administrative actions for audit purposes.</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="log_retention_days" class="form-label">Log Retention Period (Days)</label>
                        <input type="number" class="form-control" id="log_retention_days" name="log_retention_days" placeholder="30" value="<?= htmlspecialchars($settings['log_retention_days'] ?? '30') ?>" min="1" max="365">
                        <div class="form-text">Number of days to keep security logs before automatic deletion.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="button-group">
            <button type="submit" class="btn btn-primary">💾 Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Optional: Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.security-section');
        sections.forEach((section, index) => {
            section.style.animationDelay = (index * 0.1) + 's';
        });
    });
</script>