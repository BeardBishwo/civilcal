<?php
/**
 * ULTRA-PREMIUM ADVANCED SETTINGS INTERFACE
 * Designed for Bishwo Calculator - Maximum Performance & Security
 */

$page_title = $page_title ?? 'Advanced Configuration';
$advanced_settings = $advanced_settings ?? [];
$system_info = $system_info ?? [];

// Helper to get setting value with default
function getSet($key, $default = '') {
    global $advanced_settings;
    return $advanced_settings[$key] ?? $default;
}

// Helper for checkboxes
function isChecked($key, $default = false) {
    $val = getSet($key, $default ? '1' : '0');
    return $val === '1' || $val === 'true' || $val === true;
}
?>

<div class="advanced-settings-wrapper">
    <!-- Header Section -->
    <div class="premium-header">
        <div class="header-content">
            <div class="title-area">
                <div class="icon-box">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="text-box">
                    <h1><?= htmlspecialchars($page_title) ?></h1>
                    <p>Core system engine, security hardening, and performance fine-tuning.</p>
                </div>
            </div>
            <div class="action-area">
                <button class="btn-premium btn-secondary-glass" onclick="resetToDefaults()">
                    <i class="fas fa-history"></i> Reset
                </button>
                <button class="btn-premium btn-primary-gradient" onclick="saveAdvancedSettings()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <div class="advanced-grid">
        <!-- Sidebar Navigation -->
        <aside class="settings-sidebar-nav">
            <div class="nav-sticky">
                <a href="#performance" class="nav-item active" data-section="performance">
                    <i class="fas fa-bolt"></i> Performance
                </a>
                <a href="#security" class="nav-item" data-section="security">
                    <i class="fas fa-shield-halved"></i> Security
                </a>
                <a href="#debug" class="nav-item" data-section="debug">
                    <i class="fas fa-bug-slash"></i> Debugging
                </a>
                <a href="#api" class="nav-item" data-section="api">
                    <i class="fas fa-brackets-curly"></i> API Engine
                </a>
                <a href="#custom-code" class="nav-item" data-section="custom-code">
                    <i class="fas fa-code"></i> Injection
                </a>
                <a href="#system-info" class="nav-item" data-section="system-info">
                    <i class="fas fa-info-circle"></i> System Stats
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="settings-main-content">
            <form id="advancedSettingsForm">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <!-- Performance Section -->
                <section id="performance" class="settings-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h2>Performance Optimization</h2>
                            <span class="status-indicator <?= isChecked('cache_enabled', true) ? 'active' : 'inactive' ?>">
                                <?= isChecked('cache_enabled', true) ? 'Engine Optimized' : 'Standard Speed' ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>System-wide Caching</label>
                                    <p>Accelerate asset delivery and database query results.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle">
                                        <input type="checkbox" name="cache_enabled" value="1" <?= isChecked('cache_enabled', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-columns">
                                <div class="form-group">
                                    <label>Cache TTL (Seconds)</label>
                                    <input type="number" name="cache_ttl" class="premium-input" value="<?= getSet('cache_ttl', 3600) ?>">
                                    <small>How long data stays in memory before refreshing.</small>
                                </div>
                                <div class="form-group">
                                    <label>Storage Driver</label>
                                    <select name="cache_driver" class="premium-select">
                                        <option value="file" <?= getSet('cache_driver') === 'file' ? 'selected' : '' ?>>Filesystem (Local)</option>
                                        <option value="redis" <?= getSet('cache_driver') === 'redis' ? 'selected' : '' ?>>Redis (Ultra Fast)</option>
                                        <option value="memcached" <?= getSet('cache_driver') === 'memcached' ? 'selected' : '' ?>>Memcached</option>
                                    </select>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>GZIP Compression</label>
                                    <p>Compress HTML/JSON output to reduce bandwidth usage.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle">
                                        <input type="checkbox" name="compression_enabled" value="1" <?= isChecked('compression_enabled', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-columns">
                                <div class="form-group">
                                    <label>Session Lifetime (Min)</label>
                                    <input type="number" name="session_lifetime" class="premium-input" value="<?= getSet('session_lifetime', 120) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Concurrent Users Cap</label>
                                    <input type="number" name="max_concurrent_users" class="premium-input" value="<?= getSet('max_concurrent_users', 5000) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Security Section -->
                <section id="security" class="settings-section">
                    <div class="section-card security-card">
                        <div class="card-header">
                            <h2>Security Hardening</h2>
                            <i class="fas fa-lock-alt text-primary"></i>
                        </div>
                        <div class="card-body">
                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>Force SSL/HTTPS</label>
                                    <p>Automatically redirect all insecure requests to secure layer.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle toggle-success">
                                        <input type="checkbox" name="force_https" value="1" <?= isChecked('force_https', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>Strict Security Headers</label>
                                    <p>Enforce HSTS, X-Frame-Options, and CSP policies.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle toggle-success">
                                        <input type="checkbox" name="security_headers" value="1" <?= isChecked('security_headers', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>Brute-Force Protection</label>
                                    <p>Limit failed login attempts and monitor suspicious requests.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle toggle-success">
                                        <input type="checkbox" name="rate_limiting" value="1" <?= isChecked('rate_limiting', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-columns">
                                <div class="form-group">
                                    <label>Max Login Drops</label>
                                    <input type="number" name="login_attempts" class="premium-input" value="<?= getSet('login_attempts', 5) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Request Throttle (req/m)</label>
                                    <input type="number" name="rate_limit_requests" class="premium-input" value="<?= getSet('rate_limit_requests', 60) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Debugging Section -->
                <section id="debug" class="settings-section">
                    <div class="section-card debug-card">
                        <div class="card-header">
                            <h2>Debugging & Diagnostics</h2>
                            <div class="debug-status <?= isChecked('debug_mode') ? 'warning' : 'safe' ?>">
                                <?= isChecked('debug_mode') ? 'DEVELOPER MODE ACTIVE' : 'PROD SAFE' ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>Debug Mode</label>
                                    <p>Display full stack traces. <strong class="text-danger">Disable in production!</strong></p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle toggle-danger">
                                        <input type="checkbox" name="debug_mode" value="1" <?= isChecked('debug_mode') ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>Database Query Logging</label>
                                    <p>Track all SQL operations for optimization profiling.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle">
                                        <input type="checkbox" name="query_debug" value="1" <?= isChecked('query_debug') ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>System Log Level</label>
                                <select name="log_level" class="premium-select">
                                    <option value="error" <?= getSet('log_level') === 'error' ? 'selected' : '' ?>>Critical & Errors</option>
                                    <option value="warning" <?= getSet('log_level') === 'warning' ? 'selected' : '' ?>>Warnings Only</option>
                                    <option value="info" <?= getSet('log_level') === 'info' ? 'selected' : '' ?>>Standard Info</option>
                                    <option value="debug" <?= getSet('log_level') === 'debug' ? 'selected' : '' ?>>Deep Debug Session</option>
                                </select>
                            </div>

                            <div class="debug-quick-actions">
                                <button type="button" class="btn-action" onclick="window.open('<?= app_base_url('/admin/debug/error-logs') ?>')">
                                    <i class="fas fa-terminal"></i> Error Logs
                                </button>
                                <button type="button" class="btn-action" onclick="runTests()">
                                    <i class="fas fa-vial"></i> Run System Tests
                                </button>
                                <button type="button" class="btn-action btn-danger-text" onclick="clearLogs()">
                                    <i class="fas fa-trash-alt"></i> Purge Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- API Section -->
                <section id="api" class="settings-section">
                    <div class="section-card api-card">
                        <div class="card-header">
                            <h2>API Gateway</h2>
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="card-body">
                            <div class="setting-row">
                                <div class="setting-info">
                                    <label>External API Access</label>
                                    <p>Allow third-party applications to interface with the calculator engine.</p>
                                </div>
                                <div class="setting-control">
                                    <label class="premium-toggle">
                                        <input type="checkbox" name="api_enabled" value="1" <?= isChecked('api_enabled', true) ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Global API Master Key</label>
                                <div class="premium-input-group">
                                    <input type="text" id="api_key_field" name="api_key" class="premium-input mono" value="<?= getSet('api_key') ?>" readonly>
                                    <button type="button" class="btn-input-action" onclick="regenerateKey()">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                                <small>Used for server-to-server communication authentication.</small>
                            </div>

                            <div class="form-group mt-3">
                                <label>CORS Allowed Origins</label>
                                <textarea name="cors_origins" class="premium-textarea mono" rows="3" placeholder="https://app.yoursite.com"><?= getSet('cors_origins') ?></textarea>
                                <small>Domains allowed to make cross-origin requests. Use '*' for open access (risky).</small>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Custom Code Injection Section -->
                <section id="custom-code" class="settings-section">
                    <div class="section-card code-card">
                        <div class="card-header">
                            <h2>Code Injection</h2>
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Header Scripts (Trackers, Meta, etc.)</label>
                                <div class="code-editor-container">
                                    <textarea name="custom_header_code" class="premium-textarea mono-code" rows="8"><?= getSet('custom_header_code') ?></textarea>
                                </div>
                                <small>Inserted globally into the &lt;head&gt; section.</small>
                            </div>

                            <div class="form-group mt-4">
                                <label>Footer Scripts (Live Chat, Pixel, etc.)</label>
                                <div class="code-editor-container">
                                    <textarea name="custom_footer_code" class="premium-textarea mono-code" rows="8"><?= getSet('custom_footer_code') ?></textarea>
                                </div>
                                <small>Inserted globally before the closing &lt;/body&gt; tag.</small>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- System Info Section -->
                <section id="system-info" class="settings-section">
                    <div class="section-card system-info-card">
                        <div class="card-header">
                            <h2>Environment Overview</h2>
                            <span class="badge-system">v<?= htmlspecialchars($system_info['app_version'] ?? '1.5.0') ?></span>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="label">Architecture</span>
                                    <span class="value"><?= htmlspecialchars($system_info['server_os'] ?? PHP_OS) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">PHP Engine</span>
                                    <span class="value"><?= htmlspecialchars($system_info['php_version'] ?? PHP_VERSION) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Memory Utilization</span>
                                    <span class="value"><?= htmlspecialchars($system_info['memory_limit'] ?? '128M') ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Web Server</span>
                                    <span class="value"><?= htmlspecialchars(explode('/', $system_info['server_software'] ?? 'Apache')[0]) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Database Driver</span>
                                    <span class="value">PDO MySQL</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Uptime Level</span>
                                    <span class="value text-success">Optimal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </main>
    </div>
</div>

<style>
/* PREMIUM DESIGN SYSTEM - ADVANCED CONFIGURATION */
:root {
    --p-indigo: #6366f1;
    --p-indigo-dark: #4f46e5;
    --p-indigo-soft: #eef2ff;
    --p-slate-50: #f8fafc;
    --p-slate-100: #f1f5f9;
    --p-slate-200: #e2e8f0;
    --p-slate-300: #cbd5e1;
    --p-slate-600: #475569;
    --p-slate-700: #334155;
    --p-slate-800: #1e293b;
    --p-slate-900: #0f172a;
    --p-emerald: #10b981;
    --p-rose: #f43f5e;
    --p-amber: #f59e0b;
    --p-card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --p-card-shadow-hover: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.advanced-settings-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Inter', -apple-system, system-ui, sans-serif;
}

/* Header */
.premium-header {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: var(--p-card-shadow);
    margin-bottom: 30px;
    border: 1px solid var(--p-slate-200);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.title-area {
    display: flex;
    align-items: center;
    gap: 20px;
}

.icon-box {
    width: 60px;
    height: 60px;
    background: var(--p-indigo-soft);
    color: var(--p-indigo);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    font-size: 28px;
}

.text-box h1 {
    font-size: 24px;
    font-weight: 800;
    color: var(--p-slate-900);
    margin: 0;
}

.text-box p {
    margin: 5px 0 0;
    color: var(--p-slate-600);
    font-size: 14px;
}

/* Buttons */
.btn-premium {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, var(--p-indigo) 0%, var(--p-indigo-dark) 100%);
    color: white;
}

.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
}

.btn-secondary-glass {
    background: var(--p-slate-100);
    color: var(--p-slate-700);
}

.btn-secondary-glass:hover {
    background: var(--p-slate-200);
}

/* Grid Layout */
.advanced-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    align-items: flex-start;
}

/* Sidebar Nav */
.settings-sidebar-nav .nav-sticky {
    position: sticky;
    top: 100px;
    background: white;
    padding: 10px;
    border-radius: 16px;
    box-shadow: var(--p-card-shadow);
    border: 1px solid var(--p-slate-200);
}

.settings-sidebar-nav .nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 10px;
    color: var(--p-slate-600);
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 4px;
    transition: all 0.2s;
}

.settings-sidebar-nav .nav-item i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.settings-sidebar-nav .nav-item:hover {
    background: var(--p-slate-50);
    color: var(--p-indigo);
}

.settings-sidebar-nav .nav-item.active {
    background: var(--p-indigo-soft);
    color: var(--p-indigo);
}

/* Sections & Cards */
.settings-section {
    margin-bottom: 30px;
}

.section-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--p-card-shadow);
    border: 1px solid var(--p-slate-200);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.section-card:hover {
    box-shadow: var(--p-card-shadow-hover);
}

.card-header {
    padding: 24px 30px;
    border-bottom: 1px solid var(--p-slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--p-slate-900);
    margin: 0;
}

.card-body {
    padding: 30px;
}

/* Indicators */
.status-indicator {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-indicator.active {
    background: #ecfdf5;
    color: var(--p-emerald);
}

.status-indicator.inactive {
    background: var(--p-slate-100);
    color: var(--p-slate-600);
}

.debug-status {
    font-size: 11px;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 6px;
}

.debug-status.safe {
    background: #ecfdf5;
    color: var(--p-emerald);
}

.debug-status.warning {
    background: #fffbeb;
    color: var(--p-amber);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Rows & Columns */
.setting-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px dashed var(--p-slate-200);
}

.setting-row:first-child { padding-top: 0; }
.setting-row:last-of-type { border-bottom: none; }

.setting-info label {
    display: block;
    font-weight: 600;
    color: var(--p-slate-800);
    font-size: 15px;
}

.setting-info p {
    margin: 2px 0 0;
    font-size: 13px;
    color: var(--p-slate-600);
}

.setting-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px 0;
}

/* Inputs */
.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--p-slate-700);
    margin-bottom: 8px;
}

.premium-input, .premium-select, .premium-textarea {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid var(--p-slate-200);
    background: var(--p-slate-50);
    font-size: 14px;
    transition: all 0.2s;
}

.premium-input:focus, .premium-select:focus, .premium-textarea:focus {
    outline: none;
    border-color: var(--p-indigo);
    background: white;
    box-shadow: 0 0 0 4px var(--p-indigo-soft);
}

.premium-textarea {
    resize: vertical;
}

.mono { font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 13px; }
.mono-code { font-family: 'JetBrains Mono', monospace; font-size: 13px; background: #1e293b; color: #e2e8f0; border: none; }

/* Toggle */
.premium-toggle {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.premium-toggle input { opacity: 0; width: 0; height: 0; }

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: var(--p-slate-300);
    transition: .4s;
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px; width: 20px;
    left: 4px; bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

input:checked + .toggle-slider { background-color: var(--p-indigo); }
.toggle-success input:checked + .toggle-slider { background-color: var(--p-emerald); }
.toggle-danger input:checked + .toggle-slider { background-color: var(--p-rose); }

input:checked + .toggle-slider:before { transform: translateX(24px); }

/* API Key Input Group */
.premium-input-group {
    display: flex;
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid var(--p-slate-200);
}

.premium-input-group .premium-input {
    border: none;
    border-radius: 0;
}

.btn-input-action {
    background: var(--p-slate-200);
    border: none;
    padding: 0 20px;
    color: var(--p-slate-700);
    cursor: pointer;
    transition: background 0.2s;
}

.btn-input-action:hover { background: var(--p-slate-300); }

/* Debug Quick Actions */
.debug-quick-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid var(--p-slate-100);
}

.btn-action {
    background: var(--p-slate-100);
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--p-slate-700);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-action:hover { background: var(--p-slate-200); color: var(--p-indigo); }
.btn-danger-text:hover { color: var(--p-rose); }

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: var(--p-slate-50);
    border-radius: 12px;
    border: 1px solid var(--p-slate-200);
}

.info-item .label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--p-slate-500);
    margin-bottom: 4px;
    font-weight: 700;
}

.info-item .value {
    font-weight: 600;
    color: var(--p-slate-800);
    font-size: 14px;
}

/* Responsive */
@media (max-width: 992px) {
    .advanced-grid { grid-template-columns: 1fr; }
    .settings-sidebar-nav { display: none; }
    .info-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 576px) {
    .setting-columns { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr; }
    .debug-quick-actions { flex-direction: column; }
    .premium-header { padding: 20px; }
    .title-area { flex-direction: column; text-align: center; }
}
</style>

<script>
/**
 * Advanced Settings Management Logic
 */

// Smooth scroll & Sidebar active state
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.settings-section');
    const navItems = document.querySelectorAll('.settings-sidebar-nav .nav-item');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-section') === current) {
                item.classList.add('active');
            }
        });
    });

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = item.getAttribute('href');
            document.querySelector(targetId).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
});

function saveAdvancedSettings() {
    const form = document.getElementById('advancedSettingsForm');
    const formData = new FormData(form);
    
    // Explicitly handle checkboxes that are unchecked (FormData ignores them)
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        if (!cb.checked) {
            formData.set(cb.name, '0');
        }
    });

    showNotification('Synchronizing engine parameters...', 'info');

    fetch('<?= app_base_url('/admin/settings/advanced/save') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('System updated successfully! 🚀', 'success');
            // Flash active indicator if cache was changed
            location.reload(); 
        } else {
            showNotification('Update failed: ' + (data.message || 'Verification error'), 'error');
        }
    })
    .catch(err => {
        showNotification('Communication error with server', 'error');
        console.error(err);
    });
}

function regenerateKey() {
    showConfirmModal('Regenerate Master API Key', 'Existing applications using this key will be disconnected immediately. Proceed?', () => {
        fetch('<?= app_base_url('/admin/settings/advanced/generate-api-key') ?>', {
            method: 'POST',
            body: new FormData(document.getElementById('advancedSettingsForm'))
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('api_key_field').value = data.api_key;
                showNotification('Global API key regenerated!', 'success');
            } else {
                showNotification('Key generation failed', 'error');
            }
        });
    });
}

function resetToDefaults() {
    showConfirmModal('Deep Reset System', 'All advanced configuration will revert to factory defaults. Custom code will be lost. Continue?', () => {
        const resetForm = new FormData();
        resetForm.append('setting_group', 'advanced');
        resetForm.append('csrf_token', '<?= csrf_token() ?>');

        fetch('<?= app_base_url('/admin/settings/reset') ?>', {
            method: 'POST',
            body: resetForm
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification('System defaults restored.', 'warning');
                setTimeout(() => location.reload(), 1500);
            }
        });
    });
}

function clearLogs() {
    showConfirmModal('Purge Logs', 'Are you sure you want to delete all historical log entries?', () => {
        fetch('<?= app_base_url('/admin/debug/clear-logs') ?>', {
            method: 'POST',
            body: new FormData(document.getElementById('advancedSettingsForm'))
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) showNotification('System logs purged.', 'success');
        });
    });
}

function runTests() {
    showNotification('Launching diagnostic suite...', 'info');
    fetch('<?= app_base_url('/admin/debug/run-tests') ?>', {
        method: 'POST',
        body: new FormData(document.getElementById('advancedSettingsForm'))
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Tests completed with 0 failures!', 'success');
            window.open('<?= app_base_url('/admin/debug/error-logs') ?>', '_blank');
        } else {
            showNotification('Diagnostics found potential issues.', 'warning');
        }
    });
}
</script>
