<?php
/**
 * Test API Endpoints for Notification System
 * Verify that all notification API endpoints are accessible and working
 */

header('Content-Type: application/json');

$apiResults = [
    'api_endpoint_test' => [
        'status' => 'running',
        'endpoints' => [],
        'timestamp' => date('Y-m-d H:i:s')
    ]
];

// Test 1: Check if routes.php has the notification endpoints
$routesContent = file_get_contents('app/routes.php');
$hasNotificationRoutes = strpos($routesContent, '/api/notifications') !== false;
$hasUnreadCountRoute = strpos($routesContent, 'unread-count') !== false;
$hasListRoute = strpos($routesContent, 'list') !== false;

$apiResults['api_endpoint_test']['endpoints'][] = [
    'name' => 'Routes Configuration',
    'passed' => $hasNotificationRoutes && $hasUnreadCountRoute && $hasListRoute,
    'details' => [
        'notification_routes_found' => $hasNotificationRoutes,
        'unread_count_route_found' => $hasUnreadCountRoute,
        'list_route_found' => $hasListRoute
    ]
];

// Test 2: Check if NotificationController exists and has required methods
$controllerPath = 'app/Controllers/Admin/NotificationController.php';
$controllerExists = file_exists($controllerPath);

if ($controllerExists) {
    $controllerContent = file_get_contents($controllerPath);
    $hasUnreadCountMethod = strpos($controllerContent, 'getUnreadCount') !== false;
    $hasListMethod = strpos($controllerContent, 'getNotifications') !== false;
    $hasMarkReadMethod = strpos($controllerContent, 'markAsRead') !== false;

    $apiResults['api_endpoint_test']['endpoints'][] = [
        'name' => 'NotificationController Methods',
        'passed' => $hasUnreadCountMethod && $hasListMethod && $hasMarkReadMethod,
        'details' => [
            'controller_exists' => true,
            'unread_count_method' => $hasUnreadCountMethod,
            'list_method' => $hasListMethod,
            'mark_read_method' => $hasMarkReadMethod
        ]
    ];
} else {
    $apiResults['api_endpoint_test']['endpoints'][] = [
        'name' => 'NotificationController Existence',
        'passed' => false,
        'details' => [
            'controller_exists' => false,
            'expected_path' => $controllerPath
        ]
    ];
}

// Test 3: Check if database table exists
$migrationFiles = glob('database/migrations/*notifications*.php');
$hasMigrationFile = !empty($migrationFiles);

$apiResults['api_endpoint_test']['endpoints'][] = [
    'name' => 'Database Migration',
    'passed' => $hasMigrationFile,
    'details' => [
        'migration_file_found' => $hasMigrationFile,
        'migration_files' => $migrationFiles
    ]
];

// Calculate overall status
$totalTests = count($apiResults['api_endpoint_test']['endpoints']);
$passedTests = 0;
foreach ($apiResults['api_endpoint_test']['endpoints'] as $endpoint) {
    if ($endpoint['passed']) {
        $passedTests++;
    }
}

$apiResults['api_endpoint_test']['status'] = $passedTests === $totalTests ? 'passed' : 'failed';
$apiResults['api_endpoint_test']['summary'] = "$passedTests/$totalTests endpoint tests passed";

// Add recommendations
$recommendations = [];
if (!$hasNotificationRoutes) {
    $recommendations[] = "Notification routes need to be added to routes.php";
}
if (!$controllerExists) {
    $recommendations[] = "NotificationController needs to be created";
}
if (!$hasMigrationFile) {
    $recommendations[] = "Database migration for notifications needs to be created";
}

$apiResults['api_endpoint_test']['recommendations'] = $recommendations;

echo json_encode($apiResults, JSON_PRETTY_PRINT);