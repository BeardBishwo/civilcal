<?php

namespace App\Models;

use App\Core\Database;

class BountySubmission
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO bounty_submissions (
                bounty_id, uploader_id, file_path, admin_status, client_status, created_at, file_hash, preview_path
            ) VALUES (?, ?, ?, 'pending', 'pending', NOW(), ?, ?)
        ");

        $stmt->execute([
            $data['bounty_id'],
            $data['uploader_id'],
            $data['file_path'],
            $data['file_hash'] ?? '',
            $data['preview_path'] ?? null
        ]);

        return $this->db->getPdo()->lastInsertId();
    }

    public function findByHash($hash)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT id, uploader_id FROM bounty_submissions WHERE file_hash = ?");
        $stmt->execute([$hash]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT bs.*, u.username as uploader_name, br.bounty_amount as reward
            FROM bounty_submissions bs
            LEFT JOIN users u ON bs.uploader_id = u.id
            LEFT JOIN bounty_requests br ON bs.bounty_id = br.id
            WHERE bs.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? (object) $row : null;
    }

    public function getByBountyId($bountyId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT bs.*, u.username as uploader_name 
            FROM bounty_submissions bs
            LEFT JOIN users u ON bs.uploader_id = u.id
            WHERE bs.bounty_id = ? 
            ORDER BY bs.created_at DESC
        ");
        $stmt->execute([$bountyId]);
        return $stmt->fetchAll();
    }

    public function updateAdminStatus($id, $status, $reason = null)
    {
        // If rejected, maybe set rejection reason too, but schema has it separate which might be for client rejection.
        // Let's assume rejection_reason is general.
        $sql = "UPDATE bounty_submissions SET admin_status = ?, updated_at = NOW()";
        $params = [$status];
        
        if ($reason) {
            $sql .= ", rejection_reason = ?";
            $params[] = $reason;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function updateClientStatus($id, $status, $reason = null)
    {
        $sql = "UPDATE bounty_submissions SET client_status = ?, updated_at = NOW()";
        $params = [$status];
        
        if ($reason) {
            $sql .= ", rejection_reason = ?";
            $params[] = $reason;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public function getPendingAdminReview()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT bs.*, br.title as bounty_title
            FROM bounty_submissions bs
            JOIN bounty_requests br ON bs.bounty_id = br.id
            WHERE bs.admin_status = 'pending'
        ");
        return $stmt->fetchAll();
    }
}
