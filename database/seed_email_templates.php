<?php

/**
 * Seed Default Email Templates
 * Creates starter templates for the Email Manager
 */

require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Config/db.php';
require_once __DIR__ . '/../app/Core/Database.php';

echo "=============================================\n";
echo "Email Templates Seeder\n";
echo "=============================================\n\n";

try {
    $db = App\Core\Database::getInstance();
    $pdo = $db->getPdo();

    echo "Connected to database successfully.\n\n";

    // Default email templates
    $templates = [
        [
            'name' => 'Welcome Message',
            'subject' => 'Welcome to {{site_name}}',
            'content' => '<p>Hello {{user_name}},</p>
<p>Welcome to {{site_name}}! We are thrilled to have you on board.</p>
<p>Our platform provides advanced engineering calculation tools to help you with:</p>
<ul>
    <li>Civil Engineering calculations</li>
    <li>Structural analysis</li>
    <li>HVAC load calculations</li>
    <li>And much more!</li>
</ul>
<p>If you have any questions, feel free to reach out to our support team.</p>
<p>Best regards,<br>{{site_name}} Team</p>',
            'category' => 'general',
            'description' => 'Welcome email sent to new users',
            'variables' => json_encode(['user_name']),
            'is_active' => 1
        ],
        [
            'name' => 'Support Inquiry Response',
            'subject' => 'Re: {{inquiry_subject}}',
            'content' => '<p>Hello {{user_name}},</p>
<p>Thank you for contacting {{site_name}} support.</p>
<p>We have received your inquiry regarding: <strong>{{inquiry_subject}}</strong></p>
<p>Our support team is reviewing your request and will get back to you within 24 hours.</p>
<p>Reference Number: #{{ticket_id}}</p>
<p>Best regards,<br>Support Team</p>',
            'category' => 'support',
            'description' => 'Acknowledgment email for support inquiries',
            'variables' => json_encode(['user_name', 'inquiry_subject', 'ticket_id']),
            'is_active' => 1
        ],
        [
            'name' => 'Issue Resolved',
            'subject' => 'Your Issue Has Been Resolved - #{{ticket_id}}',
            'content' => '<p>Hello {{user_name}},</p>
<p>Great news! Your support ticket #{{ticket_id}} has been resolved.</p>
<p><strong>Issue:</strong> {{issue_description}}</p>
<p><strong>Resolution:</strong> {{resolution_details}}</p>
<p>If you have any further questions or if the issue persists, please don\'t hesitate to reopen this ticket or create a new one.</p>
<p>Thank you for using {{site_name}}!</p>
<p>Best regards,<br>Support Team</p>',
            'category' => 'support',
            'description' => 'Email sent when a support ticket is resolved',
            'variables' => json_encode(['user_name', 'ticket_id', 'issue_description', 'resolution_details']),
            'is_active' => 1
        ],
        [
            'name' => 'Payment Confirmation',
            'subject' => 'Payment Successful - Welcome to {{site_name}} Premium!',
            'content' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f8f9fa; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #28a745; margin: 0; font-size: 24px;">🎉 Payment Successful!</h1>
            <p style="color: #6c757d; margin: 10px 0 0 0;">Welcome to {{site_name}} Premium</p>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin: 0 0 15px 0; color: #495057;">Payment Details</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #6c757d;">Transaction ID:</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: bold;">{{transaction_id}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #6c757d;">Plan:</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: bold;">{{plan_name}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #6c757d;">Amount:</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: bold;">{{currency_symbol}}{{amount}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #6c757d;">Payment Method:</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: bold;">{{payment_method}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6c757d;">Date:</td>
                    <td style="padding: 8px 0; color: #495057; font-weight: bold;">{{payment_date}}</td>
                </tr>
            </table>
        </div>
        
        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin: 0 0 15px 0; color: #1976d2;">What\'s Next?</h3>
            <ul style="color: #1976d2; margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">✅ Your premium subscription is now active</li>
                <li style="margin-bottom: 8px;">✅ All calculator features are unlocked</li>
                <li style="margin-bottom: 8px;">✅ Export and sharing options are now available</li>
                <li style="margin-bottom: 8px;">✅ Priority support is enabled</li>
                <li>✅ Advanced calculation tools are accessible</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{dashboard_url}}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Go to Dashboard</a>
        </div>
        
        <div style="border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 30px; text-align: center; color: #6c757d; font-size: 14px;">
            <p>If you have any questions, please contact our support team.</p>
            <p>Thank you for choosing {{site_name}}!</p>
        </div>
    </div>
</div>',
            'category' => 'payment',
            'description' => 'Email sent after successful payment confirmation',
            'variables' => json_encode(['transaction_id', 'plan_name', 'amount', 'currency_symbol', 'payment_method', 'payment_date', 'dashboard_url']),
            'is_active' => 1
        ],
    ];

    $inserted = 0;
    $skipped = 0;

    foreach ($templates as $template) {
        // Check if template already exists
        $stmt = $pdo->prepare("SELECT id FROM email_templates WHERE name = ?");
        $stmt->execute([$template['name']]);

        if ($stmt->rowCount() > 0) {
            echo "⚠️  Template '{$template['name']}' already exists, skipping...\n";
            $skipped++;
            continue;
        }

        // Insert template
        $stmt = $pdo->prepare("
            INSERT INTO email_templates 
            (name, subject, content, category, description, variables, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $template['name'],
            $template['subject'],
            $template['content'],
            $template['category'],
            $template['description'],
            $template['variables'],
            $template['is_active']
        ]);

        echo "✅ Created template: {$template['name']}\n";
        $inserted++;
    }

    echo "\n=============================================\n";
    echo "Seeding completed!\n";
    echo "Created: $inserted templates\n";
    echo "Skipped: $skipped templates (already exist)\n";
    echo "=============================================\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
