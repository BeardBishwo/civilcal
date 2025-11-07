<?php
// Bishwo Calculator - Front Controller
// All requests go through this file

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;

// Start session
session_start();

// Initialize router
$router = new Router();

// Load routes
require_once __DIR__ . '/../app/routes.php';

// Dispatch the request
$router->dispatch();
?>
