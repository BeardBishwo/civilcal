<?php

namespace App\Modules\Admin;

use App\Core\AdminModule;
use App\Models\User;

/**
 * Main Dashboard Module - Overview and Statistics
 */
class DashboardModule extends AdminModule
{
    protected function init()
    {
        $this->name = 'Dashboard';
        $this->version = '1.0.0';
        $this->description = 'Main admin dashboard with statistics and overview';
        $this->author = 'Bishwo Calculator Team';
        $this->icon = 'fas fa-tachometer-alt';
        $this->permissions = ['admin', 'super_admin'];
    }
    
    public function registerMenu()
    {
        return [
            'title' => 'Dashboard',
            'url' => '/admin/dashboard',
            'icon' => $this->icon,
            'position' => 1,
            'submenu' => []
        ];
    }
    
    public function renderWidget()
    {
        $userModel = new User();
        $totalUsers = count($userModel->getAll());
        $recentUsers = array_slice($userModel->getAll(), 0, 5);
        
        return [
            'title' => 'User Overview',
            'content' => $this->getUserStatsWidget($totalUsers, $recentUsers),
            'size' => 'large'
        ];
    }
    
    private function getUserStatsWidget($totalUsers, $recentUsers)
    {
        ob_start();
        ?>
        <div class="dashboard-widget user-stats">
            <div class="widget-header">
                <h3><i class="fas fa-users"></i> User Statistics</h3>
            </div>
            <div class="widget-content">
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalUsers; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($recentUsers, function($u) { return !empty($u['last_login']); })); ?></div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
                
                <div class="recent-users">
                    <h4>Recent Registrations</h4>
                    <div class="user-list">
                        <?php foreach (array_slice($recentUsers, 0, 3) as $user): ?>
                        <div class="user-item">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <div class="user-role">
                                <span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span>
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
            'dashboard_widgets' => [
                'type' => 'array',
                'label' => 'Enabled Dashboard Widgets',
                'options' => [
                    'user_stats' => 'User Statistics',
                    'calculator_usage' => 'Calculator Usage',
                    'system_info' => 'System Information',
                    'recent_activity' => 'Recent Activity'
                ],
                'default' => ['user_stats', 'system_info']
            ],
            'refresh_interval' => [
                'type' => 'select',
                'label' => 'Auto Refresh Interval',
                'options' => [
                    '30' => '30 seconds',
                    '60' => '1 minute',
                    '300' => '5 minutes',
                    'manual' => 'Manual only'
                ],
                'default' => '60'
            ]
        ];
    }
}
?>
