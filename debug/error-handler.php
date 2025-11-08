<?php
/**
 * Enhanced Error Handler for Bishwo Calculator with Advanced Error Monitoring
 * Catches missing methods, syntax errors, and provides contextual logging
 */

require_once __DIR__ . '/../app/Core/ModelLogger.php';
require_once __DIR__ . '/../app/Core/SafeModel.php';

// Define missing debug constants
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

if (!defined('DISPLAY_ERRORS')) {
    define('DISPLAY_ERRORS', false);
}

if (!defined('LOG_LEVEL_ERROR')) {
    define('LOG_LEVEL_ERROR', 1);
}

if (!defined('LOG_LEVEL_DEBUG')) {
    define('LOG_LEVEL_DEBUG', 2);
}

if (!defined('LOG_LEVEL_INFO')) {
    define('LOG_LEVEL_INFO', 3);
}

// Enhanced Error Handler Class
class AdvancedErrorHandler {
    private static $errorCount = 0;
    private static $errorHistory = [];
    private static $startTime;
    
    public static function init() {
        self::$startTime = microtime(true);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError($errno, $errstr, $errfile, $errline) {
        // Don't execute if error reporting is turned off
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING', 
            E_PARSE => 'PARSE ERROR',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE ERROR',
            E_CORE_WARNING => 'CORE WARNING',
            E_COMPILE_ERROR => 'COMPILE ERROR',
            E_COMPILE_WARNING => 'COMPILE WARNING',
            E_USER_ERROR => 'USER ERROR',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE => 'USER NOTICE',
            E_STRICT => 'STRICT NOTICE',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED'
        ];

        $errorType = $errorTypes[$errno] ?? 'UNKNOWN ERROR';
        
        // Enhanced error categorization
        $category = self::categorizeError($errstr, $errno);
        $suggestedFix = self::generateSuggestedFix($errstr, $errfile, $errline);
        $context = self::gatherContext($errfile, $errline);
        
        $errorData = [
            'type' => $errorType,
            'category' => $category,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => date('Y-m-d H:i:s'),
            'suggested_fix' => $suggestedFix,
            'context' => $context,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10)
        ];
        
        self::$errorHistory[] = $errorData;
        self::$errorCount++;
        
        // Log the enhanced error
        self::logError($errorData);
        
        // If not in debug mode, just prevent the default handler. Otherwise, display error and exit.
        if (!DEBUG_MODE || !DISPLAY_ERRORS) {
            return true;
        }
        
        self::displayEnhancedError($errorData);
        exit;
    }
    
    private static function categorizeError($errstr, $errno) {
        $message = strtolower($errstr);
        
        if (strpos($message, 'call to undefined method') !== false || 
            strpos($message, 'call to undefined function') !== false) {
            return 'MissingMethodError';
        }
        
        if (strpos($message, 'syntax error') !== false || 
            strpos($message, 'unexpected') !== false) {
            return 'SyntaxError';
        }
        
        if (strpos($message, 'undefined') !== false) {
            return 'UndefinedError';
        }
        
        if ($errno === E_ERROR || $errno === E_USER_ERROR) {
            return 'CriticalError';
        }
        
        return 'GeneralError';
    }
    
    private static function generateSuggestedFix($errstr, $errfile, $errline) {
        $message = strtolower($errstr);
        
        if (strpos($message, 'call to undefined method') !== false) {
            // Extract method name from error message
            preg_match('/call to undefined method\s+([^\s]+)::([^\s]+)/', $errstr, $matches);
            if (count($matches) >= 3) {
                $class = $matches[1];
                $method = $matches[2];
                return "Add method '{$method}' to class {$class} or create a compatibility wrapper method.";
            }
            
            preg_match('/call to undefined method\s+([^\s]+)/', $errstr, $matches);
            if (count($matches) >= 2) {
                $method = $matches[1];
                return "Check if method '{$method}' exists or if you need to add this method to the model class.";
            }
        }
        
        if (strpos($message, 'syntax error') !== false) {
            return "Check syntax around line {$errline} in {$errfile}. Look for missing semicolons, brackets, or quotes.";
        }
        
        return "Review the error at line {$errline} in {$errfile} for more details.";
    }
    
    private static function gatherContext($errfile, $errline) {
        if (!file_exists($errfile)) {
            return ['error' => 'File not found'];
        }
        
        $lines = file($errfile);
        $start = max(0, $errline - 3);
        $end = min(count($lines), $errline + 2);
        
        $context = [];
        for ($i = $start; $i < $end; $i++) {
            $lineNum = $i + 1;
            $marker = ($lineNum == $errline) ? '>> ' : '   ';
            $context[] = $marker . str_pad($lineNum, 4) . ': ' . rtrim($lines[$i]);
        }
        
        return $context;
    }
    
    private static function logError($errorData) {
        $logMessage = sprintf(
            "[%s] %s in %s:%d | Category: %s | Fix: %s",
            $errorData['timestamp'],
            $errorData['type'] . ': ' . $errorData['message'],
            $errorData['file'],
            $errorData['line'],
            $errorData['category'],
            $errorData['suggested_fix']
        );
        
        error_log($logMessage);
        
        // Also log to our custom error log if possible
        $errorLogDir = __DIR__ . '/logs';
        if (!is_dir($errorLogDir)) {
            @mkdir($errorLogDir, 0755, true);
        }
        
        $errorLogFile = $errorLogDir . '/enhanced_errors.log';
        @file_put_contents($errorLogFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    private static function displayEnhancedError($errorData) {
        // Don't display errors if headers already sent
        if (headers_sent()) {
            return;
        }

        http_response_code(500);
        
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Bishwo Calculator - Enhanced Error</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
               background: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .error-container { max-width: 1000px; margin: 0 auto; background: white; 
                          padding: 30px; border-radius: 10px; 
                          box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
                          border-left: 5px solid #dc3545; }
        .error-header { color: #dc3545; border-bottom: 1px solid #eee; 
                       padding-bottom: 15px; margin-bottom: 20px; }
        .error-type { background: #f8d7da; color: #721c24; padding: 5px 10px; 
                     border-radius: 3px; font-size: 12px; font-weight: bold; 
                     display: inline-block; margin-bottom: 10px; }
        .category { background: #d1ecf1; color: #0c5460; padding: 3px 8px; 
                   border-radius: 3px; font-size: 11px; font-weight: bold; 
                   display: inline-block; margin-left: 10px; }
        .error-details { background: #f8f9fa; padding: 15px; border-radius: 5px; 
                        margin: 15px 0; font-family: 'Courier New', monospace; }
        .suggested-fix { background: #d4edda; border: 1px solid #c3e6cb; 
                        color: #155724; padding: 15px; border-radius: 5px; 
                        margin: 15px 0; }
        .context { background: #fff3cd; border: 1px solid #ffeaa7; 
                  color: #856404; padding: 15px; border-radius: 5px; 
                  margin: 15px 0; font-family: 'Courier New', monospace; 
                  font-size: 12px; }
        .stats { background: #e9ecef; padding: 10px; border-radius: 5px; 
                margin-bottom: 20px; font-size: 12px; }
        .stats span { margin-right: 20px; }
    </style>
</head>
<body>
    <div class='error-container'>
        <div class='error-header'>
            <div class='error-type'>{$errorData['type']}</div>
            <div class='category'>{$errorData['category']}</div>
            <h1>🚨 Enhanced Error Detection</h1>
            <p><strong>Message:</strong> {$errorData['message']}</p>
        </div>
        
        <div class='stats'>
            <span><strong>Total Errors:</strong> " . self::$errorCount . "</span>
            <span><strong>Session Time:</strong> " . round(microtime(true) - self::$startTime, 2) . "s</span>
        </div>
        
        <div class='error-details'>
            <strong>File:</strong> {$errorData['file']}<br>
            <strong>Line:</strong> {$errorData['line']}<br>
            <strong>Time:</strong> {$errorData['timestamp']}
        </div>
        
        <div class='suggested-fix'>
            <h3>💡 Suggested Fix</h3>
            <p>{$errorData['suggested_fix']}</p>
        </div>
        
        <div class='context'>
            <h3>📍 Context (Lines " . max(1, $errorData['line'] - 2) . "-" . ($errorData['line'] + 2) . ")</h3>";
        
        foreach ($errorData['context'] as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
        
        echo "</div>
        <p><a href='/bishwo_calculator/'>← Back to Homepage</a></p>
    </div>
</body>
</html>";
    }
    
    public static function handleException($exception) {
        $errorData = [
            'type' => 'EXCEPTION',
            'category' => 'ExceptionError',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'timestamp' => date('Y-m-d H:i:s'),
            'suggested_fix' => 'Check exception handling and review the stack trace for more details.',
            'context' => [],
            'backtrace' => $exception->getTrace()
        ];
        
        self::$errorHistory[] = $errorData;
        self::$errorCount++;
        
        self::logError($errorData);
        
        if (DEBUG_MODE && DISPLAY_ERRORS) {
            self::displayEnhancedError($errorData);
        }
        exit;
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && $error['type'] === E_ERROR) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
        
        // Log request completion
        if (DEBUG_MODE) {
            $executionTime = microtime(true) - self::$startTime;
            $logMessage = sprintf(
                "[%s] Request completed in %.4f seconds | Errors: %d",
                date('Y-m-d H:i:s'),
                $executionTime,
                self::$errorCount
            );
            error_log($logMessage);
        }
    }
    
    public static function getErrorStats() {
        return [
            'total_errors' => self::$errorCount,
            'error_history' => array_slice(self::$errorHistory, -50), // Last 50 errors
            'execution_time' => microtime(true) - self::$startTime
        ];
    }
}

// Initialize the enhanced error handler
AdvancedErrorHandler::init();
?>
