<?php
require_once 'app/bootstrap.php';

use App\Services\SettingsService;

echo "Security Settings Test\n";
echo "====================\n\n";

try {
    // Get all security settings
    $securitySettings = SettingsService::getAll('security');
    
    echo "Current Security Settings:\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($securitySettings as $key => $value) {
        echo sprintf("%-25s: %s\n", $key, var_export($value, true));
    }
    
    echo "\nTest updating a setting...\n";
    
    // Test updating a setting
    $result = SettingsService::set('enable_2fa', '1', 'boolean', 'security', 'Enable Two-Factor Authentication');
    echo "Updated enable_2fa setting: " . ($result ? "SUCCESS" : "NO CHANGE") . "\n";
    
    // Verify the update
    $newValue = SettingsService::get('enable_2fa');
    echo "New value of enable_2fa: " . var_export($newValue, true) . "\n";
    
    echo "\nTest completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>