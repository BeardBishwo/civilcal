<?php
namespace App\Services;

/**
 * Enhanced Email System for EngiCal Pro
 * Supports both PHPMailer and PHP mail() with admin-configurable settings
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Core\Database;

class EmailManager
{
    private $settings;
    private $mailer;
    private $usePHPMailer = true;

    public function __construct()
    {
        $this->loadSettings();
        $this->initializeMailer();
    }

    /**
     * Load email settings from configuration or database
     */
    /**
     * Load email settings from database with .env fallback
     */
    private function loadSettings()
    {
        // 1. Try to load from Database (SettingsService) via 'site_settings' table pattern used in this class
        // Note: Existing code used direct query to 'site_settings', but SettingsService uses 'settings' table.
        // We will stick to the existing class logic which queries 'site_settings' directly for 'email_%' keys
        // as per the existing codebase style, but we will make it robust.
        
        $dbSettings = [];
        try {
            $db = Database::getInstance();
            // Check site_settings table (legacy/current implementation)
            $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'email_%'");
            if ($stmt) {
                while ($row = $stmt->fetch()) {
                    $key = str_replace('email_', '', $row['setting_key']);
                    $dbSettings[$key] = $row['setting_value'];
                }
            }
        } catch (Exception $e) {
            error_log('Database email settings check failed: ' . $e->getMessage());
        }

        // 2. Define defaults using .env as fallback (Soft Coding)
        $this->settings = [
            'use_phpmailer' => $dbSettings['use_phpmailer'] ?? filter_var(getenv('USE_PHPMAILER') ?: true, FILTER_VALIDATE_BOOLEAN),
            
            // Prioritize Database -> Then ENV -> Then empty
            'smtp_host' => $dbSettings['smtp_host'] ?? getenv('SMTP_HOST') ?: '',
            'smtp_port' => $dbSettings['smtp_port'] ?? getenv('SMTP_PORT') ?: 587,
            'smtp_username' => $dbSettings['smtp_username'] ?? getenv('SMTP_USER') ?: '',
            'smtp_password' => $dbSettings['smtp_password'] ?? getenv('SMTP_PASS') ?: '',
            'smtp_encryption' => $dbSettings['smtp_encryption'] ?? getenv('SMTP_ENCRYPTION') ?: 'tls',
            
            'from_email' => $dbSettings['from_email'] ?? getenv('MAIL_FROM_ADDRESS') ?: 'noreply@example.com',
            'from_name' => $dbSettings['from_name'] ?? getenv('MAIL_FROM_NAME') ?? \App\Services\SettingsService::get('site_name', 'Bishwo Calculator'),
            'reply_to' => $dbSettings['reply_to'] ?? getenv('MAIL_REPLY_TO') ?: 'support@example.com'
        ];

        // Check if PHPMailer should be used
        $this->usePHPMailer = ($this->settings['use_phpmailer'] ?? true) && !empty($this->settings['smtp_host']);
    }

    /**
     * Initialize PHPMailer or prepare for PHP mail()
     */
    private function initializeMailer()
    {
        if (!$this->usePHPMailer) {
            return;
        }

        try {
            $this->mailer = new PHPMailer(true);

            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->settings['smtp_host'];
            $this->mailer->Port = (int)$this->settings['smtp_port'];

            if (!empty($this->settings['smtp_username'])) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $this->settings['smtp_username'];
                $this->mailer->Password = $this->settings['smtp_password'];
            }

            // Handle encryption properly
            $encryption = $this->settings['smtp_encryption'] ?? 'tls';
            if ($encryption === 'ssl') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $this->mailer->SMTPSecure = '';
                $this->mailer->SMTPAutoTLS = false;
            }

            $this->mailer->SMTPDebug = 0; // Set to 2 for debugging

            // Default sender
            $this->mailer->setFrom($this->settings['from_email'], $this->settings['from_name']);
            $this->mailer->addReplyTo($this->settings['reply_to'], $this->settings['from_name']);
        } catch (Exception $e) {
            error_log('PHPMailer initialization failed: ' . $e->getMessage());
            $this->usePHPMailer = false;
        }
    }

    /**
     * Send email verification message
     */
    public function sendVerificationEmail($email, $fullName, $verificationToken)
    {
        $verificationLink = app_base_url("verify.php?token={$verificationToken}");

        $subject = "Verify Your Email - " . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');

        $body = $this->getTemplate('email_verification', [
            'full_name' => $fullName,
            'verification_link' => $verificationLink,
            'site_name' => \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro')
        ]);

        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($email, $fullName, $resetToken)
    {
        $resetLink = app_base_url("reset.php?token={$resetToken}");

        $subject = "Password Reset - " . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');

        $body = $this->getTemplate('password_reset', [
            'full_name' => $fullName,
            'reset_link' => $resetLink,
            'site_name' => \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro')
        ]);

        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send new account credentials email
     */
    public function sendNewAccountEmail($email, $fullName, $username, $password, $loginUrl)
    {
        $subject = "Your New Account Credentials - " . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');

        $body = $this->getTemplate('new_account', [
            'full_name' => $fullName,
            'username' => $username,
            'password' => $password,
            'login_url' => $loginUrl,
            'site_name' => \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro')
        ]);

        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($email, $fullName, $username)
    {
        $subject = "Welcome to " . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro') . "!";

        $body = $this->getTemplate('welcome', [
            'full_name' => $fullName,
            'username' => $username,
            'site_name' => \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro'),
            'login_url' => app_base_url('login.php')
        ]);

        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send generic email
     */
    public function sendEmail($to, $subject, $body, $isHtml = true)
    {
        try {
            if ($this->usePHPMailer && $this->mailer) {
                return $this->sendViaPHPMailer($to, $subject, $body, $isHtml);
            } else {
                return $this->sendViaPHPMail($to, $subject, $body, $isHtml);
            }
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using PHPMailer
     */
    private function sendViaPHPMailer($to, $subject, $body, $isHtml = true)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML($isHtml);

            if (!$isHtml) {
                $this->mailer->AltBody = strip_tags($body);
            }

            $result = $this->mailer->send();

            if ($result) {
                error_log("Email sent successfully to: {$to}");
            }

            return $result;
        } catch (Exception $e) {
            error_log("PHPMailer sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using PHP mail() function
     */
    private function sendViaPHPMail($to, $subject, $body, $isHtml = true)
    {
        $headers = [
            'From: ' . $this->settings['from_name'] . ' <' . $this->settings['from_email'] . '>',
            'Reply-To: ' . $this->settings['reply_to'],
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0'
        ];

        if ($isHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }

        $result = mail($to, $subject, $body, implode("\r\n", $headers));

        if ($result) {
            error_log("Email sent successfully via PHP mail() to: {$to}");
        } else {
            error_log("PHP mail() sending failed for: {$to}");
        }

        return $result;
    }

    /**
     * Get email template
     */
    private function getTemplate($templateName, $variables = [])
    {
        // Map logical template names to file names
        $templateMap = [
            'email_verification' => 'verification.php',
            'password_reset' => 'password_reset.php',
            'welcome' => 'welcome.php',
            'new_account' => 'new_account.php'
        ];

        $fileName = $templateMap[$templateName] ?? null;
        if (!$fileName) {
            return '';
        }

        $templatePath = __DIR__ . '/../../themes/default/emails/' . $fileName;
        
        if (!file_exists($templatePath)) {
            error_log("Email template not found: " . $templatePath);
            return '';
        }

        $template = file_get_contents($templatePath);

        // Add default variables
        $variables['current_year'] = date('Y');
        $variables['site_name'] = \App\Services\SettingsService::get('site_name', 'Bishwo Calculator');

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', htmlspecialchars($value), $template);
        }

        return $template;
    }

    /**
     * Test email configuration
     */
    public function testEmailSettings($testEmail)
    {
        try {
            $testResult = $this->sendEmail(
                $testEmail,
                'EngiCal Pro - Email Test',
                '<p>This is a test email to verify your email configuration is working correctly.</p><p>If you received this email, your settings are configured properly!</p>'
            );

            return [
                'success' => $testResult,
                'message' => $testResult ? 'Test email sent successfully!' : 'Failed to send test email'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get current email settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Update email settings
     */
    public function updateSettings($newSettings)
    {
        try {
            $db = Database::getInstance();

            foreach ($newSettings as $key => $value) {
                $settingKey = 'email_' . $key;

                // Check if setting exists
                $stmt = $db->query(
                    "SELECT id FROM site_settings WHERE setting_key = ?",
                    [$settingKey]
                );

                if ($stmt && $stmt->fetch()) {
                    // Update existing setting
                    $db->query(
                        "UPDATE site_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?",
                        [$value, $settingKey]
                    );
                } else {
                    // Insert new setting
                    $db->query(
                        "INSERT INTO site_settings (setting_key, setting_value, setting_group, created_at, updated_at) VALUES (?, ?, 'email', NOW(), NOW())",
                        [$settingKey, $value]
                    );
                }
            }

            // Reload settings
            $this->loadSettings();
            $this->initializeMailer();

            return true;
        } catch (Exception $e) {
            error_log('Failed to update email settings: ' . $e->getMessage());
            return false;
        }
    }
}
