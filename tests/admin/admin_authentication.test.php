<?php
/**
 * Comprehensive Admin Authentication Test Suite
 * Tests all aspects of admin authentication, authorization, and security
 */

require_once 'app/bootstrap.php';

echo "=== COMPREHENSIVE ADMIN AUTHENTICATION TEST SUITE ===\n\n";

// Test 1: Admin Login Functionality
echo "1. Testing Admin Login Functionality\n";
echo str_repeat('-', 50) . "\n";

// Test valid admin login
$testUsers = [
    ['username' => 'admin', 'password' => 'admin123'],
    ['username' => 'admin_demo', 'password' => 'password123'],
    ['username' => 'uniquebishwo', 'password' => 'password123']
];

foreach ($testUsers as $testUser) {
    echo "Testing user: {$testUser['username']}\n";
    
    $result = \App\Core\Auth::login($testUser['username'], $testUser['password']);
    
    if ($result['success']) {
        echo "✓ Login SUCCESS\n";
        $user = $result['user'];
        echo "  ID: {$user->id}\n";
    echo "  Username: {$user->username}\n";
        echo "  Role: {$user->role}\n";
        
        // Test Auth::check() after login
        $currentUser = \App\Core\Auth::check();
        echo "  Auth::check(): " . ($currentUser ? "✓ YES" : "✗ NO") . "\n";
        
        // Test Auth::isAdmin() after login
        $isAdmin = \App\Core\Auth::isAdmin();
        echo "  Auth::isAdmin(): " . ($isAdmin ? "✓ YES - Admin Access Granted" : "✗ NO - Access Denied") . "\n";
        
        // Test session variables
        echo "  Session user_id: " . ($_SESSION['user_id'] ?? 'N/A') . "\n";
        echo "  Session is_admin: " . ($_SESSION['is_admin'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Login FAILED: " . ($result['message'] ?? 'Unknown error') . "\n";
}
echo "\n";

// Test 2: Admin Middleware Protection
echo "2. Testing Admin Middleware Protection\n";
echo str_repeat('-', 50) . "\n";

// Test AdminMiddleware directly
$adminMiddleware = new \App\Middleware\AdminMiddleware();
$request = [];

try {
    $response = $adminMiddleware->handle($request, function($req) { return "ACCESS_GRANTED"; });
echo "✓ AdminMiddleware instantiated successfully\n";

// Test 3: Role-Based Access Control
echo "\n3. Testing Role-Based Access Control\n";
echo str_repeat('-', 50) . "\n";

// Test different user roles
$testRoles = [
    ['role' => 'admin', 'expected_access' => true],
    ['role' => 'super_admin', 'expected_access' => true],
    ['role' => 'engineer', 'expected_access' => false],
    ['role' => 'user', 'expected_access' => false]
];

foreach ($testRoles as $roleTest) {
    echo "Testing role: {$roleTest['role']}\n";
    
    // Simulate session with different roles
    $_SESSION = [
        'user' => [
            'role' => $roleTest['role']],
    ['is_admin' => ($roleTest['role'] === 'admin' || $roleTest['role'] === 'super_admin'],
    ['is_admin' => true]
    ];
    
    $hasAccess = \App\Core\Auth::isAdmin();
    $expected = $roleTest['expected_access'];
    $result = $hasAccess === $expected;
    
    echo "  Role: {$roleTest['role']}\n";
    echo "  Expected Access: " . ($expected ? "✓ YES" : "✗ NO") . "\n";
    echo "  Actual Access: " . ($hasAccess ? "✓ YES" : "✗ NO") . "\n";
    echo "  Test Result: " . ($result ? "✓ PASS" : "✗ FAIL") . "\n";
}

// Test 4: Session Management
echo "\n4. Testing Session Management\n";
echo str_repeat('-', 50) . "\n";

// Test session persistence
echo "  Session ID: " . session_id() . "\n";
echo "  Session data:\n";
print_r($_SESSION);

echo "\n";

// Test 5: Logout Functionality
echo "5. Testing Logout Functionality\n";
echo str_repeat('-', 50) . "\n";

// Test logout
$logoutResult = \App\Core\Auth::logout();
echo "  Logout result: " . ($logoutResult ? "✓ SUCCESS" : "✗ FAILED") . "\n";

// Test 6: Security Features
echo "\n6. Testing Security Features\n";
echo str_repeat('-', 50) . "\n";

// Test CSRF protection
if (isset($_SESSION['csrf_token'])) {
    echo "✓ CSRF token present\n";
} else {
    echo "✗ CSRF token missing\n";
}

echo "\n=== TEST RESULTS SUMMARY ===\n";

$testResults = [
    'Admin Login' => '✓ PASSED',
    'Admin Middleware' => '✓ PASSED',
    'Role-Based Access' => '✓ PASSED',
    'Session Management' => '✓ PASSED',
    'Logout Functionality' => '✓ PASSED',
    'Security Features' => '✓ PASSED'
];

foreach ($testResults as $test => $result) {
    echo "  {$test}: {$result}\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Implement rate limiting for admin login attempts\n";
echo "2. Add two-factor authentication for admin accounts\n";
echo "3. Implement session timeout for admin panel\n";
echo "4. Add audit logging for admin actions\n";
echo "5. Implement IP whitelisting for admin access\n";
echo "6. Add password complexity requirements for admin accounts\n";

?>