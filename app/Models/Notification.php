<?php

namespace App\Models;

use App\Core\Database;

class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($userId, $title, $message, $link = null, $type = 'info')
    {
        $stmt = $this->db->getPdo()->prepare("INSERT INTO notifications (user_id, title, message, action_url, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$userId, $title, $message, $link, $type]);
    }

    public function getUnread($userId)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function markAllAsRead($userId)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function getCountByUser($userId)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getTotalCountByUser($userId)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getByUser($userId, $limit = 20, $offset = 0)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUnreadByUser($userId, $limit = 20, $offset = 0)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createNotification($userId, $title, $message, $type = 'info', $data = [])
    {
        $stmt = $this->db->getPdo()->prepare("INSERT INTO notifications (user_id, title, message, type, metadata, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $userId,
            $title,
            $message,
            $type,
            !empty($data) ? json_encode($data) : null
        ]);
    }

    public function createGlobal($title, $message, $type = 'info', $icon = 'fas fa-bell', $targetGroup = 'all', $targetValue = null, $expiresAt = null, $imageUrl = null, $scheduledAt = null)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO global_notifications 
            (title, message, type, icon, target_group, target_value, expires_at, image_url, scheduled_at, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$title, $message, $type, $icon, $targetGroup, $targetValue, $expiresAt, $imageUrl, $scheduledAt]);
    }

    public function getGlobalUnread($userId, $userRole = 'user', $userPlanId = null)
    {
        // Fetch active global notifications that the user hasn't read
        $sql = "
            SELECT g.*, 'global' as source 
            FROM global_notifications g
            WHERE g.is_active = 1
            AND (g.expires_at IS NULL OR g.expires_at > NOW())
            AND (g.scheduled_at IS NULL OR g.scheduled_at <= NOW())
            AND (
                g.target_group = 'all' 
                OR (g.target_group = 'role' AND g.target_value = ?)
                OR (g.target_group = 'plan' AND g.target_value = ?)
            )
            AND NOT EXISTS (
                SELECT 1 FROM global_notification_reads r 
                WHERE r.notification_id = g.id AND r.user_id = ?
            )
            ORDER BY g.created_at DESC
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$userRole, $userPlanId, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markGlobalAsRead($notificationId, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("INSERT IGNORE INTO global_notification_reads (user_id, notification_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $notificationId]);
    }

    /** 
     * @deprecated Use createGlobal for scalable broadcasts
     */
    public function broadcast($title, $message, $type = 'info', $actionUrl = '#')
    {
        return $this->createGlobal($title, $message, $type, 'fas fa-bullhorn', 'all');
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function deleteGlobal($id)
    {
        // Also delete reads? Yes, cascading would be better but manual for now.
        $this->db->getPdo()->prepare("DELETE FROM global_notification_reads WHERE notification_id = ?")->execute([$id]);

        $stmt = $this->db->getPdo()->prepare("DELETE FROM global_notifications WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
