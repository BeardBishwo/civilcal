<?php

/**
 * Admin User Create View
 * Path: app/Views/admin/users/create.php
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Create New User</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/users') ?>">Users</a></li>
                            <li class="breadcrumb-item active">Create User</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?= app_base_url('/admin/users/store') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <small class="text-muted">Username must be unique and contain only letters, numbers, and underscores</small>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <small class="text-muted">A valid email address is required</small>
                        </div>

                        <!-- Full Name -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Password must be at least 8 characters long</small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" selected>Regular User</option>
                                <option value="engineer">Engineer</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <small class="text-muted">Select the appropriate role for this user</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                            <small class="text-muted">Inactive users cannot log in to the system</small>
                        </div>

                        <!-- Send Welcome Email -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email" value="1" checked>
                                <label class="form-check-label" for="send_welcome_email">
                                    Send welcome email to user
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Create User
                            </button>
                            <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>User Creation Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Username:</strong> Must be unique and contain only alphanumeric characters and underscores</li>
                        <li><strong>Email:</strong> Must be a valid email address and will be used for notifications</li>
                        <li><strong>Password:</strong> Should be at least 8 characters with a mix of letters and numbers</li>
                        <li><strong>Role:</strong> Determines what actions the user can perform in the system</li>
                        <li><strong>Active Status:</strong> Only active users can log in to the system</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Password confirmation validation
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;

        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long!');
            return false;
        }
    });
</script>