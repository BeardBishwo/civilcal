<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();

// Fix Structural
echo "Fixing Structural...\n";
$stmt = $db->prepare('UPDATE calculator_urls SET full_path = CONCAT("modules/", full_path) WHERE category = "structural" AND full_path NOT LIKE "modules/%"');
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " rows.\n";

// Fix Site
echo "Fixing Site...\n";
$stmt = $db->prepare('UPDATE calculator_urls SET full_path = CONCAT("modules/", full_path) WHERE category = "site" AND full_path NOT LIKE "modules/%"');
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " rows.\n";

// Verify
$stmt = $db->prepare('SELECT full_path FROM calculator_urls WHERE slug="beam-design"');
$stmt->execute();
$path = $stmt->fetchColumn();
echo "New path for beam-design: $path\n";
