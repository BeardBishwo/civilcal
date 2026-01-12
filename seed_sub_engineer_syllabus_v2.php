<?php

/**
 * Sub Engineer Syllabus Seeder v2
 * 1. Creates Master Data (Subjects/Topics)
 * 2. Populates Syllabus Nodes (The actual tree hidden in the Grid)
 */

require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();

echo "🚀 Starting Sub Engineer Syllabus Seeder (Master + Grid)...\n\n";

// Configuration
$levelName = 'sub engineer'; // This must match the URL param (decoded)
$courseName = 'Civil Engineering';
$eduLevelName = 'Sub Engineer';
$posLevelName = 'Sub Engineer';

try {
    // 1. Get/Create Categories
    $categoryStmt = $db->prepare("SELECT id FROM quiz_categories WHERE name = ? LIMIT 1");
    $categoryStmt->execute([$courseName]);
    $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        $db->prepare("INSERT INTO quiz_categories (name, slug, status, is_active) VALUES (?, ?, ?, ?)")
            ->execute([$courseName, 'civil-engineering', 1, 1]);
        $categoryId = $db->lastInsertId();
    } else {
        $categoryId = $category['id'];
    }

    // 2. Clear existing syllabus nodes for this level to avoid duplicates
    // THIS RESET IS IMPORTANT FOR A CLEAN SEED
    echo "🧹 Clearing existing syllabus nodes for '$levelName'...\n";
    $db->prepare("DELETE FROM syllabus_nodes WHERE level = ?")->execute([$levelName]);

    // 3. Define Structure
    $subjects = [
        [
            'name' => 'Surveying',
            'weight' => 0, // Set weight if known
            'topics' => [
                'General - Classification',
                'General - Principle of surveying',
                'General - Selection of suitable method',
                'General - Scales, plans and maps',
                'General - Entry into survey field books and level books',
                'Levelling - Methods of levelling',
                'Levelling - Levelling instruments and accessories',
                'Levelling - Principles of levelling',
                'Chain surveying',
                'Compass surveying',
                'Plane Tabling - Equipments required',
                'Plane Tabling - Methods of plane tabling',
                'Plane Tabling - Two and three point problems',
                'Theodolite and Traverse Surveying - Basic difference between different theodolites',
                'Theodolite and Traverse Surveying - Temporary adjustments of theodolites',
                'Theodolite and Traverse Surveying - Fundamental lines and desired relations',
                'Theodolite and Traverse Surveying - Tacheometry: stadia method',
                'Theodolite and Traverse Surveying - Trigonometrical levelling',
                'Theodolite and Traverse Surveying - Checks in closed traverse',
                'Contouring - Characteristics of contour lines',
                'Contouring - Methods of locating contours',
                'Contouring - Contour plotting',
                'Setting out - Small buildings',
                'Setting out - Simple curves'
            ]
        ],
        [
            'name' => 'Construction Materials',
            'topics' => [
                'Stone - Formation and availability of stones in Nepal',
                'Stone - Methods of laying and construction with various stones',
                'Cement - Different cements',
                'Cement - Storage and transport',
                'Cement - Admixtures',
                'Clay and clay products - Brick: type, manufacture, laying, bonds',
                'Paints and Varnishes',
                'Timber',
                'Lime',
                'Bitumen'
            ]
        ],
        [
            'name' => 'Mechanics of Materials and Structures',
            'topics' => [
                'Mechanics of materials',
                'Mechanics of beams',
                'Simple strut theory'
            ]
        ],
        [
            'name' => 'Hydraulics',
            'topics' => [
                'General - Properties of fluid',
                'General - Pressure and Pascal\'s law',
                'Hydro-kinematics and Hydro-dynamics',
                'Measurement of discharge',
                'Flows - Characteristics of pipe flow'
            ]
        ],
        [
            'name' => 'Soil Mechanics',
            'topics' => [
                'General - Soil types and classification',
                'General - Three phases of system of soils',
                'General - Unit weight of soil mass',
                'Soil water relation - Terzaghi\'s principle',
                'Soil water relation - Darcy\'s law',
                'Compaction of soil',
                'Shear strength of soil',
                'Earth Pressures',
                'Foundation engineering'
            ]
        ],
        [
            'name' => 'Structural Design',
            'topics' => [
                'R.C. Section in bending',
                'Shear and Bond for R.C. Sections',
                'Axially Loaded R.C. Columns',
                'Design and Drafting of R.C. structures'
            ]
        ],
        [
            'name' => 'Building Construction Technology',
            'topics' => [
                'Foundation',
                'Walls',
                'Damp proofing',
                'Concrete technology',
                'Wood work',
                'Flooring and finishing'
            ]
        ],
        [
            'name' => 'Water Supply and Sanitation Engineering',
            'topics' => [
                'Objectives of water supply system',
                'Source of water and its selection',
                'Gravity water supply system',
                'Design of sewer',
                'Excreta disposal in unsewered area'
            ]
        ],
        [
            'name' => 'Irrigation Engineering',
            'topics' => [
                'General - Advantages and disadvantages',
                'Water requirement',
                'Flow irrigation canals'
            ]
        ],
        [
            'name' => 'Highway Engineering',
            'topics' => [
                'General - Introduction and history',
                'General - Classification of road in Nepal',
                'General - Basic requirements of road alignment',
                'Geometric design',
                'Drainage System',
                'Road Pavement',
                'Road Machineries',
                'Road Construction Technology',
                'Bridge',
                'Road Maintenance and Repair',
                'Tracks and Trails'
            ]
        ],
        [
            'name' => 'Estimating and Costing',
            'topics' => [
                'General - Main items of work and units',
                'Rate analysis',
                'Specifications',
                'Valuation'
            ]
        ],
        [
            'name' => 'Construction Management',
            'topics' => [
                'Organization',
                'Site Management',
                'Contract Procedure',
                'Accounts',
                'Planning and Control'
            ]
        ],
        [
            'name' => 'Airport Engineering',
            'topics' => [
                'General - Introduction',
                'Design',
                'Airport maintenance'
            ]
        ]
    ];

    // 4. Loop & Insert
    $order = 0;

    foreach ($subjects as $sData) {
        $subjectName = $sData['name'];
        $subjectSlug = strtolower(str_replace(' ', '-', $subjectName));

        // A. Ensure Master Subject exists
        $subStmt = $db->prepare("SELECT id FROM quiz_subjects WHERE name = ? AND category_id = ?");
        $subStmt->execute([$subjectName, $categoryId]);
        $masterSubject = $subStmt->fetch(PDO::FETCH_ASSOC);

        if (!$masterSubject) {
            $db->prepare("INSERT INTO quiz_subjects (category_id, name, slug, is_active) VALUES (?, ?, ?, 1)")
                ->execute([$categoryId, $subjectName, $subjectSlug]);
            $masterSubjectId = $db->lastInsertId();
        } else {
            $masterSubjectId = $masterSubject['id'];
        }

        // B. Insert Syllabus Node (Main Category / Section)
        $order++;
        $db->prepare("INSERT INTO syllabus_nodes 
            (level, title, slug, type, parent_id, `order`, is_active, linked_category_id, linked_topic_id) 
            VALUES (:level, :title, :slug, 'section', NULL, :order, 1, :linked_cat, NULL)")
            ->execute([
                'level' => $levelName,
                'title' => $subjectName,
                'slug' => $subjectSlug,
                'order' => $order,
                'linked_cat' => $masterSubjectId // We link to quiz_subjects here based on current logic? 
                // WAIT: Controller 'manage' uses: sn.linked_category_id = qc.id (quiz_categories)
                // But default_api says: Main Category (section) -> select from quiz_subjects.
                // So linked_category_id should store quiz_subjects.id if Type is Section.
                // Verify Controller Join: LEFT JOIN quiz_categories qc ON sn.linked_category_id = qc.id
                // This implies linked_category_id points to quiz_categories table!
                // BUT user wants Main Category to select from SUBJECTS.
                // This mismatch might be key!

                // Let's check manage.php:
                // "Main Category" maps to type 'section'.
                // Dropdown populates from subjectsDB. subjectsDB comes from quiz_subjects.
                // When saved, it saves to `linked_category_id`.
                // BUT Controller joins `linked_category_id` with `quiz_categories`.
                // IF we are storing quiz_subject_id in linked_category_id, the join will fail or show wrong name 
                // UNLESS quiz_subjects and quiz_categories IDs coincidentally match.

                // Correct Logic:
                // If type='section' (Main Category), we want to link a Subject.
                // Should we store it in linked_subject_id? 
                // There is no linked_subject_id column in syllabus_nodes usually unless I added it?
                // Let's check schema/controller bulkSave.
                // bulkSave: 'linked_category_id' => ... 'linked_topic_id' => ...

                // If the user wants to link "Subjects", we should probably use a column for it.
                // OR we repurpose `linked_category_id` to store `quiz_subject_id`.

                // Let's stick to what allows the dropdown to work:
                // manage.php uses `linked_category_id` for 'Main Category'.
                // And populates it from `subjectsDB` (which is quiz_subjects).
                // So we definitely store `quiz_subjects.id` in `linked_category_id`.
                // The Controller Join `LEFT JOIN quiz_categories qc` is WRONG if we do this.
                // But for seeding, I must proceed with storing the ID.
            ]);
        $sectionNodeId = $db->lastInsertId();
        echo "   ✅ Added Section: $subjectName \n";

        // C. Topics
        foreach ($sData['topics'] as $topicName) {
            $topicSlug = strtolower(str_replace(' ', '-', substr($topicName, 0, 50)));

            // Ensure Master Topic exists
            $topStmt = $db->prepare("SELECT id FROM quiz_topics WHERE name = ? AND subject_id = ?");
            $topStmt->execute([$topicName, $masterSubjectId]);
            $masterTopic = $topStmt->fetch(PDO::FETCH_ASSOC);

            if (!$masterTopic) {
                $db->prepare("INSERT INTO quiz_topics (subject_id, name, slug, is_active) VALUES (?, ?, ?, 1)")
                    ->execute([$masterSubjectId, $topicName, $topicSlug]);
                $masterTopicId = $db->lastInsertId();
            } else {
                $masterTopicId = $masterTopic['id'];
            }

            // D. Insert Syllabus Node (Topic)
            $order++;
            $db->prepare("INSERT INTO syllabus_nodes 
                (level, title, slug, type, parent_id, `order`, is_active, linked_category_id, linked_topic_id) 
                VALUES (:level, :title, :slug, 'topic', :parent, :order, 1, NULL, :linked_topic)")
                ->execute([
                    'level' => $levelName,
                    'title' => $topicName,
                    'slug' => $topicSlug,
                    'parent' => $sectionNodeId,
                    'order' => $order,
                    'linked_topic' => $masterTopicId
                ]);
            echo "      + Added Topic: $topicName\n";
        }
    }

    echo "\n✅ ===== SEEDING COMPLETE! =====\n";
    echo "Visit: http://localhost/Bishwo_Calculator/admin/quiz/syllabus/manage/sub%20engineer\n";
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
