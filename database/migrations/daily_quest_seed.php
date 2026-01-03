<?php
/**
 * Migration: Seed Daily Quest Exam Placeholder
 */

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    echo "🌱 Seeding Daily Quest Exam Placeholder...\n";

    // 1. Check if it exists
    $stmt = $conn->prepare("SELECT id FROM quiz_exams WHERE slug = 'daily-quest'");
    $stmt->execute();
    $exists = $stmt->fetch();

    if (!$exists) {
        $sql = "INSERT INTO quiz_exams (title, slug, description, type, mode, duration_minutes, total_marks, status) 
                VALUES ('Daily Quest', 'daily-quest', 'Your daily improved challenge.', 'practice', 'exam', 10, 50, 'published')";
        $conn->exec($sql);
        echo "✅ Created 'Daily Quest' exam (slug: daily-quest)\n";
    } else {
        echo "ℹ️ 'Daily Quest' exam already exists.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
