<?php

namespace App\Services;

use App\Models\User;
use App\Models\Calculation;
use App\Models\ActivityLog;
use Exception;

class AnalyticsService
{
    private $userModel;
    private $calculationModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->calculationModel = new Calculation();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Get overview statistics for the analytics dashboard
     */
    public function getOverviewStats()
    {
        try {
            $stats = [];

            // Total users and growth (vs previous month)
            $totalUsers = $this->userModel->getTotalUserCount();
            $newUsersCurrent = $this->userModel->getNewUserCount(30);
            $newUsersPrevious = $this->userModel->getNewUserCount(60) - $newUsersCurrent;
            $stats['total_users'] = $totalUsers;
            $stats['new_users'] = $newUsersCurrent;
            $stats['user_growth'] = $this->calculateGrowthRate($newUsersCurrent, $newUsersPrevious);

            // Active users
            $activeCurrent = $this->calculationModel->getActiveUserCount(30);
            $activePrevious = $this->calculationModel->getActiveUserCountBetween(60, 30);
            $stats['active_users'] = $activeCurrent;
            $stats['active_user_growth'] = $this->calculateGrowthRate($activeCurrent, $activePrevious);

            // Calculations
            $totalCalculations = $this->calculationModel->getTotalCalculationCount();
            $monthlyCalculations = $this->calculationModel->getMonthlyCalculationCount(30);
            $previousMonthCalculations = $this->calculationModel->getCalculationCountBetween(60, 30);
            $stats['total_calculations'] = $totalCalculations;
            $stats['monthly_calculations'] = $monthlyCalculations;
            $stats['calculation_growth'] = $this->calculateGrowthRate($monthlyCalculations, $previousMonthCalculations);
            $stats['monthly_calculation_growth'] = $stats['calculation_growth'];

            // Engagement / performance placeholders
            $stats['avg_session_duration'] = $this->formatDuration(5 * 60 + 12); // 5m12s placeholder
            $stats['bounce_rate'] = 27.4; // placeholder — replace when real metric available

            // Top calculators
            $stats['top_calculators'] = $this->calculationModel->getTopCalculators(5);

            return $stats;
        } catch (Exception $e) {
            error_log('AnalyticsService overview stats error: ' . $e->getMessage());
            return [
                'total_users' => 0,
                'active_users' => 0,
                'total_calculations' => 0,
                'monthly_calculations' => 0,
                'user_growth' => 0,
                'active_user_growth' => 0,
                'calculation_growth' => 0,
                'monthly_calculation_growth' => 0,
                'avg_session_duration' => '0m 00s',
                'bounce_rate' => 0,
                'top_calculators' => []
            ];
        }
    }

    /**
     * Get chart data for overview
     */
    public function getChartData($days = 30)
    {
        try {
            // Get daily calculations for specified period
            $rawData = $this->calculationModel->getDailyCalculations($days);

            // Format the data
            $formattedData = [];
            foreach ($rawData as $row) {
                $formattedData[] = [
                    'date' => $row['date'],
                    'count' => (int)$row['count']
                ];
            }

            return [
                'daily_calculations' => $formattedData
            ];
        } catch (Exception $e) {
            error_log('AnalyticsService chart data error: ' . $e->getMessage());
            return ['daily_calculations' => []];
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        try {
            $stats = [];

            // Users by role
            $stats['by_role'] = $this->userModel->getUserStats();

            // New users this month
            $stats['new_this_month'] = $this->userModel->getNewUserCount(30);

            return $stats;
        } catch (Exception $e) {
            error_log('AnalyticsService user stats error: ' . $e->getMessage());
            return ['by_role' => [], 'new_this_month' => 0];
        }
    }

    /**
     * Get user growth data
     */
    public function getUserGrowthData($days = 90)
    {
        try {
            $rawGrowth = $this->userModel->getUserGrowthData($days);

            // Format the data
            $growth = [];
            foreach ($rawGrowth as $row) {
                $growth[] = [
                    'date' => $row['date'],
                    'count' => (int)$row['count']
                ];
            }

            return $growth;
        } catch (Exception $e) {
            error_log('AnalyticsService user growth data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get calculator statistics
     */
    public function getCalculatorStats($limit = 10)
    {
        try {
            return $this->calculationModel->getCalculatorStats($limit);
        } catch (Exception $e) {
            error_log('AnalyticsService calculator stats error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get calculator usage data
     */
    public function getCalculatorUsageData($days = 30)
    {
        try {
            return $this->calculationModel->getCalculatorUsageData($days);
        } catch (Exception $e) {
            error_log('AnalyticsService calculator usage data error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics()
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
    public function getAvailableReports()
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
     * Generate a specific report
     */
    public function generateReport($type, $options = [])
    {
        switch ($type) {
            case 'user_activity':
                return $this->generateUserActivityReport($options);
            case 'calculator_usage':
                return $this->generateCalculatorUsageReport($options);
            case 'performance':
                return $this->generatePerformanceReport($options);
            default:
                throw new Exception("Unknown report type: {$type}");
        }
    }

    /**
     * Generate user activity report
     */
    private function generateUserActivityReport($options)
    {
        $days = $options['days'] ?? 30;
        
        return [
            'report_type' => 'user_activity',
            'generated_at' => date('Y-m-d H:i:s'),
            'period' => $days,
            'data' => [
                'total_users' => $this->userModel->getTotalUserCount(),
                'active_users' => $this->userModel->getNewUserCount($days),
                'user_growth' => $this->getUserGrowthData($days),
                'user_stats' => $this->getUserStats()
            ]
        ];
    }

    /**
     * Generate calculator usage report
     */
    private function generateCalculatorUsageReport($options)
    {
        $days = $options['days'] ?? 30;
        
        return [
            'report_type' => 'calculator_usage',
            'generated_at' => date('Y-m-d H:i:s'),
            'period' => $days,
            'data' => [
                'total_calculations' => $this->calculationModel->getTotalCalculationCount(),
                'calculator_popularity' => $this->getCalculatorStats(20),
                'usage_trends' => $this->getCalculatorUsageData($days),
                'daily_calculations' => $this->getChartData($days)
            ]
        ];
    }

    /**
     * Generate performance report
     */
    private function generatePerformanceReport($options)
    {
        return [
            'report_type' => 'performance',
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => $this->getPerformanceMetrics()
        ];
    }

    /**
     * Get real-time activity data
     */
    public function getRealtimeActivity()
    {
        try {
            // Get recent activity logs
            $recentActivity = $this->activityLogModel->getRecent(10);
            
            return [
                'recent_activity' => $recentActivity,
                'active_users_now' => $this->getActiveUsersCount(),
                'recent_calculations' => $this->calculationModel->getRecent(10)
            ];
        } catch (Exception $e) {
            error_log('AnalyticsService realtime activity error: ' . $e->getMessage());
            return [
                'recent_activity' => [],
                'active_users_now' => 0,
                'recent_calculations' => []
            ];
        }
    }

    /**
     * Get active user count (users with activity in last 5 minutes)
     */
    private function getActiveUsersCount()
    {
        try {
            $fiveMinsAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            
            $stmt = $this->activityLogModel->getDb()->getPdo()->prepare("
                SELECT COUNT(DISTINCT user_id) as count
                FROM activity_logs
                WHERE created_at >= ? AND user_id IS NOT NULL
            ");
            $stmt->execute([$fiveMinsAgo]);
            $result = $stmt->fetch();
            
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log('AnalyticsService active users count error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get the database connection from a model for internal use
     */
    public function getDatabaseConnection()
    {
        return $this->userModel->getDb()->getPdo();
    }
}