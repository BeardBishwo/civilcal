<?php
/**
 * Check PHP Error Logs
 * This script helps identify what errors are occurring
 */

echo "🔍 PHP ERROR LOG CHECKER\n";
echo "========================\n\n";

// Get PHP error log location
$error_log = ini_get('error_log');
if (empty($error_log)) {
    $error_log = ini_get('log_errors_max_len') ? '/tmp/php_errors.log' : 'php://stderr';
}

echo "📍 Error log location: " . $error_log . "\n";
echo "📊 Error reporting level: " . error_reporting() . "\n";
echo "🔧 Display errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "\n";
echo "📝 Log errors: " . (ini_get('log_errors') ? 'ON' : 'OFF') . "\n\n";

// Check if error log file exists and is readable
if ($error_log && $error_log !== 'php://stderr' && file_exists($error_log)) {
    echo "📄 Reading last 50 lines of error log:\n";
    echo "=====================================\n";
    
    $lines = file($error_log);
    $recent_lines = array_slice($lines, -50);
    
    foreach ($recent_lines as $line) {
        // Highlight login-related errors
        if (stripos($line, 'login') !== false || stripos($line, 'auth') !== false) {
            echo "🔴 " . trim($line) . "\n";
        } else {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "❌ Error log file not found or not accessible\n";
    echo "💡 Trying alternative locations...\n\n";
    
    $possible_locations = [
        '/var/log/php_errors.log',
        '/var/log/apache2/error.log',
        '/var/log/nginx/error.log',
        'C:\\laragon\\logs\\php_errors.log',
        'C:\\laragon\\logs\\apache_error.log',
        'C:\\xampp\\logs\\php_error_log',
        'C:\\wamp\\logs\\php_error.log'
    ];
    
    foreach ($possible_locations as $location) {
        if (file_exists($location)) {
            echo "✅ Found log file: $location\n";
            
            $lines = file($location);
            $recent_lines = array_slice($lines, -20);
            
            echo "📄 Last 20 lines:\n";
            foreach ($recent_lines as $line) {
                if (stripos($line, 'login') !== false || stripos($line, 'auth') !== false) {
                    echo "🔴 " . trim($line) . "\n";
                } else {
                    echo "   " . trim($line) . "\n";
                }
            }
            echo "\n";
            break;
        }
    }
}

// Test error logging
echo "\n🧪 Testing error logging:\n";
echo "=========================\n";

error_log("Test error log entry from check_error_logs.php - " . date('Y-m-d H:i:s'));
echo "✅ Test error logged (check above for the entry)\n";

// Check if we can write to error log
if (is_writable(dirname($error_log))) {
    echo "✅ Error log directory is writable\n";
} else {
    echo "❌ Error log directory is not writable\n";
}

echo "\n📋 DEBUGGING TIPS:\n";
echo "==================\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Check network tab for failed requests\n";
echo "3. Look for 'API Login' entries in the logs above\n";
echo "4. Test login with /test_login_debug.html\n";
echo "5. Check if session is working properly\n";

echo "\n✨ Log check complete!\n";
?>
