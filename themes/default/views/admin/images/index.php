<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Image Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage system images, logos, and visual assets</p>
        </div>
    </div>
</div>

<!-- Image Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-images" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Images</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_images'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">In Library</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +2 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-hdd" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Storage Used</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['storage_used'] ?? '0 MB'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">of <?php echo $stats['storage_limit'] ?? '1 GB'; ?></div>
        <small style="color: <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($stats['storage_used'] ?? 0) / ($stats['storage_limit'] ?? 1) > 0.8 ? 'Almost Full' : 'Space Available'; ?>
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file-image" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Image Types</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['image_types'] ?? 'JPG, PNG, GIF'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Supported</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Optimized</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-compress" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Optimized</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['optimized_images'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Compressed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Performance</small>
    </div>
</div>

<!-- Image Gallery -->
<div class="admin-card">
    <h2 class="admin-card-title">Image Gallery</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; padding: 1rem;">
            <?php if (!empty($images)): ?>
                <?php foreach (array_slice($images, 0, 12) as $image): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; text-align: center;">
                        <div style="height: 100px; display: flex; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.8);">
                            <img src="<?php echo $image['path'] ?? ''; ?>" alt="<?php echo htmlspecialchars($image['name'] ?? 'Image'); ?>" 
                                 style="max-width: 100%; max-height: 100px; object-fit: contain;">
                        </div>
                        <div style="padding: 0.75rem;">
                            <h4 style="color: #f9fafb; font-size: 0.875rem; margin: 0;"><?php echo htmlspecialchars(substr($image['name'] ?? 'Unknown', 0, 15)).(strlen($image['name'] ?? 'Unknown') > 15 ? '...' : ''); ?></h4>
                            <p style="color: #9ca3af; font-size: 0.75rem; margin: 0.25rem 0;"><?php echo $image['size'] ?? '0 KB'; ?></p>
                            <div style="margin-top: 0.5rem; display: flex; gap: 0.25rem; justify-content: center;">
                                <a href="<?php echo app_base_url('/admin/images/'.($image['id'] ?? 0).'/edit'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/images/'.($image['id'] ?? 0).'/delete'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.75rem;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-images" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No images in the gallery</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Image Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/images/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-upload"></i>
            <span>Upload Images</span>
        </a>

        <a href="<?php echo app_base_url('/admin/images/gallery'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-th-large"></i>
            <span>View Gallery</span>
        </a>

        <a href="<?php echo app_base_url('/admin/images/optimizer'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-compress"></i>
            <span>Optimize Images</span>
        </a>

        <a href="<?php echo app_base_url('/admin/images/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Image Settings</span>
        </a>
    </div>
</div>

<!-- Image Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Image Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #4cc9f0;"></i>
                Homepage Images
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['homepage'] ?? 0); ?> images</p>
            <a href="<?php echo app_base_url('/admin/images/category/homepage'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #34d399;"></i>
                Calculator Icons
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['calculator'] ?? 0); ?> icons</p>
            <a href="<?php echo app_base_url('/admin/images/category/calculator'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #fbbf24;"></i>
                User Images
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['user'] ?? 0); ?> avatars</p>
            <a href="<?php echo app_base_url('/admin/images/category/user'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-bar" style="color: #22d3ee;"></i>
                Chart Images
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['charts'] ?? 0); ?> images</p>
            <a href="<?php echo app_base_url('/admin/images/category/charts'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>Manage</span>
            </a>
        </div>
    </div>
</div>

<!-- Upload Area -->
<div class="admin-card">
    <h2 class="admin-card-title">Upload Images</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 2rem; border-radius: 8px; text-align: center; border: 2px dashed rgba(102, 126, 234, 0.3);">
        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #4cc9f0; margin-bottom: 1rem; display: block;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">Drag & Drop Images Here</h3>
        <p style="color: #9ca3af; margin-bottom: 1.5rem;">or click to select files from your computer</p>
        <form action="<?php echo app_base_url('/admin/images/upload'); ?>" method="post" enctype="multipart/form-data" style="display: inline-block;">
            <input type="file" name="images[]" multiple style="display: none;" id="image-upload" accept="image/*">
            <label for="image-upload" style="display: inline-block; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer; text-decoration: none;">
                <i class="fas fa-folder-open"></i>
                <span>Choose Files</span>
            </label>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; margin-left: 1rem; cursor: pointer;">
                <i class="fas fa-upload"></i>
                <span>Upload</span>
            </button>
        </form>
        <p style="color: #9ca3af; margin-top: 1rem; font-size: 0.75rem;">Supported formats: JPG, PNG, GIF, WEBP. Maximum file size: 5MB.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>