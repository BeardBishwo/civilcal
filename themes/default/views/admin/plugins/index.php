<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Plugin Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and configure application plugins</p>
        </div>
    </div>
</div>

<!-- Plugin Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-puzzle-piece" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Plugins</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_plugins'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Installed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +3 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-toggle-on" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Plugins</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_plugins'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Running</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Operational</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-download" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Updates Available</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['updates_available'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Ready</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-sync-alt"></i> Update Available</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-star" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Top Plugin</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['top_plugin'] ?? 'None'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Most Used</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-trophy"></i> Popular</small>
    </div>
</div>

<!-- Plugin Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/plugins'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-th-large"></i>
            <span>Installed</span>
        </a>
        <a href="<?php echo app_base_url('/admin/plugins/marketplace'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-store"></i>
            <span>Marketplace</span>
        </a>
        <a href="<?php echo app_base_url('/admin/plugins/upload'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-upload"></i>
            <span>Upload</span>
        </a>
        <a href="<?php echo app_base_url('/admin/plugins/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Installed Plugins</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($plugins)): ?>
                <?php foreach ($plugins as $plugin): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; border: <?php echo $plugin['is_active'] ? '1px solid rgba(52, 211, 153, 0.3)' : '1px solid rgba(102, 126, 234, 0.2)'; ?>;">
                        <div style="padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                        <i class="fas fa-<?php echo $plugin['icon'] ?? 'puzzle-piece'; ?>" style="color: <?php echo $plugin['is_active'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                                        <h3 style="color: <?php echo $plugin['is_active'] ? '#f9fafb' : '#9ca3af'; ?>; margin: 0; font-size: 1.125rem;"><?php echo htmlspecialchars($plugin['name'] ?? 'Unknown Plugin'); ?></h3>
                                    </div>
                                    <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars(substr($plugin['description'] ?? '', 0, 100)).(strlen($plugin['description'] ?? '') > 100 ? '...' : ''); ?></p>
                                </div>
                                <?php if ($plugin['is_active']): ?>
                                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                                <span style="color: #9ca3af; background: rgba(102, 126, 234, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">v<?php echo htmlspecialchars($plugin['version'] ?? '1.0.0'); ?></span>
                                <span style="color: #9ca3af; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;"><?php echo htmlspecialchars($plugin['author'] ?? 'Developer'); ?></span>
                                <span style="color: #9ca3af; background: rgba(245, 158, 11, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;"><?php echo htmlspecialchars($plugin['category'] ?? 'General'); ?> Plugin</span>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php if (!$plugin['is_active']): ?>
                                    <a href="<?php echo app_base_url('/admin/plugins/'.($plugin['id'] ?? 0).'/activate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-play"></i>
                                        <span>Activate</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo app_base_url('/admin/plugins/'.($plugin['id'] ?? 0).'/deactivate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-stop"></i>
                                        <span>Deactivate</span>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo app_base_url('/admin/plugins/'.($plugin['id'] ?? 0).'/configure'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/plugins/'.($plugin['id'] ?? 0).'/remove'); ?>" 
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
                    <i class="fas fa-puzzle-piece" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No plugins installed</p>
                    <a href="<?php echo app_base_url('/admin/plugins/marketplace'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-store"></i>
                        <span>Browse Marketplace</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Plugin Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Plugin Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/plugins/install'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-download"></i>
            <span>Install Plugin</span>
        </a>

        <a href="<?php echo app_base_url('/admin/plugins/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-upload"></i>
            <span>Upload Plugin</span>
        </a>

        <a href="<?php echo app_base_url('/admin/plugins/marketplace'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-store"></i>
            <span>Plugin Marketplace</span>
        </a>

        <a href="<?php echo app_base_url('/admin/plugins/updates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-sync-alt"></i>
            <span>Update Plugins</span>
        </a>

        <a href="<?php echo app_base_url('/admin/plugins/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Plugin Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>