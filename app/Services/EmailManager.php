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
    private function loadSettings()
    {
        // Default settings
        $this->settings = [
            'use_phpmailer' => true,
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_email' => 'noreply@example.com',
            'from_name' => \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro'),
            'reply_to' => 'support@example.com'
        ];

        // Load from database if available
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'email_%'");
            if ($stmt) {
                while ($row = $stmt->fetch()) {
                    $key = str_replace('email_', '', $row['setting_key']);
                    $this->settings[$key] = $row['setting_value'];
                }
            }
        } catch (Exception $e) {
            // Use defaults if database loading fails
            error_log('Email settings loading failed: ' . $e->getMessage());
        }

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
        $templates = [
            'email_verification' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
                        .content { background: #f9f9f9; padding: 30px; }
                        .button { display: inline-block; background: #4f46e5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🔧 {{site_name}}</h1>
                            <p>Professional Engineering Calculations</p>
                        </div>
                        <div class="content">
                            <h2>Verify Your Email Address</h2>
                            <p>Hello {{full_name}},</p>
                            <p>Thank you for signing up with {{site_name}}! To complete your registration and start using our professional engineering calculation tools, please verify your email address.</p>
                            <div style="text-align: center;">
                                <a href="{{verification_link}}" class="button">Verify Email Address</a>
                            </div>
                            <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                            <p><a href="{{verification_link}}">{{verification_link}}</a></p>
                            <p>This verification link will expire in 24 hours for security purposes.</p>
                            <p>If you didn\'t create an account with {{site_name}}, please ignore this email.</p>
                        </div>
                        <div class="footer">
                            <p>&copy; {{current_year}} {{site_name}}. All rights reserved.</p>
                            <p>Professional Engineering Calculations | Building Excellence Through Technology</p>
                        </div>
                    </div>
                </body>
                </html>
            ',
            'password_reset' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 30px; text-align: center; }
                        .content { background: #f9f9f9; padding: 30px; }
                        .button { display: inline-block; background: #ef4444; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🔐 Password Reset Request</h1>
                            <p>EngiCal Pro Security</p>
                        </div>
                        <div class="content">
                            <h2>Reset Your Password</h2>
                            <p>Hello {{full_name}},</p>
                            <p>We received a request to reset your password for your EngiCal Pro account. Click the button below to create a new password:</p>
                            <div style="text-align: center;">
                                <a href="{{reset_link}}" class="button">Reset Password</a>
                            </div>
                            <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                            <p><a href="{{reset_link}}">{{reset_link}}</a></p>
                            <p><strong>This reset link will expire in 1 hour for security purposes.</strong></p>
                            <p>If you didn\'t request a password reset, please ignore this email. Your password will remain unchanged.</p>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 EngiCal Pro. All rights reserved.</p>
                            <p>For security reasons, this email was sent to {{email}}</p>
                        </div>
                    </div>
                </body>
                </html>
            ',
            'welcome' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; text-align: center; }
                        .content { background: #f9f9f9; padding: 30px; }
                        .button { display: inline-block; background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .features { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🎉 Welcome to EngiCal Pro!</h1>
                            <p>Your Professional Engineering Toolkit</p>
                        </div>
                        <div class="content">
                            <h2>Account Verified Successfully!</h2>
                            <p>Hello {{full_name}},</p>
                            <p>Congratulations! Your EngiCal Pro account has been verified and is now fully active. You can now access all our professional engineering calculation tools.</p>
                            
                            <div class="features">
                                <h3>🚀 What\'s Available Now:</h3>
                                <ul>
                                    <li><strong>Civil Engineering:</strong> Structural calculations, concrete design, foundation analysis</li>
                                    <li><strong>Electrical Engineering:</strong> Load calculations, power distribution, circuit design</li>
                                    <li><strong>Mechanical/HVAC:</strong> Ventilation, heating/cooling load calculations</li>
                                    <li><strong>Fire Safety:</strong> Fire protection system calculations</li>
                                    <li><strong>Plumbing:</strong> Water supply and drainage calculations</li>
                                </ul>
                            </div>
                            
                            <div style="text-align: center;">
                                <a href="{{login_url}}" class="button">Start Calculating</a>
                            </div>
                            
                            <p><strong>Username:</strong> {{username}}</p>
                            <p>If you have any questions or need support, don\'t hesitate to contact us.</p>
                            <p>Welcome aboard!</p>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 EngiCal Pro. All rights reserved.</p>
                            <p>Building Excellence Through Professional Engineering Tools</p>
                        </div>
                    </div>
                </body>
                </html>
            '
            ,
            'new_account' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #4f46e5, #4338ca); color: white; padding: 30px; text-align: center; }
                        .content { background: #f9f9f9; padding: 30px; }
                        .button { display: inline-block; background: #4f46e5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .credentials-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
                        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                        .warning { color: #b91c1c; background: #fef2f2; padding: 10px; border-radius: 4px; font-size: 14px; margin-top: 15px; border-left: 4px solid #ef4444; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>Welcome to {{site_name}}</h1>
                        </div>
                        <div class="content">
                            <h2>Your Account Has Been Created</h2>
                            <p>Hello {{full_name}},</p>
                            <p>An administrator has created an account for you. Here are your login credentials:</p>
                            
                            <div class="credentials-box">
                                <p><strong>Username:</strong> {{username}}</p>
                                <p><strong>Temporary Password:</strong> {{password}}</p>
                            </div>

                            <div class="warning">
                                <strong>⚠️ Important:</strong> This temporary password will expire in 1 hour. Please log in immediately to change it.
                            </div>
                            
                            <div style="text-align: center;">
                                <a href="{{login_url}}" class="button">Log In Now</a>
                            </div>
                            
                            <p>If the button doesn\'t work, you can login here:</p>
                            <p><a href="{{login_url}}">{{login_url}}</a></p>
                        </div>
                        <div class="footer">
                            <p>&copy; 2025 {{site_name}}. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
            '
        ];

        $template = $templates[$templateName] ?? '';

        // Add current year to variables
        $variables['current_year'] = date('Y');

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
