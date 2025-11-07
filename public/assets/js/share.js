/**
 * Share System JavaScript
 * Handles share functionality, comments, and social media integration
 */

class ShareSystem {
    constructor() {
        this.currentPage = 1;
        this.currentSort = 'best';
        this.isLoading = false;
        this.commentLimit = 20;
    }

    /**
     * Initialize the share system
     */
    init() {
        this.setupEventListeners();
        this.loadComments();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Character count for comment form
        const commentContent = document.getElementById('commentContent');
        if (commentContent) {
            const charCount = document.getElementById('charCount');
            commentContent.addEventListener('input', () => {
                charCount.textContent = commentContent.value.length;
            });
        }

        // Sort buttons
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const sortType = e.target.dataset.sort;
                this.changeSort(sortType);
            });
        });
    }

    /**
     * Change comment sort order
     */
    changeSort(sortType) {
        this.currentSort = sortType;
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-sort="${sortType}"]`).classList.add('active');
        this.loadComments();
    }

    /**
     * Load comments for the current share
     */
    async loadComments() {
        if (this.isLoading) return;

        this.isLoading = true;
        const container = document.getElementById('commentsContainer');
        const commentCount = document.getElementById('commentCount');

        try {
            // Show loading state
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading comments...</p>
                </div>
            `;

            const response = await fetch(`/api/comments/${shareId}?page=${this.currentPage}&limit=${this.commentLimit}&sort=${this.currentSort}`);
            const data = await response.json();

            if (data.success) {
                this.renderComments(data.data.comments);
                this.updateCommentCount(data.data.pagination.total_comments);
                this.renderPagination(data.data.pagination);
            } else {
                throw new Error(data.message || 'Failed to load comments');
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Failed to load comments. Please try again.
                </div>
            `;
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Render comments in the container
     */
    renderComments(comments) {
        const container = document.getElementById('commentsContainer');

        if (comments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                </div>
            `;
            return;
        }

        let html = '';
        comments.forEach(comment => {
            html += this.renderComment(comment);
        });

        container.innerHTML = html;
        this.setupCommentEvents();
    }

    /**
     * Render a single comment
     */
    renderComment(comment) {
            const isOwner = comment.user_id == window.currentUserId;
            const canEdit = isOwner && this.canEditComment(comment.created_at);

            return `
            <div class="comment mb-4 ${comment.parent_id ? 'ms-4' : ''}" data-comment-id="${comment.id}">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            ${comment.user_name ? comment.user_name.charAt(0).toUpperCase() : 'U'}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <strong>${this.escapeHtml(comment.user_name || 'Anonymous')}</strong>
                                <small class="text-muted ms-2">${this.timeAgo(comment.created_at)}</small>
                                ${comment.is_edited ? '<small class="text-muted">(edited)</small>' : ''}
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary btn-sm vote-btn ${comment.user_vote > 0 ? 'active' : ''}" 
                                        data-vote="up" data-comment-id="${comment.id}">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <span class="btn btn-outline-secondary btn-sm disabled">
                                    ${comment.score || 0}
                                </span>
                                <button class="btn btn-outline-secondary btn-sm vote-btn ${comment.user_vote < 0 ? 'active' : ''}" 
                                        data-vote="down" data-comment-id="${comment.id}">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button class="btn btn-outline-secondary btn-sm reply-btn" data-comment-id="${comment.id}">
                                    <i class="fas fa-reply"></i>
                                    Reply
                                </button>
                                ${canEdit ? `
                                    <button class="btn btn-outline-secondary btn-sm edit-btn" data-comment-id="${comment.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                ` : ''}
                                ${isOwner ? `
                                    <button class="btn btn-outline-danger btn-sm delete-btn" data-comment-id="${comment.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                        <div class="comment-content mb-2">
                            ${this.formatCommentContent(comment.content)}
                        </div>
                        <div class="comment-actions">
                            <small class="text-muted">
                                <i class="fas fa-thumbs-up me-1"></i>${comment.upvotes || 0}
                                <i class="fas fa-thumbs-down ms-2 me-1"></i>${comment.downvotes || 0}
                                ${comment.reply_count > 0 ? `<span class="ms-2"><i class="fas fa-comments me-1"></i>${comment.reply_count} repl${comment.reply_count === 1 ? 'y' : 'ies'}</span>` : ''}
                            </small>
                        </div>
                        
                        <!-- Reply Form (hidden by default) -->
                        <div class="reply-form mt-3" style="display: none;">
                            <form class="reply-comment-form" data-parent-id="${comment.id}">
                                <div class="mb-2">
                                    <textarea class="form-control" rows="2" placeholder="Write a reply..." maxlength="5000" required></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane me-1"></i>Reply
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm cancel-reply">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Edit Form (hidden by default) -->
                        <div class="edit-form mt-3" style="display: none;">
                            <form class="edit-comment-form" data-comment-id="${comment.id}">
                                <div class="mb-2">
                                    <textarea class="form-control" rows="3" maxlength="5000" required>${this.escapeHtml(comment.content)}</textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save me-1"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm cancel-edit">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Nested replies -->
                        ${comment.replies ? comment.replies.map(reply => this.renderComment(reply)).join('') : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Setup event listeners for comments
     */
    setupCommentEvents() {
        // Vote buttons
        document.querySelectorAll('.vote-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const voteType = e.target.dataset.vote;
                const commentId = e.target.dataset.commentId;
                this.voteComment(commentId, voteType);
            });
        });

        // Reply buttons
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const commentId = e.target.dataset.commentId;
                this.showReplyForm(commentId);
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const commentId = e.target.dataset.commentId;
                this.showEditForm(commentId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const commentId = e.target.dataset.commentId;
                this.deleteComment(commentId);
            });
        });

        // Reply form submissions
        document.querySelectorAll('.reply-comment-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitReply(form);
            });
        });

        // Edit form submissions
        document.querySelectorAll('.edit-comment-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitEdit(form);
            });
        });

        // Cancel buttons
        document.querySelectorAll('.cancel-reply').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.hideReplyForm(e.target.closest('.comment'));
            });
        });

        document.querySelectorAll('.cancel-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.hideEditForm(e.target.closest('.comment'));
            });
        });
    }

    /**
     * Submit a new comment
     */
    async submitComment(content) {
        try {
            const response = await fetch('/api/comments/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    share_id: shareId,
                    content: content
                })
            });

            const data = await response.json();
            if (data.success) {
                this.loadComments(); // Reload comments
                document.getElementById('commentContent').value = '';
                document.getElementById('charCount').textContent = '0';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            this.showAlert('Failed to submit comment. Please try again.', 'danger');
        }
    }

    /**
     * Submit a reply
     */
    async submitReply(form) {
        const content = form.querySelector('textarea').value;
        const parentId = form.dataset.parentId;

        try {
            const response = await fetch('/api/comments/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    share_id: shareId,
                    content: content,
                    parent_id: parentId
                })
            });

            const data = await response.json();
            if (data.success) {
                this.loadComments(); // Reload comments
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error submitting reply:', error);
            this.showAlert('Failed to submit reply. Please try again.', 'danger');
        }
    }

    /**
     * Submit an edit
     */
    async submitEdit(form) {
        const content = form.querySelector('textarea').value;
        const commentId = form.dataset.commentId;

        try {
            const response = await fetch(`/api/comments/${commentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content })
            });

            const data = await response.json();
            if (data.success) {
                this.loadComments(); // Reload comments
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error updating comment:', error);
            this.showAlert('Failed to update comment. Please try again.', 'danger');
        }
    }

    /**
     * Vote on a comment
     */
    async voteComment(commentId, voteType) {
        try {
            const response = await fetch(`/api/comments/${commentId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ vote_type: voteType })
            });

            const data = await response.json();
            if (data.success) {
                this.updateVoteUI(commentId, data.data);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error voting on comment:', error);
            this.showAlert('Failed to vote. Please try again.', 'danger');
        }
    }

    /**
     * Delete a comment
     */
    async deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`/api/comments/${commentId}`, {
                method: 'DELETE'
            });

            const data = await response.json();
            if (data.success) {
                this.loadComments(); // Reload comments
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error deleting comment:', error);
            this.showAlert('Failed to delete comment. Please try again.', 'danger');
        }
    }

    /**
     * Update vote UI after voting
     */
    updateVoteUI(commentId, voteData) {
        const comment = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!comment) return;

        // Update vote buttons
        const upBtn = comment.querySelector('[data-vote="up"]');
        const downBtn = comment.querySelector('[data-vote="down"]');
        const scoreDisplay = comment.querySelector('.btn.btn-outline-secondary.btn-sm.disabled');

        upBtn.classList.toggle('active', voteData.user_vote > 0);
        downBtn.classList.toggle('active', voteData.user_vote < 0);
        scoreDisplay.textContent = voteData.score;
    }

    /**
     * Show reply form
     */
    showReplyForm(commentId) {
        this.hideAllForms();
        const comment = document.querySelector(`[data-comment-id="${commentId}"]`);
        const replyForm = comment.querySelector('.reply-form');
        replyForm.style.display = 'block';
    }

    /**
     * Show edit form
     */
    showEditForm(commentId) {
        this.hideAllForms();
        const comment = document.querySelector(`[data-comment-id="${commentId}"]`);
        const editForm = comment.querySelector('.edit-form');
        editForm.style.display = 'block';
    }

    /**
     * Hide reply form
     */
    hideReplyForm(comment) {
        const replyForm = comment.querySelector('.reply-form');
        replyForm.style.display = 'none';
    }

    /**
     * Hide edit form
     */
    hideEditForm(comment) {
        const editForm = comment.querySelector('.edit-form');
        editForm.style.display = 'none';
    }

    /**
     * Hide all forms
     */
    hideAllForms() {
        document.querySelectorAll('.reply-form, .edit-form').forEach(form => {
            form.style.display = 'none';
        });
    }

    /**
     * Update comment count
     */
    updateCommentCount(count) {
        const commentCount = document.getElementById('commentCount');
        if (commentCount) {
            commentCount.textContent = count;
        }
    }

    /**
     * Render pagination
     */
    renderPagination(pagination) {
        // Implementation for pagination
        // This would render pagination controls based on the pagination data
    }

    /**
     * Check if comment can be edited (within 24 hours)
     */
    canEditComment(createdAt) {
        const now = new Date();
        const created = new Date(createdAt);
        const diffInHours = (now - created) / (1000 * 60 * 60);
        return diffInHours < 24;
    }

    /**
     * Format comment content (basic markdown-like formatting)
     */
    formatCommentContent(content) {
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code>$1</code>');
    }

    /**
     * Time ago helper
     */
    timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        return date.toLocaleDateString();
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show alert message
     */
    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Global functions for share functionality
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.select();
        element.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        // Show feedback
        const originalText = element.nextElementSibling.innerHTML;
        element.nextElementSibling.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            element.nextElementSibling.innerHTML = originalText;
        }, 2000);
    }
}

function shareToSocial(platform, url, title) {
    const encodedUrl = encodeURIComponent(url);
    const encodedTitle = encodeURIComponent(title);
    
    let shareUrl;
    switch (platform) {
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
            break;
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
            break;
        default:
            return;
    }
    
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

// Initialize share system
document.addEventListener('DOMContentLoaded', function() {
    window.shareSystem = new ShareSystem();
    window.shareSystem.init();
});