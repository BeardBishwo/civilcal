<?php
require 'index.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("SHOW COLUMNS FROM quiz_questions");
print_r($stmt->fetchAll(\PDO::FETCH_ASSOC));
