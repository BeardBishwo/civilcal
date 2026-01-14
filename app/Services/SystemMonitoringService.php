<?php

namespace App\Services;

class SystemMonitoringService
{
    /**
     * Get system health metrics
     */
    public function getSystemHealth()
    {
        return [
            'server' => $this->getServerMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'storage' => $this->getStorageMetrics(),
            'application' => $this->getApplicationMetrics(),
            'security' => $this->getSecurityMetrics(),
        ];
    }

    /**
     * Get server metrics
     */
    public function getServerMetrics()
    {
        $metrics = [
            'status' => 'online',
            'load_average' => $this->getLoadAverage(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage(),
            'uptime' => $this->getUptime(),
            'disk_io' => $this->getDiskIo(),
            'network_io' => $this->getNetworkIo(),
        ];

        // Determine overall status based on metrics
        $metrics['status'] = $this->determineStatus($metrics, 'server');

        return $metrics;
    }

    /**
     * Get database metrics
     */
    public function getDatabaseMetrics()
    {
        try {
            // Test database connection and get metrics
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new \PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Get connection stats
            $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
            $connections = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt = $pdo->query("SHOW STATUS LIKE 'Questions'");
            $queries = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt = $pdo->query("SHOW STATUS LIKE 'Slow_queries'");
            $slowQueries = $stmt->fetch(\PDO::FETCH_ASSOC);

            $metrics = [
                'status' => 'online',
                'connections' => (int)($connections['Value'] ?? 0),
                'total_queries' => (int)($queries['Value'] ?? 0),
                'slow_queries' => (int)($slowQueries['Value'] ?? 0),
                'response_time' => $this->getDatabaseResponseTime(),
                'table_status' => $this->getTableStatus(),
            ];

            $metrics['status'] = $this->determineStatus($metrics, 'database');

            return $metrics;
        } catch (\Exception $e) {
            error_log('SystemMonitoringService::getDatabaseMetrics error: ' . $e->getMessage());
            return [
                'status' => 'offline',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get storage metrics
     */
    public function getStorageMetrics()
    {
        $total = disk_total_space(BASE_PATH);
        $free = disk_free_space(BASE_PATH);
        $used = $total - $free;
        $usagePercent = $total > 0 ? round(($used / $total) * 100, 2) : 0;

        $metrics = [
            'status' => 'online',
            'total_space' => $this->formatBytes($total),
            'used_space' => $this->formatBytes($used),
            'free_space' => $this->formatBytes($free),
            'usage_percent' => $usagePercent,
            'disk_partitions' => $this->getDiskPartitions(),
        ];

        $metrics['status'] = $this->determineStatus($metrics, 'storage');

        return $metrics;
    }

    /**
     * Get application metrics
     */
    public function getApplicationMetrics()
    {
        try {
            // Get application-specific metrics
            $metrics = [
                'status' => 'online',
                'php_version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'errors_today' => $this->getErrorsToday(),
                'requests_per_minute' => $this->getRequestsPerMinute(),
                'active_sessions' => $this->getActiveSessions(),
            ];

            $metrics['status'] = $this->determineStatus($metrics, 'application');

            return $metrics;
        } catch (\Exception $e) {
            error_log('SystemMonitoringService::getApplicationMetrics error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get security metrics
     */
    public function getSecurityMetrics()
    {
        $metrics = [
            'status' => 'online',
            'failed_login_attempts' => $this->getFailedLoginAttempts(),
            'security_events' => $this->getSecurityEvents(),
            'ssl_certificate_status' => $this->getSslCertificateStatus(),
            'last_security_scan' => $this->getLastSecurityScan(),
        ];

        $metrics['status'] = $this->determineStatus($metrics, 'security');

        return $metrics;
    }

    /**
     * Get load average
     */
    private function getLoadAverage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0] ?? 0,
                '5min' => $load[1] ?? 0,
                '15min' => $load[2] ?? 0,
            ];
        }
        return ['1min' => 0, '5min' => 0, '15min' => 0];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        $memoryTotal = $this->getSystemMemoryTotal();
        $memoryFree = $this->getSystemMemoryFree();
        $memoryUsed = $memoryTotal - $memoryFree;
        $percent = $memoryTotal > 0 ? round(($memoryUsed / $memoryTotal) * 100, 2) : 0;

        return [
            'total' => $this->formatBytes($memoryTotal),
            'used' => $this->formatBytes($memoryUsed),
            'free' => $this->formatBytes($memoryFree),
            'percent' => $percent,
        ];
    }

    /**
     * Get CPU usage
     */
    private function getCpuUsage()
    {
        // For Windows, sys_getloadavg is not available
        if (PHP_OS_FAMILY === 'Windows') {
            return [
                'usage_percent' => 'N/A (Windows)',
                'count' => $this->getCpuCount(),
                'model' => $this->getCpuModel()
            ];
        }

        // On Unix-like systems, use sys_getloadavg for non-blocking load check
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cpuUsage = $load[0] ?? 'N/A';
        } else {
            $cpuUsage = 'N/A';
        }

        return [
            'usage_percent' => $cpuUsage,
            'count' => $this->getCpuCount(),
            'model' => $this->getCpuModel(),
        ];
    }

    /**
     * Get system memory total
     */
    private function getSystemMemoryTotal()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $fh = fopen('/proc/meminfo', 'r');
            if ($fh) {
                while ($line = fgets($fh)) {
                    $pieces = array();
                    if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                        fclose($fh);
                        return $pieces[1] * 1024; // Convert to bytes
                    }
                }
                fclose($fh);
            }
        }
        // For Windows or if we can't get the exact value, return a reasonable default
        return 2 * 1024 * 1024 * 1024; // 2GB as fallback
    }

    /**
     * Get system memory free
     */
    private function getSystemMemoryFree()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $fh = fopen('/proc/meminfo', 'r');
            if ($fh) {
                while ($line = fgets($fh)) {
                    $pieces = array();
                    if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
                        fclose($fh);
                        return $pieces[1] * 1024; // Convert to bytes
                    }
                }
                fclose($fh);
            }
        }
        // For Windows or if we can't get the exact value, return a reasonable default
        return 512 * 1024 * 1024; // 512MB as fallback
    }

    /**
     * Get CPU time (helper for CPU usage)
     */
    private function getCpuTime()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $fh = fopen('/proc/stat', 'r');
            if ($fh) {
                $line = fgets($fh);
                fclose($fh);
                $data = explode(' ', $line);
                $total = 0;
                for ($i = 1; $i < 5; $i++) {
                    $total += (int)$data[$i];
                }
                return $total;
            }
        }
        return false;
    }

    /**
     * Get CPU count
     */
    private function getCpuCount()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            return count($matches[0]) ?: 1;
        }
        return 1; // Default for Windows
    }

    /**
     * Get CPU model
     */
    private function getCpuModel()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match('/^model name\s+:\s+(.+)$/m', $cpuinfo, $matches);
            return $matches[1] ?? 'Unknown';
        }
        return 'Unknown';
    }

    /**
     * Get uptime
     */
    private function getUptime()
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            if (file_exists('/proc/uptime')) {
                $uptime = file_get_contents('/proc/uptime');
                $uptime = explode(' ', $uptime);
                $uptime = trim($uptime[0]);
                return $this->formatUptime((int)$uptime);
            }
        }
        return 'N/A'; // For Windows
    }

    /**
     * Format uptime (seconds to human-readable)
     */
    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) $parts[] = $days . " days";
        if ($hours > 0) $parts[] = $hours . " hours";
        if ($minutes > 0) $parts[] = $minutes . " minutes";

        return implode(", ", $parts);
    }

    /**
     * Get disk I/O
     */
    private function getDiskIo()
    {
        // Simplified implementation - in a real system you'd monitor actual I/O operations
        return [
            'reads' => 'N/A',
            'writes' => 'N/A',
            'read_bytes' => 'N/A',
            'write_bytes' => 'N/A',
        ];
    }

    /**
     * Get network I/O
     */
    private function getNetworkIo()
    {
        // Simplified implementation - in a real system you'd monitor actual network activity
        return [
            'rx_bytes' => 'N/A',
            'tx_bytes' => 'N/A',
        ];
    }

    /**
     * Get database response time
     */
    private function getDatabaseResponseTime()
    {
        try {
            $start = microtime(true);
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new \PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query('SELECT 1');
            $result = $stmt->fetch();
            $end = microtime(true);

            return round(($end - $start) * 1000, 2) . ' ms';
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    /**
     * Get table status
     */
    private function getTableStatus()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new \PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SHOW TABLE STATUS");
            $tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $totalRows = 0;
            $totalSize = 0;
            $tableCount = count($tables);

            foreach ($tables as $table) {
                $totalRows += (int)$table['Rows'];
                $totalSize += (int)$table['Data_length'] + (int)$table['Index_length'];
            }

            return [
                'count' => $tableCount,
                'total_rows' => $totalRows,
                'total_size' => $this->formatBytes($totalSize),
            ];
        } catch (\Exception $e) {
            return [
                'count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get disk partitions
     */
    private function getDiskPartitions()
    {
        $partitions = [];
        if (PHP_OS_FAMILY !== 'Windows') {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $df = ''; // Not supported on Windows
            } else {
                // SECURITY: Internal command only. Do not add user input here.
                $df = shell_exec('df -h');
            }
            $lines = explode("\n", $df);
            foreach ($lines as $line) {
                if (preg_match('/^(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)$/', $line, $matches)) {
                    $partitions[] = [
                        'filesystem' => $matches[1],
                        'size' => $matches[2],
                        'used' => $matches[3],
                        'available' => $matches[4],
                        'use_percent' => $matches[5],
                        'mounted_on' => $matches[6],
                    ];
                }
            }
        } else {
            // For Windows, we'll just return the main drive info
            $partitions[] = [
                'filesystem' => 'C:',
                'size' => $this->formatBytes(disk_total_space('C:')),
                'used' => $this->formatBytes(disk_total_space('C:') - disk_free_space('C:')),
                'available' => $this->formatBytes(disk_free_space('C:')),
                'use_percent' => round((disk_free_space('C:') / disk_total_space('C:')) * 100) . '%',
                'mounted_on' => 'C:',
            ];
        }

        return $partitions;
    }

    /**
     * Get errors today
     */
    private function getErrorsToday()
    {
        // This would check your error logs for today's errors
        // For now, we'll return a mock value
        return rand(0, 5);
    }

    /**
     * Get requests per minute
     */
    private function getRequestsPerMinute()
    {
        // In a real implementation, you would track this in a cache or database
        return rand(10, 100);
    }

    /**
     * Get active sessions
     */
    private function getActiveSessions()
    {
        // In a real implementation, you would check active sessions
        // For now, we'll return a mock value
        return rand(1, 20);
    }

    /**
     * Get failed login attempts
     */
    private function getFailedLoginAttempts()
    {
        // This would check your security logs for failed login attempts
        // For now, we'll return a mock value
        return rand(0, 10);
    }

    /**
     * Get security events
     */
    private function getSecurityEvents()
    {
        // This would check your security logs
        // For now, we'll return a mock value
        return rand(0, 3);
    }

    /**
     * Get SSL certificate status
     */
    private function getSslCertificateStatus()
    {
        // This would check the SSL certificate for the website
        // For now, we'll return a mock value
        return [
            'valid' => true,
            'expires_in' => '30 days',
            'issuer' => 'Self-Signed',
        ];
    }

    /**
     * Get last security scan
     */
    private function getLastSecurityScan()
    {
        return [
            'date' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'status' => 'completed',
            'findings' => 0,
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Determine status based on metrics
     */
    private function determineStatus($metrics, $type)
    {
        switch ($type) {
            case 'storage':
                if ($metrics['usage_percent'] > 90) return 'critical';
                if ($metrics['usage_percent'] > 80) return 'warning';
                return 'online';

            case 'server':
                // For load average, if any value is > 4, it's critical
                if ($metrics['load_average']['1min'] > 4) return 'critical';
                if ($metrics['load_average']['1min'] > 2) return 'warning';
                if ($metrics['memory_usage']['percent'] > 90) return 'critical';
                if ($metrics['memory_usage']['percent'] > 80) return 'warning';
                return 'online';

            case 'database':
                if (isset($metrics['error'])) return 'offline';
                if ($metrics['connections'] > 100) return 'warning';
                if ($metrics['slow_queries'] > 10) return 'warning';
                return 'online';

            case 'application':
                if (isset($metrics['error'])) return 'error';
                if ($metrics['errors_today'] > 10) return 'warning';
                return 'online';

            case 'security':
                if ($metrics['failed_login_attempts'] > 20) return 'critical';
                if ($metrics['failed_login_attempts'] > 10) return 'warning';
                return 'online';

            default:
                return 'online';
        }
    }
}
