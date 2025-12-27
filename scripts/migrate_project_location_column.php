<?php
// scripts/migrate_project_location_column.php

$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add location_id if not exists
    $stmt = $db->query("DESCRIBE est_projects");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('location_id', $columns)) {
        echo "Adding location_id column to est_projects...\n";
        $db->exec("ALTER TABLE est_projects ADD COLUMN location_id INT NULL DEFAULT NULL AFTER district_id");
        $db->exec("ALTER TABLE est_projects ADD CONSTRAINT fk_project_location FOREIGN KEY (location_id) REFERENCES est_locations(id) ON DELETE SET NULL");
        echo "Column 'location_id' added successfully.\n";
    } else {
        echo "Column 'location_id' already exists.\n";
    }

} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage();
}
