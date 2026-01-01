<?php
define('BASE_PATH', __DIR__ . '/..');
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running Onboarding & Suggestion Engine Migration...\n";

    // 1. quiz_categories updates (ensure slug/icon)
    $stmt = $pdo->query("SHOW COLUMNS FROM quiz_categories LIKE 'slug'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE quiz_categories ADD COLUMN slug VARCHAR(100) NULL");
        $pdo->exec("ALTER TABLE quiz_categories ADD COLUMN icon VARCHAR(255) NULL");
        echo "Updated quiz_categories table.\n";
    }

    // 2. quiz_subjects (The 52 Subjects)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quiz_subjects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES quiz_categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created quiz_subjects table.\n";

    // 3. Update quiz_questions
    $qCheck = $pdo->query("SHOW COLUMNS FROM quiz_questions LIKE 'subject_id'");
    if ($qCheck->rowCount() == 0) {
        // Adding subject_id and category_id
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN subject_id INT NULL");
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN category_id INT NULL"); // Denormalized for easier filtering
        
        // Add FKs (optional if data is clean, otherwise skip strict FK for now or use SET NULL)
        // $pdo->exec("ALTER TABLE quiz_questions ADD FOREIGN KEY (category_id) REFERENCES quiz_categories(id) ON DELETE SET NULL");
        echo "Updated quiz_questions table.\n";
    }

    // 4. user_interests (The Connection)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_interest (user_id, category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Created user_interests table.\n";

    // 5. Seed some categories if empty
    $count = $pdo->query("SELECT count(*) FROM quiz_categories")->fetchColumn();
    if ($count < 3) {
        $cats = [
            ['Structure', 'structure'],
            ['Water Resources', 'water'],
            ['Transportation', 'transport'],
            ['Geotechnical', 'geotech'],
            ['Management', 'management'],
            ['Environment', 'environment'],
            ['General', 'general']
        ];
        $insert = $pdo->prepare("INSERT INTO quiz_categories (name, slug) VALUES (?, ?)");
        foreach ($cats as $c) {
            $insert->execute($c);
        }
        echo "Seeded categories.\n";
    }

    echo "Migration Complete.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
