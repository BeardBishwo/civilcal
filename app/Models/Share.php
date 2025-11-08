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
    
    /**
     * Get a share by ID with user verification (for ShareController)
     */
    public function getShareById($id, $userId = null) {
        if ($userId) {
            // Authenticated access - verify ownership
            $stmt = $this->db->getPdo()->prepare("
                SELECT s.*, u.first_name, u.last_name, u.email, ch.calculation_title, ch.calculator_type 
                FROM shares s 
                LEFT JOIN users u ON s.user_id = u.id 
                LEFT JOIN calculation_history ch ON s.calculation_id = ch.id
                WHERE s.id = ? AND s.user_id = ? AND s.is_active = 1
            ");
            $stmt->execute([$id, $userId]);
        } else {
            // Public access
            $stmt = $this->db->getPdo()->prepare("
                SELECT s.*, u.first_name, u.last_name, u.email, ch.calculation_title, ch.calculator_type 
                FROM shares s 
                LEFT JOIN users u ON s.user_id = u.id 
                LEFT JOIN calculation_history ch ON s.calculation_id = ch.id
                WHERE s.id = ? AND s.is_active = 1
            ");
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }
    
    /**
     * Get a share by token (for ShareController)
     */
    public function getShareByToken($token) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT s.*, u.first_name, u.last_name, u.email, ch.calculation_title, ch.calculator_type 
            FROM shares s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN calculation_history ch ON s.calculation_id = ch.id
            WHERE s.share_token = ? AND s.is_active = 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    public function findByToken($token) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM shares WHERE share_token = ? AND is_active = 1");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new share (for ShareController)
     */
    public function createShare($calculationId, $userId, $isPublic, $title, $description, $expiryDate = null) {
        // Generate unique token
        $token = $this->generateUniqueToken();
        
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO shares (
                calculation_id, user_id, share_token, title, description, 
                is_public, expires_at, view_count, comment_count
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)
        ");
        
        $success = $stmt->execute([
            $calculationId,
            $userId,
            $token,
            $title,
            $description,
            $isPublic ? 1 : 0,
            $expiryDate
        ]);
        
        if ($success) {
            return [
                'id' => $this->db->getPdo()->lastInsertId(),
                'token' => $token,
                'calculation_id' => $calculationId,
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'is_public' => $isPublic,
                'expires_at' => $expiryDate,
                'view_count' => 0,
                'comment_count' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return null;
    }
    
    /**
     * Get shares by user (for ShareController)
     */
    public function getSharesByUser($userId) {
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
    
    /**
     * Update share (for ShareController)
     */
    public function updateShare($shareId, $updates) {
        $allowedFields = ['title', 'description', 'is_public', 'expires_at', 'password'];
        $setParts = [];
        $params = [];
        
        foreach ($updates as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $setParts[] = "$field = ?";
                $params[] = $value;
            }
        }
        
        if (empty($setParts)) {
            return false;
        }
        
        $params[] = $shareId;
        
        $stmt = $this->db->getPdo()->prepare("
            UPDATE shares 
            SET " . implode(', ', $setParts) . ", updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute($params);
    }
    
    /**
     * Delete share (for ShareController)
     */
    public function deleteShare($shareId, $userId) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE shares 
            SET is_active = 0, deleted_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$shareId, $userId]);
    }
    
    /**
     * Get share statistics (for ShareController)
     */
    public function getShareStats($shareId, $userId) {
        // Verify ownership
        $share = $this->getShareById($shareId, $userId);
        if (!$share) {
            return null;
        }
        
        $db = $this->db->getPdo();
        
        // Total views
        $stmt = $db->prepare("SELECT view_count FROM shares WHERE id = ?");
        $stmt->execute([$shareId]);
        $viewCount = $stmt->fetch()['view_count'];
        
        // Total comments
        $stmt = $db->prepare("SELECT comment_count FROM shares WHERE id = ?");
        $stmt->execute([$shareId]);
        $commentCount = $stmt->fetch()['comment_count'];
        
        // Recent views (last 30 days)
        $stmt = $db->prepare("
            SELECT COUNT(*) as recent_views 
            FROM share_views 
            WHERE share_id = ? AND viewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$shareId]);
        $recentViews = $stmt->fetch()['recent_views'];
        
        // Top referrers
        $stmt = $db->prepare("
            SELECT referrer, COUNT(*) as visits 
            FROM share_views 
            WHERE share_id = ? AND referrer IS NOT NULL
            GROUP BY referrer 
            ORDER BY visits DESC 
            LIMIT 5
        ");
        $stmt->execute([$shareId]);
        $topReferrers = $stmt->fetchAll();
        
        return [
            'total_views' => $viewCount,
            'total_comments' => $commentCount,
            'recent_views' => $recentViews,
            'top_referrers' => $topReferrers,
            'share_info' => $share
        ];
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
    
    /**
     * Generate unique share token
     */
    private function generateUniqueToken() {
        do {
            $token = bin2hex(random_bytes(16));
            $stmt = $this->db->getPdo()->prepare("SELECT id FROM shares WHERE share_token = ?");
            $stmt->execute([$token]);
        } while ($stmt->fetch());
        
        return $token;
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
