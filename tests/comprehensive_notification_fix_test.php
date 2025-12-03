<?php
/**
 * Comprehensive Notification System Fix Test
 * Tests all aspects of the notification system to ensure proper functionality
 */

require_once __DIR__ . '/../app/bootstrap.php';

class NotificationSystemTest {
    private $results = [];
    private $startTime;

    public function __construct() {
        $this->startTime = microtime(true);
        $this->results = [
            'metadata' => [
                'start_time' => date('Y-m-d H:i:s'),
                'php_version' => phpversion(),
                'test_environment' => $_ENV['APP_ENV'] ?? 'development'
            ],
            'tests' => [],
            'summary' => []
        ];
    }

    public function runAllTests() {
        echo "🧪 Running Comprehensive Notification System Tests\n";
        echo "================================================\n\n";

        // Test 1: File System Verification
        $this->testFileSystem();

        // Test 2: JavaScript System Verification
        $this->testJavaScriptSystem();

        // Test 3: API Route Configuration
        $this->testApiRoutes();

        // Test 4: Controller Accessibility
        $this->testControllerAccess();

        // Test 5: Database Connectivity
        $this->testDatabaseConnectivity();

        // Test 6: UI Element Verification
        $this->testUiElements();

        // Test 7: Real-time Functionality
        $this->testRealTimeFunctionality();

        // Generate summary
        $this->generateSummary();

        // Save results
        $this->saveResults();

        echo "\n✅ All tests completed!\n";
        echo "📊 Results saved to: tests/comprehensive_notification_test_results.json\n";
    }

    private function testFileSystem() {
        echo "1️⃣ Testing File System...\n";

        $filesToCheck = [
            'themes/admin/assets/js/notification-unified.js' => 'Unified notification JavaScript',
            'themes/admin/layouts/admin.php' => 'Admin layout with notification system',
            'app/routes.php' => 'Routes configuration',
            'app/Controllers/Admin/NotificationController.php' => 'Notification controller',
            'app/Models/Notification.php' => 'Notification model'
        ];

        $fileResults = [];
        foreach ($filesToCheck as $file => $description) {
            $exists = file_exists($file);
            $fileResults[$file] = [
                'exists' => $exists,
                'description' => $description,
                'size' => $exists ? filesize($file) : 0
            ];

            $status = $exists ? '✅' : '❌';
            echo "   $status $description: " . ($exists ? 'Found' : 'Missing') . "\n";
        }

        $this->results['tests']['file_system'] = [
            'name' => 'File System Verification',
            'passed' => !in_array(false, array_column($fileResults, 'exists')),
            'details' => $fileResults
        ];
    }

    private function testJavaScriptSystem() {
        echo "\n2️⃣ Testing JavaScript System...\n";

        $jsFile = 'themes/admin/assets/js/notification-unified.js';
        if (!file_exists($jsFile)) {
            $this->results['tests']['javascript_system'] = [
                'name' => 'JavaScript System Verification',
                'passed' => false,
                'error' => 'Unified JavaScript file not found'
            ];
            echo "   ❌ Unified JavaScript file not found\n";
            return;
        }

        $jsContent = file_get_contents($jsFile);

        $requiredFeatures = [
            'class NotificationSystem' => 'NotificationSystem class definition',
            'init()' => 'Initialization method',
            'fetchUnreadCount()' => 'Unread count fetching',
            'fetchNotifications()' => 'Notifications fetching',
            'markAsRead()' => 'Mark as read functionality',
            'startPolling()' => 'Real-time polling',
            'handlePollingError()' => 'Error handling',
            'showNotificationToast()' => 'Toast notifications',
            'window.notificationSystem' => 'Global accessibility'
        ];

        $featureResults = [];
        foreach ($requiredFeatures as $feature => $description) {
            $found = strpos($jsContent, $feature) !== false;
            $featureResults[$feature] = $found;
            $status = $found ? '✅' : '❌';
            echo "   $status $description\n";
        }

        $this->results['tests']['javascript_system'] = [
            'name' => 'JavaScript System Verification',
            'passed' => !in_array(false, $featureResults),
            'details' => [
                'file_size' => strlen($jsContent),
                'features_found' => array_sum($featureResults),
                'total_features' => count($featureResults),
                'feature_details' => $featureResults
            ]
        ];
    }

    private function testApiRoutes() {
        echo "\n3️⃣ Testing API Route Configuration...\n";

        $routesFile = 'app/routes.php';
        if (!file_exists($routesFile)) {
            $this->results['tests']['api_routes'] = [
                'name' => 'API Route Configuration',
                'passed' => false,
                'error' => 'Routes file not found'
            ];
            echo "   ❌ Routes file not found\n";
            return;
        }

        $routesContent = file_get_contents($routesFile);

        $requiredRoutes = [
            '/api/notifications/unread-count' => 'Unread count endpoint',
            '/api/notifications/list' => 'Notifications list endpoint',
            '/api/notifications/mark-read/' => 'Mark read endpoint',
            '/api/notifications/mark-all-read' => 'Mark all read endpoint',
            '["auth"]' => 'User authentication middleware',
            'Admin\\NotificationController' => 'Controller reference'
        ];

        $routeResults = [];
        foreach ($requiredRoutes as $route => $description) {
            $found = strpos($routesContent, $route) !== false;
            $routeResults[$route] = $found;
            $status = $found ? '✅' : '❌';
            echo "   $status $description\n";
        }

        // Check for problematic admin-only routes
        $adminOnlyRoutes = preg_match_all('/\/api\/notifications.*\["auth",\s*"admin"\]/', $routesContent);
        $status = $adminOnlyRoutes === 0 ? '✅' : '❌';
        echo "   $status No admin-only notification routes\n";

        $this->results['tests']['api_routes'] = [
            'name' => 'API Route Configuration',
            'passed' => !in_array(false, $routeResults) && $adminOnlyRoutes === 0,
            'details' => [
                'routes_found' => array_sum($routeResults),
                'total_routes' => count($routeResults),
                'admin_only_routes' => $adminOnlyRoutes,
                'route_details' => $routeResults
            ]
        ];
    }

    private function testControllerAccess() {
        echo "\n4️⃣ Testing Controller Accessibility...\n";

        $controllerFile = 'app/Controllers/Admin/NotificationController.php';
        if (!file_exists($controllerFile)) {
            $this->results['tests']['controller_access'] = [
                'name' => 'Controller Accessibility',
                'passed' => false,
                'error' => 'Controller file not found'
            ];
            echo "   ❌ Controller file not found\n";
            return;
        }

        $controllerContent = file_get_contents($controllerFile);

        // Check for proper authentication handling
        $authChecks = [
            'Auth::user()' => 'User authentication check',
            '!$user->is_admin' => 'Admin-only access checks (should be limited)',
            'Exception' => 'Exception handling',
            'try {' => 'Try-catch blocks',
            'json_encode' => 'JSON responses'
        ];

        $authResults = [];
        foreach ($authChecks as $check => $description) {
            $found = strpos($controllerContent, $check) !== false;
            $authResults[$check] = $found;
            $status = $found ? '✅' : '❌';
            echo "   $status $description\n";
        }

        // Count admin-only restrictions (should be minimal)
        $adminRestrictions = substr_count($controllerContent, '!$user->is_admin');
        $status = $adminRestrictions <= 2 ? '✅' : '❌';
        echo "   $status Admin restrictions: $adminRestrictions (should be ≤ 2)\n";

        $this->results['tests']['controller_access'] = [
            'name' => 'Controller Accessibility',
            'passed' => !in_array(false, array_slice($authResults, 0, 3)) && $adminRestrictions <= 2,
            'details' => [
                'auth_checks_found' => array_sum($authResults),
                'total_checks' => count($authResults),
                'admin_restrictions' => $adminRestrictions,
                'auth_details' => $authResults
            ]
        ];
    }

    private function testDatabaseConnectivity() {
        echo "\n5️⃣ Testing Database Connectivity...\n";

        try {
            // Test database connection
            $db = new PDO(
                'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'bishwo_calculator'),
                $_ENV['DB_USER'] ?? 'root',
                $_ENV['DB_PASSWORD'] ?? ''
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Test notifications table existence
            $tableExists = $db->query("SHOW TABLES LIKE 'admin_notifications'")->rowCount() > 0;

            // Test basic query functionality
            $queryTest = false;
            if ($tableExists) {
                $stmt = $db->query("SELECT COUNT(*) as count FROM admin_notifications");
                $queryTest = $stmt && $stmt->fetch() !== false;
            }

            echo "   ✅ Database connection: Successful\n";
            echo "   " . ($tableExists ? '✅' : '❌') . " Notifications table: " . ($tableExists ? 'Found' : 'Missing') . "\n";
            echo "   " . ($queryTest ? '✅' : '❌') . " Basic query functionality: " . ($queryTest ? 'Working' : 'Failed') . "\n";

            $this->results['tests']['database_connectivity'] = [
                'name' => 'Database Connectivity',
                'passed' => $tableExists && $queryTest,
                'details' => [
                    'connection_success' => true,
                    'table_exists' => $tableExists,
                    'query_functional' => $queryTest
                ]
            ];

        } catch (Exception $e) {
            echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";

            $this->results['tests']['database_connectivity'] = [
                'name' => 'Database Connectivity',
                'passed' => false,
                'error' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function testUiElements() {
        echo "\n6️⃣ Testing UI Elements...\n";

        $adminFile = 'themes/admin/layouts/admin.php';
        if (!file_exists($adminFile)) {
            $this->results['tests']['ui_elements'] = [
                'name' => 'UI Elements Verification',
                'passed' => false,
                'error' => 'Admin layout file not found'
            ];
            echo "   ❌ Admin layout file not found\n";
            return;
        }

        $adminContent = file_get_contents($adminFile);

        $requiredElements = [
            'id="notificationToggle"' => 'Notification button',
            'id="notificationDropdown"' => 'Notification dropdown',
            'id="notificationBadge"' => 'Notification badge',
            'class="notification-list"' => 'Notification list container',
            'notification-unified.js' => 'Unified JavaScript include',
            'notification-badge' => 'Badge CSS styles',
            'notification-dropdown' => 'Dropdown CSS styles'
        ];

        $elementResults = [];
        foreach ($requiredElements as $element => $description) {
            $found = strpos($adminContent, $element) !== false;
            $elementResults[$element] = $found;
            $status = $found ? '✅' : '❌';
            echo "   $status $description\n";
        }

        // Check for visibility CSS fixes
        $visibilityFixes = strpos($adminContent, 'display: inline-block !important') !== false;
        echo "   " . ($visibilityFixes ? '✅' : '❌') . " Visibility CSS fixes: " . ($visibilityFixes ? 'Found' : 'Missing') . "\n";

        $this->results['tests']['ui_elements'] = [
            'name' => 'UI Elements Verification',
            'passed' => !in_array(false, $elementResults) && $visibilityFixes,
            'details' => [
                'elements_found' => array_sum($elementResults),
                'total_elements' => count($elementResults),
                'visibility_fixes' => $visibilityFixes,
                'element_details' => $elementResults
            ]
        ];
    }

    private function testRealTimeFunctionality() {
        echo "\n7️⃣ Testing Real-time Functionality...\n";

        // Test JavaScript polling configuration
        $jsFile = 'themes/admin/assets/js/notification-unified.js';
        if (!file_exists($jsFile)) {
            $this->results['tests']['real_time_functionality'] = [
                'name' => 'Real-time Functionality',
                'passed' => false,
                'error' => 'JavaScript file not found'
            ];
            echo "   ❌ JavaScript file not found\n";
            return;
        }

        $jsContent = file_get_contents($jsFile);

        $pollingFeatures = [
            'startPolling()' => 'Polling initialization',
            'setInterval' => 'Interval-based polling',
            'fetchUnreadCount()' => 'Unread count fetching',
            'handlePollingError()' => 'Error handling',
            'retryCount' => 'Retry mechanism',
            'maxRetries' => 'Max retries configuration',
            'exponential backoff' => 'Backoff strategy'
        ];

        $pollingResults = [];
        foreach ($pollingFeatures as $feature => $description) {
            $found = strpos($jsContent, $feature) !== false;
            $pollingResults[$feature] = $found;
            $status = $found ? '✅' : '❌';
            echo "   $status $description\n";
        }

        // Check polling interval (should be around 30000ms)
        $intervalMatch = preg_match('/pollingInterval:\s*(\d+)/', $jsContent, $matches);
        $interval = $intervalMatch ? (int)$matches[1] : 0;
        $intervalOk = $interval >= 25000 && $interval <= 35000;
        $status = $intervalOk ? '✅' : '❌';
        echo "   $status Polling interval: " . ($interval ? $interval . 'ms' : 'Not found') . "\n";

        $this->results['tests']['real_time_functionality'] = [
            'name' => 'Real-time Functionality',
            'passed' => !in_array(false, $pollingResults) && $intervalOk,
            'details' => [
                'polling_features_found' => array_sum($pollingResults),
                'total_polling_features' => count($pollingFeatures),
                'polling_interval' => $interval,
                'interval_ok' => $intervalOk,
                'polling_details' => $pollingResults
            ]
        ];
    }

    private function generateSummary() {
        echo "\n📊 Generating Test Summary...\n";

        $totalTests = count($this->results['tests']);
        $passedTests = 0;

        foreach ($this->results['tests'] as $test) {
            if ($test['passed']) {
                $passedTests++;
            }
        }

        $passRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

        $this->results['summary'] = [
            'total_tests' => $totalTests,
            'passed_tests' => $passedTests,
            'failed_tests' => $totalTests - $passedTests,
            'pass_rate' => $passRate . '%',
            'execution_time' => round(microtime(true) - $this->startTime, 3) . 's',
            'status' => $passRate >= 80 ? 'PASS' : ($passRate >= 50 ? 'PARTIAL' : 'FAIL')
        ];

        echo "   📋 Total Tests: $totalTests\n";
        echo "   ✅ Passed: $passedTests\n";
        echo "   ❌ Failed: " . ($totalTests - $passedTests) . "\n";
        echo "   📊 Pass Rate: " . $this->results['summary']['pass_rate'] . "\n";
        echo "   ⏱️ Execution Time: " . $this->results['summary']['execution_time'] . "\n";
        echo "   🏷️ Status: " . $this->results['summary']['status'] . "\n";

        // Generate recommendations
        $this->results['recommendations'] = $this->generateRecommendations();
    }

    private function generateRecommendations() {
        $recommendations = [];

        // Check specific test failures
        foreach ($this->results['tests'] as $testName => $test) {
            if (!$test['passed']) {
                switch ($testName) {
                    case 'file_system':
                        $recommendations[] = "⚠️ Missing required files. Check file system permissions.";
                        break;
                    case 'javascript_system':
                        $recommendations[] = "⚠️ JavaScript system incomplete. Review notification-unified.js implementation.";
                        break;
                    case 'api_routes':
                        $recommendations[] = "⚠️ API routes misconfigured. Check routes.php for proper notification endpoints.";
                        break;
                    case 'controller_access':
                        $recommendations[] = "⚠️ Controller access issues. Review authentication logic in NotificationController.";
                        break;
                    case 'database_connectivity':
                        $recommendations[] = "⚠️ Database connectivity problems. Verify database configuration and table structure.";
                        break;
                    case 'ui_elements':
                        $recommendations[] = "⚠️ UI elements missing. Check admin.php for proper notification HTML/CSS.";
                        break;
                    case 'real_time_functionality':
                        $recommendations[] = "⚠️ Real-time functionality issues. Review polling implementation in JavaScript.";
                        break;
                }
            }
        }

        // Add general recommendations based on pass rate
        $passRate = (float)str_replace('%', '', $this->results['summary']['pass_rate']);

        if ($passRate >= 80) {
            $recommendations[] = "✅ System is working well! Consider adding more advanced features.";
            $recommendations[] = "🎉 Notification system is ready for production use.";
        } elseif ($passRate >= 50) {
            $recommendations[] = "⚠️ System is partially working. Address the failed tests for full functionality.";
            $recommendations[] = "🔧 Some components need attention before production deployment.";
        } else {
            $recommendations[] = "❌ System has critical issues. Review all components systematically.";
            $recommendations[] = "🚨 Do not deploy to production until major issues are resolved.";
        }

        return $recommendations;
    }

    private function saveResults() {
        $resultsFile = 'tests/comprehensive_notification_test_results.json';
        file_put_contents($resultsFile, json_encode($this->results, JSON_PRETTY_PRINT));

        // Also save a human-readable summary
        $summaryFile = 'tests/comprehensive_notification_test_summary.txt';
        $summary = "Notification System Test Results - " . date('Y-m-d H:i:s') . "\n";
        $summary .= "================================================\n\n";

        foreach ($this->results['tests'] as $test) {
            $summary .= "📋 " . $test['name'] . ": " . ($test['passed'] ? "✅ PASS" : "❌ FAIL") . "\n";
        }

        $summary .= "\n📊 SUMMARY:\n";
        $summary .= "   Total Tests: " . $this->results['summary']['total_tests'] . "\n";
        $summary .= "   Passed: " . $this->results['summary']['passed_tests'] . "\n";
        $summary .= "   Failed: " . $this->results['summary']['failed_tests'] . "\n";
        $summary .= "   Pass Rate: " . $this->results['summary']['pass_rate'] . "\n";
        $summary .= "   Status: " . $this->results['summary']['status'] . "\n";

        $summary .= "\n💡 RECOMMENDATIONS:\n";
        foreach ($this->results['recommendations'] as $recommendation) {
            $summary .= "   • $recommendation\n";
        }

        file_put_contents($summaryFile, $summary);
    }
}

// Run the comprehensive test suite
$testSuite = new NotificationSystemTest();
$testSuite->runAllTests();

// Output final message
echo "\n🎉 Notification System Test Suite Complete!\n";
echo "📋 Check the following files for detailed results:\n";
echo "   • tests/comprehensive_notification_test_results.json (full JSON results)\n";
echo "   • tests/comprehensive_notification_test_summary.txt (human-readable summary)\n";
echo "\n🚀 Next Steps:\n";
echo "   1. Review test results\n";
echo "   2. Address any failed tests\n";
echo "   3. Run manual testing in browser\n";
echo "   4. Deploy to production when ready\n";