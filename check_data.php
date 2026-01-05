<?php
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    $stmt = $pdo->query("SELECT title, course_id, education_level_id FROM position_levels WHERE level_number BETWEEN 4 AND 8 AND (course_id IS NULL OR education_level_id IS NULL)");
    $unlinked = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($unlinked)) {
        echo "PERFECT: All Level 4-8 positions are linked!";
    } else {
        echo "WARNING: Found unlinked positions:\n";
        print_r($unlinked);
    }
    
    echo "\n\nUNLINKED POSITION LEVELS:\n";
    $stmt = $pdo->query("SELECT id, title, slug, course_id FROM position_levels WHERE course_id IS NULL OR education_level_id IS NULL");
    $unlinked = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($unlinked as $row) {
        echo "[{$row['id']}] {$row['title']} (Slug: {$row['slug']})\n";
    }
    
    echo "\nTotal Unlinked: " . count($unlinked);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
