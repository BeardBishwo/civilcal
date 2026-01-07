<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$active = $db->query('SELECT * FROM themes WHERE status = \'active\'')->fetch();

echo "--- THEMES TABLE ---\n";
if ($active) {
    echo "ID: {$active['id']} | Name: {$active['name']} | Status: {$active['status']}\n";
} else {
    echo "NO ACTIVE THEME FOUND IN DATABASE.\n";
}

echo "\n--- ALL THEMES ---\n";
$all = $db->query('SELECT id, name, status FROM themes')->fetchAll();
foreach ($all as $t) {
    echo "ID: {$t['id']} | Name: {$t['name']} | Status: {$t['status']}\n";
}

echo "\n--- SEARCHING FOR exams/index.php ---\n";
$themesDir = BASE_PATH . '/themes';
$themes = scandir($themesDir);
foreach ($themes as $theme) {
    if ($theme === '.' || $theme === '..') continue;
    $path = $themesDir . '/' . $theme . '/views/exams/index.php';
    echo "Theme: $theme | Path Exists: " . (file_exists($path) ? "YES" : "NO") . "\n";
}
