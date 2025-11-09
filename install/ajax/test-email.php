<?php
/**
 * Bishwo Calculator - Email Test Handler
 * AJAX endpoint for testing SMTP email configuration
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
session_start();

// Include PHPMailer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    // Fallback to form data
    $data = $_POST;
}

// Extract SMTP configuration
$smtpHost = trim($data['smtp_host'] ?? '');
$smtpPort = trim($data['smtp_port'] ?? '');
$smtpUser = trim($data['smtp_user'] ?? '');
$smtpPass = $data['smtp_pass'] ?? '';

// Validate required fields
if (empty($smtpHost) || empty($smtpPort) || empty($smtpUser) || empty($smtpPass)) {
    echo json_encode([
        'success' => false, 
        'message' => 'All SMTP fields (host, port, username, password) are required'
    ]);
    exit;
}

// Validate host format
if (!preg_match('/^[a-zA-Z0-9.-]+$/', $smtpHost)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid SMTP host format'
    ]);
    exit;
}

// Validate port
$port = (int)$smtpPort;
if ($port < 1 || $port > 65535) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid port number. Must be between 1 and 65535'
    ]);
    exit;
}

try {
    // Enhanced SMTP connection testing with multiple methods
    
    // Method 1: Test basic connection
    $connection = @fsockopen($smtpHost, $port, $errno, $errstr, 30); // Increased timeout to 30 seconds
    
    if (!$connection) {
        echo json_encode([
            'success' => false, 
            'message' => "Cannot connect to SMTP server: $errstr ($errno). Please check your SMTP host and port."
        ]);
        exit;
    }
    
    fclose($connection);
    
    // If connection is successful, try to send a test email using PHPMailer
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings with enhanced configuration
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->Port = $port;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            
            // Enhanced SSL/TLS detection and configuration
            if ($port == 465) {
                $mail->SMTPSecure = 'ssl';
            } else {
                $mail->SMTPSecure = 'tls';
            }
            
            $mail->Timeout = 60; // Increased timeout to 60 seconds
            $mail->SMTPDebug = 0; // Disable debug output in production
            
            // Enhanced connection options
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Recipients
            $mail->setFrom($smtpUser, 'Bishwo Calculator');
            $mail->addAddress($smtpUser, 'Test Recipient');
            $mail->addReplyTo($smtpUser, 'Bishwo Calculator Support');
            
            // Content - Premium Email Template
            $mail->isHTML(true);
            $mail->Subject = '🚀 Bishwo Calculator - System Configuration Test';
            
            $currentYear = date('Y');
            $testTime = date('Y-m-d H:i:s');
            $securityType = ($port == 465) ? 'SSL/TLS' : 'TLS';
            
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Bishwo Calculator - Email Test</title>
            </head>
            <body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); line-height: 1.6;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 0;">
                    <tr>
                        <td align="center">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background: #ffffff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); overflow: hidden;">
                                
                                <!-- Header -->
                                <tr>
                                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                                        <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 12px; display: inline-block; margin-bottom: 20px;">
                                            <h1 style="color: #ffffff; font-size: 32px; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                🚀 Bishwo Calculator
                                            </h1>
                                        </div>
                                        <h2 style="color: #ffffff; font-size: 24px; font-weight: 600; margin: 0; opacity: 0.9;">
                                            Email Configuration Test
                                        </h2>
                                    </td>
                                </tr>
                                
                                <!-- Content -->
                                <tr>
                                    <td style="padding: 50px 40px;">
                                        
                                        <!-- Success Message -->
                                        <div style="text-align: center; margin-bottom: 40px;">
                                            <div style="background: linear-gradient(135deg, #4CAF50, #45a049); padding: 20px; border-radius: 12px; display: inline-block; margin-bottom: 20px;">
                                                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                                    <span style="font-size: 30px;">✅</span>
                                                </div>
                                            </div>
                                            <h3 style="color: #2c3e50; font-size: 28px; font-weight: 700; margin: 0 0 15px;">Email Test Successful!</h3>
                                            <p style="color: #7f8c8d; font-size: 16px; margin: 0;">Your SMTP email configuration is working perfectly.</p>
                                        </div>
                                        
                                        <!-- Configuration Details Card -->
                                        <div style="background: #f8f9fa; border-radius: 12px; padding: 30px; margin: 40px 0; border-left: 4px solid #667eea;">
                                            <h4 style="color: #2c3e50; font-size: 18px; font-weight: 600; margin: 0 0 20px; display: flex; align-items: center;">
                                                <span style="background: #667eea; color: white; padding: 8px; border-radius: 6px; margin-right: 12px; font-size: 14px;">ℹ️</span>
                                                Configuration Details
                                            </h4>
                                            
                                            <table role="presentation" width="100%">
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; width: 35%;">
                                                        <strong style="color: #495057; font-size: 14px;">SMTP Host:</strong>
                                                    </td>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <code style="background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 13px;">' . htmlspecialchars($smtpHost) . '</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <strong style="color: #495057; font-size: 14px;">Port:</strong>
                                                    </td>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <code style="background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 13px;">' . htmlspecialchars($smtpPort) . '</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <strong style="color: #495057; font-size: 14px;">Username:</strong>
                                                    </td>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <code style="background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 13px;">' . htmlspecialchars($smtpUser) . '</code>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <strong style="color: #495057; font-size: 14px;">Security:</strong>
                                                    </td>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef;">
                                                        <span style="color: #28a745; font-weight: 600; font-size: 13px;">' . $securityType . '</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px 0;">
                                                        <strong style="color: #495057; font-size: 14px;">Test Time:</strong>
                                                    </td>
                                                    <td style="padding: 8px 0;">
                                                        <span style="color: #6c757d; font-size: 13px;">' . $testTime . '</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                        <!-- Next Steps -->
                                        <div style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; padding: 30px; margin: 40px 0; text-align: center;">
                                            <h4 style="color: #ffffff; font-size: 18px; font-weight: 600; margin: 0 0 15px;">🎯 What\'s Next?</h4>
                                            <p style="color: rgba(255,255,255,0.9); font-size: 15px; margin: 0 0 20px;">Your email system is now ready to send notifications, user registrations, and system alerts.</p>
                                            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 8px; text-align: left;">
                                                <ul style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0; padding-left: 20px;">
                                                    <li style="margin-bottom: 8px;">✅ User registration emails will be sent automatically</li>
                                                    <li style="margin-bottom: 8px;">✅ System notifications will reach your users</li>
                                                    <li style="margin-bottom: 0px;">✅ Password reset emails will work perfectly</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <!-- Support Section -->
                                        <div style="text-align: center; padding: 20px 0;">
                                            <p style="color: #6c757d; font-size: 14px; margin: 0;">
                                                If you received this email, your SMTP configuration is working correctly!<br>
                                                <strong style="color: #667eea;">Bishwo Calculator Installation Complete</strong>
                                            </p>
                                        </div>
                                        
                                    </td>
                                </tr>
                                
                                <!-- Footer -->
                                <tr>
                                    <td style="background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                                        <div style="margin-bottom: 20px;">
                                            <h5 style="color: #2c3e50; font-size: 16px; font-weight: 600; margin: 0 0 10px;">Bishwo Calculator</h5>
                                            <p style="color: #6c757d; font-size: 13px; margin: 0;">Professional Engineering Calculator Suite</p>
                                        </div>
                                        <div style="border-top: 1px solid #dee2e6; padding-top: 20px;">
                                            <p style="color: #6c757d; font-size: 12px; margin: 0;">
                                                This test email was sent by Bishwo Calculator Installation Wizard.<br>
                                                © ' . $currentYear . ' Bishwo Calculator. All rights reserved.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
            
            $mail->AltBody = '🚀 Bishwo Calculator - Email Configuration Test\n\n' .
                           '✅ SUCCESS! Your SMTP email configuration is working perfectly.\n\n' .
                           'Configuration Details:\n' .
                           '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n' .
                           'SMTP Host: ' . $smtpHost . '\n' .
                           'Port: ' . $smtpPort . '\n' .
                           'Username: ' . $smtpUser . '\n' .
                           'Security: ' . $securityType . '\n' .
                           'Test Time: ' . $testTime . '\n' .
                           '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n' .
                           '✅ What\'s Next:\n' .
                           '• User registration emails will be sent automatically\n' .
                           '• System notifications will reach your users\n' .
                           '• Password reset emails will work perfectly\n\n' .
                           'If you received this email, your SMTP configuration is working correctly!\n\n' .
                           '© ' . $currentYear . ' Bishwo Calculator - Professional Engineering Calculator Suite';
            
            // Send the email
            if ($mail->send()) {
                echo json_encode([
                    'success' => true, 
                    'message' => '🚀 Test email sent successfully! Please check your inbox (including spam folder). Your SMTP configuration is working perfectly!'
                ]);
            } else {
                throw new Exception('Email send failed: ' . $mail->ErrorInfo);
            }
            
        } catch (Exception $e) {
            // Enhanced error reporting
            $errorDetails = $mail->ErrorInfo ?? $e->getMessage();
            
            // Provide specific error guidance
            $guidance = '';
            if (strpos($errorDetails, 'authentication') !== false || strpos($errorDetails, 'login') !== false) {
                $guidance = ' Please check your username and password. For Gmail, use an app-specific password.';
            } elseif (strpos($errorDetails, 'timed out') !== false) {
                $guidance = ' This might be a network issue. Try again or contact your SMTP provider.';
            } elseif (strpos($errorDetails, 'ssl') !== false || strpos($errorDetails, 'tls') !== false) {
                $guidance = ' SSL/TLS configuration issue. Try port 587 (TLS) instead of 465 (SSL).';
            } elseif (strpos($errorDetails, 'connection') !== false) {
                $guidance = ' Cannot connect to SMTP server. Please verify your host and port.';
            }
            
            echo json_encode([
                'success' => false, 
                'message' => 'SMTP connection successful, but email sending failed: ' . $errorDetails . $guidance . ' Please try again or check your SMTP configuration.'
            ]);
        }
        
    } else {
        // PHPMailer not available
        echo json_encode([
            'success' => false, 
            'message' => 'PHPMailer library is not available. Please ensure PHPMailer is installed via Composer.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Email test failed: ' . $e->getMessage() . ' Please check your SMTP configuration and try again.'
    ]);
}
?>
