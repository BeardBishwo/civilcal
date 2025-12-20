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
            'charts' => $this->analyticsService->getChartData()
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
