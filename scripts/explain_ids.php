<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    SYLLABUS NODE IDs - COMPLETE BREAKDOWN                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Get all nodes grouped by type
$types = ['course', 'education_level', 'position', 'category', 'sub_category', 'topic'];

foreach ($types as $type) {
    $nodes = $db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = '$type' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

    if (count($nodes) > 0) {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo strtoupper($type) . " (Total: " . count($nodes) . ")\n";
        echo str_repeat("=", 80) . "\n";

        foreach ($nodes as $node) {
            $parentInfo = $node['parent_id'] ? "Parent: {$node['parent_id']}" : "ROOT (No Parent)";
            printf("  [%3d] %-50s → %s\n", $node['id'], $node['title'], $parentInfo);
        }
    }
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           WHAT EACH ID IS USED FOR                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "📚 COURSE (ID: 1)\n";
echo "   Purpose: Top-level academic program\n";
echo "   Example: Civil Engineering\n";
echo "   Used for: Grouping all education levels under this field\n\n";

echo "🎓 EDUCATION_LEVEL (ID: 2)\n";
echo "   Purpose: Degree/diploma level within a course\n";
echo "   Example: Diploma in Civil Engineering\n";
echo "   Used for: Organizing positions by qualification level\n\n";

echo "👷 POSITION (ID: 3)\n";
echo "   Purpose: Job role/designation\n";
echo "   Example: Sub Engineer\n";
echo "   Used for: Grouping syllabus categories specific to this position\n\n";

echo "📂 CATEGORY (IDs: 4-264)\n";
echo "   Purpose: Main subject areas\n";
echo "   Examples: Surveying, Hydraulics, Construction Materials\n";
echo "   Two types:\n";
echo "     • Universal (parent_id = NULL): General subjects for all positions\n";
echo "     • Civil (parent_id = 3): Specific to Sub Engineer position\n";
echo "   Used for: High-level organization of syllabus content\n\n";

echo "📑 SUB_CATEGORY (IDs vary)\n";
echo "   Purpose: Subtopics within a category\n";
echo "   Examples: 'General', 'Levelling', 'Chain surveying' under Surveying\n";
echo "   Used for: Breaking down categories into manageable sections\n\n";

echo "📝 TOPIC (IDs vary)\n";
echo "   Purpose: Specific learning objectives\n";
echo "   Examples: 'Classification', 'Principle of surveying', 'Scales, plans and maps'\n";
echo "   Used for: Assigning questions to specific syllabus points\n\n";

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                            HOW IDs ARE LINKED                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "Example Hierarchy Chain:\n\n";
echo "  [1] Civil Engineering (course)\n";
echo "   └─ [2] Diploma in Civil Engineering (education_level)\n";
echo "       └─ [3] Sub Engineer (position)\n";
echo "           └─ [31] Surveying (category)\n";
echo "               ├─ [32] General (sub_category)\n";
echo "               │   ├─ [33] Classification (topic) ← Questions attach here\n";
echo "               │   ├─ [34] Principle of surveying (topic)\n";
echo "               │   └─ [35] Selection of suitable method (topic)\n";
echo "               └─ [38] Levelling (sub_category)\n";
echo "                   ├─ [39] Methods of levelling (topic)\n";
echo "                   └─ [40] Levelling instruments (topic)\n\n";

echo "Universal Category Example (No Course/Position):\n\n";
echo "  [4] General Awareness (category) ← parent_id = NULL (ROOT)\n";
echo "   ├─ [5] Geographical condition of Nepal (sub_category)\n";
echo "   ├─ [6] Historical, cultural, social condition (sub_category)\n";
echo "   └─ [7] Economic condition and plans (sub_category)\n\n";

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                        PRACTICAL USAGE IN SYSTEM                           ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "1. QUESTION ASSIGNMENT\n";
echo "   • Questions link to TOPIC IDs (e.g., ID 33, 34, 35)\n";
echo "   • Allows filtering: 'Show all questions for Surveying > General'\n\n";

echo "2. QUIZ GENERATION\n";
echo "   • Select by Category: 'Create quiz from Hydraulics (ID 90)'\n";
echo "   • Select by Position: 'Sub Engineer exam (ID 3) - all topics'\n\n";

echo "3. NAVIGATION\n";
echo "   • Breadcrumbs: Course > Education > Position > Category > Sub-Category > Topic\n";
echo "   • Tree view: Expand/collapse by parent_id relationships\n\n";

echo "4. STATISTICS\n";
echo "   • Count questions per category\n";
echo "   • Track user progress: '15/20 topics completed in Surveying'\n\n";

$totalNodes = $db->query("SELECT COUNT(*) FROM syllabus_nodes")->fetchColumn();
echo "\n📊 TOTAL NODES IN DATABASE: $totalNodes\n";
