<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>License Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage theme licenses and activations</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">View and manage all premium theme licenses</p>
    </div>
</div>

<!-- License Overview -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-key" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Licenses</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_licenses'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Valid Licenses</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">License Status</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['expiring_soon'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Expired Licenses</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Licenses Expiring Soon</small>
    </div>
</div>

<!-- License Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">License Operations</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/premium-themes/licenses/validate'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
                    <i class="fas fa-search"></i>
                    <span>Validate Licenses</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/premium-themes/licenses/renew'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-sync-alt"></i>
                        <span>Renew Licenses</span>
                </a>
                
                <a href="<?php echo app_base_url('/admin/premium-themes/licenses/activate'); ?>"
                   style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New License</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>