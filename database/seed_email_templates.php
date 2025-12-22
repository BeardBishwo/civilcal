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
            'name' => 'Calculation Help',
            'subject' => 'Help with {{calculation_type}} Calculations',
            'content' => '<p>Hello {{user_name}},</p>
<p>Thank you for your question about <strong>{{calculation_type}}</strong> calculations.</p>
<p>Here are some helpful resources:</p>
<ul>
    <li>Documentation: {{doc_link}}</li>
    <li>Video Tutorial: {{video_link}}</li>
    <li>Example: {{example_link}}</li>
</ul>
<p>If you need additional assistance, please feel free to reply to this email.</p>
<p>Best regards,<br>Technical Support Team</p>',
            'category' => 'technical',
            'description' => 'Template for providing calculation help and resources',
            'variables' => json_encode(['user_name', 'calculation_type', 'doc_link', 'video_link', 'example_link']),
            'is_active' => 1
        ]
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
