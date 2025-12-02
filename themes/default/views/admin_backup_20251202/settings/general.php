<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>System Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure global system settings and parameters</p>
        </div>
    </div>
</div>

<!-- Settings Navigation Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-cog"></i>
            <span>General</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/security'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/email'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/performance'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-bolt"></i>
            <span>Performance</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">General Settings</h2>
    <form method="POST" action="<?php echo app_base_url('/admin/settings/update'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div class="admin-grid">
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-home" style="color: #4cc9f0;"></i>
                    Site Information
                </h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site Title</label>
                    <input type="text" name="site_title" value="<?php echo htmlspecialchars($config['site_title'] ?? 'Bishwo Calculator'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site Description</label>
                    <textarea name="site_description" rows="3" 
                              style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;"><?php echo htmlspecialchars($config['site_description'] ?? 'Advanced Engineering Calculator'); ?></textarea>
                </div>
                
                <div>
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site URL</label>
                    <input type="url" name="site_url" value="<?php echo htmlspecialchars($config['site_url'] ?? 'https://example.com'); ?>" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user" style="color: #34d399;"></i>
                    User Settings
                </h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default User Role</label>
                    <select name="default_user_role" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="user" <?php echo ($config['default_user_role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>Regular User</option>
                        <option value="engineer" <?php echo ($config['default_user_role'] ?? 'user') === 'engineer' ? 'selected' : ''; ?>>Engineer</option>
                        <option value="admin" <?php echo ($config['default_user_role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Registration Status</label>
                    <select name="registration_enabled" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="1" <?php echo ($config['registration_enabled'] ?? true) ? 'selected' : ''; ?>>Enabled</option>
                        <option value="0" <?php echo ($config['registration_enabled'] ?? true) ? '' : 'selected'; ?>>Disabled</option>
                    </select>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="color: #f9fafb;">Allow File Uploads</label>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="allow_file_uploads" <?php echo ($config['allow_file_uploads'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($config['allow_file_uploads'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Allow users to upload files</p>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-paint-brush" style="color: #fbbf24;"></i>
                    Appearance
                </h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default Theme</label>
                    <select name="default_theme" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="default" <?php echo ($config['default_theme'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default Theme</option>
                        <option value="dark" <?php echo ($config['default_theme'] ?? 'default') === 'dark' ? 'selected' : ''; ?>>Dark Theme</option>
                        <option value="light" <?php echo ($config['default_theme'] ?? 'default') === 'light' ? 'selected' : ''; ?>>Light Theme</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Language</label>
                    <select name="language" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="en" <?php echo ($config['language'] ?? 'en') === 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="es" <?php echo ($config['language'] ?? 'en') === 'es' ? 'selected' : ''; ?>>Spanish</option>
                        <option value="fr" <?php echo ($config['language'] ?? 'en') === 'fr' ? 'selected' : ''; ?>>French</option>
                        <option value="de" <?php echo ($config['language'] ?? 'en') === 'de' ? 'selected' : ''; ?>>German</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Timezone</label>
                    <select name="timezone" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="UTC" <?php echo ($config['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?php echo ($config['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                        <option value="Europe/London" <?php echo ($config['timezone'] ?? 'UTC') === 'Europe/London' ? 'selected' : ''; ?>>London Time</option>
                        <option value="Asia/Kolkata" <?php echo ($config['timezone'] ?? 'UTC') === 'Asia/Kolkata' ? 'selected' : ''; ?>>Indian Standard Time</option>
                    </select>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-cogs" style="color: #22d3ee;"></i>
                    System Settings
                </h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Maintenance Mode</label>
                    <select name="maintenance_mode" 
                            style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="0" <?php echo ($config['maintenance_mode'] ?? false) ? '' : 'selected'; ?>>Off</option>
                        <option value="1" <?php echo ($config['maintenance_mode'] ?? false) ? 'selected' : ''; ?>>On</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label style="color: #f9fafb;">Enable SSL</label>
                        <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Force HTTPS connections</p>
                    </div>
                    <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                        <input type="checkbox" name="enable_ssl" <?php echo ($config['enable_ssl'] ?? false) ? 'checked' : ''; ?> 
                               style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($config['enable_ssl'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                        <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                    </label>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="color: #f9fafb;">Debug Mode</label>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="debug_mode" <?php echo ($config['debug_mode'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($config['debug_mode'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Show detailed error messages</p>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" 
                    style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
            <a href="<?php echo app_base_url('/admin/settings/reset'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; text-decoration: none;">
                <i class="fas fa-undo"></i>
                <span>Reset Defaults</span>
            </a>
        </div>
    </form>
</div>

<!-- System Information -->
<div class="admin-card">
    <h2 class="admin-card-title">System Information</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #4cc9f0;"></i>
                Server Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Software:</strong> <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Max Execution:</strong> <?php echo ini_get('max_execution_time'); ?> seconds</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #34d399;"></i>
                Database Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Driver:</strong> <?php echo $db_info['driver'] ?? 'MySQL'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Version:</strong> <?php echo $db_info['version'] ?? 'Unknown'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Host:</strong> <?php echo $db_info['host'] ?? 'localhost'; ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Size:</strong> <?php echo $db_info['size'] ?? 'Unknown'; ?></p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-code" style="color: #fbbf24;"></i>
                Application Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Name:</strong> <?php echo $app_info['name'] ?? 'Bishwo Calculator'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Version:</strong> <?php echo $app_info['version'] ?? '1.0.0'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Environment:</strong> <?php echo $app_info['environment'] ?? 'production'; ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Build:</strong> <?php echo $app_info['build'] ?? 'dev'; ?></p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-microchip" style="color: #22d3ee;"></i>
                System Stats
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>OS:</strong> <?php echo $system_stats['os'] ?? PHP_OS; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Uptime:</strong> <?php echo $system_stats['uptime'] ?? 'N/A'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Load Avg:</strong> <?php echo $system_stats['load_avg'] ?? 'N/A'; ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Processes:</strong> <?php echo $system_stats['processes'] ?? 'N/A'; ?></p>
        </div>
    </div>
</div>

<!-- Cache Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Cache Management</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Cache Status
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo $cache_status['enabled'] ? 'Cache is enabled and active' : 'Cache is disabled'; ?></p>
            <a href="<?php echo app_base_url('/admin/settings/cache'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-cog"></i>
                <span>Cache Settings</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sync-alt" style="color: #34d399;"></i>
                Clear Cache
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Clear all cached data and reset cache system</p>
            <a href="<?php echo app_base_url('/admin/settings/cache/clear-all'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-trash"></i>
                <span>Clear Cache</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-image" style="color: #fbbf24;"></i>
                Image Cache
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Manage image cache and thumbnails</p>
            <a href="<?php echo app_base_url('/admin/settings/cache/image'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-images"></i>
                <span>Manage Images</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-browser" style="color: #22d3ee;"></i>
                Browser Cache
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Control browser caching for users</p>
            <a href="<?php echo app_base_url('/admin/settings/cache/browser'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>
</div>

<!-- System Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">System Actions</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        <a href="<?php echo app_base_url('/admin/settings/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-database"></i>
            <span>Backup System</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/restore'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-undo"></i>
            <span>Restore System</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/import-data'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/export-data'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/cleanup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-broom"></i>
            <span>System Cleanup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/diagnostics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-stethoscope"></i>
            <span>System Diagnostics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/restart'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); border-radius: 6px; text-decoration: none; color: #ec4899;">
            <i class="fas fa-power-off"></i>
            <span>Restart System</span>
        </a>
    </div>
</div>

<!-- Environment Variables -->
<div class="admin-card">
    <h2 class="admin-card-title">Environment Configuration</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <h3 style="color: #f9fafb; margin-bottom: 1rem;">Application Environment</h3>
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Environment:</span>
                        <span style="color: <?php echo $env_config['environment'] === 'production' ? '#34d399' : ($env_config['environment'] === 'development' ? '#fbbf24' : '#22d3ee'); ?>;">
                            <?php echo ucfirst($env_config['environment'] ?? 'production'); ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Debug Mode:</span>
                        <span style="color: <?php echo $env_config['debug'] ? '#f87171' : '#34d399'; ?>;">
                            <?php echo $env_config['debug'] ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #9ca3af;">Timezone:</span>
                        <span style="color: #f9fafb;"><?php echo $env_config['timezone'] ?? 'UTC'; ?></span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 style="color: #f9fafb; margin-bottom: 1rem;">Database Configuration</h3>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Driver:</span>
                        <span style="color: #f9fafb;"><?php echo $env_config['db_driver'] ?? 'mysql'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Host:</span>
                        <span style="color: #f9fafb;"><?php echo $env_config['db_host'] ?? 'localhost'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #9ca3af;">Connection:</span>
                        <span style="color: <?php echo $env_config['db_connected'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $env_config['db_connected'] ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 style="color: #f9fafb; margin-bottom: 1rem;">Security Settings</h3>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">HTTPS:</span>
                        <span style="color: <?php echo $env_config['https'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $env_config['https'] ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">CSRF Token:</span>
                        <span style="color: <?php echo $env_config['csrf'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $env_config['csrf'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #9ca3af;">Session Timeout:</span>
                        <span style="color: #f9fafb;"><?php echo $env_config['session_timeout'] ?? '30'; ?> mins</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>