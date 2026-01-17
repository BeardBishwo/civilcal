<?php

/**
 * Email Settings Page - Premium Design
 */
?>
<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<style>
    .email-settings-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem;
        font-family: 'Inter', sans-serif;
    }

    .settings-header {
        margin-bottom: 2rem;
        animation: slideDown 0.6s ease-out;
    }

    .settings-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .settings-header p {
        font-size: 1rem;
        color: #718096;
    }

    /* Cards */
    .settings-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
    }

    .settings-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-header.driver-header {
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%);
        color: #555;
    }

    .section-header.driver-header h2,
    .section-header.driver-header .section-icon {
        color: #2d3748;
    }

    .section-icon {
        font-size: 1.5rem;
    }

    .section-title-group h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    .section-title-group p {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0;
    }

    .section-body {
        padding: 2rem;
    }

    /* Forms */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.2s;
        font-size: 1rem;
        background: #f8fafc;
    }

    .form-control:focus {
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .driver-select {
        font-size: 1.1rem;
        padding: 1rem;
        border-color: #cbd5e0;
    }

    /* Buttons */
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .btn-test {
        background: white;
        color: #10b981;
        border: 2px solid #10b981;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-test:hover {
        background: #ecfdf5;
    }

    /* Animations */
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

    /* Columns */
    .row-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 900px) {
        .row-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="email-settings-container">
    <div class="settings-header">
        <h1>📧 Email System</h1>
        <p>Configure your delivery drivers and verify connections.</p>
    </div>

    <form action="/admin/settings/save" method="POST" id="emailSettingsForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="setting_group" value="email">

        <div class="row-grid">
            <!-- Main Configuration -->
            <div class="main-column">

                <!-- Driver Selection Card -->
                <div class="settings-section">
                    <div class="section-header driver-header">
                        <span class="section-icon">🚀</span>
                        <div class="section-title-group">
                            <h2>Sending Driver</h2>
                            <p>Choose your email delivery service provider</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">Active Driver</label>
                            <select class="form-control driver-select" name="driver" id="driverSelect" onchange="toggleDrivers()">
                                <option value="smtp" <?php echo ($settings['driver'] ?? 'smtp') === 'smtp' ? 'selected' : ''; ?>>SMTP (Standard)</option>
                                <option value="active_campaign" <?php echo ($settings['driver'] ?? '') === 'active_campaign' ? 'selected' : ''; ?>>ActiveCampaign (Postmark)</option>
                                <option value="sendgrid" <?php echo ($settings['driver'] ?? '') === 'sendgrid' ? 'selected' : ''; ?>>SendGrid</option>
                                <option value="mailgun" <?php echo ($settings['driver'] ?? '') === 'mailgun' ? 'selected' : ''; ?>>Mailgun</option>
                                <option value="brevo" <?php echo ($settings['driver'] ?? '') === 'brevo' ? 'selected' : ''; ?>>Brevo (Sendinblue)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div id="smtp_settings" class="driver-settings settings-section">
                    <div class="section-header">
                        <span class="section-icon">🔌</span>
                        <div class="section-title-group">
                            <h2>SMTP Configuration</h2>
                            <p>Connection details for your mail server</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" name="smtp_host" value="<?php echo $settings['smtp_host'] ?? ''; ?>" placeholder="smtp.example.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Port</label>
                                <input type="text" class="form-control" name="smtp_port" value="<?php echo $settings['smtp_port'] ?? '587'; ?>" placeholder="587">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="smtp_username" value="<?php echo $settings['smtp_username'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="smtp_password" value="<?php echo $settings['smtp_password'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Encryption</label>
                            <select class="form-control" name="smtp_encryption">
                                <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="" <?php echo ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ActiveCampaign -->
                <div id="active_campaign_settings" class="driver-settings settings-section" style="display:none;">
                    <div class="section-header">
                        <span class="section-icon">📢</span>
                        <div class="section-title-group">
                            <h2>ActiveCampaign</h2>
                            <p>Marketing automation integration</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">API URL</label>
                            <input type="text" class="form-control" name="active_campaign_url" value="<?php echo $settings['active_campaign_url'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="password" class="form-control" name="active_campaign_key" value="<?php echo $settings['active_campaign_key'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- SendGrid -->
                <div id="sendgrid_settings" class="driver-settings settings-section" style="display:none;">
                    <div class="section-header">
                        <span class="section-icon">📧</span>
                        <div class="section-title-group">
                            <h2>SendGrid API</h2>
                            <p>High-volume delivery service</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="password" class="form-control" name="sendgrid_key" value="<?php echo $settings['sendgrid_key'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Mailgun -->
                <div id="mailgun_settings" class="driver-settings settings-section" style="display:none;">
                    <div class="section-header">
                        <span class="section-icon">📫</span>
                        <div class="section-title-group">
                            <h2>Mailgun API</h2>
                            <p>Developer-focused email API</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">Domain</label>
                            <input type="text" class="form-control" name="mailgun_domain" value="<?php echo $settings['mailgun_domain'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="password" class="form-control" name="mailgun_key" value="<?php echo $settings['mailgun_key'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Endpoint</label>
                            <select class="form-control" name="mailgun_endpoint">
                                <option value="api.mailgun.net" <?php echo ($settings['mailgun_endpoint'] ?? 'api.mailgun.net') === 'api.mailgun.net' ? 'selected' : ''; ?>>US (api.mailgun.net)</option>
                                <option value="api.eu.mailgun.net" <?php echo ($settings['mailgun_endpoint'] ?? '') === 'api.eu.mailgun.net' ? 'selected' : ''; ?>>EU (api.eu.mailgun.net)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Brevo -->
                <div id="brevo_settings" class="driver-settings settings-section" style="display:none;">
                    <div class="section-header">
                        <span class="section-icon">📨</span>
                        <div class="section-title-group">
                            <h2>Brevo (SendinBlue)</h2>
                            <p>Marketing & Transactional API</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="password" class="form-control" name="brevo_key" value="<?php echo $settings['brevo_key'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Sender Identity -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="section-icon">👤</span>
                        <div class="section-title-group">
                            <h2>Sender Identity</h2>
                            <p>How you appear in inboxes</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="form-label">From Name</label>
                                <input type="text" class="form-control" name="from_name" value="<?php echo $settings['from_name'] ?? 'Bishwo Calculator'; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">From Email</label>
                                <input type="email" class="form-control" name="from_email" value="<?php echo $settings['from_email'] ?? 'noreply@example.com'; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save">💾 Save Configuration</button>
            </div>

            <!-- Sidebar / Testing -->
            <div class="side-column">
                <div class="settings-section">
                    <div class="section-body" style="background: #f0fff4; border-left: 4px solid #10b981;">
                        <h3 style="margin-top:0; color:#047857; font-size:1.1rem; font-weight:700;">✅ Connection Test</h3>
                        <p style="font-size:0.9rem; color:#065f46; margin-bottom:1rem;">Verify your settings instantly.</p>

                        <div class="form-group">
                            <input type="email" class="form-control" id="testEmail" placeholder="your@email.com" style="background:white;">
                        </div>
                        <button type="button" class="btn-test" onclick="sendTestEmail()">Send Test Email</button>
                    </div>
                </div>

                <div class="settings-section">
                    <div class="section-body">
                        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem;">ℹ️ Quick Tips</h4>
                        <ul style="padding-left:1.2rem; margin:0; font-size:0.9rem; color:#4a5568; line-height:1.6;">
                            <li><strong>SMTP</strong> is best for low volumes or personal servers.</li>
                            <li><strong>API Drivers</strong> (SendGrid, Mailgun) are recommended for high deliverability.</li>
                            <li>Always <strong>Save</strong> before testing a new driver.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleDrivers() {
        const driver = document.getElementById('driverSelect').value;
        document.querySelectorAll('.driver-settings').forEach(el => el.style.display = 'none');

        const target = document.getElementById(driver + '_settings');
        if (target) {
            target.style.display = 'block';
            target.style.animation = 'fadeInUp 0.5s ease';
        }
    }

    // Initialize on load
    toggleDrivers();

    async function sendTestEmail() {
        const email = document.getElementById('testEmail').value;
        if (!email) {
            alert('Please enter a test email address.');
            return;
        }

        const btn = event.target;
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Sending...';
        btn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

            const res = await fetch('/admin/api/settings/test-email', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                alert('Success! ' + data.message);
            } else {
                alert('Failed: ' + data.message);
            }
        } catch (e) {
            alert('Error connecting to server.');
            console.error(e);
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }

    // AJAX Save
    document.getElementById('emailSettingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = e.target.querySelector('.btn-save');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Saving...';
        btn.disabled = true;

        try {
            const formData = new FormData(e.target);
            const res = await fetch('/admin/settings/save', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                btn.innerHTML = '✅ Saved Successfully!';
                btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                setTimeout(() => {
                    btn.innerHTML = origText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
            } else {
                alert('Save failed: ' + data.message);
                btn.innerHTML = origText;
                btn.disabled = false;
            }
        } catch (e) {
            alert('Save error.');
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    });
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>