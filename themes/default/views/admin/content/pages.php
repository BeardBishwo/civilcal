<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Page Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage all static pages and their content</p>
        </div>
    </div>
</div>

<!-- Page Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Pages</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Created</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +5 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Published</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['published_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Live</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-globe"></i> Public</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Drafts</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['draft_pages'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Unpublished</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-pen"></i> Work in Progress</small>
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
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-files"></i>
            <span>All Content</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/pages'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
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
    
    <h2 class="admin-card-title">Managed Pages</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Page Title</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">URL</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Views</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Modified</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pages)): ?>
                        <?php foreach ($pages as $page): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div>
                                        <span style="color: #f9fafb;"><?php echo htmlspecialchars($page['title'] ?? 'Untitled Page'); ?></span>
                                        <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars($page['description'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/'.($page['slug'] ?? '')); ?>" 
                                       style="color: #4cc9f0; text-decoration: none;" target="_blank">
                                        /<?php echo htmlspecialchars($page['slug'] ?? ''); ?>
                                    </a>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $page['status'] === 'published' ? 'success' : 'warning'; ?>" 
                                          style="background: rgba(<?php echo $page['status'] === 'published' ? '52, 211, 153, 0.1' : '251, 191, 36, 0.1'; ?>); 
                                                 border: 1px solid rgba(<?php echo $page['status'] === 'published' ? '52, 211, 153, 0.3' : '251, 191, 36, 0.3'; ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst(htmlspecialchars($page['status'] ?? 'draft')); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo number_format($page['views'] ?? 0); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $page['updated_at'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/content/pages/'.($page['id'] ?? 0).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/'.($page['slug'] ?? '')); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 4px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/content/pages/'.($page['id'] ?? 0).'/delete'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No pages created yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Page Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Page Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/content/pages/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Page</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/pages/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-clipboard-list"></i>
            <span>Page Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/pages/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Pages</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/pages/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Pages</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/pages/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Page Settings</span>
        </a>
    </div>
</div>

<!-- Popular Pages -->
<div class="admin-card">
    <h2 class="admin-card-title">Popular Pages</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Page</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Views</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Last Visited</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($popular_pages)): ?>
                        <?php foreach (array_slice($popular_pages, 0, 5) as $page): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div>
                                        <a href="<?php echo app_base_url('/'.($page['slug'] ?? '')); ?>" 
                                           style="color: #f9fafb; text-decoration: none;"><?php echo htmlspecialchars($page['title'] ?? 'Untitled'); ?></a>
                                        <div style="color: #9ca3af; font-size: 0.75rem;">/<?php echo htmlspecialchars($page['slug'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo number_format($page['views'] ?? 0); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $page['last_visited'] ?? 'Never'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo ($page['trend'] ?? 0) >= 0 ? '#34d399' : '#f87171'; ?>; display: flex; align-items: center; gap: 0.25rem;">
                                        <i class="fas <?php echo ($page['trend'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                        <?php echo abs($page['trend'] ?? 0); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1rem; color: #9ca3af;">No popular pages data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>