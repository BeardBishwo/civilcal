<?php

namespace App\Core;

use Exception;
use App\Core\Database;

/**
 * WordPress-like module manager for admin functionality
 */
class AdminModuleManager
{
    private static $instance = null;
    private $modules = [];
    private $activeModules = [];
    private $db;
    private $menuItems = [];
    private $widgets = [];
    
    private function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadModules();
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load all available modules
     */
    private function loadModules()
    {
        $moduleDirectories = [
            __DIR__ . '/../Modules/Admin/',
            __DIR__ . '/../Modules/Analytics/',
            __DIR__ . '/../Modules/Marketing/',
            __DIR__ . '/../Modules/Content/'
        ];
        
        foreach ($moduleDirectories as $dir) {
            if (is_dir($dir)) {
                $this->scanModuleDirectory($dir);
            }
        }
        
        $this->activateEnabledModules();
    }
    
    /**
     * Scan directory for module files
     */
    private function scanModuleDirectory($directory)
    {
        $files = glob($directory . '*Module.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $fullClassName = 'App\\Modules\\' . str_replace('/', '\\', substr($directory, strlen(__DIR__ . '/../'))) . $className;
            
            if (class_exists($fullClassName)) {
                try {
                    $module = new $fullClassName();
                    if ($module instanceof AdminModule) {
                        $this->modules[$className] = $module;
                    }
                } catch (Exception $e) {
                    error_log("Failed to load module {$className}: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Activate enabled modules from database
     */
    private function activateEnabledModules()
    {
        try {
            $this->ensureModulesTable();
            
            $stmt = $this->db->getPdo()->query("SELECT * FROM admin_modules WHERE is_active = 1");
            $activeModuleNames = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
            
            foreach ($this->modules as $name => $module) {
                if (in_array($name, $activeModuleNames)) {
                    $this->activeModules[$name] = $module;
                    $module->activate();
                    
                    // Register menu items and widgets
                    $menuItem = $module->registerMenu();
                    if ($menuItem) {
                        $this->menuItems[] = $menuItem;
                    }
                    
                    $widget = $module->renderWidget();
                    if ($widget) {
                        $this->widgets[$name] = $widget;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Module activation error: " . $e->getMessage());
        }
    }
    
    /**
     * Ensure admin_modules table exists
     */
    private function ensureModulesTable()
    {
        $createTable = "
            CREATE TABLE IF NOT EXISTS admin_modules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                version VARCHAR(50),
                is_active TINYINT(1) DEFAULT 0,
                settings JSON,
                installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_active (is_active),
                INDEX idx_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $this->db->getPdo()->exec($createTable);
    }
    
    /**
     * Get all available modules
     */
    public function getAllModules()
    {
        $moduleList = [];
        foreach ($this->modules as $name => $module) {
            $moduleList[$name] = $module->getInfo();
        }
        return $moduleList;
    }
    
    /**
     * Get active modules
     */
    public function getActiveModules()
    {
        return $this->activeModules;
    }
    
    /**
     * Get admin menu items
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }
    
    /**
     * Get dashboard widgets
     */
    public function getWidgets()
    {
        return $this->widgets;
    }
    
    /**
     * Activate a module
     */
    public function activateModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception("Module {$moduleName} not found");
        }
        
        try {
            $module = $this->modules[$moduleName];
            
            // Install if not already installed
            $this->installModule($moduleName);
            
            // Activate in database
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO admin_modules (name, version, is_active) 
                VALUES (?, ?, 1) 
                ON DUPLICATE KEY UPDATE is_active = 1
            ");
            
            $info = $module->getInfo();
            $stmt->execute([$moduleName, $info['version']]);
            
            // Activate module
            $module->activate();
            $this->activeModules[$moduleName] = $module;
            
            return true;
        } catch (Exception $e) {
            error_log("Module activation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deactivate a module
     */
    public function deactivateModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception("Module {$moduleName} not found");
        }
        
        try {
            // Deactivate in database
            $stmt = $this->db->getPdo()->prepare("UPDATE admin_modules SET is_active = 0 WHERE name = ?");
            $stmt->execute([$moduleName]);
            
            // Deactivate module
            if (isset($this->activeModules[$moduleName])) {
                $this->activeModules[$moduleName]->deactivate();
                unset($this->activeModules[$moduleName]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Module deactivation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Install a module
     */
    public function installModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception("Module {$moduleName} not found");
        }
        
        $module = $this->modules[$moduleName];
        return $module->install();
    }
    
    /**
     * Uninstall a module
     */
    public function uninstallModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception("Module {$moduleName} not found");
        }
        
        try {
            // Deactivate first
            $this->deactivateModule($moduleName);
            
            // Uninstall
            $module = $this->modules[$moduleName];
            $module->uninstall();
            
            // Remove from database
            $stmt = $this->db->getPdo()->prepare("DELETE FROM admin_modules WHERE name = ?");
            $stmt->execute([$moduleName]);
            
            return true;
        } catch (Exception $e) {
            error_log("Module uninstall error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update module settings
     */
    public function updateModuleSettings($moduleName, $settings)
    {
        if (!isset($this->activeModules[$moduleName])) {
            throw new Exception("Module {$moduleName} not active");
        }
        
        $module = $this->activeModules[$moduleName];
        $result = $module->updateSettings($settings);
        
        if ($result) {
            // Update in database
            $stmt = $this->db->getPdo()->prepare("
                UPDATE admin_modules 
                SET settings = ?, updated_at = NOW() 
                WHERE name = ?
            ");
            $stmt->execute([json_encode($settings), $moduleName]);
        }
        
        return $result;
    }
    
    /**
     * Get module settings
     */
    public function getModuleSettings($moduleName)
    {
        try {
            $stmt = $this->db->getPdo()->prepare("SELECT settings FROM admin_modules WHERE name = ?");
            $stmt->execute([$moduleName]);
            $result = $stmt->fetch();
            
            return $result ? json_decode($result['settings'], true) : [];
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
