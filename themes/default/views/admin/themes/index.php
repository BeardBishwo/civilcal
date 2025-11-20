<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Theme Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and customize application themes</p>
    </div>
</div>

<!-- Available Themes -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Themes</h2>
    <div class="admin-grid">
        <div class="admin-card">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
            <i class="fas fa-palette" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 0.5rem;">Default Theme</h3>
            <div style="font-size: 1.25rem; font-weight: 600; color: #f9fafb; margin-bottom: 0.5rem;">Active</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Clean and professional design</p>
                
                <div style="margin-top: 1rem; display: flex; gap: 0.75rem; justify-content: center;">
                    <a href="<?php echo app_base_url('/admin/themes/customize'); ?>"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                        <i class="fas fa-edit"></i>
                        <span>Customize</span>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="admin-card">
            <div style="text-align: center;">
                    <i class="fas fa-eye" style="font-size: 1rem;">Preview</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Theme Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/premium-themes'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Upload New Theme</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/themes/upload'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-download"></i>
                    <span>Download Theme</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/themes/activate'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh Themes</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Premium Themes Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Premium Themes</h2>
    <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Access premium themes with enhanced features</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>