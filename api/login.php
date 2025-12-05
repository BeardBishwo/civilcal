<?php
/**
 * Direct API Login Endpoint
 * This bypasses the router to avoid .htaccess redirect loops
 */

// Prevent direct access check
define('BISHWO_CALCULATOR', true);

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle HTTP Basic Auth for testing
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
    
    $userModel = new \App\Models\User();
    $user = $userModel->findByUsername($username);
    
    if ($user && password_verify($password, $user->password)) {
        // Set session for this request
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['user'] = (array) $user;
        $_SESSION['is_admin'] = $user->is_admin ?? false;
        $_SESSION['api_authenticated'] = true;
    }
}

// Create controller and handle request
$controller = new \App\Controllers\Api\AuthController();
$controller->login();