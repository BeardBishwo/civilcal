<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\SettingsService;
use App\Services\GDPRService;

class SettingsController extends Controller
{
    public function index()
    {
        $this->requireAdmin();
        
        // Get settings grouped by category
        $groups = ['general', 'appearance', 'email', 'security', 'privacy', 'performance', 'system', 'api'];
        $settingsByGroup = [];
        
        foreach ($groups as $group) {
            $settingsByGroup[$group] = SettingsService::getByGroup($group);
        }
        
        return $this->view('admin/settings/index', [
            'title' => 'Settings Management',
            'settingsByGroup' => $settingsByGroup,
            'groups' => $groups
        ]);
    }
    
    public function save()
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        try {
            $updated = 0;
            
            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token' && strpos($key, '_') !== false) {
                    // Handle file uploads for image/file type settings
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $value = $this->handleFileUpload($_FILES[$key]);
                    }
                    
                    // Handle checkboxes (boolean values)
                    if (!isset($_POST[$key]) && $this->isCheckboxField($key)) {
                        $value = '0';
                    }
                    
                    if (SettingsService::set($key, $value)) {
                        $updated++;
                        
                        // Log the change
                        GDPRService::logActivity(
                            $_SESSION['user_id'] ?? null,
                            'setting_updated',
                            'settings',
                            null,
                            "Setting $key updated",
                            null,
                            ['key' => $key, 'value' => $value]
                        );
                    }
                }
            }
            
            return $this->json([
                'success' => true,
                'message' => "$updated settings updated successfully"
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
            ]);
        }
    }
    
    public function reset()
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $group = $input['group'] ?? null;
            
            // Reset settings to default values
            $db = \App\Core\Database::getInstance();
            
            if ($group) {
                $stmt = $db->prepare("
                    UPDATE settings 
                    SET setting_value = default_value 
                    WHERE setting_group = ? AND default_value IS NOT NULL
                ");
                $stmt->execute([$group]);
            } else {
                $stmt = $db->prepare("
                    UPDATE settings 
                    SET setting_value = default_value 
                    WHERE default_value IS NOT NULL
                ");
                $stmt->execute();
            }
            
            // Clear cache
            SettingsService::clearCache();
            
            // Log the action
            GDPRService::logActivity(
                $_SESSION['user_id'] ?? null,
                'settings_reset',
                'settings',
                null,
                "Settings reset for group: " . ($group ?? 'all')
            );
            
            return $this->json([
                'success' => true,
                'message' => 'Settings reset to defaults'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error resetting settings: ' . $e->getMessage()
            ]);
        }
    }
    
    public function export()
    {
        $this->requireAdmin();
        
        $settings = SettingsService::getAll();
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="settings-export-' . date('Y-m-d') . '.json"');
        
        echo json_encode($settings, JSON_PRETTY_PRINT);
        exit;
    }
    
    public function import()
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/settings');
        }
        
        try {
            if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No file uploaded');
            }
            
            $content = file_get_contents($_FILES['import_file']['tmp_name']);
            $settings = json_decode($content, true);
            
            if (!$settings) {
                throw new \Exception('Invalid JSON file');
            }
            
            foreach ($settings as $key => $value) {
                SettingsService::set($key, $value);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error importing settings: ' . $e->getMessage()
            ]);
        }
    }
    
    private function handleFileUpload($file)
    {
        $uploadDir = __DIR__ . '/../../../public/uploads/settings/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = time() . '_' . basename($file['name']);
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/settings/' . $filename;
        }
        
        return null;
    }
    
    private function isCheckboxField($key)
    {
        $checkboxFields = [
            'enable_registration', 'require_email_verification', 'enable_2fa',
            'enable_captcha', 'enable_cookie_consent', 'enable_analytics',
            'enable_cache', 'enable_minification', 'enable_compression',
            'enable_lazy_loading', 'maintenance_mode', 'debug_mode',
            'enable_error_logging', 'enable_api', 'require_api_key',
            'enable_dark_mode', 'smtp_enabled', 'require_strong_password'
        ];
        
        return in_array($key, $checkboxFields);
    }
}
