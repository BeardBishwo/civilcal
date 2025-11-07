<?php
/**
 * Custom Error Handler for Bishwo Calculator
 */

// Register custom error handler
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');
register_shutdown_function('shutdown_handler');

function custom_error_handler($errno, $errstr, $errfile, $errline) {
    // Don't execute if error reporting is turned off
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $error_types = [
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
        E_USER_DEPRECATED => 'USER DEPRECATED'
    ];

    $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN ERROR';

    $error_message = "[$error_type] $errstr in $errfile on line $errline";

    // Log the error
    error_log($error_message);
    debug_log($error_message, LOG_LEVEL_ERROR);

    // Display error if in debug mode
    if (DEBUG_MODE && DISPLAY_ERRORS) {
        display_error_page($error_type, $errstr, $errfile, $errline, debug_backtrace());
    }

    // Don't execute PHP internal error handler
    return true;
}

function custom_exception_handler($exception) {
    $error_message = "[EXCEPTION] " . $exception->getMessage() . 
                    " in " . $exception->getFile() . 
                    " on line " . $exception->getLine();

    error_log($error_message);
    debug_log($error_message, LOG_LEVEL_ERROR);

    if (DEBUG_MODE && DISPLAY_ERRORS) {
        display_error_page(
            'EXCEPTION', 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(),
            $exception->getTrace()
        );
    }
}

function shutdown_handler() {
    $error = error_get_last();
    
    if ($error !== NULL && $error['type'] === E_ERROR) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        $error_message = "[SHUTDOWN ERROR] $errstr in $errfile on line $errline";
        
        error_log($error_message);
        debug_log($error_message, LOG_LEVEL_ERROR);

        if (DEBUG_MODE && DISPLAY_ERRORS) {
            display_error_page('SHUTDOWN ERROR', $errstr, $errfile, $errline);
        }
    }

    // Log request completion
    if (DEBUG_MODE) {
        $execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        debug_log("Request completed in " . round($execution_time, 4) . " seconds", LOG_LEVEL_DEBUG);
    }
}

function display_error_page($error_type, $error_message, $error_file, $error_line, $backtrace = []) {
    // Don't display errors if headers already sent
    if (headers_sent()) {
        return;
    }

    http_response_code(500);

    // Simple error page for quick debugging
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Bishwo Calculator - Error</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background: #f8f9fa; 
                color: #333; 
                margin: 0; 
                padding: 20px; 
            }
            .error-container { 
                max-width: 800px; 
                margin: 0 auto; 
                background: white; 
                padding: 30px; 
                border-radius: 10px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                border-left: 5px solid #dc3545; 
            }
            .error-header { 
                color: #dc3545; 
                border-bottom: 1px solid #eee; 
                padding-bottom: 15px; 
                margin-bottom: 20px; 
            }
            .error-details { 
                background: #f8f9fa; 
                padding: 15px; 
                border-radius: 5px; 
                margin: 15px 0; 
                font-family: monospace; 
            }
            .backtrace { 
                margin-top: 20px; 
            }
            .backtrace-item { 
                padding: 10px; 
                border-bottom: 1px solid #eee; 
                font-family: monospace; 
                font-size: 12px; 
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <div class='error-header'>
                <h1>🚨 $error_type</h1>
                <p>$error_message</p>
            </div>
            
            <div class='error-details'>
                <strong>File:</strong> $error_file<br>
                <strong>Line:</strong> $error_line<br>
                <strong>Time:</strong> " . date('Y-m-d H:i:s') . "
            </div>";

    if (!empty($backtrace) && DEBUG_MODE) {
        echo "<div class='backtrace'>
                <h3>Backtrace:</h3>";
        foreach ($backtrace as $trace) {
            $file = $trace['file'] ?? 'unknown';
            $line = $trace['line'] ?? 'unknown';
            $function = $trace['function'] ?? 'unknown';
            $class = $trace['class'] ?? '';
            $type = $trace['type'] ?? '';
            
            echo "<div class='backtrace-item'>
                    $file:$line - $class$type$function()
                  </div>";
        }
        echo "</div>";
    }

    echo "<p><a href='/bishwo_calculator/'>← Back to Homepage</a></p>
        </div>
    </body>
    </html>";

    exit;
}
?>
