<?php
namespace App\Services;

/**
 * Performance monitoring service
 * Tracks application performance metrics and identifies bottlenecks
 */
class PerformanceMonitor {
    private array $metrics = [];
    private array $timers = [];
    private array $memoryUsage = [];
    private bool $enabled = true;
    
    public function __construct() {
        $this->enabled = (getenv('APP_ENV') !== 'production') || getenv('PERFORMANCE_MONITORING');
    }
    
    /**
     * Start timing a specific operation
     */
    public function startTimer(string $operation): void {
        if (!$this->enabled) return;
        
        $this->timers[$operation] = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
            'start_memory_real' => function_exists('memory_get_real_usage') ? memory_get_real_usage(true) : memory_get_usage(true)
        ];
    }
    
    /**
     * Stop timing and record metrics
     */
    public function stopTimer(string $operation): array {
        if (!$this->enabled || !isset($this->timers[$operation])) {
            return [];
        }
        
        $timer = $this->timers[$operation];
        $end_time = microtime(true);
        $end_memory = memory_get_usage(true);
        $end_memory_real = function_exists('memory_get_real_usage') ? memory_get_real_usage(true) : memory_get_usage(true);
        
        $duration = $end_time - $timer['start_time'];
        $memory_delta = $end_memory - $timer['start_memory'];
        $memory_real_delta = $end_memory_real - $timer['start_memory_real'];
        
        $metric = [
            'operation' => $operation,
            'duration' => $duration,
            'memory_delta' => $memory_delta,
            'memory_real_delta' => $memory_real_delta,
            'timestamp' => microtime(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        $this->metrics[$operation][] = $metric;
        
        // Keep only last 100 entries per operation
        if (count($this->metrics[$operation]) > 100) {
            array_shift($this->metrics[$operation]);
        }
        
        unset($this->timers[$operation]);
        
        return $metric;
    }
    
    /**
     * Record a performance metric
     */
    public function recordMetric(string $type, string $name, mixed $value, array $context = []): void {
        if (!$this->enabled) return;
        
        $metric = [
            'type' => $type,
            'name' => $name,
            'value' => $value,
            'context' => $context,
            'timestamp' => microtime(true),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        $this->metrics[$type . '_' . $name][] = $metric;
        
        // Keep only last 50 entries per metric
        if (count($this->metrics[$type . '_' . $name]) > 50) {
            array_shift($this->metrics[$type . '_' . $name]);
        }
    }
    
    /**
     * Get performance statistics for an operation
     */
    public function getOperationStats(string $operation): array {
        if (!isset($this->metrics[$operation]) || empty($this->metrics[$operation])) {
            return ['error' => 'No metrics found for operation'];
        }
        
        $durations = array_column($this->metrics[$operation], 'duration');
        $memory_deltas = array_column($this->metrics[$operation], 'memory_delta');
        
        return [
            'operation' => $operation,
            'total_calls' => count($durations),
            'total_time' => array_sum($durations),
            'average_time' => array_sum($durations) / count($durations),
            'min_time' => min($durations),
            'max_time' => max($durations),
            'average_memory_delta' => array_sum($memory_deltas) / count($memory_deltas),
            'min_memory_delta' => min($memory_deltas),
            'max_memory_delta' => max($memory_deltas),
            'recent_calls' => array_slice($this->metrics[$operation], -10, 10)
        ];
    }
    
    /**
     * Get all performance statistics
     */
    public function getAllStats(): array {
        $stats = [];
        
        foreach ($this->metrics as $operation => $metrics) {
            if (str_starts_with($operation, 'timer_')) {
                $stats[$operation] = $this->getOperationStats($operation);
            }
        }
        
        return [
            'enabled' => $this->enabled,
            'total_operations' => count($stats),
            'operations' => $stats,
            'system_info' => $this->getSystemInfo()
        ];
    }
    
    /**
     * Get system performance information
     */
    public function getSystemInfo(): array {
        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'real_usage' => function_exists('memory_get_real_usage') ? memory_get_real_usage(true) : memory_get_usage(true)
            ],
            'execution_time' => [
                'current' => getrusage()['ru_utime.tv_sec'] + getrusage()['ru_utime.tv_usec'] / 1000000,
                'max_execution_time' => ini_get('max_execution_time')
            ],
            'opcache' => $this->getOpcacheInfo()
        ];
    }
    
    /**
     * Get OPcache information
     */
    private function getOpcacheInfo(): array {
        if (!function_exists('opcache_get_status')) {
            return ['available' => false];
        }
        
        $status = opcache_get_status(false);
        
        return [
            'available' => true,
            'enabled' => $status['opcache_enabled'] ?? false,
            'memory_usage' => $status['memory_usage'] ?? [],
            'interned_strings_usage' => $status['interned_strings_usage'] ?? [],
            'opcache_statistics' => $status['opcache_statistics'] ?? []
        ];
    }
    
    /**
     * Identify performance bottlenecks
     */
    public function identifyBottlenecks(): array {
        $bottlenecks = [];
        
        foreach ($this->metrics as $operation => $metrics) {
            if (!str_starts_with($operation, 'timer_')) continue;
            
            if (empty($metrics)) continue;
            
            $durations = array_column($metrics, 'duration');
            $avg_time = array_sum($durations) / count($durations);
            $max_time = max($durations);
            
            // Flag operations that take longer than 100ms on average
            if ($avg_time > 0.1) {
                $bottlenecks[] = [
                    'operation' => $operation,
                    'type' => 'slow_average',
                    'severity' => $avg_time > 1.0 ? 'high' : ($avg_time > 0.5 ? 'medium' : 'low'),
                    'average_time' => $avg_time,
                    'max_time' => $max_time,
                    'recommendation' => 'Consider optimizing this operation or implementing caching'
                ];
            }
            
            // Flag operations with high memory usage
            $memory_deltas = array_column($metrics, 'memory_delta');
            $avg_memory = array_sum($memory_deltas) / count($memory_deltas);
            
            if ($avg_memory > 1024 * 1024) { // More than 1MB
                $bottlenecks[] = [
                    'operation' => $operation,
                    'type' => 'high_memory',
                    'severity' => $avg_memory > 10 * 1024 * 1024 ? 'high' : 'medium',
                    'average_memory_mb' => round($avg_memory / (1024 * 1024), 2),
                    'recommendation' => 'Consider reducing memory usage or implementing memory-efficient algorithms'
                ];
            }
        }
        
        // Sort by severity
        usort($bottlenecks, function($a, $b) {
            $severity_order = ['high' => 3, 'medium' => 2, 'low' => 1];
            return ($severity_order[$b['severity']] ?? 0) - ($severity_order[$a['severity']] ?? 0);
        });
        
        return $bottlenecks;
    }
    
    /**
     * Clear all metrics
     */
    public function clearMetrics(): void {
        $this->metrics = [];
        $this->timers = [];
    }
    
    /**
     * Export metrics to JSON
     */
    public function exportMetrics(): string {
        return json_encode([
            'metrics' => $this->metrics,
            'timers' => $this->timers,
            'export_time' => microtime(true),
            'system_info' => $this->getSystemInfo()
        ], JSON_PRETTY_PRINT);
    }
    
    /**
     * Import metrics from JSON
     */
    public function importMetrics(string $json): bool {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        
        if (isset($data['metrics'])) {
            $this->metrics = $data['metrics'];
        }
        
        if (isset($data['timers'])) {
            $this->timers = $data['timers'];
        }
        
        return true;
    }
}
