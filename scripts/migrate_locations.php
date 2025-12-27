<?php
// scripts/migrate_locations.php

// Normalized DB Connection for Migration
$host = 'localhost';
$dbname = 'bishwo_calculator';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check est_item_master for `dudbc_code` column
    echo "Checking est_item_master schema...\n";
    $stmt = $db->query("DESCRIBE est_item_master");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('dudbc_code', $columns)) {
        die("Error: 'dudbc_code' column missing in est_item_master. Aborting.\n");
    }
    echo "Schema verified (found dudbc_code). Proceeding with migration.\n\n";

    // 2. Create est_locations
    $sqlLocations = "
    CREATE TABLE IF NOT EXISTS est_locations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type ENUM('PROVINCE', 'DISTRICT', 'LOCAL_BODY') NOT NULL,
        parent_id INT DEFAULT NULL,
        FOREIGN KEY (parent_id) REFERENCES est_locations(id) ON DELETE CASCADE,
        INDEX idx_parent (parent_id),
        INDEX idx_type (type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sqlLocations);
    echo "Table 'est_locations' created.\n";

    // 3. Create est_local_rates
    // Note: Linking to 'code' as requested for universal ID
    $sqlRates = "
    CREATE TABLE IF NOT EXISTS est_local_rates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_code VARCHAR(50) NOT NULL,
        location_id INT NOT NULL,
        rate DECIMAL(15, 2) NOT NULL,
        fiscal_year VARCHAR(20) DEFAULT '2081/82',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (location_id) REFERENCES est_locations(id) ON DELETE CASCADE,
        INDEX idx_item_loc (item_code, location_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sqlRates);
    echo "Table 'est_local_rates' created.\n";

    // 4. Seed Data (Provinces and Sample Districts)
    // Nepal has 7 Provinces. Let's seed them.
    $provinces = [
        'Koshi', 'Madhesh', 'Bagmati', 'Gandaki', 'Lumbini', 'Karnali', 'Sudurpaschim'
    ];

    $check = $db->query("SELECT COUNT(*) FROM est_locations WHERE type='PROVINCE'")->fetchColumn();
    if ($check == 0) {
        $stmt = $db->prepare("INSERT INTO est_locations (name, type) VALUES (?, 'PROVINCE')");
        foreach ($provinces as $p) {
            $stmt->execute([$p]);
            echo "Seeded Province: $p\n";
        }
        
        // Seed Districts for Bagmati (as example/demo)
        $bagmatiId = $db->query("SELECT id FROM est_locations WHERE name='Bagmati'")->fetchColumn();
        $districts = ['Kathmandu', 'Bhaktapur', 'Lalitpur', 'Kavrepalanchok', 'Sindhupalchok', 'Dhading', 'Nuwakot', 'Rasuwa', 'Chitwan', 'Makwanpur', 'Ramechhap', 'Dolakha', 'Sindhuli'];
        
        $stmtDist = $db->prepare("INSERT INTO est_locations (name, type, parent_id) VALUES (?, 'DISTRICT', ?)");
        foreach ($districts as $d) {
            $stmtDist->execute([$d, $bagmatiId]);
            echo "Seeded District: $d (Bagmati)\n";
        }
        
    } else {
        echo "Data already seeded.\n";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
