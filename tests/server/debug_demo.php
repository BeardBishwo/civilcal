<?php
/**
 * Debug System Demo
 * Creates sample errors and logs for testing
 */

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logFile = $logDir . '/error.log';

// Sample error entries for testing
$sampleErrors = [
    "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection timeout in /app/Models/User.php on line 45",
    "[" . date('Y-m-d H:i:s', strtotime('-1 hour')) . "] WARNING: Deprecated function mysql_query() used in legacy code",
    "[" . date('Y-m-d H:i:s', strtotime('-2 hours')) . "] NOTICE: User login attempt from new location: 192.168.1.100",
    "[" . date('Y-m-d H:i:s', strtotime('-3 hours')) . "] INFO: Module 'Analytics' activated by admin user",
    "[" . date('Y-m-d H:i:s', strtotime('-4 hours')) . "] ERROR: Failed to load module 'CustomModule' - class not found",
    "[" . date('Y-m-d H:i:s', strtotime('-5 hours')) . "] WARNING: High memory usage detected: 85% of limit reached",
    "[" . date('Y-m-d H:i:s', strtotime('-6 hours')) . "] INFO: Installer automatically deleted after first admin login",
    "[" . date('Y-m-d H:i:s', strtotime('-1 day')) . "] ERROR: File permission denied for /storage/uploads/",
    "[" . date('Y-m-d H:i:s', strtotime('-1 day')) . "] FATAL: Out of memory error in calculation processing",
    "[" . date('Y-m-d H:i:s', strtotime('-2 days')) . "] WARNING: SSL certificate expires in 30 days"
];

// Write sample errors to log file
foreach ($sampleErrors as $error) {
    file_put_contents($logFile, $error . "\n", FILE_APPEND | LOCK_EX);
}

echo "✅ Debug Demo Complete!\n\n";
echo "📝 Sample error logs created in: {$logFile}\n";
echo "📊 Total log entries: " . count($sampleErrors) . "\n\n";

echo "🎯 Next Steps:\n";
echo "1. Visit /admin/debug to view the debug dashboard\n";
echo "2. Check /admin/debug/error-logs to see the log viewer\n";
echo "3. Run system tests from the debug panel\n";
echo "4. Test error log filtering and clearing\n\n";

echo "🔧 Admin Access:\n";
echo "URL: /admin/debug\n";
echo "Requirements: Admin login required\n\n";

// Test error logging
error_log("[DEBUG] System verification completed - " . date('Y-m-d H:i:s'));

echo "🚀 Debug system is ready for testing!\n";
?>
