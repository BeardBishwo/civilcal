<?php
$page_title = 'Global Widget Settings';
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-sliders-h"></i>
                    <h1>Global Widget Settings</h1>
                </div>
                <div class="header-subtitle">Configure system-wide settings for all widgets</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Widgets</span>
                </a>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-cogs text-primary"></i> System Configuration
                    </div>
                </div>
                
                <div class="card-content-compact">
                    <form method="post" action="<?php echo app_base_url('/admin/widgets/settings/save'); ?>">
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Widget Caching</label>
                            <select name="widget_cache_enabled" class="form-control">
                                <option value="1">Enabled (Recommended)</option>
                                <option value="0">Disabled</option>
                            </select>
                            <div class="form-help mt-2">
                                <i class="fas fa-info-circle text-info mr-1"></i>
                                Enable caching to improve dashboard performance.
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Cache Duration (Minutes)</label>
                            <input type="number" name="widget_cache_duration" class="form-control" value="60">
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Debug Mode</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="debugMode" name="widget_debug_mode">
                                <label class="form-check-label" for="debugMode">
                                    Enable Widget Debugging
                                </label>
                            </div>
                            <div class="form-help mt-2">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                Shows detailed error messages in widgets. Use only for development.
                            </div>
                        </div>

                        <hr class="border-light mb-4">
                        
                        <div class="form-actions d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary btn-compact" style="background: #667eea; color: white;">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* ========================================
       SHARED STYLES (Compact Admin Theme)
       ======================================== */
    
    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* HEADER */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left { flex: 1; }
    
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
    }

    .header-title i { font-size: 1.5rem; opacity: 0.9; }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
        color: rgba(255,255,255,0.9);
    }

    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-compact:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
    }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content-compact { padding: 1.5rem; }
    
    /* FORM STYLES */
    .form-group { margin-bottom: 1.5rem; }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
    }
    
    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        width: 1rem;
        height: 1rem;
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
    }
    
    .text-primary { color: #667eea !important; }
    .text-info { color: #4299e1 !important; }
    .text-warning { color: #ed8936 !important; }
    
    .border-light { border-color: #e5e7eb; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-2 { margin-top: 0.5rem; }
    .gap-2 { gap: 0.5rem; }
    .d-flex { display: flex; }
    .justify-content-end { justify-content: flex-end; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
    }
</style>
