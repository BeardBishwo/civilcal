<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();
$active = $db->query("SELECT name FROM themes WHERE status = 'active'")->fetchColumn();
echo "Active Theme: " . ($active ?: 'default') . "\n";

$theme = $active ?: 'default';
$base = "themes/$theme/views/";
echo "Theme Base: $base\n";

$files = [
    "quiz/portal/index.php",
    "exams/index.php",
    "exams/category.php",
    "exams/take.php",
    "exams/result.php"
];

foreach ($files as $f) {
    if (file_exists($base . $f)) {
        echo "[EXISTS] $base$f\n";
    } else {
        echo "[MISSING] $base$f\n";
    }
}
