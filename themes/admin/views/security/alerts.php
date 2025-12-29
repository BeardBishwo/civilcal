<?php
/**
 * Security Alerts View - Admin Panel
 * Display and manage security alerts
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
?>

<style>
    .alerts-container {
        padding: 2rem;
        background: #f8fafc;
        min-height: 100vh;
    }

    .alerts-header {
        margin-bottom: 2rem;
    }

    .alerts-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
    }

    .stat-card.total { border-left-color: #667eea; }
    .stat-card.unresolved { border-left-color: #f59e0b; }
    .stat-card.high-risk { border-left-color: #ef4444; }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
    }

    .filters-bar {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: #667eea;
    }

    .alerts-list {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .alert-item {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        transition: background 0.3s;
    }

    .alert-item:hover {
        background: #f8fafc;
    }

    .alert-item:last-child {
        border-bottom: none;
    }

    .alert-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .alert-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .alert-icon {
        font-size: 1.5rem;
    }

    .risk-badge {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
    }

    .alert-meta {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #64748b;
    }

    .alert-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-description {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .alert-metadata {
        background: #fef3c7;
        padding: 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .alert-actions {
        display: flex;
        gap: 1rem;
    }

    .btn-resolve {
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-resolve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .resolved-badge {
        padding: 0.6rem 1.5rem;
        background: #e2e8f0;
        color: #64748b;
        border-radius: 6px;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
</style>

<div class="admin-content">
    <div class="alerts-container">
        <div class="alerts-header">
            <h1>🚨 Security Alerts</h1>
            <p>Monitor and manage suspicious login activities</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Alerts</div>
            </div>
            <div class="stat-card unresolved">
                <div class="stat-value"><?= $stats['unresolved'] ?></div>
                <div class="stat-label">Unresolved</div>
            </div>
            <div class="stat-card high-risk">
                <div class="stat-value"><?= $stats['high_risk'] ?></div>
                <div class="stat-label">High Risk</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">All Alerts</a>
            <a href="?filter=unresolved" class="filter-btn <?= $filter === 'unresolved' ? 'active' : '' ?>">Unresolved</a>
            <a href="?risk=high" class="filter-btn <?= $risk_level === 'high' ? 'active' : '' ?>">High Risk</a>
            <a href="?risk=medium" class="filter-btn <?= $risk_level === 'medium' ? 'active' : '' ?>">Medium Risk</a>
            <a href="?risk=low" class="filter-btn <?= $risk_level === 'low' ? 'active' : '' ?>">Low Risk</a>
        </div>

        <!-- Alerts List -->
        <div class="alerts-list">
            <?php if (empty($alerts)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎉</div>
                    <h3>No Security Alerts</h3>
                    <p>All clear! No suspicious activities detected.</p>
                </div>
            <?php else: ?>
                <?php foreach ($alerts as $alert): ?>
                    <?php
                    $metadata = json_decode($alert['metadata'], true);
                    $icon = $alertTypeIcons[$alert['alert_type']] ?? '🔔';
                    $riskColor = $riskColors[$alert['risk_level']] ?? '#64748b';
                    ?>
                    <div class="alert-item" data-alert-id="<?= $alert['id'] ?>">
                        <div class="alert-header">
                            <div class="alert-title">
                                <span class="alert-icon"><?= $icon ?></span>
                                <span><?= ucwords(str_replace('_', ' ', $alert['alert_type'])) ?></span>
                            </div>
                            <span class="risk-badge" style="background: <?= $riskColor ?>">
                                <?= strtoupper($alert['risk_level']) ?> RISK
                            </span>
                        </div>

                        <div class="alert-meta">
                            <div class="alert-meta-item">
                                <strong>User:</strong> <?= htmlspecialchars($alert['username'] ?? 'Unknown') ?>
                            </div>
                            <div class="alert-meta-item">
                                <strong>Email:</strong> <?= htmlspecialchars($alert['email'] ?? 'N/A') ?>
                            </div>
                            <div class="alert-meta-item">
                                <strong>Time:</strong> <?= date('M j, Y g:i A', strtotime($alert['created_at'])) ?>
                            </div>
                        </div>

                        <div class="alert-description">
                            <?= htmlspecialchars($alert['description']) ?>
                        </div>

                        <?php if ($metadata): ?>
                            <div class="alert-metadata">
                                <strong>Details:</strong>
                                <pre style="margin: 0.5rem 0 0 0; font-family: monospace; font-size: 0.85rem;"><?= json_encode($metadata, JSON_PRETTY_PRINT) ?></pre>
                            </div>
                        <?php endif; ?>

                        <div class="alert-actions">
                            <?php if ($alert['is_resolved']): ?>
                                <span class="resolved-badge">
                                    ✓ Resolved on <?= date('M j, Y', strtotime($alert['resolved_at'])) ?>
                                </span>
                            <?php else: ?>
                                <button class="btn-resolve" onclick="resolveAlert(<?= $alert['id'] ?>)">
                                    Mark as Resolved
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function resolveAlert(alertId) {
    if (!confirm('Mark this alert as resolved?')) {
        return;
    }

    const formData = new FormData();
    formData.append('alert_id', alertId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');

    fetch('<?= app_base_url('/admin/security/alerts/resolve') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to resolve alert: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
