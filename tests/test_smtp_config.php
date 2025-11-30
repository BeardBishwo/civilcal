<?php
require_once 'app/bootstrap.php';

use App\Services\EmailManager;

echo "Testing SMTP Configuration\n";
echo "========================\n\n";

// Test with your provided SMTP settings
$testSettings = [
    'smtp_host' => 'mail.newsbishwo.com',
    'smtp_port' => 465,
    'smtp_username' => 'admin@newsbishwo.com',
    'smtp_password' => 'your_password_here', // You'll need to provide the actual password
    'smtp_encryption' => 'ssl',
    'from_email' => 'admin@newsbishwo.com',
    'from_name' => 'Bishwo Calculator',
    'reply_to' => 'admin@newsbishwo.com'
];

echo "SMTP Configuration:\n";
foreach ($testSettings as $key => $value) {
    // Don't display passwords
    if (strpos($key, 'password') !== false) {
        echo "  $key: " . (empty($value) ? '[NOT SET]' : '[SET]') . "\n";
    } else {
        echo "  $key: " . (empty($value) ? '[NOT SET]' : $value) . "\n";
    }
}

echo "\nTesting email configuration with bishwonathpaudel24@gmail.com...\n";

// Create email manager and test
$emailManager = new EmailManager();

// Update settings with your configuration
echo "\nUpdating settings...\n";
$result = $emailManager->updateSettings($testSettings);

if ($result) {
    echo "Settings updated successfully!\n";
} else {
    echo "Failed to update settings\n";
}

// Now test sending an email
echo "\nSending test email...\n";
$testResult = $emailManager->testEmailSettings('bishwonathpaudel24@gmail.com');

if ($testResult['success']) {
    echo "✅ Test email sent successfully!\n";
    echo "Message: " . $testResult['message'] . "\n";
} else {
    echo "❌ Failed to send test email\n";
    echo "Error: " . $testResult['message'] . "\n";
}

echo "\nCurrent email settings:\n";
$settings = $emailManager->getSettings();
foreach ($settings as $key => $value) {
    // Don't display passwords
    if (strpos($key, 'password') !== false) {
        echo "  $key: " . (empty($value) ? '[NOT SET]' : '[SET]') . "\n";
    } else {
        echo "  $key: " . (empty($value) ? '[NOT SET]' : $value) . "\n";
    }
}
?>