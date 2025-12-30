<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    private $analyticsService;

    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();

        // Initialize the service
        $this->analyticsService = new AnalyticsService();
    }

    /**
     * Analytics overview page
     */
    public function overview()
    {
        $data = [
            'page_title' => 'Analytics Overview',
            'stats' => $this->analyticsService->getOverviewStats(),
            'charts' => $this->analyticsService->getChartData(),
            'location_stats' => $this->analyticsService->getLoginLocationStats(5)
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
            'user_stats' => $this->analyticsService->getUserStats(),
            'growth_data' => $this->analyticsService->getUserGrowthData()
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
            'calculator_stats' => $this->analyticsService->getCalculatorStats(),
            'usage_data' => $this->analyticsService->getCalculatorUsageData()
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
            'performance_metrics' => $this->analyticsService->getPerformanceMetrics()
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
            'available_reports' => $this->analyticsService->getAvailableReports()
        ];

        $this->view->render('admin/analytics/reports', $data);
    }

    /**
     * Generate and download report
     */
    public function generateReport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['report_type'] ?? '';
            $days = $_POST['days'] ?? 30;
            
            try {
                $report = $this->analyticsService->generateReport($type, ['days' => $days]);
                
                $filename = $type . '_' . date('Y-m-d') . '.csv';
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                fputs($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                
                // Helper to flatten and write data
                fputcsv($output, ['Report Type', $type]);
                fputcsv($output, ['Generated At', $report['generated_at']]);
                fputcsv($output, ['Period', $days . ' days']);
                fputcsv($output, []); // Empty line
                
                if ($type === 'user_activity') {
                   // User Activity Data
                   fputcsv($output, ['Metric', 'Value']);
                   fputcsv($output, ['Total Users', $report['data']['total_users']]);
                   fputcsv($output, ['Active Users', $report['data']['active_users']]);
                   fputcsv($output, []);
                   
                   fputcsv($output, ['Date', 'User Count']);
                   foreach ($report['data']['user_growth'] as $row) {
                       fputcsv($output, [$row['date'], $row['count']]);
                   }
                } elseif ($type === 'calculator_usage') {
                    // Calculator Usage Data
                    fputcsv($output, ['Metric', 'Value']);
                    fputcsv($output, ['Total Calculations', $report['data']['total_calculations']]);
                     fputcsv($output, []);
                    
                    fputcsv($output, ['Calculator', 'Uses', 'Share (%)']);
                    foreach ($report['data']['calculator_popularity'] as $calc) {
                        fputcsv($output, [$calc['name'], $calc['uses'], $calc['share']]);
                    }
                } elseif ($type === 'performance') {
                    // Performance Data
                    fputcsv($output, ['Metric', 'Value']);
                    foreach ($report['data'] as $key => $value) {
                        fputcsv($output, [ucwords(str_replace('_', ' ', $key)), $value]);
                    }
                }
                
                fclose($output);
                exit;
            } catch (\Exception $e) {
                error_log('Report generation failed: ' . $e->getMessage());
                $_SESSION['error'] = 'Failed to generate report.';
            }
        }
        
        header('Location: ' . app_base_url('/admin/analytics/reports'));
        exit;
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
            header('Location: ' . app_base_url('/login'));
            exit;
        }
    }
}
