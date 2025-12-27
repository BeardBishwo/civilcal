<?php
require_once 'app/Config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $categories = $db->query("SELECT id, name, slug FROM calc_unit_categories")->fetchAll();
    echo "Categories Count: " . count($categories) . "\n";
    foreach ($categories as $cat) {
        $units = $db->query("SELECT COUNT(*) as count FROM calc_units WHERE category_id = " . $cat['id'])->fetch();
        echo "- " . $cat['name'] . " (" . $cat['slug'] . "): " . $units['count'] . " units\n";
    }
    
    $totalUnits = $db->query("SELECT COUNT(*) as count FROM calc_units")->fetch();
    echo "\nTotal Units: " . $totalUnits['count'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
