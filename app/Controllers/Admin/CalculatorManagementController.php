<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class CalculatorManagementController extends Controller
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
        $calculators = $this->scanCalculators();
        $statusConfig = $this->loadStatusConfig();
        
        // Get module statuses for cascaded control
        $moduleService = new \App\Services\ModuleService();
        $dbModules = $moduleService->getAllModules();
        $moduleStatuses = [];
        foreach ($dbModules as $m) {
            $moduleStatuses[$m['name']] = (isset($m['is_active']) && $m['is_active']) ? 'active' : 'inactive';
        }

        // Apply Status
        foreach ($calculators as &$calc) {
            $moduleStatus = $moduleStatuses[$calc['module_slug']] ?? 'active';
            
            if ($moduleStatus === 'inactive') {
                $calc['status'] = 'module_disabled';
            } else {
                // Default to Active if not in config
                $status = $statusConfig[$calc['unique_id']] ?? 'active';
                $calc['status'] = $status;
            }
        }
        unset($calc);

        // Filters
        $search = $_GET['search'] ?? '';
        $module = $_GET['module'] ?? '';

        if (!empty($search)) {
            $calculators = array_filter($calculators, function($c) use ($search) {
                return stripos($c['name'], $search) !== false || stripos($c['module_name'], $search) !== false;
            });
        }

        if (!empty($module)) {
            $calculators = array_filter($calculators, function($c) use ($module) {
                return strtolower($c['module_slug']) === strtolower($module);
            });
        }

        $modules = array_unique(array_column($calculators, 'module_name'));
        sort($modules);

        // Calculate Stats
        $stats = [
            'total' => count($calculators),
            'active' => count(array_filter($calculators, fn($c) => $c['status'] === 'active')),
            'inactive' => count(array_filter($calculators, fn($c) => $c['status'] === 'inactive' || $c['status'] === 'module_disabled')),
            'modules' => count($modules)
        ];

        $data = [
            'currentPage' => 'calculators_management',
            'calculators' => $calculators,
            'modules' => $modules,
            'stats' => $stats,
            'filters' => ['search' => $search, 'module' => $module],
            'title' => 'Calculators Management - Admin Panel'
        ];

        $this->view->render('admin/calculators/index', $data);
    }
    
    public function toggle()
    {
        // Simple JSON-based toggle instead of DB for now
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        if (!\App\Services\Security::validateCsrfToken()) {
             http_response_code(403);
             echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
             return;
        }

        $id = $_POST['id'] ?? '';
        $action = $_POST['action'] ?? ''; // activate/deactivate
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID required']);
            return;
        }

        $config = $this->loadStatusConfig();
        $config[$id] = ($action === 'activate') ? 'active' : 'inactive';
        $this->saveStatusConfig($config);

        echo json_encode(['success' => true]);
    }

    private function loadStatusConfig()
    {
        $file = BASE_PATH . '/storage/app/calculators_status.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true) ?? [];
        }
        return [];
    }

    private function saveStatusConfig($config)
    {
        $file = BASE_PATH . '/storage/app/calculators_status.json';
        $dir = dirname($file);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));
    }

    private function scanCalculators()
    {
        $modulesPath = BASE_PATH . '/modules/';
        $calculators = [];

        if (!is_dir($modulesPath)) {
            return [];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulesPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $realBasePath = realpath(BASE_PATH);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $realPath = $file->getRealPath();
                
                // Robust path calculation relative to BASE_PATH/modules
                // c:\...\modules\civil\beam.php -> civil\beam.php
                $relativePath = str_ireplace($realBasePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR, '', $realPath);
                
                // Normalize slashes
                $relativePath = str_replace('\\', '/', $relativePath);
                
                $parts = explode('/', $relativePath);
                
                if (count($parts) >= 2) {
                    $moduleSlug = $parts[0];
                    $calculatorSlug = pathinfo($realPath, PATHINFO_FILENAME);
                    
                    // Unique ID for storage (e.g. civil.beam)
                    $uniqueId = $moduleSlug . '.' . $calculatorSlug;
                    
                    $moduleName = ucwords(str_replace(['-', '_'], ' ', $moduleSlug));
                    
                    // Special Case for Nepali Land Converter
                    if ($uniqueId === 'country.nepali-land') {
                        $calculatorName = 'Nepali Land Converter';
                        $url = '/nepali';
                    } else {
                        $calculatorName = ucwords(str_replace(['-', '_'], ' ', $calculatorSlug));
                        $url = "/calculator/{$moduleSlug}/{$calculatorSlug}";
                    }

                    $calculators[] = [
                        'unique_id' => $uniqueId,
                        'name' => $calculatorName,
                        'slug' => $calculatorSlug,
                        'module_name' => $moduleName,
                        'module_slug' => $moduleSlug,
                        'path' => $relativePath,
                        'url' => $url,
                    ];
                }
            }
        }

        usort($calculators, function($a, $b) {
            if ($a['module_name'] === $b['module_name']) {
                return strcmp($a['name'], $b['name']);
            }
            return strcmp($a['module_name'], $b['module_name']);
        });

        return $calculators;
    }
}
