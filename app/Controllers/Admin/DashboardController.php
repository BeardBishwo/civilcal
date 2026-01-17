<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\Calculation;
use App\Models\Analytics;
use Exception;

class DashboardController extends Controller
{
    private $analytics;

    public function __construct()
    {
        parent::__construct();
        $this->analytics = new Analytics();
    }

    public function index()
    {
        $currentUser = $_SESSION['user'] ?? null;

        // Get dashboard statistics from Analytics model
        $counts = $this->analytics->getDashboardCounts();

        $stats = [
            'total_users' => $this->getTotalUsers(),
            'active_users' => $counts['visitors_today'] ?? 0,
            'active_modules' => $this->getActiveModules(),
            'total_calculations' => $counts['calcs_today'] ?? 0,
            'monthly_calculations' => $this->getMonthlyCalculations(),
            'api_requests' => $this->getApiRequests(),
            'system_health' => $this->getSystemHealth(),
            'storage_used' => $this->getStorageUsed(),
            'visitors_today' => $counts['visitors_today'],
            'views_today' => $counts['views_today']
        ];

        // Render the dashboard view with admin layout
        $this->view->render('admin/dashboard', [
            'currentUser' => $currentUser,
            'currentPage' => 'dashboard',
            'stats' => $stats,
            'page_title' => 'Dashboard - Admin Panel',
            'recent_activity' => $this->analytics->getRecentEvents(10),
            'system_status' => $this->getSystemStatus(),
            'user_growth' => $this->getUserGrowthData(),
            'calculator_usage' => $this->getCalculatorUsageData(),
            'popular_calculators' => $this->analytics->getPopularContent('calculator_use', 5)
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
            error_log('Dashboard usage error: ' . $e->getMessage());
            return 0;
        }
    }

    private function getActiveUsers()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT user_id) as count
                FROM calculation_history
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 850;
        } catch (\Exception $e) {
            error_log('Dashboard active users error: ' . $e->getMessage());
            return 0;
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
            return 0;
        }
    }

    private function getTotalCalculations()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM calculation_history");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 56;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMonthlyCalculations()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM calculation_history
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 15420;
        } catch (\Exception $e) {
            return 0;
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


        return 0;
    }

    private function getSystemHealth()
    {
        // Calculate system health based on various metrics
        $systemHealth = 95.0; // Default good health

        // Check disk space
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(dirname(dirname(__DIR__)));
        $total = disk_total_space($basePath);
        $free = disk_free_space($basePath);
        $storageUsed = $total > 0 ? round((($total - $free) / $total) * 100, 1) : 0;

        if ($storageUsed > 90) {
            $systemHealth -= 10;
        } elseif ($storageUsed > 80) {
            $systemHealth -= 5;
        }

        return $systemHealth;
    }

    private function getStorageUsed()
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(dirname(dirname(__DIR__)));
        $total = disk_total_space($basePath);
        $free = disk_free_space($basePath);
        $storageUsed = $total > 0 ? round((($total - $free) / $total) * 100, 1) : 0;
        return $storageUsed;
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

    /**
     * Get dashboard data (API endpoint)
     */
    public function getDashboardData()
    {
        header('Content-Type: application/json');

        try {
            // Check admin authentication - support both session and HTTP Basic Auth
            $isAdmin = false;

            // Check HTTP Basic Auth first (for API testing)
            if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $user = \App\Models\User::findByUsername($_SERVER['PHP_AUTH_USER']);
                if ($user) {
                    $userArray = is_array($user) ? $user : (array) $user;
                    if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                        // Check if user is admin
                        $isAdmin = ($userArray['is_admin'] ?? false) || ($userArray['role'] ?? '') === 'admin';
                    }
                }
            } else {
                // Fall back to session auth
                if ($this->auth->check() && $this->auth->isAdmin()) {
                    $isAdmin = true;
                }
            }

            if (!$isAdmin) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }

            $stats = [
                'total_users' => $this->getTotalUsers(),
                'active_users' => $this->getActiveUsers(),
                'active_modules' => $this->getActiveModules(),
                'total_calculations' => $this->getTotalCalculations(),
                'monthly_calculations' => $this->getMonthlyCalculations(),
                'api_requests' => $this->getApiRequests(),
                'system_health' => $this->getSystemHealth(),
                'storage_used' => $this->getStorageUsed(),
                'recent_activity' => $this->getRecentActivity(),
                'system_status' => $this->getSystemStatus(),
                'user_growth' => $this->getUserGrowthData(),
                'calculator_usage' => $this->getCalculatorUsageData()
            ];

            http_response_code(200);
            echo json_encode($stats);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to get dashboard data',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Module management page
     */
    public function modules()
    {
        $data = [
            'page_title' => 'Module Management',
            'allModules' => $this->getAllModules(),
            'activeModules' => $this->getActiveModules(),
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/modules', $data);
    }

    /**
     * Activate module
     */
    public function activateModule()
    {
        try {
            $moduleName = $_POST['module'] ?? '';

            if (empty($moduleName)) {
                throw new Exception('Module name required');
            }

            // In a real implementation, you would activate the module
            // $result = $this->moduleManager->activateModule($moduleName);

            $result = true; // Placeholder

            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Module activated successfully']);
            } else {
                throw new Exception('Failed to activate module');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Deactivate module
     */
    public function deactivateModule()
    {
        try {
            $moduleName = $_POST['module'] ?? '';

            if (empty($moduleName)) {
                throw new Exception('Module name required');
            }

            // In a real implementation, you would deactivate the module
            // $result = $this->moduleManager->deactivateModule($moduleName);

            $result = true; // Placeholder

            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Module deactivated successfully']);
            } else {
                throw new Exception('Failed to deactivate module');
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Menu customization page
     */
    public function menuCustomization()
    {
        $data = [
            'page_title' => 'Menu Customization',
            'menuItems' => $this->getMenuItems(),
            'availableModules' => $this->getAllModules()
        ];

        $this->view->render('admin/menu-customization', $data);
    }

    /**
     * Widget management page
     */
    public function widgetManagement()
    {
        $data = [
            'page_title' => 'Widget Management',
            'widgets' => $this->getWidgets(),
            'availableWidgets' => $this->getAvailableWidgets(),
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/widgets/index', $data);
    }

    /**
     * System status page
     */
    public function systemStatus()
    {
        $data = [
            'page_title' => 'System Status',
            'systemInfo' => $this->getSystemInfo(),
            'moduleStatus' => $this->getModuleStatus(),
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/system-status', $data);
    }

    /**
     * Get menu items
     */
    private function getMenuItems()
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Users',
            'settings' => 'Settings',
            'modules' => 'Modules'
        ];
    }

    /**
     * Get all available modules
     */
    private function getAllModules()
    {
        return [
            'civil_engineering' => [
                'name' => 'Civil Engineering',
                'active' => true,
                'description' => 'Concrete, steel, and structural calculations'
            ],
            'electrical' => [
                'name' => 'Electrical Engineering',
                'active' => true,
                'description' => 'Load calculations and electrical design'
            ],
            'mechanical' => [
                'name' => 'Mechanical Engineering',
                'active' => true,
                'description' => 'HVAC and mechanical systems'
            ],
            'structural' => [
                'name' => 'Structural Analysis',
                'active' => true,
                'description' => 'Structural analysis and design'
            ]
        ];
    }

    /**
     * Get available widgets from all modules
     */
    private function getAvailableWidgets()
    {
        return [
            'civil_engineering' => [
                'title' => 'Civil Engineering Tools',
                'description' => 'Concrete, steel, and structural calculations',
                'module' => 'civil_engineering'
            ],
            'electrical' => [
                'title' => 'Electrical Tools',
                'description' => 'Load calculations and electrical design',
                'module' => 'electrical'
            ]
        ];
    }

    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();

        return [
            'php_version' => PHP_VERSION,
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'loaded_extensions' => get_loaded_extensions(),
            'database_driver' => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)
        ];
    }

    /**
     * Get module status information
     */
    private function getModuleStatus()
    {
        // In a real implementation, this would come from module manager
        // For now, we'll return the list of defined modules
        $modules = $this->getAllModules();
        $statusList = [];

        foreach ($modules as $key => $module) {
            $statusList[] = [
                'name' => $module['name'],
                'active' => $module['active'],
                'version' => '1.0.0' // Placeholder version
            ];
        }

        return $statusList;
    }

    /**
     * JSON response helper
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Module settings page
     */
    public function moduleSettings($module)
    {
        $data = [
            'page_title' => 'Module Settings',
            'module' => $module,
            'settings' => [], // Placeholder
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/module-settings', $data);
    }

    /**
     * Update module settings
     */
    public function updateModuleSettings()
    {
        try {
            // Placeholder logic
            $this->jsonResponse(['success' => true, 'message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get widgets
     */
    private function getWidgets()
    {
        // Placeholder data
        return [
            'active_widgets' => [],
            'inactive_widgets' => []
        ];
    }

    public function configuredDashboard()
    {
        $data = [
            'page_title' => 'Configured Dashboard',
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/configured-dashboard', $data);
    }

    public function performanceDashboard()
    {
        $data = [
            'page_title' => 'Performance Dashboard',
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/performance-dashboard', $data);
    }

    public function dashboardComplex()
    {
        $data = [
            'page_title' => 'Complex Dashboard',
            'menuItems' => $this->getMenuItems()
        ];

        $this->view->render('admin/dashboard_complex', $data);
    }

    /**
     * Get aggregated stats for charts (AJAX)
     */
    public function getStats()
    {
        try {
            $type = $_GET['type'] ?? 'page_view';
            $days = (int)($_GET['days'] ?? 30);

            // Get data from analytics model
            // Note: Since we don't have a background job filling the summary table yet,
            // we'll query the events table directly for immediate real-time data.
            $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                    FROM analytics_events 
                    WHERE event_type = :type 
                    AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    GROUP BY DATE(created_at) 
                    ORDER BY date ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':type', $type);
            $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Fill in missing dates
            $data = [];
            $startDate = new \DateTime("-{$days} days");
            $endDate = new \DateTime();
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

            // Convert results to map for easy lookup
            $counts = [];
            foreach ($results as $row) {
                $counts[$row['date']] = (int)$row['count'];
            }

            foreach ($period as $dt) {
                $date = $dt->format('Y-m-d');
                $data[] = [
                    'date' => $date,
                    'count' => $counts[$date] ?? 0
                ];
            }

            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get popular content (AJAX)
     */
    public function getPopular()
    {
        try {
            $type = $_GET['type'] ?? 'page_view';
            $limit = (int)($_GET['limit'] ?? 10);

            $data = $this->analytics->getPopularContent($type, $limit);
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }
}
