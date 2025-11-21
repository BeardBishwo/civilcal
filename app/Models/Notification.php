<?php
namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected $table = 'admin_notifications';

    /**
     * Get all notifications for a user
     */
    public function getByUser($userId, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? OR user_id IS NULL 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadByUser($userId, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new notification
     */
    public function create($userId, $title, $message, $type = 'info', $data = [])
    {
        $dataJson = json_encode($data);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (user_id, title, message, type, data, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $title, $message, $type, $dataJson]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id, $userId = null)
    {
        if ($userId) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND user_id = ?
            ");
            return $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Get notification count for a user
     */
    public function getCountByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    /**
     * Delete a notification
     */
    public function delete($id, $userId = null)
    {
        if ($userId) {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        }
    }
}