<?php
// database/migrations/seed_difficulty_levels.php

require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "🌱 Seeding Dummy Questions for Difficulty...\n";

// Seed 10 Easy (Level 1)
for ($i=0; $i<10; $i++) {
    $stmt = $pdo->prepare("INSERT INTO quiz_questions (content, type, difficulty_level, default_marks) VALUES (:content, 'mcq_single', 1, 1)");
    $content = json_encode(['text' => "Dummy Easy Question $i", 'images' => []]);
    $stmt->execute(['content' => $content]);
}
echo "✅ Added 10 Easy Questions (Level 1)\n";

// Seed 10 Hard (Level 5)
for ($i=0; $i<10; $i++) {
    $stmt = $pdo->prepare("INSERT INTO quiz_questions (content, type, difficulty_level, default_marks) VALUES (:content, 'mcq_single', 5, 1)");
    $content = json_encode(['text' => "Dummy Hard Question $i", 'images' => []]);
    $stmt->execute(['content' => $content]);
}
echo "✅ Added 10 Hard Questions (Level 5)\n";
