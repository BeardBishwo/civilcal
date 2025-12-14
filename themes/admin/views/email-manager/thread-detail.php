<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-comment-alt"></i> Thread: <?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?></h1>
            <p class="page-description">View and respond to this email conversation</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Threads
            </a>
        </div>
    </div>
</div>

<!-- Thread Overview -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <!-- Thread Messages -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-comments"></i>
                    Conversation
                </h5>
            </div>
            <div class="card-content">
                <div class="space-y-6">
                    <?php if (!empty($thread['messages'])): ?>
                        <?php foreach ($thread['messages'] as $message): ?>
                            <div class="border border-gray-200 rounded-lg p-4 <?php echo !empty($message['is_internal']) ? 'bg-yellow-50 border-yellow-200' : 'bg-white'; ?>">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <?php echo htmlspecialchars($message['sender_name'] ?? 'Unknown Sender'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('M d, Y H:i', strtotime($message['created_at'] ?? 'now')); ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($message['is_internal'])): ?>
                                        <span class="badge bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-lock mr-1"></i> Internal Note
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="prose max-w-none">
                                    <?php echo nl2br(htmlspecialchars($message['content'] ?? '')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comment-slash fa-2x mb-2"></i>
                            <p>No messages in this thread yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Reply Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-reply"></i>
                    Add Reply
                </h5>
            </div>
            <div class="card-content">
                <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/reply'); ?>" id="replyForm">
                    <div class="form-group mb-4">
                        <textarea name="message" class="form-control" rows="6" placeholder="Type your reply here..." required></textarea>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_internal" id="isInternal" class="form-check-input" value="1">
                        <label for="isInternal" class="form-check-label">
                            <i class="fas fa-lock mr-1"></i> Internal Note (Only visible to team members)
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Reply
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearReplyForm()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="space-y-6">
        <!-- Thread Details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Thread Details
                </h5>
            </div>
            <div class="card-content">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">
                            <?php 
                            $status = $thread['status'] ?? 'new';
                            $statusClass = '';
                            $statusText = '';
                            switch ($status) {
                                case 'new':
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    $statusText = 'New';
                                    break;
                                case 'in_progress':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    $statusText = 'In Progress';
                                    break;
                                case 'resolved':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'Resolved';
                                    break;
                                case 'closed':
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Closed';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = ucfirst($status);
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Priority</label>
                        <div class="mt-1">
                            <?php 
                            $priority = $thread['priority'] ?? 'medium';
                            $priorityClass = '';
                            $priorityText = '';
                            switch ($priority) {
                                case 'low':
                                    $priorityClass = 'bg-gray-100 text-gray-800';
                                    $priorityText = 'Low';
                                    break;
                                case 'medium':
                                    $priorityClass = 'bg-blue-100 text-blue-800';
                                    $priorityText = 'Medium';
                                    break;
                                case 'high':
                                    $priorityClass = 'bg-orange-100 text-orange-800';
                                    $priorityText = 'High';
                                    break;
                                case 'urgent':
                                    $priorityClass = 'bg-red-100 text-red-800';
                                    $priorityText = 'Urgent';
                                    break;
                                default:
                                    $priorityClass = 'bg-gray-100 text-gray-800';
                                    $priorityText = ucfirst($priority);
                            }
                            ?>
                            <span class="badge <?php echo $priorityClass; ?>"><?php echo $priorityText; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Category</label>
                        <div class="mt-1">
                            <span class="badge bg-purple-100 text-purple-800">
                                <?php echo ucfirst(htmlspecialchars($thread['category'] ?? 'General')); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo date('M d, Y H:i', strtotime($thread['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Updated</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo date('M d, Y H:i', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-cog"></i>
                    Actions
                </h5>
            </div>
            <div class="card-content">
                <div class="space-y-3">
                    <!-- Status Update -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 mb-2 block">Update Status</label>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/status'); ?>">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="new" <?php echo ($thread['status'] === 'new') ? 'selected' : ''; ?>>New</option>
                                <option value="in_progress" <?php echo ($thread['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo ($thread['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                <option value="closed" <?php echo ($thread['status'] === 'closed') ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </form>
                    </div>
                    
                    <!-- Priority Update -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 mb-2 block">Update Priority</label>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/priority'); ?>">
                            <select name="priority" class="form-select" onchange="this.form.submit()">
                                <option value="low" <?php echo ($thread['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo ($thread['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo ($thread['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                                <option value="urgent" <?php echo ($thread['priority'] === 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                            </select>
                        </form>
                    </div>
                    
                    <!-- Assign To -->
                    <div>
                        <label class="text-sm font-medium text-gray-500 mb-2 block">Assign To</label>
                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id'] . '/assign'); ?>">
                            <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                                <option value="">Unassigned</option>
                                <?php if (!empty($availableAssignees)): ?>
                                    <?php foreach ($availableAssignees as $assignee): ?>
                                        <option value="<?php echo $assignee['id']; ?>" <?php echo ($thread['assigned_to'] == $assignee['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($assignee['name'] ?? $assignee['username'] ?? 'User ' . $assignee['id']); ?>
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle reply form submission
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const threadId = '<?php echo $thread['id']; ?>';
        
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
                // Show success message
                showNotification('Reply sent successfully!', 'success');
                // Clear form
                this.reset();
                // Reload page to show new message
                location.reload();
            } else {
                showNotification('Error: ' + (data.error || 'Failed to send reply'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while sending the reply', 'error');
        });
    });
    
    console.log('Thread detail page loaded');
});

function clearReplyForm() {
    document.querySelector('textarea[name="message"]').value = '';
    document.getElementById('isInternal').checked = false;
}
</script>