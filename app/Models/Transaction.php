<?php

namespace App\Models;

use App\Core\Database;

class Transaction
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO user_transactions (
                user_id, amount, type, reference_id, description, created_at
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['user_id'],
            $data['amount'],
            $data['type'],
            $data['reference_id'] ?? null,
            $data['description']
        ]);

        return $this->db->getPdo()->lastInsertId();
    }

    public function getByUserId($userId, $limit = 20, $offset = 0)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM user_transactions 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getTotalEarnings($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT SUM(amount) as total 
            FROM user_transactions 
            WHERE user_id = ? AND amount > 0
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? ($row['total'] ?? 0) : 0;
    }

    public function getTotalSpent($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT SUM(ABS(amount)) as total 
            FROM user_transactions 
            WHERE user_id = ? AND amount < 0
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? ($row['total'] ?? 0) : 0;
    }
}
