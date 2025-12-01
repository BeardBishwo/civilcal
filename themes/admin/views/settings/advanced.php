<?php
$page_title = $page_title ?? 'Advanced Settings';
$advanced_settings = $advanced_settings ?? [];
$system_info = $system_info ?? [];
$debug_options = $debug_options ?? [];
require_once __DIR__ . '/../../layouts/main.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-cogs"></i> Advanced Settings</h1>
        <p>Configure advanced system settings and debugging options</p>
        <div class="page-actions">
            <button class="btn btn-warning" onclick="resetToDefaults()">
                <i class="fas fa-undo"></i> Reset to Defaults
            </button>
            <button class="btn btn-primary" onclick="saveAdvancedSettings()">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </div>

    <!-- System Information -->
    <div class="system-info-section">
        <h3>System Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Application Version</label>
                <span><?= htmlspecialchars($system_info['app_version'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>PHP Version</label>
                <span><?= htmlspecialchars($system_info['php_version'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Database Version</label>
                <span><?= htmlspecialchars($system_info['db_version'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Web Server</label>
                <span><?= htmlspecialchars($system_info['web_server'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Operating System</label>
                <span><?= htmlspecialchars($system_info['os'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Memory Limit</label>
                <span><?= htmlspecialchars($system_info['memory_limit'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Max Upload Size</label>
                <span><?= htmlspecialchars($system_info['upload_max_size'] ?? 'Unknown') ?></span>
            </div>
            <div class="info-item">
                <label>Time Zone</label>
                <span><?= htmlspecialchars($system_info['timezone'] ?? 'Unknown') ?></span>
            </div>
        </div>
    </div>

    <div class="advanced-settings-container">
        <!-- Performance Settings -->
        <div class="settings-section">
            <h3>Performance Settings</h3>
            <form id="performance-settings">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="form-group">
                    <label for="cache-enabled">
                        <input type="checkbox" id="cache-enabled" name="cache_enabled" 
                               <?= ($advanced_settings['cache_enabled'] ?? true) ? 'checked' : '' ?>>
                        Enable Caching
                    </label>
                    <small class="form-text text-muted">
                        Enable system-wide caching for improved performance
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="cache-ttl">Cache TTL (seconds)</label>
                        <input type="number" class="form-control" id="cache-ttl" name="cache_ttl" 
                               value="<?= $advanced_settings['cache_ttl'] ?? 3600 ?>" min="60" max="86400">
                        <small class="form-text text-muted">
                            How long cached data remains valid
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cache-driver">Cache Driver</label>
                        <select class="form-control" id="cache-driver" name="cache_driver">
                            <option value="file" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'file' ? 'selected' : '' ?>>File</option>
                            <option value="redis" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'redis' ? 'selected' : '' ?>>Redis</option>
                            <option value="memcached" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'memcached' ? 'selected' : '' ?>>Memcached</option>
                            <option value="database" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'database' ? 'selected' : '' ?>>Database</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="compression-enabled">
                        <input type="checkbox" id="compression-enabled" name="compression_enabled" 
                               <?= ($advanced_settings['compression_enabled'] ?? false) ? 'checked' : '' ?>>
                        Enable Output Compression
                    </label>
                    <small class="form-text text-muted">
                        Compress HTML output for faster page loads
                    </small>
                </div>

                <div class="form-group">
                    <label for="minify-assets">
                        <input type="checkbox" id="minify-assets" name="minify_assets" 
                               <?= ($advanced_settings['minify_assets'] ?? false) ? 'checked' : '' ?>>
                        Minify CSS and JavaScript
                    </label>
                    <small class="form-text text-muted">
                        Automatically minify assets for improved performance
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="session-lifetime">Session Lifetime (minutes)</label>
                        <input type="number" class="form-control" id="session-lifetime" name="session_lifetime" 
                               value="<?= $advanced_settings['session_lifetime'] ?? 120 ?>" min="5" max="1440">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="max-concurrent-users">Max Concurrent Users</label>
                        <input type="number" class="form-control" id="max-concurrent-users" name="max_concurrent_users" 
                               value="<?= $advanced_settings['max_concurrent_users'] ?? 1000 ?>" min="10" max="10000">
                    </div>
                </div>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="settings-section">
            <h3>Security Settings</h3>
            <form id="security-settings">
                <div class="form-group">
                    <label for="force-https">
                        <input type="checkbox" id="force-https" name="force_https" 
                               <?= ($advanced_settings['force_https'] ?? false) ? 'checked' : '' ?>>
                        Force HTTPS
                    </label>
                    <small class="form-text text-muted">
                        Redirect all HTTP requests to HTTPS
                    </small>
                </div>

                <div class="form-group">
                    <label for="security-headers">
                        <input type="checkbox" id="security-headers" name="security_headers" 
                               <?= ($advanced_settings['security_headers'] ?? true) ? 'checked' : '' ?>>
                        Enable Security Headers
                    </label>
                    <small class="form-text text-muted">
                        Add security headers to all responses
                    </small>
                </div>

                <div class="form-group">
                    <label for="rate-limiting">
                        <input type="checkbox" id="rate-limiting" name="rate_limiting" 
                               <?= ($advanced_settings['rate_limiting'] ?? true) ? 'checked' : '' ?>>
                        Enable Rate Limiting
                    </label>
                    <small class="form-text text-muted">
                        Limit number of requests per time period
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="rate-limit-requests">Rate Limit (requests/minute)</label>
                        <input type="number" class="form-control" id="rate-limit-requests" name="rate_limit_requests" 
                               value="<?= $advanced_settings['rate_limit_requests'] ?? 60 ?>" min="1" max="1000">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="login-attempts">Max Login Attempts</label>
                        <input type="number" class="form-control" id="login-attempts" name="login_attempts" 
                               value="<?= $advanced_settings['login_attempts'] ?? 5 ?>" min="1" max="20">
                    </div>
                </div>

                <div class="form-group">
                    <label for="csrf-protection">
                        <input type="checkbox" id="csrf-protection" name="csrf_protection" 
                               <?= ($advanced_settings['csrf_protection'] ?? true) ? 'checked' : '' ?>>
                        Enable CSRF Protection
                    </label>
                    <small class="form-text text-muted">
                        Protect forms from Cross-Site Request Forgery attacks
                    </small>
                </div>

                <div class="form-group">
                    <label for="xss-protection">
                        <input type="checkbox" id="xss-protection" name="xss_protection" 
                               <?= ($advanced_settings['xss_protection'] ?? true) ? 'checked' : '' ?>>
                        Enable XSS Protection
                    </label>
                    <small class="form-text text-muted">
                        Filter and sanitize user input for XSS attacks
                    </small>
                </div>
            </form>
        </div>

        <!-- Debug Settings -->
        <div class="settings-section">
            <h3>Debug Settings</h3>
            <form id="debug-settings">
                <div class="form-group">
                    <label for="debug-mode">
                        <input type="checkbox" id="debug-mode" name="debug_mode" 
                               <?= ($advanced_settings['debug_mode'] ?? false) ? 'checked' : '' ?>>
                        Enable Debug Mode
                    </label>
                    <small class="form-text text-muted">
                        Show detailed error messages and debug information
                    </small>
                </div>

                <div class="form-group">
                    <label for="error-logging">
                        <input type="checkbox" id="error-logging" name="error_logging" 
                               <?= ($advanced_settings['error_logging'] ?? true) ? 'checked' : '' ?>>
                        Enable Error Logging
                    </label>
                    <small class="form-text text-muted">
                        Log errors to file for troubleshooting
                    </small>
                </div>

                <div class="form-group">
                    <label for="query-debug">
                        <input type="checkbox" id="query-debug" name="query_debug" 
                               <?= ($advanced_settings['query_debug'] ?? false) ? 'checked' : '' ?>>
                        Enable Query Debug
                    </label>
                    <small class="form-text text-muted">
                        Log database queries for performance analysis
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="log-level">Log Level</label>
                        <select class="form-control" id="log-level" name="log_level">
                            <option value="error" <?= ($advanced_settings['log_level'] ?? 'error') === 'error' ? 'selected' : '' ?>>Error</option>
                            <option value="warning" <?= ($advanced_settings['log_level'] ?? 'error') === 'warning' ? 'selected' : '' ?>>Warning</option>
                            <option value="info" <?= ($advanced_settings['log_level'] ?? 'error') === 'info' ? 'selected' : '' ?>>Info</option>
                            <option value="debug" <?= ($advanced_settings['log_level'] ?? 'error') === 'debug' ? 'selected' : '' ?>>Debug</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="performance-monitoring">Performance Monitoring</label>
                        <select class="form-control" id="performance-monitoring" name="performance_monitoring">
                            <option value="disabled" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                            <option value="basic" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'basic' ? 'selected' : '' ?>>Basic</option>
                            <option value="detailed" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'detailed' ? 'selected' : '' ?>>Detailed</option>
                            <option value="comprehensive" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'comprehensive' ? 'selected' : '' ?>>Comprehensive</option>
                        </select>
                    </div>
                </div>

                <div class="debug-actions">
                    <button type="button" class="btn btn-outline-info" onclick="viewSystemLogs()">
                        <i class="fas fa-file-alt"></i> View System Logs
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="clearSystemLogs()">
                        <i class="fas fa-trash"></i> Clear System Logs
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="runDiagnostics()">
                        <i class="fas fa-stethoscope"></i> Run Diagnostics
                    </button>
                </div>
            </form>
        </div>

        <!-- API Settings -->
        <div class="settings-section">
            <h3>API Settings</h3>
            <form id="api-settings">
                <div class="form-group">
                    <label for="api-enabled">
                        <input type="checkbox" id="api-enabled" name="api_enabled" 
                               <?= ($advanced_settings['api_enabled'] ?? true) ? 'checked' : '' ?>>
                        Enable API
                    </label>
                    <small class="form-text text-muted">
                        Enable REST API for external integrations
                    </small>
                </div>

                <div class="form-group">
                    <label for="api-key">API Key</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="api-key" name="api_key" 
                               value="<?= $advanced_settings['api_key'] ?? '' ?>" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateApiKey()">
                                <i class="fas fa-sync"></i> Generate
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        API key for authenticating external requests
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="api-rate-limit">API Rate Limit (requests/hour)</label>
                        <input type="number" class="form-control" id="api-rate-limit" name="api_rate_limit" 
                               value="<?= $advanced_settings['api_rate_limit'] ?? 1000 ?>" min="1" max="10000">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="api-timeout">API Timeout (seconds)</label>
                        <input type="number" class="form-control" id="api-timeout" name="api_timeout" 
                               value="<?= $advanced_settings['api_timeout'] ?? 30 ?>" min="1" max="300">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cors-origins">CORS Allowed Origins</label>
                    <textarea class="form-control" id="cors-origins" name="cors_origins" rows="3"
                              placeholder="https://example.com&#10;https://app.example.com"><?= htmlspecialchars($advanced_settings['cors_origins'] ?? '') ?></textarea>
                    <small class="form-text text-muted">
                        Enter one origin per line. Use * for all origins.
                    </small>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeAdvancedSettings();
});

function initializeAdvancedSettings() {
    // Handle form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAdvancedSettings();
        });
    });
}

function saveAdvancedSettings() {
    const formData = new FormData();
    
    // Collect all form data
    document.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.type === 'checkbox') {
            formData.append(field.name, field.checked ? '1' : '0');
        } else {
            formData.append(field.name, field.value);
        }
    });
    
    // Add CSRF token
    formData.append('csrf_token', '<?= csrf_token() ?>');
    
    showNotification('Saving advanced settings...', 'info');
    
    fetch('<?= app_base_url('/admin/settings/advanced/save') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Advanced settings saved successfully', 'success');
        } else {
            showNotification('Failed to save settings: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving settings', 'error');
    });
}

function resetToDefaults() {
    if (confirm('Are you sure you want to reset all advanced settings to their default values? This action cannot be undone.')) {
        fetch('<?= app_base_url('/admin/settings/advanced/reset') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Settings reset to defaults', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Failed to reset settings', 'error');
            }
        });
    }
}

function generateApiKey() {
    fetch('<?= app_base_url('/admin/settings/advanced/generate-api-key') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('api-key').value = data.api_key;
            showNotification('New API key generated', 'success');
        } else {
            showNotification('Failed to generate API key', 'error');
        }
    });
}

function viewSystemLogs() {
    window.open('<?= app_base_url('/admin/logs/view') ?>', '_blank');
}

function clearSystemLogs() {
    if (confirm('Are you sure you want to clear all system logs? This action cannot be undone.')) {
        fetch('<?= app_base_url('/admin/logs/clear') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('System logs cleared successfully', 'success');
            } else {
                showNotification('Failed to clear logs', 'error');
            }
        });
    }
}

function runDiagnostics() {
    showNotification('Running system diagnostics...', 'info');
    
    fetch('<?= app_base_url('/admin/diagnostics/run') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Diagnostics completed successfully', 'success');
            window.open('<?= app_base_url('/admin/diagnostics/results') ?>', '_blank');
        } else {
            showNotification('Diagnostics failed: ' + data.message, 'error');
        }
    });
}
</script>

<style>
.system-info-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.system-info-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 500;
    color: #495057;
}

.info-item span {
    color: #212529;
    font-family: monospace;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
}

.advanced-settings-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin: 20px 0;
}

.settings-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.settings-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    display: block;
}

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group-append {
    display: flex;
    margin-left: -1px;
}

.debug-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .debug-actions {
        flex-direction: column;
    }
}
</style>
