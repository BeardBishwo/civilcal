<?php
namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * Create a new notification
     */
    public function createNotification($userId, $type, $title, $message, $options = [])
    {
        $sql = "INSERT INTO {$this->table} (
            user_id, type, title, message, action_url, action_text, 
            icon, priority, metadata, expires_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            $options['action_url'] ?? null,
            $options['action_text'] ?? null,
            $options['icon'] ?? 'fa-bell',
            $options['priority'] ?? 'normal',
            isset($options['metadata']) ? json_encode($options['metadata']) : null,
            $options['expires_at'] ?? null
        ]);
    }

    /**
     * Get user notifications with filters
     */
    public function getUserNotifications($userId, $filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $params = [$userId];

        // Filter by read status
        if (isset($filters['is_read'])) {
            $sql .= " AND is_read = ?";
            $params[] = $filters['is_read'];
        }

        // Filter by type
        if (isset($filters['type'])) {
            $sql .= " AND type = ?";
            $params[] = $filters['type'];
        }

        // Filter by archived
        if (!isset($filters['include_archived']) || !$filters['include_archived']) {
            $sql .= " AND is_archived = 0";
        }

        // Exclude expired
        $sql .= " AND (expires_at IS NULL OR expires_at > NOW())";

        // Order by created date
        $sql .= " ORDER BY created_at DESC";

        // Limit
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND is_read = 0 AND is_archived = 0
                AND (expires_at IS NULL OR expires_at > NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW() WHERE id = ?";
        $params = [$notificationId];

        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW() 
                WHERE user_id = ? AND is_read = 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    /**
     * Delete notification
     */
    public function delete($notificationId, $userId = null)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $params = [$notificationId];

        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Archive notification
     */
    public function archive($notificationId, $userId = null)
    {
        $sql = "UPDATE {$this->table} SET is_archived = 1 WHERE id = ?";
        $params = [$notificationId];

        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get notification by ID
     */
    public function find($id, $userId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $params = [$id];

        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Legacy methods for backward compatibility
    public function getByUser($userId, $limit = 50, $offset = 0)
    {
        return $this->getUserNotifications($userId, ['limit' => $limit]);
    }

    public function getUnreadByUser($userId, $limit = 50, $offset = 0)
    {
        return $this->getUserNotifications($userId, ['is_read' => 0, 'limit' => $limit]);
    }

    public function getCountByUser($userId)
    {
        return $this->getUnreadCount($userId);
    }
}
