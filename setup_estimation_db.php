<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

$queries = [
    "CREATE TABLE IF NOT EXISTS est_projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        client_name VARCHAR(255),
        location VARCHAR(255),
        district_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS est_item_master (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dudbc_code VARCHAR(50),
        item_name TEXT NOT NULL,
        unit VARCHAR(20),
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS est_boq_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        grid_data LONGTEXT, -- Stores JSpreadsheet JSON
        status ENUM('Draft', 'Final') DEFAULT 'Draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES est_projects(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS est_district_rates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        district_id INT NOT NULL,
        item_id INT NOT NULL,
        rate DECIMAL(15, 2) NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "Successfully executed: " . substr($sql, 0, 50) . "...\n";
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "\n";
    }
}

echo "Database setup complete.\n";
