<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Theme Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and configure application themes</p>
        </div>
    </div>
</div>

<!-- Theme Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-palette" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Themes</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_themes'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Available</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +2 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-toggle-on" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Theme</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['active_theme'] ?? 'Default Theme'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Currently Used</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Active</small>
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
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Most Popular</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['most_popular_theme'] ?? 'None'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">User Favorite</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-trophy"></i> Popular</small>
    </div>
</div>

<!-- Theme Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/themes'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-th-large"></i>
            <span>Installed</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/marketplace'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-store"></i>
            <span>Marketplace</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/upload'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-upload"></i>
            <span>Upload</span>
        </a>
        <a href="<?php echo app_base_url('/admin/themes/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Available Themes</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($themes)): ?>
                <?php foreach ($themes as $theme): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; border: <?php echo $theme['is_active'] ? '2px solid rgba(76, 201, 240, 0.5)' : '1px solid rgba(102, 126, 234, 0.2)'; ?>;">
                        <div style="height: 150px; background: linear-gradient(to right, <?php echo $theme['color_scheme'] ?? '#4cc9f0, #34d399'; ?>); display: flex; align-items: center; justify-content: center;">
                            <?php if (isset($theme['screenshot'])): ?>
                                <img src="<?php echo $theme['screenshot']; ?>" alt="<?php echo htmlspecialchars($theme['name'] ?? 'Theme'); ?>" 
                                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <div style="background: rgba(0, 0, 0, 0.2); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-palette" style="font-size: 3rem; color: rgba(255, 255, 255, 0.5);"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h3 style="color: <?php echo $theme['is_active'] ? '#4cc9f0' : '#f9fafb'; ?>; margin: 0 0 0.5rem 0; font-size: 1.125rem;"><?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?></h3>
                                    <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars(substr($theme['description'] ?? '', 0, 80)).(strlen($theme['description'] ?? '') > 80 ? '...' : ''); ?></p>
                                </div>
                                <?php if ($theme['is_active']): ?>
                                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; color: #9ca3af; font-size: 0.75rem; margin-bottom: 1rem;">
                                <span>By <?php echo htmlspecialchars($theme['author'] ?? 'Developer'); ?></span>
                                <span>v<?php echo $theme['version'] ?? '1.0'; ?></span>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php if (!$theme['is_active']): ?>
                                    <a href="<?php echo app_base_url('/admin/themes/'.($theme['id'] ?? 0).'/activate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; justify-content: center; min-width: 70px;">
                                        <i class="fas fa-play"></i>
                                        <span>Activate</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo app_base_url('/admin/themes/'.($theme['id'] ?? 0).'/deactivate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 70px;">
                                        <i class="fas fa-stop"></i>
                                        <span>Deactivate</span>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo app_base_url('/admin/themes/'.($theme['id'] ?? 0).'/customize'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; justify-content: center; min-width: 70px;">
                                    <i class="fas fa-paint-brush"></i>
                                    <span>Customize</span>
                                </a>
                                
                                <a href="<?php echo app_base_url('/admin/themes/'.($theme['id'] ?? 0).'/delete'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 70px;">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-palette" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No themes installed yet</p>
                    <a href="<?php echo app_base_url('/admin/themes/marketplace'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-store"></i>
                        <span>Browse Marketplace</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Theme Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #4cc9f0;"></i>
                Homepage
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['homepage'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/themes/category/homepage'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-search"></i>
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #34d399;"></i>
                Calculator UI
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['calculator'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/themes/category/calculator'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-search"></i>
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #fbbf24;"></i>
                User Dashboard
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['dashboard'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/themes/category/dashboard'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-search"></i>
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #22d3ee;"></i>
                Premium
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['premium'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/themes/category/premium'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-search"></i>
                <span>Browse</span>
            </a>
        </div>
    </div>
</div>

<!-- Theme Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/themes/install'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-download"></i>
            <span>Install Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-upload"></i>
            <span>Upload Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/marketplace'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-store"></i>
            <span>Theme Marketplace</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/customize'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-paint-brush"></i>
            <span>Customize Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Theme Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/themes/updates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); border-radius: 6px; text-decoration: none; color: #ec4899;">
            <i class="fas fa-sync-alt"></i>
            <span>Check Updates</span>
        </a>
    </div>
</div>

<!-- Active Theme Preview -->
<div class="admin-card">
    <h2 class="admin-card-title">Active Theme Preview</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 2rem; border-radius: 8px; text-align: center;">
        <div style="background: rgba(30, 41, 59, 0.8); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; min-height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <div style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem; font-weight: 600;"><?php echo htmlspecialchars($active_theme['name'] ?? 'Default Theme'); ?></div>
            <div style="color: #9ca3af; margin-bottom: 1rem;">Sample preview of the active theme</div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                    <i class="fas fa-edit"></i>
                    <span>Button</span>
                </button>
                <button style="padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                    <i class="fas fa-check"></i>
                    <span>Success</span>
                </button>
                <button style="padding: 0.5rem 1rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; cursor: pointer;">
                    <i class="fas fa-times"></i>
                    <span>Error</span>
                </button>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/themes/active/customize'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                <i class="fas fa-paint-brush"></i>
                <span>Customize Active</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/themes/active/settings'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
                <i class="fas fa-cog"></i>
                <span>Theme Settings</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/themes/preview'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
                <i class="fas fa-eye"></i>
                <span>Live Preview</span>
            </a>
        </div>
    </div>
</div>

<!-- Theme Documentation -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Documentation</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-book" style="color: #4cc9f0;"></i>
                Theme Development
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Learn how to create and customize themes</p>
            <a href="<?php echo app_base_url('/admin/themes/documentation/development'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Development Guide</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-graduation-cap" style="color: #34d399;"></i>
                Customization Tutorials
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Step-by-step guides for theme customization</p>
            <a href="<?php echo app_base_url('/admin/themes/documentation/tutorials'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>View Tutorials</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-code" style="color: #fbbf24;"></i>
                Template Reference
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Documentation for template structure</p>
            <a href="<?php echo app_base_url('/admin/themes/documentation/templates'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Template Guide</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-support" style="color: #22d3ee;"></i>
                Support
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Get help with theme issues</p>
            <a href="<?php echo app_base_url('/admin/themes/documentation/support'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Get Support</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>