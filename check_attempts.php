<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();

echo "=== QUIZ_ATTEMPTS SCHEMA ===\n";
$schema = $db->query("DESCRIBE quiz_attempts")->fetchAll();
foreach($schema as $s) {
    echo "{$s['Field']} | {$s['Type']}\n";
}
