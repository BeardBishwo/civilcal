<?php
// debug_auth_isolation.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/bootstrap.php';

// Mock Session
session_start();
// No user

$_SERVER['REQUEST_URI'] = '/api/firms/create';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Running AuthMiddleware...\n";

$m = new \App\Middleware\AuthMiddleware();
$m->handle([], function ($req) {
    echo "Next called (Authentication Passed?)\n";
});

echo "\nFinished.\n";
