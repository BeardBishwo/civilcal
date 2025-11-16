<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

/**
 * Admin Settings Controller
 * Comprehensive settings management system like ToolKing
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main settings dashboard
     */
    public function index()
    {
        $this->general();
    }

    /**
     * General Settings
     */
    public function general()
    {
        $this->setTitle('General Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'General Settings',
            'current_section' => 'general',
            'settings_sections' => $this->getSettingsSections(),
            'general_settings' => $this->getGeneralSettings()
        ];

        $this->view('admin/settings/general', $data);
    }

    /**
     * Application Settings
     */
    public function application()
    {
        $this->setTitle('Application Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'Application Settings',
            'current_section' => 'application',
            'settings_sections' => $this->getSettingsSections(),
            'app_settings' => $this->getApplicationSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * User Settings
     */
    public function users()
    {
        $this->setTitle('User Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'User Settings',
            'current_section' => 'users',
            'settings_sections' => $this->getSettingsSections(),
            'user_settings' => $this->getUserSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * Security Settings
     */
    public function security()
    {
        $this->setTitle('Security Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'Security Settings',
            'current_section' => 'security',
            'settings_sections' => $this->getSettingsSections(),
            'security_settings' => $this->getSecuritySettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * Email Settings
     */
    public function email()
    {
        $this->setTitle('Email Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'Email Settings',
            'current_section' => 'email',
            'settings_sections' => $this->getSettingsSections(),
            'email_settings' => $this->getEmailSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * API Settings
     */
    public function api()
    {
        $this->setTitle('API Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'API Settings',
            'current_section' => 'api',
            'settings_sections' => $this->getSettingsSections(),
            'api_settings' => $this->getApiSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * Performance Settings
     */
    public function performance()
    {
        $this->setTitle('Performance Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'Performance Settings',
            'current_section' => 'performance',
            'settings_sections' => $this->getSettingsSections(),
            'performance_settings' => $this->getPerformanceSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * Advanced Settings
     */
    public function advanced()
    {
        $this->setTitle('Advanced Settings - Admin Panel');
        $this->setCategory('admin');

        $data = [
            'page_title' => 'Advanced Settings',
            'current_section' => 'advanced',
            'settings_sections' => $this->getSettingsSections(),
            'advanced_settings' => $this->getAdvancedSettings()
        ];

        $this->view('admin/settings/index', $data);
    }

    /**
     * Update settings
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $section = $_POST['section'] ?? '';
        $settings = $_POST['settings'] ?? [];

        // In a real application, this would update the database
        $success = $this->updateSettings($section, $settings);

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Settings updated successfully' : 'Failed to update settings'
            ]);
        } else {
            // Redirect back with success message
            header('Location: /admin/settings/' . $section . '?updated=1');
        }
    }

    /**
     * Get settings sections for navigation
     */
    private function getSettingsSections()
    {
        return [
            [
                'id' => 'general',
                'name' => 'General Settings',
                'icon' => 'fas fa-cog',
                'description' => 'Basic site configuration',
                'url' => '/admin/settings/general'
            ],
            [
                'id' => 'application',
                'name' => 'Application Settings',
                'icon' => 'fas fa-desktop',
                'description' => 'App behavior and features',
                'url' => '/admin/settings/application'
            ],
            [
                'id' => 'users',
                'name' => 'User Settings',
                'icon' => 'fas fa-users',
                'description' => 'User management and registration',
                'url' => '/admin/settings/users'
            ],
            [
                'id' => 'security',
                'name' => 'Security Settings',
                'icon' => 'fas fa-shield-alt',
                'description' => 'Security and authentication',
                'url' => '/admin/settings/security'
            ],
            [
                'id' => 'email',
                'name' => 'Email Settings',
                'icon' => 'fas fa-envelope',
                'description' => 'Email delivery and templates',
                'url' => '/admin/settings/email'
            ],
            [
                'id' => 'api',
                'name' => 'API Settings',
                'icon' => 'fas fa-code',
                'description' => 'API keys and integrations',
                'url' => '/admin/settings/api'
            ],
            [
                'id' => 'performance',
                'name' => 'Performance',
                'icon' => 'fas fa-tachometer-alt',
                'description' => 'Caching and optimization',
                'url' => '/admin/settings/performance'
            ],
            [
                'id' => 'backup',
                'name' => 'Backup Settings',
                'icon' => 'fas fa-download',
                'description' => 'Automated backups',
                'url' => '/admin/settings/backup'
            ],
            [
                'id' => 'maintenance',
                'name' => 'Maintenance',
                'icon' => 'fas fa-tools',
                'description' => 'Maintenance mode settings',
                'url' => '/admin/settings/maintenance'
            ],
            [
                'id' => 'analytics',
                'name' => 'Analytics',
                'icon' => 'fas fa-chart-line',
                'description' => 'Tracking and analytics',
                'url' => '/admin/settings/analytics'
            ],
            [
                'id' => 'advanced',
                'name' => 'Advanced Settings',
                'icon' => 'fas fa-cogs',
                'description' => 'Technical and developer settings',
                'url' => '/admin/settings/advanced'
            ]
        ];
    }

    /**
     * Get general settings
     */
    private function getGeneralSettings()
    {
        return [
            'site_information' => [
                'title' => 'Site Information',
                'description' => 'Basic information about your site',
                'fields' => [
                    [
                        'name' => 'site_name',
                        'label' => 'Site Name',
                        'type' => 'text',
                        'value' => 'Engineering Calculator Pro',
                        'description' => 'The name of your site as it appears to users',
                        'required' => true
                    ],
                    [
                        'name' => 'site_description',
                        'label' => 'Site Description',
                        'type' => 'textarea',
                        'value' => 'Professional engineering calculation tools for modern construction',
                        'description' => 'A brief description of your site for SEO and social sharing',
                        'required' => true
                    ],
                    [
                        'name' => 'site_url',
                        'label' => 'Site URL',
                        'type' => 'url',
                        'value' => 'https://engicalc.com',
                        'description' => 'The primary URL of your site',
                        'required' => true
                    ],
                    [
                        'name' => 'admin_email',
                        'label' => 'Admin Email',
                        'type' => 'email',
                        'value' => 'admin@engicalc.com',
                        'description' => 'Email address for administrative notifications',
                        'required' => true
                    ]
                ]
            ],
            'regional_settings' => [
                'title' => 'Regional Settings',
                'description' => 'Localization and regional preferences',
                'fields' => [
                    [
                        'name' => 'timezone',
                        'label' => 'Timezone',
                        'type' => 'select',
                        'value' => 'Asia/Kathmandu',
                        'options' => [
                            'UTC' => 'UTC',
                            'America/New_York' => 'Eastern Time',
                            'America/Chicago' => 'Central Time',
                            'America/Denver' => 'Mountain Time',
                            'America/Los_Angeles' => 'Pacific Time',
                            'Europe/London' => 'London',
                            'Europe/Paris' => 'Paris',
                            'Asia/Kathmandu' => 'Nepal (UTC+5:45)',
                            'Asia/Tokyo' => 'Tokyo'
                        ],
                        'description' => 'Default timezone for the site',
                        'required' => true
                    ],
                    [
                        'name' => 'date_format',
                        'label' => 'Date Format',
                        'type' => 'select',
                        'value' => 'Y-m-d',
                        'options' => [
                            'Y-m-d' => '2025-11-14',
                            'm/d/Y' => '11/14/2025',
                            'd/m/Y' => '14/11/2025',
                            'F j, Y' => 'November 14, 2025'
                        ],
                        'description' => 'How dates are displayed throughout the site',
                        'required' => true
                    ],
                    [
                        'name' => 'default_language',
                        'label' => 'Default Language',
                        'type' => 'select',
                        'value' => 'en',
                        'options' => [
                            'en' => 'English',
                            'es' => 'Spanish',
                            'fr' => 'French',
                            'de' => 'German',
                            'ne' => 'Nepali'
                        ],
                        'description' => 'Default language for the site interface',
                        'required' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Get application settings
     */
    private function getApplicationSettings()
    {
        return [
            'home_page_redirect' => [
                'title' => 'Home Page Redirect',
                'description' => 'Control where users are redirected after login',
                'fields' => [
                    [
                        'name' => 'login_redirect',
                        'label' => 'Login Redirect',
                        'type' => 'select',
                        'value' => 'dashboard',
                        'options' => [
                            'dashboard' => 'User Dashboard',
                            'profile' => 'User Profile',
                            'home' => 'Home Page',
                            'last_page' => 'Last Visited Page'
                        ],
                        'description' => 'Where to redirect users after successful login'
                    ]
                ]
            ],
            'features' => [
                'title' => 'Feature Settings',
                'description' => 'Enable or disable application features',
                'fields' => [
                    [
                        'name' => 'enable_registration',
                        'label' => 'Enable Registration',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Allow new users to register accounts'
                    ],
                    [
                        'name' => 'enable_favorites',
                        'label' => 'Enable Favorites',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Allow users to save favorite calculators'
                    ],
                    [
                        'name' => 'enable_history',
                        'label' => 'Enable History',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Save calculation history for logged-in users'
                    ],
                    [
                        'name' => 'enable_api',
                        'label' => 'Enable API',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable REST API for developers'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get user settings
     */
    private function getUserSettings()
    {
        return [
            'registration' => [
                'title' => 'User Registration',
                'description' => 'Control how users can register',
                'fields' => [
                    [
                        'name' => 'require_email_verification',
                        'label' => 'Require Email Verification',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Users must verify their email before accessing the site'
                    ],
                    [
                        'name' => 'auto_approve_users',
                        'label' => 'Auto-approve Users',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Automatically approve new user registrations'
                    ]
                ]
            ],
            'password_policy' => [
                'title' => 'Password Policy',
                'description' => 'Set password requirements',
                'fields' => [
                    [
                        'name' => 'min_password_length',
                        'label' => 'Minimum Password Length',
                        'type' => 'number',
                        'value' => 8,
                        'description' => 'Minimum number of characters required',
                        'min' => 6,
                        'max' => 50
                    ],
                    [
                        'name' => 'require_special_chars',
                        'label' => 'Require Special Characters',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Passwords must contain special characters'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get security settings
     */
    private function getSecuritySettings()
    {
        return [
            'authentication' => [
                'title' => 'Authentication',
                'description' => 'Login and authentication settings',
                'fields' => [
                    [
                        'name' => 'session_timeout',
                        'label' => 'Session Timeout (minutes)',
                        'type' => 'number',
                        'value' => 1440,
                        'description' => 'How long users stay logged in',
                        'min' => 30,
                        'max' => 10080
                    ],
                    [
                        'name' => 'max_login_attempts',
                        'label' => 'Max Login Attempts',
                        'type' => 'number',
                        'value' => 5,
                        'description' => 'Maximum failed login attempts before lockout',
                        'min' => 3,
                        'max' => 20
                    ]
                ]
            ],
            'ssl_security' => [
                'title' => 'SSL & Security',
                'description' => 'HTTPS and security headers',
                'fields' => [
                    [
                        'name' => 'force_ssl',
                        'label' => 'Force SSL',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Redirect all traffic to HTTPS'
                    ],
                    [
                        'name' => 'security_headers',
                        'label' => 'Security Headers',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Add security headers to responses'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get email settings
     */
    private function getEmailSettings()
    {
        return [
            'smtp_settings' => [
                'title' => 'SMTP Configuration',
                'description' => 'Email delivery settings',
                'fields' => [
                    [
                        'name' => 'smtp_host',
                        'label' => 'SMTP Host',
                        'type' => 'text',
                        'value' => 'smtp.gmail.com',
                        'description' => 'SMTP server hostname'
                    ],
                    [
                        'name' => 'smtp_port',
                        'label' => 'SMTP Port',
                        'type' => 'number',
                        'value' => 587,
                        'description' => 'SMTP server port'
                    ],
                    [
                        'name' => 'smtp_username',
                        'label' => 'SMTP Username',
                        'type' => 'text',
                        'value' => '',
                        'description' => 'SMTP authentication username'
                    ],
                    [
                        'name' => 'smtp_password',
                        'label' => 'SMTP Password',
                        'type' => 'password',
                        'value' => '',
                        'description' => 'SMTP authentication password'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get API settings
     */
    private function getApiSettings()
    {
        return [
            'api_configuration' => [
                'title' => 'API Configuration',
                'description' => 'REST API settings and limits',
                'fields' => [
                    [
                        'name' => 'api_enabled',
                        'label' => 'Enable API',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable REST API endpoints'
                    ],
                    [
                        'name' => 'rate_limit',
                        'label' => 'Rate Limit (requests/hour)',
                        'type' => 'number',
                        'value' => 1000,
                        'description' => 'Maximum API requests per hour per user'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get performance settings
     */
    private function getPerformanceSettings()
    {
        return [
            'caching' => [
                'title' => 'Caching Settings',
                'description' => 'Improve site performance with caching',
                'fields' => [
                    [
                        'name' => 'enable_caching',
                        'label' => 'Enable Caching',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable page and data caching'
                    ],
                    [
                        'name' => 'cache_duration',
                        'label' => 'Cache Duration (hours)',
                        'type' => 'number',
                        'value' => 24,
                        'description' => 'How long to cache content'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get advanced settings
     */
    private function getAdvancedSettings()
    {
        return [
            'system_configuration' => [
                'title' => 'System Configuration',
                'description' => 'Core system settings and technical configuration',
                'fields' => [
                    [
                        'name' => 'debug_mode',
                        'label' => 'Debug Mode',
                        'type' => 'toggle',
                        'value' => false,
                        'description' => 'Enable debug mode for development and troubleshooting'
                    ],
                    [
                        'name' => 'error_reporting',
                        'label' => 'Error Reporting Level',
                        'type' => 'select',
                        'value' => 'production',
                        'options' => [
                            'production' => 'Production (Errors hidden)',
                            'development' => 'Development (Show all errors)',
                            'testing' => 'Testing (Log errors only)'
                        ],
                        'description' => 'Control how errors are displayed and logged'
                    ],
                    [
                        'name' => 'log_level',
                        'label' => 'Log Level',
                        'type' => 'select',
                        'value' => 'info',
                        'options' => [
                            'emergency' => 'Emergency',
                            'alert' => 'Alert',
                            'critical' => 'Critical',
                            'error' => 'Error',
                            'warning' => 'Warning',
                            'notice' => 'Notice',
                            'info' => 'Info',
                            'debug' => 'Debug'
                        ],
                        'description' => 'Minimum log level to record'
                    ],
                    [
                        'name' => 'max_execution_time',
                        'label' => 'Max Execution Time (seconds)',
                        'type' => 'number',
                        'value' => 300,
                        'min' => 30,
                        'max' => 3600,
                        'description' => 'Maximum time a script can run before timing out'
                    ]
                ]
            ],
            'database_settings' => [
                'title' => 'Database Settings',
                'description' => 'Database connection and optimization settings',
                'fields' => [
                    [
                        'name' => 'db_connection_pool',
                        'label' => 'Connection Pool Size',
                        'type' => 'number',
                        'value' => 10,
                        'min' => 1,
                        'max' => 100,
                        'description' => 'Maximum number of database connections'
                    ],
                    [
                        'name' => 'db_query_cache',
                        'label' => 'Query Cache',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable database query result caching'
                    ],
                    [
                        'name' => 'db_slow_query_log',
                        'label' => 'Slow Query Logging',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Log queries that take longer than threshold'
                    ],
                    [
                        'name' => 'db_slow_query_time',
                        'label' => 'Slow Query Threshold (seconds)',
                        'type' => 'number',
                        'value' => 2,
                        'min' => 1,
                        'max' => 60,
                        'description' => 'Time threshold for slow query logging'
                    ]
                ]
            ],
            'security_advanced' => [
                'title' => 'Advanced Security',
                'description' => 'Advanced security and protection settings',
                'fields' => [
                    [
                        'name' => 'csrf_protection',
                        'label' => 'CSRF Protection',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable Cross-Site Request Forgery protection'
                    ],
                    [
                        'name' => 'xss_protection',
                        'label' => 'XSS Protection',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable Cross-Site Scripting protection'
                    ],
                    [
                        'name' => 'content_security_policy',
                        'label' => 'Content Security Policy',
                        'type' => 'toggle',
                        'value' => false,
                        'description' => 'Enable Content Security Policy headers'
                    ],
                    [
                        'name' => 'ip_whitelist',
                        'label' => 'Admin IP Whitelist',
                        'type' => 'textarea',
                        'value' => '',
                        'description' => 'Comma-separated list of IP addresses allowed admin access'
                    ]
                ]
            ],
            'api_advanced' => [
                'title' => 'Advanced API Settings',
                'description' => 'Developer and API configuration options',
                'fields' => [
                    [
                        'name' => 'api_versioning',
                        'label' => 'API Versioning',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable API version management'
                    ],
                    [
                        'name' => 'api_documentation',
                        'label' => 'API Documentation',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Enable public API documentation'
                    ],
                    [
                        'name' => 'webhook_timeout',
                        'label' => 'Webhook Timeout (seconds)',
                        'type' => 'number',
                        'value' => 30,
                        'min' => 5,
                        'max' => 300,
                        'description' => 'Timeout for outgoing webhook requests'
                    ],
                    [
                        'name' => 'cors_origins',
                        'label' => 'CORS Allowed Origins',
                        'type' => 'textarea',
                        'value' => '*',
                        'description' => 'Comma-separated list of allowed CORS origins'
                    ]
                ]
            ],
            'monitoring' => [
                'title' => 'Monitoring & Analytics',
                'description' => 'System monitoring and analytics configuration',
                'fields' => [
                    [
                        'name' => 'performance_monitoring',
                        'label' => 'Performance Monitoring',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Monitor system performance metrics'
                    ],
                    [
                        'name' => 'user_analytics',
                        'label' => 'User Analytics',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Track user behavior and usage patterns'
                    ],
                    [
                        'name' => 'error_tracking',
                        'label' => 'Error Tracking',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Track and report application errors'
                    ],
                    [
                        'name' => 'metrics_retention',
                        'label' => 'Metrics Retention (days)',
                        'type' => 'number',
                        'value' => 90,
                        'min' => 7,
                        'max' => 365,
                        'description' => 'How long to keep performance metrics'
                    ]
                ]
            ],
            'developer_tools' => [
                'title' => 'Developer Tools',
                'description' => 'Tools and settings for developers',
                'fields' => [
                    [
                        'name' => 'query_profiler',
                        'label' => 'Query Profiler',
                        'type' => 'toggle',
                        'value' => false,
                        'description' => 'Enable database query profiling'
                    ],
                    [
                        'name' => 'template_cache',
                        'label' => 'Template Cache',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Cache compiled templates for better performance'
                    ],
                    [
                        'name' => 'minify_assets',
                        'label' => 'Minify Assets',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Minify CSS and JavaScript files'
                    ],
                    [
                        'name' => 'asset_versioning',
                        'label' => 'Asset Versioning',
                        'type' => 'toggle',
                        'value' => true,
                        'description' => 'Add version numbers to static assets for cache busting'
                    ]
                ]
            ]
        ];
    }

    /**
     * Update settings (mock implementation)
     */
    private function updateSettings($section, $settings)
    {
        // In a real application, this would update the database
        return true;
    }
}
