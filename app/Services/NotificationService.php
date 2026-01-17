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
        // Allow pre-fetched email to avoid N+1 queries
        $userEmail = $options['user_email'] ?? null;

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
            $this->sendEmail($userId, $type, $title, $message, array_merge($options, ['user_email' => $userEmail]));
        }

        return true;
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulk($userIds, $type, $title, $message, $options = [])
    {
        if (empty($userIds)) return [];

        // Pre-fetch all emails to avoid N+1 queries in the loop
        $db = \App\Core\Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $stmt = $db->prepare("SELECT id, email, first_name, last_name FROM users WHERE id IN ($placeholders)");
        $stmt->execute($userIds);
        $users = $stmt->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);

        $results = [];
        foreach ($userIds as $userId) {
            $userOptions = $options;
            if (isset($users[$userId])) {
                $userOptions['user_email'] = $users[$userId]['email'];
                $userOptions['user_data'] = $users[$userId];
            }
            $results[$userId] = $this->send($userId, $type, $title, $message, $userOptions);
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
            // Get user data (use pre-fetched if available)
            $user = $options['user_data'] ?? null;
            $email = $options['user_email'] ?? null;

            if (!$user || !$email) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                $email = $user['email'] ?? null;
            }

            if (!$user || !$email) {
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
                'to' => $email,
                'to_name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                'subject' => $title,
                'template' => 'notification',
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'icon' => $options['icon'] ?? '🔔',
                    'actionUrl' => $options['action_url'] ?? $options['actionUrl'] ?? null,
                    'actionText' => $options['action_text'] ?? null,
                    'image_url' => $options['image_url'] ?? null, // Pass Billboard Image
                    'metadata' => $options['metadata'] ?? null,
                    'siteName' => \App\Services\SettingsService::get('site_name', 'Bishwo Calculator'),
                    'baseUrl' => app_base_url()
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
    /**
     * Broadcast notification to all users (Scalable)
     */
    public function broadcast($type, $title, $message, $options = [])
    {
        $targetGroup = $options['target_group'] ?? 'all';
        $targetValue = $options['target_value'] ?? null;
        $expiresAt = $options['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+7 days'));

        return $this->notificationModel->createGlobal(
            $title,
            $message,
            $type,
            $options['icon'] ?? 'fas fa-info-circle',
            $targetGroup,
            $targetValue,
            $expiresAt
        );
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
