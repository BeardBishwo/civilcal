<?php

namespace App\Modules\Analytics;

use App\Core\AdminModule;

/**
 * Analytics Module - Comprehensive site analytics
 */
class AnalyticsModule extends AdminModule
{
    protected function init()
    {
        $this->name = 'Analytics';
        $this->version = '1.0.0';
        $this->description = 'Comprehensive analytics and reporting system';
        $this->author = 'Bishwo Calculator Team';
        $this->icon = 'fas fa-chart-line';
        $this->permissions = ['admin', 'super_admin'];
    }
    
    public function registerMenu()
    {
        return [
            'title' => 'Analytics',
            'url' => '/admin/analytics',
            'icon' => $this->icon,
            'position' => 3,
            'submenu' => [
                [
                    'title' => 'Overview',
                    'url' => '/admin/analytics/overview',
                    'icon' => 'fas fa-chart-area'
                ],
                [
                    'title' => 'User Analytics',
                    'url' => '/admin/analytics/users',
                    'icon' => 'fas fa-users'
                ],
                [
                    'title' => 'Calculator Usage',
                    'url' => '/admin/analytics/calculators',
                    'icon' => 'fas fa-calculator'
                ],
                [
                    'title' => 'Performance',
                    'url' => '/admin/analytics/performance',
                    'icon' => 'fas fa-tachometer-alt'
                ],
                [
                    'title' => 'Reports',
                    'url' => '/admin/analytics/reports',
                    'icon' => 'fas fa-file-alt'
                ]
            ]
        ];
    }
    
    public function renderWidget()
    {
        $analytics = $this->getAnalyticsData();
        
        return [
            'title' => 'Site Analytics',
            'content' => $this->getAnalyticsWidget($analytics),
            'size' => 'large'
        ];
    }
    
    private function getAnalyticsData()
    {
        // Simulate analytics data - in real implementation, this would come from database
        return [
            'page_views_today' => rand(100, 500),
            'unique_visitors_today' => rand(50, 200),
            'calculator_usage_today' => rand(30, 150),
            'avg_session_duration' => '4:32',
            'bounce_rate' => '35.2%',
            'top_calculators' => [
                'Concrete Volume' => 45,
                'Rebar Calculator' => 32,
                'Foundation Design' => 28
            ]
        ];
    }
    
    private function getAnalyticsWidget($analytics)
    {
        ob_start();
        ?>
        <div class="dashboard-widget analytics">
            <div class="widget-header">
                <h3><i class="fas fa-chart-line"></i> Today's Analytics</h3>
                <a href="/admin/analytics" class="widget-link">View Details</a>
            </div>
            <div class="widget-content">
                <div class="analytics-grid">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-eye"></i></div>
                        <div class="metric-details">
                            <div class="metric-value"><?php echo number_format($analytics['page_views_today']); ?></div>
                            <div class="metric-label">Page Views</div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-users"></i></div>
                        <div class="metric-details">
                            <div class="metric-value"><?php echo number_format($analytics['unique_visitors_today']); ?></div>
                            <div class="metric-label">Unique Visitors</div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-calculator"></i></div>
                        <div class="metric-details">
                            <div class="metric-value"><?php echo number_format($analytics['calculator_usage_today']); ?></div>
                            <div class="metric-label">Calculations</div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fas fa-clock"></i></div>
                        <div class="metric-details">
                            <div class="metric-value"><?php echo $analytics['avg_session_duration']; ?></div>
                            <div class="metric-label">Avg. Session</div>
                        </div>
                    </div>
                </div>
                
                <div class="top-calculators">
                    <h4>Most Used Calculators</h4>
                    <div class="calculator-list">
                        <?php foreach ($analytics['top_calculators'] as $name => $count): ?>
                        <div class="calculator-item">
                            <span class="calculator-name"><?php echo $name; ?></span>
                            <span class="calculator-count"><?php echo $count; ?> uses</span>
                            <div class="usage-bar">
                                <div class="usage-fill" style="width: <?php echo ($count / 50) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function getSettingsSchema()
    {
        return [
            'tracking_enabled' => [
                'type' => 'checkbox',
                'label' => 'Enable Analytics Tracking',
                'default' => true
            ],
            'google_analytics_id' => [
                'type' => 'text',
                'label' => 'Google Analytics ID',
                'placeholder' => 'GA-XXXXXXXXX-X'
            ],
            'data_retention_days' => [
                'type' => 'number',
                'label' => 'Data Retention (Days)',
                'default' => 365
            ],
            'anonymous_tracking' => [
                'type' => 'checkbox',
                'label' => 'Anonymous Tracking Only',
                'default' => true
            ]
        ];
    }
    
    protected function onInstall()
    {
        // Create analytics tables
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
            
            $createTables = [
                "CREATE TABLE IF NOT EXISTS analytics_page_views (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NULL,
                    page_url VARCHAR(500),
                    referrer VARCHAR(500),
                    user_agent TEXT,
                    ip_address VARCHAR(45),
                    session_id VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
                
                "CREATE TABLE IF NOT EXISTS analytics_calculator_usage (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NULL,
                    calculator_name VARCHAR(255),
                    calculation_data JSON,
                    session_id VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_calculator_name (calculator_name),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            ];
            
            foreach ($createTables as $sql) {
                $pdo->exec($sql);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Analytics module install error: " . $e->getMessage());
            return false;
        }
    }
}
?>
