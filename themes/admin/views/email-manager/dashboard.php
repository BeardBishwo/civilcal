<?php
// Email Manager Dashboard - Compact Design
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-envelope"></i>
                    <h1>Email Manager</h1>
                </div>
                <div class="header-subtitle">Manage email threads, templates, and settings</div>
            </div>
            <div class="header-actions">
                <a href="#" class="btn btn-primary btn-compact" id="composeEmailBtn">
                    <i class="fas fa-plus"></i>
                    <span>Compose Email</span>
                </a>
            </div>
        </div>
        
        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
                    <div class="stat-label">Total Threads</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-envelope-open"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['new_count'] ?? 0); ?></div>
                    <div class="stat-label">Unread</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['resolved_count'] ?? 0); ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($stats['high_priority_count'] ?? 0); ?></div>
                    <div class="stat-label">High Priority</div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <div class="grid-layout-with-sidebar">
                <!-- Main Content: Recent Threads -->
                <div class="grid-main">
                    <div class="page-card-compact">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-comments text-primary"></i> Recent Threads
                            </div>
                            <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="btn btn-light btn-compact btn-sm">
                                View All
                            </a>
                        </div>
                        <div class="card-content-compact p-0">
                            <?php if (empty($recentThreads)): ?>
                                <div class="empty-state-compact py-5">
                                    <i class="fas fa-inbox text-muted fa-2x mb-3"></i>
                                    <p class="text-muted">No recent threads found.</p>
                                </div>
                            <?php else: ?>
                                <div class="thread-list">
                                    <?php foreach ($recentThreads as $thread): ?>
                                        <div class="thread-item" onclick="window.location.href='<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>'">
                                            <div class="thread-icon">
                                                <div class="avatar-circle-md bg-blue-50 text-blue-600">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                            </div>
                                            <div class="thread-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="thread-title"><?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?></h6>
                                                    <span class="thread-date"><?php echo date('M d, Y', strtotime($thread['created_at'] ?? 'now')); ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-end mt-1">
                                                    <p class="thread-preview"><?php echo htmlspecialchars(substr($thread['last_message'] ?? '', 0, 80)) . '...'; ?></p>
                                                    <?php if (!empty($thread['is_unread'])): ?>
                                                        <span class="unread-dot"></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Quick Actions -->
                <div class="grid-sidebar">
                    <div class="page-card-compact">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-bolt text-warning"></i> Quick Actions
                            </div>
                        </div>
                        <div class="card-content-compact p-0">
                            <div class="quick-actions-list">
                                <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="quick-action-item">
                                    <div class="action-icon bg-blue-50 text-blue-600"><i class="fas fa-list"></i></div>
                                    <span>View All Threads</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="quick-action-item">
                                    <div class="action-icon bg-green-50 text-green-600"><i class="fas fa-file-alt"></i></div>
                                    <span>manage Templates</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/email-manager/settings'); ?>" class="quick-action-item">
                                    <div class="action-icon bg-purple-50 text-purple-600"><i class="fas fa-sliders-h"></i></div>
                                    <span>Email Settings</span>
                                </a>
                                <a href="#" class="quick-action-item">
                                    <div class="action-icon bg-yellow-50 text-yellow-600"><i class="fas fa-chart-bar"></i></div>
                                    <span>View Analytics</span>
                                </a>
                            </div>
                        </div>
                    </div>
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

    /* STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #fbfbfc;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.warning { background: #ed8936; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.danger { background: #f56565; }

    .stat-info { flex: 1; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.2; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; }

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
    
    .btn-light { background: white; color: #374151; border: 1px solid #d1d5db; }
    .btn-light:hover { background: #f3f4f6; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5a67d8; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }

    /* CONTENT BODY & LAYOUT */
    .analytics-content-body {
        padding: 2rem;
    }
    
    .grid-layout-with-sidebar {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    
    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 55px;
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
    .p-0 { padding: 0 !important; }
    
    .text-muted { color: #6b7280 !important; }
    .text-primary { color: #667eea !important; }
    .text-warning { color: #ed8936 !important; }
    
    /* THREAD LIST */
    .thread-list { display: flex; flex-direction: column; }
    
    .thread-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.15s;
    }
    
    .thread-item:last-child { border-bottom: none; }
    .thread-item:hover { background: #f9fafb; }
    
    .avatar-circle-md {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .bg-blue-50 { background: #ebf8ff; }
    .text-blue-600 { color: #3182ce; }
    
    .thread-content { flex: 1; }
    .thread-title { margin: 0; font-size: 0.95rem; font-weight: 600; color: #1f2937; }
    .thread-date { font-size: 0.75rem; color: #9ca3af; }
    .thread-preview { margin: 0; font-size: 0.85rem; color: #6b7280; line-height: 1.4; }
    
    .d-flex { display: flex; }
    .justify-content-between { justify-content: space-between; }
    .align-items-start { align-items: flex-start; }
    .align-items-end { align-items: flex-end; }
    .mt-1 { margin-top: 0.25rem; }
    
    .unread-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #4299e1; display: inline-block;
    }
    
    /* QUICK ACTIONS */
    .quick-actions-list { display: flex; flex-direction: column; }
    
    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        text-decoration: none;
        color: #4b5563;
        font-weight: 500;
        font-size: 0.9rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    
    .quick-action-item:last-child { border-bottom: none; }
    .quick-action-item:hover { background: #f9fafb; color: #1f2937; }
    
    .action-icon {
        width: 32px; height: 32px; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }
    .bg-green-50 { background: #f0fff4; }
    .text-green-600 { color: #38a169; }
    .bg-purple-50 { background: #faf5ff; }
    .text-purple-600 { color: #805ad5; }
    .bg-yellow-50 { background: #fffff0; }
    .text-yellow-600 { color: #d69e2e; }

    .empty-state-compact { text-align: center; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .mb-3 { margin-bottom: 1rem; }

    /* Responsive */
    @media (max-width: 991px) {
        .grid-layout-with-sidebar { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        .compact-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compose email button functionality
    document.getElementById('composeEmailBtn').addEventListener('click', function(e) {
        e.preventDefault();
        // This would open a modal or redirect to compose page
        alert('Compose email functionality would be implemented here');
    });
});
</script>
