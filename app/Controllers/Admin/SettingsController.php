<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        // Mock settings data
        $settings = [
            'general' => [
                'site_name' => 'Bishwo Calculator',
                'site_description' => 'Professional Engineering Calculators',
                'site_url' => 'http://localhost/bishwo_calculator',
                'admin_email' => 'admin@bishwocalculator.com',
                'timezone' => 'Asia/Kathmandu',
                'date_format' => 'Y-m-d',
                'items_per_page' => '20'
            ]
        ];
        
        // Load the settings management view
        include __DIR__ . '/../../Views/admin/settings/index.php';
    }

    public function saveSettings()
    {
        if ($_POST) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'No data received']);
    }
}
?>
