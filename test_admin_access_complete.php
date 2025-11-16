<?php
// Simulate a web session environment
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'app/bootstrap.php';

echo "=== Testing Complete Admin Panel Access Flow ===\n";

// 1. First login as admin
echo "\n1. Logging in as admin...\n";
$result = \App\Core\Auth::login('admin', 'admin123');

if ($result['success']) {
    echo "✓ Login successful\n";
    $user = $result['user'];
    echo "  Username: {$user->username}\n";
    echo "  Role: {$user->role}\n";
    
    // 2. Test Auth::check() after login
    echo "\n2. Testing Auth::check()...\n";
    $currentUser = \App\Core\Auth::check();
    echo "  Auth::check(): " . ($currentUser ? "✓ YES" : "✗ NO") . "\n";
    echo "  User ID: " . ($currentUser ? $currentUser->id : "N/A") . "\n";
    
    // 3. Test Auth::isAdmin() after login
    echo "\n3. Testing Auth::isAdmin()...\n";
    $isAdmin = \App\Core\Auth::isAdmin();
    echo "  Auth::isAdmin(): " . ($isAdmin ? "✓ YES - Admin Access Granted" : "✗ NO - Access Denied") . "\n";
    
    // 4. Test AdminMiddleware logic
    echo "\n4. Testing AdminMiddleware simulation...\n";
    $isAuthenticated = !empty($_SESSION['user_id']) || !empty($_SESSION['user']);
    $isAdminFromSession = !empty($_SESSION['is_admin']) || 
                          (!empty($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']) ||
                          (!empty($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin']));
    
    echo "  Session authenticated: " . ($isAuthenticated ? "✓ YES" : "✗ NO") . "\n";
    echo "  Session admin status: " . ($isAdminFromSession ? "✓ YES" : "✗ NO") . "\n";
    
    // 5. Final verdict
    echo "\n=== FINAL RESULT ===\n";
    if ($isAuthenticated && $isAdminFromSession) {
        echo "🎉 SUCCESS: Admin panel access should work!\n";
        echo "   - User is authenticated: ✓\n";
        echo "   - User is admin: ✓\n";
        echo "   - Middleware should allow access: ✓\n";
        echo "\n💡 Next steps:\n";
        echo "   1. Clear your browser cookies for localhost\n";
        echo "   2. Go to http://localhost/Bishwo_Calculator/admin/settings/general\n";
        echo "   3. Login with username: 'admin' and password: 'admin123'\n";
    } else {
        echo "❌ FAILED: Admin panel access still not working\n";
        echo "   - Session authenticated: " . ($isAuthenticated ? "✓" : "✗") . "\n";
        echo "   - Session admin status: " . ($isAdminFromSession ? "✓" : "✗") . "\n";
    }
    
} else {
    echo "✗ Login failed: " . ($result['message'] ?? 'Unknown error') . "\n";
}

echo "\n=== Session Debug Info ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session data:\n";
print_r($_SESSION);
?>