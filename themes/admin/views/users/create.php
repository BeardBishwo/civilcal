<?php
$page_title = $page_title ?? 'Create New User';
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-plus"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Create a new user account for the system</p>
    </div>

    <!-- Breadcrumb -->
    <nav class="breadcrumb" style="margin-bottom: 24px;">
        <a href="<?= app_base_url('/admin/users') ?>">Users</a>
        <span class="breadcrumb-divider">/</span>
        <span class="breadcrumb-current">Create User</span>
    </nav>

    <!-- Create User Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i>
                User Information
            </h3>
        </div>
        <div class="card-content">
            <form id="createUserForm" method="POST" action="<?= app_base_url('/admin/users/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Personal Information -->
                    <div>
                        <h4 style="margin-bottom: 16px; color: var(--admin-gray-700); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h4>

                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                            <small style="color: var(--admin-gray-500); font-size: 12px;">Choose a unique username</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                            <small style="color: var(--admin-gray-500); font-size: 12px;">A valid email address is required</small>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div>
                        <h4 style="margin-bottom: 16px; color: var(--admin-gray-700); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                            <i class="fas fa-cog"></i>
                            Account Settings
                        </h4>

                        <div class="form-group">
                            <label class="form-label" for="password">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" required minlength="6">
                            <small style="color: var(--admin-gray-500); font-size: 12px;">Minimum 6 characters</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="role">Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select a role</option>
                                <option value="user">Regular User</option>
                                <option value="engineer">Engineer</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <small style="color: var(--admin-gray-500); font-size: 12px;">Choose appropriate access level</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Account Status</label>
                            <div style="display: flex; gap: 16px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="is_active" value="1" checked>
                                    <span>Active</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="is_active" value="0">
                                    <span>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-gray-200);">
                    <h4 class="form-section-title">
                        <i class="fas fa-envelope"></i>
                        Additional Options
                    </h4>

                    <div class="option-grid">
                        <label class="option-checkbox">
                            <input type="checkbox" name="email_verified" value="1" checked>
                            <span>Auto-verify email</span>
                        </label>

                        <label class="option-checkbox">
                            <input type="checkbox" name="terms_agreed" value="1" checked>
                            <span>Auto-agree to terms</span>
                        </label>

                        <label class="option-checkbox">
                            <input type="checkbox" name="marketing_emails" value="1">
                            <span>Allow marketing emails</span>
                        </label>

                        <label class="option-checkbox">
                            <input type="checkbox" name="send_welcome_email" value="1" checked>
                            <span>Send welcome email</span>
                        </label>
                    </div>
                </div>

                <div class="form-footer">
                    <div class="footer-actions">
                        <button type="button" class="btn btn-secondary" onclick="previewUser()">
                            <i class="fas fa-eye"></i>
                            Preview
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Form validation and interactions
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            document.getElementById('password_confirmation').focus();
            return;
        }

        if (password.length < 6) {
            alert('Password must be at least 6 characters long!');
            document.getElementById('password').focus();
            return;
        }

        // Submit the form
        createUser();
    });

    function previewUser() {
        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);

        const previewData = {
            firstName: formData.get('first_name'),
            lastName: formData.get('last_name'),
            username: formData.get('username'),
            email: formData.get('email'),
            role: formData.get('role'),
            status: formData.get('is_active') === '1' ? 'Active' : 'Inactive',
            marketingEmails: formData.get('marketing_emails') === '1' ? 'Yes' : 'No',
            welcomeEmail: formData.get('send_welcome_email') === '1' ? 'Yes' : 'No'
        };

        let previewHTML = '<h3>User Preview</h3>';
        previewHTML += '<div style="text-align: left;">';
        previewHTML += '<p><strong>Name:</strong> ' + previewData.firstName + ' ' + previewData.lastName + '</p>';
        previewHTML += '<p><strong>Username:</strong> ' + previewData.username + '</p>';
        previewHTML += '<p><strong>Email:</strong> ' + previewData.email + '</p>';
        previewHTML += '<p><strong>Role:</strong> ' + previewData.role + '</p>';
        previewHTML += '<p><strong>Status:</strong> ' + previewData.status + '</p>';
        previewHTML += '<p><strong>Marketing Emails:</strong> ' + previewData.marketingEmails + '</p>';
        previewHTML += '<p><strong>Welcome Email:</strong> ' + previewData.welcomeEmail + '</p>';
        previewHTML += '</div>';

        // Simple preview modal
        const modal = document.createElement('div');
        modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
        z-index: 9999;
    `;

        modal.innerHTML = `
        <div style="background: white; padding: 32px; border-radius: 12px; max-width: 500px; width: 90%;">
            ${previewHTML}
            <div style="margin-top: 24px; text-align: right;">
                <button onclick="this.closest('[style*=fixed]').remove()" class="btn btn-secondary">Close</button>
            </div>
        </div>
    `;

        document.body.appendChild(modal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    function createUser() {
        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;

        fetch('<?= app_base_url('/admin/users/store') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok) {
                    alert('User created successfully!');
                    window.location.href = '<?= app_base_url('/admin/users') ?>';
                } else {
                    throw new Error('Failed to create user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating user: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    }

    // Check username availability
    document.getElementById('username').addEventListener('blur', function() {
        const username = this.value;
        if (username.length > 2) {
            // Simple username availability check (you can expand this)
            const feedback = document.createElement('small');
            feedback.style.color = 'var(--admin-success)';
            feedback.style.fontSize = '12px';
            feedback.innerHTML = '<i class="fas fa-check"></i> Username available';

            // Remove existing feedback
            const existing = this.parentNode.querySelector('small');
            if (existing && existing.textContent.includes('Username')) {
                existing.remove();
            }

            this.parentNode.appendChild(feedback);
        }
    });
</script>