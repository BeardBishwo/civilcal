<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = get_db();
$stmt = $db->prepare('SELECT * FROM themes WHERE name = "default"');
$stmt->execute();
$theme = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Default theme status:\n";
print_r($theme);