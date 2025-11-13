<?php
$autoDeleteEnabled = getAutoDeleteSetting();
?>

<div class="step-content">
    <div class="step-icon" style="color: var(--success);">
        <i class="fas fa-check-circle"></i>
    </div>
    <h2 class="step-heading">Installation Complete!</h2>
    <p class="step-description">
        Congratulations! Bishwo Calculator has been successfully installed and configured.
        Your professional engineering calculator platform is ready to use.
    </p>
    
    <div class="completion-stats">
        <div class="stat-card">
            <div class="stat-number">✓</div>
            <div class="stat-label">Database Configured</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">✓</div>
            <div class="stat-label">Admin Account Created</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">✓</div>
            <div class="stat-label">Settings Saved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">50+</div>
            <div class="stat-label">Calculators Ready</div>
        </div>
    </div>
    
    <div style="background: linear-gradient(135deg, var(--success), #059669); color: white; padding: 24px; border-radius: 12px; margin: 32px 0;">
        <h3 style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-rocket"></i>
            Your Site is Ready!
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 14px;">
            <div>
                <strong>Admin Email:</strong><br>
                <?php echo htmlspecialchars($_SESSION['admin_user']['admin_email'] ?? 'admin@example.com'); ?>
            </div>
            <div>
                <strong>Admin Username:</strong><br>
                <?php echo htmlspecialchars($_SESSION['admin_user']['admin_username'] ?? 'admin'); ?>
            </div>
            <div>
                <strong>Site URL:</strong><br>
                <?php echo htmlspecialchars($_SESSION['site_settings']['site_url'] ?? 'http://' . $_SERVER['HTTP_HOST']); ?>
            </div>
        </div>
    </div>
    
    <?php if ($autoDeleteEnabled): ?>
    <div class="alert" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #d97706;">
        <i class="fas fa-info-circle"></i>
        <strong>Auto-Delete Enabled:</strong> The installer folder will be automatically deleted after you proceed to the admin dashboard for security purposes.
    </div>
    <?php else: ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Security Notice:</strong> For security reasons, please manually delete the <code>install/</code> folder after completing the installation.
    </div>
    <?php endif; ?>
    
    <div style="text-align: left; max-width: 500px; margin: 24px auto;">
        <h3 style="margin-bottom: 16px; text-align: center;">Next Steps:</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">1</div>
                <span>Access your admin dashboard to configure modules</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">2</div>
                <span>Customize site appearance and settings</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">3</div>
                <span>Test the engineering calculators</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">4</div>
                <span>Set up email configuration for notifications</span>
            </div>
        </div>
    </div>
    
    <form method="POST" style="margin-top: 32px;">
        <input type="hidden" name="action" value="finish_install">
        
        <div class="btn-actions" style="justify-content: center;">
            <button type="submit" class="btn btn-success" style="font-size: 18px; padding: 16px 32px;">
                <i class="fas fa-rocket"></i>
                Launch Admin Dashboard
            </button>
        </div>
    </form>
    
    <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--gray-600);">
        <p>Thank you for choosing Bishwo Calculator!</p>
        <p style="margin-top: 4px;">Need help? Check our <a href="#" style="color: var(--primary);">documentation</a> or <a href="#" style="color: var(--primary);">contact support</a>.</p>
    </div>
</div>
