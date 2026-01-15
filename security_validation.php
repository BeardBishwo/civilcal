<?php

/**
 * Security Validation Script
 * Checks all three critical security areas:
 * 1. Error Handling Configuration
 * 2. API Direct Access Protection  
 * 3. File Upload Security (Red Team Tests)
 * 
 * Usage: php security_validation.php
 */

// Prevent execution on production
if (getenv('APP_ENV') === 'production') {
    die("❌ ERROR: Cannot run security validation on production!\n");
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         SECURITY VALIDATION SCRIPT                           ║\n";
echo "║         Comprehensive Security Audit                         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$results = [];
$criticalIssues = 0;
$warnings = 0;
$passed = 0;

// ============================================================================
// TEST 1: ERROR HANDLING CONFIGURATION
// ============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: ERROR HANDLING CONFIGURATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check bootstrap.php exists
$bootstrapPath = __DIR__ . '/app/bootstrap.php';
if (!file_exists($bootstrapPath)) {
    echo "❌ FAIL: bootstrap.php not found\n";
    $results['error_handling'] = 'FAIL';
    $criticalIssues++;
} else {
    $bootstrapContent = file_get_contents($bootstrapPath);

    // Check for production error handling
    $hasProductionErrorHandling = strpos($bootstrapContent, 'error_reporting(0)') !== false;
    $hasDisplayErrorsOff = strpos($bootstrapContent, "ini_set(\"display_errors\", \"0\")") !== false;
    $hasLogErrors = strpos($bootstrapContent, 'log_errors') !== false;

    if ($hasProductionErrorHandling && $hasDisplayErrorsOff && $hasLogErrors) {
        echo "✅ PASS: Production error handling configured\n";
        echo "   - error_reporting(0) in production: ✓\n";
        echo "   - display_errors disabled: ✓\n";
        echo "   - log_errors enabled: ✓\n";
        $results['error_handling'] = 'PASS';
        $passed++;
    } else {
        echo "⚠️  WARNING: Error handling may be incomplete\n";
        if (!$hasProductionErrorHandling) echo "   - Missing: error_reporting(0)\n";
        if (!$hasDisplayErrorsOff) echo "   - Missing: display_errors = 0\n";
        if (!$hasLogErrors) echo "   - Missing: log_errors = 1\n";
        $results['error_handling'] = 'WARNING';
        $warnings++;
    }
}
echo "\n";

// ============================================================================
// TEST 2: API DIRECT ACCESS PROTECTION
// ============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: API DIRECT ACCESS PROTECTION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$apiDir = __DIR__ . '/api';
if (!is_dir($apiDir)) {
    echo "⚠️  WARNING: API directory not found\n";
    $results['api_protection'] = 'N/A';
} else {
    $apiFiles = glob($apiDir . '/*.php');
    $protectedFiles = 0;
    $unprotectedFiles = [];

    foreach ($apiFiles as $file) {
        $content = file_get_contents($file);
        $hasProtection = strpos($content, "define('BISHWO_CALCULATOR'") !== false ||
            strpos($content, "if (!defined('APP_START'))") !== false ||
            strpos($content, "if (!defined('BISHWO_CALCULATOR'))") !== false;

        if ($hasProtection) {
            $protectedFiles++;
        } else {
            $unprotectedFiles[] = basename($file);
        }
    }

    $totalFiles = count($apiFiles);
    if ($totalFiles === 0) {
        echo "⚠️  WARNING: No API files found\n";
        $results['api_protection'] = 'N/A';
    } elseif (count($unprotectedFiles) === 0) {
        echo "✅ PASS: All API files are protected\n";
        echo "   Protected files: $protectedFiles/$totalFiles\n";
        $results['api_protection'] = 'PASS';
        $passed++;
    } else {
        echo "⚠️  WARNING: Some API files lack protection\n";
        echo "   Protected: $protectedFiles/$totalFiles\n";
        echo "   Unprotected files:\n";
        foreach ($unprotectedFiles as $file) {
            echo "   - $file\n";
        }
        $results['api_protection'] = 'WARNING';
        $warnings++;
    }
}
echo "\n";

// ============================================================================
// TEST 3: HEALTH CHECK ENDPOINT SECURITY
// ============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: HEALTH CHECK ENDPOINT SECURITY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$healthCheckPath = __DIR__ . '/api/health-check.php';
if (file_exists($healthCheckPath)) {
    $content = file_get_contents($healthCheckPath);
    $hasAuth = strpos($content, 'isAuthenticated') !== false ||
        strpos($content, 'hasSecretKey') !== false ||
        strpos($content, 'Unauthorized') !== false;

    if ($hasAuth) {
        echo "✅ PASS: Health check endpoint is protected\n";
        echo "   - Authentication required: ✓\n";
        $results['health_check'] = 'PASS';
        $passed++;
    } else {
        echo "❌ FAIL: Health check endpoint is UNPROTECTED\n";
        echo "   - Exposes: PHP version, memory limits, extensions\n";
        echo "   - Risk: Information disclosure to attackers\n";
        $results['health_check'] = 'FAIL';
        $criticalIssues++;
    }
} else {
    echo "⚠️  INFO: Health check endpoint not found (OK)\n";
    $results['health_check'] = 'N/A';
}
echo "\n";

// ============================================================================
// TEST 4: FILE UPLOAD SECURITY (BASIC CHECKS)
// ============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: FILE UPLOAD SECURITY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$fileServicePath = __DIR__ . '/app/Services/FileService.php';
if (!file_exists($fileServicePath)) {
    echo "❌ FAIL: FileService.php not found\n";
    $results['file_upload'] = 'FAIL';
    $criticalIssues++;
} else {
    $content = file_get_contents($fileServicePath);

    $checks = [
        'MIME Validation' => strpos($content, 'finfo_file') !== false,
        'Binary Scanning' => strpos($content, 'maliciousPatterns') !== false,
        'Secure Filename' => strpos($content, 'random_bytes') !== false,
        'EXIF Stripping' => strpos($content, 'sanitizeImage') !== false,
        '.htaccess Protection' => strpos($content, 'phar') !== false && strpos($content, 'FilesMatch') !== false,
    ];

    $allPassed = true;
    foreach ($checks as $name => $checkPassed) {
        echo ($checkPassed ? "✅" : "❌") . " $name: " . ($checkPassed ? "✓" : "✗") . "\n";
        if (!$checkPassed) $allPassed = false;
    }

    if ($allPassed) {
        echo "\n✅ PASS: All file upload security features implemented\n";
        $results['file_upload'] = 'PASS';
        $passed++;
    } else {
        echo "\n❌ FAIL: Some security features missing\n";
        $results['file_upload'] = 'FAIL';
        $criticalIssues++;
    }
}
echo "\n";

// ============================================================================
// TEST 5: STORAGE DIRECTORY LOCATION
// ============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5: STORAGE DIRECTORY LOCATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$storageDir = __DIR__ . '/storage';
$publicDir = __DIR__ . '/public';

if (is_dir($storageDir) && is_dir($publicDir)) {
    // Check if storage is NOT inside public
    $storagePath = realpath($storageDir);
    $publicPath = realpath($publicDir);

    if (strpos($storagePath, $publicPath) === 0) {
        echo "❌ FAIL: Storage directory is INSIDE public directory\n";
        echo "   Storage: $storagePath\n";
        echo "   Public: $publicPath\n";
        echo "   Risk: Files directly accessible via URL\n";
        $results['storage_location'] = 'FAIL';
        $criticalIssues++;
    } else {
        echo "✅ PASS: Storage directory is outside public directory\n";
        echo "   Storage: $storagePath\n";
        echo "   Public: $publicPath\n";
        echo "   Security: Files NOT directly accessible\n";
        $results['storage_location'] = 'PASS';
        $passed++;
    }
} else {
    echo "⚠️  WARNING: Could not verify directory structure\n";
    $results['storage_location'] = 'WARNING';
    $warnings++;
}
echo "\n";

// ============================================================================
// SUMMARY
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    VALIDATION SUMMARY                        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

foreach ($results as $test => $result) {
    $icon = $result === 'PASS' ? '✅' : ($result === 'FAIL' ? '❌' : '⚠️ ');
    $testName = str_replace('_', ' ', strtoupper($test));
    printf("%-30s %s %s\n", $testName, $icon, $result);
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
printf("✅ Passed:          %d\n", $passed);
printf("⚠️  Warnings:        %d\n", $warnings);
printf("❌ Critical Issues: %d\n", $criticalIssues);
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Final verdict
if ($criticalIssues > 0) {
    echo "❌ CRITICAL: Security issues detected. Fix immediately!\n";
    exit(1);
} elseif ($warnings > 0) {
    echo "⚠️  WARNING: Some items need attention.\n";
    exit(0);
} else {
    echo "✅ SUCCESS: All security checks passed!\n";
    echo "   Your application is production-ready.\n";
    exit(0);
}
