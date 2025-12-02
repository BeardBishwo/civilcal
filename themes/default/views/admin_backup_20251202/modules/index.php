<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Module Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and configure application modules</p>
        </div>
    </div>
</div>

<!-- Module Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-cubes" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Modules</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_modules'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Installed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +2 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-toggle-on" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Modules</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_modules'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Running</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Operational</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Calculators</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculators'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Across All Modules</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Functional</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Security Status</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $stats['security_status'] ?? 'Good'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Module Security</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-lock"></i> Secure</small>
    </div>
</div>

<!-- Module Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/modules'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-th-large"></i>
            <span>Installed</span>
        </a>
        <a href="<?php echo app_base_url('/admin/modules/categories'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-layer-group"></i>
            <span>Categories</span>
        </a>
        <a href="<?php echo app_base_url('/admin/modules/marketplace'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-store"></i>
            <span>Marketplace</span>
        </a>
        <a href="<?php echo app_base_url('/admin/modules/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Installed Modules</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($modules)): ?>
                <?php foreach ($modules as $module): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; border: <?php echo $module['status'] === 'active' ? '1px solid rgba(52, 211, 153, 0.3)' : '1px solid rgba(102, 126, 234, 0.2)'; ?>;">
                        <div style="padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                        <i class="fas fa-<?php echo $module['icon'] ?? 'cube'; ?>" style="color: <?php echo $module['status'] === 'active' ? '#34d399' : '#9ca3af'; ?>;"></i>
                                        <h3 style="color: <?php echo $module['status'] === 'active' ? '#f9fafb' : '#9ca3af'; ?>; margin: 0; font-size: 1.125rem;"><?php echo htmlspecialchars($module['name'] ?? 'Unknown Module'); ?></h3>
                                    </div>
                                    <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars(substr($module['description'] ?? '', 0, 120)).(strlen($module['description'] ?? '') > 120 ? '...' : ''); ?></p>
                                </div>
                                <?php if ($module['status'] === 'active'): ?>
                                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; color: #9ca3af; margin-bottom: 1rem; font-size: 0.75rem;">
                                <span>Version: <?php echo htmlspecialchars($module['version'] ?? '1.0.0'); ?></span>
                                <span>Category: <?php echo htmlspecialchars($module['category'] ?? 'General'); ?></span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 0.875rem;">
                                <span style="color: #9ca3af;">Calculators:</span>
                                <span style="color: #f9fafb;"><?php echo number_format($module['calculators_count'] ?? 0); ?></span>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php if ($module['status'] !== 'active'): ?>
                                    <a href="<?php echo app_base_url('/admin/modules/'.($module['id'] ?? 0).'/activate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-play"></i>
                                        <span>Activate</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo app_base_url('/admin/modules/'.($module['id'] ?? 0).'/deactivate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-stop"></i>
                                        <span>Deactivate</span>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo app_base_url('/admin/modules/'.($module['id'] ?? 0).'/settings'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/modules/'.($module['id'] ?? 0).'/remove'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-cubes" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No modules installed</p>
                    <a href="<?php echo app_base_url('/admin/modules/marketplace'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-store"></i>
                        <span>Browse Marketplace</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Module Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Module Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #4cc9f0;"></i>
                Engineering
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['engineering'] ?? 0); ?> modules</p>
            <a href="<?php echo app_base_url('/admin/modules/category/engineering'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #34d399;"></i>
                Scientific
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['scientific'] ?? 0); ?> modules</p>
            <a href="<?php echo app_base_url('/admin/modules/category/scientific'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line" style="color: #fbbf24;"></i>
                Financial
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['financial'] ?? 0); ?> modules</p>
            <a href="<?php echo app_base_url('/admin/modules/category/financial'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cogs" style="color: #22d3ee;"></i>
                Utility
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['utility'] ?? 0); ?> modules</p>
            <a href="<?php echo app_base_url('/admin/modules/category/utility'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
    </div>
</div>

<!-- Module Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Module Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/modules/install'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-download"></i>
            <span>Install Module</span>
        </a>

        <a href="<?php echo app_base_url('/admin/modules/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-upload"></i>
            <span>Upload Module</span>
        </a>

        <a href="<?php echo app_base_url('/admin/modules/marketplace'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-store"></i>
            <span>Module Marketplace</span>
        </a>

        <a href="<?php echo app_base_url('/admin/modules/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Module Settings</span>
        </a>
    </div>
</div>

<!-- Module Security -->
<div class="admin-card">
    <h2 class="admin-card-title">Module Security</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-code" style="color: #4cc9f0;"></i>
                Code Scanning
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Automated security scanning for installed modules</p>
            <a href="<?php echo app_base_url('/admin/modules/security/scan'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-search"></i>
                <span>Run Scan</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-secret" style="color: #34d399;"></i>
                Permissions
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Configure module access rights</p>
            <a href="<?php echo app_base_url('/admin/modules/security/permissions'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-key"></i>
                <span>Manage Permissions</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-history" style="color: #fbbf24;"></i>
                Audit Logs
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Track module access and changes</p>
            <a href="<?php echo app_base_url('/admin/modules/security/audit'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-chart-bar"></i>
                <span>View Logs</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #22d3ee;"></i>
                Security Settings
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Configure security parameters</p>
            <a href="<?php echo app_base_url('/admin/modules/security/settings'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>