<?php
/**
 * System Health Check API Endpoint
 * Provides system health status for monitoring
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/../app/bootstrap.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $health = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'uptime' => 'Unknown',
            'version' => '1.0.0',
            'checks' => []
        ];
        
        // Database check
        try {
            $db = \App\Core\Database::getInstance();
            $stmt = $db->query("SELECT 1");
            $health['checks']['database'] = [
                'status' => 'pass',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $health['checks']['database'] = [
                'status' => 'fail',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
            $health['status'] = 'unhealthy';
        }
        
        // File system check
        $storagePath = __DIR__ . '/../storage';
        if (is_dir($storagePath) && is_writable($storagePath)) {
            $health['checks']['filesystem'] = [
                'status' => 'pass',
                'message' => 'File system is writable'
            ];
        } else {
            $health['checks']['filesystem'] = [
                'status' => 'fail',
                'message' => 'Storage directory is not writable'
            ];
            $health['status'] = 'degraded';
        }
        
        // Memory check
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = return_bytes($memoryLimit);
        $memoryUsagePercent = ($memoryUsage / $memoryLimitBytes) * 100;
        
        if ($memoryUsagePercent < 80) {
            $health['checks']['memory'] = [
                'status' => 'pass',
                'message' => 'Memory usage is normal',
                'usage_percent' => round($memoryUsagePercent, 2),
                'usage_bytes' => $memoryUsage,
                'limit_bytes' => $memoryLimitBytes
            ];
        } elseif ($memoryUsagePercent < 90) {
            $health['checks']['memory'] = [
                'status' => 'warn',
                'message' => 'Memory usage is high',
                'usage_percent' => round($memoryUsagePercent, 2),
                'usage_bytes' => $memoryUsage,
                'limit_bytes' => $memoryLimitBytes
            ];
            if ($health['status'] === 'healthy') {
                $health['status'] = 'degraded';
            }
        } else {
            $health['checks']['memory'] = [
                'status' => 'fail',
                'message' => 'Memory usage is critical',
                'usage_percent' => round($memoryUsagePercent, 2),
                'usage_bytes' => $memoryUsage,
                'limit_bytes' => $memoryLimitBytes
            ];
            $health['status'] = 'unhealthy';
        }
        
        // PHP version check
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '7.4.0', '>=')) {
            $health['checks']['php_version'] = [
                'status' => 'pass',
                'message' => 'PHP version is supported',
                'version' => $phpVersion
            ];
        } else {
            $health['checks']['php_version'] = [
                'status' => 'warn',
                'message' => 'PHP version is outdated',
                'version' => $phpVersion
            ];
            if ($health['status'] === 'healthy') {
                $health['status'] = 'degraded';
            }
        }
        
        // Extensions check
        $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
        $missingExtensions = [];
        
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }
        
        if (empty($missingExtensions)) {
            $health['checks']['extensions'] = [
                'status' => 'pass',
                'message' => 'All required extensions are loaded',
                'required' => $requiredExtensions
            ];
        } else {
            $health['checks']['extensions'] = [
                'status' => 'fail',
                'message' => 'Missing required extensions',
                'required' => $requiredExtensions,
                'missing' => $missingExtensions
            ];
            $health['status'] = 'unhealthy';
        }
        
        // Session check
        if (session_status() === PHP_SESSION_ACTIVE) {
            $health['checks']['session'] = [
                'status' => 'pass',
                'message' => 'Session is active'
            ];
        } else {
            $health['checks']['session'] = [
                'status' => 'warn',
                'message' => 'Session is not active'
            ];
            if ($health['status'] === 'healthy') {
                $health['status'] = 'degraded';
            }
        }
        
        // Set HTTP status code based on health
        if ($health['status'] === 'healthy') {
            http_response_code(200);
        } elseif ($health['status'] === 'degraded') {
            http_response_code(200); // Still functional but with warnings
        } else {
            http_response_code(503); // Service unavailable
        }
        
        echo json_encode([
            'success' => true,
            'health' => $health
        ]);
        
    } else {
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use GET.'
        ]);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}

// Helper function to convert memory limit string to bytes
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    
    return $val;
}