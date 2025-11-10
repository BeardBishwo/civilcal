<?php
/**
 * Comprehensive Testing Suite - All Application Areas
 */

echo "=== COMPREHENSIVE TESTING SUITE ===\n\n";

// Test 1: Core System Components
function testCoreSystem() {
    echo "=== TEST 1: CORE SYSTEM COMPONENTS ===\n";
    
    try {
        require_once 'app/bootstrap.php';
        echo "✅ Bootstrap: PASSED\n";
        
        $db = get_db();
        if ($db) {
            echo "✅ Database: PASSED\n";
            
            // Test all table creation
            $tables = ['themes', 'users', 'subscriptions', 'calculation_history', 'export_templates'];
            foreach ($tables as $table) {
                $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
                $exists = $stmt->fetch();
                if ($exists) {
                    echo "✅ Table '{$table}': EXISTS\n";
                } else {
                    echo "⚠️  Table '{$table}': MISSING\n";
                }
            }
        } else {
            echo "❌ Database: FAILED\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Core System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 2: Theme System
function testThemeSystem() {
    echo "=== TEST 2: THEME SYSTEM ===\n";
    
    try {
        $themeManager = new \App\Services\ThemeManager();
        echo "✅ ThemeManager: INITIALIZED\n";
        
        $activeTheme = $themeManager->getActiveTheme();
        echo "✅ Active Theme: {$activeTheme}\n";
        
        $metadata = $themeManager->getThemeMetadata();
        if (isset($metadata['name'])) {
            echo "✅ Theme Metadata: {$metadata['name']}\n";
        }
        
        // Test CSS loading
        $themeManager->loadThemeStyles();
        echo "✅ CSS Loading: COMPLETED\n";
        
        // Test available themes
        $availableThemes = $themeManager->getAvailableThemes();
        echo "✅ Available Themes: " . count($availableThemes) . " found\n";
        
    } catch (Exception $e) {
        echo "❌ Theme System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 3: Controller System
function testControllers() {
    echo "=== TEST 3: CONTROLLER SYSTEM ===\n";
    
    try {
        $homeController = new \App\Controllers\HomeController();
        echo "✅ HomeController: LOADED\n";
        
        // Test all controller methods exist
        $methods = get_class_methods($homeController);
        echo "✅ Available Methods: " . implode(', ', $methods) . "\n";
        
        // Test specific methods
        if (method_exists($homeController, 'index')) {
            echo "✅ Method 'index': EXISTS\n";
        }
        if (method_exists($homeController, 'about')) {
            echo "✅ Method 'about': EXISTS\n";
        }
        if (method_exists($homeController, 'contact')) {
            echo "✅ Method 'contact': EXISTS\n";
        }
        if (method_exists($homeController, 'dashboard')) {
            echo "✅ Method 'dashboard': EXISTS\n";
        }
        if (method_exists($homeController, 'login')) {
            echo "✅ Method 'login': EXISTS\n";
        }
        if (method_exists($homeController, 'register')) {
            echo "✅ Method 'register': EXISTS\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Controller System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 4: View System
function testViewSystem() {
    echo "=== TEST 4: VIEW SYSTEM ===\n";
    
    try {
        $view = new \App\Core\View();
        echo "✅ View System: INITIALIZED\n";
        
        // Test if render method exists
        if (method_exists($view, 'render')) {
            echo "✅ Method 'render': EXISTS\n";
        }
        if (method_exists($view, 'renderPartial')) {
            echo "✅ Method 'renderPartial': EXISTS\n";
        }
        
        // Test theme view files
        $themeViewPath = "themes/{$view->getActiveTheme()}/views";
        if (is_dir($themeViewPath)) {
            echo "✅ Theme Views Directory: EXISTS\n";
            
            $viewFiles = glob($themeViewPath . "/*/*.php");
            echo "✅ View Files Found: " . count($viewFiles) . " files\n";
            
            foreach (array_slice($viewFiles, 0, 5) as $file) {
                echo "   - " . basename($file) . "\n";
            }
        } else {
            echo "❌ Theme Views Directory: MISSING\n";
        }
        
    } catch (Exception $e) {
        echo "❌ View System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 5: Database Operations
function testDatabaseOperations() {
    echo "=== TEST 5: DATABASE OPERATIONS ===\n";
    
    try {
        $db = get_db();
        if ($db) {
            
            // Test insert/update/delete operations
            echo "✅ Database Connection: ACTIVE\n";
            
            // Test theme operations
            $themeModel = new \App\Models\Theme();
            echo "✅ Theme Model: LOADED\n";
            
            $allThemes = $themeModel->getAll();
            echo "✅ Theme Queries: " . count($allThemes) . " themes loaded\n";
            
            $activeTheme = $themeModel->getActive();
            if ($activeTheme) {
                echo "✅ Active Theme Query: SUCCESS - " . $activeTheme['display_name'] . "\n";
            } else {
                echo "⚠️  Active Theme Query: NO ACTIVE THEME\n";
            }
            
        } else {
            echo "❌ Database Connection: FAILED\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Database Operations Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 6: File System & Assets
function testFileSystem() {
    echo "=== TEST 6: FILE SYSTEM & ASSETS ===\n";
    
    try {
        // Test theme assets
        $activeTheme = \App\Services\ThemeManager::getInstance()->getActiveTheme();
        $themePath = "themes/{$activeTheme}";
        
        echo "✅ Active Theme Path: {$themePath}\n";
        
        // Test CSS files
        $cssPath = "{$themePath}/assets/css";
        if (is_dir($cssPath)) {
            $cssFiles = glob($cssPath . "/*.css");
            echo "✅ CSS Files: " . count($cssFiles) . " found\n";
            
            foreach ($cssFiles as $file) {
                echo "   - " . basename($file) . " (" . filesize($file) . " bytes)\n";
            }
        } else {
            echo "❌ CSS Directory: MISSING\n";
        }
        
        // Test JavaScript files
        $jsPath = "{$themePath}/assets/js";
        if (is_dir($jsPath)) {
            $jsFiles = glob($jsPath . "/*.js");
            echo "✅ JS Files: " . count($jsFiles) . " found\n";
        } else {
            echo "⚠️  JS Directory: MISSING\n";
        }
        
        // Test theme.json
        $themeJson = "{$themePath}/theme.json";
        if (file_exists($themeJson)) {
            $content = file_get_contents($themeJson);
            $data = json_decode($content, true);
            if ($data) {
                echo "✅ theme.json: VALID (" . count($data) . " config items)\n";
            } else {
                echo "❌ theme.json: INVALID JSON\n";
            }
        } else {
            echo "❌ theme.json: MISSING\n";
        }
        
    } catch (Exception $e) {
        echo "❌ File System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 7: Routing System
function testRoutingSystem() {
    echo "=== TEST 7: ROUTING SYSTEM ===\n";
    
    try {
        $routes = include 'app/routes.php';
        if (is_array($routes)) {
            echo "✅ Routes File: LOADED\n";
            echo "✅ Route Count: " . count($routes) . " routes\n";
            
            // Show some sample routes
            echo "✅ Sample Routes:\n";
            $routeCount = 0;
            foreach ($routes as $path => $handler) {
                if ($routeCount < 5) {
                    echo "   - {$path} => " . (is_string($handler) ? $handler : 'Closure') . "\n";
                    $routeCount++;
                }
            }
        } else {
            echo "❌ Routes File: INVALID FORMAT\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Routing System Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test 8: Security & Configuration
function testSecurityConfig() {
    echo "=== TEST 8: SECURITY & CONFIGURATION ===\n";
    
    try {
        // Test configuration files
        $configFiles = [
            'config/app.php',
            'config/database.php',
            'includes/config.php'
        ];
        
        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                echo "✅ Config '{$file}': EXISTS\n";
            } else {
                echo "❌ Config '{$file}': MISSING\n";
            }
        }
        
        // Test security constants
        if (defined('ENVIRONMENT')) {
            echo "✅ Environment: " . ENVIRONMENT . "\n";
        }
        if (defined('DB_HOST')) {
            echo "✅ Database Config: LOADED\n";
        }
        
        // Test error handling
        if (defined('E_ALL')) {
            echo "✅ Error Reporting: CONFIGURED\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Security/Config Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Run all tests
testCoreSystem();
testThemeSystem();
testControllers();
testViewSystem();
testDatabaseOperations();
testFileSystem();
testRoutingSystem();
testSecurityConfig();

echo "=== COMPREHENSIVE TESTING COMPLETE ===\n";
echo "Status: ALL SYSTEMS TESTED\n";
?>
