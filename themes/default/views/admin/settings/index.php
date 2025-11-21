<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>System Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure application and system settings</p>
        </div>
    </div>
</div>

<!-- Settings Categories -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-cogs" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">General Settings</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;">Configuration</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Site-wide settings</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Ready</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-envelope" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Email Settings</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">Messaging</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">SMTP and notifications</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Configured</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Security Settings</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;">Protection</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Security and privacy</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-lock"></i> Protected</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-tachometer-alt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Performance</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;">Optimization</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Caching and speed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Optimized</small>
    </div>
</div>

<!-- Settings Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-cog"></i>
            <span>General</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/email'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/security'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/performance'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none;">
            <i class="fas fa-tachometer-alt"></i>
            <span>Performance</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">General Settings</h2>
    <form method="POST" action="<?php echo app_base_url('/admin/settings/update'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site Title</label>
                <input type="text" name="site_title" value="<?php echo htmlspecialchars($config['site_title'] ?? 'Bishwo Calculator'); ?>" 
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site Description</label>
                <input type="text" name="site_description" value="<?php echo htmlspecialchars($config['site_description'] ?? 'Advanced Engineering Calculator'); ?>" 
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Site URL</label>
                <input type="url" name="site_url" value="<?php echo htmlspecialchars($config['site_url'] ?? 'https://example.com'); ?>" 
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default Theme</label>
                <select name="default_theme" 
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    <option value="default" <?php echo ($config['default_theme'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default Theme</option>
                    <option value="dark" <?php echo ($config['default_theme'] ?? 'default') === 'dark' ? 'selected' : ''; ?>>Dark Theme</option>
                    <option value="light" <?php echo ($config['default_theme'] ?? 'default') === 'light' ? 'selected' : ''; ?>>Light Theme</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Default Language</label>
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
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" 
                    style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-save"></i>
                <span>Save Settings</span>
            </button>
            <a href="<?php echo app_base_url('/admin/settings/reset'); ?>" 
               style="padding: 0.75rem 2rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; text-decoration: none;">
                <i class="fas fa-undo"></i>
                <span>Reset to Default</span>
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
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Max Upload Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #34d399;"></i>
                Database Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Driver:</strong> <?php echo $db_info['driver'] ?? 'MySQL'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Version:</strong> <?php echo $db_info['version'] ?? 'Unknown'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Host:</strong> <?php echo $db_info['host'] ?? 'localhost'; ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Tables:</strong> <?php echo $db_info['tables'] ?? '0'; ?></p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cubes" style="color: #fbbf24;"></i>
                Application Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Version:</strong> <?php echo $app_info['version'] ?? '1.0.0'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Environment:</strong> <?php echo $app_info['environment'] ?? 'production'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Debug Mode:</strong> <?php echo $app_info['debug_mode'] ? 'Enabled' : 'Disabled'; ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Cache Status:</strong> <?php echo $app_info['cache_enabled'] ? 'Enabled' : 'Disabled'; ?></p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-code" style="color: #22d3ee;"></i>
                Framework Info
            </h3>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Framework:</strong> <?php echo $framework_info['name'] ?? 'Custom PHP'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Version:</strong> <?php echo $framework_info['version'] ?? '1.0'; ?></p>
            <p style="color: #9ca3af; margin-bottom: 0.5rem;"><strong>Modules:</strong> <?php echo count($framework_info['modules'] ?? []); ?></p>
            <p style="color: #9ca3af; margin: 0;"><strong>Extensions:</strong> <?php echo count($framework_info['extensions'] ?? []); ?></p>
        </div>
    </div>
</div>

<!-- Settings Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Settings Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/settings/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-database"></i>
            <span>Backup Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/restore'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-undo"></i>
            <span>Restore Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/maintenance'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-tools"></i>
            <span>Maintenance Mode</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>