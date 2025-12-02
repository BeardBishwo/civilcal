<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Content Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage pages, posts, and static content</p>
        </div>
    </div>
</div>

<!-- Content Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Pages</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Created</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-plus-circle"></i> +3 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-edit" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Published</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['published_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Live Content</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-globe"></i> Online</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Drafts</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['draft_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Waiting</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-pen"></i> Unpublished</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-eye" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Page Views</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['page_views'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">This Month</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>
</div>

<!-- Content Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/content'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-files"></i>
            <span>All Pages</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/pages'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-file-alt"></i>
            <span>Pages</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/menus'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-bars"></i>
            <span>Menus</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/media'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-photo-video"></i>
            <span>Media</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">All Content</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Title</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Type</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Author</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Created</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($content_items)): ?>
                        <?php foreach ($content_items as $item): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div>
                                        <span style="color: #f9fafb;"><?php echo htmlspecialchars($item['title'] ?? 'Untitled'); ?></span>
                                        <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars(substr($item['slug'] ?? '', 0, 30)).(strlen($item['slug'] ?? '') > 30 ? '...' : ''); ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo $item['type'] === 'page' ? '#4cc9f0' : ($item['type'] === 'post' ? '#34d399' : '#fbbf24'); ?>; 
                                          background: <?php echo $item['type'] === 'page' ? 'rgba(76, 201, 240, 0.1)' : ($item['type'] === 'post' ? 'rgba(52, 211, 153, 0.1)' : 'rgba(251, 191, 36, 0.1)'); ?>; 
                                          padding: 0.25rem 0.5rem; 
                                          border-radius: 4px; 
                                          font-size: 0.75rem;">
                                        <?php echo ucfirst(htmlspecialchars($item['type'] ?? 'page')); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $item['status'] === 'published' ? 'success' : ($item['status'] === 'draft' ? 'warning' : 'error'); ?>" 
                                          style="background: rgba(<?php echo $item['status'] === 'published' ? '52, 211, 153, 0.1' : ($item['status'] === 'draft' ? '251, 191, 36, 0.1' : '248, 113, 113, 0.1'); ?>); 
                                                 border: 1px solid rgba(<?php echo $item['status'] === 'published' ? '52, 211, 153, 0.3' : ($item['status'] === 'draft' ? '251, 191, 36, 0.3' : '248, 113, 113, 0.3'); ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst(htmlspecialchars($item['status'] ?? 'draft')); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($item['author'] ?? 'System'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $item['created_at'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/content/'.($item['id'] ?? 0).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/'.($item['slug'] ?? '')); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 4px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/content/'.($item['id'] ?? 0).'/delete'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No content available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Content Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Content Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/content/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Page</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-clipboard-list"></i>
            <span>Page Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Content</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Content</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<!-- Content Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Content Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #4cc9f0;"></i>
                Home Page Content
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['homepage'] ?? 0); ?> pages</p>
            <a href="<?php echo app_base_url('/admin/content/category/home'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle" style="color: #34d399;"></i>
                About Pages
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['about'] ?? 0); ?> pages</p>
            <a href="<?php echo app_base_url('/admin/content/category/about'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #fbbf24;"></i>
                Policy Pages
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['policies'] ?? 0); ?> pages</p>
            <a href="<?php echo app_base_url('/admin/content/category/policies'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-question-circle" style="color: #22d3ee;"></i>
                Help Pages
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['help'] ?? 0); ?> pages</p>
            <a href="<?php echo app_base_url('/admin/content/category/help'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>