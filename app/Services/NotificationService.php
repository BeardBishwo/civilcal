<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificationService
{
    private $notificationModel;
    private $preferenceModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
        $this->preferenceModel = new NotificationPreference();
    }

    /**
     * Send notification to a user
     */
    public function send($userId, $type, $title, $message, $options = [])
    {
        // Create in-app notification
        $created = $this->notificationModel->createNotification(
            $userId,
            $type,
            $title,
            $message,
            $options
        );

        if (!$created) {
            return false;
        }

        // Check if should send email
        if ($this->preferenceModel->shouldSendEmail($userId, $type)) {
            $this->sendEmail($userId, $type, $title, $message, $options);
        }

        return true;
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulk($userIds, $type, $title, $message, $options = [])
    {
        $results = [];
        foreach ($userIds as $userId) {
            $results[$userId] = $this->send($userId, $type, $title, $message, $options);
        }
        return $results;
    }

    /**
     * Send notification to all admins
     */
    public function sendToAdmins($type, $title, $message, $options = [])
    {
        // Get all admin users
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin'");
        $stmt->execute();
        $admins = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return $this->sendBulk($admins, $type, $title, $message, $options);
    }

    /**
     * Send email notification
     */
    private function sendEmail($userId, $type, $title, $message, $options = [])
    {
        try {
            // Get user email
            $db = \App\Core\Database::getInstance();
            $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !$user['email']) {
                return false;
            }

            // Check if EmailService exists
            if (!class_exists('App\Services\EmailService')) {
                error_log("EmailService not found, skipping email notification");
                return false;
            }

            $emailService = new EmailService();
            
            // Prepare email data
            $emailData = [
                'to' => $user['email'],
                'to_name' => trim($user['first_name'] . ' ' . $user['last_name']),
                'subject' => $title,
                'template' => 'notification',
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'icon' => $options['icon'] ?? '🔔',
                    'actionUrl' => $options['action_url'] ?? null,
                    'actionText' => $options['action_text'] ?? null,
                    'metadata' => $options['metadata'] ?? null,
                    'siteName' => \App\Services\SettingsService::get('site_name', 'Bishwo Calculator'),
                    'baseUrl' => rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/')
                ]
            ];

            return $emailService->sendTemplateEmail($emailData);
        } catch (\Exception $e) {
            error_log("Failed to send email notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast notification to all users
     */
    public function broadcast($type, $title, $message, $options = [])
    {
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users");
        $stmt->execute();
        $userIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return $this->sendBulk($userIds, $type, $title, $message, $options);
    }

    /**
     * Clean up old notifications
     */
    public function cleanup($days = 30)
    {
        $db = \App\Core\Database::getInstance();
        $sql = "DELETE FROM notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
                AND is_archived = 1";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([$days]);
    }
}
