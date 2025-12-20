<?php
/**
 * Bishwo Calculator - Installation Router
 * Redirects to the beautiful installer
 * 
 * @package BishwoCalculator
 * @version 2.0.0
 */

// Check if already installed
if (file_exists(__DIR__ . '/../storage/install.lock')) {
    header('Location: ../');
    exit('Installation already completed!');
}

// Redirect to the beautiful installer
header('Location: installer.php');
exit;
exit;
