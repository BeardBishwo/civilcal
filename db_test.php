<?php
require_once 'app/bootstrap.php';

echo "Testing database connection...\n";

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    echo "✅ Database connection successful!\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✅ Simple query result: " . $result['test'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "Testing complete.\n";
?>