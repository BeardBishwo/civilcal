<?php include '../../partials/header.php'; ?>

<div class="admin-content">
    <div class="content-header">
        <h1><i class="fas fa-cogs"></i> Application Settings</h1>
        <p>Configure application-wide settings and preferences</p>
    </div>

    <div class="settings-container">
        <form id="application-settings-form" class="settings-form">
            <?php echo csrf_field(); ?>
            
            <!-- Basic Settings -->
            <div class="settings-section">
                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="app_name">Application Name</label>
                        <input type="text" id="app_name" name="general_app_name" 
                               value="<?php echo SettingsService::get('general_app_name', 'Bishwo Calculator'); ?>" 
                               class="form-control" required>
                        <small>The name of your application</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="app_version">Application Version</label>
                        <input type="text" id="app_version" name="general_app_version" 
                               value="<?php echo SettingsService::get('general_app_version', '1.0.0'); ?>" 
                               class="form-control">
                        <small>Current version number</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="app_description">Application Description</label>
                        <textarea id="app_description" name="general_app_description" 
                                  class="form-control" rows="3"><?php echo SettingsService::get('general_app_description', ''); ?></textarea>
                        <small>Brief description of your application</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="app_keywords">Keywords</label>
                        <input type="text" id="app_keywords" name="general_app_keywords" 
                               value="<?php echo SettingsService::get('general_app_keywords', ''); ?>" 
                               class="form-control">
                        <small>SEO keywords (comma-separated)</small>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="settings-section">
                <h3><i class="fas fa-address-card"></i> Contact Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="general_contact_email" 
                               value="<?php echo SettingsService::get('general_contact_email', ''); ?>" 
                               class="form-control">
                        <small>Public contact email address</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="support_email">Support Email</label>
                        <input type="email" id="support_email" name="general_support_email" 
                               value="<?php echo SettingsService::get('general_support_email', ''); ?>" 
                               class="form-control">
                        <small>Customer support email address</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" id="company_name" name="general_company_name" 
                               value="<?php echo SettingsService::get('general_company_name', ''); ?>" 
                               class="form-control">
                        <small>Your company or organization name</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="company_address">Company Address</label>
                        <textarea id="company_address" name="general_company_address" 
                                  class="form-control" rows="2"><?php echo SettingsService::get('general_company_address', ''); ?></textarea>
                        <small>Physical address of your company</small>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="settings-section">
                <h3><i class="fas fa-share-alt"></i> Social Media</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="social_facebook">Facebook URL</label>
                        <input type="url" id="social_facebook" name="general_social_facebook" 
                               value="<?php echo SettingsService::get('general_social_facebook', ''); ?>" 
                               class="form-control" placeholder="https://facebook.com/yourpage">
                    </div>
                    
                    <div class="form-group">
                        <label for="social_twitter">Twitter URL</label>
                        <input type="url" id="social_twitter" name="general_social_twitter" 
                               value="<?php echo SettingsService::get('general_social_twitter', ''); ?>" 
                               class="form-control" placeholder="https://twitter.com/yourhandle">
                    </div>
                    
                    <div class="form-group">
                        <label for="social_linkedin">LinkedIn URL</label>
                        <input type="url" id="social_linkedin" name="general_social_linkedin" 
                               value="<?php echo SettingsService::get('general_social_linkedin', ''); ?>" 
                               class="form-control" placeholder="https://linkedin.com/company/yourcompany">
                    </div>
                    
                    <div class="form-group">
                        <label for="social_instagram">Instagram URL</label>
                        <input type="url" id="social_instagram" name="general_social_instagram" 
                               value="<?php echo SettingsService::get('general_social_instagram', ''); ?>" 
                               class="form-control" placeholder="https://instagram.com/yourhandle">
                    </div>
                </div>
            </div>

            <!-- Legal Settings -->
            <div class="settings-section">
                <h3><i class="fas fa-gavel"></i> Legal & Compliance</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="privacy_policy_url">Privacy Policy URL</label>
                        <input type="url" id="privacy_policy_url" name="general_privacy_policy_url" 
                               value="<?php echo SettingsService::get('general_privacy_policy_url', ''); ?>" 
                               class="form-control" placeholder="/privacy-policy">
                        <small>Link to your privacy policy page</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="terms_url">Terms of Service URL</label>
                        <input type="url" id="terms_url" name="general_terms_url" 
                               value="<?php echo SettingsService::get('general_terms_url', ''); ?>" 
                               class="form-control" placeholder="/terms-of-service">
                        <small>Link to your terms of service page</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cookie_policy_url">Cookie Policy URL</label>
                        <input type="url" id="cookie_policy_url" name="general_cookie_policy_url" 
                               value="<?php echo SettingsService::get('general_cookie_policy_url', ''); ?>" 
                               class="form-control" placeholder="/cookie-policy">
                        <small>Link to your cookie policy page</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="gdpr_compliance">GDPR Compliance</label>
                        <select id="gdpr_compliance" name="general_gdpr_compliance" class="form-control">
                            <option value="0" <?php echo SettingsService::get('general_gdpr_compliance', '0') == '0' ? 'selected' : ''; ?>>Disabled</option>
                            <option value="1" <?php echo SettingsService::get('general_gdpr_compliance', '0') == '1' ? 'selected' : ''; ?>>Enabled</option>
                        </select>
                        <small>Enable GDPR compliance features</small>
                    </div>
                </div>
            </div>

            <!-- Application Features -->
            <div class="settings-section">
                <h3><i class="fas fa-puzzle-piece"></i> Application Features</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="general_enable_registration" 
                                   value="1" <?php echo SettingsService::get('general_enable_registration', '1') == '1' ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Enable User Registration
                        </label>
                        <small>Allow new users to register accounts</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="general_enable_public_access" 
                                   value="1" <?php echo SettingsService::get('general_enable_public_access', '1') == '1' ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Enable Public Access
                        </label>
                        <small>Allow non-logged-in users to access the application</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="general_enable_maintenance_mode" 
                                   value="1" <?php echo SettingsService::get('general_enable_maintenance_mode', '0') == '1' ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Maintenance Mode
                        </label>
                        <small>Put the application in maintenance mode</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="general_enable_debug_mode" 
                                   value="1" <?php echo SettingsService::get('general_enable_debug_mode', '0') == '1' ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Debug Mode
                        </label>
                        <small>Enable debugging features (development only)</small>
                    </div>
                </div>
            </div>

            <!-- Localization -->
            <div class="settings-section">
                <h3><i class="fas fa-globe"></i> Localization</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="default_language">Default Language</label>
                        <select id="default_language" name="general_default_language" class="form-control">
                            <option value="en" <?php echo SettingsService::get('general_default_language', 'en') == 'en' ? 'selected' : ''; ?>>English</option>
                            <option value="es" <?php echo SettingsService::get('general_default_language', 'en') == 'es' ? 'selected' : ''; ?>>Spanish</option>
                            <option value="fr" <?php echo SettingsService::get('general_default_language', 'en') == 'fr' ? 'selected' : ''; ?>>French</option>
                            <option value="de" <?php echo SettingsService::get('general_default_language', 'en') == 'de' ? 'selected' : ''; ?>>German</option>
                            <option value="it" <?php echo SettingsService::get('general_default_language', 'en') == 'it' ? 'selected' : ''; ?>>Italian</option>
                            <option value="pt" <?php echo SettingsService::get('general_default_language', 'en') == 'pt' ? 'selected' : ''; ?>>Portuguese</option>
                            <option value="ru" <?php echo SettingsService::get('general_default_language', 'en') == 'ru' ? 'selected' : ''; ?>>Russian</option>
                            <option value="zh" <?php echo SettingsService::get('general_default_language', 'en') == 'zh' ? 'selected' : ''; ?>>Chinese</option>
                            <option value="ja" <?php echo SettingsService::get('general_default_language', 'en') == 'ja' ? 'selected' : ''; ?>>Japanese</option>
                        </select>
                        <small>Default language for the application</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone" name="general_timezone" class="form-control">
                            <option value="UTC" <?php echo SettingsService::get('general_timezone', 'UTC') == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                            <option value="America/New_York" <?php echo SettingsService::get('general_timezone', 'UTC') == 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                            <option value="America/Chicago" <?php echo SettingsService::get('general_timezone', 'UTC') == 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                            <option value="America/Denver" <?php echo SettingsService::get('general_timezone', 'UTC') == 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                            <option value="America/Los_Angeles" <?php echo SettingsService::get('general_timezone', 'UTC') == 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                            <option value="Europe/London" <?php echo SettingsService::get('general_timezone', 'UTC') == 'Europe/London' ? 'selected' : ''; ?>>London</option>
                            <option value="Europe/Paris" <?php echo SettingsService::get('general_timezone', 'UTC') == 'Europe/Paris' ? 'selected' : ''; ?>>Paris</option>
                            <option value="Asia/Tokyo" <?php echo SettingsService::get('general_timezone', 'UTC') == 'Asia/Tokyo' ? 'selected' : ''; ?>>Tokyo</option>
                            <option value="Asia/Shanghai" <?php echo SettingsService::get('general_timezone', 'UTC') == 'Asia/Shanghai' ? 'selected' : ''; ?>>Shanghai</option>
                        </select>
                        <small>Default timezone for the application</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_format">Date Format</label>
                        <select id="date_format" name="general_date_format" class="form-control">
                            <option value="Y-m-d" <?php echo SettingsService::get('general_date_format', 'Y-m-d') == 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                            <option value="m/d/Y" <?php echo SettingsService::get('general_date_format', 'Y-m-d') == 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                            <option value="d/m/Y" <?php echo SettingsService::get('general_date_format', 'Y-m-d') == 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                            <option value="F j, Y" <?php echo SettingsService::get('general_date_format', 'Y-m-d') == 'F j, Y' ? 'selected' : ''; ?>>Month DD, YYYY</option>
                            <option value="j F Y" <?php echo SettingsService::get('general_date_format', 'Y-m-d') == 'j F Y' ? 'selected' : ''; ?>>DD Month YYYY</option>
                        </select>
                        <small>Default date format</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="time_format">Time Format</label>
                        <select id="time_format" name="general_time_format" class="form-control">
                            <option value="H:i:s" <?php echo SettingsService::get('general_time_format', 'H:i:s') == 'H:i:s' ? 'selected' : ''; ?>>24-hour (HH:MM:SS)</option>
                            <option value="g:i A" <?php echo SettingsService::get('general_time_format', 'H:i:s') == 'g:i A' ? 'selected' : ''; ?>>12-hour (HH:MM AM/PM)</option>
                            <option value="H:i" <?php echo SettingsService::get('general_time_format', 'H:i:s') == 'H:i' ? 'selected' : ''; ?>>24-hour (HH:MM)</option>
                            <option value="g:i:s A" <?php echo SettingsService::get('general_time_format', 'H:i:s') == 'g:i:s A' ? 'selected' : ''; ?>>12-hour (HH:MM:SS AM/PM)</option>
                        </select>
                        <small>Default time format</small>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetApplicationSettings()">
                    <i class="fas fa-undo"></i> Reset to Defaults
                </button>
                <button type="button" class="btn btn-info" onclick="exportApplicationSettings()">
                    <i class="fas fa-download"></i> Export Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Application settings JavaScript
document.getElementById('application-settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;
    
    fetch('/admin/settings/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Application settings saved successfully!', 'success');
        } else {
            showNotification('Error saving settings: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving settings. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function resetApplicationSettings() {
    if (confirm('Are you sure you want to reset all application settings to their default values? This action cannot be undone.')) {
        fetch('/admin/settings/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            },
            body: JSON.stringify({ group: 'general' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Application settings reset to defaults!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error resetting settings: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error resetting settings. Please try again.', 'error');
        });
    }
}

function exportApplicationSettings() {
    window.open('/admin/settings/export', '_blank');
}

// Maintenance mode warning
document.getElementById('general_enable_maintenance_mode').addEventListener('change', function() {
    if (this.checked) {
        showNotification('Warning: Enabling maintenance mode will make the application inaccessible to regular users.', 'warning');
    }
});

// Debug mode warning
document.getElementById('general_enable_debug_mode').addEventListener('change', function() {
    if (this.checked) {
        showNotification('Warning: Debug mode should only be enabled in development environments.', 'warning');
    }
});
</script>

<style>
.settings-container {
    max-width: 1200px;
    margin: 0 auto;
}

.settings-form {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-section {
    border-bottom: 1px solid #e5e7eb;
    padding: 2rem;
}

.settings-section:last-child {
    border-bottom: none;
}

.settings-section h3 {
    margin: 0 0 1.5rem 0;
    color: #374151;
    font-size: 1.25rem;
    font-weight: 600;
}

.settings-section h3 i {
    margin-right: 0.5rem;
    color: #6366f1;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group small {
    margin-top: 0.25rem;
    color: #6b7280;
    font-size: 0.75rem;
}

.checkbox-label {
    display: flex !important;
    align-items: center;
    cursor: pointer;
    font-weight: normal !important;
}

.checkbox-label input[type="checkbox"] {
    width: auto !important;
    margin-right: 0.5rem;
}

.form-actions {
    padding: 2rem;
    background: #f9fafb;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: #6366f1;
    color: white;
}

.btn-primary:hover {
    background: #5558e3;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #5b5f69;
}

.btn-info {
    background: #0ea5e9;
    color: white;
}

.btn-info:hover {
    background: #0284c7;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .settings-section {
        padding: 1rem;
    }
}
</style>

<?php include '../partials/footer.php'; ?>