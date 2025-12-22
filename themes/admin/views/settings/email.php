<?php

/**
 * Email Settings Page - Beautiful Modern Design
 * Features: Card-based layout, organized sections, responsive grid
 */
?>

<style>
    .email-settings-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .settings-header {
        margin-bottom: 2.5rem;
        animation: slideDown 0.6s ease-out;
    }

    .settings-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .settings-header p {
        font-size: 1rem;
        color: #718096;
        margin-bottom: 0;
    }

    .settings-form {
        max-width: 1000px;
        margin: 0 auto;
    }

    .settings-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
    }

    .settings-section:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-header.from-section {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .section-icon {
        font-size: 1.8rem;
        line-height: 1;
    }

    .section-title-group h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: white;
    }

    .section-title-group p {
        font-size: 0.9rem;
        opacity: 0.95;
        margin-bottom: 0;
    }

    .section-body {
        padding: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.6rem;
        letter-spacing: 0.3px;
    }

    .form-control,
    .form-select {
        padding: 0.85rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: inherit;
        background-color: #f8f9fa;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-check {
        padding: 1rem;
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #f5576c;
    }

    .form-check-input {
        width: 1.3rem;
        height: 1.3rem;
        margin-top: 0.3rem;
        cursor: pointer;
        accent-color: #667eea;
    }

    .form-check-label {
        font-weight: 600;
        color: #2d3748;
        margin-left: 0.5rem;
        cursor: pointer;
        margin-bottom: 0.25rem;
    }

    .form-text {
        font-size: 0.85rem;
        color: #718096;
        margin-top: 0.4rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 2px solid #e2e8f0;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.85rem 1.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        letter-spacing: 0.3px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-secondary:hover {
        background: #f7fafc;
        transform: translateY(-2px);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .email-settings-container {
            padding: 1rem;
        }

        .settings-header h1 {
            font-size: 1.8rem;
        }

        .section-header {
            padding: 1.2rem 1.5rem;
        }

        .section-body {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<div class="admin-content">
    <div class="email-settings-container">
        <div class="settings-header">
            <h1>📧 Email Configuration</h1>
            <p>Manage your email delivery system and SMTP settings</p>
        </div>

        <form action="<?php echo app_base_url('/admin/settings/update'); ?>" method="POST" class="settings-form ajax-form" id="emailSettingsForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="setting_group" value="email">

        <!-- SMTP Configuration Section -->
        <div class="settings-section">
            <div class="section-header">
                <span class="section-icon">🔗</span>
                <div class="section-title-group">
                    <h2>SMTP Configuration</h2>
                    <p>Configure your mail server connection</p>
                </div>
            </div>

            <div class="section-body">
                <!-- Enable SMTP Toggle -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="smtp_enabled" name="smtp_enabled" value="1" <?= ($settings['smtp_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="smtp_enabled">Enable SMTP</label>
                    <div class="form-text">Use SMTP for sending emails instead of PHP mail().</div>
                </div>

                <!-- SMTP Host and Port -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_host" class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="mail.example.com" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="smtp_port" class="form-label">SMTP Port</label>
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" placeholder="587" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>">
                    </div>
                </div>

                <!-- Authentication -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_username" class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" placeholder="your-email@example.com" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="smtp_password" class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="••••••••••" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>">
                    </div>
                </div>

                <!-- Encryption -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_encryption" class="form-label">Encryption Type</label>
                        <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                            <option value="tls" <?= ($settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS (Recommended / 587)</option>
                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL (465)</option>
                            <option value="none" <?= ($settings['smtp_encryption'] ?? '') == 'none' ? 'selected' : '' ?>>None (25)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- From Address Section -->
        <div class="settings-section">
            <div class="section-header from-section">
                <span class="section-icon">✉️</span>
                <div class="section-title-group">
                    <h2>From Address</h2>
                    <p>Configure the sender email information</p>
                </div>
            </div>

            <div class="section-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="from_email" class="form-label">From Email Address</label>
                        <input type="email" class="form-control" id="from_email" name="from_email" placeholder="noreply@example.com" value="<?= htmlspecialchars($settings['from_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="from_name" class="form-label">From Name</label>
                        <input type="text" class="form-control" id="from_name" name="from_name" placeholder="Your Company Name" value="<?= htmlspecialchars($settings['from_name'] ?? '') ?>">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">💾 Save</button>
                    <button type="button" class="btn btn-secondary" id="sendTestEmail">🧪 Send Test Email</button>
                </div>
                
                <!-- Test Email Input -->
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="test_email" class="form-label">Test Email Address</label>
                    <input type="email" class="form-control" id="test_email" name="test_email" placeholder="admin@example.com" value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>">
                    <div class="form-text">Enter the email address where you want to receive the test email.</div>
                </div>
            </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('sendTestEmail').addEventListener('click', function(e) {
        e.preventDefault();
        
        // First, save the settings before sending test email
        const formData = new FormData(document.getElementById('emailSettingsForm'));
        
        // Save settings first
        fetch('<?php echo app_base_url("/admin/settings/update"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Settings saved:', data.message);
                // Now send test email
                sendTestEmail();
            } else {
                showNotification('Failed to save settings: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            showNotification('Error saving settings. Attempting to send test email anyway...', 'warning');
            sendTestEmail();
        });
    });

    function sendTestEmail() {
        const testEmail = document.getElementById('test_email').value;
        if (!testEmail) {
            showNotification('Please enter a test email address', 'warning');
            return;
        }
        
        showConfirmModal('Send Test Email', 'Send a test email to ' + testEmail + ' to verify SMTP settings?', () => {
            const button = document.getElementById('sendTestEmail');
            button.disabled = true;
            button.textContent = '⏳ Sending...';

            fetch('<?php echo app_base_url("/admin/email/send-test"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                },
                body: JSON.stringify({test_email: testEmail})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                button.disabled = false;
                button.textContent = '🧪 Send Test Email';
                
                if (data.success) {
                    console.log('✅ Success:', data.message);
                    const message = data.message || 'Test email sent successfully!';
                    showNotification(message, 'success');
                } else {
                    console.log('❌ Error:', data.message);
                    const message = data.message || 'Failed to send test email';
                    showNotification(message, 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.textContent = '🧪 Send Test Email';
                console.error('Error:', error);
                showNotification('Error: ' + error.message, 'error');
            });
        });
    }

    // Handle form submission with visual feedback
    document.getElementById('emailSettingsForm').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = '⏳ Saving...';
        
        // Re-enable after 3 seconds
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.textContent = '💾 Save';
        }, 3000);
    });
</script>