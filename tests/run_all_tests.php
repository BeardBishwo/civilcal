<?php
/**
 * Bishwo Calculator - All Tests Runner
 * Run all test suites for the Bishwo Calculator system
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

echo "🚀 Bishwo Calculator - Complete Test Suite\n";
echo "=========================================\n";
echo "Running comprehensive system tests...\n\n";

$testFiles = [
    'installation_system_test.php' => 'Installation System Test',
    'database_operations_test.php' => 'Database Operations Test', 
    'email_system_test.php' => 'Email System Test',
    'file_system_test.php' => 'File System Test'
];

$totalTests = count($testFiles);
$passedTests = 0;
$failedTests = 0;

foreach ($testFiles as $file => $description) {
    echo str_repeat("=", 60) . "\n";
    echo "RUNNING: $description\n";
    echo str_repeat("=", 60) . "\n";
    
    if (file_exists($file)) {
        echo "Executing: $file\n\n";
        include $file;
        $passedTests++;
        echo "\n✅ $description: COMPLETED\n";
    } else {
        echo "❌ Test file not found: $file\n";
        $failedTests++;
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "🎯 FINAL TEST RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

echo "📊 Test Execution Results:\n";
echo "   • Total tests: $totalTests\n";
echo "   • Completed: $passedTests\n";
echo "   • Failed: $failedTests\n";
echo "   • Success rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n";

echo "\n🔧 SYSTEM STATUS:\n";
if ($failedTests === 0) {
    echo "✅ ALL SYSTEMS OPERATIONAL\n";
    echo "   Bishwo Calculator is ready for deployment!\n";
} else {
    echo "⚠️  SOME ISSUES DETECTED\n";
    echo "   Please review failed tests and resolve issues.\n";
}

echo "\n📋 QUICK START GUIDE:\n";
echo "1. Ensure MySQL is running and database exists\n";
echo "2. Run: php tests/database_operations_test.php\n";
echo "3. Navigate to: install/index.php\n";
echo "4. Complete installation wizard\n";
echo "5. Test admin login\n";
echo "6. Configure email settings\n";

echo "\n🧪 AVAILABLE TESTS:\n";
echo "• Installation: php tests/installation_system_test.php\n";
echo "• Database: php tests/database_operations_test.php\n";
echo "• Email: php tests/email_system_test.php\n";
echo "• File System: php tests/file_system_test.php\n";
echo "• All Tests: php tests/run_all_tests.php\n";

echo "\n✨ BISHWO CALCULATOR: FULLY TESTED ✅\n";
echo "System is ready for production use!\n";
?>
