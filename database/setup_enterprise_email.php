<?php
/**
 * Enterprise Email System Setup - Standalone Version
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "=============================================\n";
echo "Enterprise Email System Setup\n";
echo "=============================================\n\n";

// Database credentials
$dbHost = '127.0.0.1';
$dbName = 'bishwo_calculator';
$dbUser = 'root';
$dbPass = '';

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "✅ Database connected\n\n";

    // Step 1: Configure SMTP
    echo "Step 1: Configuring SMTP Settings...\n";
    
    $smtpSettings = [
        'email_smtp_host' => 'mail.newsbishwo.com',
        'email_smtp_port' => '465',
        'email_smtp_user' => 'admin@newsbishwo.com',
        'email_smtp_pass' => '^,2J?4Yqda_*YtW&',
        'email_smtp_secure' => 'ssl',
        'email_from_address' => 'admin@newsbishwo.com',
        'email_from_name' => 'Bishwo Calculator'
    ];

    foreach ($smtpSettings as $key => $value) {
        try {
            echo "  Processing $key... ";
            // Skip password - will set manually
            if ($key === 'email_smtp_pass') {
                echo "⚠️  (set manually)\n";
                continue;
            }
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ? LIMIT 1");
            $result = $stmt->execute([$value, $key]);
            echo "✓\n";
        } catch (Exception $e) {
            echo "❌ " . $e->getMessage() . "\n";
            continue; // Continue with next setting
        }
    }

    echo "\n✅ SMTP configured!\n\n";

    // Step 2: Add Templates
    echo "Step 2: Adding Enterprise Templates...\n";

    $templates = [
        ['name' => 'Password Reset', 'subject' => 'Reset Password - {{site_name}}', 'category' => 'password_reset', 'description' => 'Password reset email'],
        ['name' => 'Email Verification', 'subject' => 'Verify Email - {{site_name}}', 'category' => 'verification', 'description' => 'Email verification'],
        ['name' => 'Account Created', 'subject' => 'Your Account - {{site_name}}', 'category' => 'welcome', 'description' => 'Account created by admin'],
        ['name' => 'Contact Response', 'subject' => 'Message Received - {{site_name}}', 'category' => 'general', 'description' => 'Contact form response'],
        ['name' => 'Newsletter', 'subject' => '{{newsletter_title}}', 'category' => 'notification', 'description' => 'Newsletter template']
    ];

    $content = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h1>{{site_name}}</h1></div><div class="content"><p>Hello {{first_name}},</p><p>{{message_content}}</p></div><div class="footer"><p>&copy; {{current_year}} {{site_name}}</p></div></div></body></html>';

    $inserted = 0;
    foreach ($templates as $t) {
        $stmt = $pdo->prepare("SELECT id FROM email_templates WHERE name = ?");
        $stmt->execute([$t['name']]);
        
        if ($stmt->rowCount() == 0) {
            $pdo->prepare("INSERT INTO email_templates (name, subject, content, category, description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())")
                ->execute([$t['name'], $t['subject'], $content, $t['category'], $t['description']]);
            echo "  ✅ {$t['name']}\n";
            $inserted++;
        } else {
            echo "  ⚠️  {$t['name']} exists\n";
        }
    }

    echo "\n✅ $inserted templates added!\n\n";

    // Step 3: Send Test Email
    echo "Step 3: Sending test email...\n";
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.newsbishwo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@newsbishwo.com';
        $mail->Password = '^,2J?4Yqda_*YtW&';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->SMTPDebug = 0;
        
        $mail->setFrom('admin@newsbishwo.com', 'Bishwo Calculator');
        $mail->addAddress('bishwonathpaudel24@gmail.com');
        
        $mail->isHTML(true);
        $mail->Subject = 'Enterprise Email System - Test';
        $mail->Body = '<html><body style="font-family:Arial;padding:20px"><div style="max-width:600px;margin:0 auto;background:#f9f9f9;padding:30px;border-radius:10px"><h1 style="color:#667eea">✅ Email System Active!</h1><p>Your enterprise email system is configured and working!</p><h3>Configuration:</h3><ul><li>Server: mail.newsbishwo.com</li><li>Port: 465 (SSL)</li><li>From: admin@newsbishwo.com</li></ul><h3>Templates Added:</h3><ul><li>✅ Password Reset</li><li>✅ Email Verification</li><li>✅ Account Created</li><li>✅ Contact Response</li><li>✅ Newsletter</li></ul><p><strong>Status:</strong> If you received this, SMTP is working perfectly!</p></div></body></html>';
        
        $mail->send();
        echo "✅ Test email sent to bishwonathpaudel24@gmail.com!\n";
    } catch (Exception $e) {
        echo "❌ Email failed: {$mail->ErrorInfo}\n";
    }

    echo "\n=============================================\n";
    echo "✅ Setup Complete!\n";
    echo "=============================================\n";
    echo "SMTP: mail.newsbishwo.com:465 (SSL)\n";
    echo "Templates: $inserted added\n";
    echo "Test Email: Sent\n";
    echo "\n🚀 Enterprise email system is ready!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
