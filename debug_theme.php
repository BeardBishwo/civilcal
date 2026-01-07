<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Services/ThemeManager.php';
require_once __DIR__ . '/app/Core/View.php';

use App\Services\ThemeManager;
use App\Core\View;

$tm = new ThemeManager();
$activeTheme = $tm->getActiveTheme();
echo "Active Theme: " . $activeTheme . "\n";

$view = new View();
$viewFile = "exams/index";
$path = BASE_PATH . "/themes/" . $activeTheme . "/views/" . $viewFile . ".php";

echo "Expected View Path: " . $path . "\n";
echo "File Exists: " . (file_exists($path) ? "YES" : "NO") . "\n";

if (file_exists($path)) {
    echo "Content Length: " . strlen(file_get_contents($path)) . "\n";
} else {
    // Check fallback
    $altPath = BASE_PATH . "/app/Views/" . $viewFile . ".php";
    echo "Fallback Path: " . $altPath . "\n";
    echo "Fallback Exists: " . (file_exists($altPath) ? "YES" : "NO") . "\n";
}
