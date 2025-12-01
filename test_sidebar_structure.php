<?php

echo "=== Testing Admin Sidebar Structure ===\n\n";

// Define sidebar links
$sidebarLinks = [
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

// Test 1: Check if view files exist
echo "1. Testing View Files Existence:\n";
echo "============================================================\n";
$viewExistsCount = 0;
$viewTotalCount = count($sidebarLinks);

foreach ($sidebarLinks as $route => $viewFile) {
    if (file_exists($viewFile)) {
        echo "✅ {$route} -> {$viewFile} - File exists\n";
        $viewExistsCount++;
    } else {
        echo "❌ {$route} -> {$viewFile} - File missing\n";
    }
}

echo "\nView Files: {$viewExistsCount}/{$viewTotalCount} exist\n\n";

// Test 2: Check if view files have admin-content class
echo "2. Testing View Files Content:\n";
echo "============================================================\n";
$hasAdminContentCount = 0;

foreach ($sidebarLinks as $route => $viewFile) {
    if (file_exists($viewFile)) {
        $content = file_get_contents($viewFile);
        if (strpos($content, 'admin-content') !== false) {
            echo "✅ {$route} - Has admin-content class\n";
            $hasAdminContentCount++;
        } else {
            echo "❌ {$route} - Missing admin-content class\n";
        }
    }
}

echo "\nView Files with admin-content: {$hasAdminContentCount}/{$viewExistsCount}\n\n";

// Test 3: Check routes in routes.php
echo "3. Testing Route Definitions:\n";
echo "============================================================\n";
$routesFile = 'app/routes.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    $routeExistsCount = 0;
    
    foreach ($sidebarLinks as $route => $viewFile) {
        if (strpos($routesContent, $route) !== false) {
            echo "✅ {$route} - Route defined\n";
            $routeExistsCount++;
        } else {
            echo "❌ {$route} - Route missing\n";
        }
    }
    
    echo "\nRoutes defined: {$routeExistsCount}/{$viewTotalCount}\n\n";
} else {
    echo "❌ Routes file not found\n\n";
}

// Test 4: Check sidebar layout file
echo "4. Testing Sidebar Layout:\n";
echo "============================================================\n";
$layoutFile = 'themes/admin/layouts/main.php';
if (file_exists($layoutFile)) {
    echo "✅ Admin layout file exists\n";
    
    $layoutContent = file_get_contents($layoutFile);
    
    // Check for sidebar structure
    if (strpos($layoutContent, 'sidebar') !== false) {
        echo "✅ Sidebar structure found\n";
    } else {
        echo "❌ Sidebar structure missing\n";
    }
    
    // Check for navigation items
    $navigationItems = [
        'configured-dashboard',
        'performance-dashboard', 
        'dashboard_complex',
        'settings/application',
        'settings/backup',
        'settings/advanced',
        'system-status',
        'widget-management',
        'menu-customization'
    ];
    
    $foundItems = 0;
    foreach ($navigationItems as $item) {
        if (strpos($layoutContent, $item) !== false) {
            $foundItems++;
        }
    }
    
    echo "✅ Navigation items found: {$foundItems}/" . count($navigationItems) . "\n";
} else {
    echo "❌ Admin layout file missing\n";
}

echo "\n=== Sidebar Structure Test Complete ===\n";
echo "Summary:\n";
echo "- View files exist: {$viewExistsCount}/{$viewTotalCount}\n";
echo "- View files with admin-content: {$hasAdminContentCount}/{$viewExistsCount}\n";
echo "- Routes defined: {$routeExistsCount}/{$viewTotalCount}\n";
echo "- Layout file: " . (file_exists($layoutFile) ? "✅ Exists" : "❌ Missing") . "\n";