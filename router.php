<?php
// Router script for PHP built-in server
// This enables proper routing for the application

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico|woff|woff2|ttf|eot|svg)$/', $_SERVER["REQUEST_URI"])) {
    // Serve static files directly
    return false;
} else {
    // Route everything else through the main application
    include __DIR__ . '/public/index.php';
}
?>