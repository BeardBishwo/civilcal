<?php
$page_title = 'Site Setup Checklist - Admin Panel';
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
?>

<style>
    .admin-layout {
        display: flex;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }

    body.dark-theme .admin-layout {
        background: #0f172a;
    }

    .admin-sidebar {
        width: 280px;
        background: white;
        border-right: 1px solid #e2e8f0;
        padding: 2rem 0;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }

    body.dark-theme .admin-sidebar {
        background: #1e293b;
        border-color: #334155;
    }

    .admin-content {
        flex: 1;
        padding: 2rem;
        max-width: calc(100% - 280px);
    }

    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .progress-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .progress-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .progress-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    body.dark-theme .progress-card {
        background: #1e293b;
        border-color: #334155;
    }

    .overall-progress {
        text-align: center;
        position: relative;
    }

    .progress-circle {
        width: 120px;
        height: 120px;
        margin: 0 auto 1rem;
        position: relative;
    }

    .progress-circle svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .progress-circle .progress-bg {
        fill: none;
        stroke: #e5e7eb;
        stroke-width: 8;
    }

    .progress-circle .progress-bar {
        fill: none;
        stroke: #10b981;
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    body.dark-theme .progress-text {
        color: #f9fafb;
    }

    .checklist-sections {
        display: grid;
        gap: 2rem;
    }

    .checklist-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    body.dark-theme .checklist-section {
        background: #1e293b;
        border-color: #334155;
    }

    .section-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    body.dark-theme .section-header {
        border-color: #334155;
    }

    .section-info h3 {
        margin: 0 0 0.5rem 0;
        color: #1f2937;
        font-size: 1.25rem;
        font-weight: 600;
    }

    body.dark-theme .section-info h3 {
        color: #f9fafb;
    }

    .section-info p {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
    }

    body.dark-theme .section-info p {
        color: #9ca3af;
    }

    .section-progress {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .progress-bar-small {
        width: 100px;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .progress-good {
        background: #10b981;
    }

    .progress-warning {
        background: #f59e0b;
    }

    .progress-needs-attention {
        background: #ef4444;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-good {
        background: #dcfdf7;
        color: #065f46;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-needs-attention {
        background: #fef2f2;
        color: #991b1b;
    }

    body.dark-theme .status-good {
        background: #064e3b;
        color: #6ee7b7;
    }

    body.dark-theme .status-warning {
        background: #78350f;
        color: #fbbf24;
    }

    body.dark-theme .status-needs-attention {
        background: #7f1d1d;
        color: #fca5a5;
    }

    .checklist-items {
        padding: 0;
    }

    .checklist-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .checklist-item:last-child {
        border-bottom: none;
    }

    .checklist-item:hover {
        background: #f9fafb;
    }

    body.dark-theme .checklist-item {
        border-color: #334155;
    }

    body.dark-theme .checklist-item:hover {
        background: #0f172a;
    }

    .item-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        margin-right: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .item-checkbox.completed {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .item-info {
        flex: 1;
    }

    .item-title {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    body.dark-theme .item-title {
        color: #f9fafb;
    }

    .item-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
    }

    body.dark-theme .item-description {
        color: #9ca3af;
    }

    .item-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .required-badge {
        background: #fef2f2;
        color: #991b1b;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    body.dark-theme .required-badge {
        background: #7f1d1d;
        color: #fca5a5;
    }

    .action-btn {
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .action-btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .quick-action-card {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    body.dark-theme .quick-action-card {
        background: #1e293b;
        border-color: #334155;
    }

    .sidebar-nav {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .sidebar-nav a:hover {
        background: #f3f4f6;
        color: #1f2937;
        border-left-color: #3b82f6;
    }

    .sidebar-nav a.active {
        background: #eff6ff;
        color: #2563eb;
        border-left-color: #3b82f6;
    }

    body.dark-theme .sidebar-nav a {
        color: #d1d5db;
    }

    body.dark-theme .sidebar-nav a:hover {
        background: #374151;
        color: #f9fafb;
    }

    body.dark-theme .sidebar-nav a.active {
        background: #1e3a8a;
        color: #93c5fd;
    }

    @media (max-width: 1024px) {
        .admin-layout {
            flex-direction: column;
        }

        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
        }

        .admin-content {
            max-width: 100%;
        }
    }
</style>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="margin: 0; color: #1f2937; font-size: 1.125rem; font-weight: 600;">
                <i class="fas fa-cogs me-2"></i> Admin Panel
            </h3>
        </div>

        <ul class="sidebar-nav">
            <li><a href="<?php echo app_base_url('/admin'); ?>" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?php echo app_base_url('/admin/setup/checklist'); ?>"><i class="fas fa-tasks"></i> Setup Checklist</a></li>
            <li><a href="<?php echo app_base_url('/admin/users'); ?>"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="<?php echo app_base_url('/admin/settings'); ?>"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="<?php echo app_base_url('/admin/logo-settings'); ?>"><i class="fas fa-image"></i> Logo & Branding</a></li>
            <li><a href="<?php echo app_base_url('/admin/modules'); ?>"><i class="fas fa-puzzle-piece"></i> Modules</a></li>
            <li><a href="<?php echo app_base_url('/admin/system-status'); ?>"><i class="fas fa-server"></i> System Status</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="admin-content">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-content">
                <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0;">
                    Welcome <?php echo htmlspecialchars($current_user['full_name'] ?? 'Super Administrator'); ?> 👋
                </h1>
                <p style="font-size: 1.125rem; opacity: 0.9; margin: 0;">
                    Here is a quick overview of your account. Do not forget to check the setup checklist to ensure your site is fully configured.
                </p>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="progress-overview">
            <!-- Overall Progress -->
            <div class="progress-card overall-progress">
                <div class="progress-circle">
                    <svg>
                        <circle class="progress-bg" cx="60" cy="60" r="52"></circle>
                        <circle class="progress-bar" cx="60" cy="60" r="52"
                            stroke-dasharray="<?php echo 2 * M_PI * 52; ?>"
                            stroke-dashoffset="<?php echo (1 - $overall_progress / 100) * 2 * M_PI * 52; ?>"></circle>
                    </svg>
                    <div class="progress-text"><?php echo $overall_progress; ?>%</div>
                </div>
                <h3 style="margin: 0 0 0.5rem 0; color: #1f2937; font-weight: 600;">Overall Progress</h3>
                <p style="color: #6b7280; margin: 0; font-size: 0.875rem;">
                    Setup completion status
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="progress-card">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: #1f2937; font-weight: 600;">Total Users</h3>
                        <p style="color: #6b7280; margin: 0; font-size: 0.875rem;">Active accounts</p>
                    </div>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">1,247</div>
            </div>

            <!-- System Status -->
            <div class="progress-card">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-server"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; color: #1f2937; font-weight: 600;">System Status</h3>
                        <p style="color: #6b7280; margin: 0; font-size: 0.875rem;">All systems operational</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                    <span style="color: #10b981; font-weight: 500; font-size: 0.875rem;">Online</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-bottom: 2rem;">
            <h2 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 600;">Quick Actions</h2>
            <div class="quick-actions">
                <?php foreach ($quick_actions as $action): ?>
                    <a href="<?php echo $action['url']; ?>" class="quick-action-card">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; background: <?php echo $action['color']; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="<?php echo $action['icon']; ?>"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 0.25rem 0; color: #1f2937; font-weight: 600; font-size: 0.875rem;">
                                    <?php echo htmlspecialchars($action['title']); ?>
                                </h4>
                                <p style="margin: 0; color: #6b7280; font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($action['description']); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Setup Checklist -->
        <div class="checklist-sections">
            <h2 style="margin: 0 0 2rem 0; color: #1f2937; font-size: 1.75rem; font-weight: 600;">
                <i class="fas fa-tasks me-2"></i> Site Setup Checklist
            </h2>

            <?php foreach ($checklist_sections as $section): ?>
                <div class="checklist-section">
                    <div class="section-header">
                        <div class="section-info">
                            <h3><?php echo htmlspecialchars($section['title']); ?></h3>
                            <p><?php echo htmlspecialchars($section['description']); ?></p>
                        </div>
                        <div class="section-progress">
                            <div class="progress-bar-small">
                                <div class="progress-fill progress-<?php echo $section['status']; ?>"
                                    style="width: <?php echo $section['progress']; ?>%"></div>
                            </div>
                            <span style="font-weight: 600; color: #1f2937; min-width: 40px;">
                                <?php echo $section['progress']; ?>%
                            </span>
                            <span class="status-badge status-<?php echo $section['status']; ?>">
                                <?php echo ucfirst(str_replace('-', ' ', $section['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <div class="checklist-items">
                        <?php foreach ($section['items'] as $item): ?>
                            <div class="checklist-item">
                                <div class="item-checkbox <?php echo $item['completed'] ? 'completed' : ''; ?>"
                                    data-item-id="<?php echo $item['id']; ?>"
                                    onclick="toggleChecklistItem('<?php echo $item['id']; ?>', <?php echo $item['completed'] ? 'false' : 'true'; ?>)">
                                    <?php if ($item['completed']): ?>
                                        <i class="fas fa-check"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="item-info">
                                    <h4 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                </div>

                                <div class="item-actions">
                                    <?php if ($item['required']): ?>
                                        <span class="required-badge">Required</span>
                                    <?php endif; ?>
                                    <a href="<?php echo $item['action_url']; ?>" class="action-btn">
                                        <?php echo htmlspecialchars($item['action_text']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Recent Activities -->
        <div style="margin-top: 3rem;">
            <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                Recent Activities
            </h3>
            <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <?php foreach ($recent_activities as $activity): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #e5e7eb;">
                        <div style="width: 32px; height: 32px; background: <?php echo $activity['color']; ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem;">
                            <i class="<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div style="flex: 1;">
                            <p style="margin: 0; color: #1f2937; font-weight: 500; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($activity['action']); ?>
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 0.75rem;">
                                by <?php echo htmlspecialchars($activity['user']); ?> • <?php echo $activity['timestamp']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleChecklistItem(itemId, completed) {
        // Update UI immediately
        const checkbox = document.querySelector(`[data-item-id="${itemId}"]`);
        if (completed) {
            checkbox.classList.add('completed');
            checkbox.innerHTML = '<i class="fas fa-check"></i>';
        } else {
            checkbox.classList.remove('completed');
            checkbox.innerHTML = '';
        }

        // Send update to server
        fetch('<?php echo app_base_url('/admin/setup/update-item'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    completed: completed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update overall progress
                    updateOverallProgress(data.overall_progress);

                    // Show success message
                    showNotification('Checklist item updated successfully', 'success');
                } else {
                    // Revert UI changes on error
                    if (completed) {
                        checkbox.classList.remove('completed');
                        checkbox.innerHTML = '';
                    } else {
                        checkbox.classList.add('completed');
                        checkbox.innerHTML = '<i class="fas fa-check"></i>';
                    }
                    showNotification('Failed to update checklist item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert UI changes on error
                if (completed) {
                    checkbox.classList.remove('completed');
                    checkbox.innerHTML = '';
                } else {
                    checkbox.classList.add('completed');
                    checkbox.innerHTML = '<i class="fas fa-check"></i>';
                }
                showNotification('Network error occurred', 'error');
            });
    }

    function updateOverallProgress(percentage) {
        const progressText = document.querySelector('.progress-text');
        const progressBar = document.querySelector('.progress-bar');
        const circumference = 2 * Math.PI * 52;

        if (progressText) {
            progressText.textContent = percentage + '%';
        }

        if (progressBar) {
            const offset = (1 - percentage / 100) * circumference;
            progressBar.style.strokeDashoffset = offset;
        }
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        transition: all 0.3s ease;
        transform: translateX(100%);
        ${type === 'success' ? 'background: #10b981;' : 'background: #ef4444;'}
    `;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize progress circle animation
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            const percentage = <?php echo $overall_progress; ?>;
            const circumference = 2 * Math.PI * 52;
            progressBar.style.strokeDasharray = circumference;
            progressBar.style.strokeDashoffset = circumference;

            // Animate progress
            setTimeout(() => {
                const offset = (1 - percentage / 100) * circumference;
                progressBar.style.strokeDashoffset = offset;
            }, 500);
        }
    });
</script>

<?php require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php'; ?>