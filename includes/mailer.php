<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private static ?PHPMailer $mailer = null;
    private static array $failedEmails = [];
    private static int $lastSentTime = 0;
    private static int $emailCount = 0;
    private const RATE_LIMIT = 100; // Max emails per hour
    private const MIN_INTERVAL = 2; // Minimum seconds between emails

    private static function initMailer(): ?PHPMailer {
        if (self::$mailer === null) {
            // Check if SMTP configuration is available
            $smtp_config = self::getSmtpConfig();
            
            if (!$smtp_config || !$smtp_config['enabled']) {
                // No SMTP configured, return null to use PHP mail()
                return null;
            }
            
            try {
                self::$mailer = new PHPMailer(true);
                
                // Configure SMTP
                self::$mailer->isSMTP();
                self::$mailer->Host = $smtp_config['host'];
                self::$mailer->SMTPAuth = true;
                self::$mailer->Username = $smtp_config['username'];
                self::$mailer->Password = $smtp_config['password'];
                self::$mailer->SMTPSecure = $smtp_config['secure'];
                self::$mailer->Port = (int)$smtp_config['port'];
                
                // Set sender details
                $from = $smtp_config['from_email'] ?? 'noreply@example.com';
                $from_name = $smtp_config['from_name'] ?? 'AEC Calculator';
                self::$mailer->setFrom($from, $from_name);
                
                // Enable debug in development
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    self::$mailer->SMTPDebug = SMTP::DEBUG_SERVER;
                }
                
                // SSL options for development
                self::$mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
                
            } catch (Exception $e) {
                error_log("SMTP Configuration Error: " . $e->getMessage());
                self::$mailer = null;
            }
        }
        return self::$mailer;
    }
    
    /**
     * Get SMTP configuration from database or config
     */
    private static function getSmtpConfig(): ?array {
        try {
            // First try to get from database (admin panel settings)
            $pdo = get_db();
            if ($pdo) {
                $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'mail_%' OR setting_key LIKE 'smtp_%'");
                $stmt->execute();
                $settings = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
                
                if (!empty($settings)) {
                    return [
                        'enabled' => ($settings['smtp_enabled'] ?? 'false') === 'true',
                        'host' => $settings['smtp_host'] ?? '',
                        'port' => $settings['smtp_port'] ?? '587',
                        'username' => $settings['smtp_username'] ?? '',
                        'password' => $settings['smtp_password'] ?? '',
                        'secure' => $settings['smtp_secure'] ?? 'tls',
                        'from_email' => $settings['mail_from'] ?? 'noreply@example.com',
                        'from_name' => $settings['mail_from_name'] ?? 'AEC Calculator'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Error loading SMTP config from database: " . $e->getMessage());
        }
        
        // Fallback to config file constants
        $enabled = defined('MAIL_SMTP_HOST') && MAIL_SMTP_HOST && MAIL_SMTP_HOST !== 'smtp.example.com';
        return [
            'enabled' => $enabled,
            'host' => MAIL_SMTP_HOST ?? 'smtp.example.com',
            'port' => MAIL_SMTP_PORT ?? '587',
            'username' => MAIL_SMTP_USER ?? '',
            'password' => MAIL_SMTP_PASS ?? '',
            'secure' => MAIL_SMTP_SECURE ?? 'tls',
            'from_email' => MAIL_FROM ?? 'noreply@example.com',
            'from_name' => MAIL_FROM_NAME ?? 'AEC Calculator'
        ];
    }

    private static function checkRateLimit(): bool {
        $now = time();
        
        // Check minimum interval between emails
        if ($now - self::$lastSentTime < self::MIN_INTERVAL) {
            return false;
        }
        
        // Reset counter each hour
        if ($now - self::$lastSentTime > 3600) {
            self::$emailCount = 0;
        }
        
        // Check hourly limit
        return self::$emailCount < self::RATE_LIMIT;
    }

    public static function send(string $to, string $subject, string $htmlBody, string $plainBody = '', array $attachments = []): bool {
        try {
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address: $to");
            }

            if (!self::checkRateLimit()) {
                throw new Exception("Rate limit exceeded");
            }

            // Try SMTP first
            $mailer = self::initMailer();
            
            if ($mailer) {
                try {
                    $mailer->clearAddresses();
                    $mailer->clearAttachments();
                    
                    $mailer->addAddress($to);
                    $mailer->isHTML(true);
                    $mailer->Subject = $subject;
                    $mailer->Body = $htmlBody;
                    $mailer->AltBody = $plainBody ?: strip_tags($htmlBody);

                    // Add attachments if any
                    foreach ($attachments as $attachment) {
                        if (isset($attachment['path'])) {
                            $mailer->addAttachment(
                                $attachment['path'],
                                $attachment['name'] ?? '',
                                $attachment['encoding'] ?? 'base64',
                                $attachment['type'] ?? ''
                            );
                        }
                    }

                    $result = $mailer->send();
                    
                    if ($result) {
                        self::$lastSentTime = time();
                        self::$emailCount++;
                        error_log("Email sent successfully via SMTP to: $to");
                        return true;
                    }
                } catch (Exception $e) {
                    error_log("SMTP send failed: " . $e->getMessage());
                    // Continue to fallback
                }
            }
            
            // Fallback to PHP mail()
            return self::sendWithPhpMail($to, $subject, $htmlBody);
            
        } catch (Exception $e) {
            self::$failedEmails[] = [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            error_log("Email send error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email using PHP mail() function as fallback
     */
    private static function sendWithPhpMail(string $to, string $subject, string $htmlBody): bool {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $from = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@example.com';
            $from_name = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'AEC Calculator';
            $headers .= "From: $from_name <$from>\r\n";
            $headers .= "Reply-To: noreply@example.com\r\n";
            
            $result = mail($to, $subject, $htmlBody, $headers);
            
            if ($result) {
                self::$lastSentTime = time();
                self::$emailCount++;
                error_log("Email sent successfully via PHP mail() to: $to");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("PHP mail() send failed: " . $e->getMessage());
            return false;
        }
    }

    public static function sendVerificationEmail(string $to, string $verificationCode): bool {
        $subject = "Verify Your AEC Calculator Account";
        
        // Generate proper verification link with token parameter
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base_path = APP_BASE ?? '/aec-calculator';
        
        $link = $protocol . $host . $base_path . "/verify.php?token=" . urlencode($verificationCode);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Email Verification - AEC Calculator</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .button { display: inline-block; padding: 15px 30px; background: #4f46e5; color: white; text-decoration: none; border-radius: 10px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to AEC Calculator!</h1>
                </div>
                <div class='content'>
                    <h2>Verify Your Email Address</h2>
                    <p>Thank you for registering with AEC Calculator! Please verify your email address by clicking the button below:</p>
                    <div style='text-align: center;'>
                        <a href='$link' class='button'>Verify Email Address</a>
                    </div>
                    <p>Or copy and paste this URL into your browser:</p>
                    <p style='background: #e5e5e5; padding: 10px; border-radius: 5px; word-break: break-all;'>$link</p>
                    <p><strong>Security Note:</strong> This verification link will expire in 24 hours for your security.</p>
                    <p>If you didn't create an account with AEC Calculator, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>AEC Calculator - Professional Engineering Tools<br>
                    © 2025 AEC Calculator. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::send($to, $subject, $html);
    }

    public static function sendPasswordReset(string $to, string $resetCode): bool {
        $subject = "Reset Your AEC Calculator Password";
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base_path = APP_BASE ?? '/aec-calculator';
        
        $link = $protocol . $host . $base_path . "/reset.php?token=" . urlencode($resetCode);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Password Reset - AEC Calculator</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .button { display: inline-block; padding: 15px 30px; background: #f59e0b; color: white; text-decoration: none; border-radius: 10px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset Request</h1>
                </div>
                <div class='content'>
                    <h2>Reset Your Password</h2>
                    <p>We received a request to reset your password. Click the button below to set a new password:</p>
                    <div style='text-align: center;'>
                        <a href='$link' class='button'>Reset Password</a>
                    </div>
                    <p>Or copy and paste this URL into your browser:</p>
                    <p style='background: #e5e5e5; padding: 10px; border-radius: 5px; word-break: break-all;'>$link</p>
                    <p><strong>Security Note:</strong> This password reset link will expire in 1 hour for your security.</p>
                    <p>If you didn't request a password reset, please ignore this email and your password will remain unchanged.</p>
                </div>
                <div class='footer'>
                    <p>AEC Calculator - Professional Engineering Tools<br>
                    © 2025 AEC Calculator. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::send($to, $subject, $html);
    }

    public static function getFailedEmails(): array {
        return self::$failedEmails;
    }
    
    /**
     * Test email configuration
     */
    public static function testEmailConfig(string $testEmail = null): array {
        $testEmail = $testEmail ?: 'admin@example.com';
        
        $result = [
            'smtp_configured' => false,
            'smtp_test' => false,
            'php_mail_test' => false,
            'errors' => []
        ];
        
        try {
            // Check SMTP configuration
            $smtp_config = self::getSmtpConfig();
            if ($smtp_config && $smtp_config['enabled'] && !empty($smtp_config['host'])) {
                $result['smtp_configured'] = true;
                
                // Test SMTP
                $test_result = self::send($testEmail, 'Test Email - AEC Calculator', '<p>This is a test email to verify your SMTP configuration.</p>');
                $result['smtp_test'] = $test_result;
                
                if (!$test_result) {
                    $result['errors'][] = 'SMTP configuration is enabled but test email failed';
                }
            } else {
                $result['errors'][] = 'SMTP is not configured or disabled';
            }
            
            // Test PHP mail()
            $php_mail_result = self::sendWithPhpMail($testEmail, 'Test Email - PHP Mail', '<p>This is a test email using PHP mail().</p>');
            $result['php_mail_test'] = $php_mail_result;
            
            if (!$result['smtp_test'] && !$result['php_mail_test']) {
                $result['errors'][] = 'Both SMTP and PHP mail() failed';
            }
            
        } catch (Exception $e) {
            $result['errors'][] = 'Email test failed: ' . $e->getMessage();
        }
        
        return $result;
    }
}

// Backward compatibility function
function send_email_simple(string $to, string $subject, string $htmlBody, string $plainBody = ''): bool {
    return Mailer::send($to, $subject, $htmlBody, $plainBody);
}
