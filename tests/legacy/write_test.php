<?php require_once 'app/bootstrap.php'; \ = new App\Services\ThemeManager(); file_put_contents('test_output.txt', 'renderPartial: ' . (method_exists(\, 'renderPartial') ? 'YES' : 'NO') . PHP_EOL . 'Active theme: ' . \->getActiveTheme() . PHP_EOL); ?>


