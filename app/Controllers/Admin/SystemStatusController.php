<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\SystemMonitoringService;
use App\Core\Auth;

class SystemStatusController extends Controller
{
    private $monitoringService;

    public function __construct()
    {
        parent::__construct();
<<<<<<< HEAD
            }
=======
        $this->monitoringService = new SystemMonitoringService();
    }
>>>>>>> temp-branch

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            die('Access denied');
        }

        $systemHealth = $this->monitoringService->getSystemHealth();
        
        $data = [
            'user' => $user,
            'systemHealth' => $systemHealth,
            'page_title' => 'System Status - Admin Panel',
            'currentPage' => 'system-status'
        ];

        $this->view->render('admin/system-status/index', $data);
    }

    /**
     * Get system health via API
     */
    public function getSystemHealth()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $systemHealth = $this->monitoringService->getSystemHealth();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $systemHealth,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get specific system metrics
     */
    public function getMetrics($type)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $metrics = [];
        
        switch ($type) {
            case 'server':
                $metrics = $this->monitoringService->getServerMetrics();
                break;
            case 'database':
                $metrics = $this->monitoringService->getDatabaseMetrics();
                break;
            case 'storage':
                $metrics = $this->monitoringService->getStorageMetrics();
                break;
            case 'application':
                $metrics = $this->monitoringService->getApplicationMetrics();
                break;
            case 'security':
                $metrics = $this->monitoringService->getSecurityMetrics();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid metric type']);
                return;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'type' => $type,
            'data' => $metrics,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Run a system health check
     */
    public function runHealthCheck()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $systemHealth = $this->monitoringService->getSystemHealth();
        
        $issues = $this->findIssues($systemHealth);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'health' => $systemHealth,
            'issues' => $issues,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Find any issues in the system health data
     */
    private function findIssues($systemHealth)
    {
        $issues = [];
        
        foreach ($systemHealth as $category => $metrics) {
            if (isset($metrics['status'])) {
                if ($metrics['status'] === 'critical' || $metrics['status'] === 'offline' || $metrics['status'] === 'error') {
                    $issues[] = [
                        'category' => $category,
                        'severity' => $metrics['status'],
                        'message' => $this->getStatusMessage($category, $metrics),
                        'data' => $metrics
                    ];
                }
            }
        }
        
        return $issues;
    }

    /**
     * Get status message based on category and metrics
     */
    private function getStatusMessage($category, $metrics)
    {
        switch ($category) {
            case 'server':
                if ($metrics['status'] === 'critical') {
                    if ($metrics['load_average']['1min'] > 4) {
                        return "High server load: {$metrics['load_average']['1min']}";
                    }
                    if ($metrics['memory_usage']['percent'] > 90) {
                        return "High memory usage: {$metrics['memory_usage']['percent']}%";
                    }
                }
                break;
            case 'database':
                if ($metrics['status'] === 'critical') {
                    return "Database connection issue";
                }
                break;
            case 'storage':
                if ($metrics['usage_percent'] > 90) {
                    return "Storage usage critical: {$metrics['usage_percent']}%";
                }
                break;
            case 'security':
                if ($metrics['failed_login_attempts'] > 20) {
                    return "High number of failed login attempts: {$metrics['failed_login_attempts']}";
                }
                break;
        }
        
        return "System {$category} reporting status: {$metrics['status']}";
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> temp-branch
