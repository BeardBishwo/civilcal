<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->query("SELECT username, email, is_admin, role FROM users WHERE is_admin = 1 OR role = 'admin' LIMIT 5");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($admins, JSON_PRETTY_PRINT);
