<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\SettingsService;
use App\Services\GDPRService;

class SettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureCsrfToken();
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

    public function performance()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/performance', [
            'title' => 'Performance Settings'
        ]);
    }

    public function advanced()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/advanced', [
            'title' => 'Advanced Settings'
        ]);
    }

    public function backup()
    {
        $this->requireAdminWithBasicAuth();

        $this->view->render('admin/settings/backup', [
            'title' => 'Backup Settings'
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
        $groups = ['general', 'appearance', 'email', 'security', 'privacy', 'performance', 'system', 'api'];
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

        // Enhanced debugging - write to server error log
        error_log("[SETTINGS_DEBUG] save() method called at " . date('Y-m-d H:i:s'));
        error_log("[SETTINGS_DEBUG] REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("[SETTINGS_DEBUG] REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        error_log("[SETTINGS_DEBUG] User: " . ($_SESSION['user']['username'] ?? 'unknown'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("[SETTINGS_DEBUG] ERROR: Invalid request method");
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }



        // CSRF Token validation
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (empty($csrfToken) || empty($sessionToken) || !hash_equals($sessionToken, $csrfToken)) {

            error_log("[SETTINGS_DEBUG] ERROR: Invalid CSRF token");
            error_log("[SETTINGS_DEBUG] Submitted CSRF: $csrfToken");
            error_log("[SETTINGS_DEBUG] Session CSRF: $sessionToken");
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        try {
            // Log raw POST data for debugging
            error_log("[SETTINGS_DEBUG] Raw POST data: " . json_encode($_POST));
            error_log("[SETTINGS_DEBUG] FILES data: " . json_encode($_FILES));
            $updated = 0;

            // DEBUG: Log what we receive to a specific file
            $logFile = __DIR__ . '/../../../debug_save.log';
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "SettingsController::save() - POST keys: " . implode(', ', array_keys($_POST)) . "\n", FILE_APPEND);
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "SettingsController::save() - POST data: " . json_encode($_POST) . "\n", FILE_APPEND);

            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token' && strpos($key, '_') !== false) {
                    error_log("[SETTINGS_DEBUG] Processing setting: $key = $value");

                    // Handle file uploads for image/file type settings
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        error_log("[SETTINGS_DEBUG] Handling file upload for: $key");
                        $value = $this->handleFileUpload($_FILES[$key]);
                        error_log("[SETTINGS_DEBUG] File upload result for $key: " . ($value ?: 'FAILED'));
                    }

                    // Handle checkboxes (boolean values)
                    if (!isset($_POST[$key]) && $this->isCheckboxField($key)) {
                        $value = '0';
                        error_log("[SETTINGS_DEBUG] Checkbox field $key set to 0 (unchecked)");
                    }

                    try {
                        $setResult = SettingsService::set($key, $value);
                        error_log("[SETTINGS_DEBUG] SettingsService::set($key, $value) result: " . ($setResult ? 'SUCCESS' : 'FAILED'));

                        if ($setResult) {
                            $updated++;
                            // Log the change
                            GDPRService::logActivity(
                                $_SESSION['user_id'] ?? null,
                                'setting_updated',
                                'settings',
                                null,
                                "Setting $key updated",
                                json_encode(['old_value' => SettingsService::get($key)]),
                                json_encode(['key' => $key, 'value' => $value])
                            );
                        } else {
                            error_log("[SETTINGS_DEBUG] SettingsService::set() returned false for key: $key");
                        }
                    } catch (\Exception $e) {
                        error_log("[SETTINGS_DEBUG] Exception in SettingsService::set() for key $key: " . $e->getMessage());
                    }
                }
            }

            // Handle logo and favicon file uploads specifically
            $fileFields = ['site_logo', 'favicon'];
            foreach ($fileFields as $field) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $uploadedPath = $this->handleFileUpload($_FILES[$field]);
                    if ($uploadedPath && SettingsService::set($field, $uploadedPath)) {
                        $updated++;

                        // Log the change
                        GDPRService::logActivity(
                            $_SESSION['user_id'] ?? null,
                            'setting_updated',
                            'settings',
                            null,
                            "Setting $field updated via file upload",
                            json_encode(['old_value' => SettingsService::get($field)]),
                            json_encode(['key' => $field, 'value' => $uploadedPath])
                        );
                    }
                }

                // Handle removal of current images
                $removeField = 'remove_' . $field;
                if (isset($_POST[$removeField]) && $_POST[$removeField] == '1') {
                    // Remove the current image
                    $currentValue = SettingsService::get($field, '');
                    if ($currentValue && SettingsService::set($field, '')) {
                        $updated++;

                        // Log the removal
                        GDPRService::logActivity(
                            $_SESSION['user_id'] ?? null,
                            'setting_updated',
                            'settings',
                            null,
                            "Setting $field removed (cleared)",
                            json_encode(['old_value' => $currentValue, 'new_value' => '']),
                            json_encode(['key' => $field])
                        );
                    }
                }
            }

            if ($updated > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "$updated settings updated successfully"
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => "No changes were made to the settings"
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
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

    private function handleFileUpload($file)
    {
        $uploadDir = __DIR__ . '/../../../public/uploads/settings/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = time() . '_' . basename($file['name']);
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Return the correct URL path for web access
            return '/uploads/settings/' . $filename;
        }

        return null;
    }

    private function isCheckboxField($key)
    {
        $checkboxFields = [
            'enable_registration',
            'require_email_verification',
            'enable_2fa',
            'enable_captcha',
            'enable_cookie_consent',
            'enable_analytics',
            'enable_cache',
            'enable_minification',
            'enable_compression',
            'enable_lazy_loading',
            'maintenance_mode',
            'debug_mode',
            'enable_error_logging',
            'enable_api',
            'require_api_key',
            'enable_dark_mode',
            'smtp_enabled',
            'require_strong_password',
            'force_https',
            'ip_whitelist_enabled',
            'admin_ip_notification',
            'log_failed_logins',
            'log_admin_activity'
        ];

        return in_array($key, $checkboxFields);
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
}
