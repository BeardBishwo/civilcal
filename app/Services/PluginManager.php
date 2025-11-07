<?php
namespace App\Services;

class PluginManager {
    private $db;
    private $pluginsDir;
    private $coreModulesDir;
    
    public function __construct() {
        $this->db = new \App\Core\Database();
        $this->pluginsDir = BASE_PATH . '/plugins/calculator-plugins/';
        $this->coreModulesDir = BASE_PATH . '/modules/';
    }
    
    /**
     * Scan and register all plugins
     */
    public function scanPlugins() {
        $plugins = [];
        
        // Scan plugin directories
        if (is_dir($this->pluginsDir)) {
            $pluginDirs = array_filter(glob($this->pluginsDir . '*'), 'is_dir');
            
            foreach ($pluginDirs as $pluginDir) {
                $pluginConfig = $this->loadPluginConfig($pluginDir);
                if ($pluginConfig) {
                    $plugins[] = $pluginConfig;
                }
            }
        }
        
        return $plugins;
    }
    
    /**
     * Load plugin configuration
     */
    private function loadPluginConfig($pluginDir) {
        $configFile = $pluginDir . '/plugin.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $config['plugin_path'] = $pluginDir;
            $config['slug'] = basename($pluginDir);
            $config['is_core'] = false;
            
            // Check if plugin is active in database
            $stmt = $this->db->prepare("SELECT is_active FROM plugins WHERE slug = ?");
            $stmt->execute([$config['slug']]);
            $dbPlugin = $stmt->fetch(\PDO::FETCH_ASSOC);
            $config['is_active'] = $dbPlugin ? (bool)$dbPlugin['is_active'] : false;
            
            return $config;
        }
        
        return null;
    }
    
    /**
     * Install a plugin (uploaded via admin)
     */
    public function installPlugin($zipFile) {
        if (!file_exists($zipFile)) {
            return false;
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === TRUE) {
            $pluginName = pathinfo($zipFile, PATHINFO_FILENAME);
            $extractPath = $this->pluginsDir . $pluginName;
            
            // Create directory
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
            
            // Extract files
            $zip->extractTo($extractPath);
            $zip->close();
            
            // Register in database
            return $this->registerPlugin($extractPath);
        }
        
        return false;
    }
    
    /**
     * Register plugin in database
     */
    private function registerPlugin($pluginDir) {
        $pluginConfig = $this->loadPluginConfig($pluginDir);
        
        if (!$pluginConfig) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO plugins (name, slug, type, description, version, author, author_url, plugin_path, main_file, is_active, is_core, settings, requirements)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name), description = VALUES(description), version = VALUES(version), updated_at = CURRENT_TIMESTAMP
            ");
            
            return $stmt->execute([
                $pluginConfig['name'],
                $pluginConfig['slug'],
                $pluginConfig['type'],
                $pluginConfig['description'],
                $pluginConfig['version'],
                $pluginConfig['author'] ?? '',
                $pluginConfig['author_url'] ?? '',
                $pluginConfig['plugin_path'],
                $pluginConfig['main_file'] ?? '',
                0, // Not active by default
                0, // Not core plugin
                json_encode($pluginConfig['settings'] ?? []),
                json_encode($pluginConfig['requirements'] ?? [])
            ]);
        } catch (\Exception $e) {
            error_log("Plugin registration error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activate a plugin
     */
    public function activatePlugin($pluginSlug) {
        $plugin = $this->getPlugin($pluginSlug);
        
        if ($plugin && $this->checkRequirements($plugin)) {
            try {
                // Update database
                $stmt = $this->db->prepare("UPDATE plugins SET is_active = 1 WHERE slug = ?");
                $stmt->execute([$pluginSlug]);
                
                // Run activation hooks
                $this->runActivationHook($plugin);
                
                return true;
            } catch (\Exception $e) {
                error_log("Plugin activation error: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Deactivate a plugin
     */
    public function deactivatePlugin($pluginSlug) {
        try {
            $stmt = $this->db->prepare("UPDATE plugins SET is_active = 0 WHERE slug = ?");
            $stmt->execute([$pluginSlug]);
            
            // Run deactivation hooks
            $plugin = $this->getPlugin($pluginSlug);
            if ($plugin) {
                $this->runDeactivationHook($plugin);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Plugin deactivation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a plugin
     */
    public function deletePlugin($pluginSlug) {
        try {
            // First deactivate if active
            $this->deactivatePlugin($pluginSlug);
            
            // Remove from database
            $stmt = $this->db->prepare("DELETE FROM plugins WHERE slug = ?");
            $stmt->execute([$pluginSlug]);
            
            // Remove files
            $plugin = $this->getPlugin($pluginSlug);
            if ($plugin && !$plugin['is_core'] && isset($plugin['plugin_path'])) {
                $this->removeDirectory($plugin['plugin_path']);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Plugin deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all active calculators (including plugins)
     */
    public function getActiveCalculators() {
        $calculators = [];
        
        // 1. Get core calculators (your existing modules)
        $calculators = array_merge($calculators, $this->getCoreCalculators());
        
        // 2. Get plugin calculators
        $calculators = array_merge($calculators, $this->getPluginCalculators());
        
        return $calculators;
    }
    
    /**
     * Get your existing modules as core calculators
     */
    private function getCoreCalculators() {
        $coreCalculators = [];
        $disciplines = ['civil', 'electrical', 'plumbing', 'hvac', 'fire', 'structural', 'estimation', 'mep', 'project-management', 'site'];
        
        foreach ($disciplines as $discipline) {
            $disciplinePath = $this->coreModulesDir . $discipline;
            if (is_dir($disciplinePath)) {
                $categories = array_filter(glob($disciplinePath . '/*'), 'is_dir');
                
                foreach ($categories as $category) {
                    $calculators = array_filter(glob($category . '/*.php'), 'is_file');
                    
                    foreach ($calculators as $calculator) {
                        $coreCalculators[] = [
                            'type' => 'core',
                            'discipline' => $discipline,
                            'category' => basename($category),
                            'calculator' => pathinfo($calculator, PATHINFO_FILENAME),
                            'file_path' => $calculator,
                            'name' => $this->getCalculatorName($calculator)
                        ];
                    }
                }
            }
        }
        
        return $coreCalculators;
    }
    
    /**
     * Get active plugin calculators
     */
    private function getPluginCalculators() {
        $pluginCalculators = [];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM plugins WHERE type = 'calculator' AND is_active = 1");
            $stmt->execute();
            $activePlugins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($activePlugins as $plugin) {
                $pluginConfig = $this->getPlugin($plugin['slug']);
                if ($pluginConfig && isset($pluginConfig['calculators'])) {
                    foreach ($pluginConfig['calculators'] as $calcSlug => $calcConfig) {
                        $pluginCalculators[] = [
                            'type' => 'plugin',
                            'discipline' => $calcConfig['category'],
                            'category' => $calcConfig['category'],
                            'calculator' => $calcSlug,
                            'file_path' => $plugin['plugin_path'] . '/' . $calcConfig['file'],
                            'name' => $calcConfig['name'],
                            'plugin_slug' => $plugin['slug'],
                            'plugin_name' => $plugin['name']
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Error getting plugin calculators: " . $e->getMessage());
        }
        
        return $pluginCalculators;
    }
    
    /**
     * Get plugin by slug
     */
    public function getPlugin($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM plugins WHERE slug = ?");
            $stmt->execute([$slug]);
            $plugin = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($plugin) {
                // Load plugin configuration
                if ($plugin['plugin_path']) {
                    $configFile = $plugin['plugin_path'] . '/plugin.json';
                    if (file_exists($configFile)) {
                        $pluginConfig = json_decode(file_get_contents($configFile), true);
                        $plugin = array_merge($plugin, $pluginConfig);
                    }
                }
            }
            
            return $plugin;
        } catch (\Exception $e) {
            error_log("Error getting plugin: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check plugin requirements
     */
    private function checkRequirements($plugin) {
        $requirements = $plugin['requirements'] ?? [];
        
        // Check PHP version
        if (isset($requirements['php_version'])) {
            if (version_compare(PHP_VERSION, $requirements['php_version'], '<')) {
                return false;
            }
        }
        
        // Check required plugins
        if (isset($requirements['required_plugins'])) {
            foreach ($requirements['required_plugins'] as $requiredPlugin) {
                $stmt = $this->db->prepare("SELECT is_active FROM plugins WHERE slug = ?");
                $stmt->execute([$requiredPlugin]);
                $required = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (!$required || !$required['is_active']) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Run activation hook
     */
    private function runActivationHook($plugin) {
        $hookFile = $plugin['plugin_path'] . '/activate.php';
        if (file_exists($hookFile)) {
            include $hookFile;
        }
    }
    
    /**
     * Run deactivation hook
     */
    private function runDeactivationHook($plugin) {
        $hookFile = $plugin['plugin_path'] . '/deactivate.php';
        if (file_exists($hookFile)) {
            include $hookFile;
        }
    }
    
    /**
     * Extract calculator name from file
     */
    private function getCalculatorName($filePath) {
        $content = file_get_contents($filePath);
        if (preg_match('/<h[1-6][^>]*>([^<]+)<\/h[1-6]>/i', $content, $matches)) {
            return trim($matches[1]);
        }
        
        // Fallback to filename
        return ucwords(str_replace(['-', '_'], ' ', pathinfo($filePath, PATHINFO_FILENAME)));
    }
    
    /**
     * Remove directory and its contents
     */
    private function removeDirectory($dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
            rmdir($dir);
        }
    }
}
?>
