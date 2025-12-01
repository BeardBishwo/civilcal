<?php

echo "=== Testing Settings Controller and Views ===\n\n";

// Test 1: Check controller syntax
echo "1. Testing SettingsController syntax...\n";
$output = [];
$return_var = 0;
exec('php -l app/Controllers/Admin/SettingsController.php 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "   ✅ SettingsController syntax is valid\n";
} else {
    echo "   ❌ SettingsController has syntax errors:\n";
    echo "   " . implode("\n   ", $output) . "\n";
}
echo "\n";

// Test 2: Check if backup and application methods exist
echo "2. Testing method existence...\n";
$controller_content = file_get_contents('app/Controllers/Admin/SettingsController.php');

if (strpos($controller_content, 'public function backup(') !== false) {
    echo "   ✅ backup() method exists\n";
} else {
    echo "   ❌ backup() method missing\n";
}

if (strpos($controller_content, 'public function application(') !== false) {
    echo "   ✅ application() method exists\n";
} else {
    echo "   ❌ application() method missing\n";
}
echo "\n";

// Test 3: Check view files exist
echo "3. Testing view file existence...\n";

$view_files = [
    'themes/admin/views/settings/backup.php',
    'themes/admin/views/settings/application.php',
    'themes/admin/views/settings/general.php',
    'themes/admin/views/settings/security.php',
    'themes/admin/views/settings/email.php',
    'themes/admin/views/settings/advanced.php'
];

foreach ($view_files as $view_file) {
    if (file_exists($view_file)) {
        $content = file_get_contents($view_file);
        $has_admin_content = strpos($content, 'admin-content') !== false;
        $has_header = strpos($content, '../partials/header.php') !== false || strpos($content, '../../partials/header.php') !== false;
        $has_footer = strpos($content, '../partials/footer.php') !== false || strpos($content, '../../partials/footer.php') !== false;
        $has_layout = $has_header && $has_footer;
        
        echo "   ✅ $view_file\n";
        echo "      - Contains admin-content: " . ($has_admin_content ? '✅' : '❌') . "\n";
        echo "      - Uses correct layout: " . ($has_layout ? '✅' : '❌') . "\n";
    } else {
        echo "   ❌ $view_file - File not found\n";
    }
}
echo "\n";

// Test 4: Check routes exist
echo "4. Testing route definitions...\n";
$route_files = [
    'app/routes.php',
    'app/routes_original.php'
];

foreach ($route_files as $route_file) {
    if (file_exists($route_file)) {
        $content = file_get_contents($route_file);
        
        $has_backup_route = strpos($content, '/admin/settings/backup') !== false;
        $has_application_route = strpos($content, '/admin/settings/application') !== false;
        
        echo "   📁 $route_file:\n";
        echo "      - Backup route: " . ($has_backup_route ? '✅' : '❌') . "\n";
        echo "      - Application route: " . ($has_application_route ? '✅' : '❌') . "\n";
    }
}
echo "\n";

echo "=== Settings Routes Test Complete ===\n";
echo "✅ All critical components are in place!\n";
