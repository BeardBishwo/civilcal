<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Theme Settings: <?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?></h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure specific settings for this theme</p>
        </div>
    </div>
</div>

<!-- Theme Settings Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/premium-themes'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>Themes</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/customize'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-paint-brush"></i>
            <span>Customize</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </a>
    </div>
    
    <form method="POST" action="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/update-settings'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-cog" style="color: #4cc9f0;"></i>
                        Basic Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Theme Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($theme['name'] ?? ''); ?>" required
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Theme Description</label>
                        <textarea name="description" rows="3" 
                                  style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;"><?php echo htmlspecialchars($theme['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Version</label>
                        <input type="text" name="version" value="<?php echo htmlspecialchars($theme['version'] ?? '1.0.0'); ?>" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Author</label>
                        <input type="text" name="author" value="<?php echo htmlspecialchars($theme['author'] ?? ''); ?>" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-user" style="color: #34d399;"></i>
                        User Access Controls
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Available Roles</label>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #f9fafb;">
                                <input type="checkbox" name="roles[]" value="admin" <?php echo in_array('admin', $theme['available_roles'] ?? []) ? 'checked' : ''; ?> 
                                       style="width: 1rem; height: 1rem; accent-color: #4cc9f0;">
                                <span>Administrators</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #f9fafb;">
                                <input type="checkbox" name="roles[]" value="user" <?php echo in_array('user', $theme['available_roles'] ?? []) ? 'checked' : ''; ?> 
                                       style="width: 1rem; height: 1rem; accent-color: #4cc9f0;">
                                <span>Regular Users</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #f9fafb;">
                                <input type="checkbox" name="roles[]" value="engineer" <?php echo in_array('engineer', $theme['available_roles'] ?? []) ? 'checked' : ''; ?> 
                                       style="width: 1rem; height: 1rem; accent-color: #4cc9f0;">
                                <span>Engineers</span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <div style="color: #f9fafb; font-weight: 500;">Restricted Access</div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">Only allow specific user types</div>
                        </div>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="restricted_access" <?php echo ($theme['restricted_access'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($theme['restricted_access'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <div>
                                <div style="color: #f9fafb; font-weight: 500;">Enable Licensing</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">Require license key for activation</div>
                            </div>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="requires_license" <?php echo ($theme['requires_license'] ?? false) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($theme['requires_license'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-sliders-h" style="color: #fbbf24;"></i>
                        Advanced Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Dependencies</label>
                        <textarea name="dependencies" rows="4" 
                                  style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;"><?php echo htmlspecialchars(implode("\n", $theme['dependencies'] ?? [])); ?></textarea>
                        <small style="color: #9ca3af; font-size: 0.75rem;">One dependency per line</small>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Theme Features</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            <?php $available_features = ['responsive', 'rtl_support', 'accessibility', 'dark_mode', 'custom_colors', 'custom_fonts']; ?>
                            <?php foreach ($available_features as $feature): ?>
                                <label style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; color: #f9fafb; font-size: 0.875rem;">
                                    <input type="checkbox" name="features[]" value="<?php echo $feature; ?>" <?php echo in_array($feature, $theme['features'] ?? []) ? 'checked' : ''; ?> 
                                           style="width: 0.875rem; height: 0.875rem; accent-color: #4cc9f0;">
                                    <span><?php echo ucfirst(str_replace('_', ' ', $feature)); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Required PHP Extensions</label>
                        <input type="text" name="required_extensions" value="<?php echo htmlspecialchars(implode(',', $theme['required_extensions'] ?? [])); ?>" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Minimum PHP Version</label>
                        <input type="text" name="min_php_version" value="<?php echo htmlspecialchars($theme['min_php_version'] ?? '7.4'); ?>" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                </div>
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-file-code" style="color: #22d3ee;"></i>
                        Code Settings
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Custom CSS</label>
                        <textarea name="custom_css" rows="5" 
                                  style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; font-family: monospace; font-size: 0.875rem;"><?php echo htmlspecialchars($theme['custom_css'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Custom JavaScript</label>
                        <textarea name="custom_js" rows="5" 
                                  style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; font-family: monospace; font-size: 0.875rem;"><?php echo htmlspecialchars($theme['custom_js'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">License Key</label>
                        <input type="text" name="license_key" value="<?php echo htmlspecialchars($theme['license_key'] ?? ''); ?>" 
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" 
                    style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
            
            <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/activate'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none;">
                <i class="fas fa-power-off"></i>
                <span>Activate Theme</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/preview'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none;">
                <i class="fas fa-eye"></i>
                <span>Preview Theme</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/premium-themes'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
        </div>
    </form>
</div>

<!-- Theme Documentation -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Documentation</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-book" style="color: #4cc9f0;"></i>
                Installation Guide
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Step-by-step instructions for premium theme installation</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/documentation/installation'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; text-decoration: none;font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Read Guide</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-code" style="color: #34d399;"></i>
                Developer Docs
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">For developers customizing this theme</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/documentation/development'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Dev Guide</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-question-circle" style="color: #fbbf24;"></i>
                Support Resources
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Getting help with premium themes</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/support/resources'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Support</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-contract" style="color: #22d3ee;"></i>
                Licensing Terms
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">License and usage terms for this theme</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/license/terms'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-external-link-alt"></i>
                <span>Terms</span>
            </a>
        </div>
    </div>
</div>

<!-- Theme Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/duplicate'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-copy"></i>
            <span>Duplicate Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/delete'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash"></i>
            <span>Delete Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/update'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-sync-alt"></i>
            <span>Check Updates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-database"></i>
            <span>Backup Theme</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>