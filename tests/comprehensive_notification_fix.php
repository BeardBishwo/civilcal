<?php
/**
 * Comprehensive Notification System Fix and Test
 * This script will diagnose and fix all notification system issues
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Set up error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test 1: Check if notification button exists in the HTML
echo "🔍 Testing Notification System Components...\n\n";

$adminContent = file_get_contents('themes/admin/layouts/admin.php');

// Check critical HTML elements
$checks = [
    'Notification Button' => strpos($adminContent, 'id="notificationToggle"') !== false,
    'Notification Badge' => strpos($adminContent, 'id="notificationBadge"') !== false,
    'Notification Dropdown' => strpos($adminContent, 'id="notificationDropdown"') !== false,
    'Notification List Container' => strpos($adminContent, 'class="notification-list"') !== false,
    'Notification System JS' => strpos($adminContent, 'notification-system.js') !== false,
    'Notification Toast' => strpos($adminContent, 'id="notification-toast"') !== false,
];

echo "1️⃣ HTML Structure Check:\n";
foreach ($checks as $name => $result) {
    echo "   " . ($result ? "✅" : "❌") . " $name: " . ($result ? "Found" : "Missing") . "\n";
}

// Test 2: Check CSS styles
echo "\n2️⃣ CSS Styles Check:\n";
$cssChecks = [
    '.notification-dropdown' => strpos($adminContent, '.notification-dropdown') !== false,
    '.notification-item' => strpos($adminContent, '.notification-item') !== false,
    '.notification-badge' => strpos($adminContent, '.notification-badge') !== false,
    '.notification-toast' => strpos($adminContent, '.notification-toast') !== false,
];

foreach ($cssChecks as $selector => $result) {
    echo "   " . ($result ? "✅" : "❌") . " $selector: " . ($result ? "Found" : "Missing") . "\n";
}

// Test 3: Check JavaScript file
echo "\n3️⃣ JavaScript Implementation Check:\n";
$jsContent = file_get_contents('themes/admin/assets/js/notification-system.js');

$jsChecks = [
    'NotificationSystem Class' => strpos($jsContent, 'class NotificationSystem') !== false,
    'init() Method' => strpos($jsContent, 'init()') !== false,
    'DOMContentLoaded' => strpos($jsContent, 'DOMContentLoaded') !== false,
    'API Unread Count' => strpos($jsContent, '/api/notifications/unread-count') !== false,
    'API List' => strpos($jsContent, '/api/notifications/list') !== false,
    'setInterval Polling' => strpos($jsContent, 'setInterval') !== false,
    'Error Handling' => strpos($jsContent, 'catch (error)') !== false,
    'Global Instance' => strpos($jsContent, 'window.notificationSystem') !== false,
];

foreach ($jsChecks as $name => $result) {
    echo "   " . ($result ? "✅" : "❌") . " $name: " . ($result ? "Found" : "Missing") . "\n";
}

// Test 4: Check API Routes
echo "\n4️⃣ API Routes Check:\n";
$routesContent = file_get_contents('app/routes.php');

$routeChecks = [
    'Unread Count Route' => strpos($routesContent, '/api/notifications/unread-count') !== false,
    'List Route' => strpos($routesContent, '/api/notifications/list') !== false,
    'Mark Read Route' => strpos($routesContent, '/admin/notifications/mark-read') !== false,
    'Notification Controller' => strpos($routesContent, 'NotificationController') !== false,
];

foreach ($routeChecks as $name => $result) {
    echo "   " . ($result ? "✅" : "❌") . " $name: " . ($result ? "Found" : "Missing") . "\n";
}

// Test 5: Check Database
echo "\n5️⃣ Database Check:\n";
try {
    $pdo = \App\Core\Database::getInstance()->getPdo();

    // Check if table exists
    $result = $pdo->query("SHOW TABLES LIKE 'admin_notifications'");
    $tableExists = $result->rowCount() > 0;
    echo "   " . ($tableExists ? "✅" : "❌") . " admin_notifications table: " . ($tableExists ? "Exists" : "Missing") . "\n";

    if ($tableExists) {
        // Check table structure
        $columns = $pdo->query("DESCRIBE admin_notifications")->fetchAll();
        $columnNames = array_column($columns, 'Field');

        $requiredColumns = ['id', 'user_id', 'title', 'message', 'type', 'is_read', 'created_at'];
        $missingColumns = array_diff($requiredColumns, $columnNames);

        if (empty($missingColumns)) {
            echo "   ✅ All required columns present\n";
        } else {
            echo "   ❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
        }

        // Check if there are any notifications
        $countResult = $pdo->query("SELECT COUNT(*) as count FROM admin_notifications")->fetch();
        $notificationCount = $countResult['count'];
        echo "   📊 Total notifications: $notificationCount\n";

        $unreadResult = $pdo->query("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0")->fetch();
        $unreadCount = $unreadResult['count'];
        echo "   🔔 Unread notifications: $unreadCount\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 6: Test API Endpoints
echo "\n6️⃣ API Endpoint Testing:\n";

try {
    // Test unread count endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/notifications/unread-count');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['unread_count'])) {
            echo "   ✅ Unread count API: Working (Count: " . $data['unread_count'] . ")\n";
        } else {
            echo "   ❌ Unread count API: Invalid response format\n";
        }
    } else {
        echo "   ❌ Unread count API: HTTP $httpCode - " . $response . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Unread count API: Exception - " . $e->getMessage() . "\n";
}

// Summary and Recommendations
echo "\n📋 SUMMARY AND RECOMMENDATIONS:\n";

$allPassed = true;
foreach ($checks as $result) {
    if (!$result) $allPassed = false;
}
foreach ($cssChecks as $result) {
    if (!$result) $allPassed = false;
}
foreach ($jsChecks as $result) {
    if (!$result) $allPassed = false;
}
foreach ($routeChecks as $result) {
    if (!$result) $allPassed = false;
}

if ($allPassed) {
    echo "✅ All components appear to be properly implemented!\n";
    echo "🔍 If notifications still don't work, check:\n";
    echo "   - Browser console for JavaScript errors\n";
    echo "   - Network tab for API response errors\n";
    echo "   - Server error logs for backend issues\n";
    echo "   - User authentication and permissions\n";
} else {
    echo "❌ Some components need attention. Review the failed checks above.\n";
    echo "🛠️ Recommended fixes:\n";

    if (!$checks['Notification Button'] || !$checks['Notification Badge'] || !$checks['Notification Dropdown']) {
        echo "   1. Fix HTML structure in themes/admin/layouts/admin.php\n";
    }

    if (!$jsChecks['NotificationSystem Class'] || !$jsChecks['init() Method']) {
        echo "   2. Fix JavaScript implementation in themes/admin/assets/js/notification-system.js\n";
    }

    if (!$routeChecks['Unread Count Route'] || !$routeChecks['List Route']) {
        echo "   3. Add missing API routes in app/routes.php\n";
    }

    if (!$tableExists) {
        echo "   4. Run database migration for admin_notifications table\n";
    }
}

echo "\n🚀 NEXT STEPS:\n";
echo "1. Run: php seed_notifications.php to add test data\n";
echo "2. Check browser console for JavaScript errors\n";
echo "3. Test API endpoints manually in Postman\n";
echo "4. Verify user authentication is working\n";
echo "5. Clear browser cache and retry\n";