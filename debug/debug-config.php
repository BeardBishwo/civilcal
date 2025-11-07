<?php
/**
 * Debug Configuration for Bishwo Calculator
 */

// Debug modes
define('DEBUG_MODE', true);
define('LOG_ERRORS', true);
define('DISPLAY_ERRORS', true);
define('LOG_TO_FILE', true);
define('LOG_TO_CONSOLE', true);

// Log file paths
define('ERROR_LOG_PATH', __DIR__ . '/logs/error.log');
define('DEBUG_LOG_PATH', __DIR__ . '/logs/debug.log');
define('ACCESS_LOG_PATH', __DIR__ . '/logs/access.log');
define('SYSTEM_LOG_PATH', __DIR__ . '/logs/system.log');

// Debug levels
define('LOG_LEVEL_ERROR', 1);
define('LOG_LEVEL_WARNING', 2);
define('LOG_LEVEL_INFO', 3);
define('LOG_LEVEL_DEBUG', 4);

// Current log level (set to DEBUG for maximum logging)
define('CURRENT_LOG_LEVEL', LOG_LEVEL_DEBUG);

// Set PHP error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', ERROR_LOG_PATH);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Create log directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Create temp directory if it doesn't exist
if (!is_dir(__DIR__ . '/temp')) {
    mkdir(__DIR__ . '/temp', 0755, true);
}

// Include debug systems
require_once __DIR__ . '/error-handler.php';
require_once __DIR__ . '/logger.php';

// Log system startup
if (DEBUG_MODE) {
    debug_log('Debug system initialized', LOG_LEVEL_INFO);
}
?>
