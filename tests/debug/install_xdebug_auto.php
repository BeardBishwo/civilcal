<?php
/**
 * Automated Xdebug Installation and Configuration Script
 * For Bishwo Calculator - VS Code Debug Panel Setup
 *
 * This script will:
 * 1. Detect your PHP configuration
 * 2. Guide you through Xdebug installation
 * 3. Automatically configure php.ini
 * 4. Verify the installation
 * 5. Test VS Code debug readiness
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colors for console output
$colors = [
    'reset' => "\033[0m",
    'red' => "\033[31m",
    'green' => "\033[32m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'magenta' => "\033[35m",
    'cyan' => "\033[36m",
];

function colorize($text, $color = 'reset') {
    global $colors;
    return $colors[$color] . $text . $colors['reset'];
}

function printHeader($text) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo colorize(strtoupper($text), 'cyan') . "\n";
    echo str_repeat("=", 80) . "\n\n";
}

function printSuccess($text) {
    echo colorize("✓ " . $text, 'green') . "\n";
}

function printError($text) {
    echo colorize("✗ " . $text, 'red') . "\n";
}

function printWarning($text) {
    echo colorize("⚠ " . $text, 'yellow') . "\n";
}

function printInfo($text) {
    echo colorize("ℹ " . $text, 'blue') . "\n";
}

// Clear screen
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    system('cls');
} else {
    system('clear');
}

printHeader("Automated Xdebug Installation & Configuration");

echo colorize("Bishwo Calculator - VS Code Debug Panel Setup\n", 'magenta');
echo "This script will help you install and configure Xdebug for debugging.\n\n";

// Gather system information
$phpVersion = PHP_VERSION;
$phpBinary = PHP_BINARY;
$phpIniFile = php_ini_loaded_file();
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$isLaragon = strpos($phpBinary, 'laragon') !== false;
$isThreadSafe = ZEND_THREAD_SAFE;
$architecture = PHP_INT_SIZE * 8;
$phpMajorMinor = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

printHeader("Step 1: System Detection");

printInfo("PHP Version: " . $phpVersion);
printInfo("PHP Binary: " . $phpBinary);
printInfo("php.ini Location: " . $phpIniFile);
printInfo("Operating System: " . PHP_OS);
printInfo("Architecture: " . $architecture . " bit");
printInfo("Thread Safety: " . ($isThreadSafe ? 'Yes (ZTS)' : 'No (NTS)'));

if ($isLaragon) {
    printSuccess("Laragon detected - Installation will be optimized for Laragon");
}

// Check if Xdebug is already installed
$xdebugInstalled = extension_loaded('xdebug');

echo "\n";

if ($xdebugInstalled) {
    printSuccess("Xdebug is already installed!");
    $xdebugVersion = phpversion('xdebug');
    printInfo("Xdebug Version: " . $xdebugVersion);

    echo "\nDo you want to reconfigure Xdebug? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $reconfigure = trim(strtolower($line));
    fclose($handle);

    if ($reconfigure !== 'y') {
        printInfo("Skipping to configuration check...");
        goto check_configuration;
    }
} else {
    printWarning("Xdebug is not installed");
}

printHeader("Step 2: Xdebug Installation Instructions");

if ($isWindows) {
    echo "For Windows (Laragon):\n\n";

    // Determine the correct DLL filename
    $phpVersionShort = str_replace('.', '', $phpMajorMinor);
    $tsNts = $isThreadSafe ? '' : '-nts';
    $xdebugDll = "php_xdebug-3.3.2-{$phpMajorMinor}{$tsNts}-vs16-x86_64.dll";

    printInfo("Required file: " . $xdebugDll);

    echo "\n";
    echo colorize("Option 1: Manual Download", 'yellow') . "\n";
    echo "1. Visit: https://xdebug.org/download\n";
    echo "2. Download: " . $xdebugDll . "\n";
    echo "3. Save to Downloads folder\n\n";

    echo colorize("Option 2: Direct Download Link", 'yellow') . "\n";
    $downloadUrl = "https://xdebug.org/files/php_xdebug-3.3.2-" . $phpMajorMinor . ($isThreadSafe ? '' : '-nts') . "-vs16-x86_64.dll";
    echo "Download URL:\n";
    echo colorize($downloadUrl, 'cyan') . "\n\n";

    $extDir = ini_get('extension_dir');
    $targetPath = $extDir . DIRECTORY_SEPARATOR . 'php_xdebug.dll';

    echo "After downloading, you need to:\n";
    echo "1. Rename the file to: " . colorize("php_xdebug.dll", 'green') . "\n";
    echo "2. Copy to: " . colorize($extDir, 'green') . "\n\n";

    echo "Press Enter after you've downloaded and placed the file, or type 'skip' to skip: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $continue = trim(strtolower($line));
    fclose($handle);

    if ($continue === 'skip') {
        printWarning("Skipping file placement verification");
    } else {
        // Check if file exists
        if (file_exists($targetPath)) {
            printSuccess("Xdebug DLL found at: " . $targetPath);
        } else {
            printError("Xdebug DLL not found at: " . $targetPath);
            printWarning("Please place the php_xdebug.dll file in the extensions directory");
            echo "\nContinue anyway? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $cont = trim(strtolower($line));
            fclose($handle);
            if ($cont !== 'y') {
                exit("Installation cancelled.\n");
            }
        }
    }
} else {
    // Linux/Mac
    echo "For Linux/Mac:\n\n";
    echo "Option 1: Install via PECL\n";
    echo colorize("  sudo pecl install xdebug", 'green') . "\n\n";

    echo "Option 2: Install via Package Manager\n";
    echo "  Ubuntu/Debian: " . colorize("sudo apt-get install php-xdebug", 'green') . "\n";
    echo "  Mac (Homebrew): " . colorize("brew install php@{$phpMajorMinor}-xdebug", 'green') . "\n\n";

    echo "Press Enter after installation: ";
    $handle = fopen("php://stdin", "r");
    fgets($handle);
    fclose($handle);
}

printHeader("Step 3: Configure php.ini");

if (!$phpIniFile) {
    printError("Could not locate php.ini file");
    exit("Please locate and edit your php.ini manually.\n");
}

printInfo("php.ini location: " . $phpIniFile);

// Read current php.ini
$phpIniContent = file_get_contents($phpIniFile);

// Check if Xdebug section already exists
$xdebugExists = strpos($phpIniContent, '[Xdebug]') !== false ||
                strpos($phpIniContent, 'zend_extension') !== false &&
                strpos($phpIniContent, 'xdebug') !== false;

if ($xdebugExists) {
    printWarning("Xdebug configuration already exists in php.ini");
    echo "\nDo you want to update the configuration? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $update = trim(strtolower($line));
    fclose($handle);

    if ($update !== 'y') {
        printInfo("Skipping php.ini modification");
        goto check_configuration;
    }
}

// Create Xdebug configuration
$xdebugConfig = "\n\n";
$xdebugConfig .= "; Xdebug Configuration for VS Code Debugging\n";
$xdebugConfig .= "; Added by automated installer: " . date('Y-m-d H:i:s') . "\n";
$xdebugConfig .= "[Xdebug]\n";

if ($isWindows) {
    $xdebugConfig .= "zend_extension=php_xdebug.dll\n";
} else {
    $xdebugConfig .= "zend_extension=xdebug.so\n";
}

$xdebugConfig .= "xdebug.mode=debug,develop\n";
$xdebugConfig .= "xdebug.start_with_request=yes\n";
$xdebugConfig .= "xdebug.client_port=9003\n";
$xdebugConfig .= "xdebug.client_host=127.0.0.1\n";
$xdebugConfig .= "xdebug.idekey=VSCODE\n";
$xdebugConfig .= "xdebug.log_level=7\n";
$xdebugConfig .= "\n";
$xdebugConfig .= "; Performance settings\n";
$xdebugConfig .= "xdebug.var_display_max_depth=10\n";
$xdebugConfig .= "xdebug.var_display_max_children=256\n";
$xdebugConfig .= "xdebug.var_display_max_data=1024\n";

// Create backup
$backupFile = $phpIniFile . '.backup.' . date('Y-m-d_H-i-s');

echo "\nConfiguration to be added:\n";
echo colorize($xdebugConfig, 'cyan');

echo "\nOptions:\n";
echo "1. Automatically add to php.ini (creates backup)\n";
echo "2. Show configuration to copy manually\n";
echo "3. Skip configuration\n";
echo "\nChoose option (1-3): ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$option = trim($line);
fclose($handle);

switch ($option) {
    case '1':
        // Create backup
        if (copy($phpIniFile, $backupFile)) {
            printSuccess("Backup created: " . $backupFile);
        } else {
            printWarning("Could not create backup");
        }

        // Append configuration
        if (file_put_contents($phpIniFile, $xdebugConfig, FILE_APPEND)) {
            printSuccess("Xdebug configuration added to php.ini");
        } else {
            printError("Failed to write to php.ini");
            printWarning("Please add the configuration manually");
            echo "\nConfiguration:\n";
            echo $xdebugConfig;
        }
        break;

    case '2':
        echo "\nCopy this configuration to your php.ini:\n";
        echo colorize($xdebugConfig, 'cyan');
        echo "\nPress Enter when done: ";
        $handle = fopen("php://stdin", "r");
        fgets($handle);
        fclose($handle);
        break;

    case '3':
        printInfo("Skipping php.ini configuration");
        break;

    default:
        printWarning("Invalid option, skipping configuration");
}

check_configuration:

printHeader("Step 4: Restart Web Server");

if ($isLaragon) {
    printInfo("You need to restart Laragon:");
    echo "1. Open Laragon\n";
    echo "2. Click 'Stop All'\n";
    echo "3. Click 'Start All'\n\n";
} else {
    printInfo("Restart your web server (Apache/Nginx)");
    echo "Restart command depends on your setup.\n\n";
}

echo "Press Enter after restarting the server: ";
$handle = fopen("php://stdin", "r");
fgets($handle);
fclose($handle);

printHeader("Step 5: Verify Installation");

// Re-check if Xdebug is loaded
printInfo("Checking if Xdebug is loaded...");

// Execute PHP command to check
$output = [];
exec('php -m', $output);
$xdebugInModules = in_array('xdebug', $output);

if ($xdebugInModules) {
    printSuccess("Xdebug is loaded!");

    // Get Xdebug info
    exec('php -r "echo phpversion(\'xdebug\');"', $versionOutput);
    $xdebugVersion = $versionOutput[0] ?? 'unknown';
    printInfo("Xdebug Version: " . $xdebugVersion);

    // Check configuration
    printInfo("\nChecking Xdebug configuration...");

    $settings = [
        'xdebug.mode' => null,
        'xdebug.client_port' => null,
        'xdebug.client_host' => null,
    ];

    foreach ($settings as $setting => $value) {
        exec("php -r \"echo ini_get('{$setting}');\"", $settingOutput);
        $settingValue = $settingOutput[0] ?? 'not set';

        if (!empty($settingValue)) {
            printSuccess("{$setting} = {$settingValue}");
        } else {
            printWarning("{$setting} is not set");
        }

        // Clear output array for next iteration
        $settingOutput = [];
    }

} else {
    printError("Xdebug is not loaded");
    printWarning("Please check:");
    echo "  • php_xdebug.dll is in the extensions directory\n";
    echo "  • php.ini has the correct zend_extension line\n";
    echo "  • Web server was restarted\n";
    echo "  • No typos in configuration\n\n";

    echo "Run: php -i | grep xdebug\n";
    echo "To see detailed information\n";
}

printHeader("Step 6: VS Code Setup");

$launchJsonPath = __DIR__ . '/.vscode/launch.json';

if (file_exists($launchJsonPath)) {
    printSuccess("VS Code launch.json exists");
} else {
    printWarning("VS Code launch.json not found");
    printInfo("The launch.json file should have been created earlier");
}

echo "\nVS Code Setup Checklist:\n";
echo "□ Install 'PHP Debug' extension (Ctrl+Shift+X)\n";
echo "□ Open Debug Panel (Ctrl+Shift+D)\n";
echo "□ Verify debug configurations are visible\n";
echo "□ Try setting a breakpoint (click left of line number)\n\n";

printHeader("Step 7: Test Debug Session");

echo "Let's test if debugging works:\n\n";

echo "1. Open VS Code in this project\n";
echo "2. Press Ctrl+Shift+D (Debug Panel)\n";
echo "3. Select 'Listen for Xdebug' from dropdown\n";
echo "4. Press F5 (Start Debugging)\n";
echo "5. Open: public/index.php\n";
echo "6. Click left of line 11 to set breakpoint\n";
echo "7. Visit: http://localhost/Bishwo_Calculator/\n";
echo "8. VS Code should pause at breakpoint\n\n";

echo "Did the debugging work? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$debugWorks = trim(strtolower($line));
fclose($handle);

if ($debugWorks === 'y') {
    printSuccess("Debugging is working!");
} else {
    printWarning("Debugging not working yet");
    echo "\nTroubleshooting:\n";
    echo "• Check if status bar turns orange when debugging starts\n";
    echo "• Verify port 9003 is not blocked by firewall\n";
    echo "• Check Output panel → Xdebug channel for errors\n";
    echo "• Ensure breakpoint is on executable line (not comment/whitespace)\n";
    echo "• Try clearing browser cache\n\n";

    echo "See detailed troubleshooting in: VSCODE_DEBUG_SETUP.md\n";
}

printHeader("Installation Complete!");

if ($xdebugInModules) {
    echo colorize("✓ Xdebug is installed and configured\n", 'green');
    echo colorize("✓ VS Code debug configurations ready\n", 'green');
    echo colorize("✓ Ready to debug!\n\n", 'green');

    echo "Quick Start:\n";
    echo "1. Press " . colorize("Ctrl+Shift+D", 'cyan') . " in VS Code\n";
    echo "2. Select configuration from dropdown\n";
    echo "3. Press " . colorize("F5", 'cyan') . " to start debugging\n";
    echo "4. Set breakpoints by clicking left of line numbers\n\n";
} else {
    echo colorize("⚠ Xdebug installation needs attention\n", 'yellow');
    echo "\nNext steps:\n";
    echo "1. Verify Xdebug DLL is in extensions directory\n";
    echo "2. Check php.ini configuration\n";
    echo "3. Restart web server\n";
    echo "4. Run: php setup_xdebug.php\n\n";
}

printHeader("Documentation & Resources");

echo "Available documentation:\n";
echo "• " . colorize("VSCODE_DEBUG_SETUP.md", 'cyan') . " - Complete debugging guide\n";
echo "• " . colorize("VSCODE_DEBUG_QUICK_START.txt", 'cyan') . " - Quick reference\n";
echo "• " . colorize("setup_xdebug.php", 'cyan') . " - Verification script\n\n";

echo "Test scripts:\n";
echo "• " . colorize("php test_fixes.php", 'cyan') . " - System verification\n";
echo "• " . colorize("php debug_ide_runtime.php", 'cyan') . " - Runtime analysis\n\n";

printHeader("Summary");

$summaryFile = __DIR__ . '/xdebug_installation_summary.txt';
$summary = "Xdebug Installation Summary\n";
$summary .= "Date: " . date('Y-m-d H:i:s') . "\n";
$summary .= "PHP Version: " . $phpVersion . "\n";
$summary .= "Xdebug Status: " . ($xdebugInModules ? 'INSTALLED' : 'NOT INSTALLED') . "\n";
$summary .= "php.ini: " . $phpIniFile . "\n";

if (isset($backupFile)) {
    $summary .= "Backup: " . $backupFile . "\n";
}

file_put_contents($summaryFile, $summary);
printInfo("Installation summary saved to: xdebug_installation_summary.txt");

echo "\n";
echo colorize("Thank you for using the automated Xdebug installer!", 'magenta') . "\n";
echo colorize("Happy debugging! 🐛🔍\n", 'green');
echo "\n";
echo str_repeat("=", 80) . "\n";
