<?php
/**
 * Seed Data: Level 5 Civil Engineering Syllabus
 * 
 * This creates the complete syllabus structure for:
 * - First Paper (Part I: GK + Management, Part II: Technical)
 * - Second Paper (Technical subjects)
 */

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    $conn->beginTransaction();

    // ========== FIRST PAPER ==========
    
    // Root: First Paper
    $conn->exec("INSERT INTO syllabus_nodes (title, slug, type, level, `order`) VALUES 
        ('Loksewa Level 5 - First Paper', 'level-5-first-paper', 'paper', 'Level 5', 1)");
    $firstPaperId = $conn->lastInsertId();
    echo "✅ Created First Paper (ID: $firstPaperId)\n";

    // Part I: General Knowledge & Management
    $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
        ($firstPaperId, 'Part I: General Knowledge & Management', 'part-1-gk-mgmt', 'part', 1)");
    $part1Id = $conn->lastInsertId();
    echo "✅ Created Part I (ID: $part1Id)\n";

    // Section A: General Knowledge (10 questions)
    $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
        ($part1Id, 'Section A: General Knowledge', 'section-a-gk', 'section', 1)");
    $sectionAId = $conn->lastInsertId();
    
    // GK Units
    $gkUnits = [
        ['Geography of Nepal', 'geography-nepal'],
        ['History of Nepal', 'history-nepal'],
        ['Current Affairs', 'current-affairs'],
        ['Nepali Constitution', 'nepali-constitution'],
        ['International Relations', 'international-relations'],
        ['Economics', 'economics'],
        ['Science & Technology', 'science-technology'],
        ['Sports', 'sports'],
        ['Literature & Arts', 'literature-arts'],
        ['General Science', 'general-science']
    ];
    
    foreach ($gkUnits as $index => $unit) {
        $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
            ($sectionAId, '{$unit[0]}', '{$unit[1]}', 'unit', " . ($index + 1) . ")");
    }
    echo "✅ Created 10 GK units\n";

    // Section B: Management (10 questions)
    $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
        ($part1Id, 'Section B: Management', 'section-b-management', 'section', 2)");
    $sectionBId = $conn->lastInsertId();
    
    // Management Units
    $mgmtUnits = [
        ['Principles of Management', 'principles-management'],
        ['Organizational Behavior', 'organizational-behavior'],
        ['Human Resource Management', 'hr-management'],
        ['Financial Management', 'financial-management'],
        ['Project Management', 'project-management'],
        ['Strategic Planning', 'strategic-planning'],
        ['Leadership & Motivation', 'leadership-motivation'],
        ['Communication Skills', 'communication-skills'],
        ['Decision Making', 'decision-making'],
        ['Office Management', 'office-management']
    ];
    
    foreach ($mgmtUnits as $index => $unit) {
        $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
            ($sectionBId, '{$unit[0]}', '{$unit[1]}', 'unit', " . ($index + 1) . ")");
    }
    echo "✅ Created 10 Management units\n";

    // Part II: Technical Subjects (30 questions from 5 subjects)
    $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
        ($firstPaperId, 'Part II: Technical Subjects', 'part-2-technical', 'part', 2)");
    $part2Id = $conn->lastInsertId();
    echo "✅ Created Part II (ID: $part2Id)\n";

    // Technical Subjects
    $technicalSubjects = [
        [
            'name' => 'Surveying',
            'slug' => 'surveying',
            'units' => [
                'Basic Surveying Principles',
                'Leveling & Contouring',
                'Theodolite Survey',
                'Traverse Survey',
                'Curve Setting',
                'Total Station',
                'GPS & GIS Basics',
                'Hydrographic Survey'
            ]
        ],
        [
            'name' => 'Building Materials',
            'slug' => 'building-materials',
            'units' => [
                'Cement & Concrete',
                'Bricks & Blocks',
                'Steel & Reinforcement',
                'Timber & Wood Products',
                'Stone & Aggregates',
                'Paints & Finishes',
                'Glass & Ceramics',
                'Modern Building Materials'
            ]
        ],
        [
            'name' => 'Structural Engineering',
            'slug' => 'structural-engineering',
            'units' => [
                'Mechanics of Structures',
                'Strength of Materials',
                'RCC Design Basics',
                'Steel Structure Design',
                'Foundation Design',
                'Earthquake Engineering',
                'Load Calculations',
                'Structural Analysis'
            ]
        ],
        [
            'name' => 'Hydraulics & Irrigation',
            'slug' => 'hydraulics-irrigation',
            'units' => [
                'Fluid Mechanics',
                'Open Channel Flow',
                'Pipe Flow',
                'Pumps & Turbines',
                'Irrigation Systems',
                'Canal Design',
                'Water Resources',
                'Hydrology Basics'
            ]
        ],
        [
            'name' => 'Transportation Engineering',
            'slug' => 'transportation-engineering',
            'units' => [
                'Highway Engineering',
                'Geometric Design',
                'Pavement Design',
                'Traffic Engineering',
                'Road Construction',
                'Bridge Engineering Basics',
                'Railway Engineering',
                'Airport Engineering Basics'
            ]
        ]
    ];

    foreach ($technicalSubjects as $subjectIndex => $subject) {
        // Create subject as section
        $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
            ($part2Id, '{$subject['name']}', '{$subject['slug']}', 'section', " . ($subjectIndex + 1) . ")");
        $sectionId = $conn->lastInsertId();
        
        // Create units
        foreach ($subject['units'] as $unitIndex => $unitName) {
            $unitSlug = strtolower(str_replace([' ', '&'], ['-', 'and'], $unitName));
            $conn->exec("INSERT INTO syllabus_nodes (parent_id, title, slug, type, `order`) VALUES 
                ($sectionId, '$unitName', '$unitSlug', 'unit', " . ($unitIndex + 1) . ")");
        }
        
        echo "✅ Created {$subject['name']} with " . count($subject['units']) . " units\n";
    }

    $conn->commit();
    echo "\n🎉 Level 5 Civil Engineering Syllabus Seeded Successfully!\n";
    echo "📊 Summary:\n";
    echo "   - 1 Paper (First Paper)\n";
    echo "   - 2 Parts (Part I: GK+Mgmt, Part II: Technical)\n";
    echo "   - 7 Sections (2 in Part I, 5 in Part II)\n";
    echo "   - 60 Units (20 in Part I, 40 in Part II)\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error in seeding: " . $e->getMessage() . "\n";
    exit(1);
}
?>
