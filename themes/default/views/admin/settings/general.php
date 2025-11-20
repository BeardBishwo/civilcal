<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>General Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure basic system settings and preferences</p>
    </div>
</div>

<!-- Settings Categories -->
<div class="admin-grid">
    <div class="admin-card">
        <h2 class="admin-card-title">Application Settings</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <i class="fas fa-cog" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                    <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">General</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System configuration</h3>
                <a href="<?php echo app_base_url('/admin/settings/general'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit"></i>
                    <span>Edit Settings</span>
                </a>
            </div>
            
            <div style="text-align: center;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: #34d399;"></i>
                </div>
            </div>
            
            <div style="text-align: center;">
                <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #f87171; margin-bottom: 0.5rem;">User Settings</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">User management</p>
                <a href="<?php echo app_base_url('/admin/settings/users'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                    <i class="fas fa-cog"></i>
                    <span>Configure</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Current Settings -->
<div class="admin-card">
    <h2 class="admin-card-title">Current Configuration</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Security Settings</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Security & Privacy</h3>
                <a href="<?php echo app_base_url('/admin/settings/security'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-lock"></i>
                    <span>Security Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Quick Configuration</h2>
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="<?php echo app_base_url('/admin/settings/email'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none;">
                    <i class="fas fa-envelope"></i>
                    <span>Email Settings</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/settings/api'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-rocket"></i>
                    <span>Performance</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>