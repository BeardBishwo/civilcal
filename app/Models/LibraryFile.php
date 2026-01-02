<?php

namespace App\Models;

use App\Core\Database;

class LibraryFile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO library_files (
                uploader_id, title, description, file_path, file_type, 
                file_size_kb, price_coins, status, file_hash, preview_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['uploader_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['file_path'],
            $data['file_type'],
            $data['file_size_kb'] ?? 0,
            $data['price_coins'] ?? 50,
            $data['status'] ?? 'pending',
            $data['file_hash'] ?? '',
            $data['preview_path'] ?? null
        ]);

        return $this->db->getPdo()->lastInsertId();
    }

    public function findByHash($hash)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT id, uploader_id FROM library_files WHERE file_hash = ?");
        $stmt->execute([$hash]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT f.*, u.username as uploader_name,
                   (SELECT AVG(rating) FROM library_reviews WHERE file_id = f.id) as avg_rating,
                   (SELECT COUNT(*) FROM library_reviews WHERE file_id = f.id) as review_count
            FROM library_files f
            JOIN users u ON f.uploader_id = u.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function addReview($fileId, $reviewerId, $rating, $comment)
    {
        // Ideally check if user downloaded first, but we'll assume controller checked permissions or simple logic for now
        $stmt = $this->db->getPdo()->prepare("INSERT INTO library_reviews (file_id, reviewer_id, rating, comment) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?, comment = ?");
        return $stmt->execute([$fileId, $reviewerId, $rating, $comment, $rating, $comment]);
    }

    public function report($fileId, $reporterId, $reason)
    {
        $stmt = $this->db->getPdo()->prepare("INSERT IGNORE INTO library_reports (file_id, reporter_id, reason) VALUES (?, ?, ?)");
        if ($stmt->execute([$fileId, $reporterId, $reason]) && $stmt->rowCount() > 0) {
            // Increment count
            $upd = $this->db->getPdo()->prepare("UPDATE library_files SET report_count = report_count + 1 WHERE id = ?");
            $upd->execute([$fileId]);
            
            // Check if limit reached
            $chk = $this->db->getPdo()->prepare("SELECT report_count FROM library_files WHERE id = ?");
            $chk->execute([$fileId]);
            $count = $chk->fetchColumn();
            
            if ($count >= 3) {
                 $this->db->getPdo()->prepare("UPDATE library_files SET status = 'flagged' WHERE id = ?")->execute([$fileId]);
            }
            return true;
        }
        return false;
    }

    public function getPending()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT lf.*, u.username as uploader_name, u.email as uploader_email
            FROM library_files lf 
            LEFT JOIN users u ON lf.uploader_id = u.id 
            WHERE lf.status = 'pending' 
            ORDER BY lf.created_at ASC
        ");
        return $stmt->fetchAll();
    }

    public function getKeyResources($userId = null, $type = null, $limit = 20, $offset = 0)
    {
        $sql = "
            SELECT lf.*, u.username as uploader_name,
                   CASE WHEN lu.id IS NOT NULL THEN 1 ELSE 0 END as is_unlocked
            FROM library_files lf 
            LEFT JOIN users u ON lf.uploader_id = u.id 
            LEFT JOIN library_unlocks lu ON lf.id = lu.file_id AND lu.user_id = ?
            WHERE lf.status = 'approved'
        ";
        $params = [$userId];

        if ($type) {
            $sql .= " AND lf.file_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY lf.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        
        $pIdx = 2;
        if ($type) {
            $stmt->bindValue($pIdx++, $type, \PDO::PARAM_STR);
        }
        
        $stmt->bindValue($pIdx++, (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue($pIdx++, (int)$offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function approve($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE library_files SET status = 'approved', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function reject($id, $reason = '')
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE library_files SET status = 'rejected', admin_note = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$reason, $id]);
    }

    public function incrementDownloads($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE library_files SET downloads_count = downloads_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
