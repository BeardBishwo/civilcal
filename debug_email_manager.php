<?php
require 'vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();

// Check if table exists
$tables = $db->query("SHOW TABLES LIKE 'site_settings'")->fetchAll();
echo "Table 'site_settings' exists: " . (count($tables) > 0 ? "YES" : "NO") . "\n\n";

if (count($tables) > 0) {
    // Show table structure
    echo "Table Structure:\n";
    $columns = $db->query("DESCRIBE site_settings")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }

    echo "\nAll settings in table:\n";
    $all = $db->query("SELECT * FROM site_settings")->fetchAll(PDO::FETCH_ASSOC);
    print_r($all);
}

// Test EmailManager
echo "\n\nTesting EmailManager:\n";
require_once 'app/Services/EmailManager.php';

try {
    $em = new EmailManager();
    echo "EmailManager instantiated successfully\n";

    $testSettings = [
        'from_name' => 'Test Name',
        'from_email' => 'test@example.com',
        'smtp_host' => 'smtp.test.com',
        'smtp_port' => '587',
        'smtp_username' => 'testuser',
        'smtp_password' => 'testpass',
        'smtp_encryption' => 'tls'
    ];

    echo "Attempting to update settings...\n";
    $result = $em->updateSettings($testSettings);
    echo "Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
