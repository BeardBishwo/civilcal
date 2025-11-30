<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Email & Notifications</h2>
            <p class="text-muted mb-0">Manage email templates and notifications</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                <i class="bi bi-envelope me-2"></i>Send Test Email
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-bell me-2"></i>Notification Settings
            </button>
        </div>
    </div>

    <!-- Email Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sent Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . $stats['sent_today'] . '</div>
                            <small class="stat-label">Emails delivered today</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                This Week
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . $stats['sent_week'] . '</div>
                            <small class="stat-label">Last 7 days</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-week fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . $stats['sent_month'] . '</div>
                            <small class="stat-label">Last 30 days</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-month fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Success Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . $stats['success_rate'] . '%</div>
                            <small class="stat-label">Delivery success rate</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Templates -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Email Templates</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">';
                    
                    foreach ($templates as $template) {
                        $activeClass = $template['is_active'] ? 'active' : '';
                        $content .= '
                        <a href="#" class="list-group-item list-group-item-action template-item ' . $activeClass . '" 
                           data-template-id="' . $template['id'] . '"
                           data-template-name="' . htmlspecialchars($template['name']) . '"
                           data-template-subject="' . htmlspecialchars($template['subject']) . '">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">' . htmlspecialchars($template['name']) . '</h6>
                                <span class="badge bg-' . ($template['is_active'] ? 'success' : 'secondary') . '">' . ($template['is_active'] ? 'Active' : 'Inactive') . '</span>
                            </div>
                            <p class="mb-1 text-muted">' . htmlspecialchars($template['description']) . '</p>
                            <small>Last updated: ' . date('M j, Y', strtotime($template['last_updated'])) . '</small>
                        </a>';
                    }
                    
                    $content .= '
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary" id="templateEditorTitle">Select a Template</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="templateActiveSwitch">
                        <label class="form-check-label" for="templateActiveSwitch">Active</label>
                    </div>
                </div>
                <div class="card-body">
                    <form id="templateForm" style="display: none;">
                        <input type="hidden" id="templateId" name="template_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" class="form-control" id="templateName" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Subject</label>
                            <input type="text" class="form-control" id="templateSubject" name="subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Content</label>
                            <textarea class="form-control" id="templateContent" name="content" rows="12" required></textarea>
                            <small class="form-text text-muted">
                                Available variables: {user_name}, {site_name}, {site_url}, {current_date}
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="previewTemplate">
                                <i class="bi bi-eye me-2"></i>Preview
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Template
                            </button>
                        </div>
                    </form>
                    
                    <div id="noTemplateSelected" class="text-center py-5">
                        <i class="bi bi-envelope-open fs-1 text-muted"></i>
                        <h5 class="text-muted mt-3">Select a template to edit</h5>
                        <p class="text-muted">Choose a template from the list to start editing</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Test Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required placeholder="Enter email address to test">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template</label>
                        <select class="form-select" name="template">
                            <option value="welcome">Welcome Email</option>
                            <option value="reset">Password Reset</option>
                            <option value="notification">Notification</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="testEmailForm" class="btn btn-primary">Send Test Email</button>
            </div>
        </div>
    </div>
</div>

<script>
// Template selection
document.querySelectorAll(".template-item").forEach(item => {
    item.addEventListener("click", function(e) {
        e.preventDefault();
        
        // Remove active class from all items
        document.querySelectorAll(".template-item").forEach(i => i.classList.remove("active"));
        // Add active class to clicked item
        this.classList.add("active");
        
        const templateId = this.dataset.templateId;
        const templateName = this.dataset.templateName;
        const templateSubject = this.dataset.templateSubject;
        
        // Show template form
        document.getElementById("noTemplateSelected").style.display = "none";
        document.getElementById("templateForm").style.display = "block";
        document.getElementById("templateEditorTitle").textContent = "Editing: " + templateName;
        
        // Populate form fields
        document.getElementById("templateId").value = templateId;
        document.getElementById("templateName").value = templateName;
        document.getElementById("templateSubject").value = templateSubject;
        
        // Load template content (this would typically come from an API)
        document.getElementById("templateContent").value = "Hello {user_name},\\n\\nWelcome to {site_name}! We\'re excited to have you on board.\\n\\nBest regards,\\nThe {site_name} Team\\n{site_url}";
    });
});

// Save template
document.getElementById("templateForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("/admin/email/save-template", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Template saved successfully!");
        } else {
            alert("Error: " + data.message);
        }
    });
});

// Send test email
document.getElementById("testEmailForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("/admin/email/send-test", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Test email sent successfully!");
            document.getElementById("testEmailModal").querySelector(".btn-close").click();
        } else {
            alert("Error: " + data.message);
        }
    });
});
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
