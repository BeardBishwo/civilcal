<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">
                <i class="fas fa-palette" style="margin-right: 0.5rem;"></i>Theme Management
            </h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Manage and customize your application themes</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button data-bs-toggle="modal" data-bs-target="#uploadThemeModal" style="background: #4361ee; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Upload Theme</span>
            </button>
        </div>
    </div>
</div>

<!-- Statistics Dashboard -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $stats['total_themes'] ?? 0; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Themes</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['active_themes'] ?? 0; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Active</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #9ca3af; margin-bottom: 0.5rem;"><?php echo $stats['inactive_themes'] ?? 0; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Inactive</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['deleted_themes'] ?? 0; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Deleted</div>
    </div>
</div>

<!-- Error Display -->
<?php if (!empty($error)): ?>
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; padding: 1rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
        <div style="color: #f9fafb;"><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>

<!-- Active Theme Banner -->
<?php if (!empty($activeTheme)): ?>
    <div style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem; color: #34d399;"></i>Currently Active Theme
                </h3>
                <p style="color: #f9fafb; margin: 0 0 0.5rem 0;">
                    <strong><?php echo htmlspecialchars($activeTheme['display_name'] ?? $activeTheme['name']); ?></strong>
                    <?php if (!empty($activeTheme['is_premium'])): ?>
                        <span style="background: #fbbf24; color: #000; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; margin-left: 0.5rem;">Premium</span>
                    <?php endif; ?>
                    <span style="margin-left: 0.5rem;">v<?php echo htmlspecialchars($activeTheme['version']); ?></span>
                </p>
                <small style="color: #9ca3af;">by <?php echo htmlspecialchars($activeTheme['author']); ?></small>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button id="customizeThemeBtn" data-theme-id="<?php echo (int)($activeTheme['id'] ?? 0); ?>" style="background: transparent; color: #4cc9f0; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-sliders-h"></i>
                    <span>Customize</span>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Themes Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;" id="themesGrid">
    <?php if (empty($themes)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <i class="fas fa-palette" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
            <h3 style="color: #9ca3af; margin: 0 0 1rem 0;">No themes found</h3>
            <p style="color: #9ca3af; margin: 0 0 1.5rem 0;">Start by uploading a theme</p>
            <button data-bs-toggle="modal" data-bs-target="#uploadThemeModal" style="background: #4361ee; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Upload Theme</span>
            </button>
        </div>
    <?php else: ?>
        <?php foreach ($themes as $theme): ?>
            <div class="theme-item" 
                 style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; overflow: hidden; <?php echo $theme['status'] === 'active' ? 'border-color: #34d399;' : ''; ?> <?php echo $theme['status'] === 'deleted' ? 'opacity: 0.75;' : ''; ?>">
                <!-- Theme Preview -->
                <div style="position: relative;">
                    <div style="height: 180px; background: #0a0e27; display: flex; align-items: center; justify-content: center;">
                        <?php $preview = $theme['screenshot_path'] ?? ($theme['preview_image'] ?? null); ?>
                        <?php if ($preview): ?>
                            <img src="<?php echo htmlspecialchars($preview); ?>" alt="Theme Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="text-align: center; color: #9ca3af;">
                                <i class="fas fa-palette" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                <p style="margin: 0; font-size: 0.875rem;">No preview available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Status Badge -->
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem;">
                        <?php if ($theme['status'] === 'active'): ?>
                            <span style="background: #34d399; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">Active</span>
                        <?php elseif ($theme['status'] === 'deleted'): ?>
                            <span style="background: #ef4444; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">Deleted</span>
                        <?php else: ?>
                            <span style="background: #9ca3af; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <!-- Premium Badge -->
                    <?php if (!empty($theme['is_premium'])): ?>
                        <div style="position: absolute; top: 0.5rem; left: 0.5rem;">
                            <span style="background: #fbbf24; color: #000; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-star"></i>
                                <span>Premium</span>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="padding: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">
                        <?php echo htmlspecialchars($theme['display_name'] ?? $theme['name']); ?>
                    </h3>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0 0 1rem 0;">
                        <?php echo htmlspecialchars($theme['description'] ?? 'No description available'); ?>
                    </p>
                    
                    <!-- Theme Meta -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem; color: #9ca3af;">
                            <div>
                                <strong>Version:</strong><br>
                                <?php echo htmlspecialchars($theme['version']); ?>
                            </div>
                            <div>
                                <strong>Author:</strong><br>
                                <?php echo htmlspecialchars($theme['author']); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background: rgba(10, 14, 39, 0.5); padding: 1.5rem; border-top: 1px solid rgba(102, 126, 234, 0.1);">
                    <!-- Action Buttons -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                        <?php if ($theme['status'] !== 'active'): ?>
                            <button class="activate-theme" data-theme-id="<?php echo $theme['id']; ?>" style="background: #34d399; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.875rem;">
                                <i class="fas fa-play-circle"></i>
                                <span>Activate</span>
                            </button>
                        <?php else: ?>
                            <button style="background: transparent; color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); padding: 0.5rem; border-radius: 6px; display: flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.875rem;" disabled>
                                <i class="fas fa-check-circle"></i>
                                <span>Active</span>
                            </button>
                        <?php endif; ?>

                        <?php if ($theme['status'] !== 'deleted'): ?>
                            <button class="delete-theme" data-theme-id="<?php echo $theme['id']; ?>" style="background: #ef4444; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.875rem;">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        <?php else: ?>
                            <button class="restore-theme" data-theme-id="<?php echo $theme['id']; ?>" style="background: #60a5fa; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.875rem;">
                                <i class="fas fa-arrow-rotate-left"></i>
                                <span>Restore</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Theme Modal -->
<div class="modal" id="uploadThemeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: #0a0e27; border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; width: 90%; max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #f9fafb; margin: 0;">
                <i class="fas fa-cloud-upload-alt" style="margin-right: 0.5rem;"></i>Upload New Theme
            </h3>
            <button data-bs-dismiss="modal" style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 1.5rem;">×</button>
        </div>
        <form id="uploadThemeForm" enctype="multipart/form-data" style="padding: 1.5rem;">
            <div id="uploadArea" style="border: 2px dashed rgba(102, 126, 234, 0.2); border-radius: 8px; padding: 2rem; text-align: center; margin-bottom: 1.5rem; cursor: pointer;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <p style="color: #9ca3af; margin: 0 0 1rem 0;">Drag & drop your theme ZIP file here, or click to browse</p>
                <input type="file" id="themeZipFile" name="theme_zip" accept=".zip" required style="display: none;">
                <button type="button" id="browseFileBtn" style="background: transparent; color: #4cc9f0; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">Browse Files</button>
                <div style="margin-top: 1rem;">
                    <small style="color: #9ca3af;">Maximum file size: 10MB</small>
                </div>
            </div>
            
            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 8px; padding: 1rem;">
                <h4 style="font-size: 1rem; font-weight: 600; color: #f9fafb; margin: 0 0 1rem 0;">
                    <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>Theme Requirements
                </h4>
                <ul style="color: #9ca3af; margin: 0; padding-left: 1.5rem; font-size: 0.875rem;">
                    <li style="margin-bottom: 0.5rem;">ZIP file must contain a valid theme configuration</li>
                    <li>Recommended theme size: Under 10MB</li>
                </ul>
            </div>
        </form>
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1.5rem; border-top: 1px solid rgba(102, 126, 234, 0.1);">
            <button data-bs-dismiss="modal" style="background: transparent; color: #9ca3af; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">Cancel</button>
            <button id="uploadSubmitBtn" disabled style="background: #4361ee; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-upload"></i>
                <span>Upload Theme</span>
            </button>
        </div>
    </div>
</div>

<script>
// Simple JavaScript for theme actions
document.addEventListener('DOMContentLoaded', function() {
    // Activate theme
    document.querySelectorAll('.activate-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            if (confirm('Are you sure you want to activate this theme?')) {
                // In a real implementation, this would make an AJAX call
                alert('Theme activation would happen here. Theme ID: ' + themeId);
            }
        });
    });
    
    // Delete theme
    document.querySelectorAll('.delete-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            if (confirm('Are you sure you want to delete this theme?')) {
                // In a real implementation, this would make an AJAX call
                alert('Theme deletion would happen here. Theme ID: ' + themeId);
            }
        });
    });
    
    // Restore theme
    document.querySelectorAll('.restore-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            if (confirm('Are you sure you want to restore this theme?')) {
                // In a real implementation, this would make an AJAX call
                alert('Theme restoration would happen here. Theme ID: ' + themeId);
            }
        });
    });
    
    // Upload functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('themeZipFile');
    const browseBtn = document.getElementById('browseFileBtn');
    const submitBtn = document.getElementById('uploadSubmitBtn');
    
    browseBtn.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            submitBtn.disabled = false;
        }
    });
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#4361ee';
        this.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
    });
    
    uploadArea.addEventListener('dragleave', function() {
        this.style.borderColor = 'rgba(102, 126, 234, 0.2)';
        this.style.backgroundColor = 'transparent';
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(102, 126, 234, 0.2)';
        this.style.backgroundColor = 'transparent';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            submitBtn.disabled = false;
        }
    });
    
    // Modal functionality
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.dataset.bsTarget;
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        });
    });
    
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>