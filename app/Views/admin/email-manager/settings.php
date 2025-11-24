<?php
ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>
                <i class="fas fa-cog"></i>
                Email Settings
            </h1>
            <p>Configure SMTP and email sending preferences</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="window.location.href='/admin/email-manager'">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">SMTP Configuration</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo app_base_url('/admin/email-manager/settings'); ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email_from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name"
                                value="<?= htmlspecialchars($settings['from_name'] ?? '') ?>" required>
                            <small class="text-muted">The name that appears in sent emails</small>
                        </div>
                        <div class="col-md-6">
                            <label for="email_from_address" class="form-label">From Email Address</label>
                            <input type="email" class="form-control" id="email_from_address" name="email_from_address"
                                value="<?= htmlspecialchars($settings['from_email'] ?? '') ?>" required>
                            <small class="text-muted">The email address that appears in sent emails</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="email_smtp_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="email_smtp_host" name="email_smtp_host"
                                value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email_smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="email_smtp_port" name="email_smtp_port"
                                value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email_smtp_user" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="email_smtp_user" name="email_smtp_user"
                                value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email_smtp_pass" class="form-label">SMTP Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="email_smtp_pass" name="email_smtp_pass"
                                    value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email_smtp_secure" class="form-label">Encryption</label>
                        <select class="form-select" id="email_smtp_secure" name="email_smtp_secure">
                            <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Test Configuration</h6>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="test_email_address" class="form-label">Test Email Address</label>
                            <input type="email" class="form-control" id="test_email_address"
                                placeholder="Enter email to receive test message">
                            <small class="text-muted">Send a test email to verify your SMTP settings</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-info w-100" onclick="sendTestEmail()">
                                <i class="fas fa-paper-plane"></i> Send Test Email
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('email_smtp_pass');
        const icon = document.getElementById('toggleIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function sendTestEmail() {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;

        const email = document.getElementById('test_email_address').value;
        if (!email) {
            alert('Please enter a test email address');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        // Collect current form values to test with unsaved settings
        const formData = new FormData(document.querySelector('form'));
        formData.append('test_email', email);

        fetch('/admin/email-manager/test-email', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test email sent successfully to ' + email + '!');
                } else {
                    alert('Failed to send test email: ' + (data.error || data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending test email.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }
</script>

<?php
$content = ob_get_clean();
$this->layout('admin/layout', array_merge(get_defined_vars(), [
    'content' => $content
]));
?>