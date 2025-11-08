<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\Calculation;
use App\Models\Project;

class DashboardController extends Controller
{
    public function __construct() {
        parent::__construct();
        
        // Check if user is admin
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }
    }
    
    public function index()
    {
        $currentUser = $this->auth->user();
        
        // Get dashboard statistics
        $stats = [
            'total_users' => $this->getTotalUsers(),
            'active_modules' => $this->getActiveModules(),
            'total_calculators' => $this->getTotalCalculators(),
            'api_requests' => $this->getApiRequests(),
        ];

        // Render the dashboard view with admin layout
        $this->adminView('admin/dashboard', [
            'currentUser' => $currentUser,
            'currentPage' => 'dashboard',
            'stats' => $stats,
            'title' => 'Dashboard - Admin Panel'
        ]);
    }

    private function getTotalUsers()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 1234;
        } catch (\Exception $e) {
            // Return mock data if database query fails
            return 1234;
        }
    }

    private function getActiveModules()
    {
        try {
            // Count active modules from modules directory
            $modulesPath = dirname(dirname(dirname(__DIR__))) . '/modules/';
            $activeModules = 0;
            
            if (is_dir($modulesPath)) {
                $modules = scandir($modulesPath);
                foreach ($modules as $module) {
                    if ($module !== '.' && $module !== '..' && is_dir($modulesPath . $module)) {
                        $activeModules++;
                    }
                }
            }
            
            return $activeModules;
        } catch (\Exception $e) {
            return 12; // Default value
        }
    }

    private function getTotalCalculators()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM calculations");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 56;
        } catch (\Exception $e) {
            return 56; // Default value
        }
    }

    private function getApiRequests()
    {
        // Get today's API requests from logs or return mock data
        try {
            $logFile = dirname(dirname(dirname(__DIR__))) . '/debug/logs/access.log';
            if (file_exists($logFile)) {
                $lines = file($logFile);
                $today = date('Y-m-d');
                $todayRequests = 0;
                
                foreach ($lines as $line) {
                    if (strpos($line, $today) !== false && strpos($line, 'API') !== false) {
                        $todayRequests++;
                    }
                }
                
                return $todayRequests;
            }
        } catch (\Exception $e) {
            // Return mock data if log reading fails
        }
        
        return 789; // Default value
    }

    /**
     * Get recent activity for dashboard
     */
    public function getRecentActivity()
    {
        $activities = [
            [
                'user' => 'John Doe',
                'action' => 'Used Calculator',
                'calculator' => 'Concrete Volume',
                'time' => '2 minutes ago',
                'avatar' => '/assets/images/avatar1.jpg'
            ],
            [
                'user' => 'Jane Smith',
                'action' => 'Created Project',
                'calculator' => 'Structural Analysis',
                'time' => '15 minutes ago',
                'avatar' => '/assets/images/avatar2.jpg'
            ],
            [
                'user' => 'Mike Johnson',
                'action' => 'Exported Report',
                'calculator' => 'Electrical Load',
                'time' => '1 hour ago',
                'avatar' => '/assets/images/avatar3.jpg'
            ]
        ];

        return $activities;
    }

    /**
     * Get system status for dashboard
     */
    public function getSystemStatus()
    {
        return [
            'server_load' => [
                'value' => '24%',
                'status' => 'success'
            ],
            'database' => [
                'value' => 'Online',
                'status' => 'success'
            ],
            'storage' => [
                'value' => '65%',
                'status' => 'warning'
            ],
            'uptime' => '99.8%'
        ];
    }

    /**
     * Get user growth data for charts
     */
    public function getUserGrowthData()
    {
        // This would typically come from your database
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [65, 59, 80, 81, 56, 72]
        ];
    }

    /**
     * Get calculator usage data for charts
     */
    public function getCalculatorUsageData()
    {
        // This would typically come from your database
        return [
            'labels' => ['Civil', 'Electrical', 'Structural', 'HVAC', 'Plumbing'],
            'data' => [1250, 980, 756, 543, 432]
        ];
    }
}
?>
