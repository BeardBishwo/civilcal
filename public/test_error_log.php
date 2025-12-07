<?php
// Get error log location
echo "error_log setting: " . ini_get('error_log') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";

// Try to write a test error log
error_log("TEST ERROR LOG MESSAGE");
echo "Test error logged\n";
