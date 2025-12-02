<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Logo Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage application logos, icons, and brand assets</p>
        </div>
    </div>
</div>

<!-- Logo Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-icons" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Logos</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_logos'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Brand Assets</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +1 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-hdd" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Storage Used</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['storage_used'] ?? '0 MB'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">of <?php echo $stats['storage_limit'] ?? '100 MB'; ?></div>
        <small style="color: <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'Almost Full' : 'Space Available'; ?>
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Logo Types</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['logo_types'] ?? 'PNG, SVG, JPG'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Formats</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Optimized</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-crop-alt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Logo</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['active_logo'] ?? 'Default'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Currently Used</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check"></i> Applied</small>
    </div>
</div>

<!-- Current Logo Preview -->
<div class="admin-card">
    <h2 class="admin-card-title">Current Logo</h2>
    <div style="display: flex; align-items: center; gap: 2rem; padding: 1.5rem;">
        <div style="flex: 1; text-align: center;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem;">Current Logo Preview</h3>
            <div style="display: inline-block; padding: 1rem; background: rgba(15, 23, 42, 0.5); border-radius: 8px;">
                <img src="<?php echo $current_logo['path'] ?? app_base_url('/img/logo.png'); ?>" 
                     alt="Current Logo" 
                     style="max-width: 200px; max-height: 100px; object-fit: contain;">
            </div>
            <p style="color: #9ca3af; margin-top: 1rem;"><?php echo htmlspecialchars($current_logo['name'] ?? 'Default Logo'); ?></p>
        </div>
        <div style="flex: 1; display: flex; flex-direction: column; gap: 1rem;">
            <a href="<?php echo app_base_url('/admin/logos/change'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                <i class="fas fa-sync-alt"></i>
                <span>Change Logo</span>
            </a>
            <a href="<?php echo app_base_url('/admin/logos/reset'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
                <i class="fas fa-undo"></i>
                <span>Reset to Default</span>
            </a>
            <a href="<?php echo app_base_url('/admin/logos/settings'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
                <i class="fas fa-cog"></i>
                <span>Logo Settings</span>
            </a>
        </div>
    </div>
</div>

<!-- Available Logos -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Logos</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; padding: 1rem;">
            <?php if (!empty($logos)): ?>
                <?php foreach ($logos as $logo): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; text-align: center; border: <?php echo $logo['is_active'] ? '2px solid #4cc9f0' : '1px solid rgba(102, 126, 234, 0.2)'; ?>">
                        <div style="height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.8);">
                            <img src="<?php echo $logo['path'] ?? ''; ?>" alt="<?php echo htmlspecialchars($logo['name'] ?? 'Logo'); ?>" 
                                 style="max-width: 100%; max-height: 80px; object-fit: contain;">
                        </div>
                        <div style="padding: 0.75rem;">
                            <h4 style="color: #f9fafb; font-size: 0.875rem; margin: 0;"><?php echo htmlspecialchars(substr($logo['name'] ?? 'Unknown', 0, 15)).(strlen($logo['name'] ?? 'Unknown') > 15 ? '...' : ''); ?></h4>
                            <p style="color: #9ca3af; font-size: 0.75rem; margin: 0.25rem 0;"><?php echo $logo['size'] ?? '0 KB'; ?></p>
                            <div style="margin-top: 0.5rem; display: flex; gap: 0.25rem; justify-content: center;">
                                <a href="<?php echo app_base_url('/admin/logos/'.($logo['id'] ?? 0).'/activate'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.75rem;">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/logos/'.($logo['id'] ?? 0).'/edit'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/logos/'.($logo['id'] ?? 0).'/delete'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.75rem;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-icons" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No logos in the library</p>
                    <a href="<?php echo app_base_url('/admin/logos/upload'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-upload"></i>
                        <span>Upload Logo</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Logo Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Logo Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/logos/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-upload"></i>
            <span>Upload Logo</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logos/gallery'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-th-large"></i>
            <span>Logo Gallery</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logos/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-palette"></i>
            <span>Logo Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/logos/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Logo Settings</span>
        </a>
    </div>
</div>

<!-- Upload Logo -->
<div class="admin-card">
    <h2 class="admin-card-title">Upload New Logo</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 2rem; border-radius: 8px; text-align: center; border: 2px dashed rgba(102, 126, 234, 0.3);">
        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #4cc9f0; margin-bottom: 1rem; display: block;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">Upload New Logo</h3>
        <p style="color: #9ca3af; margin-bottom: 1.5rem;">Drag & drop your logo file or click to select</p>
        <form action="<?php echo app_base_url('/admin/logos/upload'); ?>" method="post" enctype="multipart/form-data" style="display: inline-block;">
            <input type="file" name="logo" style="display: none;" id="logo-upload" accept="image/*">
            <label for="logo-upload" style="display: inline-block; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer; text-decoration: none;">
                <i class="fas fa-folder-open"></i>
                <span>Choose Logo File</span>
            </label>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; margin-left: 1rem; cursor: pointer;">
                <i class="fas fa-upload"></i>
                <span>Upload Logo</span>
            </button>
        </form>
        <p style="color: #9ca3af; margin-top: 1rem; font-size: 0.75rem;">Recommended formats: SVG, PNG, JPG. Maximum size: 2MB. Minimum dimensions: 200x50px.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>