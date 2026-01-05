<?php
/**
 * Fix: Link Civil and Electrical Engineering Position Levels
 * Updates position levels that weren't linked in the first migration
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🔧 Fixing Civil and Electrical Engineering Position Level Links...\n\n";
    
    // Get all position levels
    $allLevels = $pdo->query("SELECT id, title, course_id FROM position_levels")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all courses
    $courses = $pdo->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course'")->fetchAll(PDO::FETCH_ASSOC);
    
    // Create course lookup
    $courseLookup = [];
    foreach ($courses as $course) {
        $courseLookup[$course['title']] = $course['id'];
    }
    
    echo "📋 Found " . count($allLevels) . " position levels\n";
    echo "📋 Found " . count($courses) . " courses\n\n";
    
    // Process each position level that doesn't have a course assigned
    foreach ($allLevels as $level) {
        if ($level['course_id']) {
            echo "  ⏭️  Skipping '{$level['title']}' (already linked)\n";
            continue; // Already linked
        }
        
        $title = $level['title'];
        $levelNumber = null;
        
        // Extract level number from title
        if (preg_match('/Level\s+(\d+)/', $title, $matches)) {
            $levelNumber = (int)$matches[1];
        }
        
        if (!$levelNumber) {
            echo "  ⚠️  Could not extract level number from '{$title}'\n";
            continue;
        }
        
        // Determine which courses this level should be linked to
        $targetCourses = [];
        
        // Generic engineering levels (4-8) should be linked to Civil, Electrical, Mechanical
        if (in_array($levelNumber, [4, 5, 6, 7, 8])) {
            // Check if it's a specific discipline
            if (stripos($title, 'Computer') !== false || stripos($title, 'IT') !== false) {
                $targetCourses = ['Computer/IT Engineering'];
            } elseif (stripos($title, 'Agriculture') !== false) {
                $targetCourses = ['Agriculture Engineering'];
            } elseif (stripos($title, 'Architect') !== false) {
                $targetCourses = ['Architecture'];
            } elseif (stripos($title, 'Electrical') !== false) {
                $targetCourses = ['Electrical Engineering'];
            } elseif (stripos($title, 'Mechanical') !== false) {
                $targetCourses = ['Mechanical Engineering'];
            } elseif (stripos($title, 'Civil') !== false) {
                $targetCourses = ['Civil Engineering'];
            } else {
                // Generic engineering - link to Civil, Electrical, Mechanical
                $targetCourses = ['Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering'];
            }
        }
        
        if (empty($targetCourses)) {
            echo "  ⚠️  No target courses for '{$title}'\n";
            continue;
        }
        
        // Link to each target course
        foreach ($targetCourses as $courseName) {
            if (!isset($courseLookup[$courseName])) {
                echo "  ❌ Course '{$courseName}' not found\n";
                continue;
            }
            
            $courseId = $courseLookup[$courseName];
            
            // Find the education level for this course and level number
            $stmt = $pdo->prepare("
                SELECT id, title FROM syllabus_nodes 
                WHERE type = 'education_level' 
                AND parent_id = ? 
                AND title LIKE ?
                LIMIT 1
            ");
            $stmt->execute([$courseId, "%Level {$levelNumber}%"]);
            $eduLevel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$eduLevel) {
                echo "  ❌ Education level not found for {$courseName}, Level {$levelNumber}\n";
                continue;
            }
            
            // Create a new position level entry for this course (if it doesn't exist)
            // OR update the existing one if there's only one target course
            if (count($targetCourses) === 1) {
                // Single target - update existing
                $stmt = $pdo->prepare("UPDATE position_levels SET course_id = ?, education_level_id = ? WHERE id = ?");
                $stmt->execute([$courseId, $eduLevel['id'], $level['id']]);
                echo "  ✅ Updated '{$title}' → {$courseName}\n";
            } else {
                // Multiple targets - create duplicates for each course
                // First one updates the original
                static $firstUpdate = [];
                if (!isset($firstUpdate[$level['id']])) {
                    $stmt = $pdo->prepare("UPDATE position_levels SET course_id = ?, education_level_id = ? WHERE id = ?");
                    $stmt->execute([$courseId, $eduLevel['id'], $level['id']]);
                    $firstUpdate[$level['id']] = true;
                    echo "  ✅ Updated '{$title}' → {$courseName}\n";
                } else {
                    // Create duplicate for additional courses
                    $newTitle = $title . " ({$courseName})";
                    $newSlug = strtolower(str_replace([' ', '(', ')', '-'], ['', '', '', ''], $newTitle));
                    
                    // Check if exists first
                    $check = $pdo->prepare("SELECT id FROM position_levels WHERE slug = ?");
                    $check->execute([$newSlug]);
                    if ($check->fetch()) {
                        echo "  ⏭️  Skipping duplicate '{$newTitle}'\n";
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO position_levels 
                            (title, slug, level_number, color, icon, course_id, education_level_id, order_index, is_active)
                            SELECT ?, ?, level_number, color, icon, ?, ?, order_index, is_active
                            FROM position_levels WHERE id = ?
                        ");
                        $stmt->execute([$newTitle, $newSlug, $courseId, $eduLevel['id'], $level['id']]);
                        echo "  ✅ Created '{$newTitle}' → {$courseName}\n";
                    }
                }
            }
        }
    }
    
    echo "\n✅ Fix completed successfully!\n";
    echo "\nRefresh the Position Levels page to see the changes.\n";
    
} catch (Exception $e) {
    echo "❌ Fix failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
