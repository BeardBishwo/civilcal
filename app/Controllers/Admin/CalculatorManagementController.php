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
    
    public function create()
    {
        // Load available categories and templates
        $categories = ['civil', 'electrical', 'plumbing', 'hvac', 'fire', 'site', 'structural', 'estimation', 'mep', 'project-management', 'country'];
        $templates = $this->getTemplates();
        
        $data = [
            'currentPage' => 'calculator_create',
            'categories' => $categories,
            'templates' => $templates,
            'title' => 'Create Calculator - Admin Panel'
        ];
        
        $this->view->render('admin/calculators/create', $data);
    }
    
    public function edit($id)
    {
        // Load calculator configuration
        $calculator = $this->loadCalculatorConfig($id);
        
        if (!$calculator) {
            $_SESSION['flash_error'] = 'Calculator not found';
            $this->redirect('/admin/calculators');
            return;
        }
        
        $categories = ['civil', 'electrical', 'plumbing', 'hvac', 'fire', 'site', 'structural', 'estimation', 'mep', 'project-management', 'country'];
        
        $data = [
            'currentPage' => 'calculator_edit',
            'calculator' => $calculator,
            'categories' => $categories,
            'title' => 'Edit Calculator - Admin Panel'
        ];
        
        $this->view->render('admin/calculators/edit', $data);
    }
    
    public function store()
    {
        if (!\App\Services\Security::validateCsrfToken()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        // Validate required fields
        $required = ['name', 'slug', 'category', 'description'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'message' => "Field {$field} is required"]);
                return;
            }
        }
        
        // Save to config file
        $category = $data['category'];
        $slug = $data['slug'];
        $configFile = BASE_PATH . "/app/Config/Calculators/{$category}.php";
        
        if (!file_exists($configFile)) {
            echo json_encode(['success' => false, 'message' => 'Category config file not found']);
            return;
        }
        
        // Load existing config
        $config = include $configFile;
        
        // Add new calculator
        $config[$slug] = [
            'name' => $data['name'],
            'description' => $data['description'],
            'category' => $category,
            'subcategory' => $data['subcategory'] ?? '',
            'version' => '1.0',
            'inputs' => $data['inputs'] ?? [],
            'formulas' => $this->parseFormulas($data['formulas'] ?? []),
            'outputs' => $data['outputs'] ?? []
        ];
        
        // Save config
        $this->saveConfigFile($configFile, $config);
        
        // Create frontend file
        $this->createFrontendFile($category, $data['subcategory'] ?? '', $slug);
        
        // Update calculator_urls table
        $this->updateCalculatorUrls($category, $data['subcategory'] ?? '', $slug);
        
        echo json_encode(['success' => true, 'message' => 'Calculator created successfully', 'slug' => $slug]);
    }
    
    public function update($id)
    {
        if (!\App\Services\Security::validateCsrfToken()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        // Parse ID (format: category.slug)
        list($category, $slug) = explode('.', $id, 2);
        
        $configFile = BASE_PATH . "/app/Config/Calculators/{$category}.php";
        
        if (!file_exists($configFile)) {
            echo json_encode(['success' => false, 'message' => 'Category config file not found']);
            return;
        }
        
        // Load existing config
        $config = include $configFile;
        
        if (!isset($config[$slug])) {
            echo json_encode(['success' => false, 'message' => 'Calculator not found']);
            return;
        }
        
        // Update calculator
        $config[$slug] = array_merge($config[$slug], [
            'name' => $data['name'] ?? $config[$slug]['name'],
            'description' => $data['description'] ?? $config[$slug]['description'],
            'inputs' => $data['inputs'] ?? $config[$slug]['inputs'],
            'formulas' => $this->parseFormulas($data['formulas'] ?? $config[$slug]['formulas']),
            'outputs' => $data['outputs'] ?? $config[$slug]['outputs']
        ]);
        
        // Save config
        $this->saveConfigFile($configFile, $config);
        
        echo json_encode(['success' => true, 'message' => 'Calculator updated successfully']);
    }
    
    public function delete($id)
    {
        if (!\App\Services\Security::validateCsrfToken()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        
        // Parse ID (format: category.slug)
        list($category, $slug) = explode('.', $id, 2);
        
        $configFile = BASE_PATH . "/app/Config/Calculators/{$category}.php";
        
        if (!file_exists($configFile)) {
            echo json_encode(['success' => false, 'message' => 'Category config file not found']);
            return;
        }
        
        // Load existing config
        $config = include $configFile;
        
        if (!isset($config[$slug])) {
            echo json_encode(['success' => false, 'message' => 'Calculator not found']);
            return;
        }
        
        // Remove calculator
        unset($config[$slug]);
        
        // Save config
        $this->saveConfigFile($configFile, $config);
        
        echo json_encode(['success' => true, 'message' => 'Calculator deleted successfully']);
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
    
    private function getTemplates()
    {
        return [
            'simple-calculator' => [
                'name' => 'Simple Calculator',
                'description' => 'Basic two-input calculator',
                'inputs' => [
                    ['name' => 'value1', 'type' => 'number', 'label' => 'Value 1', 'required' => true],
                    ['name' => 'value2', 'type' => 'number', 'label' => 'Value 2', 'required' => true]
                ],
                'formulas' => ['result' => 'value1 + value2'],
                'outputs' => [['name' => 'result', 'label' => 'Result', 'unit' => '', 'precision' => 2]]
            ],
            'area-calculator' => [
                'name' => 'Area Calculator',
                'description' => 'Calculate area from length and width',
                'inputs' => [
                    ['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true],
                    ['name' => 'width', 'type' => 'number', 'label' => 'Width', 'unit' => 'm', 'required' => true]
                ],
                'formulas' => ['area' => 'length * width'],
                'outputs' => [['name' => 'area', 'label' => 'Area', 'unit' => 'm²', 'precision' => 2]]
            ],
            'volume-calculator' => [
                'name' => 'Volume Calculator',
                'description' => 'Calculate volume from dimensions',
                'inputs' => [
                    ['name' => 'length', 'type' => 'number', 'label' => 'Length', 'unit' => 'm', 'required' => true],
                    ['name' => 'width', 'type' => 'number', 'label' => 'Width', 'unit' => 'm', 'required' => true],
                    ['name' => 'height', 'type' => 'number', 'label' => 'Height', 'unit' => 'm', 'required' => true]
                ],
                'formulas' => ['volume' => 'length * width * height'],
                'outputs' => [['name' => 'volume', 'label' => 'Volume', 'unit' => 'm³', 'precision' => 3]]
            ]
        ];
    }
    
    private function loadCalculatorConfig($id)
    {
        // Parse ID (format: category.slug)
        list($category, $slug) = explode('.', $id, 2);
        
        $configFile = BASE_PATH . "/app/Config/Calculators/{$category}.php";
        
        if (!file_exists($configFile)) {
            return null;
        }
        
        $config = include $configFile;
        
        if (!isset($config[$slug])) {
            return null;
        }
        
        $calculator = $config[$slug];
        $calculator['id'] = $id;
        $calculator['slug'] = $slug;
        $calculator['category'] = $category;
        
        return $calculator;
    }
    
    private function parseFormulas($formulas)
    {
        // Convert string formulas to closures if needed
        $parsed = [];
        foreach ($formulas as $key => $formula) {
            if (is_string($formula)) {
                $parsed[$key] = $formula; // Keep as string for now
            } else {
                $parsed[$key] = $formula;
            }
        }
        return $parsed;
    }
    
    private function saveConfigFile($file, $config)
    {
        $export = var_export($config, true);
        $content = "<?php\n\nreturn {$export};\n";
        file_put_contents($file, $content);
    }
    
    private function createFrontendFile($category, $subcategory, $slug)
    {
        $dir = BASE_PATH . "/modules/{$category}";
        if ($subcategory) {
            $dir .= "/{$subcategory}";
        }
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $file = $dir . "/{$slug}.php";
        
        if (!file_exists($file)) {
            $content = "<?php\n/**\n * {$slug} Calculator - Migrated to Calculator Engine\n */\nrequire_once dirname(__DIR__, 3) . '/app/bootstrap.php';\nrequire_once dirname(__DIR__, 3) . '/themes/default/views/shared/calculator-template.php';\nrenderCalculator('{$slug}');\n";
            file_put_contents($file, $content);
        }
    }
    
    private function updateCalculatorUrls($category, $subcategory, $slug)
    {
        try {
            $db = \App\Core\Database::getInstance()->getPdo();
            $stmt = $db->prepare("
                INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at)
                VALUES (:id, :cat, :sub, :slug, :path, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                category = VALUES(category),
                subcategory = VALUES(subcategory),
                updated_at = NOW()
            ");
            
            $path = $category;
            if ($subcategory) {
                $path .= "/{$subcategory}";
            }
            $path .= "/{$slug}.php";
            
            $stmt->execute([
                ':id' => $slug,
                ':cat' => $category,
                ':sub' => $subcategory,
                ':slug' => $slug,
                ':path' => $path
            ]);
        } catch (\Exception $e) {
            // Silent fail - table might not exist yet
        }
    }
}
