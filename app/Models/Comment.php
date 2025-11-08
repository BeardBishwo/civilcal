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
        } catch (\Exception $e) {
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
    
    /**
     * Get a single comment by ID with full data
     */
    public function getCommentById($id) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, u.avatar,
                   (c.upvotes - c.downvotes) as net_votes,
                   c.upvotes, c.downvotes
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new comment (for CommentController)
     */
    public function createComment($shareId, $userId, $content, $parentId = null, $isPrivate = false) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO comments (share_id, user_id, parent_id, content, is_private) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([$shareId, $userId, $parentId, $content, $isPrivate]);
        
        if ($success) {
            // Increment comment count on share
            $shareModel = new \App\Models\Share();
            $shareModel->incrementCommentCount($shareId);
            
            return [
                'id' => $this->db->getPdo()->lastInsertId(),
                'share_id' => $shareId,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'content' => $content,
                'is_private' => $isPrivate,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return null;
    }
    
    /**
     * Get comments for a share with pagination and sorting
     */
    public function getCommentsByShare($shareId, $page = 1, $limit = 20, $sortBy = 'best') {
        $offset = ($page - 1) * $limit;
        
        $orderBy = 'net_votes DESC, c.created_at DESC';
        switch ($sortBy) {
            case 'new':
                $orderBy = 'c.created_at DESC';
                break;
            case 'old':
                $orderBy = 'c.created_at ASC';
                break;
            case 'top':
                $orderBy = 'c.upvotes DESC';
                break;
        }
        
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, u.avatar,
                   (c.upvotes - c.downvotes) as net_votes
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.share_id = ? AND c.parent_id IS NULL AND c.is_deleted = 0
            ORDER BY $orderBy
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$shareId, $limit, $offset]);
        $comments = $stmt->fetchAll();
        
        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getCommentReplies($comment['id']);
        }
        
        return $comments;
    }
    
    /**
     * Get comment count for a share
     */
    public function getCommentCountByShare($shareId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count 
            FROM comments 
            WHERE share_id = ? AND parent_id IS NULL AND is_deleted = 0
        ");
        $stmt->execute([$shareId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Update a comment
     */
    public function updateComment($commentId, $content) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE comments 
            SET content = ?, is_edited = 1, edited_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([$content, $commentId]);
    }
    
    /**
     * Soft delete a comment
     */
    public function softDeleteComment($commentId) {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE comments 
            SET is_deleted = 1, deleted_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([$commentId]);
    }
    
    /**
     * Get a complete comment thread (parent + all replies)
     */
    public function getCommentThread($commentId) {
        // Get the main comment
        $mainComment = $this->getCommentById($commentId);
        if (!$mainComment) {
            return null;
        }
        
        // Get all replies recursively
        $mainComment['replies'] = $this->getCommentReplies($commentId);
        
        return $mainComment;
    }
    
    /**
     * Get replies for a comment
     */
    private function getCommentReplies($commentId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, u.avatar,
                   (c.upvotes - c.downvotes) as net_votes
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.parent_id = ? AND c.is_deleted = 0
            ORDER BY c.created_at ASC
        ");
        
        $stmt->execute([$commentId]);
        $replies = $stmt->fetchAll();
        
        // Get nested replies for each reply
        foreach ($replies as &$reply) {
            $reply['replies'] = $this->getCommentReplies($reply['id']);
        }
        
        return $replies;
    }
    
    /**
     * Check if user has reported a comment
     */
    public function getUserReportForComment($commentId, $userId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM comment_reports 
            WHERE comment_id = ? AND user_id = ?
        ");
        $stmt->execute([$commentId, $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Report a comment
     */
    public function reportComment($commentId, $userId, $reason, $description) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO comment_reports (comment_id, user_id, reason, description) 
            VALUES (?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([$commentId, $userId, $reason, $description]);
        
        return $success ? $this->db->getPdo()->lastInsertId() : false;
    }
    
    /**
     * Get user's comments with pagination
     */
    public function getUserComments($userId, $page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, s.title as share_title, s.share_token,
                   (c.upvotes - c.downvotes) as net_votes
            FROM comments c
            LEFT JOIN shares s ON c.share_id = s.id
            WHERE c.user_id = ? AND c.is_deleted = 0
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get user's total comment count
     */
    public function getUserCommentCount($userId) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as count 
            FROM comments 
            WHERE user_id = ? AND is_deleted = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
