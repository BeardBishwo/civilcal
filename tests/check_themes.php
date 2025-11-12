<?php
// Define required constants
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

require_once 'includes/config.php';
require_once 'includes/db.php';

$db = get_db();

// Get all themes
$stmt = $db->prepare('SELECT * FROM themes');
$stmt->execute();
$themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "All themes in database:\n";
foreach ($themes as $theme) {
    echo "Name: " . $theme['name'] . ", Status: " . $theme['status'] . "\n";
}

// Get active theme
$stmt = $db->prepare('SELECT * FROM themes WHERE status = "active"');
$stmt->execute();
$activeTheme = $stmt->fetch(PDO::FETCH_ASSOC);

echo "\nActive theme:\n";
if ($activeTheme) {
    print_r($activeTheme);
} else {
    echo "No active theme found\n";
}