<?php
/**
 * Advanced Logging System for Bishwo Calculator
 */

// Define missing logging constants
if (!defined('LOG_TO_FILE')) {
    define('LOG_TO_FILE', true);
}

if (!defined('LOG_TO_CONSOLE')) {
    define('LOG_TO_CONSOLE', true);
}

if (!defined('CURRENT_LOG_LEVEL')) {
    define('CURRENT_LOG_LEVEL', LOG_LEVEL_INFO);
}

if (!defined('DEBUG_LOG_PATH')) {
    define('DEBUG_LOG_PATH', __DIR__ . '/logs/debug.log');
}

if (!defined('ERROR_LOG_PATH')) {
    define('ERROR_LOG_PATH', __DIR__ . '/logs/error.log');
}

if (!defined('ACCESS_LOG_PATH')) {
    define('ACCESS_LOG_PATH', __DIR__ . '/logs/access.log');
}

if (!defined('SYSTEM_LOG_PATH')) {
    define('SYSTEM_LOG_PATH', __DIR__ . '/logs/system.log');
}

if (!defined('LOG_LEVEL_ERROR')) {
    define('LOG_LEVEL_ERROR', 1);
}

if (!defined('LOG_LEVEL_WARNING')) {
    define('LOG_LEVEL_WARNING', 2);
}

if (!defined('LOG_LEVEL_INFO')) {
    define('LOG_LEVEL_INFO', 3);
}

if (!defined('LOG_LEVEL_DEBUG')) {
    define('LOG_LEVEL_DEBUG', 4);
}

function debug_log($message, $level = LOG_LEVEL_INFO) {
    // Check if we should log this level
    if ($level > CURRENT_LOG_LEVEL) {
        return;
    }

    $level_names = [
        LOG_LEVEL_ERROR => 'ERROR',
        LOG_LEVEL_WARNING => 'WARNING', 
        LOG_LEVEL_INFO => 'INFO',
        LOG_LEVEL_DEBUG => 'DEBUG'
    ];

    $level_name = $level_names[$level] ?? 'UNKNOWN';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    
    $log_entry = "[$timestamp] [$level_name] [$ip] $request_uri - $message" . PHP_EOL;

    // Log to file
    if (LOG_TO_FILE) {
        $log_file = DEBUG_LOG_PATH;
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }

    // Log to console (for browser developer tools)
    if (LOG_TO_CONSOLE && DEBUG_MODE) {
        $console_message = json_encode([
            'level' => strtolower($level_name),
            'message' => $message,
            'timestamp' => $timestamp
        ]);
        
        echo "<script>console.log($console_message);</script>" . PHP_EOL;
    }
}

function log_error($message) {
    debug_log($message, LOG_LEVEL_ERROR);
}

function log_warning($message) {
    debug_log($message, LOG_LEVEL_WARNING);
}

function log_info($message) {
    debug_log($message, LOG_LEVEL_INFO);
}

function log_debug($message) {
    debug_log($message, LOG_LEVEL_DEBUG);
}

function log_system($message) {
    if (LOG_TO_FILE) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] SYSTEM - $message" . PHP_EOL;
        file_put_contents(SYSTEM_LOG_PATH, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

function log_access() {
    if (LOG_TO_FILE) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'direct';
        
        $log_entry = "[$timestamp] $ip - $method $uri - $user_agent - $referrer" . PHP_EOL;
        file_put_contents(ACCESS_LOG_PATH, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Function to get recent logs for debugging
function get_recent_logs($limit = 50, $type = 'debug') {
    $log_files = [
        'debug' => DEBUG_LOG_PATH,
        'error' => ERROR_LOG_PATH,
        'access' => ACCESS_LOG_PATH,
        'system' => SYSTEM_LOG_PATH
    ];

    $log_file = $log_files[$type] ?? DEBUG_LOG_PATH;
    
    if (!file_exists($log_file)) {
        return ["Log file not found: $log_file"];
    }

    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -$limit);
    
    return array_reverse($lines);
}

// Function to clear logs
function clear_logs($type = 'all') {
    $log_files = [
        'debug' => DEBUG_LOG_PATH,
        'error' => ERROR_LOG_PATH, 
        'access' => ACCESS_LOG_PATH,
        'system' => SYSTEM_LOG_PATH
    ];

    if ($type === 'all') {
        foreach ($log_files as $file) {
            if (file_exists($file)) {
                file_put_contents($file, '');
            }
        }
        return true;
    } elseif (isset($log_files[$type])) {
        if (file_exists($log_files[$type])) {
            file_put_contents($log_files[$type], '');
            return true;
        }
    }
    
    return false;
}
?>
