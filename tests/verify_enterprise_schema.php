<?php
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

// Mock session if needed
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = Database::getInstance();

$tables_to_check = [
    'question_reports' => [
        'columns' => ['issue_type', 'status', 'question_id', 'user_id']
    ],
    'question_import_staging' => [
        'columns' => ['batch_id', 'uploader_id', 'content_hash', 'level_map', 'practical_mode']
    ],
    'syllabus_nodes' => [
        'columns' => ['type', 'is_premium', 'unlock_price', 'parent_id']
    ],
    'quiz_questions' => [
        'columns' => ['content_hash', 'status'] // checked earlier but good to double check
    ]
];

echo "<h3>Starting Schema Verification...</h3>";
echo "<ul style='font-family: monospace;'>";

$all_pass = true;

foreach ($tables_to_check as $table => $data) {
    echo "<li>Checking Table: <strong>$table</strong>... ";
    
    // Check if table exists
    try {
        $result = $db->query("SHOW TABLES LIKE '$table'")->fetch();
        if (!$result) {
            echo "<span style='color:red'>FAILED (Missing Table)</span>";
            $all_pass = false;
        } else {
            echo "<span style='color:green'>EXISTS</span>";
            
            // Check Columns
            $columns = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll();
            $col_names = array_column($columns, 'Field');
            
            $missing = [];
            foreach ($data['columns'] as $expected) {
                if (!in_array($expected, $col_names)) {
                    $missing[] = $expected;
                }
            }
            
            if (!empty($missing)) {
                echo "<br>&nbsp;&nbsp;Missing Columns: <span style='color:red'>" . implode(', ', $missing) . "</span>";
                $all_pass = false;
            } else {
                echo " | Columns Verified";
            }
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>ERROR: " . $e->getMessage() . "</span>";
        $all_pass = false;
    }
    echo "</li>";
}

echo "</ul>";

if ($all_pass) {
    echo "<h4 style='color:green'>SUCCESS: All Enterprise Migrations Verified!</h4>";
} else {
    echo "<h4 style='color:red'>FAILURE: Some migrations are missing or incomplete.</h4>";
}
