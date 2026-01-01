<?php
define('BASE_PATH', __DIR__ . '/..');
require_once __DIR__ . '/../app/Config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running Career System Migration...\n";
    
    // Check columns in users
    $colCheck = $pdo->query("SHOW COLUMNS FROM users LIKE 'rank_title'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN rank_title VARCHAR(50) DEFAULT 'Intern'");
        $pdo->exec("ALTER TABLE users ADD COLUMN study_mode ENUM('psc', 'world') DEFAULT 'psc'");
        $pdo->exec("ALTER TABLE users ADD COLUMN xp INT DEFAULT 0");
        echo "Updated users table.\n";
    }

    // Check columns in quiz_questions
    $qCheck = $pdo->query("SHOW COLUMNS FROM quiz_questions LIKE 'target_audience'");
    if ($qCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE quiz_questions ADD COLUMN target_audience ENUM('universal', 'psc_only', 'world_only') DEFAULT 'universal'");
        echo "Updated quiz_questions table.\n";
    }

    echo "Migration Complete.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
