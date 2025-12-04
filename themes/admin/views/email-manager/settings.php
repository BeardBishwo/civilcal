<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-cog"></i> Email Settings</h1>
            <p class="page-description">Configure email server and sender settings</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-server"></i>
                    SMTP Configuration
                </h5>
            </div>
            <div class="card-content">
                <form method="POST" action="<?php echo app_base_url('/admin/email-manager/settings/update'); ?>" id="settingsForm">
                    <div class="form-group mb-4">
                        <label class="form-label">SMTP Host *</label>
                        <input type="text" name="email_smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label class="form-label">SMTP Port *</label>
                            <input type="number" name="email_smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Encryption</label>
                            <select name="email_smtp_secure" class="form-select">
                                <option value="tls" <?php echo (isset($settings['smtp_encryption']) && $settings['smtp_encryption'] === 'tls') ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo (isset($settings['smtp_encryption']) && $settings['smtp_encryption'] === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                                <option value="" <?php echo (!isset($settings['smtp_encryption']) || empty($settings['smtp_encryption'])) ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="email_smtp_user" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="email_smtp_pass" class="form-control" value="">
                        <div class="form-help">
                            Leave blank to keep the current password
                        </div>
                    </div>
                    
                    <div class="card-divider"></div>
                    
                    <h6 class="text-lg font-medium mb-4">Sender Information</h6>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">From Name *</label>
                        <input type="text" name="email_from_name" class="form-control" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">From Email Address *</label>
                        <input type="email" name="email_from_address" class="form-control" value="<?php echo htmlspecialchars($settings['from_address'] ?? $settings['from_email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="testEmailSettings()">
                            <i class="fas fa-paper-plane"></i> Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="space-y-6">
        <!-- Email Test -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-flask"></i>
                    Test Email Settings
                </h5>
            </div>
            <div class="card-content">
                <div class="form-group mb-4">
                    <label class="form-label">Test Email Address</label>
                    <input type="email" id="testEmail" class="form-control" placeholder="your@email.com">
                </div>
                <button type="button" class="btn btn-outline-primary w-full" onclick="sendTestEmail()">
                    <i class="fas fa-paper-plane"></i> Send Test Email
                </button>
                <div id="testResult" class="mt-3 text-sm hidden"></div>
            </div>
        </div>
        
        <!-- Configuration Tips -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Configuration Tips
                </h5>
            </div>
            <div class="card-content">
                <div class="text-sm space-y-3">
                    <div>
                        <h6 class="font-medium text-gray-900">Common SMTP Settings</h6>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                            <li><strong>Gmail:</strong> smtp.gmail.com:587 (TLS)</li>
                            <li><strong>Outlook:</strong> smtp-mail.outlook.com:587 (TLS)</li>
                            <li><strong>Yahoo:</strong> smtp.mail.yahoo.com:587 (TLS)</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h6 class="font-medium text-gray-900">Security Notes</h6>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                            <li>Use app-specific passwords for Gmail</li>
                            <li>Enable "Less secure app access" for testing</li>
                            <li>Always use TLS/SSL encryption in production</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const testEmail = document.getElementById('testEmail').value;
    const testResult = document.getElementById('testResult');
    
    if (!testEmail) {
        alert('Please enter a test email address');
        return;
    }
    
    testResult.classList.remove('hidden', 'text-green-600', 'text-red-600');
    testResult.classList.add('text-blue-600');
    testResult.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending test email...';
    
    fetch('<?php echo app_base_url('/admin/email-manager/test-email'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'test_email=' + encodeURIComponent(testEmail)
    })
    .then(response => response.json())
    .then(data => {
        testResult.classList.remove('text-blue-600');
        if (data.success) {
            testResult.classList.add('text-green-600');
            testResult.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
        } else {
            testResult.classList.add('text-red-600');
            testResult.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.error || data.message || 'Failed to send test email');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        testResult.classList.remove('text-blue-600');
        testResult.classList.add('text-red-600');
        testResult.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred while sending the test email';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Settings saved successfully!');
            } else {
                alert('Error: ' + (data.error || 'Failed to save settings'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the settings');
        });
    });
    
    console.log('Email settings page loaded');
});
</script>