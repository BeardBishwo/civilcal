<?php
define('BISHWO_CALCULATOR', true);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/admin/debug/run-tests';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_ACCEPT'] = 'application/json';
$_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';

require __DIR__ . '/../../app/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user_id'] = 2;
$_SESSION['is_admin'] = 1;
$_SESSION['user'] = ['id' => 2, 'role' => 'admin', 'is_admin' => 1];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_expiry'] = time() + 3600;
}

$_SERVER['HTTP_X_CSRF_TOKEN'] = $_SESSION['csrf_token'];
$_POST['csrf_token'] = $_SESSION['csrf_token'];
$_POST['test_type'] = 'all';

ob_start();
$router = new \App\Core\Router();
require __DIR__ . '/../../app/routes.php';
$router->dispatch();
$output = ob_get_clean();

echo $output;
?>
