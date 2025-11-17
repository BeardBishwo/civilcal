<?php
/**
 * Direct API Login Endpoint
 * This bypasses the router to avoid .htaccess redirect loops
 */

// Prevent direct access check
define('BISHWO_CALCULATOR', true);

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Create controller and handle request
$controller = new \App\Controllers\Api\AuthController();
$controller->login();
