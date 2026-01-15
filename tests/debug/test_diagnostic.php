<?php
// Minimal test without bootstrap
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Firm Creation Diagnostic Test ===\n\n";

// Test 1: Database connection
echo "1. Testing database connection...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');
    echo "   ✓ Database connected\n\n";
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check required tables
echo "2. Checking required tables...\n";
$tables = ['guilds', 'guild_members', 'guild_vault', 'user_resources', 'quiz_sessions'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($stmt->rowCount() > 0) {
        echo "   ✓ Table '$table' exists\n";
    } else {
        echo "   ✗ Table '$table' missing\n";
    }
}
echo "\n";

// Test 3: Check if user has resources
echo "3. Checking user resources...\n";
$stmt = $pdo->query("SELECT * FROM user_resources WHERE user_id = 1");
$resources = $stmt->fetch(PDO::FETCH_ASSOC);
if ($resources) {
    echo "   ✓ User 1 has resources\n";
    echo "   - Coins: " . ($resources['coins'] ?? 0) . "\n";
} else {
    echo "   ✗ User 1 has no resources row\n";
}
echo "\n";

// Test 4: Check if user is already in a guild
echo "4. Checking guild membership...\n";
$stmt = $pdo->query("SELECT * FROM guild_members WHERE user_id = 1");
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if ($member) {
    echo "   ! User 1 is already in guild " . $member['guild_id'] . "\n";
} else {
    echo "   ✓ User 1 is not in any guild\n";
}
echo "\n";

// Test 5: Try to load required classes
echo "5. Testing class loading...\n";
require_once __DIR__ . '/app/bootstrap.php';

$classes = [
    'App\\Core\\Database',
    'App\\Services\\FirmService',
    'App\\Services\\GamificationService',
    'App\\Services\\NonceService',
    'App\\Services\\RateLimiter',
    'App\\Services\\SecurityMonitor',
    'App\\Controllers\\Quiz\\FirmController'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "   ✓ Class '$class' loaded\n";
    } else {
        echo "   ✗ Class '$class' not found\n";
    }
}

echo "\n=== Diagnostic Complete ===\n";
