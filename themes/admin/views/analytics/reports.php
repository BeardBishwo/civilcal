<?php
$page_title = 'Analytics Reports - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-export"></i>
                    <h1>Analytics Reports</h1>
                </div>
                <div class="header-subtitle">Generate and download detailed analytics exports</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/analytics'); ?>" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Overview</span>
                </a>
            </div>
        </div>

        <!-- Reports Content -->
        <div class="analytics-content-body">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mb-4">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="pages-grid-compact">
                <?php if (isset($available_reports) && is_array($available_reports)): ?>
                    <?php foreach ($available_reports as $report): ?>
                        <div class="page-card-compact report-card">
                            <div class="card-content-compact">
                                <div class="stat-icon" style="background: #e2e8f0; color: #4a5568; margin-bottom: 1rem;">
                                    <i class="fas fa-file-csv"></i>
                                </div>
                                <h3 class="card-title-compact"><?php echo htmlspecialchars($report['name'] ?? 'Report'); ?></h3>
                                <p class="text-muted small"><?php echo htmlspecialchars($report['description'] ?? ''); ?></p>
                            </div>
                            <div class="card-footer-compact bg-light p-3">
                                <form action="<?= app_base_url('/admin/analytics/reports/generate') ?>" method="POST" class="w-100">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="report_type" value="<?= htmlspecialchars($report['type']) ?>">
                                    
                                    <div class="d-flex gap-2">
                                        <select name="days" class="form-select form-select-sm" style="width: auto;">
                                            <option value="7">7 Days</option>
                                            <option value="30" selected>30 Days</option>
                                            <option value="90">90 Days</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="fas fa-download me-1"></i> Export
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="empty-state-compact">
                            <i class="fas fa-file-invoice" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">No reports available.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
    /* Reuse styles from overview.php */
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

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

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
    }
    
    .btn-compact:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .analytics-content-body {
        padding: 2rem;
    }

    .pages-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .page-card-compact:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    
    .card-content-compact {
        padding: 1.5rem;
        flex: 1;
    }
    
    .title-compact {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .text-muted { color: #6b7280; }
    .small { font-size: 0.875rem; }
    
    .bg-light { background-color: #f8f9fa !important; }
</style>