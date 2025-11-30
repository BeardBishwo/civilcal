<?php require_once __DIR__ . '/../header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope"></i>
                        <?php echo $template ? 'Edit Template' : 'Create Template'; ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Templates
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="templateForm" method="POST" action="<?php echo $template ? "/admin/email-manager/template/{$template['id']}/update" : "/admin/email-manager/template/create"; ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Template Name *</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" required>
                                            <small class="form-text text-muted">A descriptive name for this template</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category *</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Select a category...</option>
                                                <?php foreach ($templateTypes as $type): ?>
                                                    <option value="<?php echo $type; ?>"
                                                        <?php echo (isset($template['category']) && $template['category'] === $type) ? 'selected' : ''; ?>>
                                                        <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($template['description'] ?? ''); ?></textarea>
                                            <small class="form-text text-muted">Brief description of when to use this template</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Content -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Email Content</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject *</label>
                                            <input type="text" class="form-control" id="subject" name="subject"
                                                value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" required>
                                            <small class="form-text text-muted">
                                                Use variables like {{name}}, {{order_id}}, etc.
                                                <a href="#" id="insert-variable-subject" class="text-primary">Insert Variable</a>
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="content" class="form-label">Content *</label>
                                            <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($template['content'] ?? ''); ?></textarea>
                                            <small class="form-text text-muted">
                                                Use variables like {{name}}, {{email}}, {{phone}}, etc.
                                                <a href="#" id="insert-variable-content" class="text-primary">Insert Variable</a>
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Available Variables</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>Common:</strong> {{name}}, {{email}}, {{phone}}<br>
                                                        <strong>Order:</strong> {{order_id}}, {{order_date}}, {{total}}<br>
                                                        <strong>User:</strong> {{user_id}}, {{username}}, {{created_at}}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <strong>System:</strong> {{site_name}}, {{site_url}}, {{current_date}}<br>
                                                        <strong>Custom:</strong> {{custom_1}}, {{custom_2}}<br>
                                                        <strong>Address:</strong> {{address}}, {{city}}, {{country}}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Settings -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                                    value="1" <?php echo (isset($template['is_active']) && $template['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Inactive templates won't be available for use</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="variables" class="form-label">Custom Variables</label>
                                            <textarea class="form-control" id="variables" name="variables" rows="4"
                                                placeholder="custom_1, custom_2, custom_3"><?php echo htmlspecialchars($template['variables'] ?? ''); ?></textarea>
                                            <small class="form-text text-muted">Comma-separated list of custom variables</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Subject Preview</label>
                                            <div class="p-3 bg-light rounded" id="subject-preview">
                                                <?php echo htmlspecialchars($template['subject'] ?? 'Enter subject above...'); ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Content Preview</label>
                                            <div class="p-3 bg-light rounded" id="content-preview">
                                                <?php echo nl2br(htmlspecialchars($template['content'] ?? 'Enter content above...')); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="card">
                                    <div class="card-body">
                                        <button type="submit" class="btn btn-primary w-100 mb-2">
                                            <i class="fas fa-save"></i>
                                            <?php echo $template ? 'Update Template' : 'Create Template'; ?>
                                        </button>

                                        <?php if ($template): ?>
                                            <button type="button" class="btn btn-danger w-100" id="delete-btn">
                                                <i class="fas fa-trash"></i> Delete Template
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variable Insertion Modal -->
<div class="modal fade" id="variableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Insert Variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Variable</label>
                    <select class="form-select" id="variable-select">
                        <option value="">Choose a variable...</option>
                        <optgroup label="Common">
                            <option value="{{name}}">{{name}}</option>
                            <option value="{{email}}">{{email}}</option>
                            <option value="{{phone}}">{{phone}}</option>
                        </optgroup>
                        <optgroup label="Order">
                            <option value="{{order_id}}">{{order_id}}</option>
                            <option value="{{order_date}}">{{order_date}}</option>
                            <option value="{{total}}">{{total}}</option>
                        </optgroup>
                        <optgroup label="User">
                            <option value="{{user_id}}">{{user_id}}</option>
                            <option value="{{username}}">{{username}}</option>
                            <option value="{{created_at}}">{{created_at}}</option>
                        </optgroup>
                        <optgroup label="System">
                            <option value="{{site_name}}">{{site_name}}</option>
                            <option value="{{site_url}}">{{site_url}}</option>
                            <option value="{{current_date}}">{{current_date}}</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="insert-variable-btn">Insert</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('templateForm');
        const subjectInput = document.getElementById('subject');
        const contentTextarea = document.getElementById('content');
        const subjectPreview = document.getElementById('subject-preview');
        const contentPreview = document.getElementById('content-preview');
        const variableModal = new bootstrap.Modal(document.getElementById('variableModal'));
        let currentTarget = null;

        // Real-time preview
        subjectInput.addEventListener('input', updatePreview);
        contentTextarea.addEventListener('input', updatePreview);

        function updatePreview() {
            subjectPreview.textContent = subjectInput.value || 'Enter subject above...';
            contentPreview.innerHTML = contentTextarea.value ?
                contentTextarea.value.replace(/\n/g, '<br>') : 'Enter content above...';
        }

        // Variable insertion
        document.getElementById('insert-variable-subject').addEventListener('click', function(e) {
            e.preventDefault();
            currentTarget = subjectInput;
            variableModal.show();
        });

        document.getElementById('insert-variable-content').addEventListener('click', function(e) {
            e.preventDefault();
            currentTarget = contentTextarea;
            variableModal.show();
        });

        document.getElementById('insert-variable-btn').addEventListener('click', function() {
            const selectedVariable = document.getElementById('variable-select').value;
            if (selectedVariable && currentTarget) {
                const start = currentTarget.selectionStart;
                const end = currentTarget.selectionEnd;
                const text = currentTarget.value;
                currentTarget.value = text.substring(0, start) + selectedVariable + text.substring(end);
                currentTarget.focus();
                currentTarget.setSelectionRange(start + selectedVariable.length, start + selectedVariable.length);
                updatePreview();
            }
            variableModal.hide();
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Template saved successfully!');
                        window.location.href = '/admin/email-manager/templates';
                    } else {
                        const errors = data.errors ? Object.values(data.errors).flat().join('\n') : data.error;
                        alert('Error: ' + errors);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the template.');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
        });

        // Delete template
        const deleteBtn = document.getElementById('delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
                    const templateId = <?php echo $template['id']; ?>;

                    fetch(`/admin/email-manager/template/${templateId}/delete`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Template deleted successfully!');
                                window.location.href = '/admin/email-manager/templates';
                            } else {
                                alert('Error: ' + (data.error || 'Failed to delete template'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the template.');
                        });
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>