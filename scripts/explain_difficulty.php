<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    QUESTION DIFFICULTY LEVELS EXPLAINED                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Check table structure
echo "📊 QUIZ_QUESTIONS TABLE STRUCTURE\n";
echo str_repeat("=", 80) . "\n";
$columns = $db->query("DESCRIBE quiz_questions")->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    if (in_array($col['Field'], ['id', 'difficulty_level', 'type', 'default_marks', 'syllabus_node_id'])) {
        printf("%-25s %-30s %s\n", $col['Field'], $col['Type'], $col['Null'] == 'NO' ? 'REQUIRED' : 'OPTIONAL');
    }
}

echo "\n\n";
echo "🎯 DIFFICULTY LEVEL COLUMN\n";
echo str_repeat("=", 80) . "\n";
$diffCol = $db->query("SHOW COLUMNS FROM quiz_questions LIKE 'difficulty_level'")->fetch(PDO::FETCH_ASSOC);
if ($diffCol) {
    echo "Column Name: " . $diffCol['Field'] . "\n";
    echo "Data Type:   " . $diffCol['Type'] . "\n";
    echo "Nullable:    " . ($diffCol['Null'] == 'YES' ? 'Yes (Optional)' : 'No (Required)') . "\n";
    echo "Default:     " . ($diffCol['Default'] ?? 'NULL') . "\n";

    // Extract ENUM values if it's an ENUM
    if (strpos($diffCol['Type'], 'enum') !== false) {
        preg_match("/^enum\(\'(.*)\'\)$/", $diffCol['Type'], $matches);
        if (isset($matches[1])) {
            $values = explode("','", $matches[1]);
            echo "\nAllowed Values:\n";
            foreach ($values as $val) {
                echo "  • $val\n";
            }
        }
    }
}

echo "\n\n";
echo "📈 CURRENT DIFFICULTY DISTRIBUTION\n";
echo str_repeat("=", 80) . "\n";
$stats = $db->query("
    SELECT 
        difficulty_level,
        COUNT(*) as count,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM quiz_questions), 2) as percentage
    FROM quiz_questions
    GROUP BY difficulty_level
    ORDER BY 
        CASE difficulty_level
            WHEN 'easy' THEN 1
            WHEN 'medium' THEN 2
            WHEN 'hard' THEN 3
            ELSE 4
        END
")->fetchAll(PDO::FETCH_ASSOC);

$total = $db->query("SELECT COUNT(*) FROM quiz_questions")->fetchColumn();
echo "Total Questions: $total\n\n";

if (count($stats) > 0) {
    printf("%-20s %-15s %-15s\n", "Difficulty", "Count", "Percentage");
    echo str_repeat("-", 50) . "\n";
    foreach ($stats as $row) {
        $level = $row['difficulty_level'] ?? 'NULL/Not Set';
        printf(
            "%-20s %-15s %-15s\n",
            $level,
            $row['count'],
            $row['percentage'] . '%'
        );
    }
} else {
    echo "No questions found in database.\n";
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         HOW DIFFICULTY LEVELS WORK                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "🎓 DIFFICULTY LEVELS EXPLAINED\n\n";

echo "1. EASY\n";
echo "   • Basic recall questions\n";
echo "   • Simple definitions\n";
echo "   • Direct application of formulas\n";
echo "   • Recommended marks: 1-2\n";
echo "   • Example: 'What is the SI unit of pressure?'\n\n";

echo "2. MEDIUM\n";
echo "   • Requires understanding\n";
echo "   • Multi-step problems\n";
echo "   • Application of concepts\n";
echo "   • Recommended marks: 2-3\n";
echo "   • Example: 'Calculate the bearing capacity using Terzaghi's formula'\n\n";

echo "3. HARD\n";
echo "   • Complex analysis\n";
echo "   • Multiple concepts combined\n";
echo "   • Critical thinking required\n";
echo "   • Recommended marks: 3-5\n";
echo "   • Example: 'Design a complete R.C. beam section with given loads'\n\n";

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                            PRACTICAL USAGE                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "📝 Creating Questions with Difficulty Levels:\n\n";
echo "-- Easy question\n";
echo "INSERT INTO quiz_questions (syllabus_node_id, type, content, difficulty_level, default_marks)\n";
echo "VALUES (33, 'mcq', 'What is surveying?', 'easy', 1);\n\n";

echo "-- Medium question\n";
echo "INSERT INTO quiz_questions (syllabus_node_id, type, content, difficulty_level, default_marks)\n";
echo "VALUES (33, 'mcq', 'Calculate the reduced level...', 'medium', 2);\n\n";

echo "-- Hard question\n";
echo "INSERT INTO quiz_questions (syllabus_node_id, type, content, difficulty_level, default_marks)\n";
echo "VALUES (33, 'mcq', 'Design a traverse survey for...', 'hard', 4);\n\n";

echo "🎯 Filtering Questions by Difficulty:\n\n";
echo "-- Get only easy questions for practice\n";
echo "SELECT * FROM quiz_questions WHERE difficulty_level = 'easy';\n\n";

echo "-- Get medium and hard questions for exam\n";
echo "SELECT * FROM quiz_questions WHERE difficulty_level IN ('medium', 'hard');\n\n";

echo "-- Mixed difficulty quiz (2 easy, 3 medium, 1 hard)\n";
echo "(SELECT * FROM quiz_questions WHERE difficulty_level = 'easy' ORDER BY RAND() LIMIT 2)\n";
echo "UNION ALL\n";
echo "(SELECT * FROM quiz_questions WHERE difficulty_level = 'medium' ORDER BY RAND() LIMIT 3)\n";
echo "UNION ALL\n";
echo "(SELECT * FROM quiz_questions WHERE difficulty_level = 'hard' ORDER BY RAND() LIMIT 1);\n\n";

echo "💡 BEST PRACTICES\n";
echo str_repeat("=", 80) . "\n";
echo "• Always set difficulty_level when creating questions\n";
echo "• Match marks to difficulty (easy=1-2, medium=2-3, hard=3-5)\n";
echo "• Balance quiz difficulty: 40% easy, 40% medium, 20% hard\n";
echo "• Use difficulty for adaptive learning (start easy, increase difficulty)\n";
echo "• Filter by difficulty for practice vs exam modes\n";
