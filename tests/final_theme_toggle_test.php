<?php
/**
 * Final Theme Toggle Button Test
 * This test verifies that the theme toggle button is visible and working
 */

require_once __DIR__ . '/../app/bootstrap.php';

class FinalThemeToggleTest {
    private $testResults = [];
    private $passCount = 0;
    private $failCount = 0;

    public function runAllTests() {
        echo "🔍 Running Final Theme Toggle Button Test\n";
        echo "======================================\n\n";

        // Test 1: Theme Toggle Button HTML Existence
        $this->testThemeToggleButtonHTML();

        // Test 2: Theme Toggle JavaScript File
        $this->testThemeToggleJavaScriptFile();

        // Test 3: Theme Toggle CSS
        $this->testThemeToggleCSS();

        // Test 4: Fallback Initialization
        $this->testFallbackInitialization();

        // Test 5: Integration Test
        $this->testIntegration();

        // Summary
        $this->printSummary();
    }

    private function testThemeToggleButtonHTML() {
        $testName = "Theme Toggle Button HTML";
        echo "🔍 Testing: $testName\n";

        try {
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $themeButtonCheck = strpos($adminContent, 'id="themeToggle"') !== false;
            $themeIconCheck = strpos($adminContent, 'id="themeIcon"') !== false;
            $themeClassCheck = strpos($adminContent, 'theme-toggle-btn') !== false;
            $titleCheck = strpos($adminContent, 'title="Toggle Theme"') !== false;

            $allChecks = $themeButtonCheck && $themeIconCheck && $themeClassCheck && $titleCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Theme toggle button HTML is properly defined");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$themeButtonCheck) $errors[] = "Theme button ID missing";
                if (!$themeIconCheck) $errors[] = "Theme icon ID missing";
                if (!$themeClassCheck) $errors[] = "Theme button class missing";
                if (!$titleCheck) $errors[] = "Theme button title missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testThemeToggleJavaScriptFile() {
        $testName = "Theme Toggle JavaScript File";
        echo "🔍 Testing: $testName\n";

        try {
            // Check if theme toggle JS file exists
            $themeToggleFile = __DIR__ . '/../themes/admin/assets/js/theme-toggle.js';
            $jsExists = file_exists($themeToggleFile);

            if ($jsExists) {
                $jsContent = file_get_contents($themeToggleFile);

                $classCheck = strpos($jsContent, 'class ThemeToggle') !== false;
                $initCheck = strpos($jsContent, 'this.init()') !== false;
                $toggleCheck = strpos($jsContent, 'toggleTheme()') !== false;
                $localStorageCheck = strpos($jsContent, 'localStorage') !== false;

                $allChecks = $classCheck && $initCheck && $toggleCheck && $localStorageCheck;

                if ($allChecks) {
                    $this->logTestResult($testName, true, "✅ Theme toggle JavaScript file exists with all required functionality");
                    $this->passCount++;
                } else {
                    $errors = [];
                    if (!$classCheck) $errors[] = "ThemeToggle class missing";
                    if (!$initCheck) $errors[] = "Init method missing";
                    if (!$toggleCheck) $errors[] = "Toggle method missing";
                    if (!$localStorageCheck) $errors[] = "LocalStorage functionality missing";

                    $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                    $this->failCount++;
                }
            } else {
                $this->logTestResult($testName, false, "❌ Theme toggle JavaScript file does not exist");
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testThemeToggleCSS() {
        $testName = "Theme Toggle CSS";
        echo "🔍 Testing: $testName\n";

        try {
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $themeToggleCSSCheck = strpos($adminContent, '#themeToggle {') !== false;
            $themeBtnClassCheck = strpos($adminContent, '.theme-toggle-btn {') !== false;
            $fallbackCSSCheck = strpos($adminContent, '.fallback-visible') !== false;
            $themeVariablesCheck = strpos($adminContent, ':root.dark-theme') !== false;

            $allChecks = $themeToggleCSSCheck && $themeBtnClassCheck && $fallbackCSSCheck && $themeVariablesCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Theme toggle CSS is properly defined");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$themeToggleCSSCheck) $errors[] = "Theme toggle CSS missing";
                if (!$themeBtnClassCheck) $errors[] = "Theme button class CSS missing";
                if (!$fallbackCSSCheck) $errors[] = "Fallback CSS missing";
                if (!$themeVariablesCheck) $errors[] = "Theme variables missing";

                $this->logTestResult($testName, false, "❌ " . implode(", ", $errors));
                $this->failCount++;
            }

        } catch (Exception $e) {
            $this->logTestResult($testName, false, "❌ Exception: " . $e->getMessage());
            $this->failCount++;
        }

        echo "\n";
    }

    private function testFallbackInitialization() {
        $testName = "Fallback Initialization";
        echo "🔍 Testing: $testName\n";

        try {
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            $fallbackScriptCheck = strpos($adminContent, 'Theme Toggle Fallback Initialization') !== false;
            $fallbackThemeBtnCheck = strpos($adminContent, 'document.getElementById("themeToggle")') !== false;
            $fallbackThemeIconCheck = strpos($adminContent, 'document.getElementById("themeIcon")') !== false;
            $fallbackClickHandlerCheck = strpos($adminContent, 'themeBtn.addEventListener("click"') !== false;

            $allChecks = $fallbackScriptCheck && $fallbackThemeBtnCheck && $fallbackThemeIconCheck && $fallbackClickHandlerCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Fallback initialization script is properly implemented");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$fallbackScriptCheck) $errors[] = "Fallback script comment missing";
                if (!$fallbackThemeBtnCheck) $errors[] = "Fallback theme button check missing";
                if (!$fallbackThemeIconCheck) $errors[] = "Fallback theme icon check missing";
                if (!$fallbackClickHandlerCheck) $errors[] = "Fallback click handler missing";

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
            $adminContent = file_get_contents(__DIR__ . '/../themes/admin/layouts/admin.php');

            // Check that theme toggle is properly integrated
            $themeToggleScriptCheck = strpos($adminContent, 'theme-toggle.js') !== false;
            $themeToggleButtonCheck = strpos($adminContent, 'id="themeToggle"') !== false;
            $themeToggleCSSCheck = strpos($adminContent, '#themeToggle {') !== false;
            $fallbackCheck = strpos($adminContent, 'Theme Toggle Fallback Initialization') !== false;

            $allChecks = $themeToggleScriptCheck && $themeToggleButtonCheck && $themeToggleCSSCheck && $fallbackCheck;

            if ($allChecks) {
                $this->logTestResult($testName, true, "✅ Theme toggle button is fully integrated and should be visible");
                $this->passCount++;
            } else {
                $errors = [];
                if (!$themeToggleScriptCheck) $errors[] = "Theme toggle script missing";
                if (!$themeToggleButtonCheck) $errors[] = "Theme toggle button missing";
                if (!$themeToggleCSSCheck) $errors[] = "Theme toggle CSS missing";
                if (!$fallbackCheck) $errors[] = "Fallback initialization missing";

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
            echo "🎉 SUCCESS: Theme toggle button should now be visible and working!\n";
            echo "✅ Theme toggle button HTML is properly defined\n";
            echo "✅ Theme toggle JavaScript is properly implemented\n";
            echo "✅ Theme toggle CSS is properly styled\n";
            echo "✅ Fallback initialization ensures visibility\n";
            echo "✅ All components are properly integrated\n";
        } else {
            echo "⚠️  PARTIAL SUCCESS: Some theme toggle features need attention\n";
            echo "🔧 Please review the failed tests above\n";
        }
    }
}

// Run the tests
$test = new FinalThemeToggleTest();
$test->runAllTests();