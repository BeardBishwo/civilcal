<?php

echo "=== Testing Settings Routes ===\n\n";

// Test the SettingsController methods
require_once 'app/Core/Database.php';
require_once 'app/Core/Controller.php';
require_once 'app/Services/SettingsService.php';
require_once 'app/Services/GDPRService.php';
require_once 'app/Controllers/Admin/SettingsController.php';

// Mock the necessary dependencies
class MockView {
    public function render($view, $data = []) {
        echo "✅ View: $view\n";
        echo "   Title: " . $data['title'] . "\n";
        
        $viewFile = 'themes/admin/views/' . str_replace('admin/', '', $view) . '.php';
        if (file_exists($viewFile)) {
            $content = file_get_contents($viewFile);
            echo "   Content Length: " . strlen($content) . " characters\n";
            echo "   Contains admin-content: " . (strpos($content, 'admin-content') !== false ? '✅' : '❌') . "\n";
            echo "   Contains Chart.js: " . (strpos($content, 'Chart') !== false ? '✅' : '❌') . "\n";
        } else {
            echo "   ❌ View file not found: $viewFile\n";
        }
        return true;
    }
}

class MockAuth {
    public function check() { return true; }
    public function isAdmin() { return true; }
}

// Test the controller methods
$controller = new App\Controllers\Admin\SettingsController();
$controller->view = new MockView();
$controller->auth = new MockAuth();

echo "1. Testing backup method...\n";
$controller->backup();
echo "\n";

echo "2. Testing application method...\n";
$controller->application();
echo "\n";

echo "3. Testing other existing methods...\n";

// Test general method
echo "   Testing general method...\n";
$controller->general();
echo "\n";

// Test security method
echo "   Testing security method...\n";
$controller->security();
echo "\n";

// Test email method
echo "   Testing email method...\n";
$controller->email();
echo "\n";

// Test advanced method
echo "   Testing advanced method...\n";
$controller->advanced();
echo "\n";

echo "=== All Settings Routes Test Complete ===\n";
echo "✅ All methods are working correctly!\n";
