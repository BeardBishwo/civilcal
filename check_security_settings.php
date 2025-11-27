<?php
require_once 'app/bootstrap.php';

use App\Services\SettingsService;

echo "Security Settings Check\n";
echo "=====================\n\n";

try {
    // Get all security settings
    $securitySettings = SettingsService::getAll('security');
    
    if (empty($securitySettings)) {
        echo "No security settings found in database.\n";
        echo "Creating default security settings...\n\n";
        
        // Create default security settings
        $defaultSettings = [
            'enable_2fa' => ['value' => '0', 'type' => 'boolean', 'group' => 'security', 'description' => 'Enable Two-Factor Authentication'],
            'force_https' => ['value' => '0', 'type' => 'boolean', 'group' => 'security', 'description' => 'Force HTTPS Connection'],
            'password_min_length' => ['value' => '8', 'type' => 'integer', 'group' => 'security', 'description' => 'Minimum Password Length'],
            'password_complexity' => ['value' => 'medium', 'type' => 'string', 'group' => 'security', 'description' => 'Password Complexity Level'],
            'session_timeout' => ['value' => '120', 'type' => 'integer', 'group' => 'security', 'description' => 'Session Timeout (Minutes)'],
            'max_login_attempts' => ['value' => '5', 'type' => 'integer', 'group' => 'security', 'description' => 'Maximum Login Attempts'],
            'ip_whitelist_enabled' => ['value' => '0', 'type' => 'boolean', 'group' => 'security', 'description' => 'Enable IP Whitelisting']
        ];
        
        foreach ($defaultSettings as $key => $config) {
            SettingsService::set($key, $config['value'], $config['type'], $config['group'], $config['description']);
            echo "Created setting: $key = " . $config['value'] . "\n";
        }
        
        echo "\nDefault security settings created successfully!\n";
    } else {
        echo "Current Security Settings:\n";
        foreach ($securitySettings as $key => $value) {
            echo "  $key: " . var_export($value, true) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>