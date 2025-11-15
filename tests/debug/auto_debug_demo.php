<?php
/**
 * Automated Debugging Demonstration
 * This script simulates what happens during a VS Code debugging session
 *
 * Run: php auto_debug_demo.php
 */

// ANSI color codes for terminal
$colors = [
    'reset' => "\033[0m",
    'red' => "\033[31m",
    'green' => "\033[32m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'magenta' => "\033[35m",
    'cyan' => "\033[36m",
    'white' => "\033[37m",
    'bold' => "\033[1m",
    'bg_orange' => "\033[48;5;208m",
    'bg_blue' => "\033[44m",
];

function color($text, $color = 'reset') {
    global $colors;
    return $colors[$color] . $text . $colors['reset'];
}

function clearScreen() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

function sleep_ms($ms) {
    usleep($ms * 1000);
}

function printSeparator() {
    echo str_repeat("=", 80) . "\n";
}

function printStatusBar($debugging = false) {
    if ($debugging) {
        echo color("                                                                                ", 'bg_orange') . "\n";
        echo color(" STATUS: DEBUGGING ACTIVE - Press F5 to continue, F10 to step, Shift+F5 to stop ", 'bg_orange') . "\n";
        echo color("                                                                                ", 'bg_orange') . "\n";
    } else {
        echo color("                                                                                ", 'bg_blue') . "\n";
        echo color(" STATUS: Ready - Press F5 to start debugging                                    ", 'bg_blue') . "\n";
        echo color("                                                                                ", 'bg_blue') . "\n";
    }
}

function showDebugPanel($variables = [], $callStack = [], $breakpoints = []) {
    echo "\n";
    echo color("┌─────────────────────────────────────────────────────────────────────────────┐\n", 'cyan');
    echo color("│ DEBUG PANEL (Ctrl+Shift+D)                                                  │\n", 'cyan');
    echo color("├─────────────────────────────────────────────────────────────────────────────┤\n", 'cyan');

    // Variables Section
    echo color("│ ▼ VARIABLES                                                                 │\n", 'green');
    foreach ($variables as $name => $value) {
        $displayValue = is_array($value) ? json_encode($value) : (string)$value;
        if (strlen($displayValue) > 50) {
            $displayValue = substr($displayValue, 0, 50) . "...";
        }
        $line = sprintf("│   %s = %s", $name, $displayValue);
        $padding = 78 - mb_strlen($line);
        echo color($line . str_repeat(" ", $padding) . "│\n", 'white');
    }

    echo color("│                                                                             │\n", 'cyan');

    // Call Stack Section
    echo color("│ ▼ CALL STACK                                                                │\n", 'yellow');
    foreach ($callStack as $index => $call) {
        $line = sprintf("│   %d. %s", $index, $call);
        $padding = 78 - mb_strlen($line);
        echo color($line . str_repeat(" ", $padding) . "│\n", 'white');
    }

    echo color("│                                                                             │\n", 'cyan');

    // Breakpoints Section
    echo color("│ ▼ BREAKPOINTS                                                               │\n", 'red');
    foreach ($breakpoints as $bp) {
        $line = sprintf("│   ● %s", $bp);
        $padding = 78 - mb_strlen($line);
        echo color($line . str_repeat(" ", $padding) . "│\n", 'red');
    }

    echo color("└─────────────────────────────────────────────────────────────────────────────┘\n", 'cyan');
}

function showCodeWithBreakpoint($lineNum, $code, $currentLine = null) {
    echo "\n";
    echo color("┌─────────────────────────────────────────────────────────────────────────────┐\n", 'cyan');
    echo color("│ FILE: debug_test.php                                                        │\n", 'cyan');
    echo color("├─────────────────────────────────────────────────────────────────────────────┤\n", 'cyan');

    foreach ($code as $num => $line) {
        $hasBreakpoint = in_array($num, [21, 38, 59, 85]);
        $isCurrent = ($num === $currentLine);

        $lineStr = sprintf("%3d", $num);

        if ($isCurrent) {
            // Current line (yellow highlight)
            $prefix = $hasBreakpoint ? "│ ●" : "│  ";
            echo color($prefix . " " . $lineStr . " ▶ ", 'yellow');
            echo color(substr($line, 0, 60), 'bold');
            $padding = 60 - strlen(substr($line, 0, 60));
            echo str_repeat(" ", $padding > 0 ? $padding : 0);
            echo color("│\n", 'cyan');
        } elseif ($hasBreakpoint) {
            // Breakpoint line
            echo color("│ ● ", 'red') . $lineStr . "   " . substr($line, 0, 60);
            $padding = 60 - strlen(substr($line, 0, 60));
            echo str_repeat(" ", $padding > 0 ? $padding : 0);
            echo color("│\n", 'cyan');
        } else {
            // Normal line
            echo color("│   ", 'cyan') . $lineStr . "   " . substr($line, 0, 60);
            $padding = 60 - strlen(substr($line, 0, 60));
            echo str_repeat(" ", $padding > 0 ? $padding : 0);
            echo color("│\n", 'cyan');
        }
    }

    echo color("└─────────────────────────────────────────────────────────────────────────────┘\n", 'cyan');
}

// Main demonstration
clearScreen();
echo "\n";
printSeparator();
echo color("              AUTOMATED DEBUGGING DEMONSTRATION\n", 'bold');
echo color("                    VS Code Debug Panel\n", 'cyan');
printSeparator();
echo "\n";
echo "This simulation shows what happens when you debug in VS Code.\n";
echo "Watch as we step through code, inspect variables, and see the debug flow.\n\n";
echo color("Press Enter to start the demonstration...", 'yellow');
fgets(STDIN);

// Load bootstrap
require_once __DIR__ . '/app/bootstrap.php';

// Simulate debugging session
$code = [
    15 => "require_once __DIR__ . '/app/bootstrap.php';",
    16 => "",
    17 => "echo \"Starting debug test...\\n\\n\";",
    18 => "",
    19 => "// TEST 1: Basic Variables",
    20 => "echo \"TEST 1: Variables\\n\";",
    21 => "\$appName = \"Bishwo Calculator\";",
    22 => "\$version = \"1.0\";",
    23 => "\$isActive = true;",
    24 => "\$debugMode = true;",
    25 => "",
    26 => "echo \"App Name: \$appName\\n\";",
    27 => "",
    28 => "// TEST 2: Arrays",
    36 => "echo \"TEST 2: Configuration Array\\n\";",
    37 => "echo \"-------------------\\n\";",
    38 => "\$config = [",
    39 => "    'environment' => 'development',",
    40 => "    'database' => [",
    41 => "        'host' => 'localhost',",
];

// STEP 1: Show VS Code with breakpoints set
clearScreen();
printStatusBar(false);
echo "\n";
echo color("STEP 1: Breakpoints Set\n", 'bold');
echo "You clicked left of lines 21, 38 to set breakpoints (red dots)\n\n";

showCodeWithBreakpoint(21, $code, null);

echo "\n" . color("Press Enter to press F5 (Start Debugging)...", 'yellow');
fgets(STDIN);

// STEP 2: Debugging starts, paused at first breakpoint
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 2: Debugging Started - Paused at Line 21\n", 'bold');
echo "F5 was pressed. Code execution started and paused at first breakpoint!\n";
echo "Notice: Status bar is now ORANGE (debugging active)\n\n";

$variables = [];
$callStack = [
    "debug_test.php:21"
];
$breakpoints = [
    "debug_test.php:21",
    "debug_test.php:38"
];

showDebugPanel($variables, $callStack, $breakpoints);
showCodeWithBreakpoint(21, $code, 21);

echo "\n" . color("Press Enter to press F10 (Step Over)...", 'yellow');
fgets(STDIN);

// STEP 3: After stepping - variable assigned
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 3: Stepped to Line 22\n", 'bold');
echo "F10 was pressed. Line 21 executed. Variable \$appName now has a value!\n";
echo "Check the Variables panel → \$appName appeared!\n\n";

$variables = [
    '$appName' => 'Bishwo Calculator'
];
$callStack = [
    "debug_test.php:22"
];

showDebugPanel($variables, $callStack, $breakpoints);
showCodeWithBreakpoint(22, $code, 22);

echo "\n" . color("Press Enter to press F10 again...", 'yellow');
fgets(STDIN);

// STEP 4: More variables
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 4: Stepped to Line 23\n", 'bold');
echo "F10 pressed again. Line 22 executed. \$version now has a value!\n\n";

$variables = [
    '$appName' => 'Bishwo Calculator',
    '$version' => '1.0'
];
$callStack = [
    "debug_test.php:23"
];

showDebugPanel($variables, $callStack, $breakpoints);
showCodeWithBreakpoint(23, $code, 23);

echo "\n" . color("Press Enter to press F5 (Continue to next breakpoint)...", 'yellow');
fgets(STDIN);

// STEP 5: Jump to next breakpoint
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 5: Jumped to Line 38 (Next Breakpoint)\n", 'bold');
echo "F5 was pressed. Code ran until next breakpoint at line 38!\n";
echo "All variables from lines 21-37 are now visible in Variables panel.\n\n";

$variables = [
    '$appName' => 'Bishwo Calculator',
    '$version' => '1.0',
    '$isActive' => 'true',
    '$debugMode' => 'true'
];
$callStack = [
    "debug_test.php:38"
];

showDebugPanel($variables, $callStack, $breakpoints);
showCodeWithBreakpoint(38, $code, 38);

echo "\n" . color("Press Enter to press F10 (Step Over)...", 'yellow');
fgets(STDIN);

// STEP 6: Array creation
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 6: Array Variable Created\n", 'bold');
echo "F10 pressed. \$config array is now populated!\n";
echo "In Variables panel, you can expand arrays to see their contents.\n\n";

$variables = [
    '$appName' => 'Bishwo Calculator',
    '$version' => '1.0',
    '$isActive' => 'true',
    '$debugMode' => 'true',
    '$config' => ['environment' => 'development', 'database' => ['host' => 'localhost']]
];
$callStack = [
    "debug_test.php:48"
];

showDebugPanel($variables, $callStack, $breakpoints);

echo "\n";
echo color("┌─────────────────────────────────────────────────────────────────────────────┐\n", 'cyan');
echo color("│ EXPANDED ARRAY VIEW (click ▶ to expand in VS Code)                         │\n", 'cyan');
echo color("├─────────────────────────────────────────────────────────────────────────────┤\n", 'cyan');
echo color("│ ▼ \$config                                                                   │\n", 'green');
echo color("│   ▼ 'environment' = \"development\"                                           │\n", 'white');
echo color("│   ▼ 'database' (Array)                                                      │\n", 'white');
echo color("│       'host' = \"localhost\"                                                  │\n", 'white');
echo color("│       'port' = 3306                                                         │\n", 'white');
echo color("│       'name' = \"bishwo_db\"                                                  │\n", 'white');
echo color("└─────────────────────────────────────────────────────────────────────────────┘\n", 'cyan');

echo "\n" . color("Press Enter to continue...", 'yellow');
fgets(STDIN);

// STEP 7: Debug Console
clearScreen();
printStatusBar(true);
echo "\n";
echo color("STEP 7: Debug Console (Execute Code While Debugging)\n", 'bold');
echo "While paused, you can type PHP expressions in Debug Console!\n\n";

echo color("┌─────────────────────────────────────────────────────────────────────────────┐\n", 'cyan');
echo color("│ DEBUG CONSOLE                                                               │\n", 'cyan');
echo color("├─────────────────────────────────────────────────────────────────────────────┤\n", 'cyan');
echo color("│ > \$appName                                                                  │\n", 'yellow');
echo color("│ \"Bishwo Calculator\"                                                         │\n", 'green');
echo color("│                                                                             │\n", 'cyan');
echo color("│ > \$version                                                                  │\n", 'yellow');
echo color("│ \"1.0\"                                                                       │\n", 'green');
echo color("│                                                                             │\n", 'cyan');
echo color("│ > \$config['environment']                                                   │\n", 'yellow');
echo color("│ \"development\"                                                              │\n", 'green');
echo color("│                                                                             │\n", 'cyan');
echo color("│ > echo \$appName . ' v' . \$version                                          │\n", 'yellow');
echo color("│ Bishwo Calculator v1.0                                                      │\n", 'green');
echo color("└─────────────────────────────────────────────────────────────────────────────┘\n", 'cyan');

echo "\n" . color("Press Enter to press Shift+F5 (Stop Debugging)...", 'yellow');
fgets(STDIN);

// STEP 8: Debugging stopped
clearScreen();
printStatusBar(false);
echo "\n";
echo color("STEP 8: Debugging Stopped\n", 'bold');
echo "Shift+F5 was pressed. Debugging session ended.\n";
echo "Status bar returned to normal (blue).\n\n";

echo color("┌─────────────────────────────────────────────────────────────────────────────┐\n", 'cyan');
echo color("│                         DEBUGGING SESSION SUMMARY                           │\n", 'cyan');
echo color("├─────────────────────────────────────────────────────────────────────────────┤\n", 'cyan');
echo color("│                                                                             │\n", 'cyan');
echo color("│ ✓ Set breakpoints by clicking left of line numbers                         │\n", 'green');
echo color("│ ✓ Started debugging with F5                                                │\n", 'green');
echo color("│ ✓ Code paused at first breakpoint                                          │\n", 'green');
echo color("│ ✓ Inspected variables in Variables panel                                   │\n", 'green');
echo color("│ ✓ Stepped through code with F10                                            │\n", 'green');
echo color("│ ✓ Jumped to next breakpoint with F5                                        │\n", 'green');
echo color("│ ✓ Viewed array contents by expanding                                       │\n", 'green');
echo color("│ ✓ Executed code in Debug Console                                           │\n", 'green');
echo color("│ ✓ Stopped debugging with Shift+F5                                          │\n", 'green');
echo color("│                                                                             │\n", 'cyan');
echo color("└─────────────────────────────────────────────────────────────────────────────┘\n", 'cyan');

echo "\n";
printSeparator();
echo "\n";
echo color("                    🎉 DEMONSTRATION COMPLETE! 🎉\n", 'bold');
echo "\n";
printSeparator();
echo "\n";

echo color("What You Learned:\n", 'bold');
echo "  • How to set breakpoints (click left of line numbers)\n";
echo "  • How to start debugging (F5)\n";
echo "  • How to step through code (F10 = Step Over, F11 = Step Into)\n";
echo "  • How to inspect variables (Variables panel)\n";
echo "  • How to continue to next breakpoint (F5)\n";
echo "  • How to use Debug Console\n";
echo "  • How to stop debugging (Shift+F5)\n";
echo "  • Status bar changes: Blue = normal, Orange = debugging\n\n";

echo color("Now It's Your Turn!\n", 'bold');
echo "  1. Open VS Code\n";
echo "  2. Press Ctrl+Shift+D (Debug Panel)\n";
echo "  3. Open debug_test.php\n";
echo "  4. Set breakpoints (click left of line numbers)\n";
echo "  5. Press F5 and experience it yourself!\n\n";

echo color("Quick Reference:\n", 'bold');
echo "  Ctrl+Shift+D    Open Debug Panel\n";
echo "  F5              Start/Continue\n";
echo "  F10             Step Over (next line)\n";
echo "  F11             Step Into (enter function)\n";
echo "  Shift+F11       Step Out (exit function)\n";
echo "  Shift+F5        Stop\n\n";

echo color("Documentation:\n", 'bold');
echo "  • START_DEBUGGING_NOW.md      - Complete tutorial\n";
echo "  • START_HERE.txt              - Quick start\n";
echo "  • debug_test.php              - Practice file\n\n";

printSeparator();
echo "\n";
echo color("✨ You're ready to debug! Open VS Code and press Ctrl+Shift+D! ✨\n", 'green');
echo "\n";
printSeparator();
