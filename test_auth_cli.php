<?php
require __DIR__ . '/app/bootstrap.php';

use App\Models\User;
use App\Core\Auth;

// Use a predictable test identity
$username = 'cli_test_user';
$email = 'cli_test_user@example.com';
$password = 'Password123!';

$userModel = new User();
$existing = $userModel->findByEmail($email);

if (!$existing) {
    echo "Creating test user...\n";
    $userId = $userModel->create([
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'first_name' => 'CLI',
        'last_name' => 'Tester',
        'company' => 'Local',
        'profession' => 'Dev',
    ]);
    echo "Created user id: {$userId}\n";
} else {
    echo "Test user already exists (email): {$email}\n";
}

echo "Attempting login...\n";
$result = Auth::login($username, $password);
print_r($result);
