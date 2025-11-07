<?php

namespace App\Models;

use App\Core\Database;

class Share {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM shares WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByToken($token) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM shares WHERE share_token = ? AND is_active = 1");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO shares (user_id, calculation_id, share_type, share_token, title, description) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['calculation_id'],
            $data['share_type'] ?? 'public',
            $data['share_token'],
            $data['title'],
            $data['description'] ?? ''
        ]);
    }
    
    public function getPublicShares($limit = 10) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT s.*, u.first_name, u.last_name, u.email, ch.calculation_title, ch.calculator_type 
            FROM shares s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN calculation_history ch ON s.calculation_id = ch.id
            WHERE s.share_type = 'public' AND s.is_active = 1 
            ORDER BY s.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getUserShares($userId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT s.*, ch.calculation_title, ch.calculator_type 
            FROM shares s 
            LEFT JOIN calculation_history ch ON s.calculation_id = ch.id
            WHERE s.user_id = ? AND s.is_active = 1 
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function incrementViewCount($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE shares SET view_count = view_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function incrementCommentCount($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE shares SET comment_count = comment_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function decrementCommentCount($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE shares SET comment_count = GREATEST(0, comment_count - 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function delete($userId, $id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE shares SET is_active = 0 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
    
    public static function generateShareToken() {
        $db = Database::getInstance();
        $token = bin2hex(random_bytes(16));
        
        // Check if token exists
        $stmt = $db->getPdo()->prepare("SELECT id FROM shares WHERE share_token = ?");
        $stmt->execute([$token]);
        
        while ($stmt->fetch()) {
            $token = bin2hex(random_bytes(16));
            $stmt->execute([$token]);
        }
        
        return $token;
    }
}
?>
