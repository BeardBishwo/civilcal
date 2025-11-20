<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class ModuleController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check if user is admin
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $modules = $this->getAllModules();
        $categories = $this->getModuleCategories();

        // Prepare data for the view
        $data = [
            'currentPage' => 'modules',
            'modules' => $modules,
            'categories' => $categories,
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

        // TODO: Implement actual activation logic
        // For now, return success
        echo json_encode([
            'success' => true,
            'message' => "Module '{$moduleName}' activated successfully"
        ]);
    }

    public function deactivate()
    {
        $moduleName = $_POST['module'] ?? '';

        if (empty($moduleName)) {
            echo json_encode(['success' => false, 'message' => 'Module name is required']);
            return;
        }

        // TODO: Implement actual deactivation logic
        // For now, return success
        echo json_encode([
            'success' => true,
            'message' => "Module '{$moduleName}' deactivated successfully"
        ]);
    }

    public function settings($params)
    {
        $moduleName = $params['module'] ?? '';

        if (empty($moduleName)) {
            $this->redirect('/admin/modules');
            return;
        }

        // TODO: Load module settings
        $data = [
            'currentPage' => 'modules',
            'moduleName' => $moduleName,
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

        // TODO: Implement settings update logic
        echo json_encode([
            'success' => true,
            'message' => "Settings for '{$moduleName}' updated successfully"
        ]);
    }

    private function getAllModules()
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
                // Count calculators in module
                $calcCount = $this->countCalculators($fullPath);
                $modules[] = [
                    'id' => count($modules) + 1,
                    'name' => ucwords(str_replace(['-', '_'], ' ', $dir)),
                    'slug' => $dir,
                    'description' => $this->getModuleDescription($dir),
                    'status' => 'active', // TODO: Get from settings
                    'calculators_count' => $calcCount,
                    'version' => '1.0.0',
                    'category' => $this->getCategoryFromName($dir)
                ];
            }
        }

        return $modules;
    }

    private function countCalculators($modulePath)
    {
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulePath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $count++;
            }
        }

        return $count;
    }

    private function getCategoryFromName($name)
    {
        $categories = [
            'civil' => 'engineering',
            'electrical' => 'engineering',
            'hvac' => 'engineering',
            'plumbing' => 'engineering',
            'structural' => 'engineering',
            'fire' => 'engineering',
            'estimation' => 'management',
            'project-management' => 'management',
            'mep' => 'engineering',
            'site' => 'engineering'
        ];

        return $categories[$name] ?? 'custom';
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
