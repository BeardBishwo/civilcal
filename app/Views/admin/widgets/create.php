<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Create Widget - Bishwo Calculator';
// Remove the require_once for header.php
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-plus-circle"></i>
                    <h1>Create Widget</h1>
                </div>
                <div class="header-subtitle">Create a new widget for your application</div>
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
                <!-- Creation Form -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-cube text-primary"></i> Widget Configuration
                        </div>
                    </div>
                    
                    <div class="card-content-compact">
                        <form method="post" action="<?php echo app_base_url('/admin/widgets/create'); ?>">
                            <div class="form-group mb-4">
                                <label class="form-label">Widget Class <span class="text-danger">*</span></label>
                                <select name="class_name" class="form-select form-control" required>
                                    <option value="">Select a widget class</option>
                                    <?php foreach ($available_classes ?? [] as $className): ?>
                                        <option value="<?php echo htmlspecialchars($className); ?>" 
                                                <?php echo (isset($_GET['class']) && $_GET['class'] === $className) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($className); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-help mt-1">Select the PHP class that implements the widget logic</div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Enter widget title" required>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter widget description to help identify it"></textarea>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">Position Request</label>
                                <div class="input-group">
                                    <input type="number" name="position" class="form-control" value="0" min="0">
                                </div>
                                <div class="form-help mt-1">Lower numbers appear first</div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label mb-2">Visibility & Status</label>
                                <div class="toggle-group p-3 bg-light rounded border border-light">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="is_enabled" id="is_enabled" class="form-check-input" checked>
                                        <label for="is_enabled" class="form-check-label font-medium">Enabled</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="is_visible" id="is_visible" class="form-check-input" checked>
                                        <label for="is_visible" class="form-check-label font-medium">Visible in Dashboard</label>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="border-light mb-4">
                            
                            <div class="form-actions d-flex justify-content-end gap-2">
                                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-light btn-compact text-muted" style="border: 1px solid #d1d5db;">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-compact" style="background: #667eea; color: white;">
                                    <i class="fas fa-plus"></i> Create Widget
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Helper / Info Panel -->
                 <?php if (!empty($widget_class_info)): ?>
                <div class="page-card-compact h-auto">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-info-circle text-info"></i> Class Validation
                        </div>
                    </div>
                    <div class="card-content-compact bg-light-50">
                        <div class="info-list">
                            <?php foreach ($widget_class_info as $className => $info): ?>
                                <div class="class-info-item mb-4 last:mb-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-code text-muted mr-2"></i>
                                        <h6 class="m-0 font-medium"><?php echo htmlspecialchars($className); ?></h6>
                                    </div>
                                    
                                    <?php if (!empty($info['description'])): ?>
                                        <p class="text-sm text-muted bg-white p-2 rounded border border-light mb-2">
                                            <?php echo htmlspecialchars($info['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($info['methods'])): ?>
                                        <div class="methods-container">
                                            <span class="text-xs text-uppercase text-muted font-bold tracking-wide">Available Methods</span>
                                            <div class="method-tags mt-1">
                                                <?php foreach ($info['methods'] as $method): ?>
                                                    <span class="badge-tag"><?php echo htmlspecialchars($method['name']); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Create widget interface loaded');
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
        display: flex;
        flex-direction: column;
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
        background-color: #fff;
    }
    
    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        appearance: none;
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-check-input {
        width: 1em;
        height: 1em;
        margin-top: 0.15em;
        cursor: pointer;
    }
    
    .text-danger { color: #f56565 !important; }
    .text-muted { color: #9ca3af !important; }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .text-uppercase { text-transform: uppercase; }
    
    .font-medium { font-weight: 500; }
    .font-bold { font-weight: 700; }
    
    .bg-light { background: #f9fafb !important; }
    .bg-light-50 { background: #fdfdfd; }
    .bg-white { background: #ffffff; }
    
    .border-light { border-color: #e5e7eb !important; }
    .rounded { border-radius: 0.375rem; }
    
    /* UTILS */
    .grid-2-cols {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }
    
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .p-2 { padding: 0.5rem; }
    .p-3 { padding: 0.75rem; }
    .mr-2 { margin-right: 0.5rem; }
    .m-0 { margin: 0; }
    
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .justify-content-end { justify-content: flex-end; }
    .gap-2 { gap: 0.5rem; }
    
    /* TAGS */
    .badge-tag {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 500;
        color: #4b5563;
        background-color: #f3f4f6;
        border-radius: 4px;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        font-family: monospace;
    }

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