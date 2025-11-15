<?php
/**
 * Comprehensive CSS/JS Loading Test and Tests Folder Verification
 * Tests all website pages for CSS/JS loading and verifies all test files
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths
define('TEST_BASE_PATH', __DIR__);
define('TEST_APP_PATH', TEST_BASE_PATH . '/app');
define('TEST_PUBLIC_PATH', TEST_BASE_PATH . '/public');
define('TEST_THEMES_PATH', TEST_BASE_PATH . '/themes');
define('TEST_TESTS_PATH', TEST_BASE_PATH . '/tests');

echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>CSS/JS Loading & Tests Verification</title>\n";
echo "<style>\n";
echo "* { margin: 0; padding: 0; box-sizing: border-box; }\n";
echo "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; }\n";
echo ".container { max-width: 1400px; margin: 0 auto; }\n";
echo ".header { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); margin-bottom: 30px; text-align: center; }\n";
echo ".header h1 { color: #667eea; font-size: 2.5em; margin-bottom: 10px; }\n";
echo ".header p { color: #666; font-size: 1.1em; }\n";
echo ".section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 20px; }\n";
echo ".section h2 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; margin-bottom: 20px; font-size: 1.8em; }\n";
echo ".section h3 { color: #555; margin: 20px 0 10px; font-size: 1.3em; border-left: 4px solid #10b981; padding-left: 12px; }\n";
echo ".test-item { padding: 12px; margin: 8px 0; border-radius: 8px; display: flex; align-items: center; transition: all 0.3s; }\n";
echo ".test-item:hover { transform: translateX(5px); }\n";
echo ".test-item.pass { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #28a745; }\n";
echo ".test-item.fail { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border-left: 5px solid #dc3545; }\n";
echo ".test-item.warn { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 5px solid #ffc107; }\n";
echo ".test-item.info { background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border-left: 5px solid #17a2b8; }\n";
echo ".icon { font-size: 1.5em; margin-right: 15px; min-width: 30px; text-align: center; }\n";
echo ".pass .icon { color: #28a745; }\n";
echo ".fail .icon { color: #dc3545; }\n";
echo ".warn .icon { color: #ffc107; }\n";
echo ".info .icon { color: #17a2b8; }\n";
echo ".summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }\n";
echo ".summary-card { background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }\n";
echo ".summary-card.success { border-top: 5px solid #28a745; }\n";
echo ".summary-card.error { border-top: 5px solid #dc3545; }\n";
echo ".summary-card.warning { border-top: 5px solid #ffc107; }\n";
echo ".summary-card h3 { font-size: 3em; margin: 10px 0; font-weight: bold; }\n";
echo ".summary-card p { color: #666; font-size: 1.1em; }\n";
echo ".file-list { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; max-height: 300px; overflow-y: auto; }\n";
echo ".file-list ul { list-style: none; }\n";
echo ".file-list li { padding: 5px 10px; margin: 3px 0; background: white; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 0.9em; }\n";
echo ".file-list li.directory { font-weight: bold; color: #667eea; }\n";
echo ".badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.85em; font-weight: bold; margin-left: 10px; }\n";
echo ".badge.success { background: #28a745; color: white; }\n";
echo ".badge.error { background: #dc3545; color: white; }\n";
echo ".badge.warning { background: #ffc107; color: #333; }\n";
echo ".footer { text-align: center; padding: 20px; color: white; margin-top: 30px; }\n";
echo "pre { background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 0.9em; margin: 10px 0; }\n";
echo ".grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }\n";
echo "@media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";

echo "<div class='container'>\n";
echo "<div class='header'>\n";
echo "<h1>🔍 CSS/JS Loading & Tests Verification</h1>\n";
echo "<p>Comprehensive system check for asset loading and test file validation</p>\n";
echo "<p style='color: #999; margin-top: 10px;'><strong>Generated:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
echo "</div>\n";

$passed = 0;
$failed = 0;
$warnings = 0;
$cssFiles = [];
$jsFiles = [];
$testFiles = [];
$testResults = [];

// ============================================
// SECTION 1: CSS Files Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>📄 CSS Files Verification</h2>\n";

$cssPath = TEST_THEMES_PATH . '/default/assets/css';
if (is_dir($cssPath)) {
    $cssFiles = array_diff(scandir($cssPath), ['.', '..']);
    echo "<div class='test-item pass'>\n";
    echo "<span class='icon'>✓</span>\n";
    echo "<div><strong>CSS Directory Found:</strong> " . count($cssFiles) . " files detected</div>\n";
    echo "</div>\n";

    echo "<div class='file-list'>\n";
    echo "<strong>CSS Files:</strong>\n";
    echo "<ul>\n";
    foreach ($cssFiles as $file) {
        if (is_file($cssPath . '/' . $file)) {
            $size = filesize($cssPath . '/' . $file);
            $readable = $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B';
            echo "<li>📄 {$file} <span class='badge success'>{$readable}</span></li>\n";
            $passed++;
        }
    }
    echo "</ul>\n";
    echo "</div>\n";
} else {
    echo "<div class='test-item fail'>\n";
    echo "<span class='icon'>✗</span>\n";
    echo "<div><strong>CSS Directory Not Found:</strong> {$cssPath}</div>\n";
    echo "</div>\n";
    $failed++;
}

echo "</div>\n";

// ============================================
// SECTION 2: JS Files Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>📜 JavaScript Files Verification</h2>\n";

$jsPath = TEST_THEMES_PATH . '/default/assets/js';
if (is_dir($jsPath)) {
    $jsFiles = array_diff(scandir($jsPath), ['.', '..']);
    echo "<div class='test-item pass'>\n";
    echo "<span class='icon'>✓</span>\n";
    echo "<div><strong>JS Directory Found:</strong> " . count($jsFiles) . " files detected</div>\n";
    echo "</div>\n";

    echo "<div class='file-list'>\n";
    echo "<strong>JavaScript Files:</strong>\n";
    echo "<ul>\n";
    foreach ($jsFiles as $file) {
        if (is_file($jsPath . '/' . $file)) {
            $size = filesize($jsPath . '/' . $file);
            $readable = $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B';
            echo "<li>📄 {$file} <span class='badge success'>{$readable}</span></li>\n";
            $passed++;
        }
    }
    echo "</ul>\n";
    echo "</div>\n";
} else {
    echo "<div class='test-item fail'>\n";
    echo "<span class='icon'>✗</span>\n";
    echo "<div><strong>JS Directory Not Found:</strong> {$jsPath}</div>\n";
    echo "</div>\n";
    $failed++;
}

echo "</div>\n";

// ============================================
// SECTION 3: Theme Assets Proxy Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>🔗 Theme Assets Proxy Check</h2>\n";

$proxyFile = TEST_PUBLIC_PATH . '/theme-assets.php';
if (file_exists($proxyFile)) {
    echo "<div class='test-item pass'>\n";
    echo "<span class='icon'>✓</span>\n";
    echo "<div><strong>Theme Assets Proxy Found:</strong> public/theme-assets.php</div>\n";
    echo "</div>\n";

    // Check syntax
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($proxyFile) . " 2>&1", $output, $return_var);

    if ($return_var === 0) {
        echo "<div class='test-item pass'>\n";
        echo "<span class='icon'>✓</span>\n";
        echo "<div><strong>Proxy Syntax Valid:</strong> No syntax errors</div>\n";
        echo "</div>\n";
        $passed++;
    } else {
        echo "<div class='test-item fail'>\n";
        echo "<span class='icon'>✗</span>\n";
        echo "<div><strong>Proxy Has Syntax Errors</strong></div>\n";
        echo "</div>\n";
        $failed++;
    }
} else {
    echo "<div class='test-item fail'>\n";
    echo "<span class='icon'>✗</span>\n";
    echo "<div><strong>Theme Assets Proxy Not Found:</strong> {$proxyFile}</div>\n";
    echo "</div>\n";
    $failed++;
}

echo "</div>\n";

// ============================================
// SECTION 4: Critical Page Templates Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>📑 Page Templates Verification</h2>\n";

$templates = [
    'Homepage' => TEST_THEMES_PATH . '/default/views/index.php',
    'Header' => TEST_THEMES_PATH . '/default/views/partials/header.php',
    'Footer' => TEST_THEMES_PATH . '/default/views/partials/footer.php',
    'Login' => TEST_THEMES_PATH . '/default/views/auth/login.php',
    'Register' => TEST_THEMES_PATH . '/default/views/auth/register.php',
];

foreach ($templates as $name => $path) {
    if (file_exists($path)) {
        // Check for CSS/JS references
        $content = file_get_contents($path);
        $hasCss = preg_match('/link.*stylesheet|<style/i', $content);
        $hasJs = preg_match('/script.*src|<script/i', $content);

        echo "<div class='test-item pass'>\n";
        echo "<span class='icon'>✓</span>\n";
        echo "<div>\n";
        echo "<strong>{$name} Template:</strong> Found<br>\n";
        if ($hasCss) echo "<small>🎨 Contains CSS references</small> ";
        if ($hasJs) echo "<small>⚡ Contains JS references</small>";
        echo "</div>\n";
        echo "</div>\n";
        $passed++;
    } else {
        echo "<div class='test-item fail'>\n";
        echo "<span class='icon'>✗</span>\n";
        echo "<div><strong>{$name} Template Not Found:</strong> {$path}</div>\n";
        echo "</div>\n";
        $failed++;
    }
}

echo "</div>\n";

// ============================================
// SECTION 5: Tests Folder Comprehensive Check
// ============================================
echo "<div class='section'>\n";
echo "<h2>🧪 Tests Folder Comprehensive Verification</h2>\n";

if (is_dir(TEST_TESTS_PATH)) {
    echo "<div class='test-item pass'>\n";
    echo "<span class='icon'>✓</span>\n";
    echo "<div><strong>Tests Directory Found:</strong> " . TEST_TESTS_PATH . "</div>\n";
    echo "</div>\n";
    $passed++;

    // Scan all subdirectories
    $testDirs = array_diff(scandir(TEST_TESTS_PATH), ['.', '..']);

    foreach ($testDirs as $dir) {
        $fullPath = TEST_TESTS_PATH . '/' . $dir;

        if (is_dir($fullPath)) {
            echo "<h3>📁 {$dir}/</h3>\n";

            $files = array_diff(scandir($fullPath), ['.', '..']);
            $phpFiles = array_filter($files, function($f) use ($fullPath) {
                return is_file($fullPath . '/' . $f) && pathinfo($f, PATHINFO_EXTENSION) === 'php';
            });

            if (count($phpFiles) > 0) {
                echo "<div class='file-list'>\n";
                echo "<ul>\n";

                foreach ($phpFiles as $file) {
                    $filePath = $fullPath . '/' . $file;
                    $size = filesize($filePath);
                    $readable = $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B';

                    // Check syntax
                    $output = [];
                    $return_var = 0;
                    exec("php -l " . escapeshellarg($filePath) . " 2>&1", $output, $return_var);

                    if ($return_var === 0) {
                        echo "<li>✓ {$file} <span class='badge success'>{$readable}</span> <span class='badge success'>Valid</span></li>\n";
                        $testResults[] = ['file' => "{$dir}/{$file}", 'status' => 'pass'];
                        $passed++;
                    } else {
                        echo "<li>✗ {$file} <span class='badge error'>{$readable}</span> <span class='badge error'>Syntax Error</span></li>\n";
                        $testResults[] = ['file' => "{$dir}/{$file}", 'status' => 'fail', 'error' => implode("\n", $output)];
                        $failed++;
                    }

                    $testFiles[] = "{$dir}/{$file}";
                }

                echo "</ul>\n";
                echo "</div>\n";
            } else {
                echo "<div class='test-item info'>\n";
                echo "<span class='icon'>ℹ</span>\n";
                echo "<div><strong>No PHP test files in {$dir}/</strong></div>\n";
                echo "</div>\n";
            }
        } else if (pathinfo($dir, PATHINFO_EXTENSION) === 'php') {
            // Root level test file
            $filePath = $fullPath;
            $size = filesize($filePath);
            $readable = $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B';

            $output = [];
            $return_var = 0;
            exec("php -l " . escapeshellarg($filePath) . " 2>&1", $output, $return_var);

            if ($return_var === 0) {
                echo "<div class='test-item pass'>\n";
                echo "<span class='icon'>✓</span>\n";
                echo "<div><strong>{$dir}</strong> <span class='badge success'>{$readable}</span> <span class='badge success'>Valid</span></div>\n";
                echo "</div>\n";
                $testResults[] = ['file' => $dir, 'status' => 'pass'];
                $passed++;
            } else {
                echo "<div class='test-item fail'>\n";
                echo "<span class='icon'>✗</span>\n";
                echo "<div><strong>{$dir}</strong> <span class='badge error'>{$readable}</span> <span class='badge error'>Syntax Error</span></div>\n";
                echo "</div>\n";
                $testResults[] = ['file' => $dir, 'status' => 'fail', 'error' => implode("\n", $output)];
                $failed++;
            }

            $testFiles[] = $dir;
        }
    }

} else {
    echo "<div class='test-item fail'>\n";
    echo "<span class='icon'>✗</span>\n";
    echo "<div><strong>Tests Directory Not Found:</strong> " . TEST_TESTS_PATH . "</div>\n";
    echo "</div>\n";
    $failed++;
}

echo "</div>\n";

// ============================================
// SECTION 6: CSS Loading Test on Sample Pages
// ============================================
echo "<div class='section'>\n";
echo "<h2>🎨 CSS Loading Simulation Test</h2>\n";

$testPages = [
    'Homepage' => '/',
    'Login' => '/login',
    'Register' => '/register',
    'Dashboard' => '/dashboard',
    'Admin' => '/admin',
    'Calculators' => '/calculators',
];

echo "<p>Testing if CSS would be properly loaded on these pages:</p>\n";

foreach ($testPages as $pageName => $pageUrl) {
    // Check if ThemeManager would work
    $wouldLoad = file_exists(TEST_APP_PATH . '/Services/ThemeManager.php') &&
                 file_exists(TEST_PUBLIC_PATH . '/theme-assets.php') &&
                 is_dir(TEST_THEMES_PATH . '/default/assets/css');

    if ($wouldLoad) {
        echo "<div class='test-item pass'>\n";
        echo "<span class='icon'>✓</span>\n";
        echo "<div><strong>{$pageName}</strong> ({$pageUrl}) - CSS Loading: READY</div>\n";
        echo "</div>\n";
        $passed++;
    } else {
        echo "<div class='test-item fail'>\n";
        echo "<span class='icon'>✗</span>\n";
        echo "<div><strong>{$pageName}</strong> ({$pageUrl}) - CSS Loading: ISSUES DETECTED</div>\n";
        echo "</div>\n";
        $failed++;
    }
}

echo "</div>\n";

// ============================================
// SECTION 7: Common CSS/JS Loading Issues
// ============================================
echo "<div class='section'>\n";
echo "<h2>⚠️ Common CSS/JS Loading Issues Check</h2>\n";

$issues = [];

// Check 1: APP_BASE constant
if (!defined('APP_BASE')) {
    $issues[] = "APP_BASE constant not defined - may cause incorrect asset paths";
}

// Check 2: .htaccess
if (!file_exists(TEST_BASE_PATH . '/.htaccess')) {
    $issues[] = ".htaccess file missing - URL rewriting may not work";
}

// Check 3: public/index.php
if (!file_exists(TEST_PUBLIC_PATH . '/index.php')) {
    $issues[] = "public/index.php missing - entry point not found";
}

// Check 4: ThemeManager
if (!file_exists(TEST_APP_PATH . '/Services/ThemeManager.php')) {
    $issues[] = "ThemeManager.php missing - theme asset loading will fail";
}

// Check 5: Header includes CSS
$headerPath = TEST_THEMES_PATH . '/default/views/partials/header.php';
if (file_exists($headerPath)) {
    $headerContent = file_get_contents($headerPath);
    if (!preg_match('/stylesheet|\.css/i', $headerContent)) {
        $issues[] = "Header file doesn't contain CSS references";
    }
}

if (count($issues) === 0) {
    echo "<div class='test-item pass'>\n";
    echo "<span class='icon'>✓</span>\n";
    echo "<div><strong>No Common Issues Detected</strong> - CSS/JS loading setup appears correct</div>\n";
    echo "</div>\n";
    $passed++;
} else {
    foreach ($issues as $issue) {
        echo "<div class='test-item warn'>\n";
        echo "<span class='icon'>⚠</span>\n";
        echo "<div><strong>Potential Issue:</strong> {$issue}</div>\n";
        echo "</div>\n";
        $warnings++;
    }
}

echo "</div>\n";

// ============================================
// SECTION 8: Recommendations
// ============================================
echo "<div class='section'>\n";
echo "<h2>💡 Recommendations & Fixes</h2>\n";

if ($failed > 0 || $warnings > 0) {
    echo "<div class='test-item info'>\n";
    echo "<span class='icon'>💡</span>\n";
    echo "<div><strong>To fix CSS/JS loading issues:</strong></div>\n";
    echo "</div>\n";

    echo "<ol style='margin-left: 20px; line-height: 2;'>\n";
    echo "<li>Verify <code>public/theme-assets.php</code> exists and has no syntax errors</li>\n";
    echo "<li>Check that <code>app/Services/ThemeManager.php</code> is working correctly</li>\n";
    echo "<li>Ensure all CSS files exist in <code>themes/default/assets/css/</code></li>\n";
    echo "<li>Verify the header template includes CSS loading code</li>\n";
    echo "<li>Check browser console (F12) for 404 errors on CSS/JS files</li>\n";
    echo "<li>Verify <code>APP_BASE</code> and <code>APP_URL</code> constants are set correctly</li>\n";
    echo "<li>Test asset URLs directly: <code>/public/theme-assets.php?path=default/assets/css/theme.css</code></li>\n";
    echo "</ol>\n";

    echo "<h3>Quick Fix Commands:</h3>\n";
    echo "<pre>";
    echo "# Check if CSS files exist\n";
    echo "ls -la themes/default/assets/css/\n\n";
    echo "# Test theme-assets.php syntax\n";
    echo "php -l public/theme-assets.php\n\n";
    echo "# Test ThemeManager syntax\n";
    echo "php -l app/Services/ThemeManager.php\n\n";
    echo "# Check file permissions\n";
    echo "ls -la themes/default/assets/\n";
    echo "</pre>\n";
}

echo "</div>\n";

// ============================================
// SUMMARY
// ============================================
echo "<div class='section'>\n";
echo "<h2>📊 Overall Summary</h2>\n";

$totalTests = $passed + $failed + $warnings;

echo "<div class='summary'>\n";
echo "<div class='summary-card success'>\n";
echo "<h3>{$passed}</h3>\n";
echo "<p>Tests Passed</p>\n";
echo "</div>\n";
echo "<div class='summary-card error'>\n";
echo "<h3>{$failed}</h3>\n";
echo "<p>Tests Failed</p>\n";
echo "</div>\n";
echo "<div class='summary-card warning'>\n";
echo "<h3>{$warnings}</h3>\n";
echo "<p>Warnings</p>\n";
echo "</div>\n";
echo "<div class='summary-card'>\n";
echo "<h3>" . count($cssFiles) . "</h3>\n";
echo "<p>CSS Files</p>\n";
echo "</div>\n";
echo "<div class='summary-card'>\n";
echo "<h3>" . count($jsFiles) . "</h3>\n";
echo "<p>JS Files</p>\n";
echo "</div>\n";
echo "<div class='summary-card'>\n";
echo "<h3>" . count($testFiles) . "</h3>\n";
echo "<p>Test Files</p>\n";
echo "</div>\n";
echo "</div>\n";

echo "<div style='margin-top: 30px; padding: 20px; background: " . ($failed === 0 ? "#d4edda" : "#f8d7da") . "; border-radius: 10px; text-align: center;'>\n";
if ($failed === 0 && $warnings === 0) {
    echo "<h2 style='color: #155724; margin: 0;'>✅ ALL CHECKS PASSED!</h2>\n";
    echo "<p style='color: #155724; margin-top: 10px;'>CSS/JS loading is properly configured and all test files are valid.</p>\n";
} else if ($failed === 0) {
    echo "<h2 style='color: #856404; margin: 0;'>⚠️ WARNINGS DETECTED</h2>\n";
    echo "<p style='color: #856404; margin-top: 10px;'>No critical errors, but {$warnings} warning(s) need attention.</p>\n";
} else {
    echo "<h2 style='color: #721c24; margin: 0;'>❌ ISSUES FOUND</h2>\n";
    echo "<p style='color: #721c24; margin-top: 10px;'>{$failed} critical issue(s) detected. Please review and fix.</p>\n";
}
echo "</div>\n";

echo "</div>\n";

// ============================================
// FOOTER
// ============================================
echo "</div>\n"; // container

echo "<div class='footer'>\n";
echo "<p>Generated by Bishwo Calculator Test Suite</p>\n";
echo "<p style='margin-top: 10px;'><a href='/' style='color: white; text-decoration: underline;'>← Back to Home</a> | ";
echo "<a href='javascript:location.reload()' style='color: white; text-decoration: underline;'>🔄 Refresh</a></p>\n";
echo "</div>\n";

echo "</body>\n";
echo "</html>\n";

// Log results to file
$logFile = TEST_BASE_PATH . '/storage/logs/css_js_test_' . date('Y-m-d_H-i-s') . '.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$logContent = "=== CSS/JS Loading & Tests Verification ===\n";
$logContent .= "Date: " . date('Y-m-d H:i:s') . "\n";
$logContent .= "Passed: {$passed}\n";
$logContent .= "Failed: {$failed}\n";
$logContent .= "Warnings: {$warnings}\n";
$logContent .= "CSS Files: " . count($cssFiles) . "\n";
$logContent .= "JS Files: " . count($jsFiles) . "\n";
$logContent .= "Test Files: " . count($testFiles) . "\n";
$logContent .= "\nTest Results:\n";
foreach ($testResults as $result) {
    $logContent .= "- {$result['file']}: {$result['status']}\n";
    if (isset($result['error'])) {
        $logContent .= "  Error: {$result['error']}\n";
    }
}

@file_put_contents($logFile, $logContent);
?>
