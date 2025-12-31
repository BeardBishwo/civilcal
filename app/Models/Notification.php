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

    public function create($userId, $message, $link = null)
    {
        $stmt = $this->db->getPdo()->prepare("INSERT INTO notifications (user_id, message, link, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$userId, $message, $link]);
    }

    public function getUnread($userId)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
    
    public function markAllAsRead($userId)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
