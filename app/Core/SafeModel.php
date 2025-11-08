<?php

namespace App\Core;

use App\Core\ModelLogger;

/**
 * Safe Model Method Caller
 * Provides intelligent method execution with fallbacks to prevent "Call to undefined method" errors
 * Uses pattern-based default returns for missing methods
 */
class SafeModel {
    private $instance;
    private $className;
    private $methodCache = [];
    
    public function __construct($instance) {
        $this->instance = $instance;
        $this->className = get_class($instance);
        $this->cacheMethods();
    }
    
    /**
     * Safely call a method with intelligent fallback
     */
    public function safeCall($method, $args = [], $defaultReturn = null, $throwOnMissing = false) {
        $startTime = microtime(true);
        
        try {
            // Check if method exists
            if ($this->methodExists($method)) {
                $result = call_user_func_array([$this->instance, $method], $args);
                $executionTime = microtime(true) - $startTime;
                
                ModelLogger::logMethodCall($this->className, $method, $args, true, $executionTime);
                return $result;
            }
            
            // Method doesn't exist - generate smart default or throw error
            if ($throwOnMissing) {
                $executionTime = microtime(true) - $startTime;
                $missingInfo = ModelLogger::logMissingMethod($this->className, $method);
                throw new \BadMethodCallException($missingInfo['error']);
            }
            
            // Generate intelligent default return value
            $defaultValue = $this->generateSmartDefault($method, $args, $defaultReturn);
            $executionTime = microtime(true) - $startTime;
            
            ModelLogger::logMethodCall($this->className, $method, $args, false, $executionTime, 'Method not found - using default');
            ModelLogger::logMissingMethod($this->className, $method);
            
            return $defaultValue;
            
        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;
            ModelLogger::logMethodCall($this->className, $method, $args, false, $executionTime, $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if method exists in the class
     */
    private function methodExists($method) {
        return method_exists($this->instance, $method) || 
               is_callable([$this->instance, $method]);
    }
    
    /**
     * Generate intelligent default return value based on method name pattern
     */
    private function generateSmartDefault($method, $args, $defaultReturn) {
        // If explicit default is provided, use it
        if ($defaultReturn !== null) {
            return $defaultReturn;
        }
        
        // Pattern-based defaults
        $method = strtolower($method);
        
        // Getter patterns -> return default values based on property type
        if (preg_match('/^(get|find|fetch|load|read)([A-Z][a-zA-Z0-9]*)$/', $method, $matches)) {
            $property = lcfirst($matches[2]);
            return $this->getDefaultForProperty($property, $args);
        }
        
        // Setter patterns -> return boolean success
        if (preg_match('/^(set|update|save|add|create|insert)([A-Z][a-zA-Z0-9]*)$/', $method, $matches)) {
            return true; // Assume success for setters
        }
        
        // Count patterns -> return 0
        if (strpos($method, 'count') !== false || 
            strpos($method, 'total') !== false || 
            strpos($method, 'sum') !== false) {
            return 0;
        }
        
        // Boolean queries -> return false by default
        if (strpos($method, 'is') === 0 || 
            strpos($method, 'has') === 0 || 
            strpos($method, 'exists') !== false ||
            strpos($method, 'can') === 0) {
            return false;
        }
        
        // Array-returning methods -> return empty array
        if (strpos($method, 'all') !== false || 
            strpos($method, 'list') !== false || 
            strpos($method, 'get') === 0) {
            return [];
        }
        
        // Methods with filters -> return empty array
        if (strpos($method, 'filter') !== false || 
            strpos($method, 'search') !== false) {
            return [];
        }
        
        // Default fallback based on method name length and type
        if (strlen($method) > 10) {
            return null; // Complex method, return null
        } else {
            return true; // Simple method, assume success
        }
    }
    
    /**
     * Get default value for a property based on its name and context
     */
    private function getDefaultForProperty($property, $args) {
        $property = strtolower($property);
        
        // ID and numeric fields -> return first arg or 0
        if (strpos($property, 'id') !== false || 
            strpos($property, 'count') !== false ||
            strpos($property, 'total') !== false ||
            strpos($property, 'sum') !== false) {
            return !empty($args) ? $args[0] : 0;
        }
        
        // Boolean fields -> return false
        if (strpos($property, 'is') === 0 || 
            strpos($property, 'has') !== false ||
            strpos($property, 'active') !== false ||
            strpos($property, 'enabled') !== false) {
            return false;
        }
        
        // Date/time fields -> return current timestamp
        if (strpos($property, 'date') !== false || 
            strpos($property, 'time') !== false ||
            strpos($property, 'created') !== false ||
            strpos($property, 'updated') !== false) {
            return date('Y-m-d H:i:s');
        }
        
        // Email fields -> return empty string
        if (strpos($property, 'email') !== false) {
            return '';
        }
        
        // Name/Title fields -> return empty string
        if (strpos($property, 'name') !== false || 
            strpos($property, 'title') !== false) {
            return '';
        }
        
        // Description/Content fields -> return empty string
        if (strpos($property, 'description') !== false || 
            strpos($property, 'content') !== false ||
            strpos($property, 'message') !== false) {
            return '';
        }
        
        // Default to empty string
        return '';
    }
    
    /**
     * Cache class methods for performance
     */
    private function cacheMethods() {
        $reflection = new \ReflectionClass($this->instance);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method) {
            $this->methodCache[$method->getName()] = true;
        }
    }
    
    /**
     * Magic method to make models automatically safe
     */
    public function __call($method, $args) {
        return $this->safeCall($method, $args);
    }
    
    /**
     * Static helper to create safe model wrapper
     */
    public static function make($instance) {
        return new self($instance);
    }
    
    /**
     * Get method call statistics for this instance
     */
    public function getCallStats() {
        return ModelLogger::getCallsByClass($this->className);
    }
    
    /**
     * Get failed method calls for this instance
     */
    public function getFailedCalls() {
        return array_filter($this->getCallStats(), function($call) {
            return !$call['success'];
        });
    }
}

/**
 * Safe Model Trait
 * Add this trait to any model to make all method calls safe
 */
trait SafeModelTrait {
    private $safeModel;
    
    /**
     * Get safe model wrapper
     */
    protected function safe() {
        if (!$this->safeModel) {
            $this->safeModel = new SafeModel($this);
        }
        return $this->safeModel;
    }
    
    /**
     * Safe method call wrapper
     */
    protected function safeCall($method, $args = [], $defaultReturn = null, $throwOnMissing = false) {
        return $this->safe()->safeCall($method, $args, $defaultReturn, $throwOnMissing);
    }
    
    /**
     * Make all undefined method calls safe
     */
    public function __call($method, $args) {
        return $this->safeCall($method, $args);
    }
    
    /**
     * Make all undefined static method calls safe (for static contexts)
     */
    public static function __callStatic($method, $args) {
        // For static calls, create a temporary instance
        $instance = new static();
        return $instance->safeCall($method, $args);
    }
}

/**
 * Safe Database Access Helper
 * Provides safe database operations with error handling
 */
class SafeDatabase {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Safe method call with error handling
     */
    public function safeCall($method, $args = []) {
        $startTime = microtime(true);
        
        try {
            if (method_exists($this->db, $method)) {
                $result = call_user_func_array([$this->db, $method], $args);
                ModelLogger::logMethodCall(get_class($this->db), $method, $args, true, microtime(true) - $startTime);
                return $result;
            }
            
            // Method doesn't exist, return safe default
            ModelLogger::logMethodCall(get_class($this->db), $method, $args, false, microtime(true) - $startTime, 'Method not found');
            return $this->getSafeDefault($method, $args);
            
        } catch (\Exception $e) {
            ModelLogger::logMethodCall(get_class($this->db), $method, $args, false, microtime(true) - $startTime, $e->getMessage());
            return $this->getSafeDefault($method, $args);
        }
    }
    
    /**
     * Get safe default based on method name
     */
    private function getSafeDefault($method, $args) {
        $method = strtolower($method);
        
        if (strpos($method, 'prepare') !== false) {
            return false; // PDO prepare returns false on error
        }
        
        if (strpos($method, 'query') !== false) {
            return false; // PDO query returns false on error
        }
        
        if (strpos($method, 'lastinsertid') !== false) {
            return 0; // Default ID
        }
        
        if (strpos($method, 'fetch') !== false || strpos($method, 'get') !== false) {
            return null; // No data found
        }
        
        if (strpos($method, 'fetchall') !== false) {
            return []; // Empty result set
        }
        
        return null; // General default
    }
    
    /**
     * Magic method for safe method calls
     */
    public function __call($method, $args) {
        return $this->safeCall($method, $args);
    }
}
?>
