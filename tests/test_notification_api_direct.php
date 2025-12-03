<?php
/**
 * Direct Notification API Test - Bypasses authentication for testing
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Test the notification model directly
try {
    $notificationModel = new \App\Models\Notification();

    echo "🔍 Testing Notification Model Directly...\n\n";

    // Test 1: Get unread count for user ID 1 (admin)
    echo "1️⃣ Testing getCountByUser(1):\n";
    $unreadCount = $notificationModel->getCountByUser(1);
    echo "   ✅ Unread count for user 1: $unreadCount\n";

    // Test 2: Get notifications for user ID 1
    echo "\n2️⃣ Testing getByUser(1):\n";
    $notifications = $notificationModel->getByUser(1, 10, 0);
    echo "   ✅ Found " . count($notifications) . " notifications\n";

    // Test 3: Get unread notifications
    echo "\n3️⃣ Testing getUnreadByUser(1):\n";
    $unreadNotifications = $notificationModel->getUnreadByUser(1, 10, 0);
    echo "   ✅ Found " . count($unreadNotifications) . " unread notifications\n";

    // Test 4: Display sample notification
    if (!empty($unreadNotifications)) {
        echo "\n4️⃣ Sample Unread Notification:\n";
        $sample = $unreadNotifications[0];
        echo "   Title: " . ($sample['title'] ?? 'N/A') . "\n";
        echo "   Message: " . ($sample['message'] ?? 'N/A') . "\n";
        echo "   Type: " . ($sample['type'] ?? 'N/A') . "\n";
        echo "   Is Read: " . ($sample['is_read'] ? 'Yes' : 'No') . "\n";
    }

    // Test 5: Test JSON response format
    echo "\n5️⃣ Testing API Response Format:\n";
    $response = [
        'success' => true,
        'unread_count' => $unreadCount,
        'notifications' => $unreadNotifications
    ];

    $jsonResponse = json_encode($response, JSON_PRETTY_PRINT);
    echo "   " . ($jsonResponse ? "✅ JSON Response Generated" : "❌ JSON Encoding Failed") . "\n";
    if ($jsonResponse) {
        echo "   Sample Response:\n";
        echo "   " . str_replace("\n", "\n   ", $jsonResponse) . "\n";
    }

    echo "\n🎉 All model tests passed! The notification system backend is working.\n";
    echo "🔍 If API still fails, the issue is likely:\n";
    echo "   1. Authentication middleware\n";
    echo "   2. Route configuration\n";
    echo "   3. Session/cookie issues\n";
    echo "   4. Web server configuration\n";

} catch (Exception $e) {
    echo "❌ Notification Model Test Failed:\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
}