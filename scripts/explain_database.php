<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();

echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    DATABASE TABLES & THEIR IDs EXPLAINED                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Get all tables
$tables = [
    'questions',
    'syllabus_nodes',
    'position_levels',
    'question_options',
    'user_answers',
    'quiz_sessions',
    'quiz_results'
];

foreach ($tables as $table) {
    try {
        // Get table structure
        $columns = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 TABLE: $table (Total Records: $count)\n";
        echo str_repeat("=", 80) . "\n";

        foreach ($columns as $col) {
            $key = $col['Key'] == 'PRI' ? '🔑 PRIMARY KEY' : ($col['Key'] == 'MUL' ? '🔗 FOREIGN KEY' : '');
            $null = $col['Null'] == 'NO' ? 'REQUIRED' : 'OPTIONAL';
            printf(
                "  %-25s %-20s %-15s %s\n",
                $col['Field'],
                $col['Type'],
                $null,
                $key
            );
        }

        // Show sample data for key tables
        if ($count > 0 && in_array($table, ['syllabus_nodes', 'position_levels'])) {
            echo "\n  Sample Data:\n";
            $samples = $db->query("SELECT * FROM $table LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($samples as $sample) {
                echo "  → " . json_encode($sample) . "\n";
            }
        }
    } catch (Exception $e) {
        echo "  ⚠️ Table not found or error: " . $e->getMessage() . "\n";
    }
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                        HOW IDs CONNECT BETWEEN TABLES                      ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "1. QUESTIONS TABLE\n";
echo "   • id (Primary Key) - Unique question identifier\n";
echo "   • syllabus_node_id (Foreign Key) → syllabus_nodes.id\n";
echo "   • position_level_id (Foreign Key) → position_levels.id\n";
echo "   • question_type - Type of question (mcq, true_false, etc.)\n\n";

echo "2. SYLLABUS_NODES TABLE\n";
echo "   • id (Primary Key) - Unique node identifier\n";
echo "   • parent_id (Foreign Key) → syllabus_nodes.id (self-reference)\n";
echo "   • type - Node type (course, category, topic, etc.)\n\n";

echo "3. POSITION_LEVELS TABLE\n";
echo "   • id (Primary Key) - Unique position identifier\n";
echo "   • course_id (Foreign Key) → courses.id\n";
echo "   • education_level_id (Foreign Key) → education_levels.id\n";
echo "   • title - Position name\n\n";

echo "4. QUESTION_OPTIONS TABLE\n";
echo "   • id (Primary Key) - Unique option identifier\n";
echo "   • question_id (Foreign Key) → questions.id\n";
echo "   • option_text - The answer choice text\n";
echo "   • is_correct - Boolean flag\n\n";

echo "5. USER_ANSWERS TABLE\n";
echo "   • id (Primary Key) - Unique answer record\n";
echo "   • user_id (Foreign Key) → users.id\n";
echo "   • question_id (Foreign Key) → questions.id\n";
echo "   • selected_option_id (Foreign Key) → question_options.id\n\n";

echo "6. QUIZ_SESSIONS TABLE\n";
echo "   • id (Primary Key) - Unique session identifier\n";
echo "   • user_id (Foreign Key) → users.id\n";
echo "   • syllabus_node_id (Foreign Key) → syllabus_nodes.id\n";
echo "   • position_level_id (Foreign Key) → position_levels.id\n\n";

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                          RELATIONSHIP DIAGRAM                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "
┌─────────────────────┐
│  SYLLABUS_NODES     │
│  id (PK)            │◄─────┐
│  parent_id (FK)     │──────┘ (self-reference)
│  title              │
│  type               │
└──────────┬──────────┘
           │
           │ syllabus_node_id
           │
           ▼
┌─────────────────────┐       ┌─────────────────────┐
│  QUESTIONS          │       │  POSITION_LEVELS    │
│  id (PK)            │       │  id (PK)            │
│  syllabus_node_id   │       │  title              │
│  position_level_id  │◄──────│  course_id          │
│  question_text      │       │  education_level_id │
│  question_type      │       └─────────────────────┘
└──────────┬──────────┘
           │
           │ question_id
           │
           ▼
┌─────────────────────┐
│  QUESTION_OPTIONS   │
│  id (PK)            │
│  question_id (FK)   │
│  option_text        │
│  is_correct         │
└──────────┬──────────┘
           │
           │ selected_option_id
           │
           ▼
┌─────────────────────┐
│  USER_ANSWERS       │
│  id (PK)            │
│  user_id (FK)       │
│  question_id (FK)   │
│  selected_option_id │
└─────────────────────┘
";

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           PRACTICAL EXAMPLES                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "Example 1: Creating a Question\n";
echo "------------------------------\n";
echo "INSERT INTO questions (\n";
echo "  syllabus_node_id,    -- 33 (Surveying > General > Classification)\n";
echo "  position_level_id,   -- NULL or specific position\n";
echo "  question_text,       -- 'What are the types of surveying?'\n";
echo "  question_type        -- 'mcq'\n";
echo ") VALUES (33, NULL, 'What are the types of surveying?', 'mcq');\n\n";

echo "Example 2: Adding Options\n";
echo "-------------------------\n";
echo "INSERT INTO question_options (question_id, option_text, is_correct)\n";
echo "VALUES \n";
echo "  (1, 'Chain surveying', 1),\n";
echo "  (1, 'Plane table surveying', 1),\n";
echo "  (1, 'Theodolite surveying', 1),\n";
echo "  (1, 'None of the above', 0);\n\n";

echo "Example 3: Recording User Answer\n";
echo "--------------------------------\n";
echo "INSERT INTO user_answers (user_id, question_id, selected_option_id)\n";
echo "VALUES (123, 1, 2);  -- User 123 selected option 2\n\n";
