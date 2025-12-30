<?php
/**
 * Premium Security Alerts View - Admin Panel
 * High-end design for monitoring suspicious activities
 */

$alertTypeIcons = [
    'impossible_travel' => '✈️',
    'rapid_location_changes' => '🌍',
    'new_device_new_location' => '📱',
    'high_risk_country' => '⚠️'
];

$riskColors = [
    'low' => '#10b981',
    'medium' => '#f59e0b',
    'high' => '#ef4444'
];

$page_title = 'Security Threat Intelligence - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Header -->
        <div class="compact-header threat-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-shield-virus"></i>
                    <h1>Security Intelligence</h1>
                </div>
                <div class="header-subtitle">Real-time monitoring and resolution of suspicious login activities.</div>
            </div>
            <div class="header-actions">
                <div class="custom-dropdown">
                    <button class="btn btn-light btn-compact" onclick="toggleFilterDropdown()">
                        <i class="fas fa-filter text-primary"></i> 
                        <span>Filter: <?= ucwords($filter) ?></span>
                        <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 0.7rem;"></i>
                    </button>
                    <div id="filter-dropdown" class="custom-dropdown-menu shadow-lg">
                        <div class="dropdown-header">Filter by Status</div>
                        <a href="?filter=all" class="dropdown-item <?= $filter == 'all' ? 'active' : '' ?>">All Alerts</a>
                        <a href="?filter=unresolved" class="dropdown-item <?= $filter == 'unresolved' ? 'active' : '' ?>">Unresolved Only</a>
                        <div class="dropdown-header">Filter by Risk</div>
                        <a href="?risk=high" class="dropdown-item">High Risk</a>
                        <a href="?risk=medium" class="dropdown-item">Medium Risk</a>
                        <a href="?risk=low" class="dropdown-item">Low Risk</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Threat Stats Strip -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-satellite"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($stats['total']) ?></div>
                    <div class="stat-label">Total Logs</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($stats['unresolved']) ?></div>
                    <div class="stat-label">Unresolved</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon danger">
                    <i class="fas fa-skull-crossbones"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($stats['high_risk']) ?></div>
                    <div class="stat-label">Critical Risks</div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <div class="threat-grid">
                <!-- Alerts Feed -->
                <div class="feed-container">
                    <?php if (empty($alerts)): ?>
                        <div class="empty-state-premium">
                            <div class="empty-icon shadow-lg">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>Secure Perimeter</h3>
                            <p>No suspicious activity detected within the current filter parameters.</p>
                            <a href="?filter=all" class="btn btn-primary mt-3">View All Logs</a>
                        </div>
                    <?php else: ?>
                        <div class="threat-list">
                            <?php foreach ($alerts as $alert): ?>
                                <?php
                                $metadata = json_decode($alert['metadata'], true);
                                $icon = $alertTypeIcons[$alert['alert_type']] ?? '🔔';
                                $riskColor = $riskColors[$alert['risk_level']] ?? '#64748b';
                                ?>
                                <div class="threat-card <?= $alert['is_resolved'] ? 'resolved' : 'active-threat' ?>" data-alert-id="<?= $alert['id'] ?>">
                                    <div class="threat-card-header">
                                        <div class="threat-type">
                                            <span class="threat-icon"><?= $icon ?></span>
                                            <div>
                                                <div class="threat-name"><?= ucwords(str_replace('_', ' ', $alert['alert_type'])) ?></div>
                                                <div class="threat-time"><?= \App\Helpers\TimeHelper::timeAgo($alert['created_at']) ?> • <?= date('M j, Y g:i A', strtotime($alert['created_at'])) ?></div>
                                            </div>
                                        </div>
                                        <div class="threat-status">
                                            <span class="badge-risk shadow-sm" style="background: <?= $riskColor ?>;">
                                                <?= strtoupper($alert['risk_level']) ?> RISK
                                            </span>
                                        </div>
                                    </div>

                                    <div class="threat-card-body">
                                        <div class="user-intel">
                                            <div class="intel-item">
                                                <i class="fas fa-user-shield text-primary"></i>
                                                <span><?= htmlspecialchars($alert['username'] ?? 'Unknown') ?> (<?= htmlspecialchars($alert['email'] ?? 'N/A') ?>)</span>
                                            </div>
                                            <div class="intel-item">
                                                <i class="fas fa-network-wired text-muted"></i>
                                                <span class="text-monospace"><?= $metadata['ip'] ?? 'N/A' ?></span>
                                            </div>
                                        </div>

                                        <p class="threat-desc"><?= htmlspecialchars($alert['description']) ?></p>

                                        <?php if ($metadata): ?>
                                            <div class="metadata-preview">
                                                <button class="btn-toggle-meta" onclick="this.nextElementSibling.classList.toggle('show')">
                                                    <i class="fas fa-code me-1"></i> Technical Metadata
                                                </button>
                                                <div class="meta-content">
                                                    <pre><?= json_encode($metadata, JSON_PRETTY_PRINT) ?></pre>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="threat-card-footer">
                                        <?php if ($alert['is_resolved']): ?>
                                            <div class="resolved-info">
                                                <i class="fas fa-user-check me-1"></i>
                                                Resolved by Admin on <?= date('M j, Y', strtotime($alert['resolved_at'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-success btn-sm px-4 shadow-sm" onclick="resolveAlert(<?= $alert['id'] ?>)">
                                                <i class="fas fa-check me-1"></i> Mark as Resolved
                                            </button>
                                            <a href="<?= app_base_url('/admin/security/ip-restrictions?ip=' . ($metadata['ip'] ?? '')) ?>" class="btn btn-outline-danger btn-sm px-4">
                                                <i class="fas fa-ban me-1"></i> Block IP
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* COMPACT LAYOUT SHARED STYLES */
    .admin-wrapper-container { max-width: 1400px; margin: 0 auto; padding: 1rem; background: #f8f9fa; min-height: calc(100vh - 70px); }
    .admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
    
    .compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid #e5e7eb; background: white; }
    .header-left { flex: 1; }
    .header-title { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
    .header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .header-title i { font-size: 1.25rem; color: #1f2937; }
    .header-subtitle { font-size: 0.875rem; color: #6b7280; margin: 0; }

    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: #fbfbfc;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    .stat-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    
    .stat-icon { width: 3rem; height: 3rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; }
    .stat-icon.primary { background: #6366f1; }
    .stat-icon.warning { background: #f59e0b; }
    .stat-icon.danger { background: #ef4444; }
    
    .stat-info { flex: 1; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.2; }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; margin-top: 0.25rem; }

    /* CUSTOM DROPDOWN */
    .custom-dropdown { position: relative; }
    .custom-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 1000;
        display: none;
        min-width: 200px;
        padding: 0.5rem 0;
        margin: 0.5rem 0 0;
        background-color: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .custom-dropdown-menu.show { display: block; }
    .dropdown-header { padding: 0.5rem 1.25rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; }
    .dropdown-item { display: block; width: 100%; padding: 0.6rem 1.25rem; clear: both; font-weight: 500; color: #334155; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; font-size: 0.85rem; transition: all 0.2s; }
    .dropdown-item:hover { background-color: #f8fafc; color: #6366f1; padding-left: 1.5rem; }
    .dropdown-item.active { background-color: #eef2ff; color: #6366f1; }

    /* Premium Identity Alerts */
    .threat-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-bottom: none;
    }
    .threat-header h1 { color: white !important; }
    .threat-header .header-subtitle { color: #94a3b8; }
    .threat-header i { color: #ef4444; }

    /* Threat Feed */
    .analytics-content-body { padding: 2rem; }
    .threat-grid { max-width: 1000px; margin: 0 auto; }
    .threat-list { display: flex; flex-direction: column; gap: 1.5rem; }

    .threat-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .threat-card.active-threat { border-left: 6px solid #ef4444; }
    .threat-card.resolved { opacity: 0.7; border-left: 6px solid #10b981; }
    .threat-card:hover { transform: scale(1.01); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

    .threat-card-header {
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .threat-type { display: flex; align-items: center; gap: 1rem; }
    .threat-icon { font-size: 1.5rem; }
    .threat-name { font-weight: 700; color: #1e293b; font-size: 1.1rem; }
    .threat-time { font-size: 0.75rem; color: #64748b; margin-top: 2px; }

    .badge-risk { padding: 0.35rem 0.85rem; border-radius: 30px; color: white; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.05em; }

    .threat-card-body { padding: 1.5rem; }
    .user-intel { display: flex; gap: 2rem; margin-bottom: 1rem; }
    .intel-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500; }
    .text-monospace { font-family: 'JetBrains Mono', 'Fira Code', monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
    .threat-desc { color: #475569; line-height: 1.6; margin-bottom: 1.25rem; }

    .metadata-preview { margin-top: 1rem; }
    .btn-toggle-meta { background: none; border: none; color: #3b82f6; font-size: 0.85rem; font-weight: 600; cursor: pointer; padding: 0; }
    .meta-content { max-height: 0; overflow: hidden; transition: all 0.3s ease; background: #0f172a; border-radius: 8px; }
    .meta-content.show { max-height: 400px; padding: 1rem; margin-top: 0.5rem; }
    .meta-content pre { margin: 0; color: #38bdf8; font-size: 0.8rem; }

    .threat-card-footer { padding: 1rem 1.5rem; background: #f8fafc; display: flex; gap: 1rem; align-items: center; border-top: 1px solid #f1f5f9; }
    .resolved-info { font-size: 0.85rem; color: #10b981; font-weight: 600; }

    .empty-state-premium { padding: 5rem 2rem; text-align: center; background: white; border-radius: 24px; border: 2px dashed #e2e8f0; }
    .empty-icon { width: 80px; height: 80px; background: #10b981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem; }
    .empty-state-premium h3 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .empty-state-premium p { color: #64748b; }

    @media (max-width: 640px) {
        .user-intel { flex-direction: column; gap: 0.5rem; }
        .threat-card-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .threat-status { align-self: flex-end; }
        .compact-stats { grid-template-columns: 1fr; }
    }
</style>

<script>
function toggleFilterDropdown() {
    event.stopPropagation();
    document.getElementById('filter-dropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('filter-dropdown');
    const toggle = event.target.closest('.custom-dropdown');
    if (dropdown && !toggle) {
        dropdown.classList.remove('show');
    }
});

function resolveAlert(alertId) {
    if (!confirm('Mark this security threat as resolved?')) return;

    const formData = new FormData();
    formData.append('alert_id', alertId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');

    // Button feedback
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;

    fetch('<?= app_base_url('/admin/security/alerts/resolve') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification('Alert resolved successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                location.reload();
            }
        } else {
            alert('Failed to resolve alert: ' + data.message);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
