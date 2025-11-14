<?php
/**
 * Test Database Connection
 */

echo "🗄️ TESTING DATABASE CONNECTION\n";
echo "===============================\n\n";

// Test different database configurations
$configs = [
    [
        'host' => 'localhost',
        'dbname' => 'bishwo_calculator',
        'username' => 'root',
        'password' => ''
    ],
    [
        'host' => '127.0.0.1',
        'dbname' => 'bishwo_calculator',
        'username' => 'root',
        'password' => ''
    ],
    [
        'host' => 'localhost',
        'dbname' => 'bishwo_calculator',
        'username' => 'root',
        'password' => 'root'
    ]
];

foreach ($configs as $i => $config) {
    echo "🔍 Testing configuration " . ($i + 1) . ":\n";
    echo "   Host: {$config['host']}\n";
    echo "   Database: {$config['dbname']}\n";
    echo "   Username: {$config['username']}\n";
    echo "   Password: " . (empty($config['password']) ? '(empty)' : '***') . "\n";
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "   ✅ Connection successful!\n";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   📊 Users in database: {$result['count']}\n";
        
        // Test user lookup
        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
        $stmt->execute(['admin', 'admin']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "   👤 Found admin user: {$user['username']} ({$user['email']})\n";
        } else {
            echo "   ❌ Admin user not found\n";
        }
        
        echo "   🎉 This configuration works!\n\n";
        break;
        
    } catch (Exception $e) {
        echo "   ❌ Connection failed: " . $e->getMessage() . "\n\n";
    }
}

// Also test using the app's config
echo "🔧 Testing app configuration:\n";

try {
    define('BISHWO_CALCULATOR', true);
    require_once __DIR__ . '/app/bootstrap.php';
    
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   ✅ App database connection works!\n";
    echo "   📊 Users: {$result['count']}\n";
    
} catch (Exception $e) {
    echo "   ❌ App database error: " . $e->getMessage() . "\n";
}

echo "\n✨ Test complete!\n";
?>
