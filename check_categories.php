<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();

$cats = $db->query("SELECT * FROM quiz_categories")->fetchAll();

echo "ID | Name | Slug | Status\n";
echo str_repeat("-", 40) . "\n";
foreach ($cats as $c) {
    echo "{$c['id']} | {$c['name']} | {$c['slug']} | {$c['status']}\n";
}
