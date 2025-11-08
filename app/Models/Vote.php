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
    
    /**
     * Get user votes for multiple comments (for CommentController)
     */
    public function getUserVotesForComments($commentIds, $userId) {
        if (empty($commentIds)) {
            return [];
        }
        
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($commentIds) - 1) . '?';
        
        $stmt = $this->db->getPdo()->prepare("
            SELECT comment_id, vote_type 
            FROM votes 
            WHERE user_id = ? AND comment_id IN ($placeholders)
        ");
        
        $params = array_merge([$userId], $commentIds);
        $stmt->execute($params);
        
        $votes = [];
        while ($row = $stmt->fetch()) {
            $votes[$row['comment_id']] = $row['vote_type'];
        }
        
        return $votes;
    }
    
    /**
     * Vote on a comment (for CommentController)
     */
    public function voteOnComment($commentId, $userId, $voteType) {
        $db = $this->db->getPdo();
        
        try {
            $db->beginTransaction();
            
            // Check for existing vote
            $stmt = $db->prepare("SELECT id, vote_type FROM votes WHERE user_id = ? AND comment_id = ?");
            $stmt->execute([$userId, $commentId]);
            $existingVote = $stmt->fetch();
            
            if ($existingVote) {
                if ($voteType === null) {
                    // Remove vote
                    $stmt = $db->prepare("DELETE FROM votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                    
                    // Update comment vote count
                    $this->updateCommentVoteCount($commentId, $existingVote['vote_type'], -1);
                } elseif ($existingVote['vote_type'] === $voteType) {
                    // Same vote, remove it
                    $stmt = $db->prepare("DELETE FROM votes WHERE id = ?");
                    $stmt->execute([$existingVote['id']]);
                    
                    // Update comment vote count
                    $this->updateCommentVoteCount($commentId, $voteType, -1);
                } else {
                    // Change vote
                    $stmt = $db->prepare("UPDATE votes SET vote_type = ? WHERE id = ?");
                    $stmt->execute([$voteType, $existingVote['id']]);
                    
                    // Update comment vote count
                    $this->updateCommentVoteCount($commentId, $voteType, 1);
                    $this->updateCommentVoteCount($commentId, $existingVote['vote_type'], -1);
                }
            } else {
                if ($voteType !== null) {
                    // New vote
                    $stmt = $db->prepare("INSERT INTO votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
                    $stmt->execute([$userId, $commentId, $voteType]);
                    
                    // Update comment vote count
                    $this->updateCommentVoteCount($commentId, $voteType, 1);
                }
            }
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            error_log("Vote on comment error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's vote for a specific comment (for CommentController)
     */
    public function getUserVoteForComment($commentId, $userId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM votes 
            WHERE user_id = ? AND comment_id = ?
        ");
        $stmt->execute([$userId, $commentId]);
        return $stmt->fetch();
    }
    
    /**
     * Update comment vote count
     */
    private function updateCommentVoteCount($commentId, $voteType, $change) {
        $field = $voteType === 'upvote' ? 'upvotes' : 'downvotes';
        $stmt = $this->db->getPdo()->prepare("
            UPDATE comments 
            SET $field = GREATEST(0, $field + ?) 
            WHERE id = ?
        ");
        return $stmt->execute([$change, $commentId]);
    }
}
?>
