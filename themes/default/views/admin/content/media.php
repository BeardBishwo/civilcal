<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Media Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage uploaded images, files, and media assets</p>
        </div>
    </div>
</div>

<!-- Media Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-images" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Media</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_media'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Files</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12 this month</small>
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
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Images</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['image_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Photos</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Optimized</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Documents</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['document_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Files</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-file"></i> Organized</small>
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
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-bars"></i>
            <span>Menus</span>
        </a>
        <a href="<?php echo app_base_url('/admin/content/media'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-photo-video"></i>
            <span>Media</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Media Library</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <input type="text" id="searchMedia" placeholder="Search media..." 
                   style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; width: 200px;">
        </div>
        <div>
            <label style="color: #f9fafb; margin-right: 1rem;">Filter by type:</label>
            <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                <option value="all">All Files</option>
                <option value="image">Images</option>
                <option value="video">Videos</option>
                <option value="document">Documents</option>
                <option value="audio">Audio</option>
            </select>
        </div>
    </div>
    
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
            <?php if (!empty($media_items)): ?>
                <?php foreach ($media_items as $media): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; text-align: center;">
                        <div style="height: 100px; display: flex; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.8);">
                            <?php if (strpos($media['mime_type'] ?? '', 'image') !== false): ?>
                                <img src="<?php echo $media['file_path'] ?? ''; ?>" alt="<?php echo htmlspecialchars($media['filename'] ?? 'Media Item'); ?>" 
                                     style="max-width: 100%; max-height: 100px; object-fit: contain;">
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <i class="fas <?php echo $media['icon'] ?? 'fa-file'; ?>" style="font-size: 2rem; color: #4cc9f0; margin-bottom: 0.25rem;"></i>
                                    <span style="color: #9ca3af; font-size: 0.75rem; text-align: center;"><?php echo strtoupper($media['extension'] ?? 'FILE'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 0.75rem;">
                            <h4 style="color: #f9fafb; font-size: 0.875rem; margin: 0;"><?php echo htmlspecialchars(substr($media['filename'] ?? 'Unknown', 0, 15)).(strlen($media['filename'] ?? 'Unknown') > 15 ? '...' : ''); ?></h4>
                            <p style="color: #9ca3af; font-size: 0.75rem; margin: 0.25rem 0;"><?php echo $media['size'] ?? '0 KB'; ?></p>
                            <div style="margin-top: 0.5rem; display: flex; gap: 0.25rem; justify-content: center;">
                                <a href="<?php echo app_base_url('/admin/content/media/'.($media['id'] ?? 0).'/edit'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/content/media/'.($media['id'] ?? 0).'/download'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.75rem;">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="<?php echo app_base_url('/admin/content/media/'.($media['id'] ?? 0).'/delete'); ?>" 
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.75rem;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No media files uploaded yet</p>
                    <a href="<?php echo app_base_url('/admin/content/media/upload'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-plus-circle"></i>
                        <span>Upload Media</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload Box -->
<div class="admin-card">
    <h2 class="admin-card-title">Upload Media</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 2rem; border-radius: 8px; text-align: center; border: 2px dashed rgba(102, 126, 234, 0.3);">
        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #4cc9f0; margin-bottom: 1rem; display: block;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">Drag & Drop Files Here</h3>
        <p style="color: #9ca3af; margin-bottom: 1.5rem;">or click to select files from your computer</p>
        <form action="<?php echo app_base_url('/admin/content/media/upload'); ?>" method="post" enctype="multipart/form-data" style="display: inline-block;">
            <input type="file" name="media[]" multiple style="display: none;" id="media-upload" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
            <label for="media-upload" style="display: inline-block; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer; text-decoration: none;">
                <i class="fas fa-folder-open"></i>
                <span>Choose Files</span>
            </label>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; margin-left: 1rem; cursor: pointer;">
                <i class="fas fa-upload"></i>
                <span>Upload</span>
            </button>
        </form>
        <p style="color: #9ca3af; margin-top: 1rem; font-size: 0.75rem;">Supported formats: JPG, PNG, GIF, MP4, PDF, DOC, XLS, and more. Maximum file size: 10MB.</p>
    </div>
</div>

<!-- Media Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Media Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/content/media/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-upload"></i>
            <span>Upload Media</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/media/gallery'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-th-large"></i>
            <span>Media Gallery</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/media/browser'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-folder-open"></i>
            <span>File Browser</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/media/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-import"></i>
            <span>Import Media</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/media/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-file-export"></i>
            <span>Export Media</span>
        </a>

        <a href="<?php echo app_base_url('/admin/content/media/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(15, 23, 42, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #cbd5e1;">
            <i class="fas fa-cog"></i>
            <span>Media Settings</span>
        </a>
    </div>
</div>

<!-- Media Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Media Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-images" style="color: #4cc9f0;"></i>
                Images
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['images'] ?? 0); ?> files</p>
            <a href="<?php echo app_base_url('/admin/content/media?type=image'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-video" style="color: #34d399;"></i>
                Videos
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['videos'] ?? 0); ?> files</p>
            <a href="<?php echo app_base_url('/admin/content/media?type=video'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-pdf" style="color: #fbbf24;"></i>
                Documents
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['documents'] ?? 0); ?> files</p>
            <a href="<?php echo app_base_url('/admin/content/media?type=document'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-audio" style="color: #22d3ee;"></i>
                Audio
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['audio'] ?? 0); ?> files</p>
            <a href="<?php echo app_base_url('/admin/content/media?type=audio'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>