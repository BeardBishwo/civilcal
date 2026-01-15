<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\SettingsService;
use App\Services\GDPRService;
use App\Services\Security;
use App\Services\FileService;

class SettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        \App\Services\Security::startSession();
    }

    public function general()
    {
        $this->requireAdminWithBasicAuth();

        // Get all general settings
        $generalSettings = SettingsService::getAll('general');

        // Get logo and favicon settings (they might be in different groups)
        $siteLogo = SettingsService::get('site_logo');
        $favicon = SettingsService::get('favicon');

        // Merge all settings together
        $settings = array_merge($generalSettings, [
            'site_logo' => $siteLogo,
            'favicon' => $favicon
        ]);

        $this->view->render('admin/settings/general', [
            'title' => 'General Settings',
            'settings' => $settings
        ]);
    }

    public function application()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/application', [
            'title' => 'Application Settings'
        ]);
    }

    public function users()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/users', [
            'title' => 'User Settings'
        ]);
    }

    public function security()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('security');

        $this->view->render('admin/settings/security', [
            'title' => 'Security Settings',
            'settings' => $settings
        ]);
    }

    public function google()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('google');

        $this->view->render('admin/settings/google', [
            'title' => 'Google Login Settings',
            'settings' => $settings
        ]);
    }

    public function recaptcha()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('recaptcha');

        $this->view->render('admin/settings/recaptcha', [
            'title' => 'Recaptcha Settings',
            'settings' => $settings
        ]);
    }

    public function email()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('email');

        $this->view->render('admin/settings/email', [
            'title' => 'Email Settings',
            'settings' => $settings
        ]);
    }

    public function api()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/api', [
            'title' => 'API Settings'
        ]);
    }

    public function advanced()
    {
        $this->requireAdminWithBasicAuth();

        // Get advanced settings
        $settings = SettingsService::getAll('advanced');
        $perfSettings = SettingsService::getAll('performance');
        $secSettings = SettingsService::getAll('security');
        $systemSettings = SettingsService::getAll('system');
        $apiSettings = SettingsService::getAll('api');

        $advanced_settings = array_merge(
            $settings,
            $perfSettings,
            $secSettings,
            $systemSettings,
            $apiSettings
        );

        // System info
        $system_info = [
            'app_version' => '1.5.0',
            'php_version' => PHP_VERSION,
            'db_version' => 'MySQL 8.0',
            'memory_limit' => ini_get('memory_limit'),
            'server_os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'
        ];

        $this->view->render('admin/settings/advanced', [
            'title' => 'Advanced Settings',
            'advanced_settings' => $advanced_settings,
            'system_info' => $system_info
        ]);
    }

    public function payments()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('payments');

        $this->view->render('admin/settings/payments', [
            'title' => 'Payment Settings',
            'settings' => $settings
        ]);
    }

    private function ensureCsrfToken(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || strlen($_SESSION['csrf_token']) < 32) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function requireAdminWithBasicAuth()
    {
        $isAuthenticated = false;
        $isAdmin = false;

        // Check HTTP Basic Auth FIRST (for API testing)
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            // Authenticate using HTTP Basic Auth
            $userModel = new \App\Models\User();
            $user = $userModel->findByUsername($_SERVER['PHP_AUTH_USER']);
            if ($user) {
                $userArray = is_array($user) ? $user : (array) $user;
                if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                    $isAuthenticated = true;
                    $role = $userArray['role'] ?? 'user';
                    $isAdminRole = $userArray['is_admin'] ?? 0;
                    $isAdmin = ($isAdminRole == 1) || in_array($role, ['admin', 'super_admin']);

                    // Set session for subsequent requests
                    $_SESSION['user_id'] = $userArray['id'];
                    $_SESSION['username'] = $userArray['username'];
                    $_SESSION['user'] = $userArray;
                    $_SESSION['is_admin'] = $isAdmin;
                }
            }
        }

        // Fallback to session-based auth
        if (!$isAuthenticated && $this->auth->check()) {
            $isAuthenticated = true;
            $isAdmin = $this->auth->isAdmin();
        }

        if (!$isAuthenticated) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized - Please log in']);
            exit;
        }

        if (!$isAdmin) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden - Admin access required']);
            exit;
        }
    }

    public function index()
    {
        $this->requireAdminWithBasicAuth();

        // Get settings grouped by category
        $groups = ['general', 'appearance', 'email', 'security', 'privacy', 'performance', 'system', 'api', 'payments'];
        $settingsByGroup = [];

        foreach ($groups as $group) {
            $settingsByGroup[$group] = SettingsService::getAll($group);
        }

        // Use the original index view
        $this->view->render('admin/settings/index', [
            'title' => 'Settings Management',
            'settingsByGroup' => $settingsByGroup,
            'groups' => $groups
        ]);
    }

    public function save()
    {
        $this->requireAdminWithBasicAuth();

        // Set JSON header first to ensure proper response format
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        try {
            $updated = 0;
            $settingGroup = $_POST['setting_group'] ?? 'general';

            // Handle Checkboxes: If a checkbox is NOT in $_POST, it should be set to '0'
            $checkboxFields = $this->getCheckboxFields();
            foreach ($checkboxFields as $key) {
                if (!isset($_POST[$key])) {
                    if ($this->isFieldInGroup($key, $settingGroup)) {
                        if (SettingsService::set($key, '0', 'string', $settingGroup)) {
                            $updated++;
                        }
                    }
                }
            }

            // Exclude specific POST keys that are NOT settings
            $excludedKeys = ['csrf_token', 'gateway', 'submit', 'setting_group'];

            foreach ($_POST as $key => $value) {
                if (!in_array($key, $excludedKeys)) {
                    // Handle file uploads
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $value = $this->handleFileUpload($_FILES[$key], $key);
                    }

                    // Handle checkboxes (boolean values)
                    if ($value === 'on') {
                        $value = '1';
                    }

                    // Handle specific JSON fields
                    $type = 'string';
                    if ($key === 'social_links') {
                        $type = 'json';
                    }

                    if (SettingsService::set($key, $value, $type, $settingGroup)) {
                        $updated++;
                        // Log the change
                        GDPRService::logActivity(
                            $_SESSION['user_id'] ?? null,
                            'setting_updated',
                            'settings',
                            null,
                            "Setting $key updated (Group: $settingGroup)",
                            null,
                            json_encode(['key' => $key, 'value' => $value, 'group' => $settingGroup])
                        );
                    }
                }
            }

            // Handle logo and favicon file uploads specifically
            $fileFields = ['site_logo', 'favicon'];
            foreach ($fileFields as $field) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $uploadedPath = $this->handleFileUpload($_FILES[$field], $field);
                    if ($uploadedPath && SettingsService::set($field, $uploadedPath, 'string', $settingGroup)) {
                        $updated++;
                    }
                }

                // Handle removal of current images
                $removeField = 'remove_' . $field;
                if (isset($_POST[$removeField]) && $_POST[$removeField] == '1') {
                    if (SettingsService::set($field, '', 'string', $settingGroup)) {
                        $updated++;
                    }
                }
            }

            // Clear settings cache to ensure next read gets fresh data
            SettingsService::clearCache();

            echo json_encode([
                'success' => true,
                'message' => 'Settings updated successfully',
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function savePayments()
    {
        $this->requireAdminWithBasicAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        try {
            $updated = 0;
            $group = 'payments';
            $gateway = $_POST['gateway'] ?? null;

            // Handle Checkboxes first (those NOT in $_POST)
            $checkboxFields = $this->getCheckboxFields();
            foreach ($checkboxFields as $key) {
                if ($this->isFieldInGroup($key, $group) && !isset($_POST[$key])) {
                    if (SettingsService::set($key, '0', 'string', $group)) {
                        $updated++;
                    }
                }
            }

            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token' && $key !== 'gateway' && strpos($key, '_') !== false) {
                    if (SettingsService::set($key, $value, 'string', $group)) {
                        $updated++;
                    }
                }
            }

            // Handle Mutual Exclusivity
            if ($gateway === 'stripe' && isset($_POST['stripe_enabled']) && $_POST['stripe_enabled'] == '1') {
                SettingsService::set('paystack_enabled', '0', 'string', $group);
                SettingsService::set('paddle_billing_enabled', '0', 'string', $group);
                SettingsService::set('paddle_classic_enabled', '0', 'string', $group);
            } elseif ($gateway === 'paystack' && isset($_POST['paystack_enabled']) && $_POST['paystack_enabled'] == '1') {
                SettingsService::set('stripe_enabled', '0', 'string', $group);
            } elseif (($gateway === 'paddle_billing' && isset($_POST['paddle_billing_enabled']) && $_POST['paddle_billing_enabled'] == '1') ||
                ($gateway === 'paddle_classic' && isset($_POST['paddle_classic_enabled']) && $_POST['paddle_classic_enabled'] == '1')
            ) {
                SettingsService::set('stripe_enabled', '0', 'string', $group);
            }

            // Clear settings cache
            SettingsService::clearCache();

            echo json_encode([
                'success' => true,
                'message' => "Payment settings updated successfully"
            ]);

            // Log activity
            GDPRService::logActivity(
                $_SESSION['user_id'] ?? null,
                'setting_updated',
                'settings',
                null,
                "Payment settings updated" . ($gateway ? " for $gateway" : "")
            );
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function reset()
    {
        $this->requireAdminWithBasicAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            return $this->json(['success' => false, 'message' => 'Invalid CSRF token']);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $group = $input['group'] ?? null;

            // Reset settings to default values
            $db = \App\Core\Database::getInstance();

            if ($group) {
                $stmt = $db->prepare("
                    UPDATE settings 
                    SET setting_value = default_value 
                    WHERE setting_group = ? AND default_value IS NOT NULL
                ");
                $stmt->execute([$group]);
            } else {
                $stmt = $db->prepare("
                    UPDATE settings 
                    SET setting_value = default_value 
                    WHERE default_value IS NOT NULL
                ");
                $stmt->execute();
            }

            // Clear cache
            SettingsService::clearCache();

            // Log the action
            GDPRService::logActivity(
                $_SESSION['user_id'] ?? null,
                'settings_reset',
                'settings',
                null,
                "Settings reset for group: " . ($group ?? 'all')
            );

            return $this->json([
                'success' => true,
                'message' => 'Settings reset to defaults'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error resetting settings: ' . $e->getMessage()
            ]);
        }
    }

    public function export()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll();

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="settings-export-' . date('Y-m-d') . '.json"');

        echo json_encode($settings, JSON_PRETTY_PRINT);
        exit;
    }

    public function import()
    {
        $this->requireAdminWithBasicAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/settings');
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            return $this->json(['success' => false, 'message' => 'Invalid CSRF token']);
        }

        try {
            if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No file uploaded');
            }

            $content = file_get_contents($_FILES['import_file']['tmp_name']);
            $settings = json_decode($content, true);

            if (!$settings) {
                throw new \Exception('Invalid JSON file');
            }

            foreach ($settings as $key => $value) {
                SettingsService::set($key, $value);
            }

            return $this->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error importing settings: ' . $e->getMessage()
            ]);
        }
    }

    private function handleFileUpload($file, $key)
    {
        $type = 'logo'; // Default type

        // Detect type based on field key
        if (strpos($key, 'favicon') !== false) {
            $type = 'favicon';
        } elseif (strpos($key, 'banner') !== false || strpos($key, 'background') !== false) {
            $type = 'banner';
        }

        // Use the new FileService for Paranoid-Grade security
        $result = FileService::uploadAdminFile($file, $type);

        if (!$result['success']) {
            throw new \Exception($result['error']);
        }

        // Return the public URL for storage in settings
        return $result['url'] ?? $result['path'];
    }

    /**
     * Get all known checkbox fields across the application
     */
    private function getCheckboxFields()
    {
        return [
            // General/Auth
            'enable_registration',
            'require_email_verification',
            'enable_cookie_consent',
            'enable_analytics',
            'maintenance_mode',
            'enable_dark_mode',
            'require_strong_password',

            // Security
            'enable_2fa',
            'force_https',
            'ip_whitelist_enabled',
            'admin_ip_notification',
            'log_failed_logins',
            'log_admin_activity',
            'csrf_protection',
            'security_headers',
            'rate_limiting',
            'enable_location_alerts',
            'enable_suspicious_detection',
            'enable_ip_restrictions',
            'auto_block_failed_logins',

            // Email
            'smtp_enabled',

            // Advanced / Performance
            'cache_enabled',
            'enable_cache', // legacy variation
            'compression_enabled',
            'enable_compression', // legacy variation
            'enable_minification',
            'enable_lazy_loading',

            // Debug
            'debug_mode',
            'error_logging',
            'enable_error_logging', // legacy variation
            'query_debug',
            'performance_monitoring',

            // API
            'api_enabled',
            'enable_api', // legacy variation
            'require_api_key',
            'api_debug',
            'oauth_enabled',
            'cors_enabled',
            'webhook_enabled',

            // Payments
            'paypal_basic_enabled',
            'paypal_api_enabled',
            'paypal_sandbox_mode',
            'stripe_enabled',
            'mollie_enabled',
            'paddle_billing_enabled',
            'paddle_classic_enabled',
            'paystack_enabled',
            'bank_transfer_enabled',

            // Other
            'google_login_enabled',
            'captcha_on_login',
            'captcha_on_register',
            'enable_captcha',

            // User Settings
            'allow_registration',
            'email_verification',
            'auto_approve_users',
            'user_roles_enabled'
        ];
    }

    /**
     * Checks if a field key is in a specific settings group
     * This helps avoid setting '0' for checkboxes that aren't even on the current page
     */
    private function isFieldInGroup($key, $group)
    {
        $groups = [
            'general' => ['site_name', 'site_description', 'site_logo', 'favicon', 'contact_email', 'contact_address', 'contact_phone', 'default_language', 'default_timezone', 'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'enable_registration', 'require_email_verification', 'maintenance_mode', 'enable_dark_mode', 'play_store_url', 'app_store_url', 'social_links', 'report_reward_coins', 'report_reward_subsequent', 'report_notification_title', 'report_notification_first', 'report_notification_subsequent'],
            'email' => ['smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_email', 'from_name'],
            'security' => ['enable_2fa', 'force_https', 'password_min_length', 'password_complexity', 'session_timeout', 'max_login_attempts', 'ip_whitelist_enabled', 'ip_whitelist', 'admin_ip_notification', 'log_failed_logins', 'log_admin_activity', 'log_retention_days', 'csrf_protection', 'security_headers', 'rate_limiting'],
            'advanced' => ['custom_header_code', 'custom_footer_code', 'cache_enabled', 'compression_enabled', 'enable_minification', 'enable_lazy_loading', 'debug_mode', 'error_logging', 'query_debug', 'performance_monitoring', 'api_enabled', 'require_api_key'],
            'performance' => ['cache_enabled', 'compression_enabled', 'enable_minification', 'enable_lazy_loading'],
            'system' => ['debug_mode', 'error_logging', 'query_debug', 'performance_monitoring'],
            'api' => ['api_enabled', 'api_rate_limit', 'api_timeout', 'api_key_expiry', 'oauth_enabled', 'cors_enabled', 'cors_origins', 'webhook_enabled', 'webhook_timeout', 'api_documentation', 'api_version', 'api_debug', 'require_api_key'],
            'payments' => ['paypal_basic_enabled', 'paypal_email', 'paypal_api_enabled', 'paypal_client_id', 'paypal_client_secret', 'paypal_sandbox_mode', 'stripe_enabled', 'stripe_checkout_type', 'stripe_publishable_key', 'stripe_secret_key', 'stripe_webhook_secret', 'mollie_enabled', 'mollie_api_key', 'paddle_billing_enabled', 'paddle_client_token', 'paddle_api_key', 'paddle_webhook_secret', 'paddle_classic_enabled', 'paddle_vendor_id', 'paddle_classic_api_key', 'paddle_public_key', 'paddle_monthly_plan_id', 'paddle_yearly_plan_id', 'paystack_enabled', 'paystack_secret_key', 'paystack_public_key', 'bank_transfer_enabled', 'bank_info'],
            'google' => ['google_login_enabled', 'google_client_id', 'google_client_secret'],
            'recaptcha' => ['captcha_provider', 'recaptcha_site_key', 'recaptcha_secret_key', 'captcha_on_login', 'captcha_on_register', 'enable_captcha'],
            'user' => ['allow_registration', 'email_verification', 'auto_approve_users', 'password_min_length', 'password_complexity', 'session_timeout', 'max_login_attempts', 'lockout_duration', 'profile_visibility', 'user_roles_enabled']
        ];

        if (!isset($groups[$group])) {
            return false;
        }

        return in_array($key, $groups[$group]);
    }

    private function isCheckboxField($key)
    {
        return in_array($key, $this->getCheckboxFields());
    }

    /**
     * Get settings data (API endpoint)
     */
    public function getSettings()
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

            // Get settings grouped by category
            $groups = ['general', 'appearance', 'email', 'security', 'privacy', 'performance', 'system', 'api'];
            $settingsByGroup = [];

            foreach ($groups as $group) {
                $settingsByGroup[$group] = \App\Services\SettingsService::getAll($group);
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'settings' => $settingsByGroup,
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to get settings',
                'message' => $e->getMessage()
            ]);
        }
    }
    /**
     * Alias for save() method - some routes point to saveSettings
     */
    public function saveSettings()
    {
        return $this->save();
    }

    /**
     * Alias for save() method - support routes that call `update`
     */
    public function update()
    {
        return $this->save();
    }
    /**
     * Send a test email using current settings
     */
    public function sendTestEmail()
    {
        $this->requireAdminWithBasicAuth();

        // Set JSON header first
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        try {
            // Get test email from JSON body or POST data
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $testEmail = $input['test_email'] ?? '';

            if (empty($testEmail)) {
                echo json_encode(['success' => false, 'message' => 'Test email address is required']);
                return;
            }

            // Get current email settings
            $settings = SettingsService::getAll('email');

            // Get SMTP configuration
            $smtpHost = $settings['smtp_host'] ?? '';
            $smtpPort = $settings['smtp_port'] ?? 587;
            $smtpUsername = $settings['smtp_username'] ?? '';
            $smtpPassword = $settings['smtp_password'] ?? '';
            $smtpEncryption = $settings['smtp_encryption'] ?? 'tls';
            $fromEmail = $settings['from_email'] ?? $smtpUsername;
            $fromName = $settings['from_name'] ?? 'System';

            if (empty($smtpHost)) {
                throw new \Exception('SMTP Host is not configured');
            }

            if (empty($smtpUsername)) {
                throw new \Exception('SMTP Username is not configured');
            }

            if (empty($smtpPassword)) {
                throw new \Exception('SMTP Password is not configured');
            }

            // Try to send test email using PHPMailer
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->Port = $smtpPort;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUsername;
                $mail->Password = $smtpPassword;

                // Set encryption
                if ($smtpEncryption === 'ssl') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($smtpEncryption === 'tls') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                }

                // Disable SSL certificate verification (for testing - use with caution)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                // Recipients
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($testEmail);

                // Content
                $mail->isHTML(true);
                $mail->Subject = '🧪 SMTP Test Email from ' . ($_SESSION['user']['username'] ?? 'Admin');
                $mail->Body = '
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; background: #f8f9fa; padding: 20px; border-radius: 8px; }
                            .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                            .content { background: white; padding: 20px; }
                            .footer { background: #e9ecef; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
                            .success { color: #10b981; font-weight: bold; }
                            .info { background: #f0f4ff; border-left: 4px solid #667eea; padding: 15px; margin: 10px 0; border-radius: 4px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h1>✅ SMTP Configuration Test</h1>
                                <p>Email delivery is working correctly!</p>
                            </div>
                            <div class="content">
                                <p class="success">🎉 Congratulations! Your SMTP settings are configured correctly.</p>
                                <p>This is a test email to verify your email configuration.</p>
                                
                                <div class="info">
                                    <strong>📧 Email Details:</strong><br>
                                    <strong>From:</strong> ' . htmlspecialchars($fromName) . ' &lt;' . htmlspecialchars($fromEmail) . '&gt;<br>
                                    <strong>To:</strong> ' . htmlspecialchars($testEmail) . '<br>
                                    <strong>SMTP Host:</strong> ' . htmlspecialchars($smtpHost) . ':' . htmlspecialchars($smtpPort) . '<br>
                                    <strong>Encryption:</strong> ' . strtoupper($smtpEncryption) . '<br>
                                    <strong>Sent At:</strong> ' . date('Y-m-d H:i:s') . '
                                </div>
                                
                                <p>You can now use this configuration to send emails from your application.</p>
                            </div>
                            <div class="footer">
                                <p>This email was sent from your Bishwo Calculator Admin Panel</p>
                                <p>&copy; ' . date('Y') . ' All Rights Reserved</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ';
                $mail->AltBody = 'Your SMTP settings are configured correctly!';

                $mail->send();

                // Log successful test
                GDPRService::logActivity(
                    $_SESSION['user_id'] ?? null,
                    'test_email_sent',
                    'system',
                    null,
                    "✅ Test email sent successfully to $testEmail using SMTP host $smtpHost:$smtpPort",
                    null,
                    [
                        'smtp_host' => $smtpHost,
                        'smtp_port' => $smtpPort,
                        'recipient' => $testEmail
                    ]
                );

                echo json_encode([
                    'success' => true,
                    'message' => "✅ Test email sent successfully to $testEmail! Check your inbox."
                ]);
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                throw new \Exception("SMTP Error: " . $e->errorMessage());
            }
        } catch (\Exception $e) {
            // Log failed test
            GDPRService::logActivity(
                $_SESSION['user_id'] ?? null,
                'test_email_failed',
                'system',
                null,
                "❌ Test email failed: " . $e->getMessage(),
                null,
                ['error' => $e->getMessage()]
            );

            echo json_encode([
                'success' => false,
                'message' => '❌ Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }
    public function saveAdvanced()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
        }

        // Verify CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            exit(json_encode(['success' => false, 'message' => 'Invalid CSRF token']));
        }

        $settings = $_POST;
        $updated = 0;
        $errors = 0;
        $settingGroup = 'advanced'; // Default group for advanced settings

        // Map settings to their specific groups
        $groupMap = [
            // Performance
            'cache_enabled' => 'performance',
            'cache_ttl' => 'performance',
            'cache_driver' => 'performance',
            'compression_enabled' => 'performance',
            'session_lifetime' => 'performance',
            'max_concurrent_users' => 'performance',

            // Security
            'force_https' => 'security',
            'security_headers' => 'security',
            'rate_limiting' => 'security',
            'rate_limit_requests' => 'security',
            'login_attempts' => 'security',
            'csrf_protection' => 'security',

            // Debug/System
            'debug_mode' => 'system',
            'error_logging' => 'system',
            'query_debug' => 'system',
            'log_level' => 'system',
            'performance_monitoring' => 'system',

            // API
            'api_enabled' => 'api',
            'api_key' => 'api',
            'api_rate_limit' => 'api',
            'api_timeout' => 'api',
            'cors_origins' => 'api',

            // Custom Code
            'custom_header_code' => 'advanced',
            'custom_footer_code' => 'advanced'
        ];

        // Handle Checkboxes first (those NOT in $_POST)
        foreach ($groupMap as $key => $targetGroup) {
            if ($this->isCheckboxField($key) && !isset($settings[$key])) {
                if (\App\Services\SettingsService::set($key, '0', 'string', $targetGroup)) {
                    $updated++;
                }
            }
        }

        // Process settings in POST
        foreach ($settings as $key => $value) {
            if (isset($groupMap[$key])) {
                $targetGroup = $groupMap[$key];

                if (\App\Services\SettingsService::set($key, $value, 'string', $targetGroup)) {
                    $updated++;
                } else {
                    $errors++;
                }
            }
        }

        // Clear settings cache
        \App\Services\SettingsService::clearCache();

        // Log activity
        if ($updated > 0) {
            \App\Services\GDPRService::logActivity(
                $_SESSION['user_id'] ?? null,
                'settings_updated',
                'settings',
                null,
                "Advanced settings updated ($updated settings changed)"
            );
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $updated > 0 || $errors === 0,
            'message' => "Settings saved successfully. Updated: $updated",
            'updated' => $updated
        ]);
        exit;
    }

    /**
     * Permalink Settings Page - Using Proper Admin Layout
     */
    public function permalinks()
    {
        $this->requireAdminWithBasicAuth();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_permalinks'])) {
            $structure = $_POST['permalink_structure'] ?? 'calculator-only';
            $phpExtension = isset($_POST['permalink_php_extension']) ? 1 : 0;
            $basePath = $_POST['permalink_base_path'] ?? 'tools';
            $customPattern = $_POST['permalink_custom_pattern'] ?? '';
            $redirectOldUrls = isset($_POST['permalink_redirect_old_urls']) ? 1 : 0;

            // Validate custom pattern
            if ($structure === 'custom' && empty($customPattern)) {
                $message = 'Custom pattern cannot be empty';
                $messageType = 'error';
            } else {
                // Save settings
                SettingsService::set('permalink_structure', $structure, 'string', 'seo');
                SettingsService::set('permalink_php_extension', $phpExtension, 'boolean', 'seo');
                SettingsService::set('permalink_base_path', $basePath, 'string', 'seo');
                SettingsService::set('permalink_custom_pattern', $customPattern, 'string', 'seo');
                SettingsService::set('permalink_redirect_old_urls', $redirectOldUrls, 'boolean', 'seo');

                // Clear URL cache to ensure next read gets fresh data
                \App\Helpers\UrlHelper::clearCache();

                $message = 'Permalink settings updated successfully! All links will now use the new format.';
                $messageType = 'success';

                // Log the change
                \App\Services\GDPRService::logActivity(
                    $_SESSION['user_id'] ?? null,
                    'permalink_settings_updated',
                    'settings',
                    null,
                    "Permalink settings updated: structure=$structure, php_extension=$phpExtension, base_path=$basePath",
                    null,
                    json_encode([
                        'structure' => $structure,
                        'php_extension' => $phpExtension,
                        'base_path' => $basePath,
                        'custom_pattern' => $customPattern,
                        'redirect_old_urls' => $redirectOldUrls
                    ])
                );
            }
        }

        // Get current permalink structure
        $currentStructure = SettingsService::get('permalink_structure', 'calculator-only');

        // Get permalink settings
        $settings = [
            'permalink_base_path' => SettingsService::get('permalink_base_path', 'tools'),
            'permalink_php_extension' => SettingsService::get('permalink_php_extension', false),
            'permalink_custom_pattern' => SettingsService::get('permalink_custom_pattern', ''),
            'permalink_redirect_old_urls' => SettingsService::get('permalink_redirect_old_urls', true)
        ];

        // Get sample calculator for preview
        $db = \App\Core\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT calculator_id, category, subcategory, slug FROM calculator_urls LIMIT 1");
        $stmt->execute();
        $sampleCalculator = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Use the proper admin layout system
        $this->view->render('admin/settings/permalinks', [
            'title' => 'Permalink Settings',
            'currentStructure' => $currentStructure,
            'settings' => $settings,
            'sampleCalculator' => $sampleCalculator,
            'message' => $message ?? '',
            'messageType' => $messageType ?? ''
        ]);
    }

    /**
     * Economy Settings Page
     */
    public function economy()
    {
        $this->requireAdminWithBasicAuth();

        $resources = SettingsService::get('economy_resources', []);
        $ranks = SettingsService::get('economy_ranks', []);
        $hudConfig = SettingsService::get('economy_hud_config', []);
        $bundles = SettingsService::get('economy_bundles', []);
        $cashPacks = SettingsService::get('economy_cash_packs', []);

        $this->view->render('admin/settings/economy', [
            'title' => 'Gamenta Economy Settings',
            'resources' => $resources,
            'ranks' => $ranks,
            'hudConfig' => $hudConfig,
            'bundles' => $bundles,
            'cashPacks' => $cashPacks
        ]);
    }

    /**
     * Save Economy Settings
     */
    public function saveEconomy()
    {
        $this->requireAdminWithBasicAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        try {
            $type = $_POST['type'] ?? '';

            if ($type === 'resources') {
                $resources = $_POST['resources'] ?? [];
                SettingsService::set('economy_resources', $resources, 'json', 'economy');
            } elseif ($type === 'ranks') {
                $ranks = $_POST['ranks'] ?? [];
                SettingsService::set('economy_ranks', $ranks, 'json', 'economy');
            } elseif ($type === 'hud') {
                $hud = $_POST['hud'] ?? [];
                SettingsService::set('economy_hud_config', $hud, 'json', 'economy');
            } elseif ($type === 'bundles') {
                $bundles = $_POST['bundles'] ?? [];
                SettingsService::set('economy_bundles', $bundles, 'json', 'economy');
            } elseif ($type === 'cash_packs') {
                $cashPacks = $_POST['cash_packs'] ?? [];
                SettingsService::set('economy_cash_packs', $cashPacks, 'json', 'economy');
            }

            SettingsService::clearCache();

            echo json_encode([
                'success' => true,
                'message' => 'Economy settings updated successfully'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Apply Civil City Rank Standards
     */
    public function resetRanks()
    {
        $this->requireAdminWithBasicAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        // CSRF Token validation
        if (!\App\Services\Security::validateCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        try {
            // "Civil City" 27-Rank Tier System (7 Primary Icons, Rest Empty)
            $ranks = [
                // League 1: The Rookies
                ['level' => 1, 'name' => 'Intern', 'min' => 0, 'icon' => 'themes/default/assets/resources/ranks/rank_01_intern.webp', 'reward' => 'Base Avatar'],
                ['level' => 2, 'name' => 'Apprentice', 'min' => 150, 'icon' => '', 'reward' => '+50 Coins'],
                ['level' => 3, 'name' => 'Trainee', 'min' => 300, 'icon' => '', 'reward' => '+50 Coins'],

                // League 2: The Field Work
                ['level' => 4, 'name' => 'Surveyor', 'min' => 500, 'icon' => 'themes/default/assets/resources/ranks/rank_02_surveyor.webp', 'reward' => 'Unlock: Daily Challenges'],
                ['level' => 5, 'name' => 'Chainman', 'min' => 800, 'icon' => '', 'reward' => '+100 Coins'],
                ['level' => 6, 'name' => 'Leveling Staff', 'min' => 1200, 'icon' => '', 'reward' => '+100 Coins'],
                ['level' => 7, 'name' => 'Lab Technician', 'min' => 1400, 'icon' => '', 'reward' => 'Unlock: Lab Coat'],
                ['level' => 8, 'name' => 'Draftsman', 'min' => 1600, 'icon' => '', 'reward' => 'Unlock: Pencil Badge'],

                // League 3: The Site Management
                ['level' => 9, 'name' => 'Site Supervisor', 'min' => 2000, 'icon' => 'themes/default/assets/resources/ranks/rank_03_supervisor.webp', 'reward' => 'Unlock: Supervisor Avatar'],
                ['level' => 10, 'name' => 'Senior Foreman', 'min' => 2800, 'icon' => '', 'reward' => '+200 Coins'],
                ['level' => 11, 'name' => 'Safety Officer', 'min' => 3600, 'icon' => '', 'reward' => 'Unlock: Safety First Sticker'],
                ['level' => 12, 'name' => 'Site Inspector', 'min' => 4400, 'icon' => '', 'reward' => '+250 Coins'],

                // League 4: The Office Engineers
                ['level' => 13, 'name' => 'Assistant Engineer', 'min' => 5000, 'icon' => 'themes/default/assets/resources/ranks/rank_04_assistant.webp', 'reward' => 'Unlock: The Vault'],
                ['level' => 14, 'name' => 'QC Engineer', 'min' => 6200, 'icon' => '', 'reward' => 'Unlock: Quality Badge'],
                ['level' => 15, 'name' => 'Billing Engineer', 'min' => 7500, 'icon' => '', 'reward' => '+500 Coins'],
                ['level' => 16, 'name' => 'Quantity Surveyor', 'min' => 10000, 'icon' => '', 'reward' => 'Unlock: Dark Theme'],
                ['level' => 17, 'name' => 'CAD Architect', 'min' => 12500, 'icon' => '', 'reward' => '+750 Coins'],

                // League 5: The Experts
                ['level' => 18, 'name' => 'Senior Engineer', 'min' => 15000, 'icon' => 'themes/default/assets/resources/ranks/rank_05_senior.webp', 'reward' => 'Unlock: Senior Avatar'],
                ['level' => 19, 'name' => 'Structural Designer', 'min' => 22000, 'icon' => '', 'reward' => '+1,000 Coins'],
                ['level' => 20, 'name' => 'Environmental Eng.', 'min' => 26000, 'icon' => '', 'reward' => 'Unlock: Green Leaf Badge'],
                ['level' => 21, 'name' => 'Geotech Specialist', 'min' => 30000, 'icon' => '', 'reward' => 'Unlock: Earth Frame'],
                ['level' => 22, 'name' => 'Resident Engineer', 'min' => 40000, 'icon' => '', 'reward' => '+2,000 Coins'],

                // League 6: The Bosses
                ['level' => 23, 'name' => 'Project Manager', 'min' => 50000, 'icon' => 'themes/default/assets/resources/ranks/rank_06_manager.webp', 'reward' => 'Unlock: Manager Avatar'],
                ['level' => 24, 'name' => 'Urban Planner', 'min' => 65000, 'icon' => '', 'reward' => '+3,000 Coins'],
                ['level' => 25, 'name' => 'Operations Director', 'min' => 75000, 'icon' => '', 'reward' => '+5,000 Coins'],
                ['level' => 26, 'name' => 'Contract Manager', 'min' => 90000, 'icon' => '', 'reward' => 'Unlock: Gold Pen Effect'],

                // League 7: The Legends
                ['level' => 27, 'name' => 'Chief Engineer', 'min' => 100000, 'icon' => 'themes/default/assets/resources/ranks/rank_07_chief.webp', 'reward' => 'Unlock: Chief Avatar']
            ];

            SettingsService::set('economy_ranks', $ranks, 'json', 'economy');
            SettingsService::clearCache();

            echo json_encode([
                'success' => true,
                'message' => 'Applied Civil City Rank Standards (27 Tiers)'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function quiz()
    {
        $this->requireAdminWithBasicAuth();

        $settings = SettingsService::getAll('quiz');

        $this->view->render('admin/quiz/settings', [
            'title' => 'Quiz Module Settings',
            'settings' => $settings
        ]);
    }

    /**
     * Quiz Modes Settings - Toggle quiz features on/off
     */
    public function quizModes()
    {
        $this->requireAdminWithBasicAuth();

        // Handle POST (AJAX save)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            // CSRF validation
            if (!\App\Services\Security::validateCsrfToken()) {
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit;
            }

            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $modes = $input['modes'] ?? [];

                $updated = 0;
                foreach ($modes as $key => $value) {
                    // Ensure it's a quiz_mode_* setting
                    if (strpos($key, 'quiz_mode_') === 0) {
                        $settingValue = $value ? '1' : '0';
                        if (\App\Services\SettingsService::set($key, $settingValue, 'string', 'quiz_modes')) {
                            $updated++;
                        }
                    }
                }

                // Clear cache
                \App\Services\SettingsService::clearCache();

                // Log activity
                \App\Services\GDPRService::logActivity(
                    $_SESSION['user_id'] ?? null,
                    'quiz_modes_updated',
                    'settings',
                    null,
                    "Quiz modes updated ($updated changes)"
                );

                echo json_encode([
                    'success' => true,
                    'message' => 'Quiz modes updated successfully',
                    'updated' => $updated
                ]);
            } catch (\Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            exit;
        }

        // Handle GET (display page)
        $db = \App\Core\Database::getInstance();
        $pdo = $db->getPdo();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_group = 'quiz_modes'");
        $settings = $stmt->fetchAll();

        // Convert to associative array
        $modes = [];
        foreach ($settings as $setting) {
            $modes[$setting['setting_key']] = $setting['setting_value'] === '1';
        }

        $this->view->render('admin/settings/quiz_modes', [
            'title' => 'Quiz Modes Settings',
            'modes' => $modes
        ]);
    }
}
