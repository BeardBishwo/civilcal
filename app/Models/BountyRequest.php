<?php

namespace App\Models;

use App\Core\Database;

class BountyRequest
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO bounty_requests (
                requester_id, title, description, bounty_amount, status, created_at
            ) VALUES (?, ?, ?, ?, 'open', NOW())
        ");

        $stmt->execute([
            $data['requester_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['bounty_amount']
        ]);

        return $this->db->getPdo()->lastInsertId();
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT br.*, u.username as requester_name 
            FROM bounty_requests br 
            LEFT JOIN users u ON br.requester_id = u.id 
            WHERE br.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? (object) $row : null;
    }
    
    public function getOpenBounties($limit = 20, $offset = 0)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT br.*, u.username as requester_name 
            FROM bounty_requests br 
            LEFT JOIN users u ON br.requester_id = u.id 
            WHERE br.status = 'open' 
            ORDER BY br.created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM bounty_requests 
            WHERE requester_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE bounty_requests SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
