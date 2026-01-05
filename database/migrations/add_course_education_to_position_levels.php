<?php
/**
 * Migration: Add Course and Education Level to Position Levels
 * Adds course_id and education_level_id columns for filtering
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🔧 Adding Course and Education Level columns to position_levels...\n\n";
    
    // Add columns
    $sql = "ALTER TABLE position_levels 
            ADD COLUMN course_id INT NULL AFTER id,
            ADD COLUMN education_level_id INT NULL AFTER course_id,
            ADD INDEX idx_course (course_id),
            ADD INDEX idx_education_level (education_level_id)";
    
    $pdo->exec($sql);
    echo "✓ Columns added successfully\n\n";
    
    // Update existing position levels with course and education level
    echo "📝 Connecting existing position levels to hierarchy...\n\n";
    
    // Get all courses
    $courses = $pdo->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course'")->fetchAll(PDO::FETCH_ASSOC);
    
    // Map position levels to courses and education levels
    $mappings = [
        // Engineering positions (all courses except Computer/IT and Agriculture)
        'Level 4 - Assistant Sub-Engineer' => ['level' => 4, 'courses' => ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering']],
        'Level 5 - Sub-Engineer' => ['level' => 5, 'courses' => ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering']],
        'Level 6 - Engineer' => ['level' => 6, 'courses' => ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering']],
        'Level 7 - Senior Engineer' => ['level' => 7, 'courses' => ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering']],
        'Level 8 - Chief Engineer' => ['level' => 8, 'courses' => ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering']],
        
        // Computer/IT specific
        'Computer Operator (Level 4)' => ['level' => 4, 'courses' => ['Computer/IT Engineering']],
        'IT Officer (Level 5)' => ['level' => 5, 'courses' => ['Computer/IT Engineering']],
        
        // Agriculture specific
        'Agriculture Officer (Level 5)' => ['level' => 5, 'courses' => ['Agriculture Engineering']],
        
        // Architecture specific
        'Architect (Level 6)' => ['level' => 6, 'courses' => ['Architecture']],
    ];
    
    foreach ($mappings as $posTitle => $config) {
        foreach ($config['courses'] as $courseTitle) {
            // Find course ID
            $courseId = null;
            foreach ($courses as $course) {
                if ($course['title'] === $courseTitle) {
                    $courseId = $course['id'];
                    break;
                }
            }
            
            if (!$courseId) continue;
            
            // Find education level ID
            $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE type = 'education_level' AND parent_id = ? AND title LIKE ?");
            $stmt->execute([$courseId, '%Level ' . $config['level'] . '%']);
            $eduLevel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$eduLevel) continue;
            
            // Update position level
            $stmt = $pdo->prepare("UPDATE position_levels SET course_id = ?, education_level_id = ? WHERE title = ?");
            $stmt->execute([$courseId, $eduLevel['id'], $posTitle]);
            
            echo "  ✓ Connected '{$posTitle}' to {$courseTitle}\n";
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nNext: Update the Position Levels UI to show filters\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
