<?php
/**
 * OPTIMIZED MODULE SETTINGS INTERFACE
 */

$module = $data['module'];
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper bg-transparent shadow-none" style=" overflow: visible;">

        <!-- Compact Page Header -->
        <div class="compact-header rounded-3 mb-4 shadow-sm">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-cog"></i>
                    <h1><?php echo htmlspecialchars($module['display_name'] ?? ucwords(str_replace(['-', '_'], ' ', $module['name']))); ?></h1>
                </div>
                <div class="header-subtitle">Configure module settings and preferences</div>
            </div>
            <div class="header-right">
                <a href="<?php echo get_app_url(); ?>/admin/modules" class="btn-back-premium">
                    <i class="fas fa-arrow-left"></i> Back to Modules
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Settings Column -->
            <div class="col-12 col-lg-8">
                <div class="premium-card">
                    <div class="premium-card-header">
                        <h5 class="mb-0"><i class="fas fa-sliders-h"></i>General Configuration</h5>
                    </div>
                    <div class="premium-card-body">
                         <form id="moduleSettingsForm">
                            <input type="hidden" name="module" value="<?php echo htmlspecialchars($module['name']); ?>">

                            <div class="mb-4">
                                <label class="form-label-premium">Display Name</label>
                                <div class="input-group-premium">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" name="settings[display_name]" value="<?php echo htmlspecialchars($module['display_name'] ?? ucwords(str_replace(['-', '_'], ' ', $module['name']))); ?>" placeholder="Enter display name">
                                </div>
                                <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>The name displayed to users in the frontend menu.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-premium">Description</label>
                                <div class="input-group-premium textarea-group">
                                    <textarea name="settings[description]" rows="4" placeholder="Module description..."><?php echo htmlspecialchars($module['description'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-premium">Status</label>
                                <div class="status-toggle-container">
                                    <label class="status-option">
                                        <input type="radio" name="settings[status]" value="active" <?php echo ($module['status'] ?? '') === 'active' ? 'checked' : ''; ?>>
                                        <div class="status-box active-box">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Active</span>
                                        </div>
                                    </label>
                                    <label class="status-option">
                                        <input type="radio" name="settings[status]" value="inactive" <?php echo ($module['status'] ?? '') !== 'active' ? 'checked' : ''; ?>>
                                        <div class="status-box inactive-box">
                                            <i class="fas fa-power-off"></i>
                                            <span>Inactive</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="premium-alert warning mb-4">
                                <div class="icon"><i class="fas fa-flask"></i></div>
                                <div class="content">
                                    <strong>Advanced Config:</strong> Detailed configuration options for this module logic are handled via code files. These settings control display and availability only.
                                </div>
                            </div>

                            <div class="form-actions border-top pt-4 mt-2">
                                <button type="submit" class="btn-save-premium">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Side Panel Column -->
             <div class="col-12 col-lg-4">
                <div class="premium-card mb-4">
                    <div class="premium-card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i>Module Info</h5>
                    </div>
                    <div class="premium-card-body p-0">
                        <ul class="info-list">
                            <li>
                                <div class="label"><i class="fas fa-code-branch text-muted me-2"></i>Version</div>
                                <div class="value badge bg-light text-dark"><?php echo htmlspecialchars($module['version'] ?? '1.0.0'); ?></div>
                            </li>
                            <li>
                                <div class="label"><i class="fas fa-calendar-alt text-muted me-2"></i>Added</div>
                                <div class="value"><?php echo htmlspecialchars($module['created_at'] ?? date('Y-m-d')); ?></div>
                            </li>
                             <li>
                                <div class="label"><i class="fas fa-calculator text-muted me-2"></i>Tools</div>
                                <div class="value badge bg-primary"><?php echo $module['calculators_count'] ?? 0; ?> calculators</div>
                            </li>
                             <li>
                                <div class="label"><i class="fas fa-layer-group text-muted me-2"></i>Sub-categories</div>
                                <div class="value badge bg-info"><?php echo $module['subcategories_count'] ?? 0; ?></div>
                            </li>
                             <li>
                                <div class="label"><i class="fas fa-folder text-muted me-2"></i>Category</div>
                                <div class="value"><?php echo htmlspecialchars($module['category'] ?? 'General'); ?></div>
                            </li>
                        </ul>
                    </div>
                </div>

                 <div class="premium-card">
                    <div class="premium-card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Permissions</h5>
                    </div>
                     <div class="premium-card-body">
                         <p class="text-muted small mb-0">
                             This module is accessible to: <strong class="text-dark">Administrators, Editors</strong>
                         </p>
                     </div>
                </div>
            </div>
        </div>

    </div>
</div>

    document.getElementById('moduleSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';

        const formData = new FormData(this);

        fetch('<?php echo get_app_url(); ?>/admin/modules/settings/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Settings saved successfully!', 'success');
                } else {
                    showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving settings.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>

<style>
    /* SHARED PREMIUM STYLES */
    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex-wrap: wrap; /* Prevent overlap */
        gap: 1rem;
    }
    
    .rounded-3 { border-radius: 0.75rem !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
    
    .header-title {
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;
    }
    .header-title h1 {
        margin: 0; font-size: 1.75rem; font-weight: 700; color: white;
        white-space: nowrap; /* Keep title on one line if possible */
    }
    .header-title i { font-size: 1.5rem; opacity: 0.9; }
    .header-subtitle { font-size: 0.875rem; opacity: 0.8; margin: 0; }
    
    .btn-back-premium {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }
    .btn-back-premium:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateX(-2px);
        color: white;
    }

    /* CARD STYLES */
    .premium-card {
        background: white; border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
        overflow: hidden;
        height: 100%; /* Ensure equal height columns */
    }
    .premium-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
        background: #f8fafc;
        color: #2d3748;
        font-weight: 600;
    }
    .premium-card-body { padding: 1.5rem; }

    /* FORM STYLES */
    .form-label-premium {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.75rem; /* Increased margin */
        font-size: 0.9rem;
    }
    
    .input-group-premium {
        position: relative;
        display: flex; align-items: center;
    }
    .input-group-premium i {
        position: absolute; left: 1rem; color: #a0aec0; z-index: 2;
        pointer-events: none; /* Prevent blocking clicks */
    }
    .input-group-premium input, .input-group-premium textarea {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.75rem; /* Increased padding */
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem; /* Larger font */
        color: #2d3748;
        transition: all 0.2s;
        background: #fff;
    }
    .input-group-premium.textarea-group i { top: 1.25rem; }
    .input-group-premium input:focus, .input-group-premium textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); /* Larger shadow */
        outline: none;
    }
    
    /* Status Toggle */
    .status-toggle-container {
        display: flex; gap: 1.5rem; /* Increased gap */
        flex-wrap: wrap; 
    }
    .status-option { cursor: pointer; position: relative; margin: 0; }
    .status-option input { position: absolute; opacity: 0; width: 0; height: 0; }
    .status-box {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem 1.5rem; /* Larger touch target */
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        color: #718096;
        transition: all 0.2s;
        font-weight: 500;
    }
    .status-option input:checked + .status-box {
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .status-box.active-box { }
    .status-option input:checked + .status-box.active-box { background: #48bb78; }
    .status-option input:checked + .status-box.inactive-box { background: #e53e3e; }
    
    /* Alert */
    .premium-alert {
        display: flex; gap: 1rem;
        padding: 1.25rem; border-radius: 8px;
        font-size: 0.95rem; margin-bottom: 2rem;
        line-height: 1.6;
    }
    .premium-alert.warning { background: #fffaf0; border: 1px solid #fbd38d; color: #744210; }
    .premium-alert .icon { font-size: 1.25rem; flex-shrink: 0; }
    
    /* Save Button */
    .btn-save-premium {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.875rem 2.5rem;
        background: #667eea; text-decoration: none;
        color: white; border: none; border-radius: 8px;
        font-weight: 600; cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
    }
    .btn-save-premium:hover { background: #5a67d8; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
    .btn-save-premium:disabled { opacity: 0.7; cursor: not-allowed; }

    /* Info List */
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.875rem 0; /* Increased padding */
        border-bottom: 1px solid #edf2f7;
    }
    .info-list li:last-child { border-bottom: none; }
    .info-list .label { font-size: 0.9rem; color: #4a5568; display: flex; align-items: center; }
    .info-list .value { font-weight: 500; font-size: 0.95rem; color: #2d3748; }

    /* Explicit Spacing for Icons */
    .premium-card-header h5 i,
    .btn-save-premium i,
    .info-list .label i {
        margin-right: 0.75rem;
    }
    
    .header-title i {
        margin-right: 1rem;
    }
    
    .btn-back-premium i,
    .form-text i {
        margin-right: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .compact-header { flex-direction: column; align-items: stretch; text-align: center; }
        .header-title { justify-content: center; }
        .header-right { margin-top: 1rem; display: flex; justify-content: center; }
        .premium-card { height: auto; }
    }
    @media (max-width: 576px) {
        .status-toggle-container { flex-direction: column; gap: 0.75rem; }
        .status-box { width: 100%; justify-content: center; }
    }
</style>