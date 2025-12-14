<?php
/**
 * PREMIUM ADVANCED SETTINGS INTERFACE
 * Matching the design of other admin pages
 */

$page_title = $page_title ?? 'Advanced Settings';
$advanced_settings = $advanced_settings ?? [];
$system_info = $system_info ?? [];
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-cogs"></i>
                    <h1>Advanced Settings</h1>
                </div>
                <div class="header-subtitle">Configure system performance, security, and debugging options</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-warning btn-compact" onclick="resetToDefaults()">
                    <i class="fas fa-undo"></i>
                    <span>Reset to Defaults</span>
                </button>
                <button class="btn btn-primary btn-compact" onclick="saveAdvancedSettings()">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>

        <!-- System Information Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-code-branch"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo htmlspecialchars($system_info['app_version'] ?? '1.0.0'); ?></div>
                    <div class="stat-label">App Version</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fab fa-php"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo htmlspecialchars($system_info['php_version'] ?? PHP_VERSION); ?></div>
                    <div class="stat-label">PHP Version</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo htmlspecialchars($system_info['db_version'] ?? 'MySQL'); ?></div>
                    <div class="stat-label">Database</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-memory"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo htmlspecialchars($system_info['memory_limit'] ?? ini_get('memory_limit')); ?></div>
                    <div class="stat-label">Memory Limit</div>
                </div>
            </div>
        </div>

        <!-- Settings Sections -->
        <div class="settings-grid">
            <!-- Performance Settings -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <i class="fas fa-tachometer-alt"></i>
                    <h3>Performance Settings</h3>
                </div>
                <div class="settings-card-body">
                    <form id="performance-settings">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="cache_enabled" 
                                       <?= ($advanced_settings['cache_enabled'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable Caching</span>
                            </label>
                            <small>System-wide caching for improved performance</small>
                        </div>

                        <div class="form-row-compact">
                            <div class="form-group-compact">
                                <label>Cache TTL (seconds)</label>
                                <input type="number" class="form-control-compact" name="cache_ttl" 
                                       value="<?= $advanced_settings['cache_ttl'] ?? 3600 ?>" min="60" max="86400">
                            </div>
                            <div class="form-group-compact">
                                <label>Cache Driver</label>
                                <select class="form-control-compact" name="cache_driver">
                                    <option value="file" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'file' ? 'selected' : '' ?>>File</option>
                                    <option value="redis" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'redis' ? 'selected' : '' ?>>Redis</option>
                                    <option value="memcached" <?= ($advanced_settings['cache_driver'] ?? 'file') === 'memcached' ? 'selected' : '' ?>>Memcached</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="compression_enabled" 
                                       <?= ($advanced_settings['compression_enabled'] ?? false) ? 'checked' : '' ?>>
                                <span>Enable Output Compression</span>
                            </label>
                            <small>Compress HTML output for faster page loads</small>
                        </div>

                        <div class="form-row-compact">
                            <div class="form-group-compact">
                                <label>Session Lifetime (minutes)</label>
                                <input type="number" class="form-control-compact" name="session_lifetime" 
                                       value="<?= $advanced_settings['session_lifetime'] ?? 120 ?>" min="5" max="1440">
                            </div>
                            <div class="form-group-compact">
                                <label>Max Concurrent Users</label>
                                <input type="number" class="form-control-compact" name="max_concurrent_users" 
                                       value="<?= $advanced_settings['max_concurrent_users'] ?? 1000 ?>" min="10" max="10000">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Security Settings</h3>
                </div>
                <div class="settings-card-body">
                    <form id="security-settings">
                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="force_https" 
                                       <?= ($advanced_settings['force_https'] ?? false) ? 'checked' : '' ?>>
                                <span>Force HTTPS</span>
                            </label>
                            <small>Redirect all HTTP requests to HTTPS</small>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="security_headers" 
                                       <?= ($advanced_settings['security_headers'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable Security Headers</span>
                            </label>
                            <small>Add security headers to all responses</small>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="rate_limiting" 
                                       <?= ($advanced_settings['rate_limiting'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable Rate Limiting</span>
                            </label>
                            <small>Limit number of requests per time period</small>
                        </div>

                        <div class="form-row-compact">
                            <div class="form-group-compact">
                                <label>Rate Limit (requests/min)</label>
                                <input type="number" class="form-control-compact" name="rate_limit_requests" 
                                       value="<?= $advanced_settings['rate_limit_requests'] ?? 60 ?>" min="1" max="1000">
                            </div>
                            <div class="form-group-compact">
                                <label>Max Login Attempts</label>
                                <input type="number" class="form-control-compact" name="login_attempts" 
                                       value="<?= $advanced_settings['login_attempts'] ?? 5 ?>" min="1" max="20">
                            </div>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="csrf_protection" 
                                       <?= ($advanced_settings['csrf_protection'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable CSRF Protection</span>
                            </label>
                            <small>Protect forms from Cross-Site Request Forgery</small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Debug Settings -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <i class="fas fa-bug"></i>
                    <h3>Debug Settings</h3>
                </div>
                <div class="settings-card-body">
                    <form id="debug-settings">
                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="debug_mode" 
                                       <?= ($advanced_settings['debug_mode'] ?? false) ? 'checked' : '' ?>>
                                <span>Enable Debug Mode</span>
                            </label>
                            <small>Show detailed error messages (disable in production)</small>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="error_logging" 
                                       <?= ($advanced_settings['error_logging'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable Error Logging</span>
                            </label>
                            <small>Log errors to file for troubleshooting</small>
                        </div>

                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="query_debug" 
                                       <?= ($advanced_settings['query_debug'] ?? false) ? 'checked' : '' ?>>
                                <span>Enable Query Debug</span>
                            </label>
                            <small>Log database queries for performance analysis</small>
                        </div>

                        <div class="form-row-compact">
                            <div class="form-group-compact">
                                <label>Log Level</label>
                                <select class="form-control-compact" name="log_level">
                                    <option value="error" <?= ($advanced_settings['log_level'] ?? 'error') === 'error' ? 'selected' : '' ?>>Error</option>
                                    <option value="warning" <?= ($advanced_settings['log_level'] ?? 'error') === 'warning' ? 'selected' : '' ?>>Warning</option>
                                    <option value="info" <?= ($advanced_settings['log_level'] ?? 'error') === 'info' ? 'selected' : '' ?>>Info</option>
                                    <option value="debug" <?= ($advanced_settings['log_level'] ?? 'error') === 'debug' ? 'selected' : '' ?>>Debug</option>
                                </select>
                            </div>
                            <div class="form-group-compact">
                                <label>Performance Monitoring</label>
                                <select class="form-control-compact" name="performance_monitoring">
                                    <option value="disabled" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                    <option value="basic" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'basic' ? 'selected' : '' ?>>Basic</option>
                                    <option value="detailed" <?= ($advanced_settings['performance_monitoring'] ?? 'disabled') === 'detailed' ? 'selected' : '' ?>>Detailed</option>
                                </select>
                            </div>
                        </div>

                        <div class="debug-actions-compact">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="viewSystemLogs()">
                                <i class="fas fa-file-alt"></i> View Logs
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="clearSystemLogs()">
                                <i class="fas fa-trash"></i> Clear Logs
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="runDiagnostics()">
                                <i class="fas fa-stethoscope"></i> Run Diagnostics
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- API Settings -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <i class="fas fa-plug"></i>
                    <h3>API Settings</h3>
                </div>
                <div class="settings-card-body">
                    <form id="api-settings">
                        <div class="form-group-compact">
                            <label class="checkbox-label">
                                <input type="checkbox" name="api_enabled" 
                                       <?= ($advanced_settings['api_enabled'] ?? true) ? 'checked' : '' ?>>
                                <span>Enable API</span>
                            </label>
                            <small>Enable REST API for external integrations</small>
                        </div>

                        <div class="form-group-compact">
                            <label>API Key</label>
                            <div class="input-group-compact">
                                <input type="password" class="form-control-compact" name="api_key" 
                                       value="<?= $advanced_settings['api_key'] ?? '' ?>" readonly>
                                <button class="btn btn-sm btn-outline-secondary" type="button" onclick="generateApiKey()">
                                    <i class="fas fa-sync"></i> Generate
                                </button>
                            </div>
                            <small>API key for authenticating external requests</small>
                        </div>

                        <div class="form-row-compact">
                            <div class="form-group-compact">
                                <label>API Rate Limit (req/hour)</label>
                                <input type="number" class="form-control-compact" name="api_rate_limit" 
                                       value="<?= $advanced_settings['api_rate_limit'] ?? 1000 ?>" min="1" max="10000">
                            </div>
                            <div class="form-group-compact">
                                <label>API Timeout (seconds)</label>
                                <input type="number" class="form-control-compact" name="api_timeout" 
                                       value="<?= $advanced_settings['api_timeout'] ?? 30 ?>" min="1" max="300">
                            </div>
                        </div>

                        <div class="form-group-compact">
                            <label>CORS Allowed Origins</label>
                            <textarea class="form-control-compact" name="cors_origins" rows="3"
                                      placeholder="https://example.com&#10;https://app.example.com"><?= htmlspecialchars($advanced_settings['cors_origins'] ?? '') ?></textarea>
                            <small>Enter one origin per line. Use * for all origins.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function saveAdvancedSettings() {
    const formData = new FormData();
    
    // Collect all form data
    document.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.type === 'checkbox') {
            formData.append(field.name, field.checked ? '1' : '0');
        } else if (field.name) {
            formData.append(field.name, field.value);
        }
    });
    
    formData.append('csrf_token', '<?= csrf_token() ?>');
    
    fetch('<?= app_base_url('/admin/settings/advanced/save') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Advanced settings saved successfully', 'success');
        } else {
            showNotification('Failed to save settings: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving settings', 'error');
        console.error(error);
    });
}

function resetToDefaults() {
    showConfirmModal('Reset Settings', 'Are you sure you want to reset all advanced settings to their default values? This action cannot be undone.', () => {
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
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to reset settings', 'error');
            }
        });
    });
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
            document.querySelector('input[name="api_key"]').value = data.api_key;
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
    showConfirmModal('Clear Logs', 'Are you sure you want to clear all system logs? This action cannot be undone.', () => {
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
    });
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
            showNotification('Diagnostics failed: ' + (data.message || 'Unknown error'), 'error');
        }
    });
}
</script>

<style>
/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* Settings Card */
.settings-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.settings-card-header i {
    font-size: 1.25rem;
}

.settings-card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.settings-card-body {
    padding: 1.5rem;
}

/* Form Styles */
.form-group-compact {
    margin-bottom: 1.25rem;
}

.form-group-compact label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #2d3748;
    font-size: 0.875rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-label span {
    font-weight: 500;
}

.form-group-compact small {
    display: block;
    margin-top: 0.25rem;
    color: #718096;
    font-size: 0.75rem;
}

.form-row-compact {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-control-compact {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-control-compact:focus {
    outline: none;
    border-color: #667eea;
}

.input-group-compact {
    display: flex;
    gap: 0.5rem;
}

.input-group-compact .form-control-compact {
    flex: 1;
}

.debug-actions-compact {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row-compact {
        grid-template-columns: 1fr;
    }
    
    .debug-actions-compact {
        flex-direction: column;
    }
}
</style>
