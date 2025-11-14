<?php
/**
 * Bishwo Calculator - Comprehensive Test Suite
 * CLI-based testing for all system components
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap the application
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/bootstrap.php';

class TestSuite
{
    private $results = [];
    private $passCount = 0;
    private $failCount = 0;
    private $warningCount = 0;

    public function __construct()
    {
        echo "\n🧪 Bishwo Calculator - Comprehensive Test Suite\n";
        echo "==============================================\n";
        echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
    }

    public function runTest($testName, $testFunction)
    {
        echo "Running: $testName... ";
        
        try {
            $result = $testFunction();
            
            if ($result['status'] === 'pass') {
                echo "✅ PASS\n";
                $this->results[] = ['name' => $testName, 'status' => 'pass', 'details' => $result['details'] ?? ''];
                $this->passCount++;
            } elseif ($result['status'] === 'warning') {
                echo "⚠️  WARNING\n";
                $this->results[] = ['name' => $testName, 'status' => 'warning', 'details' => $result['details'] ?? ''];
                $this->warningCount++;
            } else {
                echo "❌ FAIL\n";
                $this->results[] = ['name' => $testName, 'status' => 'fail', 'details' => $result['details'] ?? ''];
                $this->failCount++;
            }
            
            if (isset($result['details'])) {
                echo "  → " . $result['details'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ ERROR\n";
            $this->results[] = ['name' => $testName, 'status' => 'error', 'details' => $e->getMessage()];
            $this->failCount++;
        }
    }

    public function testThemeSystem()
    {
        // Test ThemeManager
        $this->runTest('ThemeManager Class Exists', function() {
            $themeManager = new App\Services\ThemeManager();
            return ['status' => 'pass', 'details' => 'ThemeManager initialized successfully'];
        });

        // Test Theme Configuration
        $this->runTest('Theme Configuration Loading', function() {
            $themeManager = new App\Services\ThemeManager();
            $config = $themeManager->getThemeMetadata();
            
            if (isset($config['name']) && isset($config['version'])) {
                return ['status' => 'pass', 'details' => 'Theme config loaded: ' . $config['name'] . ' v' . $config['version']];
            } else {
                return ['status' => 'fail', 'details' => 'Invalid theme configuration'];
            }
        });

        // Test Asset URLs
        $this->runTest('Asset URL Generation', function() {
            $themeManager = new App\Services\ThemeManager();
            $cssUrl = $themeManager->getThemeAsset('css/theme.css');
            
            if (strpos($cssUrl, 'themes/default') !== false) {
                return ['status' => 'pass', 'details' => 'CSS URL: ' . $cssUrl];
            } else {
                return ['status' => 'fail', 'details' => 'Invalid CSS URL format'];
            }
        });

        // Test Category Styles
        $this->runTest('Category-Specific Styling', function() {
            $themeManager = new App\Services\ThemeManager();
            $categories = ['civil', 'electrical', 'structural', 'hvac', 'fire', 'plumbing', 'mep', 'estimation', 'project-management', 'site', 'management'];
            
            $validCategories = 0;
            foreach ($categories as $category) {
                $style = $themeManager->getCategoryStyle($category);
                if (!empty($style)) {
                    $validCategories++;
                }
            }
            
            if ($validCategories === count($categories)) {
                return ['status' => 'pass', 'details' => "All $validCategories categories have styling"];
            } else {
                return ['status' => 'warning', 'details' => "Only $validCategories of " . count($categories) . " categories have styling"];
            }
        });
    }

    public function testFileSystem()
    {
        // Test Theme Directory Structure
        $this->runTest('Theme Directory Structure', function() {
            $requiredPaths = [
                'themes/default',
                'themes/default/views',
                'themes/default/views/layouts',
                'themes/default/views/partials',
                'themes/default/assets',
                'themes/default/assets/css',
                'themes/default/assets/js',
                'themes/default/assets/images'
            ];
            
            $existingPaths = 0;
            foreach ($requiredPaths as $path) {
                if (is_dir($path)) {
                    $existingPaths++;
                }
            }
            
            if ($existingPaths === count($requiredPaths)) {
                return ['status' => 'pass', 'details' => "All $existingPaths directories exist"];
            } else {
                return ['status' => 'warning', 'details' => "$existingPaths of " . count($requiredPaths) . " required directories exist"];
            }
        });

        // Test Asset Files
        $this->runTest('Asset Files Exist', function() {
            $assetFiles = [
                'themes/default/assets/css/theme.css',
                'themes/default/assets/css/responsive.css',
                'themes/default/assets/css/civil.css',
                'themes/default/assets/js/theme.js'
            ];
            
            $existingFiles = 0;
            foreach ($assetFiles as $file) {
                if (file_exists($file)) {
                    $existingFiles++;
                }
            }
            
            if ($existingFiles === count($assetFiles)) {
                return ['status' => 'pass', 'details' => "All $existingFiles asset files exist"];
            } else {
                return ['status' => 'warning', 'details' => "$existingFiles of " . count($assetFiles) . " asset files exist"];
            }
        });

        // Test View Templates
        $this->runTest('View Templates Exist', function() {
            $viewFiles = [
                'themes/default/views/layouts/main.php',
                'themes/default/views/layouts/admin.php',
                'themes/default/views/layouts/auth.php',
                'themes/default/views/partials/header.php',
                'themes/default/views/partials/footer.php'
            ];
            
            $existingFiles = 0;
            foreach ($viewFiles as $file) {
                if (file_exists($file)) {
                    $existingFiles++;
                }
            }
            
            if ($existingFiles === count($viewFiles)) {
                return ['status' => 'pass', 'details' => "All $existingFiles view templates exist"];
            } else {
                return ['status' => 'fail', 'details' => "$existingFiles of " . count($viewFiles) . " view templates exist"];
            }
        });

        // Test Configuration Files
        $this->runTest('Configuration Files', function() {
            $configFiles = [
                'themes/default/theme.json',
                'themes/default/helpers.php',
                'app/Services/ThemeManager.php'
            ];
            
            $existingFiles = 0;
            foreach ($configFiles as $file) {
                if (file_exists($file)) {
                    $existingFiles++;
                }
            }
            
            if ($existingFiles === count($configFiles)) {
                return ['status' => 'pass', 'details' => "All $existingFiles configuration files exist"];
            } else {
                return ['status' => 'fail', 'details' => "$existingFiles of " . count($configFiles) . " configuration files exist"];
            }
        });
    }

    public function testRoutingSystem()
    {
        // Test MVC Router
        $this->runTest('MVC Router Class', function() {
            if (class_exists('App\\Core\\Router')) {
                return ['status' => 'pass', 'details' => 'Router class exists'];
            } else {
                return ['status' => 'fail', 'details' => 'Router class not found'];
            }
        });

        // Test Controllers
        $this->runTest('Controller Classes', function() {
            $controllers = [
                'App\\Controllers\\CalculatorController',
                'App\\Controllers\\AuthController',
                'App\\Controllers\\UserController'
            ];
            
            $existingControllers = 0;
            foreach ($controllers as $controller) {
                if (class_exists($controller)) {
                    $existingControllers++;
                }
            }
            
            if ($existingControllers === count($controllers)) {
                return ['status' => 'pass', 'details' => "All $existingControllers controllers exist"];
            } else {
                return ['status' => 'warning', 'details' => "$existingControllers of " . count($controllers) . " controllers exist"];
            }
        });

        // Test .htaccess
        $this->runTest('.htaccess Configuration', function() {
            if (file_exists('.htaccess')) {
                $content = file_get_contents('.htaccess');
                if (strpos($content, 'RewriteEngine On') !== false && 
                    strpos($content, 'RewriteRule') !== false) {
                    return ['status' => 'pass', 'details' => '.htaccess properly configured'];
                } else {
                    return ['status' => 'fail', 'details' => '.htaccess exists but may be incomplete'];
                }
            } else {
                return ['status' => 'fail', 'details' => '.htaccess file not found'];
            }
        });

        // Test Public Index
        $this->runTest('Public Index File', function() {
            if (file_exists('public/index.php')) {
                return ['status' => 'pass', 'details' => 'public/index.php exists'];
            } else {
                return ['status' => 'fail', 'details' => 'public/index.php not found'];
            }
        });
    }

    public function testEngineeringCategories()
    {
        $categories = [
            'civil' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering', 
            'structural' => 'Structural Engineering',
            'hvac' => 'HVAC Engineering',
            'fire' => 'Fire Protection',
            'plumbing' => 'Plumbing',
            'mep' => 'MEP Integration',
            'estimation' => 'Estimation',
            'project-management' => 'Project Management',
            'site' => 'Site Management',
            'management' => 'Management'
        ];

        foreach ($categories as $category => $name) {
            $this->runTest("$name Module", function() use ($category) {
                $modulePath = "modules/$category";
                if (is_dir($modulePath)) {
                    $subdirs = array_diff(scandir($modulePath), ['.', '..']);
                    $subdirCount = count($subdirs);
                    return ['status' => 'pass', 'details' => "$subdirCount subdirectories found"];
                } else {
                    return ['status' => 'fail', 'details' => 'Module directory not found'];
                }
            });
        }
    }

    public function testResponsiveDesign()
    {
        $this->runTest('Responsive CSS Framework', function() {
            $cssFile = 'themes/default/assets/css/responsive.css';
            if (file_exists($cssFile)) {
                $content = file_get_contents($cssFile);
                if (strpos($content, '@media') !== false) {
                    return ['status' => 'pass', 'details' => 'Media queries found in responsive.css'];
                } else {
                    return ['status' => 'warning', 'details' => 'responsive.css exists but no media queries found'];
                }
            } else {
                return ['status' => 'warning', 'details' => 'responsive.css not found, checking main CSS'];
            }
        });

        $this->runTest('Mobile-First Meta Tag', function() {
            $mainLayout = 'themes/default/views/layouts/main.php';
            if (file_exists($mainLayout)) {
                $content = file_get_contents($mainLayout);
                if (strpos($content, 'width=device-width') !== false) {
                    return ['status' => 'pass', 'details' => 'Mobile viewport meta tag present'];
                } else {
                    return ['status' => 'warning', 'details' => 'Mobile viewport meta tag missing'];
                }
            } else {
                return ['status' => 'fail', 'details' => 'Main layout template not found'];
            }
        });
    }

    public function generateSummary()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 TEST SUMMARY\n";
        echo str_repeat("=", 50) . "\n";
        echo "✅ Passed:  " . $this->passCount . "\n";
        echo "⚠️  Warnings: " . $this->warningCount . "\n";
        echo "❌ Failed:   " . $this->failCount . "\n";
        echo "📊 Total:    " . count($this->results) . "\n\n";

        $totalScore = $this->passCount + $this->warningCount + $this->failCount;
        $successRate = $totalScore > 0 ? round(($this->passCount / $totalScore) * 100, 1) : 0;
        
        echo "🎯 Success Rate: $successRate%\n\n";

        if ($this->failCount === 0) {
            echo "🎉 ALL TESTS PASSED! Theme system is ready for deployment.\n";
        } elseif ($this->failCount <= 2) {
            echo "✅ MOSTLY SUCCESSFUL! Minor issues to address.\n";
        } else {
            echo "⚠️  ISSUES DETECTED! Please review failed tests.\n";
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🌐 ACCESS YOUR WEBSITE:\n";
        echo "http://localhost/bishwo_calculator/\n";
        echo "================================================\n";
    }

    public function runAllTests()
    {
        $this->testThemeSystem();
        echo "\n";
        
        $this->testFileSystem();
        echo "\n";
        
        $this->testRoutingSystem();
        echo "\n";
        
        $this->testEngineeringCategories();
        echo "\n";
        
        $this->testResponsiveDesign();
        echo "\n";
        
        $this->generateSummary();
    }
}

// Run the test suite
$testSuite = new TestSuite();
$testSuite->runAllTests();


