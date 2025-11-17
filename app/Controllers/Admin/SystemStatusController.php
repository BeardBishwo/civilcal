<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class SystemStatusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
            }

    public function index()
    {
        $data = [
            'page_title' => 'System Status',
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'disk_space' => $this->getDiskSpace(),
            'memory_usage' => $this->getMemoryUsage(),
            'system_health' => $this->getSystemHealth()
        ];

        $this->view('admin/system/status', $data);
    }

    private function getDatabaseVersion()
    {
        try {
            $stmt = $this->db->query("SELECT VERSION()");
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getDiskSpace()
    {
        $total = disk_total_space(BASE_PATH);
        $free = disk_free_space(BASE_PATH);
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'percent_used' => $total > 0 ? round(($used / $total) * 100, 2) : 0
        ];
    }

    private function getMemoryUsage()
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
    }

    private function getSystemHealth()
    {
        $checks = [];
        
        // Check database connection
        $checks['database'] = $this->checkDatabase();
        
        // Check file permissions
        $checks['permissions'] = $this->checkPermissions();
        
        // Check PHP extensions
        $checks['extensions'] = $this->checkExtensions();
        
        return $checks;
    }

    private function checkDatabase()
    {
        try {
            $this->db->query("SELECT 1");
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkPermissions()
    {
        $paths = [
            BASE_PATH . '/storage',
            BASE_PATH . '/storage/logs',
            BASE_PATH . '/storage/cache',
            BASE_PATH . '/public/uploads'
        ];
        
        $issues = [];
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $issues[] = basename($path) . ' is not writable';
            }
        }
        
        return [
            'status' => empty($issues) ? 'ok' : 'warning',
            'message' => empty($issues) ? 'All directories writable' : implode(', ', $issues)
        ];
    }

    private function checkExtensions()
    {
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'curl'];
        $missing = [];
        
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        
        return [
            'status' => empty($missing) ? 'ok' : 'error',
            'message' => empty($missing) ? 'All required extensions loaded' : 'Missing: ' . implode(', ', $missing)
        ];
    }
}
