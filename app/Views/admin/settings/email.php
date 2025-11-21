<?php

/**
 * Email Settings Page
 */
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Email Settings</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SMTP Configuration</h6>
                </div>
                <div class="card-body">
                    <form action="/admin/settings/save" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="smtp_enabled" name="smtp_enabled" value="1" <?= ($settings['smtp_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="smtp_enabled">Enable SMTP</label>
                            <div class="form-text">Use SMTP for sending emails instead of PHP mail().</div>
                        </div>

                        <div class="mb-3">
                            <label for="smtp_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="smtp_username" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="smtp_password" class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="smtp_encryption" class="form-label">Encryption</label>
                            <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" <?= ($settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="none" <?= ($settings['smtp_encryption'] ?? '') == 'none' ? 'selected' : '' ?>>None</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="from_email" class="form-label">From Email</label>
                            <input type="email" class="form-control" id="from_email" name="from_email" value="<?= htmlspecialchars($settings['from_email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="from_name" name="from_name" value="<?= htmlspecialchars($settings['from_name'] ?? '') ?>">
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary ms-2" id="sendTestEmail">Send Test Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sendTestEmail').addEventListener('click', function() {
        if (confirm('Send a test email to the admin email address?')) {
            fetch('/admin/email/send-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Test email sent successfully!');
                    } else {
                        alert('Failed to send test email: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the test email.');
                });
        }
    });
</script>