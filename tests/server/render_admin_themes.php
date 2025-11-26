<?php
define('BISHWO_CALCULATOR', true);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/admin/themes';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';

require __DIR__ . '/../../app/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user_id'] = 2;
$_SESSION['is_admin'] = 1;
$_SESSION['user'] = ['id' => 2, 'role' => 'admin', 'is_admin' => 1];

ob_start();
$router = new \App\Core\Router();
require __DIR__ . '/../../app/routes.php';
$router->dispatch();
$output = ob_get_clean();

echo $output;
?>
