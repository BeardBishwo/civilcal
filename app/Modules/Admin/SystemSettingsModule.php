<?php

namespace App\Modules\Admin;

use App\Core\AdminModule;

/**
 * System Settings Module - Complete site configuration
 */
class SystemSettingsModule extends AdminModule
{
    protected function init()
    {
        $this->name = 'System Settings';
        $this->version = '1.0.0';
        $this->description = 'Complete system configuration and settings management';
        $this->author = 'Bishwo Calculator Team';
        $this->icon = 'fas fa-cogs';
        $this->permissions = ['super_admin'];
    }
    
    public function registerMenu()
    {
        return [
            'title' => 'Settings',
            'url' => '/admin/settings',
            'icon' => $this->icon,
            'position' => 8,
            'submenu' => [
                [
                    'title' => 'General',
                    'url' => '/admin/settings/general',
                    'icon' => 'fas fa-cog'
                ],
                [
                    'title' => 'Email',
                    'url' => '/admin/settings/email',
                    'icon' => 'fas fa-envelope'
                ],
                [
                    'title' => 'Security',
                    'url' => '/admin/settings/security',
                    'icon' => 'fas fa-shield-alt'
                ],
                [
                    'title' => 'Backup',
                    'url' => '/admin/settings/backup',
                    'icon' => 'fas fa-database'
                ],
                [
                    'title' => 'Performance',
                    'url' => '/admin/settings/performance',
                    'icon' => 'fas fa-tachometer-alt'
                ]
            ]
        ];
    }
    
    public function renderWidget()
    {
        $systemInfo = $this->getSystemInfo();
        
        return [
            'title' => 'System Status',
            'content' => $this->getSystemStatusWidget($systemInfo),
            'size' => 'large'
        ];
    }
    
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_free_space' => disk_free_space('.'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion()
        ];
    }
    
    private function getDatabaseVersion()
    {
        try {
            $pdo = \App\Core\Database::getInstance()->getPdo();
            return $pdo->query('SELECT VERSION()')->fetchColumn();
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getSystemStatusWidget($systemInfo)
    {
        ob_start();
        ?>
        <div class="dashboard-widget system-status">
            <div class="widget-header">
                <h3><i class="fas fa-server"></i> System Status</h3>
                <a href="/admin/settings" class="widget-link">Manage Settings</a>
            </div>
            <div class="widget-content">
                <div class="system-info-grid">
                    <div class="info-item">
                        <div class="info-label">PHP Version</div>
                        <div class="info-value"><?php echo $systemInfo['php_version']; ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Memory Limit</div>
                        <div class="info-value"><?php echo $systemInfo['memory_limit']; ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Upload Limit</div>
                        <div class="info-value"><?php echo $systemInfo['upload_max_filesize']; ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Disk Space</div>
                        <div class="info-value"><?php echo $this->formatBytes($systemInfo['disk_free_space']); ?></div>
                    </div>
                </div>
                
                <div class="health-checks">
                    <h4>Health Checks</h4>
                    <div class="check-item">
                        <span class="check-status <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'success' : 'warning'; ?>">
                            <i class="fas <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'fa-check' : 'fa-exclamation-triangle'; ?>"></i>
                        </span>
                        <span class="check-label">PHP Version (7.4+ recommended)</span>
                    </div>
                    
                    <div class="check-item">
                        <span class="check-status <?php echo extension_loaded('pdo') ? 'success' : 'error'; ?>">
                            <i class="fas <?php echo extension_loaded('pdo') ? 'fa-check' : 'fa-times'; ?>"></i>
                        </span>
                        <span class="check-label">PDO Extension</span>
                    </div>
                    
                    <div class="check-item">
                        <span class="check-status <?php echo is_writable('./storage') ? 'success' : 'warning'; ?>">
                            <i class="fas <?php echo is_writable('./storage') ? 'fa-check' : 'fa-exclamation-triangle'; ?>"></i>
                        </span>
                        <span class="check-label">Storage Directory Writable</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    public function getSettingsSchema()
    {
        return [
            'site_name' => [
                'type' => 'text',
                'label' => 'Site Name',
                'default' => 'Bishwo Calculator'
            ],
            'site_description' => [
                'type' => 'textarea',
                'label' => 'Site Description',
                'default' => 'Professional engineering calculator platform'
            ],
            'admin_email' => [
                'type' => 'email',
                'label' => 'Administrator Email',
                'default' => 'admin@example.com'
            ],
            'maintenance_mode' => [
                'type' => 'checkbox',
                'label' => 'Maintenance Mode',
                'default' => false
            ],
            'debug_mode' => [
                'type' => 'checkbox',
                'label' => 'Debug Mode',
                'default' => false
            ],
            'cache_enabled' => [
                'type' => 'checkbox',
                'label' => 'Enable Caching',
                'default' => true
            ]
        ];
    }
}
?>
