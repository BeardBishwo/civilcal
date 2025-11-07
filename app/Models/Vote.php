<?php

namespace App\Models;

use App\Core\Database;

class Vote {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM votes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO votes (user_id, comment_id, vote_type) 
            VALUES (?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['comment_id'],
            $data['vote_type']
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM votes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findByUserAndComment($userId, $commentId) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM votes WHERE user_id = ? AND comment_id = ?");
        $stmt->execute([$userId, $commentId]);
        return $stmt->fetch();
    }
}
?>
