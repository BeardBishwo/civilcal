<?php
require_once 'app/Config/config.php';
require_once 'app/Config/db.php';

$db = get_db();
$stmt = $db->prepare('SELECT * FROM themes WHERE name = "default"');
$stmt->execute();
$theme = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Default theme status:\n";
print_r($theme);

