<?php
/**
 * Direct Homepage Test - Simulate HTTP request
 */

echo "=== DIRECT HOMEPAGE TEST ===\n\n";

// Set up environment
$_SERVER['REQUEST_URI'] = '/bishwo_calculator/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/bishwo_calculator/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// Capture output
ob_start();

try {
    // Include the main index.php
    require_once __DIR__ . '/../public/index.php';
    
    $output = ob_get_clean();
    
    echo "✓ Homepage rendered successfully\n";
    echo "  Output size: " . strlen($output) . " bytes\n\n";
    
    // Check for CSS links
    echo "CSS LINKS CHECK:\n";
    echo "-----------------------------\n";
    
    preg_match_all('/<link[^>]*href="([^"]*\.css[^"]*)"/', $output, $matches);
    
    if (count($matches[1]) > 0) {
        echo "✓ Found " . count($matches[1]) . " CSS links:\n";
        foreach ($matches[1] as $url) {
            echo "  → " . htmlspecialchars($url) . "\n";
        }
    } else {
        echo "✗ No CSS links found\n";
    }
    echo "\n";
    
    // Check for hero section
    echo "HERO SECTION CHECK:\n";
    echo "-----------------------------\n";
    
    if (strpos($output, 'Engineering Toolkit') !== false) {
        echo "✓ Hero title found\n";
    } else {
        echo "✗ Hero title NOT found\n";
    }
    
    if (strpos($output, 'Professional Calculators') !== false) {
        echo "✓ Hero subtitle found\n";
    } else {
        echo "✗ Hero subtitle NOT found\n";
    }
    
    if (preg_match('/class="[^"]*index-page[^"]*"/', $output)) {
        echo "✓ index-page class found\n";
    } else {
        echo "✗ index-page class NOT found\n";
    }
    echo "\n";
    
    // Check for JavaScript
    echo "JAVASCRIPT CHECK:\n";
    echo "-----------------------------\n";
    
    preg_match_all('/<script[^>]*src="([^"]*\.js[^"]*)"/', $output, $jsMatches);
    
    if (count($jsMatches[1]) > 0) {
        echo "✓ Found " . count($jsMatches[1]) . " JS files:\n";
        foreach ($jsMatches[1] as $url) {
            echo "  → " . htmlspecialchars($url) . "\n";
        }
    } else {
        echo "✗ No JS files found\n";
    }
    echo "\n";
    
    // Show first 500 chars of output
    echo "FIRST 500 CHARS OF OUTPUT:\n";
    echo "-----------------------------\n";
    echo substr($output, 0, 500) . "...\n\n";
    
    echo "=== TEST COMPLETE ===\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

?>
