<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\AdminModuleManager;
use App\Models\User;
use App\Services\GeoLocationService;
use App\Services\InstallerService;
use Exception;

/**
 * Debug Controller - System Testing & Monitoring
 */
class DebugController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    /**
     * Debug dashboard - system overview
     */
    public function index()
    {
<<<<<<< HEAD
                $data = [
            'page_title' => 'Debug Dashboard - System Testing',
=======
        $data = [
            'page_title' => 'Debug Dashboard',
>>>>>>> temp-branch
            'system_info' => $this->getSystemInfo(),
            'recent_errors' => $this->getRecentErrors(),
            'test_results' => $this->runSystemTests(),
            'breadcrumbs' => [['title' => 'Debug Dashboard']]
        ];

        // Use standard view rendering which uses the theme system
        $this->view->render('admin/debug/dashboard', $data);
    }

    /**
     * Error logs viewer
     */
    public function errorLogs()
    {
<<<<<<< HEAD
                $page = $_GET['page'] ?? 1;
=======
        $page = $_GET['page'] ?? 1;
>>>>>>> temp-branch
        $filter = $_GET['filter'] ?? 'all';

        $logs = $this->getErrorLogs($page, $filter);

        $data = [
            'page_title' => 'Error Logs',
            'logs' => $logs,
            'current_page' => $page,
            'filter' => $filter,
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'Error Logs']
            ]
        ];

        $this->view->render('admin/debug/error-logs', $data);
    }

    /**
     * System tests runner
     */
    public function runTests()
    {
<<<<<<< HEAD
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
=======
        // Handle AJAX requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF Check
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                $this->json(['success' => false, 'error' => 'Invalid CSRF token']);
                return;
            }

>>>>>>> temp-branch
            $testType = $_POST['test_type'] ?? 'all';
            // Use cached test results if available (5‑second TTL)
            $results = $this->getCached('system_tests', function () use ($testType) {
                return $this->runSystemTests();
            }, 5);

            $this->json(['success' => true, 'results' => $results]);
            return;
        }

        if (empty($_SESSION['csrf_token'])) {
            if (class_exists('Security')) {
                \Security::generateCsrfToken();
            } else {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['csrf_expiry'] = time() + 3600;
            }
        }

        $data = [
            'page_title' => 'System Tests',
            'available_tests' => $this->getAvailableTests(),
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'System Tests']
            ]
        ];

        $this->view->render('admin/debug/tests', $data);
    }

    /**
     * Clear error logs
     */
    public function clearLogs()
    {
<<<<<<< HEAD
                try {
            $logFile = __DIR__ . '/../../storage/logs/error.log';
            
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
=======
        try {
            $logDir = __DIR__ . '/../../storage/logs';
            $cleared = 0;

            // Clear today's log file
            $todayLog = $logDir . '/' . date('Y-m-d') . '.log';
            if (file_exists($todayLog)) {
                file_put_contents($todayLog, '');
                $cleared++;
>>>>>>> temp-branch
            }

            // Optionally clear older log files (last 7 days)
            for ($i = 1; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $logFile = $logDir . '/' . $date . '.log';

                if (file_exists($logFile)) {
                    unlink($logFile);
                    $cleared++;
                }
            }

            $this->json(['success' => true, 'message' => "Cleared {$cleared} log file(s)"]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Live error monitoring
     */
    public function liveErrors()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $since = $_POST['since'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $errors = $this->getErrorsSince($since);

            $this->json(['success' => true, 'errors' => $errors]);
            return;
        }

        $data = [
            'page_title' => 'Live Error Monitor',
            'breadcrumbs' => [
                ['title' => 'Debug', 'url' => '/admin/debug'],
                ['title' => 'Live Monitor']
            ]
        ];

        $this->view->render('admin/debug/live-monitor', $data);
    }

    /**
     * Helper to output JSON
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
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
        $logDir = __DIR__ . '/../../storage/logs';
        $sinceTime = strtotime($since);
        $errors = [];

        // Read from daily log files (current and previous day)
        $dates = [
            date('Y-m-d'),
            date('Y-m-d', strtotime('-1 day'))
        ];

        foreach ($dates as $date) {
            $logFile = $logDir . '/' . $date . '.log';

            if (!file_exists($logFile)) {
                continue;
            }

            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $entry = @json_decode($line, true);

                if (!$entry || !isset($entry['timestamp'])) {
                    continue;
                }

                $logTime = strtotime($entry['timestamp']);

                if ($logTime >= $sinceTime) {
                    $errors[] = [
                        'timestamp' => $entry['timestamp'],
                        'message' => $entry['message'] ?? '',
                        'level' => $entry['level'] ?? 'info',
                        'context' => $entry['context'] ?? []
                    ];
                }
            }
        }
<<<<<<< HEAD
        
        return array_reverse($errors);
    }
    
    /**
     * Live error monitoring
     */
    public function liveErrors()
    {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $since = $_POST['since'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $errors = $this->getErrorsSince($since);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'errors' => $errors]);
            return;
=======

        // Also check PHP error log for critical errors
        $phpErrorLog = $logDir . '/php_error.log';
        if (file_exists($phpErrorLog)) {
            $phpLines = file($phpErrorLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recentPhpErrors = array_slice(array_reverse($phpLines), 0, 20);

            foreach ($recentPhpErrors as $line) {
                // Parse PHP error log format: [20-Nov-2025 08:16:02 UTC]
                if (preg_match('/^\[([^\]]+)\]\s+(.+)$/', $line, $matches)) {
                    $timestamp = $matches[1];
                    $message = $matches[2];

                    // Skip Xdebug timeout messages unless they're the only errors
                    if (strpos($message, 'Xdebug: [Step Debug] Time-out') !== false) {
                        continue;
                    }

                    $logTime = strtotime($timestamp);

                    if ($logTime >= $sinceTime) {
                        $errors[] = [
                            'timestamp' => date('Y-m-d H:i:s', $logTime),
                            'message' => $message,
                            'level' => 'error',
                            'context' => ['source' => 'php_error_log']
                        ];
                    }
                }
            }
>>>>>>> temp-branch
        }

        // Sort by timestamp descending
        usort($errors, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $errors;
    }

    /**
     * Get comprehensive system information
     */
    private function getSystemInfo()
    {
        // ---- Caching (5‑second TTL) ----
        return $this->getCached('system_info', function () {
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
        }, 5);
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
                'modules' => array_map(function ($module) use ($activeModules, $allModules) {
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
            $adminUsers = count(array_filter($userModel->getAll(), function ($user) {
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
            // Use absolute paths from project root
            // From app/Controllers/Admin we need to go up 3 levels to reach project root
            $rootPath = dirname(__DIR__, 3);
            $templatePath = $rootPath . '/themes/admin/layouts/main.php';
            $cssPath = $rootPath . '/themes/admin/assets/css/admin.css';
            $jsPath = $rootPath . '/themes/admin/assets/js/admin.js';

            $result = ['status' => 'pass', 'messages' => []];

            if (!file_exists($templatePath)) {
                $result['status'] = 'fail';
                $result['messages'][] = 'Admin layout missing';
            } else {
                $result['messages'][] = 'Admin layout found';
            }

            if (!file_exists($cssPath)) {
                $result['status'] = 'warning';
                $result['messages'][] = 'Admin CSS missing';
            } else {
                $result['messages'][] = 'Admin CSS found';
            }

            if (!file_exists($jsPath)) {
                $result['status'] = 'warning';
                $result['messages'][] = 'Admin JS missing';
            } else {
                $result['messages'][] = 'Admin JS found';
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
        $logDir = __DIR__ . '/../../storage/logs';
        $errors = [];

        // Read from today's log file
        $logFile = $logDir . '/' . date('Y-m-d') . '.log';

        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach (array_reverse($lines) as $line) {
                if (count($errors) >= $limit) {
                    break;
                }

                $entry = @json_decode($line, true);

                if ($entry && isset($entry['timestamp'])) {
                    $errors[] = [
                        'timestamp' => $entry['timestamp'],
                        'message' => $entry['message'] ?? '',
                        'level' => $entry['level'] ?? 'info',
                        'context' => $entry['context'] ?? []
                    ];
                }
            }
        }

        // If we need more, get from yesterday's log
        if (count($errors) < $limit) {
            $yesterdayLog = $logDir . '/' . date('Y-m-d', strtotime('-1 day')) . '.log';

            if (file_exists($yesterdayLog)) {
                $lines = file($yesterdayLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                foreach (array_reverse($lines) as $line) {
                    if (count($errors) >= $limit) {
                        break;
                    }

                    $entry = @json_decode($line, true);

                    if ($entry && isset($entry['timestamp'])) {
                        $errors[] = [
                            'timestamp' => $entry['timestamp'],
                            'message' => $entry['message'] ?? '',
                            'level' => $entry['level'] ?? 'info',
                            'context' => $entry['context'] ?? []
                        ];
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get error logs with pagination
     */
    private function getErrorLogs($page = 1, $filter = 'all')
    {
        $logDir = __DIR__ . '/../../storage/logs';
        $perPage = 50;
        $logs = [];

        // Read from multiple days (last 7 days)
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $logFile = $logDir . '/' . $date . '.log';

            if (!file_exists($logFile)) {
                continue;
            }

            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $entry = @json_decode($line, true);

                if (!$entry || !isset($entry['timestamp'])) {
                    continue;
                }

                $level = $entry['level'] ?? 'info';

                if ($filter === 'all' || $filter === $level) {
                    $logs[] = [
                        'timestamp' => $entry['timestamp'],
                        'message' => $entry['message'] ?? '',
                        'level' => $level,
                        'context' => $entry['context'] ?? []
                    ];
                }
            }
        }

        // Sort by timestamp descending
        usort($logs, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

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
<<<<<<< HEAD
    
    private function render($template, $data = [])
    {
        extract($data);
        $currentUser = (new User())->find($_SESSION['user_id']);
        
        ob_start();
        include __DIR__ . "/../../themes/admin/views/{$template}.php";
        echo ob_get_clean();
=======

    /**
     * Caches data for a given key with a specified TTL.
     *
     * @param string $key The cache key.
     * @param callable $callback The function to execute if data is not in cache.
     * @param int $ttl Time to live in seconds.
     * @return mixed The cached data or the result of the callback.
     */
    private function getCached(string $key, callable $callback, int $ttl = 60)
    {
        $cacheDir = __DIR__ . '/../../storage/cache';

        // Ensure cache directory exists
        if (!file_exists($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        $cacheFile = $cacheDir . '/' . md5($key) . '.cache';

        // Try to read from cache
        if (file_exists($cacheFile) && (filemtime($cacheFile) + $ttl) > time()) {
            $content = @file_get_contents($cacheFile);
            if ($content !== false) {
                $data = @unserialize($content);
                if ($data !== false) {
                    return $data;
                }
            }
        }

        // Execute callback to get fresh data
        $data = $callback();

        // Try to write to cache, but don't crash if it fails
        if (is_writable($cacheDir)) {
            @file_put_contents($cacheFile, serialize($data));
        }

        return $data;
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
>>>>>>> temp-branch
    }
}
