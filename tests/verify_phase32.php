<?php
// tests/verify_phase32.php

echo "Phase 32: Infrastructure Security Verification\n";
echo "==============================================\n";

$pass = 0;
$fail = 0;

function assertCondition($name, $condition)
{
    global $pass, $fail;
    if ($condition) {
        echo "[PASS] $name\n";
        $pass++;
    } else {
        echo "[FAIL] $name\n";
        $fail++;
    }
}

// 1. Check Database Timeout
$dbFile = file_get_contents(__DIR__ . '/../app/Core/Database.php');
if (strpos($dbFile, 'PDO::ATTR_TIMEOUT => 5') !== false) {
    assertCondition("Database: Connection Timeout set to 5s", true);
} else {
    assertCondition("Database: Connection Timeout set to 5s", false);
}

// 2. Check Security Middleware Existence and Usage
$middlewareFile = __DIR__ . '/../app/Middleware/SecurityHeadersMiddleware.php';
if (file_exists($middlewareFile)) {
    assertCondition("Middleware: SecurityHeadersMiddleware.php exists", true);

    $indexFile = file_get_contents(__DIR__ . '/../public/index.php');
    if (strpos($indexFile, 'SecurityHeadersMiddleware') !== false) {
        assertCondition("Middleware: Registered in index.php", true);
    } else {
        assertCondition("Middleware: Registered in index.php", false);
    }
} else {
    assertCondition("Middleware: SecurityHeadersMiddleware.php exists", false);
}

// 3. Check Session Security
$securityFile = file_get_contents(__DIR__ . '/../app/Services/Security.php');
if (strpos($securityFile, "'samesite' => 'Lax'") !== false && strpos($securityFile, "'secure' => \$secure") !== false) {
    assertCondition("Security: Strict Session Cookie Params", true);
} else {
    assertCondition("Security: Strict Session Cookie Params", false);
}

// 4. Check Exception Handler Hardening
$bootstrapFile = file_get_contents(__DIR__ . '/../app/bootstrap.php');
// Look for the production echo string we added
if (strpos($bootstrapFile, 'An unexpected error occurred. Administrators have been notified.') !== false) {
    assertCondition("Bootstrap: Production Error Message Hardened", true);
} else {
    assertCondition("Bootstrap: Production Error Message Hardened", false);
}

echo "\nSummary: $pass Passed, $fail Failed\n";
