<?php

namespace App\Core;

use Exception;

/**
 * Base class for all admin modules
 * Provides WordPress-like modularity for admin functionality
 */
abstract class AdminModule
{
    protected $name;
    protected $version;
    protected $description;
    protected $author;
    protected $icon;
    protected $permissions;
    protected $dependencies;
    protected $isActive;
    protected $settings;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->settings = [];
        $this->permissions = ['admin'];
        $this->dependencies = [];
        $this->init();
    }
    
    /**
     * Initialize the module
     */
    abstract protected function init();
    
    /**
     * Get module information
     */
    public function getInfo()
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
            'author' => $this->author,
            'icon' => $this->icon,
            'is_active' => $this->isActive,
            'permissions' => $this->permissions,
            'dependencies' => $this->dependencies
        ];
    }
    
    /**
     * Register admin menu item
     */
    abstract public function registerMenu();
    
    /**
     * Render module dashboard widget
     */
    public function renderWidget()
    {
        return null; // Optional widget for dashboard
    }
    
    /**
     * Get module settings schema
     */
    public function getSettingsSchema()
    {
        return []; // Override to define settings
    }
    
    /**
     * Update module settings
     */
    public function updateSettings($settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        return $this->saveSettings();
    }
    
    /**
     * Save settings to database
     */
    protected function saveSettings()
    {
        try {
            // Implementation to save to database
            return true;
        } catch (Exception $e) {
            error_log("Module settings save error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has permission to access module
     */
    public function hasPermission($userRole)
    {
        return in_array($userRole, $this->permissions);
    }
    
    /**
     * Activate module
     */
    public function activate()
    {
        $this->isActive = true;
        $this->onActivate();
    }
    
    /**
     * Deactivate module
     */
    public function deactivate()
    {
        $this->isActive = false;
        $this->onDeactivate();
    }
    
    /**
     * Module activation hook
     */
    protected function onActivate() {}
    
    /**
     * Module deactivation hook
     */
    protected function onDeactivate() {}
    
    /**
     * Install module (create tables, etc.)
     */
    public function install()
    {
        return $this->onInstall();
    }
    
    /**
     * Uninstall module (cleanup)
     */
    public function uninstall()
    {
        return $this->onUninstall();
    }
    
    /**
     * Installation hook
     */
    protected function onInstall() { return true; }
    
    /**
     * Uninstallation hook
     */
    protected function onUninstall() { return true; }
}
?>
