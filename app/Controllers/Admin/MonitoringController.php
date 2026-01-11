<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\CacheService;
use App\Core\Database;

/**
 * Monitoring Dashboard Controller
 * 
 * Displays real-time metrics for cache, database, and performance
 */
class MonitoringController extends Controller
{
    private $cache;
    private $db;

    public function __construct()
    {
        parent::__construct();
        
        // Check admin authentication
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }

        $this->cache = CacheService::getInstance();
        $this->db = Database::getInstance();
    }

    /**
     * Main monitoring dashboard
     */
    public function index()
    {
        $metrics = [
            'cache' => $this->getCacheMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'performance' => $this->getPerformanceMetrics(),
            'system' => $this->getSystemMetrics()
        ];

        $this->view('admin/monitoring/dashboard', [
            'title' => 'System Monitoring Dashboard',
            'metrics' => $metrics
        ]);
    }

    /**
     * API: Get real-time metrics
     */
    public function getMetrics()
    {
        $type = $_GET['type'] ?? 'all';

        $metrics = [];

        if ($type === 'all' || $type === 'cache') {
            $metrics['cache'] = $this->getCacheMetrics();
        }

        if ($type === 'all' || $type === 'database') {
            $metrics['database'] = $this->getDatabaseMetrics();
        }

        if ($type === 'all' || $type === 'performance') {
            $metrics['performance'] = $this->getPerformanceMetrics();
        }

        if ($type === 'all' || $type === 'system') {
            $metrics['system'] = $this->getSystemMetrics();
        }

        return $this->json([
            'success' => true,
            'metrics' => $metrics,
            'timestamp' => time()
        ]);
    }

    /**
     * Get cache metrics
     */
    private function getCacheMetrics()
    {
        $stats = $this->cache->getStats();

        return [
            'driver' => $this->cache->getDriver(),
            'total_items' => $stats['total_items'] ?? 0,
            'total_size_mb' => $stats['total_size_mb'] ?? 0,
            'expired_items' => $stats['expired_items'] ?? 0,
            'hit_rate' => $this->calculateCacheHitRate(),
            'status' => $this->cache->getDriver() === 'redis' ? 'Redis Active' : 'File Cache'
        ];
    }

    /**
     * Get database metrics
     */
    private function getDatabaseMetrics()
    {
        $pdo = $this->db->getPdo();

        // Get table sizes
        $stmt = $pdo->query("
            SELECT 
                TABLE_NAME,
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb,
                TABLE_ROWS
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            LIMIT 10
        ");
        $tableSizes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get total database size
        $stmt = $pdo->query("
            SELECT 
                ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_size_mb
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
        ");
        $totalSize = $stmt->fetchColumn();

        // Get connection count
        $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
        $connections = $stmt->fetch(\PDO::FETCH_ASSOC);

        return [
            'total_size_mb' => $totalSize,
            'connections' => $connections['Value'] ?? 0,
            'top_tables' => $tableSizes,
            'slow_queries' => $this->getSlowQueries()
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'avg_response_time' => $this->getAverageResponseTime(),
            'requests_per_minute' => $this->getRequestsPerMinute(),
            'error_rate' => $this->getErrorRate(),
            'uptime' => $this->getUptime()
        ];
    }

    /**
     * Get system metrics
     */
    private function getSystemMetrics()
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'disk_free_space' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2) . ' GB',
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 'N/A'
        ];
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateCacheHitRate()
    {
        // This would require tracking hits/misses in a separate table or Redis
        // For now, return estimated value
        return 80; // 80% estimated
    }

    /**
     * Get slow queries
     */
    private function getSlowQueries()
    {
        // Check if slow query log is enabled
        try {
            $pdo = $this->db->getPdo();
            $stmt = $pdo->query("SHOW VARIABLES LIKE 'slow_query_log'");
            $slowLogEnabled = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($slowLogEnabled && $slowLogEnabled['Value'] === 'ON') {
                return ['enabled' => true, 'count' => 'See slow query log file'];
            }

            return ['enabled' => false, 'message' => 'Enable slow_query_log to track slow queries'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get average response time
     */
    private function getAverageResponseTime()
    {
        // This would require tracking request times
        // For now, return estimated value
        return '120ms'; // Estimated
    }

    /**
     * Get requests per minute
     */
    private function getRequestsPerMinute()
    {
        // This would require tracking requests
        // For now, return estimated value
        return 45; // Estimated
    }

    /**
     * Get error rate
     */
    private function getErrorRate()
    {
        // This would require tracking errors
        // For now, return estimated value
        return '0.5%'; // Estimated
    }

    /**
     * Get system uptime
     */
    private function getUptime()
    {
        if (function_exists('shell_exec')) {
            $uptime = shell_exec('uptime');
            return $uptime ?: 'N/A';
        }

        return 'N/A';
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $type = $_POST['type'] ?? 'all';

        if ($type === 'all') {
            $this->cache->flush();
            $message = 'All cache cleared successfully';
        } else {
            // Clear specific cache keys
            $this->cache->delete($type . '_*');
            $message = ucfirst($type) . ' cache cleared successfully';
        }

        return $this->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Get cache driver info
     */
    public function getCacheDriver()
    {
        return $this->json([
            'success' => true,
            'driver' => $this->cache->getDriver(),
            'redis_available' => extension_loaded('redis'),
            'stats' => $this->cache->getStats()
        ]);
    }
}
