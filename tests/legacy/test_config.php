<?php
echo "Testing new config.php location...\n";
try {
    require_once __DIR__ . '/app/Config/config.php';
    echo "✅ Config loaded successfully\n";
    echo "APP_BASE: " . (defined('APP_BASE') ? APP_BASE : 'undefined') . "\n";
    echo "APP_URL: " . (defined('APP_URL') ? APP_URL : 'undefined') . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>


