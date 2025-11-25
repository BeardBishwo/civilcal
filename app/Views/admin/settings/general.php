
<?php
/**
 * General Settings Page - Modern UI/UX Design
 * Enhanced with beautiful card layout, visual feedback, and modern styling
 */
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-cog text-primary me-2"></i>
            General Settings
        </h1>
        <p class="page-description">Configure your website's basic settings and appearance</p>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <!-- Site Configuration Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-globe text-primary me-2"></i>
                        Site Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <form id="generalSettingsForm" action="<?php echo app_base_url('/admin/settings/save'); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        
                        <!-- Site Identity Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-fingerprint me-2"></i>
                                Site Identity
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label fw-semibold">Site Name</label>
                                        <input type="text" class="form-control form-control-lg" 
                                               id="site_name" 
                                               name="site_name" 
                                               value="<?= htmlspecialchars($settings['site_name'] ?? 'Bishwo Calculator') ?>" 
                                               placeholder="Enter your site name">
                                        <div class="form-text text-muted">The name of your website as displayed in the browser title.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_description" class="form-label fw-semibold">Site Description</label>
                                        <textarea class="form-control" 
                                                  id="site_description" 
                                                  name="site_description" 
                                                  rows="3" 
                                                  placeholder="Describe your website"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logo Upload -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_logo" class="form-label fw-semibold">Site Logo</label>
                                        <?php if (!empty($settings['site_logo'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= htmlspecialchars(app_base_url($settings['site_logo'])) ?>" 
                                                     alt="Current Logo" 
                                                     style="max-width: 200px; max-height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #dee2e6;">
                                                <div class="mt-2">
                                                    <small class="text-muted">Current logo: <?= htmlspecialchars(basename($settings['site_logo'])) ?></small>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="site_logo" 
                                                   name="site_logo" 
                                                   accept="image/*">
                                            <div class="form-text text-muted">Upload a new logo to replace the current one. Leave empty to keep current logo.</div>
                                        <?php else: ?>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="site_logo" 
                                                   name="site_logo" 
                                                   accept="image/*">
                                            <div class="form-text text-muted">Upload your site logo. Recommended size: 512x512px.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="favicon" class="form-label fw-semibold">Favicon</label>
                                        <?php if (!empty($settings['favicon'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= htmlspecialchars(app_base_url($settings['favicon'])) ?>" 
                                                     alt="Current Favicon" 
                                                     style="max-width: 32px; max-height: 32px; object-fit: contain; border-radius: 4px; border: 1px solid #dee2e6;">
                                                <div class="mt-2">
                                                    <small class="text-muted">Current favicon: <?= htmlspecialchars(basename($settings['favicon'])) ?></small>
                                                </div>
                                            </div>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="favicon" 
                                                   name="favicon" 
                                                   accept="image/x-icon,image/png,image/gif">
                                            <div class="form-text text-muted">Upload a new favicon to replace the current one. Leave empty to keep current favicon.</div>
                                        <?php else: ?>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="favicon" 
                                                   name="favicon" 
                                                   accept="image/x-icon,image/png,image/gif">
                                            <div class="form-text text-muted">Upload your site favicon. Recommended size: 32x32px.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Regional Settings Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-globe me-2"></i>
                                Regional Settings
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label fw-semibold">Timezone</label>
                                        <select class="form-select" id="timezone" name="timezone">
                                            <option value="UTC" <?= (isset($settings['timezone']) && $settings['timezone'] == 'UTC') ? 'selected' : '' ?>>UTC</option>
                                            <option value="Asia/Kathmandu" <?= (isset($settings['timezone']) && $settings['timezone'] == 'Asia/Kathmandu') ? 'selected' : '' ?>>Asia/Kathmandu</option>
                                            <option value="America/New_York" <?= (isset($settings['timezone']) && $settings['timezone'] == 'America/New_York') ? 'selected' : '' ?>>Eastern Time (US & Canada)</option>
                                            <option value="Europe/London" <?= (isset($settings['timezone']) && $settings['timezone'] == 'Europe/London') ? 'selected' : '' ?>>London</option>
                                            <option value="Asia/Tokyo" <?= (isset($settings['timezone']) && $settings['timezone'] == 'Asia/Tokyo') ? 'selected' : '' ?>>Tokyo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="default_language" class="form-label fw-semibold">Default Language</label>
                                        <select class="form-select" id="default_language" name="default_language">
                                            <option value="en" <?= (isset($settings['default_language']) && $settings['default_language'] == 'en') ? 'selected' : '' ?>>English</option>
                                            <option value="ne" <?= (isset($settings['default_language']) && $settings['default_language'] == 'ne') ? 'selected' : '' ?>>Nepali</option>
                                            <option value="hi" <?= (isset($settings['default_language']) && $settings['default_language'] == 'hi') ? 'selected' : '' ?>>Hindi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Display Settings Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-desktop me-2"></i>
                                Display Settings
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="items_per_page" class="form-label fw-semibold">Items Per Page</label>
                                        <input type="number" class="form-control" 
                                               id="items_per_page" 
                                               name="items_per_page" 
                                               value="<?= htmlspecialchars($settings['items_per_page'] ?? '20') ?>" 
                                               min="10" max="100">
                                        <div class="form-text text-muted">Number of items to display per page in lists</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_url" class="form-label fw-semibold">Site URL</label>
                                        <input type="url" class="form-control" 
                                               id="site_url" 
                                               name="site_url" 
                                               value="<?= htmlspecialchars($settings['site_url'] ?? 'http://localhost') ?>" 
                                               placeholder="https://yoursite.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Branding Settings Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-paint-brush me-2"></i>
                                Branding
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo_text" class="form-label fw-semibold">Logo Text</label>
                                        <input type="text" class="form-control" 
                                               id="logo_text" 
                                               name="logo_text" 
                                               value="<?= htmlspecialchars($settings['logo_text'] ?? 'Bishwo Calculator') ?>" 
                                               placeholder="Your site name">
                                        <div class="form-text text-muted">Custom text to display in the header alongside or instead of the logo.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="header_style" class="form-label fw-semibold">Header Style</label>
                                        <select class="form-select" id="header_style" name="header_style">
                                            <option value="logo_only" <?= (isset($settings['header_style']) && $settings['header_style'] == 'logo_only') ? 'selected' : '' ?>>Logo Only</option>
                                            <option value="text_only" <?= (isset($settings['header_style']) && $settings['header_style'] == 'text_only') ? 'selected' : '' ?>>Text Only</option>
                                            <option value="logo_text" <?= (isset($settings['header_style']) && $settings['header_style'] == 'logo_text') ? 'selected' : '' ?>>Logo + Text</option>
                                        </select>
                                        <div class="form-text text-muted">Choose how the header displays: logo only, text only, or both.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-envelope me-2"></i>
                                Contact Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_email" class="form-label fw-semibold">Admin Email</label>
                                        <input type="email" class="form-control" 
                                               id="admin_email" 
                                               name="admin_email" 
                                               value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>" 
                                               placeholder="admin@yoursite.com">
                                        <div class="form-text text-muted">Email address for administrative notifications</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4 col-xl-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Quick Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Current Time</small>
                        <strong><?= date('Y-m-d H:i:s') ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Timezone</small>
                        <strong><?= date_default_timezone_get() ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">PHP Version</small>
                        <strong><?= PHP_VERSION ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>