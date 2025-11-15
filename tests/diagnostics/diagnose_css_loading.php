<?php
/**
 * CSS Loading Diagnostic Script
 * Simple script to diagnose why CSS is not loading on website pages
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>CSS Loading Diagnostic</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }\n";
echo ".pass { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }\n";
echo ".fail { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }\n";
echo ".info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }\n";
echo ".code { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; overflow-x: auto; }\n";
echo "h1 { color: #333; }\n";
echo "h2 { color: #555; border-bottom: 2px solid #667eea; padding-bottom: 10px; }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";

echo "<h1>🔍 CSS Loading Diagnostic Report</h1>\n";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

// Load bootstrap
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>1. Basic Configuration Check</h2>\n";

// Check constants
$constants = ['BASE_PATH', 'APP_PATH', 'APP_BASE', 'APP_URL'];
foreach ($constants as $const) {
    if (defined($const)) {
        echo "<div class='pass'>✓ {$const} = " . constant($const) . "</div>\n";
    } else {
        echo "<div class='fail'>✗ {$const} is NOT defined</div>\n";
    }
}

echo "<h2>2. CSS Files Check</h2>\n";

$cssPath = BASE_PATH . '/themes/default/assets/css';
if (is_dir($cssPath)) {
    echo "<div class='pass'>✓ CSS directory exists: {$cssPath}</div>\n";

    $cssFiles = ['theme.css', 'footer.css', 'back-to-top.css', 'home.css', 'logo-enhanced.css'];
    foreach ($cssFiles as $file) {
        $fullPath = $cssPath . '/' . $file;
        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            echo "<div class='pass'>✓ {$file} exists (" . round($size/1024, 2) . " KB)</div>\n";
        } else {
            echo "<div class='fail'>✗ {$file} NOT FOUND</div>\n";
        }
    }
} else {
    echo "<div class='fail'>✗ CSS directory NOT found: {$cssPath}</div>\n";
}

echo "<h2>3. Theme Assets Proxy Check</h2>\n";

$proxyPath = BASE_PATH . '/public/theme-assets.php';
if (file_exists($proxyPath)) {
    echo "<div class='pass'>✓ Theme assets proxy exists</div>\n";

    // Check syntax
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($proxyPath) . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        echo "<div class='pass'>✓ Proxy has valid syntax</div>\n";
    } else {
        echo "<div class='fail'>✗ Proxy has syntax errors:<br>" . implode("<br>", $output) . "</div>\n";
    }
} else {
    echo "<div class='fail'>✗ Theme assets proxy NOT found: {$proxyPath}</div>\n";
}

echo "<h2>4. ThemeManager Service Check</h2>\n";

try {
    if (class_exists('App\\Services\\ThemeManager')) {
        echo "<div class='pass'>✓ ThemeManager class exists</div>\n";

        $themeManager = new \App\Services\ThemeManager();
        echo "<div class='pass'>✓ ThemeManager instantiated successfully</div>\n";

        // Test URL generation
        $testUrl = $themeManager->themeUrl('assets/css/theme.css');
        echo "<div class='info'>ℹ Generated URL: <code>{$testUrl}</code></div>\n";

        // Check if URL is accessible
        echo "<div class='info'>ℹ Test this URL in browser: <a href='{$testUrl}' target='_blank'>{$testUrl}</a></div>\n";

    } else {
        echo "<div class='fail'>✗ ThemeManager class NOT found</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='fail'>✗ ThemeManager error: " . htmlspecialchars($e->getMessage()) . "</div>\n";
}

echo "<h2>5. Header Template Check</h2>\n";

$headerPath = BASE_PATH . '/themes/default/views/partials/header.php';
if (file_exists($headerPath)) {
    echo "<div class='pass'>✓ Header template exists</div>\n";

    $headerContent = file_get_contents($headerPath);

    // Check for CSS loading code
    if (strpos($headerContent, 'stylesheet') !== false) {
        echo "<div class='pass'>✓ Header contains CSS link tags</div>\n";
    } else {
        echo "<div class='fail'>✗ Header does NOT contain CSS link tags</div>\n";
    }

    // Check for ThemeManager usage
    if (strpos($headerContent, 'ThemeManager') !== false || strpos($headerContent, 'themeUrl') !== false) {
        echo "<div class='pass'>✓ Header uses ThemeManager for CSS loading</div>\n";
    } else {
        echo "<div class='fail'>✗ Header does NOT use ThemeManager for CSS loading</div>\n";
    }

    // Show CSS loading snippet
    preg_match('/foreach.*cssFiles.*?endforeach;/s', $headerContent, $matches);
    if (!empty($matches)) {
        echo "<div class='info'>ℹ CSS Loading Code Found:</div>\n";
        echo "<pre class='code'>" . htmlspecialchars($matches[0]) . "</pre>\n";
    }

} else {
    echo "<div class='fail'>✗ Header template NOT found: {$headerPath}</div>\n";
}

echo "<h2>6. Browser Test URLs</h2>\n";

echo "<div class='info'>\n";
echo "<p><strong>Test these URLs directly in your browser:</strong></p>\n";
echo "<ol>\n";

$baseUrl = defined('APP_BASE') ? APP_BASE : '';
$testUrls = [
    "Direct CSS file" => $baseUrl . "/themes/default/assets/css/theme.css",
    "Via theme-assets proxy" => $baseUrl . "/public/theme-assets.php?path=default/assets/css/theme.css",
    "Homepage" => $baseUrl . "/",
    "Login page" => $baseUrl . "/login",
];

foreach ($testUrls as $label => $url) {
    echo "<li><strong>{$label}:</strong> <a href='{$url}' target='_blank'>{$url}</a></li>\n";
}

echo "</ol>\n";
echo "</div>\n";

echo "<h2>7. Common Issues & Solutions</h2>\n";

echo "<div class='info'>\n";
echo "<h3>If CSS is NOT loading, check these:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Browser Console (F12):</strong> Check for 404 errors or CORS errors</li>\n";
echo "<li><strong>Network Tab:</strong> See if CSS files are being requested</li>\n";
echo "<li><strong>View Page Source:</strong> Check if &lt;link&gt; tags are present in HTML</li>\n";
echo "<li><strong>File Permissions:</strong> Ensure web server can read CSS files</li>\n";
echo "<li><strong>htaccess:</strong> Verify URL rewriting is working</li>\n";
echo "<li><strong>APP_BASE:</strong> Must match your installation path (e.g., '/' or '/bishwo')</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<h2>8. Quick Fix: Direct CSS Include (Temporary)</h2>\n";

echo "<div class='info'>\n";
echo "<p>If theme-assets.php is not working, you can temporarily use direct CSS includes:</p>\n";
echo "<pre class='code'>";
echo htmlspecialchars('<!-- Add this to themes/default/views/partials/header.php -->
<link rel="stylesheet" href="/themes/default/assets/css/theme.css">
<link rel="stylesheet" href="/themes/default/assets/css/footer.css">
<link rel="stylesheet" href="/themes/default/assets/css/home.css">
<link rel="stylesheet" href="/themes/default/assets/css/logo-enhanced.css">
<link rel="stylesheet" href="/themes/default/assets/css/back-to-top.css">');
echo "</pre>\n";
echo "</div>\n";

echo "<h2>9. Test: Render Sample CSS URL</h2>\n";

try {
    if (class_exists('App\\Services\\ThemeManager')) {
        $tm = new \App\Services\ThemeManager();

        echo "<div class='info'>\n";
        echo "<p><strong>Generated CSS URLs:</strong></p>\n";
        echo "<ul>\n";

        $testFiles = ['theme.css', 'footer.css', 'home.css'];
        foreach ($testFiles as $file) {
            $url = $tm->themeUrl('assets/css/' . $file);
            echo "<li><code>{$file}</code> → <a href='{$url}' target='_blank'>{$url}</a></li>\n";
        }

        echo "</ul>\n";
        echo "</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='fail'>✗ Error generating URLs: " . htmlspecialchars($e->getMessage()) . "</div>\n";
}

echo "<h2>10. Recommended Actions</h2>\n";

$issues = [];

// Check for common problems
if (!file_exists(BASE_PATH . '/public/theme-assets.php')) {
    $issues[] = "Create public/theme-assets.php file";
}

if (!is_dir(BASE_PATH . '/themes/default/assets/css')) {
    $issues[] = "Create themes/default/assets/css directory";
}

if (!file_exists(BASE_PATH . '/.htaccess')) {
    $issues[] = "Create .htaccess file for URL rewriting";
}

if (!defined('APP_BASE') || APP_BASE === null) {
    $issues[] = "Define APP_BASE constant in app/Config/config.php";
}

if (count($issues) > 0) {
    echo "<div class='fail'>\n";
    echo "<h3>⚠️ Issues Found - Take these actions:</h3>\n";
    echo "<ol>\n";
    foreach ($issues as $issue) {
        echo "<li>{$issue}</li>\n";
    }
    echo "</ol>\n";
    echo "</div>\n";
} else {
    echo "<div class='pass'>\n";
    echo "<h3>✅ No major issues detected</h3>\n";
    echo "<p>CSS loading infrastructure appears to be configured correctly.</p>\n";
    echo "<p><strong>Next steps:</strong></p>\n";
    echo "<ol>\n";
    echo "<li>Clear browser cache and hard refresh (Ctrl+Shift+R or Cmd+Shift+R)</li>\n";
    echo "<li>Check browser console (F12) for any errors</li>\n";
    echo "<li>Verify file permissions on themes/default/assets/</li>\n";
    echo "<li>Test the URLs listed above in your browser</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
}

echo "<hr style='margin: 30px 0;'>\n";
echo "<p style='text-align: center; color: #666;'>\n";
echo "<a href='/'>← Back to Home</a> | \n";
echo "<a href='javascript:location.reload()'>🔄 Refresh Diagnostic</a>\n";
echo "</p>\n";

echo "</body>\n";
echo "</html>\n";
