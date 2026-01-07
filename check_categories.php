<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();

// Check categories table structure
echo "=== QUIZ_CATEGORIES SCHEMA ===\n";
$schema = $db->query("DESCRIBE quiz_categories")->fetchAll();
foreach($schema as $s) {
    echo "{$s['Field']} | {$s['Type']}\n";
}

echo "\n=== QUIZ CATEGORIES DATA ===\n";
$cats = $db->query("SELECT * FROM quiz_categories WHERE status = 1 ORDER BY `order` ASC")->fetchAll();
foreach($cats as $c) {
    $icon = isset($c['icon']) ? $c['icon'] : 'N/A';
    echo "ID: {$c['id']} | Name: {$c['name']} | Slug: {$c['slug']} | Icon: {$icon}\n";
}

echo "\n=== EXAM STATS ===\n";
$stats = $db->query("
    SELECT 
        e.id,
        e.title,
        e.type,
        e.is_premium,
        e.created_at,
        COUNT(DISTINCT a.id) as attempt_count
    FROM quiz_exams e
    LEFT JOIN quiz_attempts a ON e.id = a.exam_id
    WHERE e.status = 'published'
    GROUP BY e.id
    ORDER BY attempt_count DESC
")->fetchAll();

foreach($stats as $s) {
    $days_old = round((time() - strtotime($s['created_at'])) / 86400);
    echo "Exam: {$s['title']}\n";
    echo "  Type: {$s['type']} | Premium: {$s['is_premium']} | Age: {$days_old} days | Attempts: {$s['attempt_count']}\n\n";
}
