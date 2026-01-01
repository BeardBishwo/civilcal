<?php
define('BASE_PATH', __DIR__ . '/..');
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running Shop System Migration...\n";

    // 1. shop_items
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS shop_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            price INT NOT NULL DEFAULT 100,
            type ENUM('blueprint', 'calculator', 'badge', 'perk') NOT NULL DEFAULT 'blueprint',
            resource_id INT NULL, -- Link to library_files.id or other tables
            icon VARCHAR(255) NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created shop_items table.\n";

    // 2. user_purchases
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_purchases (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_id INT NOT NULL,
            cost INT NOT NULL,
            purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES shop_items(id) ON DELETE CASCADE
            -- User FK skipped for simplicity or add if users table is guaranteed
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created user_purchases table.\n";

    // 3. Seed some initial items (Example Blueprints)
    // Check if we have library files to link?
    // For now, let's just create a 'Premium User Badge' item as a test
    $check = $pdo->query("SELECT count(*) FROM shop_items WHERE name = 'Chief Engineer Badge'");
    if ($check->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO shop_items (name, description, price, type, icon) VALUES ('Chief Engineer Badge', 'Unlock the golden aesthetic.', 500, 'badge', 'chief_engineer.png')");
        echo "Seeded 'Chief Engineer Badge'.\n";
    }

    echo "Migration Complete.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
