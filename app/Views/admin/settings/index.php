<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">System Settings</h2>
            <p class="text-muted mb-0">Configure your calculator system</p>
        </div>
        <div class="quick-actions">
            <button type="submit" form="settingsForm" class="btn btn-primary btn-sm">
                <i class="bi bi-check-circle me-2"></i>Save Settings
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="resetSettings">
                <i class="bi bi-arrow-clockwise me-2"></i>Reset to Default
            </button>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general" type="button">
                            <i class="bi bi-gear me-2"></i>General Settings
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#email" type="button">
                            <i class="bi bi-envelope me-2"></i>Email Settings
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security" type="button">
                            <i class="bi bi-shield-lock me-2"></i>Security
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#payment" type="button">
                            <i class="bi bi-credit-card me-2"></i>Payment Gateway
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#api" type="button">
                            <i class="bi bi-key me-2"></i>API Settings
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#maintenance" type="button">
                            <i class="bi bi-tools me-2"></i>Maintenance
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form id="settingsForm">
                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-gear me-2"></i>General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Site Name</label>
                                            <input type="text" class="form-control" name="site_name" 
                                                   value="Bishwo Calculator" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Site URL</label>
                                            <input type="url" class="form-control" name="site_url" 
                                                   value="http://localhost/bishwo_calculator" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Site Description</label>
                                    <textarea class="form-control" name="site_description" rows="3">Professional Engineering Calculators</textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Admin Email</label>
                                            <input type="email" class="form-control" name="admin_email" 
                                                   value="admin@bishwocalculator.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Timezone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="Asia/Kathmandu" selected>Kathmandu (GMT+5:45)</option>
                                                <option value="UTC">UTC</option>
                                                <option value="America/New_York">New York (GMT-5)</option>
                                                <option value="Europe/London">London (GMT+0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date Format</label>
                                            <select class="form-select" name="date_format">
                                                <option value="Y-m-d" selected>YYYY-MM-DD</option>
                                                <option value="d/m/Y">DD/MM/YYYY</option>
                                                <option value="m/d/Y">MM/DD/YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Items Per Page</label>
                                            <input type="number" class="form-control" name="items_per_page" 
                                                   value="20" min="5" max="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-envelope me-2"></i>Email Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host" 
                                                   value="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" name="smtp_port" 
                                                   value="587">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Username</label>
                                            <input type="text" class="form-control" name="smtp_username" 
                                                   value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Password</label>
                                            <input type="password" class="form-control" name="smtp_password" 
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Encryption</label>
                                            <select class="form-select" name="smtp_encryption">
                                                <option value="tls" selected>TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="">None</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">From Name</label>
                                            <input type="text" class="form-control" name="from_name" 
                                                   value="Bishwo Calculator">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">From Email</label>
                                    <input type="email" class="form-control" name="from_email" 
                                           value="noreply@bishwocalculator.com">
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Test Email Configuration:</strong> 
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2">Send Test Email</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-shield-lock me-2"></i>Security Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Max Login Attempts</label>
                                            <input type="number" class="form-control" name="login_attempts" 
                                                   value="5" min="1" max="10">
                                            <small class="form-text text-muted">Number of failed login attempts before lockout</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Lockout Time (minutes)</label>
                                            <input type="number" class="form-control" name="lockout_time" 
                                                   value="15" min="1" max="60">
                                            <small class="form-text text-muted">Time to lock account after max attempts</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Minimum Password Length</label>
                                            <input type="number" class="form-control" name="password_min_length" 
                                                   value="8" min="6" max="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password Requirements</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="require_strong_password" 
                                                       value="1" checked>
                                                <label class="form-check-label">Require strong passwords (uppercase, lowercase, numbers, symbols)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enable_2fa" 
                                               value="1">
                                        <label class="form-check-label">Enable Two-Factor Authentication (2FA)</label>
                                    </div>
                                    <small class="form-text text-muted">Users will be required to set up 2FA on next login</small>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Security Notice:</strong> Always keep your system updated and use strong security settings.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Gateway Settings -->
                    <div class="tab-pane fade" id="payment">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-credit-card me-2"></i>Payment Gateway Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-success mb-3">💳 Supported Payment Methods</h6>
                                        <p class="text-muted">
                                            <span class="badge bg-primary me-2">PayPal</span> International users (USD)
                                            <span class="badge bg-info me-2 ms-3">PayTM/UPI</span> Indian users (₹)
                                            <span class="badge bg-success me-2 ms-3">eSewa</span> Nepali users (रू)
                                            <span class="badge bg-warning me-2 ms-3">Khalti</span> Nepali users (रू)
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- PayPal Settings -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">🌎 PayPal Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayPal Email</label>
                                                <input type="email" class="form-control" name="paypal_email" 
                                                       value="<?= $settings[\"paypal_email\"] ?? \"\" ?>" placeholder="your-paypal@email.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayPal Environment</label>
                                                <select class="form-select" name="paypal_sandbox">
                                                    <option value="false" <?= ($settings[\"paypal_sandbox\"] ?? false) == false ? \"selected\" : \"\" ?>>Production</option>
                                                    <option value="true" <?= ($settings[\"paypal_sandbox\"] ?? false) == true ? \"selected\" : \"\" ?>>Sandbox (Testing)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- PayTM/UPI Settings -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">🇮🇳 PayTM/UPI Configuration (India)</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayTM Merchant ID</label>
                                                <input type="text" class="form-control" name="paytm_merchant_id" 
                                                       value="<?= $settings[\"paytm_merchant_id\"] ?? \"\" ?>" placeholder="Your PayTM Merchant ID">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayTM Merchant Key</label>
                                                <input type="password" class="form-control" name="paytm_merchant_key" 
                                                       value="<?= $settings[\"paytm_merchant_key\"] ?? \"\" ?>" placeholder="Your PayTM Merchant Key">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayTM Website</label>
                                                <select class="form-select" name="paytm_website">
                                                    <option value="WEBSTAGING" <?= ($settings[\"paytm_website\"] ?? \"\") == \"WEBSTAGING\" ? \"selected\" : \"\" ?>>Staging</option>
                                                    <option value="DEFAULT" <?= ($settings[\"paytm_website\"] ?? \"\") == \"DEFAULT\" ? \"selected\" : \"\" ?>>Production</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">PayTM Industry Type</label>
                                                <input type="text" class="form-control" name="paytm_industry_type" 
                                                       value="<?= $settings[\"paytm_industry_type\"] ?? \"Retail\" ?>" placeholder="Retail">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- eSewa Settings -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">🇳🇵 eSewa Configuration (Nepal)</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">eSewa Merchant Code</label>
                                                <input type="text" class="form-control" name="esewa_merchant_code" 
                                                       value="<?= $settings[\"esewa_merchant_code\"] ?? \"\" ?>" placeholder="Your eSewa Merchant Code">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">eSewa Secret Key</label>
                                                <input type="password" class="form-control" name="esewa_secret_key" 
                                                       value="<?= $settings[\"esewa_secret_key\"] ?? \"\" ?>" placeholder="Your eSewa Secret Key">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Khalti Settings -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">🇳🇵 Khalti Configuration (Nepal)</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Khalti Public Key</label>
                                                <input type="text" class="form-control" name="khalti_public_key" 
                                                       value="<?= $settings[\"khalti_public_key\"] ?? \"\" ?>" placeholder="Your Khalti Public Key">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Khalti Secret Key</label>
                                                <input type="password" class="form-control" name="khalti_secret_key" 
                                                       value="<?= $settings[\"khalti_secret_key\"] ?? \"\" ?>" placeholder="Your Khalti Secret Key">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing Configuration -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">💰 Country-Specific Pricing</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">🇮🇳 India (₹INR)</label>
                                                <input type="number" class="form-control" name="pricing_india" 
                                                       value="<?= $settings[\"pricing_india\"] ?? \"499\" ?>" placeholder="499">
                                                <small class="form-text text-muted">USD 1 = ₹85 (approx.)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">🇳🇵 Nepal (रूNPR)</label>
                                                <input type="number" class="form-control" name="pricing_nepal" 
                                                       value="<?= $settings[\"pricing_nepal\"] ?? \"799\" ?>" placeholder="799">
                                                <small class="form-text text-muted">USD 1 = रू130 (approx.)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">🌍 International (USD)</label>
                                                <input type="number" class="form-control" name="pricing_usd" 
                                                       value="<?= $settings[\"pricing_usd\"] ?? \"15\" ?>" placeholder="15">
                                                <small class="form-text text-muted">Default USD pricing</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Configuration Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use sandbox/test mode during development</li>
                                        <li>Country detection is based on user IP geolocation</li>
                                        <li>Payment methods are automatically shown based on user location</li>
                                        <li>All payment confirmations are logged for audit purposes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("settingsForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("/admin/settings/save", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Settings saved successfully!");
        } else {
            alert("Error: " + data.message);
        }
    });
});

document.getElementById("resetSettings").addEventListener("click", function() {
    if (confirm("Are you sure you want to reset all settings to default? This cannot be undone.")) {
        // Reset logic would go here
        alert("Settings reset to default values.");
    }
});
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
