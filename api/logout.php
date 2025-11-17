<?php
/**
 * Direct API Logout Endpoint
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../app/bootstrap.php';

$controller = new \App\Controllers\Api\AuthController();
$controller->logout();
