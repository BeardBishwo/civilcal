<?php
/**
 * Direct API Logout Endpoint
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../app/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new \App\Controllers\Api\AuthController();
$controller->logout();
