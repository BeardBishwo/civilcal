<?php
// Debug routing test
echo "=== ROUTING DEBUG ===\n";

// Test if files exist and are accessible
$basePath = dirname(__DIR__);
echo "Base path: $basePath\n";

$bootstrapFile = $basePath . '/app/bootstrap.php';
echo "Bootstrap exists: " . (file_exists($bootstrapFile) ? "YES" : "NO") . "\n";

$routerFile = $basePath . '/app/Core/Router.php';
echo "Router exists: " . (file_exists($routerFile) ? "YES" : "NO") . "\n";

$routesFile = $basePath . '/app/routes.php';
echo "Routes file exists: " . (file_exists($routesFile) ? "YES" : "NO") . "\n";

$homeControllerFile = $basePath . '/app/Controllers/HomeController.php';
echo "HomeController exists: " . (file_exists($homeControllerFile) ? "YES" : "NO") . "\n";

if (file_exists($routesFile)) {
    echo "\n--- Routes file content (first 10 lines) ---\n";
    $lines = file($routesFile);
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

// Test theme files
$themeIndexFile = $basePath . '/themes/default/views/home/index.php';
echo "\nTheme index exists: " . (file_exists($themeIndexFile) ? "YES" : "NO") . "\n";

if (file_exists($themeIndexFile)) {
    echo "Theme file size: " . filesize($themeIndexFile) . " bytes\n";
}
?>
