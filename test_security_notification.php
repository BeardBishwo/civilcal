<?php
require_once 'app/bootstrap.php';

use App\Services\SecurityNotificationService;
use ReflectionClass;

// Test the security notification service
echo "Testing Security Notification Service\n";
echo "====================================\n\n";

try {
    $securityService = new SecurityNotificationService();
    
    // Test with a sample admin user data
    $testUser = (object)[
        'id' => 1,
        'username' => 'admin',
        'email' => 'bishwonathpaudel24@gmail.com',
        'role' => 'admin'
    ];
    
    $testIpAddress = '150.107.106.149'; // Nepal IP from your example
    
    echo "Simulating new login for admin user from IP: $testIpAddress\n";
    
    // Use reflection to access the private method for testing
    $reflection = new ReflectionClass($securityService);
    $method = $reflection->getMethod('sendSecurityNotification');
    $method->setAccessible(true);
    
    echo "Sending test security notification...\n";
    $method->invokeArgs($securityService, [$testUser, $testIpAddress]);
    
    echo "Test notification sent successfully!\n";
    echo "Check your email at bishwonathpaudel24@gmail.com\n";
    echo "You should receive an email similar to:\n\n";
    
    // Show what the email would look like
    $locationInfo = ['country' => 'Nepal', 'city' => 'Unknown', 'latitude' => null, 'longitude' => null];
    $method = $reflection->getMethod('generateNotificationEmail');
    $method->setAccessible(true);
    $emailContent = $method->invokeArgs($securityService, [$testUser, $testIpAddress, $locationInfo]);
    
    // Extract just the text content for display
    $emailText = strip_tags($emailContent);
    $emailText = preg_replace('/\s+/', ' ', $emailText);
    $emailText = preg_replace('/\s{2,}/', "\n\n", $emailText);
    
    echo "Subject: Security Alert: New Login Detected\n";
    echo "Body:\n";
    echo "Hello!\n\n";
    echo "A new login has been made from a new IP address. If this wasn't you, please secure your account immediately.\n\n";
    echo "IP Address: $testIpAddress\n";
    echo "Location: Unknown, Nepal\n";
    echo "Date & Time: " . date('d-m-Y H:i') . "\n";
    echo "User: admin\n\n";
    echo "What to do if this wasn't you:\n";
    echo "- Change your password immediately\n";
    echo "- Enable two-factor authentication if not already enabled\n";
    echo "- Review your recent account activity\n";
    echo "- Contact support if you need assistance\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>