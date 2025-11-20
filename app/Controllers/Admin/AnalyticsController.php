<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    /**
     * Analytics overview page
     */
    public function overview()
    {
        $data = [
            'page_title' => 'Analytics Overview',
            'stats' => $this->getOverviewStats(),
            'charts' => $this->getChartData()
        ];

        $this->view->render('admin/analytics/overview', $data);
    }

    /**
     * User analytics page
     */
    public function users()
    {
        $data = [
            'page_title' => 'User Analytics',
            'user_stats' => $this->getUserStats(),
            'growth_data' => $this->getUserGrowthData()
        ];

        $this->view->render('admin/analytics/users', $data);
    }

    /**
     * Calculator analytics page
     */
    public function calculators()
    {
        $data = [
            'page_title' => 'Calculator Analytics',
            'calculator_stats' => $this->getCalculatorStats(),
            'usage_data' => $this->getCalculatorUsageData()
        ];

        $this->view->render('admin/analytics/calculators', $data);
    }

    /**
     * Performance analytics page
     */
    public function performance()
    {
        $data = [
            'page_title' => 'Performance Analytics',
            'performance_metrics' => $this->getPerformanceMetrics()
        ];

        $this->view->render('admin/analytics/performance', $data);
    }

    /**
     * Reports page
     */
    public function reports()
    {
        $data = [
            'page_title' => 'Analytics Reports',
            'available_reports' => $this->getAvailableReports()
        ];

        $this->view->render('admin/analytics/reports', $data);
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        try {
            $stats = [];

            // Total users
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
            $stats['total_users'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Active users (last 30 days)
            $stmt = $this->db->query("
                SELECT COUNT(DISTINCT user_id) as count 
                FROM calculation_history 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stats['active_users'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Total calculations
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM calculation_history");
            $stats['total_calculations'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Monthly calculations
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM calculation_history 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stats['monthly_calculations'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            return $stats;
        } catch (\Exception $e) {
            error_log('Analytics stats error: ' . $e->getMessage());
            return [
                'total_users' => 0,
                'active_users' => 0,
                'total_calculations' => 0,
                'monthly_calculations' => 0
            ];
        }
    }

    /**
     * Get chart data for overview
     */
    private function getChartData()
    {
        try {
            // Get daily calculations for last 30 days
            $stmt = $this->db->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM calculation_history
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");

            $dailyCalculations = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $dailyCalculations[] = [
                    'date' => $row['date'],
                    'count' => (int)$row['count']
                ];
            }

            return [
                'daily_calculations' => $dailyCalculations
            ];
        } catch (\Exception $e) {
            error_log('Chart data error: ' . $e->getMessage());
            return ['daily_calculations' => []];
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        try {
            $stats = [];

            // Users by role
            $stmt = $this->db->query("
                SELECT role, COUNT(*) as count 
                FROM users 
                GROUP BY role
            ");
            $stats['by_role'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // New users this month
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stats['new_this_month'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            return $stats;
        } catch (\Exception $e) {
            error_log('User stats error: ' . $e->getMessage());
            return ['by_role' => [], 'new_this_month' => 0];
        }
    }

    /**
     * Get user growth data
     */
    private function getUserGrowthData()
    {
        try {
            $stmt = $this->db->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");

            $growth = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $growth[] = [
                    'date' => $row['date'],
                    'count' => (int)$row['count']
                ];
            }

            return $growth;
        } catch (\Exception $e) {
            error_log('User growth data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get calculator statistics
     */
    private function getCalculatorStats()
    {
        try {
            // Most used calculators
            $stmt = $this->db->query("
                SELECT calculator_type, COUNT(*) as usage_count
                FROM calculation_history
                GROUP BY calculator_type
                ORDER BY usage_count DESC
                LIMIT 10
            ");

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Calculator stats error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get calculator usage data
     */
    private function getCalculatorUsageData()
    {
        try {
            $stmt = $this->db->query("
                SELECT DATE(created_at) as date, calculator_type, COUNT(*) as count
                FROM calculation_history
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at), calculator_type
                ORDER BY date ASC
            ");

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Calculator usage data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'avg_page_load' => '1.2s',
            'avg_calculation_time' => '0.5s',
            'server_uptime' => '99.9%',
            'error_rate' => '0.1%'
        ];
    }

    /**
     * Get available reports
     */
    private function getAvailableReports()
    {
        return [
            [
                'name' => 'User Activity Report',
                'description' => 'Detailed user activity and engagement metrics',
                'type' => 'user_activity'
            ],
            [
                'name' => 'Calculator Usage Report',
                'description' => 'Calculator usage statistics and trends',
                'type' => 'calculator_usage'
            ],
            [
                'name' => 'Performance Report',
                'description' => 'System performance and health metrics',
                'type' => 'performance'
            ]
        ];
    }

    /**
     * Check admin access
     */
    private function checkAdminAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: /login');
            exit;
        }
    }
}
