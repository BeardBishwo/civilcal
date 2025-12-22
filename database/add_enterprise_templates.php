<?php
/**
 * Add Enterprise Email Templates
 * Simple script to add 5 professional templates
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "=============================================\n";
echo "Adding Enterprise Email Templates\n";
echo "=============================================\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=bishwo_calculator;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $templates = [
        [
            'name' => 'Password Reset Request',
            'subject' => 'Reset Your Password - {{site_name}}',
            'content' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#ef4444,#dc2626);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.button{display:inline-block;background:#ef4444;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;margin:20px 0;font-weight:bold}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h1>🔐 Password Reset</h1></div><div class="content"><h2>Reset Your Password</h2><p>Hello {{first_name}},</p><p>Click below to reset your password:</p><div style="text-align:center"><a href="{{reset_url}}" class="button">Reset Password</a></div><p>Link expires in 1 hour.</p></div><div class="footer"><p>&copy; {{current_year}} {{site_name}}</p></div></div></body></html>',
            'category' => 'general'
        ],
        [
            'name' => 'Email Verification',
            'subject' => 'Verify Your Email - {{site_name}}',
            'content' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.button{display:inline-block;background:#667eea;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;margin:20px 0;font-weight:bold}.code-box{background:white;border:2px dashed #667eea;padding:20px;text-align:center;font-size:24px;font-weight:bold;margin:20px 0}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h1>✉️ Verify Email</h1></div><div class="content"><h2>Welcome!</h2><p>Hello {{first_name}},</p><p>Verify your email:</p><div style="text-align:center"><a href="{{verification_url}}" class="button">Verify Email</a></div><p>Or use code:</p><div class="code-box">{{verification_code}}</div></div><div class="footer"><p>&copy; {{current_year}} {{site_name}}</p></div></div></body></html>',
            'category' => 'general'
        ],
        [
            'name' => 'Account Created',
            'subject' => 'Your Account - {{site_name}}',
            'content' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#10b981,#059669);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.button{display:inline-block;background:#10b981;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;margin:20px 0;font-weight:bold}.credentials{background:white;border:1px solid #e5e7eb;padding:20px;border-radius:8px;margin:20px 0}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h1>🎉 Welcome!</h1></div><div class="content"><h2>Account Ready</h2><p>Hello {{first_name}},</p><div class="credentials"><p><strong>Username:</strong> {{username}}</p><p><strong>Email:</strong> {{email}}</p><p><strong>Password:</strong> {{password}}</p></div><div style="text-align:center"><a href="{{action_url}}" class="button">Login Now</a></div></div><div class="footer"><p>&copy; {{current_year}} {{site_name}}</p></div></div></body></html>',
            'category' => 'general'
        ],
        [
            'name' => 'Contact Form Response',
            'subject' => 'Message Received - {{site_name}}',
            'content' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.message-box{background:white;border-left:4px solid #3b82f6;padding:15px;margin:20px 0}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h1>📧 Message Received</h1></div><div class="content"><h2>Thank You</h2><p>Hello {{first_name}},</p><p>We received your message.</p><div class="message-box"><p>{{message_content}}</p></div><p><strong>Reference:</strong> #{{ticket_id}}</p></div><div class="footer"><p>&copy; {{current_year}} {{site_name}}</p></div></div></body></html>',
            'category' => 'general'
        ],
        [
            'name' => 'Newsletter',
            'subject' => '{{newsletter_title}} - {{site_name}}',
            'content' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#f59e0b,#d97706);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}.content{background:#f9f9f9;padding:30px}.footer{background:#f1f1f1;padding:20px;text-align:center;font-size:12px;color:#666;border-radius:0 0 10px 10px}</style></head><body><div class="container"><div class="header"><h2>{{newsletter_title}}</h2></div><div class="content"><p>Hello {{first_name}},</p>{{newsletter_content}}</div><div class="footer"><p>&copy; {{current_year}} {{company_name}}</p><div><a href="{{unsubscribe_url}}">Unsubscribe</a></div></div></div></body></html>',
            'category' => 'general'
        ]
    ];

    $inserted = 0;
    $skipped = 0;

    foreach ($templates as $t) {
        $stmt = $pdo->prepare("SELECT id FROM email_templates WHERE name = ?");
        $stmt->execute([$t['name']]);
        
        if ($stmt->rowCount() == 0) {
            $pdo->prepare("INSERT INTO email_templates (name, subject, content, category, description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())")
                ->execute([$t['name'], $t['subject'], $t['content'], $t['category'], 'Enterprise template']);
            echo "✅ {$t['name']}\n";
            $inserted++;
        } else {
            echo "⚠️  {$t['name']} (already exists)\n";
            $skipped++;
        }
    }

    echo "\n=============================================\n";
    echo "✅ Complete!\n";
    echo "=============================================\n";
    echo "Added: $inserted templates\n";
    echo "Skipped: $skipped templates\n";
    echo "\nNext: Configure SMTP via admin panel\n";
    echo "Then send test email to: bishwonathpaudel24@gmail.com\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
