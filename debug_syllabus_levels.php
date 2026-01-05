<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🔍 Checking for Hierarchy Nodes with Assigned Levels...\n\n";
    
    $sql = "SELECT id, type, title, level 
            FROM syllabus_nodes 
            WHERE type IN ('course', 'education_level', 'category', 'sub_category') 
            AND level IS NOT NULL 
            AND level != ''";
            
    $nodes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($nodes)) {
        echo "✅ No hierarchy nodes have assigned levels. Clean!\n";
    } else {
        echo "⚠️ Found " . count($nodes) . " hierarchy nodes with accidental levels:\n";
        foreach ($nodes as $node) {
            echo "  - [{$node['type']}] {$node['title']} (Level: '{$node['level']}')\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
