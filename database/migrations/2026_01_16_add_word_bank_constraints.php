<?php
$host = 'localhost';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. Add Foreign Key
    try {
        $pdo->exec("ALTER TABLE word_bank ADD CONSTRAINT fk_word_bank_category FOREIGN KEY (category_id) REFERENCES syllabus_nodes(id) ON DELETE SET NULL");
        echo "FK Added\n";
    } catch (Exception $e) {
        echo "FK Notice: " . $e->getMessage() . "\n";
    }

    // 2. Add Index on category_id
    try {
        $pdo->exec("CREATE INDEX idx_word_bank_category ON word_bank(category_id)");
        echo "Index Category Added\n";
    } catch (Exception $e) {
        echo "Index Category Notice: " . $e->getMessage() . "\n";
    }

    // 3. Add Index on term
    try {
        $pdo->exec("CREATE INDEX idx_word_bank_term ON word_bank(term)");
        echo "Index Term Added\n";
    } catch (Exception $e) {
        echo "Index Term Notice: " . $e->getMessage() . "\n";
    }
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
