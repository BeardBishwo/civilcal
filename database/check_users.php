<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->query("SELECT id, username, email, role, is_admin FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\n=== Users in Database ===\n";
foreach ($users as $user) {
    echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}, IsAdmin: {$user['is_admin']}\n";
}
echo "\nTotal users: " . count($users) . "\n";
