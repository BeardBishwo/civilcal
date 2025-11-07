<?php
/**
 * Bishwo Calculator Debug System
 * Console and File Logging System
 */

// Debug configuration
define('DEBUG_MODE', true);
define('LOG_TO_FILE', true);
define('LOG_TO_CONSOLE', true);

// Log directory
define('LOG_DIR', __DIR__ . '/logs/');
define('DEBUG_LOG', LOG_DIR . 'debug.log');
define('ERROR_LOG', LOG_DIR . 'error.log');
define('ACCESS_LOG', LOG_DIR . 'access.log');

// Create log directory if it doesn't exist
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

/**
 * Log a debug message
 */
function log_debug($message, $context = 'system') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [DEBUG] [$context] $message" . PHP_EOL;
    
    if (LOG_TO_CONSOLE) {
        echo "<script>console.log({\"level\":\"debug\",\"message\":\"$message\",\"timestamp\":\"$timestamp\"});</script>" . PHP_EOL;
    }
    
    if (LOG_TO_FILE) {
        file_put_contents(DEBUG_LOG, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Log an error message
 */
function log_error($message, $context = 'system') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [ERROR] [$context] $message" . PHP_EOL;
    
    if (LOG_TO_CONSOLE) {
        echo "<script>console.log({\"level\":\"error\",\"message\":\"$message\",\"timestamp\":\"$timestamp\"});</script>" . PHP_EOL;
    }
    
    if (LOG_TO_FILE) {
        file_put_contents(ERROR_LOG, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Log an info message
 */
function log_info($message, $context = 'system') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [INFO] [$context] $message" . PHP_EOL;
    
    if (LOG_TO_CONSOLE) {
        echo "<script>console.log({\"level\":\"info\",\"message\":\"$message\",\"timestamp\":\"$timestamp\"});</script>" . PHP_EOL;
    }
    
    if (LOG_TO_FILE) {
        file_put_contents(DEBUG_LOG, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Log access request
 */
function log_access() {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $logMessage = "[$timestamp] [ACCESS] [$ip] $method $uri" . PHP_EOL;
    
    if (LOG_TO_FILE) {
        file_put_contents(ACCESS_LOG, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Get recent logs
 */
function get_recent_logs($limit = 50, $type = 'debug') {
    if ($type === 'error') {
        $logFile = ERROR_LOG;
    } elseif ($type === 'access') {
        $logFile = ACCESS_LOG;
    } else {
        $logFile = DEBUG_LOG;
    }
    
    if (!file_exists($logFile)) {
        return [];
    }
    
    $lines = file($logFile);
    $lines = array_slice($lines, -$limit);
    return array_reverse($lines);
}

/**
 * Clear logs
 */
function clear_logs($type = 'debug') {
    if ($type === 'error') {
        $logFile = ERROR_LOG;
    } elseif ($type === 'access') {
        $logFile = ACCESS_LOG;
    } else {
        $logFile = DEBUG_LOG;
    }
    
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
    }
}

// Initialize debug system
log_info("Debug system initialized", "system");
