<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\User;
use App\Core\Database;

$email = 'uniquebishwo@gmail.com';
$password = 'c9PU7XAsAADYk_A';

echo "<h1>Login Debugger V2</h1>";

// 1. Check if user exists
echo "<h2>1. Finding User</h2>";
$user = User::findByUsername($email);
if ($user) {
    echo "User found: " . htmlspecialchars($user->username) . " (ID: " . $user->id . ")<br>";
    echo "Current Hash: " . htmlspecialchars(substr($user->password, 0, 20)) . "...<br>";
} else {
    die("User not found!");
}

// 2. Reset Password
echo "<h2>2. Resetting Password</h2>";
$newHash = password_hash($password, PASSWORD_DEFAULT);
$db = Database::getInstance();
$pdo = $db->getPdo();
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
if ($stmt->execute([$newHash, $user->id])) {
    echo "Password updated successfully in DB.<br>";
} else {
    die("Failed to update password in DB.");
}

// 3. Verify
echo "<h2>3. Verification</h2>";
$userRefetched = User::findByUsername($email);
echo "New Hash in DB: " . htmlspecialchars(substr($userRefetched->password, 0, 20)) . "...<br>";

if (password_verify($password, $userRefetched->password)) {
    echo "<h3 style='color:green'>SUCCESS: Password matches hash!</h3>";
} else {
    echo "<h3 style='color:red'>FAILURE: Password does NOT match hash!</h3>";
}

// 4. Session Check
echo "<h2>4. Session Info</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "<br>";
echo "Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";
