<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\ModelLogger;
use AdvancedErrorHandler;

class ErrorLogController extends Controller {
    
    public function index() {
        $this->checkAdminAuth();
        
        $stats = ModelLogger::getStatistics();
        $recentErrors = ModelLogger::getRecentCalls(20);
        $failedCalls = ModelLogger::getFailedCalls();
        
        $data = [
            'stats' => $stats,
            'recent_errors' => $recentErrors,
            'failed_calls' => $failedCalls,
            'error_categories' => $this->getErrorCategories(),
            'performance_data' => $this->getPerformanceData()
        ];
        
        $this->view('admin/error-logs/index', $data);
    }
    
    public function getErrorStats() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        $stats = ModelLogger::getStatistics();
        $recentErrors = ModelLogger::getRecentCalls(50);
        $failedCalls = ModelLogger::getFailedCalls();
        
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'recent_errors' => $recentErrors,
            'failed_calls' => $failedCalls,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    public function getMethodCalls() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        $class = $_GET['class'] ?? null;
        $method = $_GET['method'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        
        $calls = [];
        
        if ($class) {
            $calls = ModelLogger::getCallsByClass($class);
        } elseif ($method) {
            $calls = ModelLogger::getCallsByMethod($method);
        } else {
            $calls = ModelLogger::getRecentCalls($limit);
        }
        
        echo json_encode([
            'success' => true,
            'calls' => array_slice($calls, -$limit),
            'total' => count($calls)
        ]);
        exit;
    }
    
    public function getFailedCalls() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        $failedCalls = ModelLogger::getFailedCalls();
        
        echo json_encode([
            'success' => true,
            'failed_calls' => $failedCalls,
            'total_failed' => count($failedCalls)
        ]);
        exit;
    }
    
    public function clearLogs() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Clear error logs
            $this->clearLogFiles();
            
            // Reset statistics
            $this->resetStatistics();
            
            $this->session->setFlash('success', 'Error logs cleared successfully.');
            $this->redirect('/admin/error-logs');
        }
        
        $this->view('admin/error-logs/confirm-clear');
    }
    
    public function exportLogs() {
        $this->checkAdminAuth();
        
        $format = $_GET['format'] ?? 'json';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        $calls = ModelLogger::getRecentCalls(1000); // Get last 1000 calls
        
        // Filter by date range if provided
        if ($startDate || $endDate) {
            $calls = array_filter($calls, function($call) use ($startDate, $endDate) {
                $timestamp = strtotime($call['timestamp']);
                
                if ($startDate && $timestamp < strtotime($startDate)) {
                    return false;
                }
                
                if ($endDate && $timestamp > strtotime($endDate . ' 23:59:59')) {
                    return false;
                }
                
                return true;
            });
        }
        
        if ($format === 'csv') {
            $this->exportToCsv($calls);
        } else {
            $this->exportToJson($calls);
        }
    }
    
    private function getErrorCategories() {
        $failedCalls = ModelLogger::getFailedCalls();
        $categories = [];
        
        foreach ($failedCalls as $call) {
            $category = $this->categorizeError($call['error']);
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }
        
        return $categories;
    }
    
    private function getPerformanceData() {
        $stats = ModelLogger::getStatistics();
        $recentCalls = ModelLogger::getRecentCalls(100);
        
        $performance = [
            'slowest_calls' => [],
            'average_response_time' => 0,
            'memory_usage' => [],
            'error_rate_trend' => []
        ];
        
        // Find slowest calls
        $slowCalls = array_filter($recentCalls, function($call) {
            return $call['execution_time'] > 0.1; // Slower than 100ms
        });
        
        usort($slowCalls, function($a, $b) {
            return $b['execution_time'] - $a['execution_time'];
        });
        
        $performance['slowest_calls'] = array_slice($slowCalls, 0, 10);
        
        // Calculate average response time
        $totalTime = array_sum(array_column($recentCalls, 'execution_time'));
        $performance['average_response_time'] = $totalTime / count($recentCalls);
        
        // Memory usage trend
        foreach (array_slice($recentCalls, -20) as $call) {
            $performance['memory_usage'][] = [
                'timestamp' => $call['timestamp'],
                'memory' => $call['memory_usage'],
                'peak_memory' => $call['peak_memory']
            ];
        }
        
        // Error rate trend (last 10 requests)
        $errorTrend = [];
        for ($i = 9; $i >= 0; $i--) {
            $start = max(0, count($recentCalls) - (10 - $i) * 10);
            $end = min(count($recentCalls), count($recentCalls) - (9 - $i) * 10);
            
            $batch = array_slice($recentCalls, $start, $end - $start);
            $errors = count(array_filter($batch, function($call) {
                return !$call['success'];
            }));
            
            $errorTrend[] = [
                'period' => "Batch " . (10 - $i),
                'errors' => $errors,
                'total' => count($batch),
                'error_rate' => count($batch) > 0 ? ($errors / count($batch)) * 100 : 0
            ];
        }
        
        $performance['error_rate_trend'] = $errorTrend;
        
        return $performance;
    }
    
    private function categorizeError($error) {
        if (!$error) return 'Unknown';
        
        $error = strtolower($error);
        
        if (strpos($error, 'undefined method') !== false) {
            return 'Missing Method';
        }
        
        if (strpos($error, 'syntax error') !== false) {
            return 'Syntax Error';
        }
        
        if (strpos($error, 'undefined') !== false) {
            return 'Undefined Variable';
        }
        
        if (strpos($error, 'call to undefined') !== false) {
            return 'Undefined Call';
        }
        
        if (strpos($error, 'not found') !== false) {
            return 'Not Found';
        }
        
        return 'Other';
    }
    
    private function clearLogFiles() {
        $logDirs = [
            __DIR__ . '/../../debug/logs',
            __DIR__ . '/../../debug/logs/model_calls.log',
            __DIR__ . '/../../debug/logs/enhanced_errors.log'
        ];
        
        foreach ($logDirs as $path) {
            if (is_file($path)) {
                @unlink($path);
            } elseif (is_dir($path)) {
                $files = glob($path . '/*.log');
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
        }
    }
    
    private function resetStatistics() {
        // Reset ModelLogger statistics (this would need to be implemented in ModelLogger)
        // For now, just clear the log files
    }
    
    private function exportToJson($calls) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="error_logs_' . date('Y-m-d_H-i-s') . '.json"');
        
        echo json_encode([
            'export_date' => date('Y-m-d H:i:s'),
            'total_records' => count($calls),
            'calls' => $calls
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    private function exportToCsv($calls) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="error_logs_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Timestamp',
            'Class',
            'Method', 
            'Success',
            'Execution Time',
            'Error',
            'Memory Usage',
            'Peak Memory'
        ]);
        
        // CSV data
        foreach ($calls as $call) {
            fputcsv($output, [
                $call['timestamp'],
                $call['class'],
                $call['method'],
                $call['success'] ? 'Yes' : 'No',
                $call['execution_time'],
                $call['error'] ?? '',
                $call['memory_usage'],
                $call['peak_memory']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function checkAdminAuth() {
        // Check if user is admin (implement based on your auth system)
        if (!$this->isAdmin()) {
            $this->redirect('/admin/login');
        }
    }
    
    private function isAdmin() {
        // Check if current user is admin
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
?>
