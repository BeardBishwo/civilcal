<?php
require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting Quiz Hierarchy Database Updates...\n";

// 1. Create exam_position_levels Junction Table
$sqlExamLevels = "CREATE TABLE IF NOT EXISTS exam_position_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    position_level_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES quiz_exams(id) ON DELETE CASCADE,
    FOREIGN KEY (position_level_id) REFERENCES position_levels(id) ON DELETE CASCADE,
    UNIQUE KEY unique_exam_level (exam_id, position_level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

try {
    $pdo->exec($sqlExamLevels);
    echo "[SUCCESS] Created table 'exam_position_levels'.\n";
} catch (PDOException $e) {
    echo "[ERROR] Creating 'exam_position_levels': " . $e->getMessage() . "\n";
}

// 2. Add columns to quiz_exams
$columnsToAdd = [
    'course_id' => 'INT NULL AFTER description',
    'education_level_id' => 'INT NULL AFTER course_id'
];

foreach ($columnsToAdd as $col => $def) {
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM quiz_exams LIKE ?");
    $stmt->execute([$col]);
    if (!$stmt->fetch()) {
        try {
            $pdo->exec("ALTER TABLE quiz_exams ADD COLUMN $col $def");
            echo "[SUCCESS] Added column '$col' to 'quiz_exams'.\n";
            
            // Add Index
            $pdo->exec("ALTER TABLE quiz_exams ADD INDEX idx_{$col} ($col)");
             echo "[SUCCESS] Added index for '$col'.\n";
        } catch (PDOException $e) {
             echo "[ERROR] Adding column '$col': " . $e->getMessage() . "\n";
        }
    } else {
        echo "[INFO] Column '$col' already exists in 'quiz_exams'.\n";
    }
}

echo "Database updates completed.\n";
