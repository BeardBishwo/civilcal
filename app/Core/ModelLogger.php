<?php

namespace App\Core;

/**
 * Model Method Call Logger
 * Tracks all model method calls with parameters, execution time, and success/failure status
 * Detects missing methods automatically and generates suggested fixes
 */
class ModelLogger {
    private static $methodCalls = [];
    private static $startTime;
    private static $errorCount = 0;
    
    public static function init() {
        self::$startTime = microtime(true);
    }
    
    /**
     * Log a method call
     */
    public static function logMethodCall($class, $method, $args = [], $success = true, $executionTime = 0, $error = null) {
        $callData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'class' => $class,
            'method' => $method,
            'args' => self::serializeArgs($args),
            'success' => $success,
            'execution_time' => $executionTime,
            'error' => $error,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        self::$methodCalls[] = $callData;
        
        // Keep only last 100 calls to prevent memory issues
        if (count(self::$methodCalls) > 100) {
            self::$methodCalls = array_slice(self::$methodCalls, -100);
        }
        
        if (!$success) {
            self::$errorCount++;
        }
        
        // Log to file
        self::logToFile($callData);
    }
    
    /**
     * Detect missing method and log it
     */
    public static function logMissingMethod($class, $method, $calledBy = null) {
        $errorMessage = "Call to undefined method {$class}::{$method}";
        $suggestedFix = self::generateMissingMethodSuggestion($class, $method);
        
        $callData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'class' => $class,
            'method' => $method,
            'args' => [],
            'success' => false,
            'execution_time' => 0,
            'error' => $errorMessage,
            'suggested_fix' => $suggestedFix,
            'called_by' => $calledBy,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        self::$methodCalls[] = $callData;
        self::$errorCount++;
        
        // Log the missing method error
        error_log("[ModelLogger] Missing Method: {$errorMessage} | Fix: {$suggestedFix}");
        
        return [
            'error' => $errorMessage,
            'suggested_fix' => $suggestedFix
        ];
    }
    
    /**
     * Generate suggestion for missing method
     */
    private static function generateMissingMethodSuggestion($class, $method) {
        $suggestions = [];
        
        // Check if it's a compatibility wrapper
        $wrapperMethod = self::findCompatibilityMethod($class, $method);
        if ($wrapperMethod) {
            return "Method '{$method}' might be a compatibility wrapper. Add this method to {$class} or check if there's a similar method like '{$wrapperMethod}'.";
        }
        
        // Check if it's a common CRUD method pattern
        $crudPattern = self::identifyCrudPattern($method);
        if ($crudPattern) {
            return "Consider adding '{$method}' method to {$class}. Expected pattern: {$crudPattern['pattern']} returning: {$crudPattern['return_type']}";
        }
        
        // Check if it's a getter/setter pattern
        if (preg_match('/^(get|set|is|has)([A-Z][a-zA-Z0-9]*)$/', $method, $matches)) {
            $property = lcfirst($matches[2]);
            return "Add {$matches[1]} method for '{$property}' property to {$class}. Example: public function {$matches[1]}{$matches[2]}()";
        }
        
        return "Method '{$method}' is not defined in class {$class}. Add this method or create a compatibility wrapper method.";
    }
    
    /**
     * Find compatibility methods in the class
     */
    private static function findCompatibilityMethod($class, $method) {
        $compatMethods = [
            'getAll' => 'find',
            'getById' => 'find',
            'createNew' => 'create',
            'addNew' => 'create',
            'updateRecord' => 'update',
            'deleteRecord' => 'delete',
            'getAllWithFilters' => 'find',
            'getCount' => 'count',
            'getTotal' => 'count'
        ];
        
        return $compatMethods[$method] ?? null;
    }
    
    /**
     * Identify CRUD method pattern
     */
    private static function identifyCrudPattern($method) {
        $patterns = [
            'getAll' => ['pattern' => 'function getAll($filters = [])', 'return_type' => 'array'],
            'getById' => ['pattern' => 'function getById($id)', 'return_type' => 'array|false'],
            'create' => ['pattern' => 'function create($data)', 'return_type' => 'bool'],
            'update' => ['pattern' => 'function update($id, $data)', 'return_type' => 'bool'],
            'delete' => ['pattern' => 'function delete($id)', 'return_type' => 'bool'],
            'find' => ['pattern' => 'function find($id)', 'return_type' => 'array|false'],
            'count' => ['pattern' => 'function count($filters = [])', 'return_type' => 'int'],
        ];
        
        return $patterns[$method] ?? null;
    }
    
    /**
     * Serialize method arguments for logging
     */
    private static function serializeArgs($args) {
        $serialized = [];
        foreach ($args as $key => $value) {
            if (is_array($value)) {
                $serialized[$key] = 'Array(' . count($value) . ' items)';
            } elseif (is_object($value)) {
                $serialized[$key] = 'Object(' . get_class($value) . ')';
            } elseif (is_string($value) && strlen($value) > 100) {
                $serialized[$key] = substr($value, 0, 100) . '...';
            } else {
                $serialized[$key] = $value;
            }
        }
        return $serialized;
    }
    
    /**
     * Log call data to file
     */
    private static function logToFile($callData) {
        $logDir = __DIR__ . '/../../debug/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/model_calls.log';
        $status = $callData['success'] ? 'SUCCESS' : 'ERROR';
        
        $logMessage = sprintf(
            "[%s] [%s] %s::%s() - Time: %.4fs, Memory: %s, Error: %s",
            $callData['timestamp'],
            $status,
            $callData['class'],
            $callData['method'],
            $callData['execution_time'],
            self::formatBytes($callData['memory_usage']),
            $callData['error'] ?? 'None'
        );
        
        @file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Format bytes to human readable format
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get method call statistics
     */
    public static function getStatistics() {
        $totalCalls = count(self::$methodCalls);
        $successfulCalls = array_filter(self::$methodCalls, function($call) {
            return $call['success'];
        });
        $totalTime = array_sum(array_column(self::$methodCalls, 'execution_time'));
        $avgTime = $totalCalls > 0 ? $totalTime / $totalCalls : 0;
        
        $slowestCall = null;
        $maxTime = 0;
        foreach (self::$methodCalls as $call) {
            if ($call['execution_time'] > $maxTime) {
                $maxTime = $call['execution_time'];
                $slowestCall = $call;
            }
        }
        
        return [
            'total_calls' => $totalCalls,
            'successful_calls' => count($successfulCalls),
            'failed_calls' => self::$errorCount,
            'success_rate' => $totalCalls > 0 ? (count($successfulCalls) / $totalCalls) * 100 : 0,
            'total_execution_time' => $totalTime,
            'average_execution_time' => $avgTime,
            'slowest_call' => $slowestCall,
            'execution_time' => microtime(true) - self::$startTime
        ];
    }
    
    /**
     * Get recent method calls
     */
    public static function getRecentCalls($limit = 20) {
        return array_slice(self::$methodCalls, -$limit);
    }
    
    /**
     * Get method calls by class
     */
    public static function getCallsByClass($class) {
        return array_filter(self::$methodCalls, function($call) use ($class) {
            return $call['class'] === $class;
        });
    }
    
    /**
     * Get method calls by method name
     */
    public static function getCallsByMethod($method) {
        return array_filter(self::$methodCalls, function($call) use ($method) {
            return $call['method'] === $method;
        });
    }
    
    /**
     * Get failed method calls
     */
    public static function getFailedCalls() {
        return array_filter(self::$methodCalls, function($call) {
            return !$call['success'];
        });
    }
}

// Initialize the model logger
ModelLogger::init();
?>
