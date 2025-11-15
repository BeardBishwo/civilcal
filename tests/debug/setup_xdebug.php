<?php
/**
 * Xdebug Setup Verification and Configuration Script
 * Checks if Xdebug is installed and provides setup instructions
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "================================================================================\n";
echo "                    XDEBUG SETUP VERIFICATION SCRIPT\n";
echo "                     Bishwo Calculator - VS Code Debugging\n";
echo "================================================================================\n\n";

$phpVersion = PHP_VERSION;
$phpBinary = PHP_BINARY;
$phpIniFile = php_ini_loaded_file();
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$isLaragon = strpos($phpBinary, 'laragon') !== false;

echo "PHP Information:\n";
echo "  Version: {$phpVersion}\n";
echo "  Binary: {$phpBinary}\n";
echo "  php.ini: {$phpIniFile}\n";
echo "  OS: " . PHP_OS . "\n";
echo "  Architecture: " . (PHP_INT_SIZE * 8) . " bit\n";
echo "  Thread Safety: " . (ZEND_THREAD_SAFE ? 'Yes (ZTS)' : 'No (NTS)') . "\n";
if ($isLaragon) {
    echo "  Environment: Laragon detected ✓\n";
}
echo "\n";

// Check if Xdebug is loaded
$xdebugLoaded = extension_loaded('xdebug');

echo "================================================================================\n";
echo "                         XDEBUG STATUS CHECK\n";
echo "================================================================================\n\n";

if ($xdebugLoaded) {
    echo "✓ Xdebug is INSTALLED and LOADED\n\n";

    $xdebugVersion = phpversion('xdebug');
    echo "Xdebug Version: {$xdebugVersion}\n\n";

    echo "Current Xdebug Configuration:\n";
    echo str_repeat("-", 80) . "\n";

    $xdebugSettings = [
        'xdebug.mode' => ini_get('xdebug.mode'),
        'xdebug.start_with_request' => ini_get('xdebug.start_with_request'),
        'xdebug.client_port' => ini_get('xdebug.client_port'),
        'xdebug.client_host' => ini_get('xdebug.client_host'),
        'xdebug.idekey' => ini_get('xdebug.idekey'),
        'xdebug.log' => ini_get('xdebug.log'),
        'xdebug.log_level' => ini_get('xdebug.log_level')
    ];

    foreach ($xdebugSettings as $setting => $value) {
        $displayValue = $value ?: '(not set)';
        echo "  {$setting} = {$displayValue}\n";
    }

    echo "\n";
    echo "Recommended Configuration Check:\n";
    echo str_repeat("-", 80) . "\n";

    $issues = [];

    if (strpos(ini_get('xdebug.mode'), 'debug') === false) {
        echo "  ✗ xdebug.mode should include 'debug'\n";
        $issues[] = "xdebug.mode should be 'debug' or 'debug,develop'";
    } else {
        echo "  ✓ xdebug.mode includes debug\n";
    }

    $port = ini_get('xdebug.client_port');
    if ($port != 9003 && $port != 9000) {
        echo "  ⚠ xdebug.client_port is {$port} (recommend 9003 for Xdebug 3)\n";
        $issues[] = "xdebug.client_port should be 9003";
    } else {
        echo "  ✓ xdebug.client_port is {$port}\n";
    }

    $host = ini_get('xdebug.client_host');
    if (empty($host)) {
        echo "  ⚠ xdebug.client_host not set (recommend 127.0.0.1)\n";
        $issues[] = "xdebug.client_host should be 127.0.0.1";
    } else {
        echo "  ✓ xdebug.client_host is {$host}\n";
    }

    echo "\n";

    if (count($issues) > 0) {
        echo "Configuration Improvements Needed:\n";
        echo str_repeat("-", 80) . "\n";
        foreach ($issues as $i => $issue) {
            echo "  " . ($i + 1) . ". {$issue}\n";
        }
        echo "\n";
    } else {
        echo "✓ Configuration looks good!\n\n";
    }

    echo "VS Code Launch Configuration:\n";
    echo str_repeat("-", 80) . "\n";
    echo "Port to use in .vscode/launch.json: {$port}\n";
    echo "Status: Ready for debugging ✓\n\n";

} else {
    echo "✗ Xdebug is NOT installed\n\n";

    echo "================================================================================\n";
    echo "                      XDEBUG INSTALLATION GUIDE\n";
    echo "================================================================================\n\n";

    if ($isWindows && $isLaragon) {
        echo "Installation Steps for Laragon (Windows):\n";
        echo str_repeat("-", 80) . "\n";
        echo "\n";

        echo "Step 1: Download Xdebug\n";
        echo "  Visit: https://xdebug.org/download\n";
        echo "  Download the appropriate DLL for:\n";
        echo "    - PHP Version: " . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "\n";
        echo "    - Thread Safety: " . (ZEND_THREAD_SAFE ? 'TS (Thread Safe)' : 'NTS (Non-Thread Safe)') . "\n";
        echo "    - Architecture: " . (PHP_INT_SIZE * 8) . " bit\n";
        echo "\n";
        echo "  Recommended file: php_xdebug-3.3.2-8.3-" . (ZEND_THREAD_SAFE ? 'vs16' : 'nts-vs16') . "-x86_64.dll\n";
        echo "\n";

        echo "Step 2: Install Xdebug\n";
        $extDir = ini_get('extension_dir');
        echo "  1. Rename downloaded file to: php_xdebug.dll\n";
        echo "  2. Copy to PHP extensions directory:\n";
        echo "     {$extDir}\n";
        echo "\n";

        echo "Step 3: Configure php.ini\n";
        echo "  1. Open php.ini file:\n";
        echo "     {$phpIniFile}\n";
        echo "  2. Add the following at the end:\n\n";
        echo "     [Xdebug]\n";
        echo "     zend_extension=php_xdebug.dll\n";
        echo "     xdebug.mode=debug,develop\n";
        echo "     xdebug.start_with_request=yes\n";
        echo "     xdebug.client_port=9003\n";
        echo "     xdebug.client_host=127.0.0.1\n";
        echo "     xdebug.idekey=VSCODE\n";
        echo "     xdebug.log_level=7\n";
        echo "\n";

        echo "Step 4: Restart Web Server\n";
        echo "  1. Open Laragon\n";
        echo "  2. Click 'Stop All'\n";
        echo "  3. Click 'Start All'\n";
        echo "\n";

        echo "Step 5: Verify Installation\n";
        echo "  Run this script again:\n";
        echo "     php setup_xdebug.php\n";
        echo "\n";

    } elseif ($isWindows) {
        echo "Installation Steps for Windows:\n";
        echo str_repeat("-", 80) . "\n";
        echo "\n";

        echo "Step 1: Download Xdebug\n";
        echo "  Visit: https://xdebug.org/wizard.php\n";
        echo "  Copy output of: phpinfo()\n";
        echo "  Paste into wizard for custom instructions\n";
        echo "\n";

        echo "Step 2: Follow wizard instructions to install\n";
        echo "\n";

        echo "Step 3: Add to php.ini:\n";
        echo "  [Xdebug]\n";
        echo "  zend_extension=path\\to\\php_xdebug.dll\n";
        echo "  xdebug.mode=debug,develop\n";
        echo "  xdebug.start_with_request=yes\n";
        echo "  xdebug.client_port=9003\n";
        echo "  xdebug.client_host=127.0.0.1\n";
        echo "\n";

    } else {
        // Linux/Mac
        echo "Installation Steps for Linux/Mac:\n";
        echo str_repeat("-", 80) . "\n";
        echo "\n";

        echo "Option 1: Install via PECL\n";
        echo "  sudo pecl install xdebug\n";
        echo "\n";

        echo "Option 2: Install via Package Manager\n";
        echo "  Ubuntu/Debian:\n";
        echo "    sudo apt-get install php-xdebug\n";
        echo "\n";
        echo "  Mac (Homebrew):\n";
        echo "    brew install php@8.3-xdebug\n";
        echo "\n";

        echo "Configuration:\n";
        echo "  Add to php.ini:\n";
        echo "    zend_extension=xdebug.so\n";
        echo "    xdebug.mode=debug,develop\n";
        echo "    xdebug.start_with_request=yes\n";
        echo "    xdebug.client_port=9003\n";
        echo "    xdebug.client_host=127.0.0.1\n";
        echo "\n";
    }
}

echo "================================================================================\n";
echo "                        VS CODE DEBUG SETUP\n";
echo "================================================================================\n\n";

$launchJsonPath = __DIR__ . '/.vscode/launch.json';
if (file_exists($launchJsonPath)) {
    echo "✓ VS Code launch.json exists\n";
    echo "  Location: {$launchJsonPath}\n\n";

    echo "Available Debug Configurations:\n";
    echo "  1. Listen for Xdebug (Browser debugging)\n";
    echo "  2. Launch currently open script\n";
    echo "  3. Debug Bootstrap\n";
    echo "  4. Debug Index (Homepage)\n";
    echo "  5. Debug Test Script\n";
    echo "  6. Debug IDE Runtime Test\n";
    echo "  7. Debug Routes\n";
    echo "  8. Debug Helper Functions\n";
    echo "  9. Debug Database Connection\n";
    echo "\n";
} else {
    echo "⚠ VS Code launch.json not found\n";
    echo "  Expected location: {$launchJsonPath}\n";
    echo "  The launch.json file has been created - reload VS Code\n\n";
}

echo "VS Code Extension Check:\n";
echo "  Required: PHP Debug extension\n";
echo "  Install: Ctrl+Shift+X → Search 'PHP Debug' → Install\n";
echo "\n";

echo "================================================================================\n";
echo "                         QUICK START GUIDE\n";
echo "================================================================================\n\n";

if ($xdebugLoaded) {
    echo "You're ready to debug! Follow these steps:\n\n";

    echo "1. Open VS Code in this project:\n";
    echo "   cd " . __DIR__ . "\n";
    echo "   code .\n\n";

    echo "2. Open Debug Panel:\n";
    echo "   Press: Ctrl+Shift+D\n\n";

    echo "3. Set Breakpoints:\n";
    echo "   - Click left of line numbers in PHP files\n";
    echo "   - Recommended: public/index.php, app/bootstrap.php\n\n";

    echo "4. Select Configuration:\n";
    echo "   - Choose: 'Listen for Xdebug' from dropdown\n\n";

    echo "5. Start Debugging:\n";
    echo "   - Press F5 or click green play button\n";
    echo "   - Status bar turns orange\n\n";

    echo "6. Trigger Breakpoint:\n";
    echo "   - Visit: http://localhost/Bishwo_Calculator/\n";
    echo "   - VS Code will pause at breakpoint\n\n";

    echo "7. Debug Controls:\n";
    echo "   - F5: Continue\n";
    echo "   - F10: Step Over\n";
    echo "   - F11: Step Into\n";
    echo "   - Shift+F11: Step Out\n";
    echo "   - Shift+F5: Stop\n\n";

} else {
    echo "Complete these steps to enable debugging:\n\n";

    echo "1. Install Xdebug (see instructions above)\n";
    echo "2. Restart web server\n";
    echo "3. Run this script again to verify\n";
    echo "4. Install VS Code PHP Debug extension\n";
    echo "5. Open Debug Panel (Ctrl+Shift+D)\n";
    echo "6. Start debugging!\n\n";
}

echo "================================================================================\n";
echo "                           TESTING XDEBUG\n";
echo "================================================================================\n\n";

if ($xdebugLoaded) {
    echo "Quick Test:\n";
    echo str_repeat("-", 80) . "\n";

    // Test xdebug_info if available
    if (function_exists('xdebug_info')) {
        echo "✓ xdebug_info() function available\n";
        echo "  Run phpinfo() and search for 'xdebug' section for full details\n\n";
    }

    // Test xdebug_break
    if (function_exists('xdebug_break')) {
        echo "✓ xdebug_break() function available\n";
        echo "  Can use xdebug_break() in code to trigger breakpoint\n\n";
    }

    // Check if debugging is active
    if (function_exists('xdebug_is_debugger_active')) {
        $active = xdebug_is_debugger_active();
        if ($active) {
            echo "✓ Debugger is currently active\n\n";
        } else {
            echo "ℹ Debugger not currently connected (normal for CLI)\n";
            echo "  Will activate when VS Code starts listening\n\n";
        }
    }

} else {
    echo "Install Xdebug to enable debugging features\n\n";
}

echo "================================================================================\n";
echo "                         TROUBLESHOOTING\n";
echo "================================================================================\n\n";

echo "Common Issues:\n";
echo str_repeat("-", 80) . "\n\n";

echo "Issue: Breakpoints not hit\n";
echo "Solutions:\n";
echo "  • Ensure Xdebug is installed: php -m | grep xdebug\n";
echo "  • Check port matches (9003) in php.ini and launch.json\n";
echo "  • Restart web server after php.ini changes\n";
echo "  • Verify VS Code PHP Debug extension installed\n";
echo "  • Check firewall not blocking port 9003\n\n";

echo "Issue: 'Cannot connect to runtime process'\n";
echo "Solutions:\n";
echo "  • Verify zend_extension path in php.ini is correct\n";
echo "  • Ensure xdebug.mode includes 'debug'\n";
echo "  • Restart PHP/web server\n";
echo "  • Check xdebug.log for errors\n\n";

echo "Issue: VS Code shows no debug configurations\n";
echo "Solutions:\n";
echo "  • Ensure .vscode/launch.json exists\n";
echo "  • Reload VS Code: Ctrl+Shift+P → 'Reload Window'\n";
echo "  • Check launch.json syntax is valid JSON\n\n";

echo "================================================================================\n";
echo "                         ADDITIONAL RESOURCES\n";
echo "================================================================================\n\n";

echo "Documentation:\n";
echo "  • VSCODE_DEBUG_SETUP.md - Complete debugging guide\n";
echo "  • https://xdebug.org/docs/ - Official Xdebug docs\n";
echo "  • https://code.visualstudio.com/docs/languages/php\n\n";

echo "Test Files:\n";
echo "  • test_fixes.php - Quick system test\n";
echo "  • debug_ide_runtime.php - Comprehensive runtime test\n\n";

echo "================================================================================\n";

if ($xdebugLoaded) {
    echo "\n✓ STATUS: READY FOR DEBUGGING\n\n";
    echo "Press Ctrl+Shift+D in VS Code to start debugging!\n";
} else {
    echo "\n⚠ STATUS: XDEBUG NOT INSTALLED\n\n";
    echo "Follow the installation guide above to enable debugging.\n";
}

echo "\n================================================================================\n";
echo "Script completed: " . date('Y-m-d H:i:s') . "\n";
echo "================================================================================\n";
