<?php
/**
 * Simple test for system variables
 */

// Define BASE_PATH
define('BASE_PATH', dirname(__DIR__));

// Load only what we need
require_once __DIR__ . '/../vendor/autoload.php';

echo "=============================================\n";
echo "Testing System Variables\n";
echo "=============================================\n\n";

try {
    // Direct database connection
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=bishwo_calculator;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Get Welcome Message template
    $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE name = 'Welcome Message' LIMIT 1");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($template) {
        echo "✅ Found template: {$template['name']}\n";
        echo "Original Subject: {$template['subject']}\n\n";
        
        // Check if it uses {{site_name}}
        if (strpos($template['subject'], '{{site_name}}') !== false) {
            echo "✅ Template uses {{site_name}} variable\n";
        } else {
            echo "❌ Template does NOT use {{site_name}} variable\n";
        }
        
        // Get site_name from settings
        $stmt = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name' LIMIT 1");
        $siteName = $stmt->fetchColumn();
        
        if ($siteName) {
            echo "✅ Site name from settings: $siteName\n";
        } else {
            echo "⚠️  No site_name in settings, using default\n";
            $siteName = 'Bishwo Calculator';
        }
        
        // Manual variable replacement test
        $processedSubject = str_replace('{{site_name}}', $siteName, $template['subject']);
        echo "\nProcessed Subject: $processedSubject\n";
        
        if (strpos($processedSubject, '{{site_name}}') === false) {
            echo "✅ Variable replacement works!\n";
        }
    } else {
        echo "❌ Template not found\n";
    }
    
    echo "\n=============================================\n";
    echo "✅ Test Complete!\n";
    echo "=============================================\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
