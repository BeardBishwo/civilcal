<?php

/**
 * Admin User Edit View
 * Path: app/Views/admin/users/edit.php
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Edit User</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo app_base_url('/admin/dashboard'); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo app_base_url('/admin/users'); ?>">Users</a></li>
                            <li class="breadcrumb-item active">Edit User</li>
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

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_messages'])): ?>
        <?php foreach ($_SESSION['flash_messages'] as $type => $message): ?>
            <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash_messages']); ?>
    <?php endif; ?>

    <!-- Edit User Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo app_base_url('/admin/users/' . $user['id'] . '/update'); ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>

                        <!-- Full Name -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                        </div>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                            value="1" <?= ($user['is_active'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Active Account
                        </label>
                    </div>
                </div>

                <!-- Password Reset -->
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            placeholder="Leave blank to keep current password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Only fill this if you want to change the user's password</small>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                    <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-secondary">Cancel</a>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Metadata Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>User Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">User ID:</dt>
                    <dd class="col-sm-7">#<?php echo htmlspecialchars($user['id'] ?? ''); ?></dd>

                    <dt class="col-sm-5">Created:</dt>
                    <dd class="col-sm-7"><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></dd>

                    <?php if (isset($user['updated_at'])): ?>
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7"><?php echo date('M d, Y', strtotime($user['updated_at'])); ?></dd>
                    <?php endif; ?>

                    <?php if (isset($user['last_login'])): ?>
                        <dt class="col-sm-5">Last Login:</dt>
                        <dd class="col-sm-7"><?php echo date('M d, Y H:i', strtotime($user['last_login'])); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-key me-2"></i>Reset Password
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        onclick="if(confirm('Are you sure you want to delete this user?')) { window.location.href='<?php echo app_base_url('/admin/users/' . $user['id'] . '/delete'); ?>'; }">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('new_password');
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
</script>