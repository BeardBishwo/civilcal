<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->query("SELECT calculator_id, slug, category, subcategory, full_path FROM calculator_urls WHERE category='structural'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
