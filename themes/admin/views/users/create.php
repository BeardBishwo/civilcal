<?php
/**
 * PREMIUM USER CREATION FORM
 * Two-column layout: Identity (Left) vs Access/Security (Right)
 */
$page_title = 'Create New User';
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-user-plus"></i>
                    <h1>Create New User</h1>
                </div>
                <div class="header-subtitle">Add a new user to the system.</div>
            </div>
            <div class="header-actions">
                <a href="<?= app_base_url('/admin/users') ?>" class="btn-cancel-premium">
                    Cancel
                </a>
                <button type="button" class="btn-save-premium" id="save-btn" onclick="submitUserForm()">
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
        </div>

        <!-- Form Content -->
        <form id="createUserForm" class="premium-form-grid">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <!-- LEFT COLUMN: Identity -->
            <div class="form-column-main">
                <div class="premium-card">
                    <div class="card-header-clean">
                        <h3><i class="fas fa-id-card"></i> User Identity</h3>
                    </div>
                    <div class="card-body-clean">
                        
                        <div class="form-row-2">
                            <div class="form-group-premium">
                                <label class="label-premium required">First Name</label>
                                <input type="text" name="first_name" class="input-premium" required placeholder="e.g. John">
                            </div>
                            <div class="form-group-premium">
                                <label class="label-premium required">Last Name</label>
                                <input type="text" name="last_name" class="input-premium" required placeholder="e.g. Doe">
                            </div>
                        </div>

                        <div class="form-group-premium">
                            <label class="label-premium required">Email Address</label>
                            <input type="email" name="email" class="input-premium" required placeholder="john.doe@example.com">
                            <small class="hint-text">Used for login and notifications.</small>
                        </div>

                        <div class="form-group-premium">
                            <label class="label-premium required">Username</label>
                            <div class="input-with-icon">
                                <i class="fas fa-at"></i>
                                <input type="text" id="username" name="username" class="input-premium pl-4" required placeholder="johndoe">
                            </div>
                            <small id="username-feedback" class="hint-text" style="display:block; min-height:20px;">Unique login identifier.</small>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Security & Access -->
            <div class="form-column-side">
                
                <!-- Security -->
                <div class="premium-card">
                    <div class="card-header-clean">
                        <h3><i class="fas fa-shield-alt"></i> Security</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-premium">
                            <label class="label-premium required">Password</label>
                            <div class="password-group">
                                <input type="text" id="password" name="password" class="input-premium" required minlength="8" placeholder="Strong Password">
                                <button type="button" class="btn-generate" onclick="generatePassword()" title="Generate Random">
                                    <i class="fas fa-random"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Access & Role -->
                <div class="premium-card mt-4">
                    <div class="card-header-clean">
                        <h3><i class="fas fa-user-tag"></i> Access Control</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-premium">
                            <label class="label-premium required">Role</label>
                            <select name="role" class="select-premium" required>
                                <option value="user" selected>Regular User</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <div class="form-group-premium">
                            <label class="label-premium">Status</label>
                            <select name="is_active" class="select-premium">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                         <div class="form-divider"></div>

                         <div class="preference-list">
                             <label class="toggle-row">
                                 <span class="toggle-label">Verify Email</span>
                                 <input type="checkbox" name="email_verified" value="1" checked>
                             </label>
                             <label class="toggle-row">
                                 <span class="toggle-label">Welcome Email</span>
                                 <input type="checkbox" name="send_welcome_email" value="1" checked>
                             </label>
                         </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Generate Password
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let pass = "";
        for(let i=0; i<14; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('password').value = pass;
    }
    document.addEventListener('DOMContentLoaded', generatePassword);

    // Username Check
    let userTimeout;
    const usernameInput = document.getElementById('username');
    const feedback = document.getElementById('username-feedback');
    
    usernameInput.addEventListener('keyup', function() {
        clearTimeout(userTimeout);
        if(this.value.length < 3) { feedback.innerHTML = 'Unique login identifier.'; feedback.style.color=''; return; }
        
        userTimeout = setTimeout(() => {
            fetch(`<?= app_base_url('/api/check-username') ?>?username=${this.value}`)
                .then(r => r.json())
                .then(d => {
                    if(d.available === false) {
                        feedback.innerHTML = '<i class="fas fa-times-circle"></i> Username taken';
                        feedback.style.color = '#ef4444';
                        usernameInput.style.borderColor = '#ef4444';
                    } else {
                        feedback.innerHTML = '<i class="fas fa-check-circle"></i> Username available';
                        feedback.style.color = '#10b981';
                        usernameInput.style.borderColor = '#10b981';
                    }
                }).catch(() => {});
        }, 500);
    });

    // Submit Form
    function submitUserForm() {
        const form = document.getElementById('createUserForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const btn = document.getElementById('save-btn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;

        const formData = new FormData(form);

        fetch('<?= app_base_url('/admin/users/store') ?>', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'User Created',
                    text: 'The user has been successfully added.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => window.location.href = '<?= app_base_url('/admin/users') ?>');
            } else {
                Swal.fire('Error', data.message || 'Failed to create user', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'An unexpected error occurred', 'error');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>

<style>
/* PREMIUM FORM STYLES (Scoped) */
:root { --admin-primary: #667eea; --admin-bg: #f8f9fa; }
body { background: var(--admin-bg); font-family: 'Inter', sans-serif; }

.admin-wrapper-container { padding: 1rem; max-width: 1200px; margin: 0 auto; }
.admin-content-wrapper { background: transparent; }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1e293b; }
.header-title i { color: var(--admin-primary); margin-right: 10px; }
.header-subtitle { color: #64748b; font-size: 0.9rem; margin-top: 4px; margin-left: 36px; }

/* Buttons */
.btn-save-premium { background: var(--admin-primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-save-premium:hover { background: #5a67d8; transform: translateY(-1px); }
.btn-cancel-premium { background: #f1f5f9; color: #64748b; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; transition: 0.2s; }
.btn-cancel-premium:hover { background: #e2e8f0; color: #475569; }

/* Grid Layout */
.premium-form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
@media (max-width: 900px) { .premium-form-grid { grid-template-columns: 1fr; } }

/* Cards */
.premium-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.card-header-clean { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
.card-header-clean h3 { margin: 0; font-size: 1rem; font-weight: 600; color: #334155; }
.card-body-clean { padding: 1.5rem; }

/* Form Elements */
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group-premium { margin-bottom: 1.25rem; }
.form-group-premium:last-child { margin-bottom: 0; }
.label-premium { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
.label-premium.required::after { content: "*"; color: #ef4444; margin-left: 2px; }

.input-premium, .select-premium { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; transition: 0.2s; outline: none; }
.input-premium:focus, .select-premium:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
.input-premium.pl-4 { padding-left: 2.25rem; }

.hint-text { font-size: 0.75rem; color: #94a3b8; margin-top: 4px; display: block; }
.password-group { display: flex; gap: 8px; }
.btn-generate { background: #f1f5f9; border: 1px solid #cbd5e1; color: #64748b; border-radius: 8px; width: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.btn-generate:hover { background: #e2e8f0; color: #475569; }

/* Preferences */
.form-divider { height: 1px; background: #f1f5f9; margin: 1.5rem 0; }
.preference-list { display: flex; flex-direction: column; gap: 0.75rem; }
.toggle-row { display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
.toggle-label { font-size: 0.9rem; color: #334155; font-weight: 500; }
</style>