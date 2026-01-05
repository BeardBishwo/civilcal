<?php
/**
 * Seed Complete Loksewa Engineering Hierarchy Data
 * Populates: Courses → Education Levels → Categories → Sub Categories → Position Levels
 * AND ensures Position Levels are correctly linked to Courses/Education Levels
 */

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "🌱 Starting Complete Loksewa Engineering Data Seeding...\n\n";
    
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
        $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE slug = ? AND type = 'course'");
        $stmt->execute([$course['slug']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
             $courseIds[$course['slug']] = $existing['id'];
             echo "  ✓ {$course['title']} (Existing)\n";
        } else {
             $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (title, slug, type, order_index, is_active) VALUES (?, ?, 'course', ?, 1)");
             $stmt->execute([$course['title'], $course['slug'], $course['order']]);
             $courseIds[$course['slug']] = $pdo->lastInsertId();
             echo "  ✓ {$course['title']} (Created)\n";
        }
    }
    
    // Create reverse lookup for ID -> Title
    $courseTitleById = [];
    foreach ($courseIds as $slug => $id) {
        // Find title from original array or DB query (simplest to map back if consistent)
        foreach ($courses as $c) {
            if ($c['slug'] === $slug) {
                $courseTitleById[$id] = $c['title'];
                break;
            }
        }
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
    
    $levelIds = []; // $levelIds[course_slug][base_slug] = db_id
    foreach ($courses as $course) {
        foreach ($educationLevels as $level) {
            $parentId = $courseIds[$course['slug']];
            $title = $level['title'] . ' (' . $course['title'] . ')';
            $slug = $level['slug'] . '-' . $course['slug'];
            
            $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE slug = ? AND type = 'education_level'");
            $stmt->execute([$slug]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                 $levelIds[$course['slug']][$level['slug']] = $existing['id'];
                 echo "  ✓ {$title} (Existing)\n";
            } else {
                 $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'education_level', ?, 1)");
                 $stmt->execute([$parentId, $title, $slug, $level['order']]);
                 $levelIds[$course['slug']][$level['slug']] = $pdo->lastInsertId();
                 echo "  ✓ {$title} (Created)\n";
            }
        }
    }
    
    // ==========================================
    // 3. MAIN CATEGORIES (Subject Areas)
    // ==========================================
    echo "\n📖 Creating Main Categories (Subject Areas)...\n";
    
    $categories = [
        'civil-engineering' => [
            ['title' => 'Structural Engineering', 'slug' => 'structural-engineering'],
            ['title' => 'Transportation Engineering', 'slug' => 'transportation-engineering'],
            ['title' => 'Water Resources Engineering', 'slug' => 'water-resources-engineering'],
            ['title' => 'Geotechnical Engineering', 'slug' => 'geotechnical-engineering'],
            ['title' => 'Construction Management', 'slug' => 'construction-management'],
        ],
        'electrical-engineering' => [
            ['title' => 'Power Systems', 'slug' => 'power-systems'],
            ['title' => 'Control Systems', 'slug' => 'control-systems'],
            ['title' => 'Electronics & Communication', 'slug' => 'electronics-communication'],
            ['title' => 'Electrical Machines', 'slug' => 'electrical-machines'],
            ['title' => 'Power Electronics', 'slug' => 'power-electronics'],
        ],
        'computer-it-engineering' => [
            ['title' => 'Programming & Algorithms', 'slug' => 'programming-algorithms'],
            ['title' => 'Database Management', 'slug' => 'database-management'],
            ['title' => 'Networking & Security', 'slug' => 'networking-security'],
            ['title' => 'Web Development', 'slug' => 'web-development'],
            ['title' => 'Operating Systems', 'slug' => 'operating-systems'],
        ],
        'mechanical-engineering' => [
            ['title' => 'Thermodynamics', 'slug' => 'thermodynamics'],
            ['title' => 'Fluid Mechanics', 'slug' => 'fluid-mechanics'],
            ['title' => 'Machine Design', 'slug' => 'machine-design'],
            ['title' => 'Manufacturing Processes', 'slug' => 'manufacturing-processes'],
            ['title' => 'Mechanics of Materials', 'slug' => 'mechanics-materials'],
        ],
        'agriculture-engineering' => [
            ['title' => 'Crop Science', 'slug' => 'crop-science'],
            ['title' => 'Soil Science', 'slug' => 'soil-science'],
            ['title' => 'Irrigation Engineering', 'slug' => 'irrigation-engineering'],
            ['title' => 'Agricultural Economics', 'slug' => 'agricultural-economics'],
            ['title' => 'Farm Machinery', 'slug' => 'farm-machinery'],
        ],
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
            if (!isset($levelIds[$courseSlug]['level-5-sub-engineer'])) {
                echo "  ⚠️ Skipping {$cat['title']} - Parent not found\n";
                continue;
            }
            $parentId = $levelIds[$courseSlug]['level-5-sub-engineer'];
            
            $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE slug = ? AND type = 'category'");
            $stmt->execute([$cat['slug']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $categoryIds[$courseSlug][$cat['slug']] = $existing['id'];
                echo "  ✓ {$cat['title']} (Existing)\n";
            } else {
                $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'category', ?, 1)");
                $stmt->execute([$parentId, $cat['title'], $cat['slug'], $order]);
                $categoryIds[$courseSlug][$cat['slug']] = $pdo->lastInsertId();
                echo "  ✓ {$cat['title']} ({$courseSlug}) (Created)\n";
            }
            $order++;
        }
    }
    
    // ==========================================
    // 4. SUB CATEGORIES (Topics)
    // ==========================================
    echo "\n📝 Creating Sub Categories (Topics)...\n";
    
    $subCategories = [
        'structural-engineering' => ['Analysis of Structures', 'Design of RCC Structures', 'Steel Structures', 'Foundation Engineering', 'Earthquake Engineering'],
        'transportation-engineering' => ['Highway Engineering', 'Traffic Engineering', 'Pavement Design', 'Railway Engineering', 'Airport Engineering'],
        'power-systems' => ['Power Generation', 'Transmission & Distribution', 'Power System Protection', 'Load Flow Analysis', 'Renewable Energy Systems'],
        'programming-algorithms' => ['Data Structures', 'Algorithm Design', 'Object-Oriented Programming', 'Problem Solving Techniques', 'Complexity Analysis'],
        'thermodynamics' => ['Laws of Thermodynamics', 'Heat Transfer', 'Refrigeration & Air Conditioning', 'IC Engines', 'Gas Turbines'],
    ];
    
    $subOrder = 1;
    foreach ($subCategories as $catSlug => $topics) {
        foreach ($categoryIds as $courseSlug => $cats) {
            if (isset($cats[$catSlug])) {
                $parentId = $cats[$catSlug];
                foreach ($topics as $topic) {
                    $slug = strtolower(str_replace([' ', '&'], ['-', 'and'], $topic));

                    $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE slug = ? AND type = 'sub_category'");
                    $stmt->execute([$slug]);
                    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existing) {
                         echo "  ✓ {$topic} (Existing)\n";
                    } else {
                         $stmt = $pdo->prepare("INSERT INTO syllabus_nodes (parent_id, title, slug, type, order_index, is_active) VALUES (?, ?, ?, 'sub_category', ?, 1)");
                         $stmt->execute([$parentId, $topic, $slug, $subOrder]);
                         echo "  ✓ {$topic} (Created)\n";
                    }
                    $subOrder++;
                }
                break;
            }
        }
    }
    
    // ==========================================
    // 5. POSITION LEVELS (Directly Linked)
    // ==========================================
    echo "\n🏆 Creating Linked Position Levels...\n";
    
    $abstractPositionLevels = [
        ['title' => 'Level 4 (Assistant)', 'level_number' => 4, 'color' => '#10b981', 'icon' => 'fa-user'],
        ['title' => 'Level 5 (Sub-Engineer)', 'level_number' => 5, 'color' => '#3b82f6', 'icon' => 'fa-user-tie'],
        ['title' => 'Level 6 (Engineer)', 'level_number' => 6, 'color' => '#8b5cf6', 'icon' => 'fa-user-graduate'],
        ['title' => 'Level 7 (Senior)', 'level_number' => 7, 'color' => '#f59e0b', 'icon' => 'fa-user-shield'],
        ['title' => 'Level 8 (Chief Engineer)', 'level_number' => 8, 'color' => '#ef4444', 'icon' => 'fa-crown'],
        ['title' => 'Computer Operator (Level 4)', 'level_number' => 4, 'color' => '#06b6d4', 'icon' => 'fa-desktop'],
        ['title' => 'IT Officer (Level 5)', 'level_number' => 5, 'color' => '#14b8a6', 'icon' => 'fa-laptop-code'],
        ['title' => 'Agriculture Officer (Level 5)', 'level_number' => 5, 'color' => '#84cc16', 'icon' => 'fa-seedling'],
        ['title' => 'Architect (Level 6)', 'level_number' => 6, 'color' => '#a855f7', 'icon' => 'fa-drafting-compass'],
    ];
    
    // Course Slug to ID lookup
    $courseIdBySlug = $courseIds; 
    
    foreach ($abstractPositionLevels as $pos) {
        $title = $pos['title'];
        $levelNumber = $pos['level_number'];
        
        $targetCourses = [];
        // Determine mapping logic
        if (stripos($title, 'Computer') !== false || stripos($title, 'IT') !== false) {
            $targetCourses = ['computer-it-engineering'];
        } elseif (stripos($title, 'Agriculture') !== false) {
            $targetCourses = ['agriculture-engineering'];
        } elseif (stripos($title, 'Architect') !== false) {
            $targetCourses = ['architecture'];
        } elseif (stripos($title, 'Electrical') !== false) {
             $targetCourses = ['electrical-engineering'];
        } elseif (stripos($title, 'Mechanical') !== false) {
             $targetCourses = ['mechanical-engineering'];
        } elseif (stripos($title, 'Civil') !== false) {
             $targetCourses = ['civil-engineering'];
        } else {
             // Generic fallbacks
             $targetCourses = ['civil-engineering', 'electrical-engineering', 'mechanical-engineering'];
        }
        
        foreach ($targetCourses as $courseSlug) {
            if (!isset($courseIdBySlug[$courseSlug])) {
                echo "  ⚠️ Skipping {$title} for {$courseSlug} (Course not found)\n";
                continue;
            }
            
            $courseId = $courseIdBySlug[$courseSlug];
            $courseName = $courseTitleById[$courseId] ?? $courseSlug; // Fallback
            
            // Generate Specific Title & Slug
            $specificTitle = $title . " (" . $courseName . ")";
            $specificSlug = strtolower(str_replace([' ', '(', ')', '-'], ['', '', '', ''], $specificTitle));
             // Ensure uniqueness if multiple spaces etc
            $specificSlug = preg_replace('/\s+/', '-', trim($specificSlug));

            // Find Education Level ID
            // Logic: type=education_level, parent_id = courseId, title LIKE %Level X%
            $stmt = $pdo->prepare("SELECT id FROM syllabus_nodes WHERE type = 'education_level' AND parent_id = ? AND title LIKE ? LIMIT 1");
            $stmt->execute([$courseId, "%Level {$levelNumber}%"]);
            $eduLevel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$eduLevel) {
                 echo "  ⚠️ Education Level {$levelNumber} not found for {$courseSlug}\n";
                 continue;
            }
            $eduLevelId = $eduLevel['id'];

            // Check if Position Level exists
            $check = $pdo->prepare("SELECT id FROM position_levels WHERE slug = ?");
            $check->execute([$specificSlug]);
            if ($check->fetch()) {
                echo "  ✓ {$specificTitle} (Existing)\n";
            } else {
                $stmt = $pdo->prepare("INSERT INTO position_levels (title, slug, level_number, color, icon, course_id, education_level_id, order_index, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$specificTitle, $specificSlug, $levelNumber, $pos['color'], $pos['icon'], $courseId, $eduLevelId, $levelNumber]);
                 echo "  ✓ {$specificTitle} (Created)\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ COMPLETE QUIZ HIERARCHY SEEDING FINISHED!\n";
    echo str_repeat("=", 50) . "\n\n";

} catch (Exception $e) {
    echo "❌ Seeding failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
