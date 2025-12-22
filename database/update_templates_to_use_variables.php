<?php
/**
 * Update existing email templates to use {{site_name}} variable
 */

echo "=============================================\n";
echo "Updating Email Templates\n";
echo "=============================================\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=bishwo_calculator;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $updates = [
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
<p>Best regards,<br>{{site_name}} Team</p>'
        ],
        [
            'name' => 'Support Inquiry Response',
            'subject' => 'Re: {{inquiry_subject}}',
            'content' => '<p>Hello {{user_name}},</p>
<p>Thank you for contacting {{site_name}} support.</p>
<p>We have received your inquiry regarding: <strong>{{inquiry_subject}}</strong></p>
<p>Our support team is reviewing your request and will get back to you within 24 hours.</p>
<p>Reference Number: #{{ticket_id}}</p>
<p>Best regards,<br>Support Team</p>'
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
<p>Best regards,<br>Support Team</p>'
        ]
    ];

    $updated = 0;
    foreach ($updates as $update) {
        $stmt = $pdo->prepare("UPDATE email_templates SET subject = ?, content = ?, updated_at = NOW() WHERE name = ?");
        $result = $stmt->execute([$update['subject'], $update['content'], $update['name']]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo "✅ Updated: {$update['name']}\n";
            $updated++;
        } else {
            echo "⚠️  Skipped: {$update['name']} (not found or no changes)\n";
        }
    }

    echo "\n=============================================\n";
    echo "✅ Complete!\n";
    echo "=============================================\n";
    echo "Updated: $updated templates\n";
    echo "\nAll templates now use {{site_name}} variable!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
