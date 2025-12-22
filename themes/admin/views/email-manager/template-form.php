<?php
// Email Template Form - Beautiful Modern Design
?>

<div class="template-editor-wrapper">
    <div class="template-editor-container">
        
        <!-- Compact Modern Header -->
        <div class="template-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="header-text">
                        <h1 class="header-title">
                            <?php echo !empty($template) ? 'Edit Email Template' : 'Create Email Template'; ?>
                        </h1>
                        <p class="header-subtitle">
                            <?php echo !empty($template) ? 'Customize your email template with dynamic variables' : 'Design a new email template for your communications'; ?>
                        </p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn-header btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Templates</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="template-content-grid">
            
            <!-- Left Column - Main Form -->
            <div class="template-main-column">
                <div class="template-card">
                    <div class="card-header-modern">
                        <div class="card-header-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h3 class="card-header-title">Template Content</h3>
                    </div>
                    
                    <div class="card-body-modern">
                        <form method="POST" action="<?php echo !empty($template) ? app_base_url('/admin/email-manager/template/' . $template['id'] . '/update') : app_base_url('/admin/email-manager/template/create'); ?>" id="templateForm">
                            
                            <!-- Template Name -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <span class="label-text">Template Name</span>
                                    <span class="label-required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <input type="text" name="name" class="form-input-modern" placeholder="e.g., Welcome Email" value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <!-- Subject Line -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <span class="label-text">Email Subject</span>
                                    <span class="label-required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <i class="fas fa-heading"></i>
                                    </div>
                                    <input type="text" name="subject" class="form-input-modern" placeholder="e.g., Welcome to {{site_name}}, {{first_name}}!" value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" required>
                                </div>
                                <div class="form-hint">
                                    <i class="fas fa-lightbulb"></i>
                                    Use variables like {{first_name}} to personalize
                                </div>
                            </div>

                            <!-- Email Content -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <span class="label-text">Email Content</span>
                                    <span class="label-required">*</span>
                                </label>
                                <div class="textarea-wrapper">
                                    <textarea name="content" class="form-textarea-modern" rows="14" placeholder="Write your email content here... Use {{variables}} for dynamic content." required><?php echo htmlspecialchars($template['content'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    HTML and plain text supported. Click variables on the right to insert.
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <span class="label-text">Description</span>
                                    <span class="label-optional">(Optional)</span>
                                </label>
                                <div class="textarea-wrapper">
                                    <textarea name="description" class="form-textarea-modern" rows="3" placeholder="Brief description of this template's purpose..."><?php echo htmlspecialchars($template['description'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="form-actions-modern">
                                <button type="submit" class="btn-modern btn-primary">
                                    <i class="fas fa-save"></i>
                                    <span><?php echo !empty($template) ? 'Update Template' : 'Create Template'; ?></span>
                                </button>
                                <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn-modern btn-outline">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="template-sidebar-column">
                
                <!-- Settings Card -->
                <div class="template-card sidebar-card">
                    <div class="card-header-modern">
                        <div class="card-header-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="card-header-title">Settings</h3>
                    </div>
                    <div class="card-body-modern">
                        <!-- Category -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <span class="label-text">Category</span>
                            </label>
                            <div class="select-wrapper">
                                <select name="category" class="form-select-modern" form="templateForm">
                                    <option value="general" <?php echo (!isset($template['category']) || $template['category'] === 'general') ? 'selected' : ''; ?>>General</option>
                                    <option value="welcome" <?php echo (isset($template['category']) && $template['category'] === 'welcome') ? 'selected' : ''; ?>>Welcome</option>
                                    <option value="notification" <?php echo (isset($template['category']) && $template['category'] === 'notification') ? 'selected' : ''; ?>>Notification</option>
                                    <option value="password_reset" <?php echo (isset($template['category']) && $template['category'] === 'password_reset') ? 'selected' : ''; ?>>Password Reset</option>
                                    <option value="verification" <?php echo (isset($template['category']) && $template['category'] === 'verification') ? 'selected' : ''; ?>>Verification</option>
                                </select>
                                <div class="select-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="form-group-modern">
                            <label class="toggle-label">
                                <input type="checkbox" name="is_active" id="isActive" class="toggle-input" value="1" form="templateForm" <?php echo (!isset($template) || !empty($template['is_active'])) ? 'checked' : ''; ?>>
                                <div class="toggle-switch">
                                    <div class="toggle-slider"></div>
                                </div>
                                <span class="toggle-text">
                                    <span class="toggle-title">Active Status</span>
                                    <span class="toggle-description">Enable this template for use</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Available Variables Card -->
                <div class="template-card sidebar-card">
                    <div class="card-header-modern">
                        <div class="card-header-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 class="card-header-title">Available Variables</h3>
                    </div>
                    <div class="card-body-modern">
                        <p class="variables-intro">Click to copy variable to clipboard:</p>
                        <div class="variables-list">
                            <div class="variable-item" onclick="copyVariable('{{username}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{username}}</code>
                                    <span class="variable-desc">User's username</span>
                                </div>
                            </div>
                            <div class="variable-item" onclick="copyVariable('{{email}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{email}}</code>
                                    <span class="variable-desc">Email address</span>
                                </div>
                            </div>
                            <div class="variable-item" onclick="copyVariable('{{first_name}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-id-badge"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{first_name}}</code>
                                    <span class="variable-desc">First name</span>
                                </div>
                            </div>
                            <div class="variable-item" onclick="copyVariable('{{last_name}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{last_name}}</code>
                                    <span class="variable-desc">Last name</span>
                                </div>
                            </div>
                            <div class="variable-item" onclick="copyVariable('{{site_name}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{site_name}}</code>
                                    <span class="variable-desc">Site name</span>
                                </div>
                            </div>
                            <div class="variable-item" onclick="copyVariable('{{site_url}}')" title="Click to copy">
                                <div class="variable-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="variable-content">
                                    <code class="variable-code">{{site_url}}</code>
                                    <span class="variable-desc">Site URL</span>
                                </div>
                            </div>
                        </div>
                        <div class="variables-tip">
                            <i class="fas fa-magic"></i>
                            <span>Variables are replaced with actual values when emails are sent</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   TEMPLATE EDITOR - MODERN BEAUTIFUL DESIGN
   ======================================== */

.template-editor-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
    min-height: calc(100vh - 70px);
    padding: 1.5rem;
}

.template-editor-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* ========================================
   COMPACT MODERN HEADER
   ======================================== */

.template-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex: 1;
}

.header-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    backdrop-filter: blur(10px);
}

.header-text {
    flex: 1;
}

.header-title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
}

.header-subtitle {
    margin: 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-header {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.95);
    color: #667eea;
}

.btn-secondary:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* ========================================
   CONTENT GRID
   ======================================== */

.template-content-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
}

.template-main-column {
    min-width: 0;
}

.template-sidebar-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ========================================
   MODERN CARDS
   ======================================== */

.template-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.template-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.sidebar-card {
    position: sticky;
    top: 1.5rem;
}

.card-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-header-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.card-header-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
}

.card-body-modern {
    padding: 2rem;
}

.sidebar-card .card-body-modern {
    padding: 1.5rem;
}

/* ========================================
   MODERN FORM ELEMENTS
   ======================================== */

.form-group-modern {
    margin-bottom: 1.75rem;
}

.form-group-modern:last-child {
    margin-bottom: 0;
}

.form-label-modern {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.95rem;
}

.label-text {
    flex: 1;
}

.label-required {
    color: #ef4444;
    font-weight: 700;
}

.label-optional {
    color: #9ca3af;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Input Wrapper with Icon */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    font-size: 0.95rem;
    pointer-events: none;
    z-index: 1;
}

.form-input-modern {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    color: #1f2937;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.form-input-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-input-modern::placeholder {
    color: #9ca3af;
}

/* Textarea */
.textarea-wrapper {
    position: relative;
}

.form-textarea-modern {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    color: #1f2937;
    background: #f9fafb;
    font-family: 'Courier New', monospace;
    line-height: 1.6;
    resize: vertical;
    transition: all 0.3s ease;
}

.form-textarea-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-textarea-modern::placeholder {
    color: #9ca3af;
    font-family: system-ui, -apple-system, sans-serif;
}

/* Form Hints */
.form-hint {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-hint i {
    color: #667eea;
}

/* Select Dropdown */
.select-wrapper {
    position: relative;
}

.form-select-modern {
    width: 100%;
    padding: 0.875rem 3rem 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    color: #1f2937;
    background: #f9fafb;
    cursor: pointer;
    appearance: none;
    transition: all 0.3s ease;
}

.form-select-modern:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

/* ========================================
   MODERN TOGGLE SWITCH
   ======================================== */

.toggle-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.toggle-label:hover {
    background: white;
    border-color: #667eea;
}

.toggle-input {
    display: none;
}

.toggle-switch {
    position: relative;
    width: 52px;
    height: 28px;
    background: #d1d5db;
    border-radius: 14px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.toggle-slider {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.toggle-input:checked + .toggle-switch {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.toggle-input:checked + .toggle-switch .toggle-slider {
    transform: translateX(24px);
}

.toggle-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.toggle-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

.toggle-description {
    font-size: 0.8rem;
    color: #6b7280;
}

/* ========================================
   ACTION BUTTONS
   ======================================== */

.form-actions-modern {
    display: flex;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f3f4f6;
    margin-top: 2rem;
}

.btn-modern {
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-outline {
    background: white;
    color: #6b7280;
    border-color: #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
}

/* ========================================
   VARIABLES LIST
   ======================================== */

.variables-intro {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.variables-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.variable-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.variable-item:hover {
    background: white;
    border-color: #667eea;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.variable-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

.variable-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.variable-code {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    font-weight: 600;
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
}

.variable-desc {
    font-size: 0.75rem;
    color: #6b7280;
}

.variables-tip {
    margin-top: 1rem;
    padding: 0.875rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.8rem;
    color: #667eea;
}

.variables-tip i {
    font-size: 1rem;
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 1024px) {
    .template-content-grid {
        grid-template-columns: 1fr;
    }
    
    .template-sidebar-column {
        position: static;
    }
    
    .sidebar-card {
        position: static;
    }
}

@media (max-width: 768px) {
    .template-editor-wrapper {
        padding: 1rem;
    }
    
    .template-header {
        padding: 1.5rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .header-left {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .card-body-modern {
        padding: 1.5rem;
    }
    
    .form-actions-modern {
        flex-direction: column;
    }
    
    .btn-modern {
        justify-content: center;
    }
}
</style>

<script>
// Copy variable to clipboard
function copyVariable(variable) {
    navigator.clipboard.writeText(variable).then(function() {
        showNotification('Copied ' + variable + ' to clipboard!', 'success');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = this.action;
        
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('<?php echo !empty($template) ? 'Template updated' : 'Template created'; ?> successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo app_base_url('/admin/email-manager/templates'); ?>';
                }, 1000);
            } else {
                const errorMessage = data.errors ? Object.values(data.errors).join('\n') : (data.error || 'An error occurred');
                showNotification('Error: ' + errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while saving the template', 'error');
        });
    });
});
</script>