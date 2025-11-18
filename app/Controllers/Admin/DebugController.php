<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\AdminModuleManager;
use App\Models\User;
use App\Services\GeoLocationService;
use App\Services\InstallerService;
use Exception;

/**
 * Debug Controller - System testing and error log viewing
 */
class DebugController extends Controller
{
    /**
     * Debug dashboard - system overview
     */
    public function index()
    {
        $this->checkAdminAccess();
        
        $data = [
            'page_title' => 'Debug Dashboard - System Testing',
            'system_info' => $this->getSystemInfo(),
            'recent_errors' => $this->getRecentErrors(),
            'test_results' => $this->runSystemTests(),
            'breadcrumbs' => [['title' => 'Debug Dashboard']]
        ];
        
        $this->render('debug/dashboard', $data);
    }
    
    /**
     * Error logs viewer
     */
    public function errorLogs()
    {
        $this->checkAdminAccess();
        
        $page = $_GET['page'] ?? 1;
        $filter = $_GET['filter'] ?? 'all';
        
        $logs = $this->getErrorLogs($page, $filter);
        
        $data = [
            'page_title' => 'Error Logs - System Debug',
            'logs' => $logs,
            'current_page' => $page,
            'filter' => $filter,
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'Error Logs']
            ]
        ];
        
        $this->render('debug/error-logs', $data);
    }
    
    /**
     * System tests runner
     */
    public function runTests()
    {
        $this->checkAdminAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $testType = $_POST['test_type'] ?? 'all';
            $results = $this->runSystemTests();
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'results' => $results]);
            return;
        }
        
        $data = [
            'page_title' => 'System Tests - Debug Tools',
            'available_tests' => $this->getAvailableTests(),
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'System Tests']
            ]
        ];
        
        $this->render('debug/tests', $data);
    }
    
    /**
     * Clear error logs
     */
    public function clearLogs()
    {
        $this->checkAdminAccess();
        
        try {
            $logFile = __DIR__ . '/../../storage/logs/error.log';
            
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Error logs cleared']);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get available test categories
     */
    private function getAvailableTests()
    {
        return [
            'system' => 'System Requirements',
            'database' => 'Database Connection',
            'modules' => 'Module System',
            'auth' => 'Authentication',
            'services' => 'Services',
            'files' => 'File Permissions',
            'all' => 'All Tests'
        ];
    }
    
    /**
     * Get errors since a specific timestamp
     */
    private function getErrorsSince($since)
    {
        $logFile = __DIR__ . '/../../storage/logs/error.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $sinceTime = strtotime($since);
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errors = [];
        
        foreach (array_reverse($lines) as $line) {
            if (preg_match('/^\[([^\]]+)\]\s+(.+)$/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                
                if ($logTime >= $sinceTime) {
                    $errors[] = [
                        'timestamp' => $matches[1],
                        'message' => $matches[2],
                        'level' => $this->getLogLevel($matches[2])
                    ];
                }
            }
        }
        
        return array_reverse($errors);
    }
    
    /**
     * Live error monitoring
     */
    public function liveErrors()
    {
        $this->checkAdminAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $since = $_POST['since'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $errors = $this->getErrorsSince($since);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'errors' => $errors]);
            return;
        }
        
        $data = [
            'page_title' => 'Live Error Monitor',
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'Live Monitor']
            ]
        ];
        
        $this->render('debug/live-monitor', $data);
    }
    
    /**
     * Get comprehensive system information
     */
    private function getSystemInfo()
    {
        return [
            'php' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'memory_usage' => $this->formatBytes(memory_get_usage(true)),
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'execution_time' => ini_get('max_execution_time'),
                'error_reporting' => error_reporting(),
                'display_errors' => ini_get('display_errors'),
                'log_errors' => ini_get('log_errors'),
                'error_log_file' => ini_get('error_log')
            ],
            'server' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
                'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time())
            ],
            'database' => $this->getDatabaseInfo(),
            'files' => $this->getFileSystemInfo(),
            'modules' => $this->getModuleInfo()
        ];
    }
    
    /**
     * Get database connection info
     */
    private function getDatabaseInfo()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            
            return [
                'status' => 'Connected',
                'version' => $pdo->query('SELECT VERSION()')->fetchColumn(),
                'charset' => $pdo->query('SELECT @@character_set_database')->fetchColumn(),
                'timezone' => $pdo->query('SELECT @@session.time_zone')->fetchColumn(),
                'tables' => $this->getDatabaseTables()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'Error',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get database tables info
     */
    private function getDatabaseTables()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            
            $stmt = $pdo->query('SHOW TABLES');
            $tables = [];
            
            while ($table = $stmt->fetchColumn()) {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
                $count = $countStmt->fetchColumn();
                $tables[$table] = $count;
            }
            
            return $tables;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get file system information
     */
    private function getFileSystemInfo()
    {
        $storage = __DIR__ . '/../../storage';
        
        return [
            'storage_writable' => is_writable($storage),
            'storage_size' => $this->getDirectorySize($storage),
            'log_file_exists' => file_exists($storage . '/logs/error.log'),
            'cache_dir_writable' => is_writable($storage . '/cache'),
            'uploads_dir_writable' => is_writable($storage . '/uploads'),
            'disk_free_space' => $this->formatBytes(disk_free_space('.')),
            'disk_total_space' => $this->formatBytes(disk_total_space('.'))
        ];
    }
    
    /**
     * Get module information
     */
    private function getModuleInfo()
    {
        try {
            $moduleManager = AdminModuleManager::getInstance();
            $allModules = $moduleManager->getAllModules();
            $activeModules = $moduleManager->getActiveModules();
            
            return [
                'total' => count($allModules),
                'active' => count($activeModules),
                'inactive' => count($allModules) - count($activeModules),
                'modules' => array_map(function($module) use ($activeModules, $allModules) {
                    $moduleName = array_search($module, $allModules);
                    return [
                        'name' => $module['name'],
                        'version' => $module['version'] ?? '1.0.0',
                        'active' => isset($activeModules[$moduleName])
                    ];
                }, $allModules)
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Run comprehensive system tests
     */
    private function runSystemTests()
    {
        $tests = [
            'PHP Version' => $this->testPhpVersion(),
            'Database Connection' => $this->testDatabaseConnection(),
            'File Permissions' => $this->testFilePermissions(),
            'Module System' => $this->testModuleSystem(),
            'User Authentication' => $this->testUserAuth(),
            'GeoLocation Service' => $this->testGeoLocation(),
            'Installer Service' => $this->testInstallerService(),
            'Admin Panel' => $this->testAdminPanel()
        ];
        
        return $tests;
    }
    
    /**
     * Test PHP version and extensions
     */
    private function testPhpVersion()
    {
        $result = ['status' => 'pass', 'messages' => []];
        
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $result['status'] = 'fail';
            $result['messages'][] = 'PHP 7.4+ required, found ' . PHP_VERSION;
        } else {
            $result['messages'][] = 'PHP version: ' . PHP_VERSION;
        }
        
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'curl', 'openssl'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $result['status'] = 'fail';
                $result['messages'][] = "Missing extension: {$ext}";
            }
        }
        
        return $result;
    }
    
    /**
     * Test database connection
     */
    private function testDatabaseConnection()
    {
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            $pdo->query('SELECT 1');
            
            return [
                'status' => 'pass',
                'messages' => ['Database connection successful']
            ];
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'messages' => ['Database error: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Test file permissions
     */
    private function testFilePermissions()
    {
        $result = ['status' => 'pass', 'messages' => []];
        
        $paths = [
            '../storage' => 'Storage directory',
            '../storage/logs' => 'Logs directory',
            '../storage/cache' => 'Cache directory',
            '../config' => 'Config directory'
        ];
        
        foreach ($paths as $path => $name) {
            if (!is_writable($path)) {
                $result['status'] = 'fail';
                $result['messages'][] = "{$name} not writable";
            } else {
                $result['messages'][] = "{$name} writable";
            }
        }
        
        return $result;
    }
    
    /**
     * Test module system
     */
    private function testModuleSystem()
    {
        try {
            $moduleManager = AdminModuleManager::getInstance();
            $modules = $moduleManager->getAllModules();
            
            return [
                'status' => 'pass',
                'messages' => [
                    'Module manager initialized',
                    count($modules) . ' modules loaded'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'messages' => ['Module system error: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Test user authentication
     */
    private function testUserAuth()
    {
        try {
            $userModel = new User();
            $adminUsers = count(array_filter($userModel->getAll(), function($user) {
                return in_array($user['role'], ['admin', 'super_admin']);
            }));
            
            return [
                'status' => 'pass',
                'messages' => [
                    'User model loaded',
                    "{$adminUsers} admin users found"
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'messages' => ['Auth system error: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Test GeoLocation service
     */
    private function testGeoLocation()
    {
        try {
            $geoService = new GeoLocationService();
            $status = $geoService->getStatus();
            
            return [
                'status' => 'pass',
                'messages' => ['GeoLocation service initialized']
            ];
        } catch (Exception $e) {
            return [
                'status' => 'warning',
                'messages' => ['GeoLocation warning: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Test installer service
     */
    private function testInstallerService()
    {
        try {
            $canDelete = InstallerService::shouldAutoDelete();
            $isProcessed = InstallerService::isInstallerProcessed();
            
            return [
                'status' => 'pass',
                'messages' => [
                    'Installer service loaded',
                    'Auto-delete: ' . ($canDelete ? 'enabled' : 'disabled'),
                    'Processed: ' . ($isProcessed ? 'yes' : 'no')
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'messages' => ['Installer error: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Test admin panel access
     */
    private function testAdminPanel()
    {
        try {
            $templatePath = __DIR__ . '/../../themes/admin/layouts/main.php';
            $cssPath = __DIR__ . '/../../themes/admin/assets/css/admin.css';
            $jsPath = __DIR__ . '/../../themes/admin/assets/js/admin.js';
            
            $result = ['status' => 'pass', 'messages' => []];
            
            if (!file_exists($templatePath)) {
                $result['status'] = 'fail';
                $result['messages'][] = 'Admin layout missing';
            }
            
            if (!file_exists($cssPath)) {
                $result['status'] = 'warning';
                $result['messages'][] = 'Admin CSS missing';
            }
            
            if (!file_exists($jsPath)) {
                $result['status'] = 'warning';
                $result['messages'][] = 'Admin JS missing';
            }
            
            if (empty($result['messages'])) {
                $result['messages'][] = 'Admin panel files present';
            }
            
            return $result;
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'messages' => ['Admin panel error: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Get recent error logs
     */
    private function getRecentErrors($limit = 50)
    {
        $logFile = __DIR__ . '/../../storage/logs/error.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $lines = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        $errors = [];
        
        foreach (array_slice($lines, 0, $limit) as $line) {
            if (preg_match('/^\[([^\]]+)\]\s+(.+)$/', $line, $matches)) {
                $errors[] = [
                    'timestamp' => $matches[1],
                    'message' => $matches[2],
                    'level' => $this->getLogLevel($matches[2])
                ];
            }
        }
        
        return $errors;
    }
    
    /**
     * Get error logs with pagination
     */
    private function getErrorLogs($page = 1, $filter = 'all')
    {
        $logFile = __DIR__ . '/../../storage/logs/error.log';
        $perPage = 50;
        
        if (!file_exists($logFile)) {
            return ['logs' => [], 'total' => 0, 'pages' => 0];
        }
        
        $lines = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        $logs = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^\[([^\]]+)\]\s+(.+)$/', $line, $matches)) {
                $level = $this->getLogLevel($matches[2]);
                
                if ($filter === 'all' || $filter === $level) {
                    $logs[] = [
                        'timestamp' => $matches[1],
                        'message' => $matches[2],
                        'level' => $level
                    ];
                }
            }
        }
        
        $total = count($logs);
        $pages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $logs = array_slice($logs, $offset, $perPage);
        
        return [
            'logs' => $logs,
            'total' => $total,
            'pages' => $pages
        ];
    }
    
    /**
     * Get log level from message
     */
    private function getLogLevel($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'fatal') !== false) return 'fatal';
        if (strpos($message, 'error') !== false) return 'error';
        if (strpos($message, 'warning') !== false) return 'warning';
        if (strpos($message, 'notice') !== false) return 'notice';
        if (strpos($message, 'info') !== false) return 'info';
        
        return 'debug';
    }
    
    /**
     * Helper methods
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $this->formatBytes($size);
    }
    
    private function checkAdminAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . app_base_url('/login?redirect=' . urlencode($_SERVER['REQUEST_URI'])));
            exit;
        }
        
        $userModel = new User();
        if (!$userModel->isAdmin($_SESSION['user_id'])) {
            header('Location: ' . app_base_url('/dashboard?error=access_denied'));
            exit;
        }
    }
    
    private function render($template, $data = [])
    {
        extract($data);
        $currentUser = (new User())->find($_SESSION['user_id']);
        
        ob_start();
        include __DIR__ . "/../../themes/admin/views/{$template}.php";
        echo ob_get_clean();
    }
}
?>
