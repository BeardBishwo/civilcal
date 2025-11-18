<?php
/**
 * Temporary Admin User Creation Page
 * Direct access file to test the user creation functionality
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load configuration
require BASE_PATH . '/app/bootstrap.php';

// Create a simple authentication check
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple admin check (for testing purposes)
$isAdmin = true; // Temporary bypass for testing

if (!$isAdmin) {
    header('Location: /login');
    exit;
}

// Load the create view directly
require BASE_PATH . '/themes/admin/views/users/create.php';
?>