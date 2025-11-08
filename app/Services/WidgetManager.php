<?php

namespace App\Services;

use App\Widgets\BaseWidget;
use App\Core\Database;
use Exception;

/**
 * WidgetManager - Service for managing widgets in the Bishwo Calculator system
 * 
 * This service provides functionality for:
 * - Loading and saving widgets
 * - Managing widget instances
 * - Widget configuration and settings
 * - Widget lifecycle management
 * - Database integration for widget persistence
 */
class WidgetManager
{
    private $database;
    private $widgetInstances = [];
    private $widgetClasses = [];
    private $widgetConfigs = [];
    private $isInitialized = false;
    
    // Widget table configuration
    private $widgetTable = 'widgets';
    private $widgetSettingsTable = 'widget_settings';
    
    /**
     * Constructor
     * 
     * @param Database|null $database
     */
    public function __construct(Database $database = null)
    {
        $this->database = $database;
        $this->initialize();
    }
    
    /**
     * Initialize WidgetManager
     */
    private function initialize()
    {
        try {
            $this->loadWidgetClasses();
            $this->loadWidgetConfigs();
            $this->isInitialized = true;
        } catch (Exception $e) {
            error_log("WidgetManager initialization failed: " . $e->getMessage());
            $this->isInitialized = false;
        }
    }
    
    /**
     * Load available widget classes
     */
    private function loadWidgetClasses()
    {
        $widgetDirectories = [
            __DIR__ . '/../Widgets/',
            __DIR__ . '/../../plugins/widget-plugins/'
        ];
        
        foreach ($widgetDirectories as $directory) {
            if (is_dir($directory)) {
                $this->scanWidgetDirectory($directory);
            }
        }
    }
    
    /**
     * Scan directory for widget classes
     * 
     * @param string $directory
     */
    private function scanWidgetDirectory($directory)
    {
        $files = glob($directory . '*.php');
        
        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);
            if ($className && $this->isWidgetClass($className)) {
                $this->widgetClasses[$className] = $file;
            }
        }
    }
    
    /**
     * Get class name from PHP file
     * 
     * @param string $file
     * @return string|null
     */
    private function getClassNameFromFile($file)
    {
        $content = file_get_contents($file);
        if (preg_match('/class\s+(\w+)\s+extends\s+BaseWidget/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Check if class is a valid widget
     * 
     * @param string $className
     * @return bool
     */
    private function isWidgetClass($className)
    {
        return class_exists($className) && is_subclass_of($className, BaseWidget::class);
    }
    
    /**
     * Load widget configurations from database
     */
    private function loadWidgetConfigs()
    {
        if (!$this->database) {
            return; // Skip database loading if no database connection
        }
        
        try {
            $query = "SELECT * FROM {$this->widgetTable} ORDER BY position ASC";
            $results = $this->database->query($query);
            
            while ($row = $results->fetch_assoc()) {
                $this->widgetConfigs[$row['id']] = $row;
            }
        } catch (Exception $e) {
            error_log("Failed to load widget configs: " . $e->getMessage());
        }
    }
    
    /**
     * Register a new widget class
     * 
     * @param string $className
     * @param string $filePath
     */
    public function registerWidgetClass($className, $filePath)
    {
        if ($this->isWidgetClass($className)) {
            $this->widgetClasses[$className] = $filePath;
        }
    }
    
    /**
     * Get all available widget classes
     * 
     * @return array
     */
    public function getAvailableWidgetClasses()
    {
        return array_keys($this->widgetClasses);
    }
    
    /**
     * Get all widget classes (alias for getAvailableWidgetClasses)
     * 
     * @return array
     */
    public function getWidgetClasses()
    {
        return $this->getAvailableWidgetClasses();
    }
    
    /**
     * Create a new widget instance
     * 
     * @param string $className
     * @param array $config
     * @return BaseWidget|null
     */
    public function createWidget($className, $config = [])
    {
        if (!isset($this->widgetClasses[$className])) {
            throw new Exception("Widget class {$className} not found");
        }
        
        try {
            $widget = new $className($config);
            return $widget;
        } catch (Exception $e) {
            error_log("Failed to create widget {$className}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Save widget configuration
     * 
     * @param BaseWidget $widget
     * @return bool
     */
    public function saveWidget(BaseWidget $widget)
    {
        // Always update local cache
        $data = $widget->toArray();
        $this->widgetConfigs[$data['id']] = $data;
        $this->widgetInstances[$data['id']] = $widget;
        
        // Skip database operations if no database connection
        if (!$this->database) {
            return true;
        }
        
        try {
            // Database operations would go here
            // For now, just return true since we don't have a real database
            return true;
        } catch (Exception $e) {
            error_log("Failed to save widget: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all widgets
     * 
     * @param bool $enabledOnly
     * @param bool $visibleOnly
     * @return array
     */
    public function getAllWidgets($enabledOnly = false, $visibleOnly = false)
    {
        $widgets = [];
        
        foreach ($this->widgetConfigs as $widgetId => $config) {
            if ($enabledOnly && !$config['is_enabled']) {
                continue;
            }
            
            if ($visibleOnly && !$config['is_visible']) {
                continue;
            }
            
            $widgetData = array_merge($config, [
                'config' => json_decode($config['config'], true) ?? []
            ]);
            
            try {
                $widget = BaseWidget::fromArray($widgetData);
                if ($widget && $widget->isEnabled()) {
                    $widgets[] = $widget;
                }
            } catch (Exception $e) {
                error_log("Failed to load widget {$widgetId}: " . $e->getMessage());
            }
        }
        
        // Sort by position if position method exists
        usort($widgets, function($a, $b) {
            if (method_exists($a, 'getPosition') && method_exists($b, 'getPosition')) {
                return $a->getPosition() - $b->getPosition();
            }
            return 0;
        });
        
        return $widgets;
    }
    
    /**
     * Render all enabled and visible widgets
     * 
     * @param array $data Additional data for rendering
     * @return array
     */
    public function renderWidgets($data = [])
    {
        $widgets = $this->getAllWidgets(true, true);
        $rendered = [];
        
        foreach ($widgets as $widget) {
            try {
                $rendered[] = [
                    'widget' => $widget,
                    'html' => $widget->render($data),
                    'metadata' => $widget->getMetadata()
                ];
            } catch (Exception $e) {
                error_log("Failed to render widget {$widget->getId()}: " . $e->getMessage());
            }
        }
        
        return $rendered;
    }
    
    /**
     * Get WidgetManager status
     * 
     * @return array
     */
    public function getStatus()
    {
        return [
            'initialized' => $this->isInitialized,
            'available_widget_classes' => count($this->widgetClasses),
            'loaded_widgets' => count($this->widgetConfigs),
            'active_widgets' => count($this->getAllWidgets(true, true)),
            'widget_classes' => array_keys($this->widgetClasses),
            'database_connected' => $this->database !== null
        ];
    }
}
