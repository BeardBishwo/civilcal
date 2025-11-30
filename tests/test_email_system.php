<?php
require_once 'app/bootstrap.php';

use App\Services\EmailManager;

// Test the email system with your configuration
$emailManager = new EmailManager();

// Display current settings
echo "<h1>Email System Test</h1>\n";
echo "<h2>Current Settings</h2>\n";
$settings = $emailManager->getSettings();

echo "<pre>\n";
foreach ($settings as $key => $value) {
    if (strpos($key, 'password') !== false) {
        echo "$key: " . (empty($value) ? '[NOT SET]' : '[SET]') . "\n";
    } else {
        echo "$key: " . (empty($value) ? '[NOT SET]' : $value) . "\n";
    }
}
echo "</pre>\n";

// Test form
echo "<h2>Test Email Configuration</h2>\n";
echo "<form method='post'>\n";
echo "  <label for='test_email'>Test Email Address:</label><br>\n";
echo "  <input type='email' id='test_email' name='test_email' value='bishwonathpaudel24@gmail.com' required><br><br>\n";
echo "  <input type='submit' value='Send Test Email'>\n";
echo "</form>\n";

if ($_POST['test_email'] ?? false) {
    $testEmail = $_POST['test_email'];
    echo "<h2>Sending Test Email to $testEmail</h2>\n";
    
    $result = $emailManager->testEmailSettings($testEmail);
    
    if ($result['success']) {
        echo "<p style='color: green; font-weight: bold;'>✅ Success: " . htmlspecialchars($result['message']) . "</p>\n";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Error: " . htmlspecialchars($result['message']) . "</p>\n";
    }
}
?>