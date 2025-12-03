<?php
/**
 * Debug Notification UI Issues
 * Test to identify why notification UI is not visible
 */

require_once __DIR__ . '/../app/bootstrap.php';

echo "🔍 DEBUGGING NOTIFICATION UI ISSUES\n";
echo "===================================\n\n";

// Test 1: Check if the user is properly authenticated
echo "1️⃣ AUTHENTICATION CHECK\n";
echo "----------------------\n";

try {
    // Check session or auth status
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
    $isLoggedIn = isset($_SESSION['user_id']);

    echo "   User ID: " . ($userId ?? 'Not logged in') . "\n";
    echo "   Logged In: " . ($isLoggedIn ? 'Yes' : 'No') . "\n";

    if (!$isLoggedIn) {
        echo "   ❌ USER NOT LOGGED IN - This is the main issue!\n";
        echo "   🔑 Please login first to see notifications\n";
    } else {
        echo "   ✅ User is logged in with ID: $userId\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Session check error: " . $e->getMessage() . "\n";
}

// Test 2: Check HTML structure in admin.php
echo "\n2️⃣ HTML STRUCTURE VERIFICATION\n";
echo "----------------------------\n";

$adminContent = file_get_contents('themes/admin/layouts/admin.php');

// Check for critical notification elements
$htmlChecks = [
    'Notification Button' => strpos($adminContent, 'id="notificationToggle"') !== false,
    'Notification Badge' => strpos($adminContent, 'id="notificationBadge"') !== false,
    'Notification Dropdown' => strpos($adminContent, 'id="notificationDropdown"') !== false,
    'Notification List' => strpos($adminContent, 'class="notification-list"') !== false,
    'Notification Toast' => strpos($adminContent, 'id="notification-toast"') !== false,
    'Notification JS Include' => strpos($adminContent, 'notification-system.js') !== false,
];

foreach ($htmlChecks as $element => $exists) {
    echo "   " . ($exists ? "✅" : "❌") . " $element: " . ($exists ? "Found" : "Missing") . "\n";
}

// Test 3: Check CSS visibility issues
echo "\n3️⃣ CSS VISIBILITY CHECK\n";
echo "----------------------\n";

$cssChecks = [
    'Dropdown Display' => strpos($adminContent, '.notification-dropdown') !== false,
    'Dropdown Show Class' => strpos($adminContent, '.notification-dropdown.show') !== false,
    'Badge Styling' => strpos($adminContent, '.notification-badge') !== false,
    'Toast Styling' => strpos($adminContent, '.notification-toast') !== false,
];

foreach ($cssChecks as $style => $exists) {
    echo "   " . ($exists ? "✅" : "❌") . " $style: " . ($exists ? "Found" : "Missing") . "\n";
}

// Test 4: Check JavaScript initialization
echo "\n4️⃣ JAVASCRIPT INITIALIZATION CHECK\n";
echo "----------------------------------\n";

$jsContent = file_get_contents('themes/admin/assets/js/notification-system.js');

$jsChecks = [
    'DOMContentLoaded' => strpos($jsContent, 'DOMContentLoaded') !== false,
    'NotificationSystem Class' => strpos($jsContent, 'class NotificationSystem') !== false,
    'Global Instance' => strpos($jsContent, 'window.notificationSystem') !== false,
    'Button Click Handler' => strpos($jsContent, 'addEventListener(\'click\'') !== false,
    'Admin Page Check' => strpos($jsContent, 'includes(\'/admin\')') !== false,
    'Initialization Call' => strpos($jsContent, 'notificationSystem.init()') !== false,
];

foreach ($jsChecks as $check => $exists) {
    echo "   " . ($exists ? "✅" : "❌") . " $check: " . ($exists ? "Found" : "Missing") . "\n";
}

// Test 5: Check if user has notifications
echo "\n5️⃣ USER NOTIFICATION DATA CHECK\n";
echo "-------------------------------\n";

try {
    $notificationModel = new \App\Models\Notification();

    // Check for user 3 specifically
    $userId = 3;
    $unreadCount = $notificationModel->getCountByUser($userId);
    $notifications = $notificationModel->getUnreadByUser($userId, 5, 0);

    echo "   User ID: $userId\n";
    echo "   Unread Notifications: $unreadCount\n";
    echo "   Total Notifications: " . count($notifications) . "\n";

    if ($unreadCount > 0) {
        echo "   ✅ User has unread notifications - badge should show\n";
    } else {
        echo "   ❌ User has no unread notifications - badge will be empty\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 6: Check common issues
echo "\n6️⃣ COMMON ISSUE DIAGNOSTICS\n";
echo "---------------------------\n";

$commonIssues = [
    'JavaScript File Exists' => file_exists('themes/admin/assets/js/notification-system.js'),
    'JavaScript File Size' => filesize('themes/admin/assets/js/notification-system.js') > 1000,
    'Admin Layout Exists' => file_exists('themes/admin/layouts/admin.php'),
    'Admin Layout Size' => filesize('themes/admin/layouts/admin.php') > 10000,
];

foreach ($commonIssues as $issue => $status) {
    echo "   " . ($status ? "✅" : "❌") . " $issue: " . ($status ? "OK" : "Problem") . "\n";
}

// Summary and troubleshooting
echo "\n🎯 TROUBLESHOOTING SUMMARY\n";
echo "========================\n";

$allHtmlOk = true;
foreach ($htmlChecks as $result) if (!$result) $allHtmlOk = false;

$allCssOk = true;
foreach ($cssChecks as $result) if (!$result) $allCssOk = false;

$allJsOk = true;
foreach ($jsChecks as $result) if (!$result) $allJsOk = false;

if (!$allHtmlOk) {
    echo "❌ HTML STRUCTURE ISSUE\n";
    echo "   The notification button/dropdown HTML is missing or incorrect\n";
    echo "   Fix: Check themes/admin/layouts/admin.php for proper HTML structure\n";
}

if (!$allCssOk) {
    echo "❌ CSS STYLING ISSUE\n";
    echo "   Notification elements are not properly styled or hidden\n";
    echo "   Fix: Check CSS in themes/admin/layouts/admin.php\n";
}

if (!$allJsOk) {
    echo "❌ JAVASCRIPT ISSUE\n";
    echo "   Notification system JavaScript is not properly initialized\n";
    echo "   Fix: Check themes/admin/assets/js/notification-system.js\n";
}

echo "\n🔍 SPECIFIC UI DEBUGGING STEPS:\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Check Console tab for JavaScript errors\n";
echo "3. Check Elements tab - search for 'notificationToggle'\n";
echo "4. Check Network tab - look for API calls to /api/notifications/\n";
echo "5. Check if element exists: document.getElementById('notificationToggle')\n";
echo "6. Check if JS loaded: typeof NotificationSystem !== 'undefined'\n";

echo "\n💡 QUICK FIXES TO TRY:\n";
echo "1. Clear browser cache (Ctrl+Shift+Del)\n";
echo "2. Hard refresh (Ctrl+F5)\n";
echo "3. Try different browser (Chrome, Firefox)\n";
echo "4. Check if you're on an admin page (/admin/ in URL)\n";
echo "5. Verify you're logged in as user ID 3\n";

echo "\n🚀 IF STILL NOT WORKING:\n";
echo "The issue might be:\n";
echo "- JavaScript not loading (404 error)\n";
echo "- Element IDs don't match between HTML and JS\n";
echo "- CSS display:none hiding the button\n";
echo "- Authentication preventing UI rendering\n";
echo "- Browser extensions blocking scripts\n";

echo "\n📋 DEBUG COMMANDS TO RUN IN BROWSER CONSOLE:\n";
echo 'console.log("Button exists:", document.getElementById("notificationToggle"));';
echo 'console.log("JS loaded:", typeof NotificationSystem);';
echo 'console.log("Global instance:", typeof window.notificationSystem);';
echo 'console.log("Current user:", ' . ($userId ?? 'null') . ');';