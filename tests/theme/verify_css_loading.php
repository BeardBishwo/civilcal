<?php
/**
 * Verify CSS Loading - Check actual HTTP responses
 */

echo "=== CSS LOADING VERIFICATION ===\n\n";

// Test 1: Check if homepage returns HTML with CSS links
echo "TEST 1: Homepage HTML Check\n";
echo "-----------------------------\n";

$homepage = @file_get_contents('http://localhost/bishwo_calculator/');

if ($homepage === false) {
    echo "✗ FAILED: Cannot fetch homepage\n";
    echo "  Trying alternative URL...\n";
    $homepage = @file_get_contents('http://127.0.0.1/bishwo_calculator/');
    
    if ($homepage === false) {
        echo "✗ FAILED: Cannot fetch from any URL\n";
        exit(1);
    }
}

echo "✓ Homepage fetched successfully\n";
echo "  Size: " . strlen($homepage) . " bytes\n\n";

// Test 2: Check for CSS links in HTML
echo "TEST 2: CSS Links in HTML\n";
echo "-----------------------------\n";

$cssPatterns = [
    'theme.css' => '/themes\/default\/assets\/css\/theme\.css/',
    'footer.css' => '/themes\/default\/assets\/css\/footer\.css/',
    'back-to-top.css' => '/themes\/default\/assets\/css\/back-to-top\.css/',
    'home.css' => '/themes\/default\/assets\/css\/home\.css/'
];

foreach ($cssPatterns as $name => $pattern) {
    if (preg_match($pattern, $homepage)) {
        echo "✓ " . $name . " link found\n";
    } else {
        echo "✗ " . $name . " link NOT found\n";
    }
}
echo "\n";

// Test 3: Check for hero section content
echo "TEST 3: Hero Section Content\n";
echo "-----------------------------\n";

$heroPatterns = [
    'Engineering Toolkit' => '/Engineering Toolkit/',
    'Professional Calculators' => '/Professional Calculators/',
    'index-page class' => '/class="[^"]*index-page[^"]*"/',
];

foreach ($heroPatterns as $name => $pattern) {
    if (preg_match($pattern, $homepage)) {
        echo "✓ " . $name . " found\n";
    } else {
        echo "✗ " . $name . " NOT found\n";
    }
}
echo "\n";

// Test 4: Extract and verify CSS URLs
echo "TEST 4: CSS URLs Verification\n";
echo "-----------------------------\n";

preg_match_all('/<link[^>]*href="([^"]*css[^"]*)"/', $homepage, $matches);

if (count($matches[1]) > 0) {
    echo "Found " . count($matches[1]) . " CSS links:\n";
    foreach ($matches[1] as $url) {
        echo "  → " . $url . "\n";
        
        // Try to fetch each CSS file
        $cssContent = @file_get_contents($url);
        if ($cssContent !== false) {
            echo "    ✓ CSS file accessible (" . strlen($cssContent) . " bytes)\n";
        } else {
            echo "    ✗ CSS file NOT accessible\n";
        }
    }
} else {
    echo "✗ No CSS links found in HTML\n";
}
echo "\n";

// Test 5: Check for JavaScript
echo "TEST 5: JavaScript Loading\n";
echo "-----------------------------\n";

preg_match_all('/<script[^>]*src="([^"]*\.js[^"]*)"/', $homepage, $jsMatches);

if (count($jsMatches[1]) > 0) {
    echo "Found " . count($jsMatches[1]) . " JS files:\n";
    foreach ($jsMatches[1] as $url) {
        echo "  → " . $url . "\n";
    }
} else {
    echo "✗ No JS files found\n";
}
echo "\n";

// Test 6: Check for gradient/styling in home.css
echo "TEST 6: Home CSS Content\n";
echo "-----------------------------\n";

$homeCssUrl = null;
preg_match('/href="([^"]*home\.css[^"]*)"/', $homepage, $match);
if (isset($match[1])) {
    $homeCssUrl = $match[1];
    $homeCss = @file_get_contents($homeCssUrl);
    
    if ($homeCss !== false) {
        echo "✓ home.css loaded\n";
        
        $checks = [
            'gradient' => strpos($homeCss, 'gradient') !== false,
            'background' => strpos($homeCss, 'background') !== false,
            'index-page' => strpos($homeCss, 'index-page') !== false,
        ];
        
        foreach ($checks as $name => $found) {
            echo "  " . ($found ? "✓" : "✗") . " Contains: " . $name . "\n";
        }
    } else {
        echo "✗ home.css NOT accessible\n";
    }
}
echo "\n";

// Test 7: Summary
echo "=== SUMMARY ===\n";
echo "If all tests pass, CSS is loading correctly!\n";
echo "If tests fail, check:\n";
echo "  1. Server is running on localhost/bishwo_calculator\n";
echo "  2. Theme files exist in themes/default/assets/css/\n";
echo "  3. ThemeManager is generating correct URLs\n";

?>


