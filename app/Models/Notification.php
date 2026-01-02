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

    public function delete($id, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}
