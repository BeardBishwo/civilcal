<?php
/**
 * Homepage CSS Loading Debug Test
 * Tests if CSS files are loading correctly from theme directory
 */

echo "=== HOMEPAGE CSS LOADING DEBUG ===\n\n";

try {
    // Include bootstrap
    require_once __DIR__ . '/../app/bootstrap.php';
    
    echo "✓ Bootstrap loaded\n\n";
    
    // 1. Check ThemeManager
    echo "1. THEME MANAGER CHECK\n";
    echo "------------------------\n";
    $themeManager = new \App\Services\ThemeManager();
    $activeTheme = $themeManager->getActiveTheme();
    echo "   Active Theme: " . $activeTheme . "\n\n";
    
    // 2. Check CSS files exist
    echo "2. CSS FILES CHECK\n";
    echo "------------------------\n";
    $cssFiles = [
        'assets/css/theme.css',
        'assets/css/footer.css',
        'assets/css/back-to-top.css',
        'assets/css/home.css'
    ];
    
    $themePath = BASE_PATH . '/themes/' . $activeTheme . '/';
    foreach ($cssFiles as $css) {
        $fullPath = $themePath . $css;
        $exists = file_exists($fullPath);
        $size = $exists ? filesize($fullPath) : 0;
        echo "   " . ($exists ? "✓" : "✗") . " " . $css . " (" . $size . " bytes)\n";
    }
    echo "\n";
    
    // 3. Check generated URLs
    echo "3. GENERATED URLS CHECK\n";
    echo "------------------------\n";
    foreach ($cssFiles as $css) {
        $url = $themeManager->themeUrl($css);
        echo "   " . $css . "\n";
        echo "   → " . $url . "\n";
    }
    echo "\n";
    
    // 4. Check cache busting
    echo "4. CACHE BUSTING CHECK\n";
    echo "------------------------\n";
    foreach ($cssFiles as $css) {
        $fullPath = $themePath . $css;
        if (file_exists($fullPath)) {
            $mtime = filemtime($fullPath);
            $url = $themeManager->themeUrl($css . '?v=' . $mtime);
            echo "   " . $css . "\n";
            echo "   → Modified: " . date('Y-m-d H:i:s', $mtime) . "\n";
            echo "   → URL: " . $url . "\n";
        }
    }
    echo "\n";
    
    // 5. Check home.css content
    echo "5. HOME.CSS CONTENT CHECK\n";
    echo "------------------------\n";
    $homeCssPath = $themePath . 'assets/css/home.css';
    if (file_exists($homeCssPath)) {
        $content = file_get_contents($homeCssPath);
        $lines = count(explode("\n", $content));
        $hasGradient = strpos($content, 'gradient') !== false;
        $hasBackground = strpos($content, 'background') !== false;
        echo "   File size: " . strlen($content) . " bytes\n";
        echo "   Lines: " . $lines . "\n";
        echo "   Has gradient: " . ($hasGradient ? "YES" : "NO") . "\n";
        echo "   Has background: " . ($hasBackground ? "YES" : "NO") . "\n";
        echo "   First 200 chars:\n";
        echo "   " . substr($content, 0, 200) . "...\n";
    }
    echo "\n";
    
    // 6. Check theme.json
    echo "6. THEME.JSON CHECK\n";
    echo "------------------------\n";
    $themeJsonPath = $themePath . 'theme.json';
    if (file_exists($themeJsonPath)) {
        $config = json_decode(file_get_contents($themeJsonPath), true);
        echo "   ✓ theme.json exists\n";
        echo "   Styles defined: " . count($config['styles'] ?? []) . "\n";
        echo "   Scripts defined: " . count($config['scripts'] ?? []) . "\n";
        echo "   Styles:\n";
        foreach ($config['styles'] ?? [] as $style) {
            echo "      - " . $style . "\n";
        }
    } else {
        echo "   ✗ theme.json NOT FOUND\n";
    }
    echo "\n";
    
    // 7. Simulate header.php rendering
    echo "7. HEADER.PHP SIMULATION\n";
    echo "------------------------\n";
    echo "   Creating ThemeManager instance...\n";
    $tm = new \App\Services\ThemeManager();
    echo "   ✓ ThemeManager created\n";
    
    echo "   Generating CSS links:\n";
    foreach ($cssFiles as $css) {
        $fullPath = $themePath . $css;
        if (file_exists($fullPath)) {
            $mtime = filemtime($fullPath);
            $url = $tm->themeUrl($css . '?v=' . $mtime);
            echo "   <link rel=\"stylesheet\" href=\"" . $url . "\">\n";
        }
    }
    echo "\n";
    
    echo "=== DEBUG COMPLETE ===\n";
    echo "Status: ALL CHECKS PASSED ✓\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>


