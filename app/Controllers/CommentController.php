<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Models\Vote;
use App\Models\Share;
use Exception;

/**
 * Comment Controller
 * Handles Reddit-style comments with nested replies and voting
 */
class CommentController extends Controller
{
    private $commentModel;
    private $voteModel;
    private $shareModel;
    
    public function __construct() {
        parent::__construct();
        $this->commentModel = new Comment();
        $this->voteModel = new Vote();
        $this->shareModel = new Share();
    }
    
    /**
     * Create a new comment
     * POST /comments/create
     */
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $shareId = $input['share_id'] ?? null;
        $content = trim($input['content'] ?? '');
        $parentId = $input['parent_id'] ?? null; // For nested replies
        $isPrivate = filter_var($input['is_private'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        if (!$shareId || !$content) {
            $this->json(['success' => false, 'message' => 'Share ID and content are required'], 400);
            return;
        }
        
        if (strlen($content) < 3) {
            $this->json(['success' => false, 'message' => 'Comment content must be at least 3 characters'], 400);
            return;
        }
        
        if (strlen($content) > 5000) {
            $this->json(['success' => false, 'message' => 'Comment content cannot exceed 5000 characters'], 400);
            return;
        }
        
        // Verify the share exists and is accessible
        try {
            // If parent comment is provided, verify it belongs to the same share
            if ($parentId) {
                $parentComment = $this->commentModel->getCommentById($parentId);
                if (!$parentComment || $parentComment['share_id'] != $shareId) {
                    $this->json(['success' => false, 'message' => 'Invalid parent comment'], 400);
                    return;
                }
            }
            
            // Create the comment
            $comment = $this->commentModel->createComment(
                $shareId,
                $this->getUser()['id'],
                $content,
                $parentId,
                $isPrivate
            );
            
            if ($comment) {
                // Get the full comment with user data
                $fullComment = $this->commentModel->getCommentById($comment['id']);
                
                $this->json([
                    'success' => true,
                    'message' => 'Comment created successfully',
                    'data' => [
                        'comment' => $fullComment,
                        'reply_count' => $fullComment['reply_count'] ?? 0,
                        'upvotes' => 0,
                        'downvotes' => 0,
                        'user_vote' => 0
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create comment'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Comment creation error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while creating the comment'], 500);
        }
    }
    
    /**
     * Get comments for a share
     * GET /comments/{shareId}
     */
    public function getComments($shareId) {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
            $sortBy = $_GET['sort'] ?? 'best'; // 'new', 'old', 'top', 'best'
            
            // Verify share exists
            $share = $this->shareModel->getShareById($shareId);
            if (!$share) {
                $this->json(['success' => false, 'message' => 'Share not found'], 404);
                return;
            }
            
            // Get comments with pagination
            $comments = $this->commentModel->getCommentsByShare($shareId, $page, $limit, $sortBy);
            
            // Get total count for pagination
            $totalComments = $this->commentModel->getCommentCountByShare($shareId);
            $totalPages = ceil($totalComments / $limit);
            
            // Get user vote data if authenticated
            $userVotes = [];
            if ($this->isAuthenticated()) {
                $userVotes = $this->voteModel->getUserVotesForComments(
                    array_column($comments, 'id'), 
                    $this->getUser()['id']
                );
            }
            
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => $comments,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_comments' => $totalComments,
                        'per_page' => $limit
                    ],
                    'user_votes' => $userVotes
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Get comments error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load comments'], 500);
        }
    }
    
    /**
     * Vote on a comment
     * POST /comments/{commentId}/vote
     */
    public function vote($commentId) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $voteType = $input['vote_type'] ?? null; // 'upvote' or 'downvote' or null to remove vote
        
        if (!in_array($voteType, ['upvote', 'downvote', null])) {
            $this->json(['success' => false, 'message' => 'Invalid vote type'], 400);
            return;
        }
        
        try {
            // Verify comment exists
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                $this->json(['success' => false, 'message' => 'Comment not found'], 404);
                return;
            }
            
            // Handle voting
            $result = $this->voteModel->voteOnComment(
                $commentId,
                $this->getUser()['id'],
                $voteType
            );
            
            if ($result) {
                // Get updated comment with vote counts
                $updatedComment = $this->commentModel->getCommentById($commentId);
                $userVote = $this->voteModel->getUserVoteForComment($commentId, $this->getUser()['id']);
                
                $this->json([
                    'success' => true,
                    'message' => $voteType ? 'Vote recorded successfully' : 'Vote removed successfully',
                    'data' => [
                        'upvotes' => $updatedComment['upvote_count'],
                        'downvotes' => $updatedComment['downvote_count'],
                        'user_vote' => $userVote ? ($userVote['vote_type'] === 'upvote' ? 1 : -1) : 0,
                        'score' => $updatedComment['upvote_count'] - $updatedComment['downvote_count']
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to process vote'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Comment vote error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while processing the vote'], 500);
        }
    }
    
    /**
     * Update a comment
     * PUT /comments/{commentId}
     */
    public function update($commentId) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $content = trim($input['content'] ?? '');
        
        if (!$content) {
            $this->json(['success' => false, 'message' => 'Content is required'], 400);
            return;
        }
        
        if (strlen($content) < 3) {
            $this->json(['success' => false, 'message' => 'Content must be at least 3 characters'], 400);
            return;
        }
        
        if (strlen($content) > 5000) {
            $this->json(['success' => false, 'message' => 'Content cannot exceed 5000 characters'], 400);
            return;
        }
        
        try {
            // Verify comment ownership
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                $this->json(['success' => false, 'message' => 'Comment not found'], 404);
                return;
            }
            
            if ($comment['user_id'] != $this->getUser()['id']) {
                $this->json(['success' => false, 'message' => 'You can only edit your own comments'], 403);
                return;
            }
            
            // Check if comment is too old to edit (24 hours)
            $editTimeLimit = 24 * 60 * 60; // 24 hours in seconds
            if (time() - strtotime($comment['created_at']) > $editTimeLimit) {
                $this->json(['success' => false, 'message' => 'Comments can only be edited within 24 hours'], 400);
                return;
            }
            
            // Update the comment
            $success = $this->commentModel->updateComment($commentId, $content);
            
            if ($success) {
                $updatedComment = $this->commentModel->getCommentById($commentId);
                
                $this->json([
                    'success' => true,
                    'message' => 'Comment updated successfully',
                    'data' => [
                        'comment' => $updatedComment
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update comment'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Comment update error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating the comment'], 500);
        }
    }
    
    /**
     * Delete a comment
     * DELETE /comments/{commentId}
     */
    public function delete($commentId) {
        $this->requireAuth();
        
        try {
            // Verify comment exists and ownership
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                $this->json(['success' => false, 'message' => 'Comment not found'], 404);
                return;
            }
            
            // Check if user owns the comment or is admin
            $user = $this->getUser();
            if ($comment['user_id'] != $user['id'] && $user['role'] !== 'admin') {
                $this->json(['success' => false, 'message' => 'You can only delete your own comments'], 403);
                return;
            }
            
            // Soft delete the comment
            $success = $this->commentModel->softDeleteComment($commentId);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Comment deleted successfully'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to delete comment'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Comment delete error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while deleting the comment'], 500);
        }
    }
    
    /**
     * Get comment thread (with nested replies)
     * GET /comments/thread/{commentId}
     */
    public function getThread($commentId) {
        try {
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                $this->json(['success' => false, 'message' => 'Comment not found'], 404);
                return;
            }
            
            // Get the full thread (parent comment + all replies)
            $thread = $this->commentModel->getCommentThread($commentId);
            
            $this->json([
                'success' => true,
                'data' => [
                    'thread' => $thread
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Comment thread error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load comment thread'], 500);
        }
    }
    
    /**
     * Report a comment
     * POST /comments/{commentId}/report
     */
    public function report($commentId) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $reason = trim($input['reason'] ?? '');
        $description = trim($input['description'] ?? '');
        
        if (!$reason) {
            $this->json(['success' => false, 'message' => 'Report reason is required'], 400);
            return;
        }
        
        try {
            // Verify comment exists
            $comment = $this->commentModel->getCommentById($commentId);
            if (!$comment) {
                $this->json(['success' => false, 'message' => 'Comment not found'], 404);
                return;
            }
            
            // Check if user already reported this comment
            $existingReport = $this->commentModel->getUserReportForComment($commentId, $this->getUser()['id']);
            if ($existingReport) {
                $this->json(['success' => false, 'message' => 'You have already reported this comment'], 400);
                return;
            }
            
            // Create report
            $reportId = $this->commentModel->reportComment($commentId, $this->getUser()['id'], $reason, $description);
            
            if ($reportId) {
                $this->json([
                    'success' => true,
                    'message' => 'Comment reported successfully. Our team will review it.'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to report comment'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Comment report error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while reporting the comment'], 500);
        }
    }
    
    /**
     * Get user's comment history
     * GET /comments/user/{userId}
     */
    public function userComments($userId) {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
            
            // If requesting own comments, require auth
            if ($this->isAuthenticated() && $this->getUser()['id'] == $userId) {
                $this->requireAuth();
            }
            
            $comments = $this->commentModel->getUserComments($userId, $page, $limit);
            $totalComments = $this->commentModel->getUserCommentCount($userId);
            $totalPages = ceil($totalComments / $limit);
            
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => $comments,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_comments' => $totalComments,
                        'per_page' => $limit
                    ]
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("User comments error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load user comments'], 500);
        }
    }
}
?>
