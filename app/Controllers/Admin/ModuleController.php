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
        
        // Removed automatic file-to-db sync from index to improve performance
        // This is now handled by the sync() method
        
        // Re-fetch all from DB (now including newly added ones)
        $modules = $this->moduleService->getAllModules();
        $categories = $this->getModuleCategories();

        // Enhance DB modules with display categories AND file system stats
        $modulesPath = dirname(dirname(dirname(__DIR__))) . '/Calculators/';
        
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
            'title' => 'Modules Management - Admin Panel',
            'csrf_token' => $this->generateCsrfToken()
        ];

        // Load the view
        $this->view->render('admin/modules/index', $data);
    }

    public function activate()
    {
        // Validate CSRF
        if (empty($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

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
        // Validate CSRF
        if (empty($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

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
        $calculatorsPath = dirname(dirname(dirname(__DIR__))) . '/Calculators/';
        // Mapping: name 'civil' -> folder 'Civil' (First letter cap usually, or exact match if we standardizes)
        // Let's scan for case-insensitive match
        $targetDir = '';
        $scan = @scandir($calculatorsPath);
        if ($scan) {
            foreach($scan as $d) {
                if (strtolower($d) === strtolower($key)) {
                    $targetDir = $d;
                    break;
                }
            }
        }
        
        $moduleDir = $calculatorsPath . $targetDir;
        
        if ($targetDir && is_dir($moduleDir)) {
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
            'title' => "Module Settings: {$moduleName}",
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view->render('admin/modules/settings', $data);
    }

    public function updateSettings()
    {
        // Validate CSRF
        if (empty($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

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
     * Explicitly sync modules from file system to DB
     */
    public function sync()
    {
        // Validate CSRF
        if (empty($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $modulesFromDb = $this->moduleService->getAllModules();
        $modulesFromFile = $this->getAllModulesFromFileSystem();
        $newCount = 0;

        foreach ($modulesFromFile as $fModule) {
            $exists = false;
            foreach ($modulesFromDb as $dbModule) {
                if ($dbModule['name'] === $fModule['slug']) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $this->moduleService->createModule([
                    'name' => $fModule['slug'],
                    'description' => $fModule['description'],
                    'category' => $fModule['category'],
                    'is_active' => 1
                ]);
                $newCount++;
            }
        }

        echo json_encode(['success' => true, 'message' => "Synced successfully. {$newCount} new modules added."]);
    }

    /**
     * Get modules from the App/Calculators namespace
     * Replaces the legacy file-system check in 'modules/'
     */
    private function getAllModulesFromFileSystem()
    {
        // New Path: app/Calculators
        $calculatorsPath = dirname(dirname(__DIR__)) . '/Calculators/';
        $modules = [];

        if (!is_dir($calculatorsPath)) {
            error_log("Calculators directory not found: {$calculatorsPath}");
            return $modules;
        }

        $items = @scandir($calculatorsPath);
        if ($items === false) return $modules;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === 'CalculatorFactory.php' || $item === 'EnterprisePipeline.php' || $item === 'EnterpriseCalculator.php') continue;

            $fullPath = $calculatorsPath . $item;
            
            // In the new architecture, Folders = Modules (e.g., Civil, Electrical)
            if (is_dir($fullPath)) {
                $slug = strtolower($item);
                $stats = $this->getModuleStats($fullPath);
                
                $modules[] = [
                    'id' => count($modules) + 1,
                    'name' => $slug, // slug for DB sync
                    'slug' => $slug,
                    'display_name' => $item, // Capitalized Folder Name
                    'description' => $this->getModuleDescription($slug),
                    'status' => 'active', 
                    'calculators_count' => $stats['calculators'],
                    'subcategories_count' => 0, // Flattened architecture
                    'version' => '2.0.0 (Enterprise)',
                    'category' => $this->getCategoryFromName($slug)
                ];
            }
        }

        return $modules;
    }

    /**
     * Count calculators in the class-based structure
     */
    private function getModuleStats($modulePath)
    {
        $stats = [
            'calculators' => 0,
            'subcategories' => 0
        ];

        if (!is_dir($modulePath)) return $stats;

        $files = @scandir($modulePath);
        if ($files !== false) {
            foreach ($files as $file) {
                // Count Classes ending in Calculator.php
                if (strpos($file, 'Calculator.php') !== false) {
                    $stats['calculators']++;
                }
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
