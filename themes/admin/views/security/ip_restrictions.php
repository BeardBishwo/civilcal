<?php
$page_title = 'IP Access Restrictions - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');

// Pre-calculate stats for the strip
$total_whitelist = count($whitelist ?? []);
$total_blacklist = count($blacklist ?? []);
$total_restrictions = $total_whitelist + $total_blacklist;
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-shield-alt"></i>
                    <h1>IP Access Control</h1>
                </div>
                <div class="header-subtitle">Manage whitelisted and blacklisted IP addresses to secure your platform.</div>
            </div>
            <div class="header-actions">
                <button onclick="location.reload()" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Stats Strip -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $total_restrictions; ?></div>
                    <div class="stat-label">Total Rules</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-success"><?php echo $total_whitelist; ?></div>
                    <div class="stat-label">Whitelisted</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon danger">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value text-danger"><?php echo $total_blacklist; ?></div>
                    <div class="stat-label">Blacklisted</div>
                </div>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="compact-grid">
                <!-- Add Restriction Area -->
                <div class="grid-col-4">
                    <div class="page-card-compact sticky-top" style="top: 1rem;">
                        <div class="card-header-compact">
                            <div class="header-title-sm">
                                <i class="fas fa-plus-circle text-primary"></i> Add New Rule
                            </div>
                        </div>
                        <div class="card-content-compact">
                            <form action="<?= app_base_url('/admin/security/ip-restrictions/add') ?>" method="POST" class="modern-form">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                
                                <div class="form-group-compact">
                                    <label class="form-label-sm">IP Address</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-network-wired"></i>
                                        <input type="text" name="ip_address" placeholder="e.g. 192.168.1.1" required>
                                    </div>
                                </div>

                                <div class="form-group-compact">
                                    <label class="form-label-sm">Rule Type</label>
                                    <select name="restriction_type" class="form-select-compact">
                                        <option value="blacklist">Blacklist (Block)</option>
                                        <option value="whitelist">Whitelist (Allow)</option>
                                    </select>
                                </div>

                                <div class="form-group-compact">
                                    <label class="form-label-sm">Reason / Note</label>
                                    <textarea name="reason" placeholder="Why is this IP being added?" rows="2"></textarea>
                                </div>

                                <div class="form-group-compact">
                                    <label class="form-label-sm">Expiration (Optional)</label>
                                    <input type="datetime-local" name="expires_at" class="form-input-compact">
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-2">
                                    <i class="fas fa-plus me-1"></i> Add Restriction
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Restrictions Lists -->
                <div class="grid-col-8">
                    
                    <!-- Whitelist Table -->
                    <div class="page-card-compact mb-4">
                        <div class="card-header-compact bg-light">
                            <div class="header-title-sm">
                                <i class="fas fa-check-double text-success"></i> Whitelist (Priority Access)
                            </div>
                            <span class="badge rounded-pill bg-success px-3"><?= $total_whitelist ?> IPs</span>
                        </div>
                        <div class="table-container">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Reason</th>
                                        <th>Expires</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($whitelist)): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No whitelisted IPs found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($whitelist as $item): ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?= htmlspecialchars($item['ip_address']) ?></td>
                                                <td class="text-muted small"><?= htmlspecialchars($item['reason'] ?: 'No reason provided') ?></td>
                                                <td>
                                                    <?php if ($item['expires_at']): ?>
                                                        <span class="text-warning tiny">
                                                            <i class="far fa-clock"></i> <?= date('M d, Y', strtotime($item['expires_at'])) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted tiny">Permanent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <form action="<?= app_base_url('/admin/security/ip-restrictions/remove') ?>" method="POST" onsubmit="return confirm('Remove this restriction?');">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <button type="submit" class="btn-action text-danger" title="Remove">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Blacklist Table -->
                    <div class="page-card-compact">
                        <div class="card-header-compact bg-light">
                            <div class="header-title-sm">
                                <i class="fas fa-user-slash text-danger"></i> Blacklist (Blocked Access)
                            </div>
                            <span class="badge rounded-pill bg-danger px-3"><?= $total_blacklist ?> IPs</span>
                        </div>
                        <div class="table-container">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Reason</th>
                                        <th>Expires</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($blacklist)): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No blacklisted IPs found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($blacklist as $item): ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?= htmlspecialchars($item['ip_address']) ?></td>
                                                <td class="text-muted small"><?= htmlspecialchars($item['reason'] ?: 'Security threat') ?></td>
                                                <td>
                                                    <?php if ($item['expires_at']): ?>
                                                        <span class="text-danger tiny">
                                                            <i class="far fa-clock"></i> <?= date('M d, Y', strtotime($item['expires_at'])) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted tiny">Permanent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <form action="<?= app_base_url('/admin/security/ip-restrictions/remove') ?>" method="POST" onsubmit="return confirm('Remove this restriction?');">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <button type="submit" class="btn-action text-danger" title="Remove">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Styling consistent with reports.php and new premium standard */
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
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
        color: white;
    }

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

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
    }

    /* Stats Strip */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 2rem;
        background: #fbfbfc;
        border-bottom: 1px solid #edf2f7;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #edf2f7;
    }

    .stat-icon {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.danger { background: #f56565; }

    .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.8rem; color: #718096; font-weight: 600; text-transform: uppercase; margin-top: 4px; }

    /* Grid & Layout */
    .analytics-content-body { padding: 2rem; }
    .compact-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }
    
    .grid-col-4 { grid-column: span 4; }
    .grid-col-8 { grid-column: span 8; }

    @media (max-width: 992px) {
        .grid-col-4, .grid-col-8 { grid-column: span 12; }
    }

    /* Cards */
    .page-card-compact {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-header-compact {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Forms */
    .card-content-compact { padding: 1.5rem; }
    .form-group-compact { margin-bottom: 1.25rem; }
    .form-label-sm { display: block; font-size: 0.85rem; font-weight: 600; color: #4a5568; margin-bottom: 0.5rem; }
    
    .input-with-icon {
        position: relative;
    }
    .input-with-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }
    .input-with-icon input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .input-with-icon input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-select-compact, .form-input-compact, textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        font-size: 0.9rem;
        background: white;
    }

    /* Tables */
    .table-compact { width: 100%; border-collapse: collapse; }
    .table-compact thead th {
        text-align: left;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        font-size: 0.75rem;
        font-weight: 700;
        color: #718096;
        text-transform: uppercase;
    }
    .table-compact tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }
    .tiny { font-size: 0.75rem; font-weight: 500; }
    .fw-bold { font-weight: 700; }

    /* Action Buttons */
    .btn-action {
        background: none;
        border: none;
        padding: 0.5rem;
        border-radius: 6px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-action:hover { background: #fff5f5; transform: scale(1.1); }
    
    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .bg-light { background-color: #fbfbfb !important; }
</style>
