<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    echo "\n\nSeeding users...\n";

    $db = Database::getInstance();
    $pdo = $db->getPdo();

    // Disable foreign key checks temporarily
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Truncate users table
    $pdo->exec("TRUNCATE TABLE users");

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Cleared existing users.\n";

    // Define users to seed
    $users = [
        [
            'username' => 'Engineer Demo',
            'email' => 'engineer@engicalpro.com',
            'password' => 'Engineer123!',
            'role' => 'user',
            'is_admin' => 0
        ],
        [
            'username' => 'Admin Demo',
            'email' => 'admin@engicalpro.com',
            'password' => 'password',
            'role' => 'admin',
            'is_admin' => 1
        ],
        [
            'username' => 'Bishwo',
            'email' => 'uniquebishwo@gmail.com',
            'password' => 'c9PU7XAsAADYk_A',
            'role' => 'admin',
            'is_admin' => 1
        ]
    ];

    // Insert users
    foreach ($users as $user) {
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role, is_admin, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user['username'],
            $user['email'],
            password_hash($user['password'], PASSWORD_DEFAULT),
            $user['role'],
            $user['is_admin']
        ]);
        echo "Created user: {$user['email']}\n";
    }

    echo "\nSeeding complete!\n";
    echo "You can now login with:\n";
    echo "  - engineer@engicalpro.com / Engineer123!\n";
    echo "  - admin@engicalpro.com / password\n";
    echo "  - uniquebishwo@gmail.com / c9PU7XAsAADYk_A\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
