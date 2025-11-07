<?php

namespace App\Models;

use App\Core\Database;

class Comment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO comments (user_id, share_id, parent_id, content) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['share_id'],
            $data['parent_id'] ?? null,
            $data['content']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE comments 
            SET content = ?, is_edited = 1, edited_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([$data['content'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getTopLevelComments($shareId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, u.avatar,
                   (c.upvotes - c.downvotes) as net_votes
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.share_id = ? AND c.parent_id IS NULL
            ORDER BY net_votes DESC, c.created_at DESC
        ");
        $stmt->execute([$shareId]);
        $comments = $stmt->fetchAll();
        
        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getReplies($comment['id']);
        }
        
        return $comments;
    }
    
    public function getReplies($parentId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, u.avatar,
                   (c.upvotes - c.downvotes) as net_votes
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.parent_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$parentId]);
        $replies = $stmt->fetchAll();
        
        // Get nested replies
        foreach ($replies as &$reply) {
            $reply['replies'] = $this->getReplies($reply['id']);
        }
        
        return $replies;
    }
    
    public function getUserVote($userId, $commentId) {
        $stmt = $this->db->getPdo()->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND comment_id = ?");
        $stmt->execute([$userId, $commentId]);
        $result = $stmt->fetch();
        return $result ? $result['vote_type'] : null;
    }
    
    public function addVote($userId, $commentId, $voteType) {
        $db = $this->db->getPdo();
        
        try {
            $db->beginTransaction();
            
            // Check for existing vote
            $stmt = $db->prepare("SELECT id, vote_type FROM votes WHERE user_id = ? AND comment_id = ?");
            $stmt->execute([$userId, $commentId]);
            $existingVote = $stmt->fetch();
            
            if ($existingVote) {
                if ($existingVote['vote_type'] === $voteType) {
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
                // New vote
                $stmt = $db->prepare("INSERT INTO votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $commentId, $voteType]);
                
                // Update comment vote count
                $this->updateCommentVoteCount($commentId, $voteType, 1);
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    
    private function updateCommentVoteCount($commentId, $voteType, $change) {
        $field = $voteType === 'up' ? 'upvotes' : 'downvotes';
        $stmt = $this->db->getPdo()->prepare("
            UPDATE comments 
            SET $field = GREATEST(0, $field + ?) 
            WHERE id = ?
        ");
        return $stmt->execute([$change, $commentId]);
    }
    
    public function getNetVotes($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT upvotes, downvotes FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? ($result['upvotes'] - $result['downvotes']) : 0;
    }
}
?>
