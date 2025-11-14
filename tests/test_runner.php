<?php
/**
 * Test Runner - Execute all authentication and system tests
 */

echo "🧪 BISHWO CALCULATOR TEST SUITE\n";
echo "===============================\n\n";

$testSuites = [
    'API Tests' => [
        'tests/api/test_login_endpoint.php' => 'Login Endpoint Testing',
        'tests/api/test_session_management.php' => 'Session Management Testing',
        'tests/api/test_remember_me.php' => 'Remember Me Token Testing'
    ],
    'Registration Tests' => [
        'tests/registration/test_registration_api.php' => 'Registration API Testing'
    ],
    'Username Tests' => [
        'tests/username/test_username_availability.php' => 'Username Availability Testing'
    ]
];

$totalTests = 0;
$passedSuites = 0;
$failedSuites = 0;

foreach ($testSuites as $suiteName => $tests) {
    echo "📁 $suiteName\n";
    echo str_repeat("=", strlen($suiteName) + 3) . "\n\n";
    
    foreach ($tests as $testFile => $testDescription) {
        echo "🔍 Running: $testDescription\n";
        echo "   File: $testFile\n";
        
        if (file_exists(__DIR__ . '/../' . $testFile)) {
            echo "   Status: ";
            
            // Capture output
            ob_start();
            $startTime = microtime(true);
            
            try {
                include __DIR__ . '/../' . $testFile;
                $endTime = microtime(true);
                $output = ob_get_clean();
                
                $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
                
                // Simple pass/fail detection based on output
                $passed = (strpos($output, '❌') === false || strpos($output, '✅') !== false);
                
                if ($passed) {
                    echo "✅ PASSED";
                    $passedSuites++;
                } else {
                    echo "❌ FAILED";
                    $failedSuites++;
                }
                
                echo " (" . number_format($executionTime, 2) . "ms)\n";
                $totalTests++;
                
                // Show summary from output if available
                if (preg_match('/📊.*?SUMMARY.*?\n(.*?)✨/s', $output, $matches)) {
                    $summary = trim($matches[1]);
                    $summaryLines = explode("\n", $summary);
                    foreach ($summaryLines as $line) {
                        if (trim($line)) {
                            echo "   " . trim($line) . "\n";
                        }
                    }
                }
                
            } catch (Exception $e) {
                ob_end_clean();
                echo "❌ ERROR: " . $e->getMessage() . "\n";
                $failedSuites++;
                $totalTests++;
            }
        } else {
            echo "   Status: ❌ FILE NOT FOUND\n";
            $failedSuites++;
            $totalTests++;
        }
        
        echo "\n";
    }
    
    echo "\n";
}

// Overall summary
echo "📊 OVERALL TEST SUMMARY\n";
echo "=======================\n";
echo "🧪 Total Test Suites: $totalTests\n";
echo "✅ Passed: $passedSuites\n";
echo "❌ Failed: $failedSuites\n";
echo "📈 Success Rate: " . number_format(($passedSuites / $totalTests) * 100, 1) . "%\n";

if ($failedSuites === 0) {
    echo "\n🎉 ALL TESTS PASSED! 🎉\n";
} else {
    echo "\n⚠️ Some tests failed. Please review the output above.\n";
}

echo "\n✨ Test suite execution complete!\n";

// Frontend tests information
echo "\n🌐 FRONTEND TESTS\n";
echo "=================\n";
echo "Frontend tests require a web browser. Access them at:\n";
echo "• Login Form Test: /tests/frontend/test_login_form.html\n";
echo "• Registration Test: /test_registration_frontend.html (to be moved)\n";
echo "• Username Check Test: /test_username_check.html (to be moved)\n";
?>
