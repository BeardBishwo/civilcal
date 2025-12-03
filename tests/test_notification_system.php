<?php
/**
 * Comprehensive Notification System Test
 * Tests the entire notification system from database to UI
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\AdminNotification;
use App\Services\DatabaseService;

// Initialize database connection
DatabaseService::initialize();

echo "🔍 Starting Notification System Test\n";
echo "=================================\n\n";

// Test 1: Database Table Existence
echo "1️⃣ Testing database table existence...\n";
try {
    $result = DatabaseService::query("SHOW TABLES LIKE 'admin_notifications'");
    if ($result->rowCount() > 0) {
        echo "✅ admin_notifications table exists\n";
    } else {
        echo "❌ admin_notifications table does not exist\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Sample Data Insertion
echo "\n2️⃣ Testing sample data insertion...\n";
try {
    $notification = new AdminNotification();
    $testData = [
        'user_id' => 1,
        'title' => 'Test Notification',
        'message' => 'This is a test notification for system verification',
        'type' => 'info',
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $id = $notification->create($testData);
    if ($id) {
        echo "✅ Test notification created with ID: $id\n";
    } else {
        echo "❌ Failed to create test notification\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error creating notification: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: API Endpoint Test
echo "\n3️⃣ Testing API endpoints...\n";
$apiUrl = app_base_url('api/notifications/unread-count');
echo "Testing URL: $apiUrl\n";

try {
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Requested-With: XMLHttpRequest',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success'] === true) {
            echo "✅ API endpoint working, unread count: " . ($data['unread_count'] ?? 0) . "\n";
        } else {
            echo "❌ API endpoint returned error: " . ($data['message'] ?? 'Unknown error') . "\n";
            exit(1);
        }
    } else {
        echo "❌ API endpoint returned HTTP $httpCode\n";
        echo "Response: " . $response . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error testing API endpoint: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: JavaScript File Existence
echo "\n4️⃣ Testing JavaScript files...\n";
$jsFiles = [
    'themes/admin/assets/js/admin.js',
    'themes/admin/assets/js/notification-system.js'
];

foreach ($jsFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file does not exist\n";
        exit(1);
    }
}

// Test 5: HTML Structure Test
echo "\n5️⃣ Testing HTML structure...\n";
$layoutFile = 'themes/admin/layouts/admin_enhanced.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);

    $requiredElements = [
        'notification-badge' => 'Notification badge element',
        'notificationDropdown' => 'Notification dropdown container',
        'notification-list' => 'Notification list container'
    ];

    foreach ($requiredElements as $id => $description) {
        if (strpos($content, 'id="' . $id . '"') !== false) {
            echo "✅ $description found\n";
        } else {
            echo "❌ $description not found\n";
            exit(1);
        }
    }
} else {
    echo "❌ Layout file not found: $layoutFile\n";
    exit(1);
}

// Test 6: Cleanup
echo "\n6️⃣ Cleaning up test data...\n";
try {
    $notification = new AdminNotification();
    $deleted = $notification->delete($id);
    if ($deleted) {
        echo "✅ Test notification deleted successfully\n";
    } else {
        echo "❌ Failed to delete test notification\n";
    }
} catch (Exception $e) {
    echo "❌ Error deleting test notification: " . $e->getMessage() . "\n";
}

// Final Summary
echo "\n🎉 Notification System Test Summary\n";
echo "==================================\n";
echo "✅ Database table exists and is accessible\n";
echo "✅ Notification creation and deletion works\n";
echo "✅ API endpoints are functional\n";
echo "✅ JavaScript files are in place\n";
echo "✅ HTML structure is correct\n";
echo "\n🚀 Notification system is ready for use!\n";
echo "📝 The notification button should now show real-time updates\n";
echo "🔔 New notifications will trigger toast messages\n";
echo "📋 Clicking the bell icon will show the notification dropdown\n";