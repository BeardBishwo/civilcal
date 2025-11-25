<?php
require 'vendor/autoload.php';
require 'app/Services/EmailManager.php';

$emailManager = new EmailManager();

// Save real SMTP settings
$realSettings = [
    'from_name' => 'Bishwo Calculator',
    'from_email' => 'admin@newsbishwo.com',
    'smtp_host' => 'mail.newsbishwo.com',
    'smtp_port' => '465',
    'smtp_username' => 'admin@newsbishwo.com',
    'smtp_password' => '^,2J?4Yqda_*YtW&',
    'smtp_encryption' => 'ssl'
];

echo "Saving real SMTP settings...\n";
if ($emailManager->updateSettings($realSettings)) {
    echo "✅ Settings saved successfully!\n\n";

    echo "Sending test email to bishwonathpaudel24@gmail.com...\n";
    $result = $emailManager->testEmailSettings('bishwonathpaudel24@gmail.com');

    if ($result['success']) {
        echo "✅ TEST EMAIL SENT SUCCESSFULLY!\n";
        echo "Message: " . $result['message'] . "\n";
    } else {
        echo "❌ Failed to send test email\n";
        echo "Error: " . $result['error'] . "\n";
    }
} else {
    echo "❌ Failed to save settings\n";
}
