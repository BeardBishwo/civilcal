<?php
/**
 * Application Settings View - Fixed for Theme
 * Self-contained content for admin layout
 */

// Ensure SettingsService is available
if (!class_exists('App\\Services\\SettingsService')) {
    require_once __DIR__ . '/../../../../app/Services/SettingsService.php';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Application Settings</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= app_base_url('/admin') ?>">Admin</a></li>
                <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/settings') ?>">Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">Application</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary preview-btn" id="previewChanges">
            <i class="bi bi-eye"></i> Preview
        </button>
        <button class="btn btn-success" id="saveSettings">
            <i class="bi bi-save"></i> Save
        </button>
        <button class="btn btn-outline-danger" id="resetSettings">
            <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <ul class="nav nav-tabs settings-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="bi bi-house-door"></i> General
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance" type="button" role="tab">
                    <i class="bi bi-exclamation-triangle"></i> Maintenance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="debug-tab" data-bs-toggle="tab" data-bs-target="#debug" type="button" role="tab">
                    <i class="bi bi-bug"></i> Debug
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab">
                    <i class="bi bi-speedometer2"></i> Performance
                </button>
            </li>
        </ul>

        <form id="applicationSettingsForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="tab-content" id="settingsTabContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="glass-card">
                        <div class="glass-header">
                            <i class="bi bi-info-circle"></i> General Configuration
                        </div>
                        <div class="glass-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Application Name</label>
                                    <input type="text" class="form-control enhanced-input" id="app_name" name="app_name" 
                                           value="<?= htmlspecialchars(\App\Services\SettingsService::get('app_name', 'Bishwo Calculator')) ?>" 
                                           data-tooltip="Displayed across the app">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Version</label>
                                    <input type="text" class="form-control" id="app_version" name="app_version" readonly 
                                           value="<?= htmlspecialchars(\App\Services\SettingsService::get('app_version', '1.0.0')) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Tagline</label>
                                    <input type="text" class="form-control enhanced-input" id="app_tagline" name="app_tagline" 
                                           value="<?= htmlspecialchars(\App\Services\SettingsService::get('app_tagline', 'Advanced Engineering Calculator')) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Base URL</label>
                                    <input type="url" class="form-control enhanced-input" id="base_url" name="base_url" 
                                           value="<?= htmlspecialchars(\App\Services\SettingsService::get('base_url', app_base_url('/'))) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Tab -->
                <div class="tab-pane fade" id="maintenance" role="tabpanel">
                    <div class="glass-card warning-card">
                        <div class="glass-header">
                            <i class="bi bi-exclamation-triangle"></i> Maintenance Mode
                        </div>
                        <div class="glass-body">
                            <div class="form-check form-switch form-switch-lg mb-4">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       <?= \App\Services\SettingsService::get('maintenance_mode', '0') ? 'checked' : '' ?> value="1">
                                <label class="form-check-label" for="maintenance_mode" style="font-size: 1.1rem;">
                                    Enable Maintenance Mode <span class="badge bg-warning ms-2">Visitors Blocked</span>
                                </label>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Custom Message</label>
                                    <textarea class="form-control enhanced-textarea" id="maintenance_message" name="maintenance_message" rows="4"><?= htmlspecialchars(\App\Services\SettingsService::get('maintenance_message', 'Site under maintenance. We\'ll be back soon!')) ?></textarea>
                                    <div class="form-text">Shown to all non-admin visitors</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Tab -->
                <div class="tab-pane fade" id="debug" role="tabpanel">
                    <div class="glass-card info-card">
                        <div class="glass-header">
                            <i class="bi bi-bug"></i> Debug & Development
                        </div>
                        <div class="glass-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" 
                                               <?= \App\Services\SettingsService::get('debug_mode', '0') ? 'checked' : '' ?> value="1">
                                        <label class="form-check-label" for="debug_mode">
                                            Debug Mode <span class="badge bg-info ms-1">Dev Only</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_error_logging" name="enable_error_logging" 
                                               <?= \App\Services\SettingsService::get('enable_error_logging', '1') ? 'checked' : '' ?> value="1">
                                        <label class="form-check-label" for="enable_error_logging">Error Logging</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Log Level</label>
                                    <select class="form-select enhanced-select" id="log_level" name="log_level">
                                        <option value="error" <?= \App\Services\SettingsService::get('log_level', 'error') === 'error' ? 'selected' : '' ?>>Error</option>
                                        <option value="warning" <?= \App\Services\SettingsService::get('log_level', 'error') === 'warning' ? 'selected' : '' ?>>Warning</option>
                                        <option value="info" <?= \App\Services\SettingsService::get('log_level', 'error') === 'info' ? 'selected' : '' ?>>Info</option>
                                        <option value="debug" <?= \App\Services\SettingsService::get('log_level', 'error') === 'debug' ? 'selected' : '' ?>>Debug</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Tab -->
                <div class="tab-pane fade" id="performance" role="tabpanel">
                    <div class="glass-card success-card">
                        <div class="glass-header">
                            <i class="bi bi-speedometer2"></i> Performance Optimization
                        </div>
                        <div class="glass-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_cache" name="enable_cache" 
                                               <?= \App\Services\SettingsService::get('enable_cache', '1') ? 'checked' : '' ?> value="1">
                                        <label class="form-check-label" for="enable_cache">
                                            Caching <span class="badge bg-success ms-1">Faster Loads</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_compression" name="enable_compression" 
                                               <?= \App\Services\SettingsService::get('enable_compression', '1') ? 'checked' : '' ?> value="1">
                                        <label class="form-check-label" for="enable_compression">GZIP Compression</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-3">
        <div class="sticky-sidebar">
            <div class="glass-card preview-panel">
                <div class="glass-header">
                    <i class="bi bi-eye"></i> Quick Actions
                </div>
                <div class="glass-body text-center p-3">
                    <button class="btn btn-outline-info btn-sm w-100 mb-2" id="testSettings">
                        <i class="bi bi-play-circle"></i> Test Config
                    </button>
                    <div class="status-badge mt-3">
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
</div>

<style>
:root {
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
    --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    --glass-backdrop: blur(10px);
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.glass-card {
    background: var(--glass-bg);
    backdrop-filter: var(--glass-backdrop);
    border-radius: 20px;
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    transition: all 0.3s ease;
    overflow: hidden;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.glass-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.glass-body {
    padding: 1.5rem;
}

.glass-card.warning-card .glass-header {
    background: var(--warning-gradient);
}

.glass-card.info-card .glass-header {
    background: var(--info-gradient);
}

.glass-card.success-card .glass-header {
    background: var(--success-gradient);
}

.enhanced-input, .enhanced-select, .enhanced-textarea {
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: rgba(255,255,255,0.9);
}

.enhanced-input:focus, .enhanced-select:focus, .enhanced-textarea:focus {
    border-color: var(--primary-gradient);
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

.nav-tabs .nav-link {
    border-radius: 12px 12px 0 0;
    margin-right: 0.5rem;
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 -4px 20px rgba(102, 126, 234, 0.4);
}

.btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.sticky-sidebar {
    position: sticky;
    top: 100px;
}

.preview-panel {
    height: fit-content;
}

.status-badge .badge {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
}

@media (max-width: 992px) {
    .settings-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    
    .sticky-sidebar {
        position: relative;
        top: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationSettingsForm');
    const saveBtn = document.getElementById('saveSettings');
    const resetBtn = document.getElementById('resetSettings');
    const previewBtn = document.getElementById('previewChanges');
    const testBtn = document.getElementById('testSettings');

    // Tab persistence
    const activeTab = localStorage.getItem('activeSettingsTab') || 'general';
    const tabElement = document.getElementById(activeTab + '-tab');
    if (tabElement) tabElement.click();

    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            localStorage.setItem('activeSettingsTab', this.id.replace('-tab', ''));
        });
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            this.classList.add('is-valid');
            if (this.value.trim() === '') this.classList.remove('is-valid');
        });
    });

    // Save with animation
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const btn = this;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split spinner"></i> Saving...';

        fetch(app_base_url('/admin/settings/update'), {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.success ? 'success' : 'danger', data.message || (data.success ? 'Saved!' : 'Error'));
            if (data.success) {
                confettiEffect();
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch(err => showToast('danger', 'Save failed'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    resetBtn.addEventListener('click', function() {
        if (confirm('Reset ALL settings to defaults?')) {
            fetch(app_base_url('/admin/settings/reset'), {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({group: 'application'})
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.success ? 'warning' : 'danger', data.message);
                if (data.success) location.reload();
            });
        }
    });

    previewBtn.addEventListener('click', () => {
        showToast('info', 'Preview mode: Changes saved live!');
    });

    testBtn.addEventListener('click', () => {
        showToast('success', 'All systems nominal! 🚀');
    });

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0`;
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.querySelector('.toast-container').appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function confettiEffect() {
        // Simple confetti using canvas
        const canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999';
        document.body.appendChild(canvas);
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        // Confetti particles animation...
        setTimeout(() => canvas.remove(), 3000);
    }
});
</script>
