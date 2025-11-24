<?php
ob_start();

// Helper functions
if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status)
    {
        $classes = [
            'new' => 'primary',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary'
        ];
        return $classes[$status] ?? 'secondary';
    }
}

if (!function_exists('getPriorityBadgeClass')) {
    function getPriorityBadgeClass($priority)
    {
        $classes = [
            'low' => 'info',
            'medium' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger'
        ];
        return $classes[$priority] ?? 'secondary';
    }
}

if (!function_exists('getTimeAgo')) {
    function getTimeAgo($datetime)
    {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $interval = $now->diff($past);

        if ($interval->days > 0) {
            return $interval->days . 'd';
        } elseif ($interval->h > 0) {
            return $interval->h . 'h';
        } elseif ($interval->i > 0) {
            return $interval->i . 'm';
        } else {
            return 'now';
        }
    }
}
?>

<div class="container-fluid email-manager-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-envelope"></i> Thread: <?= htmlspecialchars($thread['subject']) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Threads
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Thread Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Thread Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>From:</strong> <?= htmlspecialchars($thread['from_name']) ?>
                                            &lt;<?= htmlspecialchars($thread['from_email']) ?>&gt;
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Category:</strong>
                                            <span class="badge bg-info"><?= htmlspecialchars($thread['category']) ?></span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Status:</strong>
                                            <span class="badge bg-<?= getStatusBadgeClass($thread['status']) ?>">
                                                <?= htmlspecialchars($thread['status']) ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Priority:</strong>
                                            <span class="badge bg-<?= getPriorityBadgeClass($thread['priority']) ?>">
                                                <?= htmlspecialchars($thread['priority']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Assigned To:</strong>
                                            <?php if ($thread['assigned_first_name']): ?>
                                                <?= htmlspecialchars($thread['assigned_first_name'] . ' ' . $thread['assigned_last_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Created:</strong>
                                            <?= date('M j, Y g:i A', strtotime($thread['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Original Message -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Original Message</h5>
                                </div>
                                <div class="card-body">
                                    <div class="message-content">
                                        <?= nl2br(htmlspecialchars($thread['message'])) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Responses -->
                            <?php if (!empty($thread['responses'])): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Responses (<?= count($thread['responses']) ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($thread['responses'] as $response): ?>
                                            <div class="response-item mb-3 p-3 border rounded" style="<?= $response['is_internal_note'] ? 'background-color: #fff3cd;' : '' ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <strong><?= htmlspecialchars($response['first_name'] . ' ' . $response['last_name']) ?></strong>
                                                        <?php if ($response['is_internal_note']): ?>
                                                            <span class="badge bg-warning text-dark">Internal Note</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= date('M j, Y g:i A', strtotime($response['created_at'])) ?>
                                                    </small>
                                                </div>
                                                <div class="message-content">
                                                    <?= $response['message'] // Assuming message is HTML safe or handled by TinyMCE 
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Reply Form -->
                            <div class="card" id="reply">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Add Response</h5>
                                </div>
                                <div class="card-body">
                                    <form id="replyForm" method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/'); ?><?= $thread['id'] ?>/reply">
                                        <div class="mb-3">
                                            <label for="template_select" class="form-label">Use Template (Optional)</label>
                                            <select class="form-select" id="template_select" name="template_id">
                                                <option value="">Select a template...</option>
                                                <?php foreach ($templates as $template): ?>
                                                    <option value="<?= $template['id'] ?>">
                                                        <?= htmlspecialchars($template['name']) ?> (<?= htmlspecialchars($template['category']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control" id="message" name="message" rows="6"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_internal" name="is_internal" value="1">
                                                <label class="form-check-label" for="is_internal">
                                                    Internal Note (not sent to customer)
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send Response
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Actions Panel -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status_update" class="form-label">Update Status</label>
                                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/'); ?><?= $thread['id'] ?>/status" class="d-flex gap-2">
                                            <select class="form-select" id="status_update" name="status">
                                                <option value="new" <?= $thread['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                                <option value="in_progress" <?= $thread['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="resolved" <?= $thread['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                                <option value="closed" <?= $thread['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority_update" class="form-label">Update Priority</label>
                                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/'); ?><?= $thread['id'] ?>/priority" class="d-flex gap-2">
                                            <select class="form-select" id="priority_update" name="priority">
                                                <option value="low" <?= $thread['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                                <option value="medium" <?= $thread['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                                <option value="high" <?= $thread['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                                <option value="urgent" <?= $thread['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                            </select>
                                            <button type="submit" class="btn btn-warning">Update</button>
                                        </form>
                                    </div>

                                    <div class="mb-3">
                                        <label for="assign_to" class="form-label">Assign To</label>
                                        <form method="POST" action="<?php echo app_base_url('/admin/email-manager/thread/'); ?><?= $thread['id'] ?>/assign" class="d-flex gap-2">
                                            <select class="form-select" id="assign_to" name="assigned_to">
                                                <option value="">Unassigned</option>
                                                <?php foreach ($availableAssignees as $assignee): ?>
                                                    <option value="<?= $assignee['id'] ?>" <?= $thread['assigned_to'] == $assignee['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($assignee['first_name'] . ' ' . $assignee['last_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-info">Assign</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Thread Stats -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3><?= $thread['response_count'] ?></h3>
                                            <small class="text-muted">Responses</small>
                                        </div>
                                        <div class="col-6">
                                            <h3><?= getTimeAgo($thread['created_at']) ?></h3>
                                            <small class="text-muted">Age</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: '#message',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });

        // Handle reply form submission
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Trigger save to update textarea
                tinymce.triggerSave();

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success';
                            alert.textContent = 'Response sent successfully!';
                            replyForm.prepend(alert);

                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Error: ' + (data.error || 'Failed to send response'));
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while sending the response.');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
            });
        }

        // Handle template selection
        const templateSelect = document.getElementById('template_select');

        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                const templateId = this.value;
                if (templateId) {
                    fetch(`/admin/email-manager/template/${templateId}/use`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.template) {
                                // Set content in TinyMCE
                                if (tinymce.get('message')) {
                                    tinymce.get('message').setContent(data.template.content);
                                } else {
                                    document.getElementById('message').value = data.template.content;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading template:', error);
                        });
                }
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
// Removed custom CSS - using default admin theme
// $additional_css = [
//     '/assets/css/admin/email-manager.css'
// ];
$this->layout('admin/layout', array_merge(get_defined_vars(), [
    'content' => $content,
    // 'additional_css' => $additional_css
]));
?>