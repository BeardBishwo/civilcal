<?php
// Test to verify theme routing is working correctly
require_once 'includes/config.php';

// Simulate the view rendering process
$viewPath = BASE_PATH . '/themes/default/views/home/index.php';
$publicPath = BASE_PATH . '/public/index.php';

echo "=== THEME ROUTING TEST ===\n\n";

echo "Expected path (theme): $viewPath\n";
echo "File exists: " . (file_exists($viewPath) ? "YES ✓" : "NO ✗") . "\n\n";

echo "Fallback path (public): $publicPath\n";
echo "File exists: " . (file_exists($publicPath) ? "YES" : "NO") . "\n\n";

// Check if the view file has the premium content
if (file_exists($viewPath)) {
    $content = file_get_contents($viewPath);
    $hasPremiumContent = strpos($content, 'Premium Engineering Calculator') !== false;
    $hasGradients = strpos($content, 'linear-gradient') !== false;
    $hasInterFont = strpos($content, 'Inter') !== false;
    
    echo "Premium content check:\n";
    echo "- Premium title: " . ($hasPremiumContent ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
    echo "- Gradients: " . ($hasGradients ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
    echo "- Inter font: " . ($hasInterFont ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
    
    if ($hasPremiumContent && $hasGradients && $hasInterFont) {
        echo "\n🎉 SUCCESS: Premium theme file is properly configured!\n";
        echo "The homepage should now display the premium design instead of falling back to public/index.php\n";
    } else {
        echo "\n⚠️  WARNING: Premium content may be missing from the theme file\n";
    }
} else {
    echo "❌ ERROR: Theme file not found. The homepage will fall back to public/index.php\n";
}

echo "\n=== ROUTING FLOW ===\n";
echo "1. HomeController calls view('home/index', data)\n";
echo "2. View class looks for: themes/default/views/home/index.php\n";
echo "3. If found, renders premium design\n";
echo "4. If not found, would fall back to public/index.php\n";
?>
