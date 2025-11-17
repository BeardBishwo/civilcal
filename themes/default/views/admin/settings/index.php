<?php
/**
 * Settings Index - Redirects to General Settings
 * This file now redirects to the General Settings page to avoid conflicts
 */

// Security check
if (!defined('ABSPATH')) {
    exit('Access denied');
}

// Initialize container if not available
if (!isset($container)) {
    $container = \App\Core\Container::create();
}

// Redirect to General Settings page
header('Location: general.php');
exit;
?>
