<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use Exception;

/**
 * Main Admin Dashboard Controller - WordPress-like admin interface
 */
class MainDashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Main admin dashboard
     */
    public function index()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'Admin Dashboard - Bishwo Calculator',
            'widgets' => $this->getWidgets(),
            'menuItems' => $this->getMenuItems(),
            'activeModules' => $this->getActiveModules(),
            'currentUser' => $this->getCurrentUser(),
            'stats' => $this->getDashboardStats()
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    /**
     * Module management page
     */
    public function modules()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'Module Management',
            'allModules' => $this->getAllModules(),
            'activeModules' => $this->getActiveModules(),
            'menuItems' => $this->getMenuItems()
        ];
        
        $this->view('admin/modules', $data);
    }
    
    /**
     * Activate module
     */
    public function activateModule()
    {
        $this->checkAdminAccess();
        
        try {
            $moduleName = $_POST['module'] ?? '';
            
            if (empty($moduleName)) {
                throw new Exception('Module name required');
            }
            
            $result = $this->moduleManager->activateModule($moduleName);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Module activated successfully']);
            } else {
                throw new Exception('Failed to activate module');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Deactivate module
     */
    public function deactivateModule()
    {
        $this->checkAdminAccess();
        
        try {
            $moduleName = $_POST['module'] ?? '';
            
            if (empty($moduleName)) {
                throw new Exception('Module name required');
            }
            
            $result = $this->moduleManager->deactivateModule($moduleName);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Module deactivated successfully']);
            } else {
                throw new Exception('Failed to deactivate module');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Module settings page
     */
    public function moduleSettings($moduleName)
    {
        $this->checkAdminAccess();
        
        $activeModules = $this->moduleManager->getActiveModules();
        
        if (!isset($activeModules[$moduleName])) {
            $this->redirect('/admin/modules?error=module_not_found');
            return;
        }
        
        $module = $activeModules[$moduleName];
        $currentSettings = $this->moduleManager->getModuleSettings($moduleName);
        
        $data = [
            'page_title' => $module->getInfo()['name'] . ' Settings',
            'module' => $module,
            'moduleName' => $moduleName,
            'settingsSchema' => $module->getSettingsSchema(),
            'currentSettings' => $currentSettings,
            'menuItems' => $this->moduleManager->getMenuItems()
        ];
        
        $this->render('admin/module-settings', $data);
    }
    
    /**
     * Update module settings
     */
    public function updateModuleSettings()
    {
        $this->checkAdminAccess();
        
        try {
            $moduleName = $_POST['module'] ?? '';
            $settings = $_POST['settings'] ?? [];
            
            if (empty($moduleName)) {
                throw new Exception('Module name required');
            }
            
            $result = $this->moduleManager->updateModuleSettings($moduleName, $settings);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Settings updated successfully']);
            } else {
                throw new Exception('Failed to update settings');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Menu customization page
     */
    public function menuCustomization()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'Menu Customization',
            'menuItems' => $this->moduleManager->getMenuItems(),
            'availableModules' => $this->moduleManager->getActiveModules()
        ];
        
        $this->render('admin/menu-customization', $data);
    }
    
    /**
     * Widget management page
     */
    public function widgetManagement()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'Widget Management',
            'widgets' => $this->moduleManager->getWidgets(),
            'availableWidgets' => $this->getAvailableWidgets(),
            'menuItems' => $this->moduleManager->getMenuItems()
        ];
        
        $this->render('admin/widget-management', $data);
    }
    
    /**
     * System status page
     */
    public function systemStatus()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'System Status',
            'systemInfo' => $this->getSystemInfo(),
            'moduleStatus' => $this->getModuleStatus(),
            'menuItems' => $this->moduleManager->getMenuItems()
        ];
        
        $this->render('admin/system-status', $data);
    }
    
    /**
     * Check if user has admin access
     */
    private function checkAdminAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            return;
        }
        
        // Check if user is admin
        $userModel = new \App\Models\User();
        if (!$userModel->isAdmin($_SESSION['user_id'])) {
            $this->redirect('/dashboard?error=access_denied');
            return;
        }
    }
    
    /**
     * Get current user information
     */
    protected function getCurrentUser()
    {
        // Support both new structure ($_SESSION['user']) and legacy session keys
        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            return $_SESSION['user'];
        } else if (!empty($_SESSION['user_id'])) {
            // Build user array from legacy session vars
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'role' => $_SESSION['role'] ?? 'user',
                'full_name' => $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'
            ];
        }
        return [
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@engicalc.com',
            'role' => 'admin',
            'full_name' => 'Super Administrator'
        ];
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_users' => 1247,
            'active_users' => 892,
            'total_calculations' => 15673,
            'monthly_calculations' => 2341,
            'active_modules' => 12,
            'system_health' => 98.5,
            'storage_used' => 67,
            'api_requests' => 8934
        ];
    }

    /**
     * Get dashboard widgets
     */
    private function getWidgets()
    {
        return [
            'user_stats' => [
                'title' => 'User Statistics',
                'type' => 'chart',
                'data' => []
            ],
            'system_health' => [
                'title' => 'System Health',
                'type' => 'gauge',
                'data' => []
            ]
        ];
    }

    /**
     * Get menu items
     */
    private function getMenuItems()
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Users',
            'settings' => 'Settings',
            'modules' => 'Modules'
        ];
    }

    /**
     * Get active modules
     */
    private function getActiveModules()
    {
        return [
            'civil_engineering' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering',
            'mechanical' => 'Mechanical Engineering',
            'structural' => 'Structural Analysis'
        ];
    }

    /**
     * Get all available modules
     */
    private function getAllModules()
    {
        return [
            'civil_engineering' => [
                'name' => 'Civil Engineering',
                'active' => true,
                'description' => 'Concrete, steel, and structural calculations'
            ],
            'electrical' => [
                'name' => 'Electrical Engineering', 
                'active' => true,
                'description' => 'Load calculations and electrical design'
            ],
            'mechanical' => [
                'name' => 'Mechanical Engineering',
                'active' => true, 
                'description' => 'HVAC and mechanical systems'
            ],
            'structural' => [
                'name' => 'Structural Analysis',
                'active' => true,
                'description' => 'Structural analysis and design'
            ]
        ];
    }
    
    /**
     * Get available widgets from all modules
     */
    private function getAvailableWidgets()
    {
        return [
            'civil_engineering' => [
                'title' => 'Civil Engineering Tools',
                'description' => 'Concrete, steel, and structural calculations',
                'module' => 'civil_engineering'
            ],
            'electrical' => [
                'title' => 'Electrical Tools',
                'description' => 'Load calculations and electrical design',
                'module' => 'electrical'
            ]
        ];
    }
    
    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'loaded_extensions' => get_loaded_extensions()
        ];
    }
    
    /**
     * Get module status information
     */
    private function getModuleStatus()
    {
        return [
            'total_modules' => count($this->moduleManager->getAllModules()),
            'active_modules' => count($this->moduleManager->getActiveModules()),
            'inactive_modules' => count($this->moduleManager->getAllModules()) - count($this->moduleManager->getActiveModules())
        ];
    }
    
    /**
     * Render admin template
     */
    private function render($template, $data = [])
    {
        // Extract data for template use
        extract($data);
        
        // Add common admin data
        $currentUser = $this->getCurrentUser();
        
        // Template path
        $templatePath = __DIR__ . "/../../themes/admin/views/{$template}.php";
        
        if (file_exists($templatePath)) {
            // Include the template
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
            
            // Output the rendered template
            echo $output;
        } else {
            // Fallback to basic template
            $this->renderBasicTemplate($template, $data);
        }
    }
    
    /**
     * Basic template fallback
     */
    private function renderBasicTemplate($template, $data)
    {
        echo json_encode([
            'template' => $template,
            'data' => $data,
            'message' => 'Admin template system active'
        ]);
    }
    
    /**
     * JSON response helper
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    /**
     * Redirect helper
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
?>
