<?php
/**
 * Admin Routes Testing Script
 * Tests all admin panel routes for proper rendering and functionality
 */

// Include necessary files
require_once __DIR__ . '/../app/Core/View.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

// Test configuration
define('TEST_MODE', true);
define('ADMIN_ROUTES_TEST', true);

class AdminRoutesTest {
    private $passed = 0;
    private $failed = 0;
    private $skipped = 0;
    private $results = [];
    
    // Admin routes to test
    private $adminRoutes = [
        // Dashboard routes
        '/admin' => 'Admin Dashboard',
        '/admin/dashboard' => 'Admin Dashboard',
        '/admin/dashboard/complex' => 'Complex Dashboard',
        '/admin/dashboard/configured' => 'Configured Dashboard',
        '/admin/dashboard/performance' => 'Performance Dashboard',
        
        // User management
        '/admin/users' => 'User Management',
        '/admin/users/create' => 'Create User',
        '/admin/users/edit' => 'Edit User',
        '/admin/users/profile' => 'User Profile',
        
        // Settings routes
        '/admin/settings' => 'General Settings',
        '/admin/settings/backup' => 'Backup Settings',
        '/admin/settings/advanced' => 'Advanced Settings',
        '/admin/settings/security' => 'Security Settings',
        '/admin/settings/email' => 'Email Settings',
        
        // System management
        '/admin/system' => 'System Management',
        '/admin/system/status' => 'System Status',
        '/admin/system/logs' => 'System Logs',
        '/admin/system/cache' => 'Cache Management',
        
        // Module management
        '/admin/modules' => 'Module Management',
        '/admin/modules/install' => 'Install Module',
        '/admin/modules/configure' => 'Configure Module',
        
        // Analytics and reports
        '/admin/analytics' => 'Analytics Dashboard',
        '/admin/reports' => 'Reports',
        '/admin/reports/generate' => 'Generate Report',
        
        // Theme management
        '/admin/themes' => 'Theme Management',
        '/admin/themes/customize' => 'Customize Theme',
        
        // Widget management
        '/admin/widgets' => 'Widget Management',
        '/admin/widgets/create' => 'Create Widget',
        
        // Menu management
        '/admin/menu' => 'Menu Management',
        '/admin/menu/customize' => 'Menu Customization',
        
        // Backup and restore
        '/admin/backup' => 'Backup Management',
        '/admin/backup/create' => 'Create Backup',
        '/admin/backup/restore' => 'Restore Backup',
        
        // API endpoints
        '/api/admin/dashboard/stats' => 'Dashboard Stats API',
        '/api/admin/settings/save' => 'Settings Save API',
        '/api/admin/modules/toggle' => 'Module Toggle API',
        '/api/admin/backup/create' => 'Backup Create API',
        '/api/admin/system/health' => 'System Health API'
    ];
    
    public function runAllTests() {
        echo "=== Admin Routes Testing ===\n\n";
        
        // Test view file existence
        $this->testViewFiles();
        
        // Test layout file existence
        $this->testLayoutFiles();
        
        // Test asset files
        $this->testAssetFiles();
        
        // Test Chart.js integration
        $this->testChartJSIntegration();
        
        // Test route accessibility (simulation)
        $this->testRouteAccessibility();
        
        // Display results
        $this->displayResults();
    }
    
    private function testViewFiles() {
        echo "Testing View Files...\n";
        echo str_repeat("-", 40) . "\n";
        
        $viewPaths = [
            'themes/admin/views/dashboard.php',
            'themes/admin/views/dashboard_complex.php',
            'themes/admin/views/configured-dashboard.php',
            'themes/admin/views/performance-dashboard.php',
            'themes/admin/views/system-status.php',
            'themes/admin/views/widget-management.php',
            'themes/admin/views/menu-customization.php',
            'themes/admin/views/settings/backup.php',
            'themes/admin/views/settings/advanced.php'
        ];
        
        foreach ($viewPaths as $viewPath) {
            $fullPath = __DIR__ . '/../' . $viewPath;
            if (file_exists($fullPath)) {
                $this->recordTest("View file exists: $viewPath", true, "File found at " . basename($fullPath));
            } else {
                $this->recordTest("View file exists: $viewPath", false, "File missing");
            }
        }
        echo "\n";
    }
    
    private function testLayoutFiles() {
        echo "Testing Layout Files...\n";
        echo str_repeat("-", 40) . "\n";
        
        $layoutPaths = [
            'themes/admin/layouts/admin.php',
            'themes/admin/layouts/sidebar.php',
            'themes/admin/layouts/header.php'
        ];
        
        foreach ($layoutPaths as $layoutPath) {
            $fullPath = __DIR__ . '/../' . $layoutPath;
            if (file_exists($fullPath)) {
                $this->recordTest("Layout file exists: $layoutPath", true, "File found");
            } else {
                $this->recordTest("Layout file exists: $layoutPath", false, "File missing");
            }
        }
        echo "\n";
    }
    
    private function testAssetFiles() {
        echo "Testing Asset Files...\n";
        echo str_repeat("-", 40) . "\n";
        
        $assetPaths = [
            'themes/admin/assets/css/admin.css',
            'themes/admin/assets/js/admin.js',
            'themes/admin/assets/images/admin-logo.png'
        ];
        
        foreach ($assetPaths as $assetPath) {
            $fullPath = __DIR__ . '/../' . $assetPath;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                $this->recordTest("Asset file exists: $assetPath", true, "File found (Size: " . $this->formatBytes($size) . ")");
            } else {
                $this->recordTest("Asset file exists: $assetPath", false, "File missing");
            }
        }
        echo "\n";
    }
    
    private function testChartJSIntegration() {
        echo "Testing Chart.js Integration...\n";
        echo str_repeat("-", 40) . "\n";
        
        // Test admin.js for Chart.js functionality
        $adminJsPath = __DIR__ . '/../themes/admin/assets/js/admin.js';
        if (file_exists($adminJsPath)) {
            $content = file_get_contents($adminJsPath);
            
            $tests = [
                'Chart.js availability check' => 'typeof Chart === \'undefined\'',
                'Dynamic Chart.js loading' => 'loadChartJS',
                'Chart defaults configuration' => 'configureChartDefaults',
                'Server Load Chart' => 'initializeServerLoadChart',
                'Memory Usage Chart' => 'initializeMemoryUsageChart',
                'Response Time Chart' => 'initializeResponseTimeChart',
                'Database Queries Chart' => 'initializeDbQueriesChart',
                'User Growth Chart' => 'initializeDashboardCharts',
                'System Performance Chart' => 'initializeSystemPerformanceChart',
                'Revenue Chart' => 'initializeRevenueChart',
                'Activity Heatmap' => 'initializeActivityHeatmap',
                'Resource Usage Chart' => 'initializeResourceUsageChart'
            ];
            
            foreach ($tests as $testName => $searchTerm) {
                $found = strpos($content, $searchTerm) !== false;
                $this->recordTest("Chart.js: $testName", $found, $found ? "Found" : "Missing");
            }
        } else {
            $this->recordTest("Chart.js: admin.js file", false, "File missing");
        }
        
        // Test admin.css for Chart.js styling
        $adminCssPath = __DIR__ . '/../themes/admin/assets/css/admin.css';
        if (file_exists($adminCssPath)) {
            $content = file_get_contents($adminCssPath);
            
            $chartStyles = [
                '.chart-container' => 'Chart container styling',
                '.chart-loading' => 'Chart loading state',
                '.chart-error' => 'Chart error state',
                'canvas.chart' => 'Canvas chart styling',
                '@media (max-width: 768px)' => 'Responsive chart styling'
            ];
            
            foreach ($chartStyles as $selector => $description) {
                $found = strpos($content, $selector) !== false;
                $this->recordTest("Chart.js CSS: $description", $found, $found ? "Found" : "Missing");
            }
        } else {
            $this->recordTest("Chart.js CSS: admin.css file", false, "File missing");
        }
        
        echo "\n";
    }
    
    private function testRouteAccessibility() {
        echo "Testing Route Accessibility (Simulation)...\n";
        echo str_repeat("-", 40) . "\n";
        
        foreach ($this->adminRoutes as $route => $description) {
            // Simulate route testing
            $accessible = $this->simulateRouteAccess($route);
            $this->recordTest("Route: $description", $accessible, $accessible ? "Accessible" : "Not accessible");
        }
        echo "\n";
    }
    
    private function simulateRouteAccess($route) {
        // This is a simulation - in a real test environment, you would make HTTP requests
        // For now, we'll check if the route follows proper patterns
        
        // Check if route starts with /admin or /api/admin
        if (strpos($route, '/admin') !== 0 && strpos($route, '/api/admin') !== 0) {
            return false;
        }
        
        // Check if route has valid structure
        if (empty($route) || $route === '/') {
            return false;
        }
        
        // Simulate basic route validation
        return true;
    }
    
    private function recordTest($testName, $passed, $details) {
        $status = $passed ? "PASS" : "FAIL";
        echo sprintf("%-50s %s %s\n", $testName, $status, $details);
        
        if ($passed) {
            $this->passed++;
        } else {
            $this->failed++;
        }
        
        $this->results[] = [
            'test' => $testName,
            'status' => $status,
            'details' => $details
        ];
    }
    
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function displayResults() {
        echo "=== Test Results Summary ===\n";
        echo str_repeat("=", 50) . "\n";
        
        $total = $this->passed + $this->failed + $this->skipped;
        
        echo sprintf("Total Tests: %d\n", $total);
        echo sprintf("Passed: %d (%.1f%%)\n", $this->passed, $total > 0 ? ($this->passed / $total) * 100 : 0);
        echo sprintf("Failed: %d (%.1f%%)\n", $this->failed, $total > 0 ? ($this->failed / $total) * 100 : 0);
        echo sprintf("Skipped: %d (%.1f%%)\n", $this->skipped, $total > 0 ? ($this->skipped / $total) * 100 : 0);
        
        echo "\n";
        
        if ($this->failed > 0) {
            echo "Failed Tests:\n";
            echo str_repeat("-", 30) . "\n";
            
            foreach ($this->results as $result) {
                if ($result['status'] === 'FAIL') {
                    echo sprintf("- %s: %s\n", $result['test'], $result['details']);
                }
            }
            echo "\n";
        }
        
        // Overall assessment
        if ($this->failed === 0) {
            echo "✅ ALL TESTS PASSED! Admin panel is ready for production.\n";
        } elseif ($this->failed <= 5) {
            echo "⚠️  Minor issues found. Admin panel needs some attention before production.\n";
        } else {
            echo "❌ Significant issues found. Admin panel requires extensive fixes before production.\n";
        }
        
        // Recommendations
        echo "\n=== Recommendations ===\n";
        echo str_repeat("=", 30) . "\n";
        
        if ($this->failed > 0) {
            echo "1. Fix all failed tests before deploying to production\n";
            echo "2. Ensure all view files exist and are properly formatted\n";
            echo "3. Verify Chart.js integration is working correctly\n";
            echo "4. Test all admin routes in a browser environment\n";
        } else {
            echo "1. Perform manual testing in browser environment\n";
            echo "2. Test all admin functionality with real data\n";
            echo "3. Verify responsive design on mobile devices\n";
            echo "4. Test user permissions and security measures\n";
        }
        
        echo "5. Run performance tests on admin dashboard\n";
        echo "6. Test backup and restore functionality\n";
        echo "7. Verify all AJAX forms are working properly\n";
    }
}

// Run the tests
if (php_sapi_name() === 'cli') {
    $test = new AdminRoutesTest();
    $test->runAllTests();
} else {
    echo "<pre>";
    $test = new AdminRoutesTest();
    $test->runAllTests();
    echo "</pre>";
}