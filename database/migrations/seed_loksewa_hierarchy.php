<?php
/**
 * Seed Complete Loksewa Engineering Hierarchy Data
 * Populates: Courses → Education Levels → Categories → Sub Categories → Position Levels
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🌱 Starting Loksewa Engineering Data Seeding...\n\n";
    
    // ==========================================
    // 1. COURSES (Engineering Disciplines)
    // ==========================================
    echo "📚 Creating Courses (Engineering Disciplines)...\n";
    
    $courses = [
        ['title' => 'Civil Engineering', 'slug' => 'civil-engineering', 'order' => 1],
        ['title' => 'Electrical Engineering', 'slug' => 'electrical-engineering', 'order' => 2],
        ['title' => 'Computer/IT Engineering', 'slug' => 'computer-it-engineering', 'order' => 3],
        ['title' => 'Mechanical Engineering', 'slug' => 'mechanical-engineering', 'order' => 4],
        ['title' => 'Agriculture Engineering', 'slug' => 'agriculture-engineering', 'order' => 5],
        ['title' => 'Architecture', 'slug' => 'architecture', 'order' => 6],
    ];
    
    $courseIds = [];
    foreach ($courses as $course) {
        $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (title, slug, type, order_index, is_active) VALUES (?, ?, 'course', ?, 1)");
        $stmt->execute([$course['title'], $course['slug'], $course['order']]);
        $courseIds[$course['slug']] = $pdo->lastInsertId();
        echo "  ✓ {$course['title']}\n";
    }
    
    // ==========================================
    // 2. EDUCATION LEVELS (Position Levels)
    // ==========================================
    echo "\n🎓 Creating Education Levels (Position Levels)...\n";
    
    $educationLevels = [
        ['title' => 'Level 4 - Assistant', 'slug' => 'level-4-assistant', 'order' => 1],
        ['title' => 'Level 5 - Sub-Engineer/Officer', 'slug' => 'level-5-sub-engineer', 'order' => 2],
        ['title' => 'Level 6 - Engineer/Officer', 'slug' => 'level-6-engineer', 'order' => 3],
        ['title' => 'Level 7 - Senior Engineer', 'slug' => 'level-7-senior', 'order' => 4],
        ['title' => 'Level 8 - Chief Engineer', 'slug' => 'level-8-chief', 'order' => 5],
    ];
    
    $levelIds = [];
    foreach ($courses as $course) {
        foreach ($educationLevels as $level) {
            $parentId = $courseIds[$course['slug']];
            $title = $level['title'] . ' (' . $course['title'] . ')';
            $slug = $level['slug'] . '-' . $course['slug'];
            
            $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'education_level', ?, 1)");
            $stmt->execute([$parentId, $title, $slug, $level['order']]);
            $levelIds[$course['slug']][$level['slug']] = $pdo->lastInsertId();
            echo "  ✓ {$title}\n";
        }
    }
    
    // ==========================================
    // 3. MAIN CATEGORIES (Subject Areas)
    // ==========================================
    echo "\n📖 Creating Main Categories (Subject Areas)...\n";
    
    $categories = [
        // Civil Engineering Categories
        'civil-engineering' => [
            ['title' => 'Structural Engineering', 'slug' => 'structural-engineering'],
            ['title' => 'Transportation Engineering', 'slug' => 'transportation-engineering'],
            ['title' => 'Water Resources Engineering', 'slug' => 'water-resources-engineering'],
            ['title' => 'Geotechnical Engineering', 'slug' => 'geotechnical-engineering'],
            ['title' => 'Construction Management', 'slug' => 'construction-management'],
        ],
        // Electrical Engineering Categories
        'electrical-engineering' => [
            ['title' => 'Power Systems', 'slug' => 'power-systems'],
            ['title' => 'Control Systems', 'slug' => 'control-systems'],
            ['title' => 'Electronics & Communication', 'slug' => 'electronics-communication'],
            ['title' => 'Electrical Machines', 'slug' => 'electrical-machines'],
            ['title' => 'Power Electronics', 'slug' => 'power-electronics'],
        ],
        // Computer/IT Engineering Categories
        'computer-it-engineering' => [
            ['title' => 'Programming & Algorithms', 'slug' => 'programming-algorithms'],
            ['title' => 'Database Management', 'slug' => 'database-management'],
            ['title' => 'Networking & Security', 'slug' => 'networking-security'],
            ['title' => 'Web Development', 'slug' => 'web-development'],
            ['title' => 'Operating Systems', 'slug' => 'operating-systems'],
        ],
        // Mechanical Engineering Categories
        'mechanical-engineering' => [
            ['title' => 'Thermodynamics', 'slug' => 'thermodynamics'],
            ['title' => 'Fluid Mechanics', 'slug' => 'fluid-mechanics'],
            ['title' => 'Machine Design', 'slug' => 'machine-design'],
            ['title' => 'Manufacturing Processes', 'slug' => 'manufacturing-processes'],
            ['title' => 'Mechanics of Materials', 'slug' => 'mechanics-materials'],
        ],
        // Agriculture Engineering Categories
        'agriculture-engineering' => [
            ['title' => 'Crop Science', 'slug' => 'crop-science'],
            ['title' => 'Soil Science', 'slug' => 'soil-science'],
            ['title' => 'Irrigation Engineering', 'slug' => 'irrigation-engineering'],
            ['title' => 'Agricultural Economics', 'slug' => 'agricultural-economics'],
            ['title' => 'Farm Machinery', 'slug' => 'farm-machinery'],
        ],
        // Architecture Categories
        'architecture' => [
            ['title' => 'Architectural Design', 'slug' => 'architectural-design'],
            ['title' => 'Building Construction', 'slug' => 'building-construction'],
            ['title' => 'Urban Planning', 'slug' => 'urban-planning'],
            ['title' => 'Building Services', 'slug' => 'building-services'],
            ['title' => 'History of Architecture', 'slug' => 'history-architecture'],
        ],
    ];
    
    $categoryIds = [];
    $order = 1;
    foreach ($categories as $courseSlug => $cats) {
        foreach ($cats as $cat) {
            // Add to Level 5 (most common level for Loksewa)
            $parentId = $levelIds[$courseSlug]['level-5-sub-engineer'];
            
            $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'category', ?, 1)");
            $stmt->execute([$parentId, $cat['title'], $cat['slug'], $order]);
            $categoryIds[$courseSlug][$cat['slug']] = $pdo->lastInsertId();
            echo "  ✓ {$cat['title']} ({$courseSlug})\n";
            $order++;
        }
    }
    
    // ==========================================
    // 4. SUB CATEGORIES (Topics)
    // ==========================================
    echo "\n📝 Creating Sub Categories (Topics)...\n";
    
    $subCategories = [
        // Civil - Structural Engineering
        'structural-engineering' => [
            'Analysis of Structures',
            'Design of RCC Structures',
            'Steel Structures',
            'Foundation Engineering',
            'Earthquake Engineering',
        ],
        // Civil - Transportation
        'transportation-engineering' => [
            'Highway Engineering',
            'Traffic Engineering',
            'Pavement Design',
            'Railway Engineering',
            'Airport Engineering',
        ],
        // Electrical - Power Systems
        'power-systems' => [
            'Power Generation',
            'Transmission & Distribution',
            'Power System Protection',
            'Load Flow Analysis',
            'Renewable Energy Systems',
        ],
        // Computer - Programming
        'programming-algorithms' => [
            'Data Structures',
            'Algorithm Design',
            'Object-Oriented Programming',
            'Problem Solving Techniques',
            'Complexity Analysis',
        ],
        // Mechanical - Thermodynamics
        'thermodynamics' => [
            'Laws of Thermodynamics',
            'Heat Transfer',
            'Refrigeration & Air Conditioning',
            'IC Engines',
            'Gas Turbines',
        ],
    ];
    
    $subOrder = 1;
    foreach ($subCategories as $catSlug => $topics) {
        // Find the category ID
        foreach ($categoryIds as $courseSlug => $cats) {
            if (isset($cats[$catSlug])) {
                $parentId = $cats[$catSlug];
                
                foreach ($topics as $topic) {
                    $slug = strtolower(str_replace([' ', '&'], ['-', 'and'], $topic));
                    $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'sub_category', ?, 1)");
                    $stmt->execute([$parentId, $topic, $slug, $subOrder]);
                    echo "  ✓ {$topic}\n";
                    $subOrder++;
                }
                break;
            }
        }
    }
    
    // ==========================================
    // 5. POSITION LEVELS (Already created, just update)
    // ==========================================
    echo "\n🏆 Updating Position Levels...\n";
    
    $positionLevels = [
        ['title' => 'Level 4 - Assistant Sub-Engineer', 'level_number' => 4, 'color' => '#10b981', 'icon' => 'fa-user'],
        ['title' => 'Level 5 - Sub-Engineer', 'level_number' => 5, 'color' => '#3b82f6', 'icon' => 'fa-user-tie'],
        ['title' => 'Level 6 - Engineer', 'level_number' => 6, 'color' => '#8b5cf6', 'icon' => 'fa-user-graduate'],
        ['title' => 'Level 7 - Senior Engineer', 'level_number' => 7, 'color' => '#f59e0b', 'icon' => 'fa-user-shield'],
        ['title' => 'Level 8 - Chief Engineer', 'level_number' => 8, 'color' => '#ef4444', 'icon' => 'fa-crown'],
        ['title' => 'Computer Operator (Level 4)', 'level_number' => 4, 'color' => '#06b6d4', 'icon' => 'fa-desktop'],
        ['title' => 'IT Officer (Level 5)', 'level_number' => 5, 'color' => '#14b8a6', 'icon' => 'fa-laptop-code'],
        ['title' => 'Agriculture Officer (Level 5)', 'level_number' => 5, 'color' => '#84cc16', 'icon' => 'fa-seedling'],
        ['title' => 'Architect (Level 6)', 'level_number' => 6, 'color' => '#a855f7', 'icon' => 'fa-drafting-compass'],
    ];
    
    foreach ($positionLevels as $pos) {
        $slug = strtolower(str_replace([' ', '(', ')', '-'], ['', '', '', ''], $pos['title']));
        $slug = preg_replace('/\s+/', '-', trim($slug));
        
        $stmt = $pdo->prepare("INSERT INTO position_levels (title, slug, level_number, color, icon, order_index, is_active) 
                               VALUES (?, ?, ?, ?, ?, ?, 1)
                               ON DUPLICATE KEY UPDATE 
                               title = VALUES(title), 
                               level_number = VALUES(level_number),
                               color = VALUES(color),
                               icon = VALUES(icon)");
        $stmt->execute([$pos['title'], $slug, $pos['level_number'], $pos['color'], $pos['icon'], $pos['level_number']]);
        echo "  ✓ {$pos['title']}\n";
    }
    
    // ==========================================
    // SUMMARY
    // ==========================================
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ DATA SEEDING COMPLETED SUCCESSFULLY!\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "📊 Summary:\n";
    echo "  • Courses: " . count($courses) . " engineering disciplines\n";
    echo "  • Education Levels: " . (count($courses) * count($educationLevels)) . " position levels\n";
    echo "  • Main Categories: " . array_sum(array_map('count', $categories)) . " subject areas\n";
    echo "  • Sub Categories: " . array_sum(array_map('count', $subCategories)) . " topics\n";
    echo "  • Position Levels: " . count($positionLevels) . " position tags\n\n";
    
    echo "🎯 Next Steps:\n";
    echo "  1. Visit: http://localhost/Bishwo_Calculator/admin/quiz/courses\n";
    echo "  2. Verify all data is populated correctly\n";
    echo "  3. Start creating questions!\n\n";
    
} catch (Exception $e) {
    echo "❌ Seeding failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
