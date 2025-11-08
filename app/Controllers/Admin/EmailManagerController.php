<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class EmailManagerController extends Controller
{
    public function index()
    {
        $templates = $this->getEmailTemplates();
        $stats = $this->getEmailStats();
        
        // Load the email management view
        include __DIR__ . '/../../Views/admin/email/index.php';
    }

    public function sendTestEmail()
    {
        if ($_POST && isset($_POST['email'])) {
            $email = $_POST['email'];
            
            // Send test email logic would go here
            $result = $this->sendEmail($email, 'Test Email from Bishwo Calculator', 'This is a test email from your admin panel.');
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'No email provided']);
    }

    public function saveTemplate()
    {
        if ($_POST && isset($_POST['template_id'])) {
            $templateId = $_POST['template_id'];
            $content = $_POST['content'];
            $subject = $_POST['subject'];
            
            // Save template logic would go here
            $result = $this->updateTemplate($templateId, $subject, $content);
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    private function getEmailTemplates()
    {
        // Mock data for email templates
        return [
            [
                'id' => 1,
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Bishwo Calculator!',
                'description' => 'Sent when new user registers',
                'last_updated' => '2024-01-15',
                'is_active' => true
            ],
            [
                'id' => 2,
                'name' => 'Password Reset',
                'subject' => 'Reset Your Password',
                'description' => 'Sent when user requests password reset',
                'last_updated' => '2024-01-10',
                'is_active' => true
            ],
            [
                'id' => 3,
                'name' => 'Calculation Shared',
                'subject' => 'Someone shared a calculation with you',
                'description' => 'Sent when user shares calculation',
                'last_updated' => '2024-01-08',
                'is_active' => true
            ]
        ];
    }

    private function getEmailStats()
    {
        return [
            'sent_today' => 45,
            'sent_week' => 320,
            'sent_month' => 1250,
            'success_rate' => 98.5
        ];
    }

    private function sendEmail($to, $subject, $message)
    {
        // Email sending logic would go here
        return ['success' => true, 'message' => 'Test email sent successfully'];
    }

    private function updateTemplate($id, $subject, $content)
    {
        // Template update logic would go here
        return ['success' => true, 'message' => 'Template updated successfully'];
    }
}
?>
