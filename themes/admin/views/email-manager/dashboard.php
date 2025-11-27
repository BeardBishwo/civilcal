<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-envelope"></i> Email Manager</h1>
            <p class="page-description">Manage email threads, templates, and settings</p>
        </div>
        <div class="page-header-actions">
            <a href="#" class="btn btn-primary" id="composeEmailBtn">
                <i class="fas fa-plus"></i> Compose Email
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-inbox"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div class="stat-label">Total Threads</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-envelope-open"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['new_count'] ?? 0); ?></div>
        <div class="stat-label">Unread</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['resolved_count'] ?? 0); ?></div>
        <div class="stat-label">Resolved</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo number_format($stats['high_priority_count'] ?? 0); ?></div>
        <div class="stat-label">High Priority</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card lg:col-span-2">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-comments"></i>
                Recent Threads
            </h5>
        </div>
        <div class="card-content">
            <?php if (empty($recentThreads)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No recent threads</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentThreads as $thread): ?>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" 
                             onclick="window.location.href='<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>'">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="font-medium text-gray-900"><?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?></h6>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($thread['last_message'] ?? '', 0, 60)) . '...'; ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($thread['created_at'] ?? 'now')); ?></span>
                                <?php if (!empty($thread['is_unread'])): ?>
                                    <span class="inline-flex items-center justify-center w-2 h-2 ml-2 bg-blue-500 rounded-full"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-cog"></i>
                Quick Actions
            </h5>
        </div>
        <div class="card-content">
            <div class="space-y-3">
                <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-list mr-3 text-blue-500"></i>
                    <span>View All Threads</span>
                </a>
                <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-file-alt mr-3 text-green-500"></i>
                    <span>Email Templates</span>
                </a>
                <a href="<?php echo app_base_url('/admin/email-manager/settings'); ?>" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-sliders-h mr-3 text-purple-500"></i>
                    <span>Email Settings</span>
                </a>
                <a href="#" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-chart-bar mr-3 text-yellow-500"></i>
                    <span>Analytics</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compose email button functionality
    document.getElementById('composeEmailBtn').addEventListener('click', function(e) {
        e.preventDefault();
        // This would open a modal or redirect to compose page
        alert('Compose email functionality would be implemented here');
    });
    
    console.log('Email manager dashboard loaded');
});
</script>