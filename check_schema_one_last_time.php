<?php
define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->getPdo()->query("SHOW CREATE TABLE quiz_questions");
$ddl = $stmt->fetch(PDO::FETCH_ASSOC);
file_put_contents('schema_debug.txt', $ddl['Create Table']);
echo "Schema written to schema_debug.txt";
