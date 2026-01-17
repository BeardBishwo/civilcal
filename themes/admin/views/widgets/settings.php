<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Widget Settings - ' . htmlspecialchars($widget->getTitle());
// Remove the require_once for header.php
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-sliders-h"></i>
                    <h1>Widget Settings</h1>
                </div>
                <div class="header-subtitle">Configure advanced settings for <strong><?php echo htmlspecialchars($widget->getTitle()); ?></strong></div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Widgets</span>
                </a>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <div class="grid-2-cols">
                <!-- Widget Info Card -->
                <div class="page-card-compact h-auto">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-info-circle text-info"></i> Widget Information
                        </div>
                    </div>
                    <div class="card-content-compact">
                        <div class="info-group mb-3">
                            <label class="info-label">Title</label>
                            <div class="info-value font-medium"><?php echo htmlspecialchars($widget->getTitle()); ?></div>
                        </div>
                        <div class="info-group mb-3">
                            <label class="info-label">Widget Type</label>
                            <div><span class="badge-pill bg-light text-primary"><?php echo htmlspecialchars($widget->getType()); ?></span></div>
                        </div>
                        <div class="info-group">
                            <label class="info-label">Widget ID</label>
                            <div class="info-value font-mono bg-light rounded px-2 py-1 d-inline-block small text-muted">
                                <?php echo htmlspecialchars($widget->getId()); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Settings Form -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-cog text-primary"></i> Custom Configuration
                        </div>
                    </div>
                    
                    <div class="card-content-compact">
                        <form method="post" action="<?php echo app_base_url('/admin/widgets/settings/' . $widget->getId()); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <div class="form-group mb-4">
                                <label class="form-label">JSON Configuration</label>
                                <div class="editor-container">
                                    <textarea name="settings[config]" class="form-control code-editor" rows="12" placeholder="Enter JSON configuration data"><?php 
                                        $config = $widget->getConfig();
                                        echo htmlspecialchars(json_encode($config, JSON_PRETTY_PRINT));
                                    ?></textarea>
                                </div>
                                <div class="form-help mt-2">
                                    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                                    Advanced configuration in JSON format. Ensure valid syntax.
                                </div>
                            </div>
                            
                            <hr class="border-light mb-4">
                            
                            <div class="form-actions d-flex justify-content-end gap-2">
                                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-light btn-compact text-muted" style="border: 1px solid #d1d5db;">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-compact" style="background: #667eea; color: white;">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Widget settings interface loaded');
});
</script>

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
    
    .header-subtitle strong { color: white; font-weight: 600; }

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
    
    .btn-light:hover { background: #f3f4f6; color: #374151; }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .h-auto { height: auto; align-self: start; }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    .form-group { margin-bottom: 1rem; }
    
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
    
    .code-editor {
        font-family: 'Monaco', 'Menlo', 'Consolas', monospace;
        font-size: 0.85rem;
        line-height: 1.5;
        color: #1f2937;
        background: #f9fafb;
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
    }
    
    /* INFO GROUP */
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9ca3af;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 0.95rem;
        color: #1f2937;
    }
    
    .font-medium { font-weight: 500; }
    .font-mono { font-family: monospace; }
    .bg-light { background: #f3f4f6; }
    .text-primary { color: #667eea !important; }
    .text-info { color: #4299e1 !important; }
    .text-warning { color: #ed8936 !important; }
    .text-muted { color: #6b7280 !important; }
    
    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 10rem;
    }
    
    /* UTILS */
    .grid-2-cols {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
    }
    
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mr-1 { margin-right: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .d-inline-block { display: inline-block; }
    .d-flex { display: flex; }
    .justify-content-end { justify-content: flex-end; }
    .gap-2 { gap: 0.5rem; }
    .border-light { border-color: #e5e7eb; }
    .rounded { border-radius: 0.375rem; }
    .small { font-size: 0.8rem; }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .grid-2-cols { grid-template-columns: 1fr; }
    }
    
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        .analytics-content-body { padding: 1.25rem; }
    }
</style>