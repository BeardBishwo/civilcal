<?php
namespace App\Services;

/**
 * Email service for sending transactional and notification emails
 * Provides a clean interface for email operations
 */
class EmailService {
    private array $config;
    private $logger;
    
    public function __construct($logger = null) {
        $this->logger = $logger;
        $this->loadConfiguration();
    }
    
    /**
     * Load email configuration
     */
    private function loadConfiguration(): void {
        // Try to load from configuration file
        $configFile = BASE_PATH . '/config/mail.php';
        
        if (file_exists($configFile)) {
            $config = include $configFile;
            if (is_array($config)) {
                $this->config = $config;
            } else {
                // Fallback if config file doesn't return an array
                $this->config = $this->getDefaultConfig();
            }
        } else {
            // Fallback to default configuration
            $this->config = $this->getDefaultConfig();
        }
    }
    
    /**
     * Get default email configuration
     */
    private function getDefaultConfig(): array {
        return [
            'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
            'host' => getenv('MAIL_HOST') ?: 'localhost',
            'port' => getenv('MAIL_PORT') ?: 587,
            'username' => getenv('MAIL_USERNAME') ?: '',
            'password' => getenv('MAIL_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from' => [
                'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@example.com',
                'name' => getenv('MAIL_FROM_NAME') ?: 'Bishwo Calculator'
            ]
        ];
    }
    
    /**
     * Send a simple email
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool {
        try {
            $headers = $this->buildHeaders($to, $subject, $options);
            $message = $this->buildMessage($body, $options);
            
            $result = mail($to, $subject, $message, $headers);
            
            if ($result) {
                $this->logEmail('sent', $to, $subject);
                return true;
            } else {
                $this->logEmail('failed', $to, $subject, 'Mail function returned false');
                return false;
            }
            
        } catch (\Exception $e) {
            $this->logEmail('error', $to, $subject, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send HTML email
     */
    public function sendHtml(string $to, string $subject, string $htmlBody, array $options = []): bool {
        $options['html'] = true;
        return $this->send($to, $subject, $htmlBody, $options);
    }
    
    /**
     * Send email with template
     */
    public function sendTemplate(string $to, string $template, array $data = [], array $options = []): bool {
        $templatePath = BASE_PATH . "/app/Views/email/{$template}.php";
        
        if (!file_exists($templatePath)) {
            $this->logEmail('error', $to, 'Template not found', "Template: {$template}");
            return false;
        }
        
        // Extract template variables
        extract($data);
        
        // Capture template output
        ob_start();
        include $templatePath;
        $body = ob_get_clean();
        
        return $this->send($to, $options['subject'] ?? 'Notification', $body, $options);
    }
    
    /**
     * Build email headers
     */
    private function buildHeaders(string $to, string $subject, array $options): string {
        $headers = [];
        
        // From header
        $from = $options['from'] ?? $this->config['from'];
        $headers[] = "From: {$from['name']} <{$from['address']}>";
        $headers[] = "Reply-To: {$from['name']} <{$from['address']}>";
        
        // MIME headers for HTML emails
        if ($options['html'] ?? false) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }
        
        // CC and BCC
        if (isset($options['cc'])) {
            $headers[] = "Cc: {$options['cc']}";
        }
        
        if (isset($options['bcc'])) {
            $headers[] = "Bcc: {$options['bcc']}";
        }
        
        return implode("\r\n", $headers);
    }
    
    /**
     * Build email message body
     */
    private function buildMessage(string $body, array $options): string {
        // Add signature if specified
        if (isset($options['signature']) && $options['signature']) {
            $signature = $this->getSignature();
            $body .= "\r\n\r\n" . $signature;
        }
        
        // Process newlines for email
        $body = str_replace("\r\n", "\r\n", $body);
        $body = str_replace("\n", "\r\n", $body);
        
        return $body;
    }
    
    /**
     * Get email signature
     */
    private function getSignature(): string {
        return "-- \r\n" . 
               $this->config['from']['name'] . "\r\n" .
               "Bishwo Calculator Team\r\n" .
               "https://example.com";
    }
    
    /**
     * Log email activity
     */
    private function logEmail(string $action, string $to, string $subject, string $error = ''): void {
        if ($this->logger) {
            $logData = [
                'action' => $action,
                'to' => $to,
                'subject' => $subject,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            if ($error) {
                $logData['error'] = $error;
                $this->logger->error("Email {$action}: {$error}", $logData);
            } else {
                $this->logger->info("Email {$action}", $logData);
            }
        }
    }
    
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(string $to, string $userName): bool {
        $subject = 'Welcome to Bishwo Calculator!';
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 15px; background: #6c757d; color: white; text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to Bishwo Calculator!</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$userName},</h2>
                    <p>Thank you for joining Bishwo Calculator! We're excited to have you on board.</p>
                    <p>Our platform provides over 250 specialized engineering calculators across 10+ disciplines, designed specifically for Architecture, Engineering, and Construction professionals.</p>
                    <p><a href='https://example.com/login' class='btn'>Get Started</a></p>
                    <p>If you have any questions, feel free to reply to this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Bishwo Calculator. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendHtml($to, $subject, $htmlBody);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $to, string $userName, string $resetToken): bool {
        $subject = 'Password Reset Request';
        
        $resetUrl = "https://example.com/reset-password?token={$resetToken}";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ffc107; color: #333; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 15px; background: #6c757d; color: white; text-align: center; }
                .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$userName},</h2>
                    <p>We received a request to reset your password. If you made this request, click the button below to reset your password:</p>
                    <p><a href='{$resetUrl}' class='btn'>Reset Password</a></p>
                    <p>If you didn't request this reset, you can safely ignore this email.</p>
                    <p>This reset link will expire in 1 hour for security reasons.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Bishwo Calculator. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendHtml($to, $subject, $htmlBody);
    }
    
    /**
     * Send calculation result email
     */
    public function sendCalculationResultEmail(string $to, string $userName, string $calculationType, array $results): bool {
        $subject = "Your {$calculationType} Calculation Results";
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .results { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .footer { padding: 15px; background: #6c757d; color: white; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Calculation Results</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$userName},</h2>
                    <p>Your {$calculationType} calculation has been completed successfully!</p>
                    <div class='results'>
                        <h3>Results:</h3>
                        <table>
        ";
        
        foreach ($results as $key => $value) {
            $htmlBody .= "<tr><th>" . htmlspecialchars($key) . "</th><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        
        $htmlBody .="                    </table>
                    </div>
                    <p>Thank you for using Bishwo Calculator!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Bishwo Calculator. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendHtml($to, $subject, $htmlBody);
    }
    
    /**
     * Test email configuration
     */
    public function testConnection(): array {
        try {
            // Test basic mail function
            $testEmail = $this->config['from']['address'];
            $testSubject = 'Test Email';
            $testBody = 'This is a test email to verify the email configuration.';
            
            $result = mail($testEmail, $testSubject, $testBody);
            
            return [
                'success' => $result,
                'message' => $result ? 'Email configuration is working' : 'Email configuration failed',
                'config' => [
                    'driver' => $this->config['driver'],
                    'from_email' => $this->config['from']['address']
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Email configuration error: ' . $e->getMessage(),
                'config' => $this->config
            ];
        }
    }
    
    /**
     * Get email statistics
     */
    public function getStats(): array {
        return [
            'driver' => $this->config['driver'],
            'from_email' => $this->config['from']['address'],
            'from_name' => $this->config['from']['name']
        ];
    }
}
