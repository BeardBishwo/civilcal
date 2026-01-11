<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

/**
 * Admin Setup Controller
 * Handles admin setup checklist and configuration tracking
 */
class SetupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show admin setup checklist
     */
    public function checklist()
    {
        $this->setTitle('Site Setup Checklist - Admin Panel');
        $this->setDescription('Track your site configuration progress and ensure all essential settings are completed');
        $this->setCategory('admin');

        // Get current admin user
        $currentUser = $this->getUser();
        
        $data = [
            'page_title' => 'Site Setup Checklist',
            'current_user' => $currentUser,
            'checklist_sections' => $this->getChecklistSections(),
            'overall_progress' => $this->calculateOverallProgress(),
            'quick_actions' => $this->getQuickActions(),
            'recent_activities' => $this->getRecentActivities(),
            'system_status' => $this->getSystemStatus()
        ];

        $this->view->render('admin/setup/checklist', $data);
    }

    /**
     * Update checklist item status
     */
    public function updateItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['item_id'] ?? '';
        $completed = $input['completed'] ?? false;

        // In a real application, this would update the database
        // For now, we'll simulate the update
        $success = $this->updateChecklistItem($itemId, $completed);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Checklist item updated' : 'Failed to update item',
            'overall_progress' => $this->calculateOverallProgress()
        ]);
    }

    /**
     * Get checklist sections with items
     */
    private function getChecklistSections()
    {
        $savedStatus = \App\Services\SettingsService::get('admin_setup_checklist', []);
        
        $sections = [
            [
                'id' => 'basic-information',
                'title' => 'Basic Information',
                'description' => 'Essential site information and branding',
                'items' => [
                    ['id' => 'site-url', 'title' => 'Site URL', 'description' => 'Configure your site\'s primary URL', 'required' => true, 'action_url' => '/admin/settings/general', 'action_text' => 'Configure'],
                    ['id' => 'site-name', 'title' => 'Site Name', 'description' => 'Set your site\'s display name', 'required' => true, 'action_url' => '/admin/settings/general', 'action_text' => 'Configure'],
                    ['id' => 'site-title', 'title' => 'Site Title', 'description' => 'Configure page titles and SEO', 'required' => true, 'action_url' => '/admin/settings/seo', 'action_text' => 'Configure'],
                    ['id' => 'site-description', 'title' => 'Site Description', 'description' => 'Add a compelling site description', 'required' => true, 'action_url' => '/admin/settings/seo', 'action_text' => 'Configure'],
                    ['id' => 'site-logo', 'title' => 'Site Logo', 'description' => 'Upload and configure your brand logo', 'required' => false, 'action_url' => '/admin/logo-settings', 'action_text' => 'Upload'],
                    ['id' => 'favicon', 'title' => 'Favicon', 'description' => 'Set your site\'s favicon icon', 'required' => false, 'action_url' => '/admin/logo-settings', 'action_text' => 'Upload']
                ]
            ],
            [
                'id' => 'user-settings',
                'title' => 'User Settings',
                'description' => 'User management and authentication configuration',
                'items' => [
                    ['id' => 'user-registration', 'title' => 'User Registration', 'description' => 'Configure user registration settings', 'required' => true, 'action_url' => '/admin/settings/users', 'action_text' => 'Configure'],
                    ['id' => 'email-verification', 'title' => 'Email Verification', 'description' => 'Set up email verification for new users', 'required' => true, 'action_url' => '/admin/settings/email', 'action_text' => 'Configure'],
                    ['id' => 'password-policy', 'title' => 'Password Policy', 'description' => 'Define password strength requirements', 'required' => true, 'action_url' => '/admin/settings/security', 'action_text' => 'Configure'],
                    ['id' => 'user-roles', 'title' => 'User Roles', 'description' => 'Set up user roles and permissions', 'required' => false, 'action_url' => '/admin/users/roles', 'action_text' => 'Configure']
                ]
            ],
            [
                'id' => 'security-settings',
                'title' => 'Security Settings',
                'description' => 'Essential security configurations',
                'items' => [
                    ['id' => 'ssl-certificate', 'title' => 'SSL Certificate', 'description' => 'Ensure HTTPS is properly configured', 'required' => true, 'action_url' => '/admin/settings/security', 'action_text' => 'Check SSL'],
                    ['id' => 'backup-schedule', 'title' => 'Backup Schedule', 'description' => 'Configure automated backups', 'required' => true, 'action_url' => '/admin/settings/backup', 'action_text' => 'Configure'],
                    ['id' => 'security-headers', 'title' => 'Security Headers', 'description' => 'Configure security headers', 'required' => true, 'action_url' => '/admin/settings/security', 'action_text' => 'Configure'],
                    ['id' => 'rate-limiting', 'title' => 'Rate Limiting', 'description' => 'Set up API rate limiting', 'required' => false, 'action_url' => '/admin/settings/api', 'action_text' => 'Configure'],
                    ['id' => 'admin-2fa', 'title' => 'Admin 2FA', 'description' => 'Enable two-factor authentication for admins', 'required' => false, 'action_url' => '/admin/profile/security', 'action_text' => 'Enable']
                ]
            ],
            [
                'id' => 'application-settings',
                'title' => 'Application Settings',
                'description' => 'Core application functionality',
                'items' => [
                    ['id' => 'maintenance-mode', 'title' => 'Maintenance Mode', 'description' => 'Configure maintenance mode settings', 'required' => true, 'action_url' => '/admin/settings/maintenance', 'action_text' => 'Configure'],
                    ['id' => 'timezone', 'title' => 'Timezone', 'description' => 'Set the default timezone', 'required' => true, 'action_url' => '/admin/settings/general', 'action_text' => 'Configure'],
                    ['id' => 'default-language', 'title' => 'Default Language', 'description' => 'Set the site\'s default language', 'required' => true, 'action_url' => '/admin/settings/localization', 'action_text' => 'Configure'],
                    ['id' => 'caching', 'title' => 'Caching', 'description' => 'Configure caching for better performance', 'required' => false, 'action_url' => '/admin/settings/performance', 'action_text' => 'Configure']
                ]
            ],
            [
                'id' => 'integrations',
                'title' => 'Additional Integrations',
                'description' => 'Third-party services and integrations',
                'items' => [
                    ['id' => 'payment-gateway', 'title' => 'Payment Gateway Setup', 'description' => 'Configure payment processing', 'required' => false, 'action_url' => '/admin/settings/payments', 'action_text' => 'Configure'],
                    ['id' => 'email-service', 'title' => 'Email Service', 'description' => 'Set up email delivery service', 'required' => true, 'action_url' => '/admin/settings/email', 'action_text' => 'Configure'],
                    ['id' => 'analytics', 'title' => 'Analytics Tracking', 'description' => 'Set up Google Analytics or similar', 'required' => false, 'action_url' => '/admin/settings/analytics', 'action_text' => 'Configure'],
                    ['id' => 'cdn', 'title' => 'CDN Configuration', 'description' => 'Configure content delivery network', 'required' => false, 'action_url' => '/admin/settings/performance', 'action_text' => 'Configure'],
                    ['id' => 'api-keys', 'title' => 'API Keys', 'description' => 'Configure external API keys', 'required' => false, 'action_url' => '/admin/settings/api', 'action_text' => 'Configure']
                ]
            ]
        ];

        // Apply completion status and calculate section progress
        foreach ($sections as &$section) {
            $completedCount = 0;
            foreach ($section['items'] as &$item) {
                $item['completed'] = $savedStatus[$item['id']] ?? false;
                if ($item['completed']) $completedCount++;
            }
            $section['progress'] = count($section['items']) > 0 ? round(($completedCount / count($section['items'])) * 100) : 0;
            $section['status'] = $section['progress'] > 80 ? 'good' : ($section['progress'] > 40 ? 'warning' : 'needs-attention');
        }

        return $sections;
    }

    /**
     * Calculate overall progress percentage
     */
    private function calculateOverallProgress()
    {
        $sections = $this->getChecklistSections();
        $totalItems = 0;
        $completedItems = 0;

        foreach ($sections as $section) {
            foreach ($section['items'] as $item) {
                $totalItems++;
                if ($item['completed']) {
                    $completedItems++;
                }
            }
        }

        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
    }

    /**
     * Get quick actions for admin
     */
    private function getQuickActions()
    {
        return [
            [
                'title' => 'Configure SSL',
                'description' => 'Ensure your site is secure with HTTPS',
                'icon' => 'fas fa-shield-alt',
                'color' => '#ef4444',
                'url' => '/admin/settings/security',
                'priority' => 'high'
            ],
            [
                'title' => 'Setup Backups',
                'description' => 'Configure automated site backups',
                'icon' => 'fas fa-download',
                'color' => '#f59e0b',
                'url' => '/admin/settings/backup',
                'priority' => 'medium'
            ],
            [
                'title' => 'User Management',
                'description' => 'Review and manage user accounts',
                'icon' => 'fas fa-users',
                'color' => '#3b82f6',
                'url' => '/admin/users',
                'priority' => 'medium'
            ],
            [
                'title' => 'Performance Settings',
                'description' => 'Optimize site performance',
                'icon' => 'fas fa-tachometer-alt',
                'color' => '#10b981',
                'url' => '/admin/settings/performance',
                'priority' => 'low'
            ]
        ];
    }

    /**
     * Get recent admin activities
     */
    private function getRecentActivities()
    {
        return [
            [
                'action' => 'Logo settings updated',
                'user' => 'Super Admin',
                'timestamp' => '2 hours ago',
                'icon' => 'fas fa-image',
                'color' => '#3b82f6'
            ],
            [
                'action' => 'New user registered',
                'user' => 'System',
                'timestamp' => '4 hours ago',
                'icon' => 'fas fa-user-plus',
                'color' => '#10b981'
            ],
            [
                'action' => 'Security settings modified',
                'user' => 'Super Admin',
                'timestamp' => '1 day ago',
                'icon' => 'fas fa-shield-alt',
                'color' => '#f59e0b'
            ],
            [
                'action' => 'Database backup completed',
                'user' => 'System',
                'timestamp' => '2 days ago',
                'icon' => 'fas fa-database',
                'color' => '#6b7280'
            ]
        ];
    }

    /**
     * Get system status information
     */
    private function getSystemStatus()
    {
        return [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'disk_usage' => '45%',
            'memory_usage' => '67%',
            'uptime' => '15 days',
            'last_backup' => '2 days ago',
            'ssl_status' => 'Active',
            'maintenance_mode' => false
        ];
    }

    /**
     * Update checklist item (mock implementation)
     */
    private function updateChecklistItem($itemId, $completed)
    {
        $savedStatus = \App\Services\SettingsService::get('admin_setup_checklist', []);
        $savedStatus[$itemId] = (bool)$completed;
        
        return \App\Services\SettingsService::set('admin_setup_checklist', $savedStatus, 'json', 'admin', 'Store admin setup checklist completion states');
    }

    /**
     * Get current user information
     */
    protected function getUser()
    {
        // Support both new structure ($_SESSION['user']) and legacy session keys
        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            return $_SESSION['user'];
        } else if (!empty($_SESSION['user_id'])) {
            // Build user array from legacy session vars
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'role' => $_SESSION['role'] ?? 'user',
                'full_name' => $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'
            ];
        }
        return null;
    }
}
