<?php
$page_title = $page_title ?? 'Create New User';
?>

<!-- Optimized Admin Container -->
<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-user-plus"></i>
                    <h1><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
                <div class="header-subtitle">
                    Create a new user account with role-based access control
                </div>
            </div>
            <div class="header-actions">
                <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>

        <form id="createUserForm" method="POST" action="<?= app_base_url('/admin/users/store') ?>" class="main-form-container">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Compact Action Bar -->
            <div class="compact-action-bar">
                <div class="action-left">
                    <div class="save-status" id="save-status">
                         <i class="fas fa-circle"></i>
                        <span>Ready to create</span>
                    </div>
                </div>
                <div class="action-right">
                    <button type="button" class="btn btn-outline-secondary btn-compact" onclick="previewUser()">
                        <i class="fas fa-eye"></i>
                        Preview
                    </button>
                    <button type="submit" class="btn btn-primary btn-compact" id="save-btn">
                        <i class="fas fa-save"></i>
                        Create User
                    </button>
                </div>
            </div>

            <!-- Main Content Layout (Single Column) -->
            <div class="create-content-single-column">

                <!-- Personal Information -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-user"></i> Personal Information</h3>
                    </div>
                    <div class="card-body-clean">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group-modern">
                                <label class="form-label required" for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control-modern" required placeholder="John">
                            </div>

                            <div class="form-group-modern">
                                <label class="form-label required" for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control-modern" required placeholder="Doe">
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label required" for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control-modern" required placeholder="john.doe@example.com">
                            <small style="color: var(--gray-500); font-size: 0.8rem; margin-top: 0.25rem; display: block;">A valid email address is required for communication.</small>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label required" for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control-modern" required placeholder="johndoe">
                            <small style="color: var(--gray-500); font-size: 0.8rem; margin-top: 0.25rem; display: block;">Unique identifier for login.</small>
                        </div>
                    </div>
                </div>

                <!-- Account Settings & Security -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-shield-alt"></i> Security & Access</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-modern">
                            <label class="form-label required" for="password">Password</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <div style="position: relative; flex-grow: 1;">
                                    <input type="password" id="password" name="password" class="form-control-modern" required minlength="8" placeholder="Generated Password">
                                    <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-500); cursor: pointer;">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-secondary btn-compact" onclick="generatePassword()">
                                    <i class="fas fa-magic"></i> Generate
                                </button>
                            </div>
                            <small style="color: var(--gray-500); font-size: 0.8rem; margin-top: 0.25rem; display: block;">Auto-generated strong password. You can also type manually.</small>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                            <div class="form-group-modern">
                                <label class="form-label required" for="role">Role</label>
                                <select id="role" name="role" class="form-control-modern" required>
                                    <option value="">Select a role...</option>
                                    <option value="user">Regular User</option>
                                    <option value="engineer">Engineer</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <div class="form-group-modern">
                                <label class="form-label">Account Status</label>
                                <select name="is_active" class="form-control-modern">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-cog"></i> Preferences</h3>
                    </div>
                    <div class="card-body-clean">
                         <div class="option-grid">
                            <label class="option-checkbox-modern">
                                <input type="checkbox" name="email_verified" value="1" checked>
                                <span class="checkmark"></span>
                                <span class="label-text">Auto-verify email</span>
                            </label>

                            <label class="option-checkbox-modern">
                                <input type="checkbox" name="terms_agreed" value="1" checked>
                                <span class="checkmark"></span>
                                <span class="label-text">Auto-agree to terms</span>
                            </label>

                             <label class="option-checkbox-modern">
                                <input type="checkbox" name="send_welcome_email" value="1" checked>
                                <span class="checkmark"></span>
                                <span class="label-text">Send welcome email</span>
                            </label>

                            <label class="option-checkbox-modern">
                                <input type="checkbox" name="marketing_emails" value="1" checked>
                                <span class="checkmark"></span>
                                <span class="label-text">Allow marketing emails</span>
                            </label>
                        </div>

            <!-- Bottom Actions -->
            <div style="max-width: 960px; margin: 2rem auto 0 auto; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-secondary btn-compact">Cancel</a>
                <button type="submit" class="btn btn-primary btn-compact">
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Page Preview Modal -->
<div id="preview-modal" class="preview-modal-compact" style="display: none;">
    <div class="preview-backdrop" onclick="closePreviewModal()"></div>
    <div class="preview-content-compact">
        <div class="preview-header-compact">
            <h3 class="preview-title">User Preview</h3>
            <div class="preview-actions">
                <button class="btn btn-sm btn-outline-secondary" onclick="closePreviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="preview-body-compact" id="preview-body-content" style="padding: 2rem; overflow-y: auto;">
            <!-- Content injected via JS -->
        </div>
    </div>
</div>

<script>
    // Form validation and interactions
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveUser();
    });

    function saveUser() {
        const password = document.getElementById('password').value;
        // Password confirmation removed as per requirement

        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = document.getElementById('save-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        updateSaveStatus('saving');

        fetch('<?= app_base_url('/admin/users/store') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                }
            })
            .then(response => {
                if (response.ok) {
                    updateSaveStatus('saved');
                    alert('User created successfully!');
                    window.location.href = '<?= app_base_url('/admin/users') ?>';
                } else {
                    throw new Error('Failed to create user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                updateSaveStatus('error');
                alert('Error creating user: ' + error.message);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    }

    function updateSaveStatus(status) {
        const statusElement = document.getElementById('save-status');
        const icon = statusElement.querySelector('i');
        const text = statusElement.querySelector('span');

        switch (status) {
            case 'saving':
                icon.className = 'fas fa-spinner fa-spin text-info';
                text.textContent = 'Saving...';
                break;
            case 'saved':
                icon.className = 'fas fa-check-circle text-success';
                text.textContent = 'Saved successfully';
                break;
             case 'error':
                icon.className = 'fas fa-exclamation-circle text-danger';
                text.textContent = 'Error saving';
                break;
             default:
                icon.className = 'fas fa-circle';
                text.textContent = 'Ready to create';
        }
    }

    function previewUser() {
        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);

        const data = {
            firstName: formData.get('first_name'),
            lastName: formData.get('last_name'),
            username: formData.get('username'),
            email: formData.get('email'),
            role: formData.get('role'),
            status: formData.get('is_active') === '1' ? 'Active' : 'Inactive',
        };

        let html = `
            <div style="display: grid; grid-template-columns: 100px 1fr; gap: 1rem; align-items: center;">
                <div style="width: 80px; height: 80px; background: var(--primary-50); color: var(--primary-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem; color: var(--gray-900);">${data.firstName} ${data.lastName}</h2>
                    <p style="margin: 0; color: var(--gray-500);">@${data.username}</p>
                    <span class="status-badge" style="display: inline-block; background: ${data.status === 'Active' ? 'var(--success-500)' : 'var(--gray-400)'}; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; margin-top: 0.5rem;">${data.status}</span>
                </div>
            </div>
            <div style="margin-top: 2rem; border-top: 1px solid var(--gray-200); padding-top: 1rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <strong style="display: block; color: var(--gray-500); font-size: 0.875rem;">Email</strong>
                        <span style="color: var(--gray-800);">${data.email}</span>
                    </div>
                     <div>
                        <strong style="display: block; color: var(--gray-500); font-size: 0.875rem;">Role</strong>
                        <span style="color: var(--gray-800); text-transform: capitalize;">${data.role || 'N/A'}</span>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('preview-body-content').innerHTML = html;
        
        const modal = document.getElementById('preview-modal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('visible'), 10);
    }

    function closePreviewModal() {
        const modal = document.getElementById('preview-modal');
        modal.classList.remove('visible');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    // Username check & Auto-generate password
    document.addEventListener('DOMContentLoaded', function() {
        generatePassword();

        const usernameInput = document.getElementById('username');
        const feedback = document.createElement('small');
        feedback.style.display = 'block';
        feedback.style.marginTop = '0.25rem';
        usernameInput.parentNode.appendChild(feedback);

        let timeout = null;
        usernameInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            const username = this.value;
            
            if (username.length < 3) {
                 feedback.textContent = '';
                 this.style.borderColor = 'var(--gray-300)';
                 return;
            }

            timeout = setTimeout(() => {
                fetch(`<?= app_base_url('/api/check-username') ?>?username=${username}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.available === false) { // Assuming API returns { available: false } if taken
                            this.style.borderColor = 'var(--danger-500)';
                            feedback.textContent = '❌ Username not available';
                            feedback.style.color = 'var(--danger-500)';
                        } else {
                            this.style.borderColor = 'var(--success-500)';
                            feedback.textContent = '✅ Username available';
                            feedback.style.color = 'var(--success-500)';
                        }
                    })
                    .catch(err => console.error(err));
            }, 500);
        });
    });

    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById("password").value = password;
        
        // Ensure visible if implicit requirement, but let's keep it masked by default unless toggled
        // Optional: document.getElementById("password").type = "text"; 
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const icon = document.getElementById("password-icon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

</script>

<style>
    /* ========================================
       PREMIUM DESIGN SYSTEM (PRODUCTION READY)
       ======================================== */
    :root {
        --primary-600: #4f46e5;
        --primary-700: #4338ca;
        --primary-50: #eef2ff;

        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;

        --success-500: #10b981;
        --warning-500: #f59e0b;
        --danger-500: #ef4444;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    body.admin-body {
        background-color: var(--gray-50);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--gray-800);
    }

    .page-create-container {
        max-width: 100%;
        padding-bottom: 5rem;
        background-color: var(--gray-50);
        min-height: 100vh;
    }
    
    .page-create-wrapper {
        background: transparent;
    }

    /* --- Header --- */
    .compact-create-header {
        max-width: 960px;
        margin: 0 auto;
        padding: 2rem 0 1.5rem 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }
    
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .header-title i {
        font-size: 1.75rem;
        color: var(--primary-600);
    }
    
    .header-title h1 {
        margin: 0;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--gray-900);
        letter-spacing: -0.025em;
        line-height: 1.2;
    }
    
    .header-subtitle {
        font-size: 0.9375rem;
        color: var(--gray-500);
        margin: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    /* --- Buttons --- */
    .btn {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-primary {
        background: var(--primary-600);
        color: white;
    }
    .btn-primary:hover {
        background: var(--primary-700);
    }

    .btn-secondary {
        background: var(--gray-200);
        color: var(--gray-800);
    }
    .btn-secondary:hover {
        background: var(--gray-300);
    }

    .btn-outline-secondary {
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
    }
    .btn-outline-secondary:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    /* --- Floating Action Bar --- */
    .compact-action-bar {
        max-width: 960px;
        margin: 1.5rem auto 2rem auto;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        padding: 0.75rem 1.25rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--gray-200);
        position: sticky;
        top: 1.5rem;
        z-index: 50;
    }

    .save-status {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-500);
    }

    .save-status i {
        font-size: 0.625rem;
    }

    .action-right {
        display: flex;
        gap: 0.75rem;
    }

    /* --- Content --- */
    .create-content-single-column {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .content-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s ease;
        overflow: hidden;
    }
    .content-card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-header-clean {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: white;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .card-title i {
         color: var(--gray-400);
         font-size: 1rem;
    }

    .card-body-clean {
        padding: 1.5rem;
    }

    /* --- Form Controls --- */
    .form-group-modern {
        margin-bottom: 1.5rem;
    }
    .form-group-modern:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }
    .form-label.required::after {
        content: "*";
        color: var(--danger-500);
        margin-left: 0.125rem;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.95rem;
        line-height: 1.5;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        background-color: white;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        border-color: var(--primary-600);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* --- Custom Checkbox --- */
    .option-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .option-checkbox-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        padding: 0.75rem;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-md);
        transition: all 0.2s;
    }
    .option-checkbox-modern:hover {
        background-color: var(--gray-50);
        border-color: var(--gray-300);
    }
    
    .option-checkbox-modern input {
        display: none;
    }
    
    .checkmark {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--gray-300);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .option-checkbox-modern input:checked + .checkmark {
        background-color: var(--primary-600);
        border-color: var(--primary-600);
    }
    
    .checkmark:after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        color: white;
        font-size: 0.75rem;
        opacity: 0;
        transform: scale(0);
        transition: all 0.2s;
    }
    
    .option-checkbox-modern input:checked + .checkmark:after {
        opacity: 1;
        transform: scale(1);
    }

    /* --- Preview Modal --- */
    .preview-modal-compact {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .preview-modal-compact.visible {
        opacity: 1;
    }
    
    .preview-backdrop {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    
    .preview-content-compact {
        position: relative;
        width: 95%;
        max-width: 600px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    
    .preview-header-compact {
        padding: 1rem 1.5rem;
        background: white;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>