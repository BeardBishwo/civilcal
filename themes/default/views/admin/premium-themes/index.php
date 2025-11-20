<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Premium Themes Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage premium themes, licenses, and installations</p>
    </div>
</div>

<!-- Premium Themes Overview -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-crown" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Premium Themes</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;">Premium Theme Marketplace</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['premium_themes'] ?? 0); ?></div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-star"></i> Featured Themes</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Available for Installation</span>
            </a>
        </div>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-key" style="font-size: 1.5rem; color: #34d399; margin-bottom: 0.5rem;">License Management</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Theme Licenses</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_licenses'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Valid Licenses</small>
    </div>
</div>

<!-- Theme Installation Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Installation</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Available for Download</h3>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Upload and install premium themes</p>
        </div>
    </div>
</div>

<!-- License Management -->
<div class="admin-card">
    <h2 class="admin-card-title">License Status</h2>
    <div class="admin-grid">
        <div class="admin-card">
            <div style="text-align: center;">
                <a href="<?php echo app_base_url('/admin/premium-themes/install'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-download"></i>
            <span>Download Theme</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/premium-themes/licenses'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-upload"></i>
                <span>Upload ZIP</span>
            </a>
        </div>
    </div>
</div>

<!-- Theme Customization -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Customization</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/premium-themes/customize'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-paint-brush"></i>
            <span>Customize Theme</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/premium-themes/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-eye"></i>
                    <span>Preview Theme</span>
        </a>
    </div>
</div>

<!-- Marketplace Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Marketplace</h2>
    <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Browse premium themes marketplace</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>