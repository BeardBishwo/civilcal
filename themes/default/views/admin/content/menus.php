<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Menu Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage navigation menus and menu items</p>
        </div>
    </div>
</div>

<!-- Menu Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bars" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Menus</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_menus'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Created</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-plus-circle"></i> +2 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-link" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Links</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_links'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Menu Items</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-toggle-on" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Menus</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_menus'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Published</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Live</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Users Online</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Using Menus</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Active</small>
    </div>
</div>

<!-- Content Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/content'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-files"></i>
            <span>All Content</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/pages'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-file-alt"></i>
            <span>Pages</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/menus'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-bars"></i>
            <span>Menus</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/media'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-photo-video"></i>
            <span>Media</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Navigation Menus</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($menus)): ?>
                <?php foreach ($menus as $menu): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden;">
                        <div style="padding: 1rem; background: rgba(76, 201, 240, 0.1); border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                            <h3 style="color: #f9fafb; margin: 0; display: flex; justify-content: space-between; align-items: center;">
                                <span><?php echo htmlspecialchars($menu['name'] ?? 'Unnamed Menu'); ?></span>
                                <span style="font-size: 0.75rem; color: #9ca3af;"><?php echo $menu['location'] ?? 'Global'; ?> Menu</span>
                            </h3>
                        </div>
                        
                        <div style="padding: 1rem;">
                            <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="color: #9ca3af; font-size: 0.75rem;"><i class="fas fa-link"></i> <?php echo number_format($menu['item_count'] ?? 0); ?> items</span>
                                <span style="color: #9ca3af; font-size: 0.75rem;"><i class="fas fa-eye"></i> <?php echo number_format($menu['views'] ?? 0); ?> views</span>
                                <span style="color: #9ca3af; font-size: 0.75rem;">
                                    <i class="fas fa-<?php echo $menu['is_active'] ? 'toggle-on' : 'toggle-off'; ?>" style="color: <?php echo $menu['is_active'] ? '#34d399' : '#f87171'; ?>;"></i>
                                    <?php echo $menu['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <h4 style="color: #f9fafb; margin: 0 0 0.5rem 0; font-size: 0.875rem;">Menu Items:</h4>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php if (!empty($menu['items'])): ?>
                                        <?php foreach (array_slice($menu['items'], 0, 3) as $item): ?>
                                            <li style="color: #9ca3af; font-size: 0.75rem; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                                <i class="fas fa-chevron-right" style="color: #4cc9f0; font-size: 0.5rem;"></i>
                                                <span><?php echo htmlspecialchars($item['title'] ?? 'Untitled Link'); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php if (count($menu['items']) > 3): ?>
                                            <li style="color: #9ca3af; font-size: 0.75rem; margin-top: 0.25rem;">
                                                <i class="fas fa-plus" style="color: #4cc9f0; font-size: 0.5rem;"></i>
                                                <?php echo count($menu['items']) - 3; ?> more items...
                                            </li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <li style="color: #9ca3af; font-size: 0.75rem;">No items added</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="<?php echo app_base_url('/admin/content/menus/'.($menu['id'] ?? 0).'/edit'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/content/menus/'.($menu['id'] ?? 0).'/preview'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.75rem;">
                                    <i class="fas fa-eye"></i>
                                    <span>Preview</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/content/menus/'.($menu['id'] ?? 0).'/toggle'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(<?php echo $menu['is_active'] ? '248, 113, 113, 0.1' : '52, 211, 153, 0.1'; ?>); border: 1px solid rgba(<?php echo $menu['is_active'] ? '248, 113, 113, 0.2' : '52, 211, 153, 0.2'; ?>); border-radius: 4px; text-decoration: none; color: <?php echo $menu['is_active'] ? '#f87171' : '#34d399'; ?>; font-size: 0.75rem;">
                                    <i class="fas fa-<?php echo $menu['is_active'] ? 'toggle-off' : 'toggle-on'; ?>"></i>
                                    <span><?php echo $menu['is_active'] ? 'Disable' : 'Enable'; ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-bars" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No menus created yet</p>
                    <a href="<?php echo app_base_url('/admin/content/menus/create'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Menu</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Menu Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Menu Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/content/menus/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Menu</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/menus/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-clipboard-list"></i>
            <span>Menu Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/menus/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Menu</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/menus/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Menus</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/menus/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Menu Settings</span>
        </a>
    </div>
</div>

<!-- Menu Locations -->
<div class="admin-card">
    <h2 class="admin-card-title">Menu Locations</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #4cc9f0;"></i>
                Header Navigation
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Primary navigation displayed in the header</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Assigned Menu:</span>
                <span style="color: #f9fafb;"><?php echo htmlspecialchars($locations['header'] ?? 'None'); ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #34d399;"></i>
                User Dashboard
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Navigation for user dashboard area</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Assigned Menu:</span>
                <span style="color: #f9fafb;"><?php echo htmlspecialchars($locations['dashboard'] ?? 'None'); ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-footprints" style="color: #fbbf24;"></i>
                Footer Navigation
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Footer links and navigation</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Assigned Menu:</span>
                <span style="color: #f9fafb;"><?php echo htmlspecialchars($locations['footer'] ?? 'None'); ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-mobile-alt" style="color: #22d3ee;"></i>
                Mobile Menu
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Responsive mobile navigation</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Assigned Menu:</span>
                <span style="color: #f9fafb;"><?php echo htmlspecialchars($locations['mobile'] ?? 'None'); ?></span>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>