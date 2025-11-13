<?php
require_once 'app/bootstrap.php';

$tm = new App\Services\ThemeManager();
echo "renderPartial exists: " . (method_exists($tm, 'renderPartial') ? "YES" : "NO") . "\n";
echo "Active theme: " . $tm->getActiveTheme() . "\n";
?>


