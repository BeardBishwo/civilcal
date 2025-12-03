<?php
/**
 * Test Notification System Fix
 * Comprehensive verification of the notification system functionality
 */

// Set headers for JSON response
header('Content-Type: application/json');

// Test the notification system by checking all components
$results = [
    'notification_system_test' => [
        'status' => 'running',
        'tests' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ]
];

// Test 1: Check if notification button exists in admin.php
$adminContent = file_get_contents('themes/admin/layouts/admin.php');
$hasNotificationButton = strpos($adminContent, 'id="notificationToggle"') !== false;
$hasNotificationBadge = strpos($adminContent, 'id="notificationBadge"') !== false;
$hasNotificationDropdown = strpos($adminContent, 'id="notificationDropdown"') !== false;

$results['notification_system_test']['tests'][] = [
    'name' => 'Notification Button HTML Structure',
    'passed' => $hasNotificationButton,
    'details' => [
        'notification_button_found' => $hasNotificationButton,
        'notification_badge_found' => $hasNotificationBadge,
        'notification_dropdown_found' => $hasNotificationDropdown
    ]
];

// Test 2: Check if notification system JS is included
$hasNotificationJS = strpos($adminContent, 'notification-system.js') !== false;
$results['notification_system_test']['tests'][] = [
    'name' => 'Notification System JS Inclusion',
    'passed' => $hasNotificationJS,
    'details' => [
        'notification_js_included' => $hasNotificationJS
    ]
];

// Test 3: Check if notification CSS styles are present
$hasNotificationCSS = strpos($adminContent, '.notification-dropdown') !== false;
$hasNotificationItemCSS = strpos($adminContent, '.notification-item') !== false;
$results['notification_system_test']['tests'][] = [
    'name' => 'Notification CSS Styles',
    'passed' => $hasNotificationCSS && $hasNotificationItemCSS,
    'details' => [
        'notification_dropdown_css' => $hasNotificationCSS,
        'notification_item_css' => $hasNotificationItemCSS
    ]
];

// Test 4: Check notification system JS file exists and has proper structure
$notificationJSContent = file_get_contents('themes/admin/assets/js/notification-system.js');
$hasNotificationClass = strpos($notificationJSContent, 'class NotificationSystem') !== false;
$hasInitMethod = strpos($notificationJSContent, 'init()') !== false;
$hasClickHandler = strpos($notificationJSContent, 'addEventListener(\'click\'') !== false;
$hasPolling = strpos($notificationJSContent, 'setInterval') !== false;
$hasAPICalls = strpos($notificationJSContent, 'fetch(\'/api/notifications') !== false;

$results['notification_system_test']['tests'][] = [
    'name' => 'Notification System JS Structure',
    'passed' => $hasNotificationClass && $hasInitMethod && $hasClickHandler && $hasPolling && $hasAPICalls,
    'details' => [
        'notification_class_found' => $hasNotificationClass,
        'init_method_found' => $hasInitMethod,
        'click_handler_found' => $hasClickHandler,
        'polling_found' => $hasPolling,
        'api_calls_found' => $hasAPICalls
    ]
];

// Test 5: Check if notification system is properly initialized
$hasDOMContentLoaded = strpos($notificationJSContent, 'DOMContentLoaded') !== false;
$hasGlobalInstance = strpos($notificationJSContent, 'window.notificationSystem') !== false;
$results['notification_system_test']['tests'][] = [
    'name' => 'Notification System Initialization',
    'passed' => $hasDOMContentLoaded && $hasGlobalInstance,
    'details' => [
        'dom_content_loaded_handler' => $hasDOMContentLoaded,
        'global_instance_created' => $hasGlobalInstance
    ]
];

// Test 6: Check API endpoints exist in routes
$routesContent = file_get_contents('app/routes.php');
$hasNotificationRoutes = strpos($routesContent, '/api/notifications') !== false;
$hasUnreadCountRoute = strpos($routesContent, 'unread-count') !== false;
$hasListRoute = strpos($routesContent, 'list') !== false;

$results['notification_system_test']['tests'][] = [
    'name' => 'Notification API Routes',
    'passed' => $hasNotificationRoutes && $hasUnreadCountRoute && $hasListRoute,
    'details' => [
        'notification_routes_found' => $hasNotificationRoutes,
        'unread_count_route_found' => $hasUnreadCountRoute,
        'list_route_found' => $hasListRoute
    ]
];

// Test 7: Check database table exists
$databaseFiles = glob('database/migrations/*notifications*.php');
$hasMigrationFile = !empty($databaseFiles);
$results['notification_system_test']['tests'][] = [
    'name' => 'Notification Database Migration',
    'passed' => $hasMigrationFile,
    'details' => [
        'migration_file_found' => $hasMigrationFile,
        'migration_files' => $databaseFiles
    ]
];

// Calculate overall status
$totalTests = count($results['notification_system_test']['tests']);
$passedTests = 0;
foreach ($results['notification_system_test']['tests'] as $test) {
    if ($test['passed']) {
        $passedTests++;
    }
}

$results['notification_system_test']['status'] = $passedTests === $totalTests ? 'passed' : 'failed';
$results['notification_system_test']['summary'] = "$passedTests/$totalTests tests passed";

// Add recommendations
$recommendations = [];
if (!$hasNotificationButton) {
    $recommendations[] = "Notification button HTML structure needs to be added to admin.php";
}
if (!$hasNotificationJS) {
    $recommendations[] = "Notification system JS needs to be included in admin.php";
}
if (!$hasNotificationClass) {
    $recommendations[] = "NotificationSystem class needs to be properly defined";
}
if (!$hasNotificationRoutes) {
    $recommendations[] = "Notification API routes need to be added to routes.php";
}
if (!$hasMigrationFile) {
    $recommendations[] = "Notification database migration needs to be created";
}

$results['notification_system_test']['recommendations'] = $recommendations;

// Output results
echo json_encode($results, JSON_PRETTY_PRINT);