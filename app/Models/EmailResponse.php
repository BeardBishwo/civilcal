<?php

namespace App\Models;

use App\Core\Database;

class EmailResponse {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_responses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO email_responses (thread_id, user_id, message, is_internal_note) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['thread_id'],
            $data['user_id'],
            $data['message'],
            $data['is_internal_note'] ?? false
        ]);
    }
    
    public function getByThread($threadId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT er.*, u.first_name, u.last_name, u.email
            FROM email_responses er
            LEFT JOIN users u ON er.user_id = u.id
            WHERE er.thread_id = ?
            ORDER BY er.created_at ASC
        ");
        $stmt->execute([$threadId]);
        return $stmt->fetchAll();
    }

    // Compatibility wrappers expected by controllers
    public function getResponsesByThread($threadId)
    {
        return $this->getByThread($threadId);
    }

    public function addResponse($data)
    {
        $success = $this->create([
            'thread_id' => $data['thread_id'],
            'user_id' => $data['user_id'],
            'message' => $data['content'] ?? ($data['message'] ?? ''),
            'is_internal_note' => $data['is_internal'] ?? ($data['is_internal_note'] ?? false)
        ]);

        if ($success) {
            // return the created response (best effort)
            $lastId = $this->db->lastInsertId();
            return $this->find($lastId);
        }
        return false;
    }
}
?>
