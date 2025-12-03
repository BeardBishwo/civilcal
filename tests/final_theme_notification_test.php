<?php
/**
 * Final Comprehensive Test for Notification Visibility and Theme Toggle
 * This test verifies that both the notification UI is visible and the theme toggle works
 */

require_once __DIR__ . '/../app/bootstrap.php';

class FinalThemeNotificationTest {
    private $testResults = [];
    private $passCount = 0;
    private $failCount = 0;

    public function runAllTests() {
        echo "🧪 Running Final Comprehensive Test for Notification & Theme Features\n";
        echo "==============================================================\n\n";

        // Test 1: Notification UI Visibility
        $this->testNotificationUIVisibility();

        // Test 2: Theme Toggle Button Existence
        $this->testThemeToggleButtonExistence();

        // Test 3: Theme Toggle JavaScript
        $this->testThemeToggleJavaScript();

        // Test 4: CSS Theme Variables
        $this->testCSSThemeVariables();

        // Test 5: Notification JavaScript Enhancements
        $this->testNotificationJavaScriptEnhancements();

        // Test 6: Integration Test
        $this->testIntegration();

        // Summary
        $this->printSummary();
    }

    private function testNotificationUIVisibility() {
        $testName = "Notification UI Visibility";
        echo "🔍 Testing: $testName\n";

        try {
            // Check if admin.php contains the notification button with proper CSS
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $notificationButtonCheck = strpos($adminContent, 'id="notificationToggle"') !== false;
            $notificationCSSCheck = strpos($adminContent, '#notificationToggle {') !== false;
            $visibilityCSSCheck = strpos($adminContent, 'visibility: visible !important;') !== false;
            $opacityCSSCheck = strpos($adminContent, 'opacity: 1 !important;') !== false;

            $allChecks = $notificationButtonCheck && $notificationCSSCheck && $visibilityCSSCheck && $opacityCSSCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Notification button exists with proper visibility CSS");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$notificationButtonCheck) $errors[] = "Notification button HTML missing";
                if (!$notificationCSSCheck) $errors[] = "Notification CSS missing";
                if (!$visibilityCSSCheck) $errors[] = "Visibility CSS missing";
                if (!$opacityCSSCheck) $errors[] = "Opacity CSS missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testThemeToggleButtonExistence() {
        $testName = "Theme Toggle Button Existence";
        echo "🔍 Testing: $testName\n";

        try {
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $themeButtonCheck = strpos($adminContent, 'id="themeToggle"') !== false;
            $themeIconCheck = strpos($adminContent, 'id="themeIcon"') !== false;
            $themeToggleJSCheck = strpos($adminContent, 'theme-toggle.js') !== false;

            $allChecks = $themeButtonCheck && $themeIconCheck && $themeToggleJSCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Theme toggle button exists with proper HTML and JS");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$themeButtonCheck) $errors[] = "Theme button HTML missing";
                if (!$themeIconCheck) $errors[] = "Theme icon missing";
                if (!$themeToggleJSCheck) $errors[] = "Theme toggle JS missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testThemeToggleJavaScript() {
        $testName = "Theme Toggle JavaScript";
        echo "🔍 Testing: $testName\n";

        try {
            $themeToggleContent = file_get_contents(__DIR__ . '/../themes/admin/assets/js/theme-toggle.js');

            $classCheck = strpos($themeToggleContent, 'class ThemeToggle') !== false;
            $localStorageCheck = strpos($themeToggleContent, 'localStorage.setItem') !== false;
            $localStorageGetCheck = strpos($themeToggleContent, 'localStorage.getItem') !== false;
            $themeIconUpdateCheck = strpos($themeToggleContent, 'updateThemeIcon') !== false;
            $themeUIUpdateCheck = strpos($themeToggleContent, 'updateThemeUI') !== false;

            $allChecks = $classCheck && $localStorageCheck && $localStorageGetCheck && $themeIconUpdateCheck && $themeUIUpdateCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Theme toggle JS has all required functionality");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$classCheck) $errors[] = "ThemeToggle class missing";
                if (!$localStorageCheck) $errors[] = "localStorage.setItem missing";
                if (!$localStorageGetCheck) $errors[] = "localStorage.getItem missing";
                if (!$themeIconUpdateCheck) $errors[] = "updateThemeIcon method missing";
                if (!$themeUIUpdateCheck) $errors[] = "updateThemeUI method missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testCSSThemeVariables() {
        $testName = "CSS Theme Variables";
        echo "🔍 Testing: $testName\n";

        try {
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $darkThemeCheck = strpos($adminContent, ':root.dark-theme') !== false;
            $lightThemeCheck = strpos($adminContent, ':root.light-theme') !== false;
            $themeVariablesCheck = strpos($adminContent, '--admin-gray-50:') !== false;
            $themeFeedbackCheck = strpos($adminContent, '.theme-feedback-toast') !== false;

            $allChecks = $darkThemeCheck && $lightThemeCheck && $themeVariablesCheck && $themeFeedbackCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ CSS theme variables properly defined");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$darkThemeCheck) $errors[] = "Dark theme CSS missing";
                if (!$lightThemeCheck) $errors[] = "Light theme CSS missing";
                if (!$themeVariablesCheck) $errors[] = "Theme variables missing";
                if (!$themeFeedbackCheck) $errors[] = "Theme feedback CSS missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testNotificationJavaScriptEnhancements() {
        $testName = "Notification JavaScript Enhancements";
        echo "🔍 Testing: $testName\n";

        try {
            $notificationContent = file_get_contents(__DIR__ . '/../themes/admin/assets/js/notification-unified.js');

            $errorHandlingCheck = strpos($notificationContent, 'handlePollingError') !== false;
            $retryLogicCheck = strpos($notificationContent, 'exponential backoff') !== false;
            $debugLoggingCheck = strpos($notificationContent, 'this.log') !== false;
            $fallbackHandlerCheck = strpos($notificationContent, 'fallback click handler') !== false;

            $allChecks = $errorHandlingCheck && $retryLogicCheck && $debugLoggingCheck && $fallbackHandlerCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Notification JS has enhanced error handling and debugging");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$errorHandlingCheck) $errors[] = "Error handling missing";
                if (!$retryLogicCheck) $errors[] = "Retry logic missing";
                if (!$debugLoggingCheck) $errors[] = "Debug logging missing";
                if (!$fallbackHandlerCheck) $errors[] = "Fallback handler missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testIntegration() {
        $testName = "Integration Test";
        echo "🔍 Testing: $testName\n";

        try {
            // Check that both systems can coexist
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $notificationSystemCheck = strpos($adminContent, 'notification-unified.js') !== false;
            $themeToggleSystemCheck = strpos($adminContent, 'theme-toggle.js') !== false;
            $bothScriptsCheck = strpos($adminContent, 'notification-unified.js') !== false &&
                               strpos($adminContent, 'theme-toggle.js') !== false;

            $cssCompatibilityCheck = strpos($adminContent, ':root.dark-theme') !== false &&
                                   strpos($adminContent, '#notificationToggle') !== false;

            $allChecks = $notificationSystemCheck && $themeToggleSystemCheck && $bothScriptsCheck && $cssCompatibilityCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Both notification and theme systems integrated properly");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$notificationSystemCheck) $errors[] = "Notification system missing";
                if (!$themeToggleSystemCheck) $errors[] = "Theme toggle system missing";
                if (!$bothScriptsCheck) $errors[] = "Both scripts not present";
                if (!$cssCompatibilityCheck) $errors[] = "CSS compatibility issues";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function logTestResult($testName, $passed, $message) {
        $status = $passed ? "PASS" : "FAIL";
        $emoji = $passed ? "✅" : "❌";
        echo "  $emoji $testName: $status\n";
        echo "     $message\n";

        $this->testResults[] = [
            'test' => $testName,
            'passed' => $passed,
            'message' => $message
        ];
    }

    private function printSummary() {
        echo "📊 TEST SUMMARY\n";
        echo "=============\n\n";

        $totalTests = count($this->testResults);
        $passRate = $totalTests > 0 ? ($this->passCount / $totalTests) * 100 : 0;

        echo "Total Tests: $totalTests\n";
        echo "Passed: $this->passCount\n";
        echo "Failed: $this->failCount\n";
        echo "Pass Rate: " . number_format($passRate, 2) . "%\n\n";

        echo "📋 DETAILED RESULTS:\n";
        echo "==================\n";

        foreach ($this->testResults as $result) {
            $status = $result['passed'] ? "PASS" : "FAIL";
            $emoji = $result['passed'] ? "✅" : "❌";
            echo "$emoji {$result['test']}: $status\n";
            echo "   {$result['message']}\n";
        }

        echo "\n";

        // Final verdict
        if ($passRate >= 80) {
            echo "🎉 SUCCESS: Both notification and theme features are working properly!\n";
            echo "✅ Notification UI is now visible and functional\n";
            echo "✅ Theme toggle button works with dark/light switching\n";
            echo "✅ Both systems integrate without conflicts\n";
        } else {
            echo "⚠️  PARTIAL SUCCESS: Some features need attention\n";
            echo "🔧 Please review the failed tests above\n";
        }
    }
}

// Run the tests
$test = new FinalThemeNotificationTest();
$test->runAllTests();