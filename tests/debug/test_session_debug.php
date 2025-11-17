<?php
require_once 'app/bootstrap.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Session ID: " . session_id() . PHP_EOL;
echo "Session data:" . PHP_EOL;
print_r($_SESSION);

echo PHP_EOL . "Cookies:" . PHP_EOL;
print_r($_COOKIE);

// Check auth
$user = \App\Core\Auth::check();
echo PHP_EOL . "Auth::check() result:" . PHP_EOL;
if ($user) {
    print_r($user);
} else {
    echo "No authenticated user" . PHP_EOL;
}
?>
