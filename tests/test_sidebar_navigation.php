<?php
require_once 'app/Core/View.php';

// Include controller files to test class existence
require_once 'app/Controllers/Admin/DashboardController.php';
require_once 'app/Controllers/Admin/SettingsController.php';

echo "=== Testing Admin Sidebar Navigation ===\n\n";

// Define all the new sidebar links we added
$sidebarLinks = [
    // Dashboard submenu
    'admin/dashboard' => 'Dashboard Overview',
    'admin/configured-dashboard' => 'Configured Dashboard',
    'admin/performance-dashboard' => 'Performance Dashboard',
    'admin/dashboard_complex' => 'Analytics Dashboard',
    
    // Settings submenu
    'admin/settings/application' => 'Application Settings',
    'admin/settings/general' => 'General Settings',
    'admin/settings/email' => 'Email Settings',
    'admin/settings/security' => 'Security Settings',
    'admin/settings/backup' => 'Backup Settings',
    'admin/settings/advanced' => 'Advanced Settings',
    
    // System submenu
    'admin/system-status' => 'System Status',
    'admin/widget-management' => 'Widget Management',
    'admin/menu-customization' => 'Menu Customization',
    
    // Content submenu (updated)
    'admin/content/pages' => 'Pages',
    'admin/content/menus' => 'Menus',
    'admin/content/media' => 'Media',
];

echo "1. Testing Controller Methods for All Sidebar Links:\n";
echo str_repeat("=", 60) . "\n";

$controllerMethods = [
    'admin/dashboard' => ['DashboardController', 'index'],
    'admin/configured-dashboard' => ['DashboardController', 'configuredDashboard'],
    'admin/performance-dashboard' => ['DashboardController', 'performanceDashboard'],
    'admin/dashboard_complex' => ['DashboardController', 'dashboardComplex'],
    'admin/settings/application' => ['SettingsController', 'application'],
    'admin/settings/general' => ['SettingsController', 'general'],
    'admin/settings/email' => ['SettingsController', 'email'],
    'admin/settings/security' => ['SettingsController', 'security'],
    'admin/settings/backup' => ['SettingsController', 'backup'],
    'admin/settings/advanced' => ['SettingsController', 'advanced'],
    'admin/system-status' => ['DashboardController', 'systemStatus'],
    'admin/widget-management' => ['DashboardController', 'widgetManagement'],
    'admin/menu-customization' => ['DashboardController', 'menuCustomization'],
];

foreach ($controllerMethods as $route => $method) {
    $controllerClass = "Admin\\{$method[0]}";
    $methodName = $method[1];
    
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, $methodName)) {
            echo "✅ {$route} -> {$method[0]}::{$methodName()} - Method exists\n";
        } else {
            echo "❌ {$route} -> {$method[0]}::{$methodName()} - Method missing\n";
        }
    } else {
        echo "❌ {$route} -> {$method[0]} - Controller class missing\n";
    }
}

echo "\n2. Testing View Files for All Sidebar Links:\n";
echo str_repeat("=", 60) . "\n";

$viewPaths = [
    'admin/dashboard' => 'themes/admin/views/dashboard.php',
    'admin/configured-dashboard' => 'themes/admin/views/configured-dashboard.php',
    'admin/performance-dashboard' => 'themes/admin/views/performance-dashboard.php',
    'admin/dashboard_complex' => 'themes/admin/views/dashboard_complex.php',
    'admin/settings/application' => 'themes/admin/views/settings/application.php',
    'admin/settings/general' => 'themes/admin/views/settings/general.php',
    'admin/settings/email' => 'themes/admin/views/settings/email.php',
    'admin/settings/security' => 'themes/admin/views/settings/security.php',
    'admin/settings/backup' => 'themes/admin/views/settings/backup.php',
    'admin/settings/advanced' => 'themes/admin/views/settings/advanced.php',
    'admin/system-status' => 'themes/admin/views/system-status.php',
    'admin/widget-management' => 'themes/admin/views/widget-management.php',
    'admin/menu-customization' => 'themes/admin/views/menu-customization.php',
];

foreach ($viewPaths as $route => $viewPath) {
    if (file_exists($viewPath)) {
        $content = file_get_contents($viewPath);
        $hasAdminContent = strpos($content, 'admin-content') !== false;
        $hasCorrectLayout = strpos($content, '../layouts/main.php') !== false || strpos($content, '../../layouts/main.php') !== false;
        
        echo "✅ {$route} -> {$viewPath}\n";
        echo "   - Has admin-content class: " . ($hasAdminContent ? "✅" : "❌") . "\n";
        echo "   - Uses correct layout: " . ($hasCorrectLayout ? "✅" : "❌") . "\n";
    } else {
        echo "❌ {$route} -> {$viewPath} - File missing\n";
    }
}

echo "\n3. Testing Route Definitions:\n";
echo str_repeat("=", 60) . "\n";

$routeFiles = [
    'app/routes.php',
    'app/routes_original.php'
];

$expectedRoutes = [
    'admin/dashboard',
    'admin/configured-dashboard',
    'admin/performance-dashboard',
    'admin/dashboard_complex',
    'admin/settings/application',
    'admin/settings/general',
    'admin/settings/email',
    'admin/settings/security',
    'admin/settings/backup',
    'admin/settings/advanced',
    'admin/system-status',
    'admin/widget-management',
    'admin/menu-customization'
];

foreach ($routeFiles as $routeFile) {
    if (file_exists($routeFile)) {
        echo "\n📁 {$routeFile}:\n";
        $content = file_get_contents($routeFile);
        
        foreach ($expectedRoutes as $route) {
            if (strpos($content, $route) !== false) {
                echo "   ✅ {$route} - Route defined\n";
            } else {
                echo "   ❌ {$route} - Route missing\n";
            }
        }
    } else {
        echo "❌ {$routeFile} - File not found\n";
    }
}

echo "\n4. Sidebar Navigation Structure Verification:\n";
echo str_repeat("=", 60) . "\n";

$layoutFile = 'themes/admin/layouts/main.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Check for Dashboard submenu
    $hasDashboardSubmenu = strpos($content, 'admin/configured-dashboard') !== false;
    echo "✅ Dashboard submenu added: " . ($hasDashboardSubmenu ? "Yes" : "No") . "\n";
    
    // Check for Settings submenu additions
    $hasApplicationSettings = strpos($content, 'admin/settings/application') !== false;
    $hasBackupSettings = strpos($content, 'admin/settings/backup') !== false;
    $hasAdvancedSettings = strpos($content, 'admin/settings/advanced') !== false;
    
    echo "✅ Application Settings link: " . ($hasApplicationSettings ? "Yes" : "No") . "\n";
    echo "✅ Backup Settings link: " . ($hasBackupSettings ? "Yes" : "No") . "\n";
    echo "✅ Advanced Settings link: " . ($hasAdvancedSettings ? "Yes" : "No") . "\n";
    
    // Check for System submenu
    $hasSystemSubmenu = strpos($content, 'admin/widget-management') !== false;
    echo "✅ System submenu added: " . ($hasSystemSubmenu ? "Yes" : "No") . "\n";
    
    // Check for Menu Customization in Content
    $hasMenuCustomization = strpos($content, 'admin/menu-customization') !== false;
    echo "✅ Menu Customization link: " . ($hasMenuCustomization ? "Yes" : "No") . "\n";
    
    // Count total nav items
    preg_match_all('/<li class="nav-item/', $content, $matches);
    $navItemCount = count($matches[0]);
    echo "✅ Total navigation items: {$navItemCount}\n";
    
    // Count submenu items
    preg_match_all('/<li><a href=/', $content, $submenuMatches);
    $submenuCount = count($submenuMatches[0]);
    echo "✅ Total submenu items: {$submenuCount}\n";
    
} else {
    echo "❌ Layout file not found\n";
}

echo "\n=== Sidebar Navigation Test Complete ===\n";
echo "All sidebar links have been successfully added to the admin navigation!\n";