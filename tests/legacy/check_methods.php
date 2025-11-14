<?php
require_once 'app/bootstrap.php';

$themeManager = new App\Services\ThemeManager();

echo "Checking ThemeManager methods:\n";
echo "renderPartial: " . (method_exists($themeManager, 'renderPartial') ? "EXISTS" : "MISSING") . "\n";
echo "renderView: " . (method_exists($themeManager, 'renderView') ? "EXISTS" : "MISSING") . "\n";
echo "loadCategoryStyle: " . (method_exists($themeManager, 'loadCategoryStyle') ? "EXISTS" : "MISSING") . "\n";
echo "setTheme: " . (method_exists($themeManager, 'setTheme') ? "EXISTS" : "MISSING") . "\n";
?>


