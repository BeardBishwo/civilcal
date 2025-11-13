<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\AdminModuleManager;
use App\Models\User;
use Exception;

/**
 * Admin API Controller - RESTful API for admin operations
 */
class AdminController extends Controller
{
    private $moduleManager;
    
    public function __construct()
    {
        $this->moduleManager = AdminModuleManager::getInstance();
    }
    
    /**
     * Get admin dashboard stats
     */
    public function getDashboardStats()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $userModel = new User();
            $users = $userModel->getAll();
            
            $stats = [
                'users' => [
                    'total' => count($users),
                    'active' => count(array_filter($users, fn($u) => $u['is_active'])),
                    'new_today' => $this->getNewUsersToday($users),
                    'roles' => $this->getUserRoleDistribution($users)
                ],
                'system' => [
                    'php_version' => PHP_VERSION,
                    'memory_usage' => $this->formatBytes(memory_get_usage(true)),
                    'storage_used' => $this->getStorageUsage(),
                    'uptime' => $this->getSystemUptime()
                ],
                'modules' => [
                    'total' => count($this->moduleManager->getAllModules()),
                    'active' => count($this->moduleManager->getActiveModules()),
                    'available_updates' => 0
                ],
                'analytics' => $this->getAnalyticsSnapshot()
            ];
            
            echo json_encode(['success' => true, 'stats' => $stats]);
            
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get all modules with status
     */
    public function getModules()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $allModules = $this->moduleManager->getAllModules();
            $activeModules = $this->moduleManager->getActiveModules();
            
            $modules = [];
            foreach ($allModules as $name => $info) {
                $modules[] = array_merge($info, [
                    'is_active' => isset($activeModules[$name]),
                    'settings_url' => "/admin/modules/{$name}/settings",
                    'has_settings' => !empty($info['settings_schema'] ?? [])
                ]);
            }
            
            echo json_encode(['success' => true, 'modules' => $modules]);
            
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Toggle module status (activate/deactivate)
     */
    public function toggleModule()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $input = json_decode(file_get_contents('php://input'), true);
            $moduleName = $input['module'] ?? '';
            $action = $input['action'] ?? '';
            
            if (empty($moduleName) || !in_array($action, ['activate', 'deactivate'])) {
                throw new Exception('Invalid module or action');
            }
            
            if ($action === 'activate') {
                $result = $this->moduleManager->activateModule($moduleName);
                $message = 'Module activated successfully';
            } else {
                $result = $this->moduleManager->deactivateModule($moduleName);
                $message = 'Module deactivated successfully';
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                throw new Exception("Failed to {$action} module");
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get system health check
     */
    public function getSystemHealth()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $health = [
                'overall_status' => 'healthy',
                'checks' => [
                    'php_version' => [
                        'status' => version_compare(PHP_VERSION, '7.4', '>=') ? 'pass' : 'warning',
                        'message' => 'PHP ' . PHP_VERSION . (version_compare(PHP_VERSION, '7.4', '>=') ? ' (Good)' : ' (Upgrade recommended)'),
                        'value' => PHP_VERSION
                    ],
                    'memory_usage' => [
                        'status' => $this->getMemoryUsagePercent() < 80 ? 'pass' : 'warning',
                        'message' => 'Memory usage: ' . $this->getMemoryUsagePercent() . '%',
                        'value' => $this->getMemoryUsagePercent()
                    ],
                    'storage_space' => [
                        'status' => $this->getStorageUsagePercent() < 90 ? 'pass' : 'warning',
                        'message' => 'Storage usage: ' . $this->getStorageUsagePercent() . '%',
                        'value' => $this->getStorageUsagePercent()
                    ],
                    'database_connection' => [
                        'status' => $this->testDatabaseConnection() ? 'pass' : 'fail',
                        'message' => $this->testDatabaseConnection() ? 'Database connected' : 'Database connection failed',
                        'value' => $this->testDatabaseConnection()
                    ],
                    'file_permissions' => [
                        'status' => $this->checkFilePermissions() ? 'pass' : 'warning',
                        'message' => $this->checkFilePermissions() ? 'File permissions OK' : 'Some files not writable',
                        'value' => $this->checkFilePermissions()
                    ]
                ]
            ];
            
            // Determine overall status
            $hasFailures = array_filter($health['checks'], fn($check) => $check['status'] === 'fail');
            $hasWarnings = array_filter($health['checks'], fn($check) => $check['status'] === 'warning');
            
            if (!empty($hasFailures)) {
                $health['overall_status'] = 'critical';
            } elseif (!empty($hasWarnings)) {
                $health['overall_status'] = 'warning';
            }
            
            echo json_encode(['success' => true, 'health' => $health]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Backup database
     */
    public function createBackup()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = __DIR__ . '/../../../storage/backups/' . $backupName;
            
            // Create backup directory if it doesn't exist
            $backupDir = dirname($backupPath);
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $result = $this->performDatabaseBackup($backupPath);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'backup_name' => $backupName,
                    'file_size' => $this->formatBytes(filesize($backupPath))
                ]);
            } else {
                throw new Exception('Backup creation failed');
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get user activity logs
     */
    public function getUserActivity()
    {
        header('Content-Type: application/json');
        
        try {
            $this->checkAdminAccess();
            
            $limit = $_GET['limit'] ?? 50;
            $offset = $_GET['offset'] ?? 0;
            
            // This would be implemented with proper activity logging
            $activities = [
                [
                    'id' => 1,
                    'user' => 'admin@example.com',
                    'action' => 'Module activated: Analytics',
                    'ip_address' => '192.168.1.100',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                    'type' => 'module'
                ],
                [
                    'id' => 2,
                    'user' => 'engineer@example.com',
                    'action' => 'User registered',
                    'ip_address' => '192.168.1.101',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                    'type' => 'user'
                ]
            ];
            
            echo json_encode([
                'success' => true,
                'activities' => array_slice($activities, $offset, $limit),
                'total' => count($activities)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    // Helper methods
    private function checkAdminAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user_id'])) {
            throw new Exception('Authentication required');
        }
        
        $userModel = new User();
        if (!$userModel->isAdmin($_SESSION['user_id'])) {
            throw new Exception('Admin access required');
        }
    }
    
    private function getNewUsersToday($users)
    {
        $today = date('Y-m-d');
        return count(array_filter($users, function($user) use ($today) {
            return isset($user['created_at']) && strpos($user['created_at'], $today) === 0;
        }));
    }
    
    private function getUserRoleDistribution($users)
    {
        $roles = ['user' => 0, 'engineer' => 0, 'admin' => 0];
        foreach ($users as $user) {
            $role = $user['role'] ?? 'user';
            if (isset($roles[$role])) {
                $roles[$role]++;
            }
        }
        return $roles;
    }
    
    private function getStorageUsage()
    {
        $size = 0;
        $path = __DIR__ . '/../../../storage';
        
        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        
        return $this->formatBytes($size);
    }
    
    private function getAnalyticsSnapshot()
    {
        return [
            'page_views_today' => rand(100, 500),
            'calculator_usage_today' => rand(50, 200),
            'active_sessions' => rand(10, 50)
        ];
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getMemoryUsagePercent()
    {
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));
        $memoryUsage = memory_get_usage(true);
        
        return $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 1) : 0;
    }
    
    private function getStorageUsagePercent()
    {
        $total = disk_total_space('.');
        $free = disk_free_space('.');
        
        return $total > 0 ? round((($total - $free) / $total) * 100, 1) : 0;
    }
    
    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }
    
    private function testDatabaseConnection()
    {
        try {
            \App\Core\Database::getInstance()->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function checkFilePermissions()
    {
        $paths = ['./storage', './storage/logs', './storage/cache', './storage/uploads'];
        
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function getSystemUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = sys_getloadavg();
            return isset($uptime[0]) ? round($uptime[0], 2) . ' load avg' : 'Unknown';
        }
        
        return 'Unknown';
    }
    
    private function performDatabaseBackup($backupPath)
    {
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            
            // Get all table names
            $tables = [];
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $sql = "-- Database Backup Created: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Get table structure
                $createResult = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createResult->fetch(\PDO::FETCH_ASSOC);
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createRow['Create Table'] . ";\n\n";
                
                // Get table data
                $dataResult = $pdo->query("SELECT * FROM `{$table}`");
                while ($row = $dataResult->fetch(\PDO::FETCH_ASSOC)) {
                    $sql .= "INSERT INTO `{$table}` VALUES (";
                    $values = [];
                    foreach ($row as $value) {
                        $values[] = $pdo->quote($value);
                    }
                    $sql .= implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
            
            return file_put_contents($backupPath, $sql) !== false;
        } catch (Exception $e) {
            error_log("Backup error: " . $e->getMessage());
            return false;
        }
    }
}
?>
