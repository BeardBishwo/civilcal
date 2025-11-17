<?php
/**
 * Green Building Tools Plugin
 * 
 * Environmental impact calculations for sustainable building design
 * 
 * @package GreenBuildingTools
 * @version 1.0.0
 * @author Sustainable Engineering Co
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not permitted');
}

// Plugin initialization
class GreenBuildingToolsPlugin {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Register plugin hooks
        $this->registerHooks();
    }
    
    /**
     * Register plugin hooks and filters
     */
    private function registerHooks() {
        // Add plugin calculators to the system
        add_filter('calculators_list', [$this, 'addCalculators']);
        
        // Register plugin assets
        add_action('enqueue_scripts', [$this, 'enqueueAssets']);
        
        // Register plugin routes
        add_action('init_routes', [$this, 'registerRoutes']);
    }
    
    /**
     * Add plugin calculators to the system
     */
    public function addCalculators($calculators) {
        $pluginCalculators = [
            [
                'slug' => 'energy-efficiency-analysis',
                'name' => 'Energy Efficiency Analysis',
                'description' => 'Calculate building energy consumption and efficiency ratings',
                'category' => 'estimation',
                'icon' => 'fas fa-bolt',
                'plugin' => 'green-building-tools'
            ],
            [
                'slug' => 'carbon-footprint-calculator',
                'name' => 'Carbon Footprint Calculator',
                'description' => 'Estimate carbon emissions from construction and operation',
                'category' => 'estimation',
                'icon' => 'fas fa-cloud',
                'plugin' => 'green-building-tools'
            ],
            [
                'slug' => 'leed-points-calculator',
                'name' => 'LEED Points Calculator',
                'description' => 'Calculate potential LEED certification points',
                'category' => 'estimation',
                'icon' => 'fas fa-certificate',
                'plugin' => 'green-building-tools'
            ]
        ];
        
        return array_merge($calculators, $pluginCalculators);
    }
    
    /**
     * Enqueue plugin assets (CSS/JS)
     */
    public function enqueueAssets() {
        // Register plugin styles
        if (file_exists(__DIR__ . '/assets/css/plugin.css')) {
            echo '<link rel="stylesheet" href="/plugins/calculator-plugins/green-building-tools/assets/css/plugin.css">';
        }
        
        // Register plugin scripts
        if (file_exists(__DIR__ . '/assets/js/plugin.js')) {
            echo '<script src="/plugins/calculator-plugins/green-building-tools/assets/js/plugin.js"></script>';
        }
    }
    
    /**
     * Register plugin routes
     */
    public function registerRoutes() {
        // Routes will be registered by the main routing system
        // This is a placeholder for custom route registration if needed
    }
}

// Initialize the plugin
GreenBuildingToolsPlugin::getInstance();

// Helper functions for the plugin (if any)
if (!function_exists('add_filter')) {
    function add_filter($hook, $callback) {
        // Placeholder - will be implemented by main system
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback) {
        // Placeholder - will be implemented by main system
    }
}
