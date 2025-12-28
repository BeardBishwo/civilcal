<?php
// Migration for Guild (Engineering Firms) System - FINAL ID FIX

$host = '127.0.0.1';
$db   = 'bishwo_calculator';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Drop tables if we partially created them
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("DROP TABLE IF EXISTS guild_vault;");
    $pdo->exec("DROP TABLE IF EXISTS guild_members;");
    $pdo->exec("DROP TABLE IF EXISTS guilds;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 1. Guilds Table - Matching users.id (INT NOT NULL)
    $pdo->exec("CREATE TABLE guilds (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        logo_url VARCHAR(255),
        leader_id INT NOT NULL,
        level INT DEFAULT 1,
        xp INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 2. Guild Members Table
    $pdo->exec("CREATE TABLE guild_members (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        guild_id INT UNSIGNED NOT NULL,
        user_id INT NOT NULL,
        role ENUM('Leader', 'Architect', 'Intern') DEFAULT 'Intern',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id), -- User can only be in one guild
        FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 3. Guild Vault Table
    $pdo->exec("CREATE TABLE guild_vault (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        guild_id INT UNSIGNED NOT NULL,
        resource_type ENUM('bricks', 'cement', 'steel', 'coins') NOT NULL,
        amount INT UNSIGNED DEFAULT 0,
        UNIQUE(guild_id, resource_type),
        FOREIGN KEY (guild_id) REFERENCES guilds(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    echo "Guild System tables created successfully with matching standard INT types.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
