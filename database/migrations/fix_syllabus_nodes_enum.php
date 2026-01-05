<?php
if (file_exists(dirname(__DIR__, 2) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    echo "MODIFYING syllabus_nodes table...\n";
    
    // Change ENUM to VARCHAR to allow new types (course, education_level, etc.)
    $sql = "ALTER TABLE syllabus_nodes MODIFY COLUMN type VARCHAR(50) NOT NULL";
    
    $conn->exec($sql);
    
    echo "✅ Successfully changed 'type' column to VARCHAR(50).\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
