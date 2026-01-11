<?php

namespace App\Services;

use App\Widgets\BaseWidget;
use App\Core\Database;
use Exception;
use PDO;

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
    public function __construct(?Database $database = null)
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
     * Get class name from PHP file using Tokenizer
     * 
     * @param string $file
     * @return string|null
     */
    private function getClassNameFromFile($file)
    {
        $fp = fopen($file, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;
        
        while (!$class) {
            if (feof($fp)) break;
            
            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);
            
            if (strpos($buffer, '{') === false) continue;
            
            for (; $i < count($tokens); $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= $tokens[$j][1] . '\\';
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }
                
                if ($tokens[$i][0] === T_CLASS) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }
        
        fclose($fp);
        return $class ? $namespace . $class : null;
    }
    
    /**
     * Check if class is a valid widget
     * 
     * @param string $className
     * @return bool
     */
    private function isWidgetClass($className)
    {
        if (!class_exists($className)) {
            return false;
        }
        
        $reflection = new \ReflectionClass($className);
        return $reflection->isSubclassOf(BaseWidget::class) && !$reflection->isAbstract();
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
            
            if ($results) {
                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                    $this->widgetConfigs[$row['id']] = $row;
                }
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
    
    /**
     * Get information about a specific widget class
     * 
     * @param string $className
     * @return array|null
     */
    public function getWidgetClassInfo($className)
    {
        if (!isset($this->widgetClasses[$className])) {
            return null;
        }
        
        try {
            // Load the class to get reflection information
            require_once $this->widgetClasses[$className];
            
            $reflection = new \ReflectionClass($className);
            $info = [
                'name' => $className,
                'file' => $this->widgetClasses[$className],
                'description' => $reflection->getDocComment(),
                'methods' => [],
                'properties' => [],
                'is_abstract' => $reflection->isAbstract(),
                'is_interface' => $reflection->isInterface(),
                'parent_class' => $reflection->getParentClass() ? $reflection->getParentClass()->getName() : null,
                'interfaces' => $reflection->getInterfaceNames()
            ];
            
            // Get public methods
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $info['methods'][] = [
                    'name' => $method->getName(),
                    'return_type' => $method->getReturnType(),
                    'parameters' => array_map(function($param) {
                        return [
                            'name' => $param->getName(),
                            'type' => $param->getType(),
                            'optional' => $param->isOptional(),
                            'default_value' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
                        ];
                    }, $method->getParameters())
                ];
            }
            
            // Get public properties
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $info['properties'][] = [
                    'name' => $property->getName(),
                    'type' => $property->getType(),
                    'default_value' => $property->getValue()
                ];
            }
            
            return $info;
        } catch (Exception $e) {
            error_log("Failed to get widget class info for {$className}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Load a specific widget by ID
     * 
     * @param string $widgetId
     * @return BaseWidget|null
     */
    public function loadWidget($widgetId)
    {
        if (isset($this->widgetInstances[$widgetId])) {
            return $this->widgetInstances[$widgetId];
        }
        
        if (!isset($this->widgetConfigs[$widgetId])) {
            return null;
        }
        
        try {
            $config = $this->widgetConfigs[$widgetId];
            $widgetData = array_merge($config, [
                'config' => json_decode($config['config'], true) ?? []
            ]);
            
            $widget = BaseWidget::fromArray($widgetData);
            
            if ($widget) {
                $this->widgetInstances[$widgetId] = $widget;
            }
            
            return $widget;
        } catch (Exception $e) {
            error_log("Failed to load widget {$widgetId}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Delete a widget
     * 
     * @param string $widgetId
     * @return bool
     */
    public function deleteWidget($widgetId)
    {
        try {
            // Remove from local cache
            unset($this->widgetInstances[$widgetId]);
            unset($this->widgetConfigs[$widgetId]);
            
            // Skip database operations if no database connection
            if (!$this->database) {
                return true;
            }
            
            // Database deletion would go here
            // For now, just return true
            return true;
        } catch (Exception $e) {
            error_log("Failed to delete widget {$widgetId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set widget enabled/disabled status
     * 
     * @param string $widgetId
     * @param bool $enabled
     * @return bool
     */
    public function setWidgetEnabled($widgetId, $enabled = true)
    {
        try {
            if (isset($this->widgetInstances[$widgetId])) {
                $widget = $this->widgetInstances[$widgetId];
                if ($enabled) {
                    $widget->enable();
                } else {
                    $widget->disable();
                }
            }
            
            // Update configuration
            if (isset($this->widgetConfigs[$widgetId])) {
                $this->widgetConfigs[$widgetId]['is_enabled'] = $enabled;
            }
            
            // Skip database operations if no database connection
            if (!$this->database) {
                return true;
            }
            
            // Database update would go here
            return true;
        } catch (Exception $e) {
            error_log("Failed to set widget enabled status for {$widgetId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set widget visibility
     * 
     * @param string $widgetId
     * @param bool $visible
     * @return bool
     */
    public function setWidgetVisible($widgetId, $visible = true)
    {
        try {
            if (isset($this->widgetInstances[$widgetId])) {
                $widget = $this->widgetInstances[$widgetId];
                $widget->setVisible($visible);
            }
            
            // Update configuration
            if (isset($this->widgetConfigs[$widgetId])) {
                $this->widgetConfigs[$widgetId]['is_visible'] = $visible;
            }
            
            // Skip database operations if no database connection
            if (!$this->database) {
                return true;
            }
            
            // Database update would go here
            return true;
        } catch (Exception $e) {
            error_log("Failed to set widget visibility for {$widgetId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set widget position
     * 
     * @param string $widgetId
     * @param int $position
     * @return bool
     */
    public function setWidgetPosition($widgetId, $position)
    {
        try {
            if (isset($this->widgetInstances[$widgetId])) {
                $widget = $this->widgetInstances[$widgetId];
                $widget->setPosition($position);
            }
            
            // Update configuration
            if (isset($this->widgetConfigs[$widgetId])) {
                $this->widgetConfigs[$widgetId]['position'] = $position;
            }
            
            // Skip database operations if no database connection
            if (!$this->database) {
                return true;
            }
            
            // Database update would go here
            return true;
        } catch (Exception $e) {
            error_log("Failed to set widget position for {$widgetId}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create widget database tables
     * 
     * @return bool
     */
    public function createWidgetTables()
    {
        if (!$this->database) {
            return false;
        }
        
        try {
            // Create main widgets table
            $widgetsTable = "
                CREATE TABLE IF NOT EXISTS {$this->widgetTable} (
                    id VARCHAR(255) PRIMARY KEY,
                    class_name VARCHAR(255) NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    config JSON,
                    is_enabled BOOLEAN DEFAULT 1,
                    is_visible BOOLEAN DEFAULT 1,
                    position INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_enabled (is_enabled),
                    INDEX idx_visible (is_visible),
                    INDEX idx_position (position)
                )
            ";
            
            $this->database->query($widgetsTable);
            
            // Create widget settings table
            $settingsTable = "
                CREATE TABLE IF NOT EXISTS {$this->widgetSettingsTable} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    widget_id VARCHAR(255) NOT NULL,
                    setting_key VARCHAR(255) NOT NULL,
                    setting_value TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (widget_id) REFERENCES {$this->widgetTable}(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_widget_setting (widget_id, setting_key),
                    INDEX idx_widget_id (widget_id)
                )
            ";
            
            $this->database->query($settingsTable);
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to create widget tables: " . $e->getMessage());
            return false;
        }
    }
}
?>
