<?php
// Define required constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'app/Models/Theme.php';

// Get the default theme
$themeModel = new App\Models\Theme();
$defaultTheme = $themeModel->getByName('default');

if ($defaultTheme) {
    echo "Found default theme with ID: " . $defaultTheme['id'] . "\n";
    
    // Activate the default theme
    $result = $themeModel->activate($defaultTheme['id']);
    
    if ($result) {
        echo "Default theme activated successfully!\n";
    } else {
        echo "Failed to activate default theme.\n";
    }
} else {
    echo "Default theme not found in database.\n";
}