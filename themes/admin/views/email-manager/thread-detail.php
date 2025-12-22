<style>
/* Premium Email Thread Styles */
.thread-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.thread-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    animation: slideDown 0.5s ease-out;
}

.thread-title {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.thread-subtitle {
    color: #64748b;
    font-size: 0.95rem;
}

.message-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease-out;
    position: relative;
    overflow: hidden;
}

.message-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea, #764ba2);
}

.message-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2);
}

.message-card.internal {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-left: 4px solid #f59e0b;
}

.message-card.internal::before {
    background: linear-gradient(180deg, #f59e0b, #d97706);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.sender-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sender-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.sender-details h4 {
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    font-size: 1.1rem;
}

.sender-details .time {
    color: #64748b;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.message-content {
    color: #334155;
    line-height: 1.7;
    font-size: 0.95rem;
}

.internal-badge {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.reply-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
    animation: fadeInUp 0.6s ease-out;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.reply-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.reply-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.reply-textarea {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    font-size: 0.95rem;
    resize: vertical;
    transition: all 0.3s ease;
    font-family: inherit;
}

.reply-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.internal-note-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #fef3c7;
    border-radius: 12px;
    margin: 1rem 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.internal-note-toggle:hover {
    background: #fde68a;
}

.toggle-switch {
    position: relative;
    width: 50px;
    height: 26px;
    background: #cbd5e1;
    border-radius: 13px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.toggle-switch.active {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.toggle-switch::after {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.toggle-switch.active::after {
    left: 26px;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn-send {
    flex: 1;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-clear {
    background: white;
    color: #64748b;
    border: 2px solid #e2e8f0;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-clear:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.sidebar-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
    animation: fadeInRight 0.5s ease-out;
}

.sidebar-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.sidebar-card-header h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.sidebar-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.detail-item {
    margin-bottom: 1.25rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.detail-value {
    font-size: 0.95rem;
    color: #1e293b;
    font-weight: 500;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-new {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.status-in-progress {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.status-resolved {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.status-closed {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

.priority-low {
    background: linear-gradient(135deg, #94a3b8, #64748b);
    color: white;
}

.priority-medium {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.priority-high {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
}

.priority-urgent {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.category-badge {
    background: linear-gradient(135deg, #a855f7, #9333ea);
    color: white;
}

.action-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1e293b;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1.1rem;
    margin: 0;
}

.back-button {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.back-button:hover {
    background: #667eea;
    color: white;
    transform: translateX(-4px);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .thread-container {
        padding: 1rem 0;
    }
    
    .thread-header {
        padding: 1.5rem;
        border-radius: 16px;
    }
    
    .thread-title {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-send, .btn-clear {
        width: 100%;
    }
}
</style>

<div class="thread-container">
    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
        <!-- Header -->
        <div class="thread-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1; min-width: 300px;">
                    <h1 class="thread-title">
                        <i class="fas fa-envelope-open-text"></i>
                        <?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?>
                    </h1>
                    <p class="thread-subtitle">
                        <i class="fas fa-info-circle"></i>
                        View and respond to this email conversation
                    </p>
                </div>
                <div>
                    <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Threads
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 2rem;">
            <!-- Left Column - Messages & Reply -->
            <div>
                <!-- Messages -->
                <div style="margin-bottom: 2rem;">
                    <?php if (!empty($thread['messages'])): ?>
                        <?php foreach ($thread['messages'] as $index => $message): ?>
                            <div class="message-card <?php echo !empty($message['is_internal']) ? 'internal' : ''; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                                <div class="message-header">
                                    <div class="sender-info">
                                        <div class="sender-avatar">
                                            <?php 
                                            $name = $message['sender_name'] ?? 'U';
                                            echo strtoupper(substr($name, 0, 1));
                                            ?>
                                        </div>
                                        <div class="sender-details">
                                            <h4><?php echo htmlspecialchars($message['sender_name'] ?? 'Unknown Sender'); ?></h4>
                                            <div class="time">
                                                <i class="far fa-clock"></i>
                                                <?php echo date('M d, Y • h:i A', strtotime($message['created_at'] ?? 'now')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($message['is_internal'])): ?>
                                        <span class="internal-badge">
                                            <i class="fas fa-lock"></i>
                                            Internal Note
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="message-content">
                                    <?php echo nl2br(htmlspecialchars($message['content'] ?? '')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message-card">
                            <div class="empty-state">
                                <i class="fas fa-comment-slash"></i>
                                <p>No messages in this thread yet</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reply Form -->
                <div class="reply-section">
                    <div class="reply-header">
                        <div class="reply-icon">
                            <i class="fas fa-reply"></i>
                        </div>
                        <h3>Add Reply</h3>
                    </div>
                    
                    <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/reply'); ?>" id="replyForm">
                        <textarea 
                            name="message" 
                            class="reply-textarea" 
                            rows="8" 
                            placeholder="Type your reply here..." 
                            required
                        ></textarea>
                        
                        <label class="internal-note-toggle" onclick="toggleInternal()">
                            <div class="toggle-switch" id="internalToggle"></div>
                            <input type="checkbox" name="is_internal" id="isInternal" value="1" style="display: none;">
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">
                                    <i class="fas fa-lock"></i>
                                    Internal Note
                                </div>
                                <div style="font-size: 0.85rem; color: #64748b;">
                                    Only visible to team members
                                </div>
                            </div>
                        </label>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-send">
                                <i class="fas fa-paper-plane"></i>
                                Send Reply
                            </button>
                            <button type="button" class="btn-clear" onclick="clearReplyForm()">
                                <i class="fas fa-times"></i>
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Details & Actions -->
            <div>
                <!-- Thread Details -->
                <div class="sidebar-card">
                    <div class="sidebar-card-header">
                        <div class="sidebar-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Thread Details</h3>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <?php 
                            $status = $thread['status'] ?? 'new';
                            $statusClass = 'status-' . str_replace('_', '-', $status);
                            $statusText = ucwords(str_replace('_', ' ', $status));
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                <?php echo $statusText; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Priority</div>
                        <div class="detail-value">
                            <?php 
                            $priority = $thread['priority'] ?? 'medium';
                            $priorityClass = 'priority-' . $priority;
                            $priorityText = ucfirst($priority);
                            ?>
                            <span class="status-badge <?php echo $priorityClass; ?>">
                                <i class="fas fa-flag"></i>
                                <?php echo $priorityText; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">
                            <span class="status-badge category-badge">
                                <i class="fas fa-tag"></i>
                                <?php echo ucfirst(htmlspecialchars($thread['category'] ?? 'General')); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            <i class="far fa-calendar-alt" style="color: #667eea; margin-right: 0.5rem;"></i>
                            <?php echo date('M d, Y • h:i A', strtotime($thread['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">
                            <i class="far fa-clock" style="color: #667eea; margin-right: 0.5rem;"></i>
                            <?php echo date('M d, Y • h:i A', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="sidebar-card">
                    <div class="sidebar-card-header">
                        <div class="sidebar-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h3>Quick Actions</h3>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Update Status</div>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/status'); ?>">
                            <select name="status" class="action-select" onchange="this.form.submit()">
                                <option value="new" <?php echo ($thread['status'] === 'new') ? 'selected' : ''; ?>>🔵 New</option>
                                <option value="in_progress" <?php echo ($thread['status'] === 'in_progress') ? 'selected' : ''; ?>>🟡 In Progress</option>
                                <option value="resolved" <?php echo ($thread['status'] === 'resolved') ? 'selected' : ''; ?>>🟢 Resolved</option>
                                <option value="closed" <?php echo ($thread['status'] === 'closed') ? 'selected' : ''; ?>>⚫ Closed</option>
                            </select>
                        </form>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Update Priority</div>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/priority'); ?>">
                            <select name="priority" class="action-select" onchange="this.form.submit()">
                                <option value="low" <?php echo ($thread['priority'] === 'low') ? 'selected' : ''; ?>>🔹 Low</option>
                                <option value="medium" <?php echo ($thread['priority'] === 'medium') ? 'selected' : ''; ?>>🔸 Medium</option>
                                <option value="high" <?php echo ($thread['priority'] === 'high') ? 'selected' : ''; ?>>🔶 High</option>
                                <option value="urgent" <?php echo ($thread['priority'] === 'urgent') ? 'selected' : ''; ?>>🔴 Urgent</option>
                            </select>
                        </form>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Assign To</div>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/assign'); ?>">
                            <select name="assigned_to" class="action-select" onchange="this.form.submit()">
                                <option value="">👤 Unassigned</option>
                                <?php if (!empty($availableAssignees)): ?>
                                    <?php foreach ($availableAssignees as $assignee): ?>
                                        <option value="<?php echo $assignee['id']; ?>" <?php echo ($thread['assigned_to'] == $assignee['id']) ? 'selected' : ''; ?>>
                                            👨‍💼 <?php echo htmlspecialchars($assignee['name'] ?? $assignee['username'] ?? 'User ' . $assignee['id']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle internal note
function toggleInternal() {
    const toggle = document.getElementById('internalToggle');
    const checkbox = document.getElementById('isInternal');
    
    toggle.classList.toggle('active');
    checkbox.checked = !checkbox.checked;
}

// Handle reply form submission
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const threadId = '<?php echo $thread['id']; ?>';
        
        // Show loading state
        const submitBtn = this.querySelector('.btn-send');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
        
        fetch('<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/reply'); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Reply sent successfully!', 'success');
                this.reset();
                document.getElementById('internalToggle').classList.remove('active');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error: ' + (data.error || 'Failed to send reply'), 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while sending the reply', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

function clearReplyForm() {
    document.querySelector('textarea[name="message"]').value = '';
    document.getElementById('isInternal').checked = false;
    document.getElementById('internalToggle').classList.remove('active');
}
</script>