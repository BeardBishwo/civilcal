<?php
/**
 * Web Application Testing - HTML Output and Functionality
 */

echo "=== WEB APPLICATION TESTING ===\n\n";

try {
    require_once 'app/bootstrap.php';
    
    // Test 1: Homepage rendering
    echo "=== TEST 1: HOMEPAGE RENDERING ===\n";
    
    ob_start();
    try {
        $homeController = new \App\Controllers\HomeController();
        $homeController->index();
        $output = ob_get_clean();
        
        echo "✅ Homepage: RENDERED\n";
        echo "✅ Output Length: " . strlen($output) . " characters\n";
        
        // Check for key elements
        $keyElements = [
            '<html' => 'HTML tag',
            '<head' => 'Head section',
            '<body' => 'Body tag',
            'Bishwo Calculator' => 'Application title',
            '<script' => 'JavaScript',
            '<link' => 'CSS links',
            'theme' => 'Theme integration'
        ];
        
        foreach ($keyElements as $pattern => $description) {
            if (strpos($output, $pattern) !== false) {
                echo "✅ {$description}: FOUND\n";
            } else {
                echo "⚠️  {$description}: NOT FOUND\n";
            }
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ Homepage Error: " . $e->getMessage() . "\n";
    }
    
    // Test 2: About page
    echo "\n=== TEST 2: ABOUT PAGE ===\n";
    
    ob_start();
    try {
        $homeController->about();
        $output = ob_get_clean();
        
        echo "✅ About Page: RENDERED\n";
        echo "✅ Output Length: " . strlen($output) . " characters\n";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ About Page Error: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Contact page
    echo "\n=== TEST 3: CONTACT PAGE ===\n";
    
    ob_start();
    try {
        $homeController->contact();
        $output = ob_get_clean();
        
        echo "✅ Contact Page: RENDERED\n";
        echo "✅ Output Length: " . strlen($output) . " characters\n";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ Contact Page Error: " . $e->getMessage() . "\n";
    }
    
    // Test 4: CSS Loading Test
    echo "\n=== TEST 4: CSS LOADING ===\n";
    
    $themeManager = new \App\Services\ThemeManager();
    $themeManager->loadThemeStyles();
    
    // Get the CSS files that should be loaded
    $metadata = $themeManager->getThemeMetadata();
    if (isset($metadata['config']['styles'])) {
        echo "✅ CSS Files Configured: " . count($metadata['config']['styles']) . "\n";
        
        foreach ($metadata['config']['styles'] as $cssFile) {
            $fullPath = "themes/procalculator/" . $cssFile;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "✅ {$cssFile}: LOADED ({$size} bytes)\n";
            } else {
                echo "❌ {$cssFile}: MISSING\n";
            }
        }
    } else {
        echo "⚠️  No CSS files configured\n";
    }
    
    // Test 5: Theme Configuration
    echo "\n=== TEST 5: THEME CONFIGURATION ===\n";
    
    $activeTheme = $themeManager->getActiveTheme();
    echo "✅ Active Theme: {$activeTheme}\n";
    
    $themeConfig = $themeManager->getThemeMetadata();
    echo "✅ Theme Name: " . ($themeConfig['name'] ?? 'Unknown') . "\n";
    echo "✅ Theme Version: " . ($themeConfig['version'] ?? 'Unknown') . "\n";
    echo "✅ Theme Author: " . ($themeConfig['author'] ?? 'Unknown') . "\n";
    
    // Test 6: Database Integration
    echo "\n=== TEST 6: DATABASE INTEGRATION ===\n";
    
    $db = get_db();
    if ($db) {
        // Test theme data in database
        $themeModel = new \App\Models\Theme();
        $activeThemeData = $themeModel->getActive();
        
        if ($activeThemeData) {
            echo "✅ Database Active Theme: " . $activeThemeData['display_name'] . "\n";
            echo "✅ Database Theme Status: " . $activeThemeData['status'] . "\n";
        } else {
            echo "⚠️  No active theme in database\n";
        }
        
        $allThemes = $themeModel->getAll();
        echo "✅ Database Total Themes: " . count($allThemes) . "\n";
    } else {
        echo "❌ Database connection failed\n";
    }
    
    // Test 7: File Structure Validation
    echo "\n=== TEST 7: FILE STRUCTURE ===\n";
    
    $requiredStructure = [
        'themes/procalculator/theme.json' => 'Theme config',
        'themes/procalculator/config.php' => 'Theme config PHP',
        'themes/procalculator/functions.php' => 'Theme functions',
        'themes/procalculator/assets/css/procalculator' => 'Main CSS',
        'themes/procalculator/assets/css/animations.css' => 'Animations CSS',
        'themes/procalculator/assets/css/responsive.css' => 'Responsive CSS',
        'themes/procalculator/assets/css/auth.css' => 'Auth CSS',
        'themes/procalculator/assets/css/components.css' => 'Components CSS',
        'themes/procalculator/assets/css/dashboard.css' => 'Dashboard CSS',
        'themes/procalculator/views/home/index.php' => 'Homepage view',
        'themes/procalculator/views/home/about.php' => 'About view',
        'themes/procalculator/views/home/contact.php' => 'Contact view'
    ];
    
    foreach ($requiredStructure as $file => $description) {
        if (file_exists($file)) {
            echo "✅ {$description}: EXISTS\n";
        } else {
            echo "❌ {$description}: MISSING\n";
        }
    }
    
    // Test 8: Performance Metrics
    echo "\n=== TEST 8: PERFORMANCE METRICS ===\n";
    
    $startTime = microtime(true);
    
    // Simulate multiple page loads
    for ($i = 0; $i < 3; $i++) {
        ob_start();
        $homeController->index();
        ob_end_clean();
    }
    
    $endTime = microtime(true);
    $totalTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    
    echo "✅ Load Time (3 pages): " . number_format($totalTime, 2) . " ms\n";
    echo "✅ Average Page Load: " . number_format($totalTime / 3, 2) . " ms\n";
    
    if ($totalTime < 1000) {
        echo "✅ Performance: EXCELLENT\n";
    } elseif ($totalTime < 3000) {
        echo "✅ Performance: GOOD\n";
    } else {
        echo "⚠️  Performance: NEEDS OPTIMIZATION\n";
    }
    
    echo "\n=== WEB APPLICATION TESTING COMPLETE ===\n";
    echo "Status: ALL WEB COMPONENTS TESTED\n";
    
} catch (Exception $e) {
    echo "❌ WEB APPLICATION ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>


