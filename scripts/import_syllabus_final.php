<?php

/**
 * Final Syllabus Import Script
 * 
 * Hierarchy:
 * 1. Course: Civil Engineering
 * 2. Education Level: Diploma in Civil Engineering
 * 3. Position: Sub Engineer
 * 4. Main Category: [Subjects] (e.g. 1. General Awareness)
 * 5. Sub Category: [1.1 ...]
 * 6. Topic: [1.1.1 ...]
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/bootstrap.php';

use App\Services\SyllabusService;
use App\Core\Database;

$service = new SyllabusService();
$db = Database::getInstance();

echo "Starting Final Import...\n";

// 1. Truncate Table
echo "Clearing existing syllabus nodes...\n";
// Disable foreign key checks for truncation
$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->getPdo()->exec("TRUNCATE TABLE syllabus_nodes");
$db->getPdo()->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "Table truncated.\n";

// Helper to create node
function createNode($service, $title, $type, $parentId = null, $order = 0)
{
    // Determine type for DB
    // Map 'main_category' -> 'category' if needed, but user said 'main category'. 
    // Types available: course, paper, part, section, sub-topic, topic, education_level, category, sub_category, position.
    // If 'position' type doesn't exist in ENUM, we might have issue. 
    // We saw types: course, paper, part, section, sub-topic, topic.
    // So 'education_level', 'category', 'sub_category', 'position' might need to be added to ENUM or valid types.
    // Assuming DB accepts string or we rely on what works. 

    // Mapping internal types to DB types if necessary. 
    // Based on user request: 
    // Main Category -> category
    // Sub Category -> sub_category

    $data = [
        'parent_id' => $parentId,
        'title' => $title,
        'type' => $type,
        'description' => '',
        'order' => $order,
        'is_active' => 1
    ];

    try {
        return $service->createNode($data);
    } catch (Exception $e) {
        echo "Error creating '$title': " . $e->getMessage() . "\n";
        return null;
    }
}

// 2. Create Static Top Levels
$courseId = createNode($service, "Civil Engineering", "course", null, 1);
echo "Created Course: Civil Engineering [$courseId]\n";

$educationId = createNode($service, "Diploma in Civil Engineering", "education_level", $courseId, 1);
echo "Created Education: Diploma in Civil Engineering [$educationId]\n";

$positionId = createNode($service, "Sub Engineer", "position", $educationId, 1);
echo "Created Position: Sub Engineer [$positionId]\n";


// 3. Parse File
$filePath = ROOT_PATH . '/001_Sub Engineer Syllabus.md.processed';
if (!file_exists($filePath)) {
    // Try original if processed not found (though we renamed it)
    $filePath = ROOT_PATH . '/001_Sub Engineer Syllabus.md';
}
if (!file_exists($filePath)) {
    die("File not found.\n");
}

$lines = file($filePath);

$categoryId = null;
$subCategoryId = null;

foreach ($lines as $lineIndex => $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Ignore Headers
    if (preg_match('/^\*\*(Paper|Part|Section).*?\*\*/i', $line)) {
        echo "Skipping structure line: $line\n";
        continue;
    }

    // 4. Main Category (Level 4): 1. Subject
    if (preg_match('/^(\d+)(\\|\.)\s+(.*)/', $line, $matches)) {
        $title = trim($matches[3]);
        // Clean "(Marks)"
        $title = preg_replace('/\(.*Marks.*\)/', '', $title);
        $title = trim($title);

        $categoryId = createNode($service, $title, 'category', $positionId, $lineIndex);
        $subCategoryId = null;
        echo "  Created Category: $title\n";
        continue;
    }

    // 5. Sub Category (Level 5): 1.1. Sub-Subject
    if (preg_match('/^\s*\*?\s*(\d+\.\d+)\.?\s+(.*)/', $line, $matches) && !preg_match('/^\s*\*?\s*(\d+\.\d+\.\d+)/', $line)) {
        $title = trim($matches[2]);
        // Parent is Category. Fallback to Position if Category not set (shouldn't happen with valid file)
        $parent = $categoryId ?? $positionId;

        $subCategoryId = createNode($service, $title, 'sub_category', $parent, $lineIndex);
        echo "    Created Sub-Category: $title\n";
        continue;
    }

    // 6. Topic (Level 6): 2.1.1. Topic
    if (preg_match('/^\s*\*?\s*(\d+\.\d+\.\d+)\.?\s+(.*)/', $line, $matches)) {
        $title = trim($matches[2]);
        $parent = $subCategoryId ?? $categoryId;

        createNode($service, $title, 'topic', $parent, $lineIndex);
        echo "      Created Topic: $title\n";
        continue;
    }
}

echo "Import Completed Successfully.\n";
