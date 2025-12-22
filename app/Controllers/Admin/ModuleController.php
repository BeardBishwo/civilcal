<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ModuleService;

class ModuleController extends Controller
{
    private $moduleService;

    public function __construct()
    {
        parent::__construct();
        $this->moduleService = new ModuleService();

        // Check if user is admin
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        // Prevent browser caching
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $modulesFromDb = $this->moduleService->getAllModules();
        $modulesFromFile = $this->getAllModulesFromFileSystem();
        
        // Sync missing modules to DB
        foreach ($modulesFromFile as $fModule) {
            $exists = false;
            foreach ($modulesFromDb as $dbModule) {
                if ($dbModule['name'] === $fModule['slug']) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                // Add missing module to DB
                $this->moduleService->createModule([
                    'name' => $fModule['slug'],
                    'description' => $fModule['description'],
                    'category' => $fModule['category'],
                    'is_active' => 1
                ]);
            }
        }
        
        // Re-fetch all from DB (now including newly added ones)
        $modules = $this->moduleService->getAllModules();
        $categories = $this->getModuleCategories();

        // Enhance DB modules with display categories AND file system stats
        $modulesPath = dirname(dirname(dirname(__DIR__))) . '/modules/';
        
        foreach ($modules as &$module) {
            // The 'name' field in DB now contains the directory slug (e.g., 'civil', 'electrical')
            $key = $module['name'];
            $module['category'] = $this->getCategoryFromName($key);
            
            // Get actual file system stats for this module
            $moduleDir = $modulesPath . $key;
            if (is_dir($moduleDir)) {
                $fileStats = $this->getModuleStats($moduleDir);
                $module['calculators_count'] = $fileStats['calculators'];
                $module['subcategories_count'] = $fileStats['subcategories'];
                
                // Set display name
                $module['display_name'] = ucwords(str_replace(['-', '_'], ' ', $key));
            } else {
                // Fallback if directory doesn't exist
                $module['calculators_count'] = 0;
                $module['subcategories_count'] = 0;
                $module['display_name'] = ucwords(str_replace(['-', '_'], ' ', $key));
            }
            
            // Map is_active (database) to status (view)
            $module['status'] = (isset($module['is_active']) && $module['is_active']) ? 'active' : 'inactive';
        }

        // Calculate real stats from processed modules
        $stats = [
            'total' => count($modules),
            'active' => count(array_filter($modules, function($m) { return ($m['status'] ?? 'inactive') === 'active'; })),
            'inactive' => count(array_filter($modules, function($m) { return ($m['status'] ?? 'inactive') === 'inactive'; })),
            'categories' => count(array_unique(array_column($modules, 'category')))
        ];

        // Prepare data for the view
        $data = [
            'currentPage' => 'modules',
            'modules' => $modules,
            'categories' => $categories,
            'stats' => $stats,
            'title' => 'Modules Management - Admin Panel'
        ];

        // Load the view
        $this->view->render('admin/modules/index', $data);
    }

    public function activate()
    {
        $moduleName = $_POST['module'] ?? '';

        if (empty($moduleName)) {
            echo json_encode(['success' => false, 'message' => 'Module name is required']);
            return;
        }

        // Get module by name to get its ID
        $module = $this->moduleService->getModuleByName($moduleName);
        if (!$module) {
            echo json_encode(['success' => false, 'message' => 'Module not found']);
            return;
        }

        // Activate module by ID
        $result = $this->moduleService->activateModule($module['id']);
        echo json_encode($result);
    }

    public function deactivate()
    {
        $moduleName = $_POST['module'] ?? '';

        if (empty($moduleName)) {
            echo json_encode(['success' => false, 'message' => 'Module name is required']);
            return;
        }

        // Get module by name to get its ID
        $module = $this->moduleService->getModuleByName($moduleName);
        if (!$module) {
            echo json_encode(['success' => false, 'message' => 'Module not found']);
            return;
        }

        // Deactivate module by ID
        $result = $this->moduleService->deactivateModule($module['id']);
        echo json_encode($result);
    }

    public function settings($moduleName)
    {
        // Decode URL parameter if needed (though router usually does this)
        $moduleName = urldecode($moduleName);

        if (empty($moduleName)) {
            $this->redirect('/admin/modules');
            return;
        }

        $module = $this->moduleService->getModuleByName($moduleName);

        // Fallback: If not found in DB/Service (because it's file-based only), look in file system
        if (!$module) {
            $modules = $this->getAllModulesFromFileSystem();
            foreach ($modules as $m) {
                if ($m['name'] === $moduleName) {
                    $module = $m;
                    break;
                }
            }
        }

        if (!$module) {
            $this->redirect('/admin/modules');
            return;
        }

        // Enhance module with display category and file system stats
        $key = $module['name']; // DB 'name' field contains the directory slug
        $module['category'] = $this->getCategoryFromName($key);
        $module['display_name'] = ucwords(str_replace(['-', '_'], ' ', $key));
        
        // Get actual file system stats for this module
        $modulesPath = dirname(dirname(dirname(__DIR__))) . '/modules/';
        $moduleDir = $modulesPath . $key;
        if (is_dir($moduleDir)) {
            $stats = $this->getModuleStats($moduleDir);
            $module['calculators_count'] = $stats['calculators'];
            $module['subcategories_count'] = $stats['subcategories'];
        } else {
            $module['calculators_count'] = 0;
            $module['subcategories_count'] = 0;
        }
        
        // Map is_active (database) to status (view)
        $module['status'] = (isset($module['is_active']) && $module['is_active']) ? 'active' : 'inactive';

        $data = [
            'currentPage' => 'modules',
            'module' => $module,
            'title' => "Module Settings: {$moduleName}"
        ];

        $this->view->render('admin/modules/settings', $data);
    }

    public function updateSettings()
    {
        $moduleName = $_POST['module'] ?? '';

        if (empty($moduleName)) {
            echo json_encode(['success' => false, 'message' => 'Module name is required']);
            return;
        }

        // Get module by name to get its ID
        $module = $this->moduleService->getModuleByName($moduleName);
        if (!$module) {
            echo json_encode(['success' => false, 'message' => 'Module not found']);
            return;
        }

        $settingsData = $_POST['settings'] ?? [];
        $result = $this->moduleService->updateModuleConfig($module['id'], $settingsData);

        echo json_encode($result);
    }

    /**
     * Get modules from the file system as fallback
     */
    private function getAllModulesFromFileSystem()
    {
        // Get real modules from modules directory
        $modulesPath = dirname(dirname(dirname(__DIR__))) . '/modules/';
        $modules = [];

        // Add error handling for directory access
        if (!is_dir($modulesPath)) {
            error_log("Modules directory not found: {$modulesPath}");
            return $modules;
        }

        if (!is_readable($modulesPath)) {
            error_log("Modules directory not readable: {$modulesPath}");
            return $modules;
        }

        $dirs = @scandir($modulesPath);

        if ($dirs === false) {
            error_log("Failed to scan modules directory: {$modulesPath}");
            return $modules;
        }

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $fullPath = $modulesPath . $dir;
            if (is_dir($fullPath)) {
                // Get detailed stats about this module
                $stats = $this->getModuleStats($fullPath);
                
                $modules[] = [
                    'id' => count($modules) + 1,
                    'name' => ucwords(str_replace(['-', '_'], ' ', $dir)),
                    'slug' => $dir,
                    'description' => $this->getModuleDescription($dir),
                    'status' => 'active', // TODO: Get from settings
                    'calculators_count' => $stats['calculators'],
                    'subcategories_count' => $stats['subcategories'],
                    'version' => '1.0.0',
                    'category' => $this->getCategoryFromName($dir)
                ];
            }
        }

        return $modules;
    }

    /**
     * Count calculators and sub-categories in a module
     * Returns array with 'calculators' and 'subcategories' counts
     */
    private function getModuleStats($modulePath)
    {
        $stats = [
            'calculators' => 0,
            'subcategories' => 0
        ];

        if (!is_dir($modulePath)) {
            return $stats;
        }

        // Scan immediate subdirectories (these are sub-categories)
        $items = @scandir($modulePath);
        if ($items === false) {
            return $stats;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $itemPath = $modulePath . '/' . $item;
            
            if (is_dir($itemPath)) {
                // This is a sub-category
                $stats['subcategories']++;
                
                // Count PHP files in this sub-category
                $files = @scandir($itemPath);
                if ($files !== false) {
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'index.php') {
                            $stats['calculators']++;
                        }
                    }
                }
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'php' && $item !== 'index.php') {
                // Direct PHP file in module root (also a calculator)
                $stats['calculators']++;
            }
        }

        return $stats;
    }

    /**
     * Legacy method for backward compatibility
     */
    private function countCalculators($modulePath)
    {
        $stats = $this->getModuleStats($modulePath);
        return $stats['calculators'];
    }

    private function getCategoryFromName($name)
    {
        // specific mappings for nicer display
        $categories = [
            'civil' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering',
            'hvac' => 'HVAC & Cooling',
            'plumbing' => 'Plumbing & Sanitary',
            'structural' => 'Structural Analysis',
            'fire' => 'Fire Safety',
            'estimation' => 'Cost Estimation',
            'project-management' => 'Project Mgmt',
            'mep' => 'MEP Systems',
            'site' => 'Site Operations'
        ];

        // Default to capitalized name if not in map
        return $categories[$name] ?? ucwords(str_replace(['-', '_'], ' ', $name));
    }

    private function getModuleDescription($name)
    {
        $descriptions = [
            'civil' => 'Civil engineering calculations and tools',
            'electrical' => 'Electrical calculations and circuit design',
            'hvac' => 'HVAC load calculations and duct sizing',
            'plumbing' => 'Plumbing and drainage calculations',
            'structural' => 'Structural analysis and design',
            'fire' => 'Fire protection system calculations',
            'estimation' => 'Cost estimation and project budgeting',
            'project-management' => 'Project planning and management tools',
            'mep' => 'MEP coordination and integration tools',
            'site' => 'Site work and construction tools'
        ];

        return $descriptions[$name] ?? 'Module for ' . ucwords(str_replace(['-', '_'], ' ', $name));
    }

    private function getModuleCategories()
    {
        return [
            'engineering' => 'Engineering',
            'management' => 'Project Management',
            'analysis' => 'Analysis Tools',
            'custom' => 'Custom Modules'
        ];
    }
}
