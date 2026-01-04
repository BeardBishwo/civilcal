<?php
/**
 * PREMIUM IP ACCESS CONTROL
 * Manage Whitelisted and Blacklisted IPs.
 */
$page_title = 'IP Access Control';
$total_whitelist = count($whitelist ?? []);
$total_blacklist = count($blacklist ?? []);
?>

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
                <button onclick="location.reload()" class="btn-secondary-premium">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Premium Stats Grid -->
        <div class="stats-grid-premium">
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper bg-primary-soft">
                    <i class="fas fa-globe text-primary"></i>
                </div>
                <div>
                    <div class="stat-value-premium"><?= $total_whitelist + $total_blacklist ?></div>
                    <div class="stat-label-premium">Total Rules</div>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper bg-success-soft">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div>
                    <div class="stat-value-premium"><?= $total_whitelist ?></div>
                    <div class="stat-label-premium">Whitelisted</div>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper bg-danger-soft">
                    <i class="fas fa-ban text-danger"></i>
                </div>
                <div>
                    <div class="stat-value-premium"><?= $total_blacklist ?></div>
                    <div class="stat-label-premium">Blacklisted</div>
                </div>
            </div>
        </div>

        <!-- Main Grid Content -->
        <div class="premium-grid-layout">
            
            <!-- Left: Add New Rule -->
            <div class="grid-column-small">
                <div class="premium-card sticky-card">
                    <div class="card-header-clean">
                        <h3><i class="fas fa-plus-circle text-primary"></i> Add New Rule</h3>
                    </div>
                    <div class="card-body-clean">
                        <form action="<?= app_base_url('/admin/security/ip-restrictions/add') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            
                            <div class="form-group-premium">
                                <label class="label-premium required">IP Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-network-wired"></i>
                                    <input type="text" name="ip_address" class="input-premium pl-4" placeholder="e.g. 192.168.1.1" required>
                                </div>
                            </div>

                            <div class="form-group-premium">
                                <label class="label-premium required">Rule Type</label>
                                <select name="restriction_type" class="select-premium">
                                    <option value="blacklist">Blacklist (Block Access)</option>
                                    <option value="whitelist">Whitelist (Allow Access)</option>
                                </select>
                            </div>

                            <div class="form-group-premium">
                                <label class="label-premium">Reason / Note</label>
                                <textarea name="reason" class="input-premium" rows="2" placeholder="Why is this IP being added?"></textarea>
                            </div>

                            <div class="form-group-premium">
                                <label class="label-premium">Expiration (Optional)</label>
                                <input type="datetime-local" name="expires_at" class="input-premium">
                            </div>

                            <button type="submit" class="btn-primary-premium w-100 mt-2 justify-center">
                                <i class="fas fa-plus"></i> Add Restriction
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Tables -->
            <div class="grid-column-large">
                
                <!-- Whitelist Table -->
                <div class="premium-card mb-4">
                    <div class="card-header-clean bg-success-light">
                        <h3 class="text-success"><i class="fas fa-check-double"></i> Whitelist Priorities</h3>
                        <span class="badge-pill badge-success"><?= $total_whitelist ?> Active</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($whitelist)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No whitelisted IPs found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($whitelist as $item): ?>
                                        <tr>
                                            <td class="font-mono font-bold text-dark"><?= htmlspecialchars($item['ip_address']) ?></td>
                                            <td class="text-sm text-muted"><?= htmlspecialchars($item['reason'] ?: '-') ?></td>
                                            <td>
                                                <?php if ($item['expires_at']): ?>
                                                    <span class="badge-tag">Expires: <?= date('M d, Y', strtotime($item['expires_at'])) ?></span>
                                                <?php else: ?>
                                                    <span class="badge-tag bg-green-50 text-green-600">Permanent</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <form action="<?= app_base_url('/admin/security/ip-restrictions/remove') ?>" method="POST" onsubmit="return confirm('Remove this restriction?');" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="action-btn-icon text-red" title="Remove">
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
                <div class="premium-card">
                    <div class="card-header-clean bg-danger-light">
                        <h3 class="text-danger"><i class="fas fa-ban"></i> Blacklisted IPs</h3>
                        <span class="badge-pill badge-danger"><?= $total_blacklist ?> Blocked</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($blacklist)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No blacklisted IPs found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($blacklist as $item): ?>
                                        <tr>
                                            <td class="font-mono font-bold text-dark"><?= htmlspecialchars($item['ip_address']) ?></td>
                                            <td class="text-sm text-muted"><?= htmlspecialchars($item['reason'] ?: 'Security Threat') ?></td>
                                            <td>
                                                <?php if ($item['expires_at']): ?>
                                                    <span class="badge-tag">Expires: <?= date('M d, Y', strtotime($item['expires_at'])) ?></span>
                                                <?php else: ?>
                                                    <span class="badge-tag bg-red-50 text-red-600">Permanent</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <form action="<?= app_base_url('/admin/security/ip-restrictions/remove') ?>" method="POST" onsubmit="return confirm('Remove this restriction?');" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="action-btn-icon text-red" title="Remove">
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

<style>
/* PREMIUM SYSTEM STYLES (Scoped) */
:root { --admin-primary: #667eea; --admin-bg: #f8f9fa; --admin-text: #1e293b; }
body { background: var(--admin-bg); font-family: 'Inter', sans-serif; }

.admin-wrapper-container { padding: 1rem; max-width: 1400px; margin: 0 auto; }
.admin-content-wrapper { background: transparent; }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { color: #60a5fa; font-size: 1.25rem; }
.header-subtitle { color: #9ca3af; font-size: 0.9rem; margin-top: 4px; }

/* Buttons */
.btn-primary-premium { background: var(--admin-primary); color: white; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
.btn-primary-premium:hover { background: #5a67d8; transform: translateY(-1px); }
.justify-center { justify-content: center; }

.btn-secondary-premium { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
.btn-secondary-premium:hover { background: rgba(255,255,255,0.2); }

/* Stats Grid */
.stats-grid-premium { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card-premium { background: white; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem; }
.stat-icon-wrapper { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stat-value-premium { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; margin-bottom: 4px; }
.stat-label-premium { font-size: 0.85rem; color: #64748b; font-weight: 500; }

/* Colors */
.bg-primary-soft { background: #e0e7ff; } .text-primary { color: #4338ca; }
.bg-success-soft { background: #dcfce7; } .text-success { color: #15803d; }
.bg-danger-soft { background: #fee2e2; } .text-danger { color: #b91c1c; }

/* Layout Grid */
.premium-grid-layout { display: grid; grid-template-columns: 350px 1fr; gap: 1.5rem; }
@media (max-width: 1024px) { .premium-grid-layout { grid-template-columns: 1fr; } }

/* Cards & Forms */
.premium-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.sticky-card { position: sticky; top: 1rem; }
.card-header-clean { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: white; display: flex; justify-content: space-between; align-items: center; }
.card-header-clean h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #334155; display: flex; align-items: center; gap: 8px; }
.card-body-clean { padding: 1.5rem; }

.bg-success-light { background: #f0fdf4; }
.bg-danger-light { background: #fef2f2; }

/* Input Fields */
.form-group-premium { margin-bottom: 1rem; }
.label-premium { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
.label-premium.required::after { content: "*"; color: #ef4444; margin-left: 2px; }
.input-premium, .select-premium { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none; transition: 0.2s; }
.input-premium:focus, .select-premium:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
.input-premium.pl-4 { padding-left: 2.25rem; }

/* Table */
.table-premium { width: 100%; border-collapse: collapse; }
.table-premium th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
.table-premium td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.9rem; }

/* Badges & Actions */
.badge-pill { display: inline-flex; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.badge-tag { background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }
.bg-green-50 { background: #f0fdf4; } .text-green-600 { color: #16a34a; }
.bg-red-50 { background: #fef2f2; } .text-red-600 { color: #dc2626; }

.action-btn-icon { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; color: #94a3b8; }
.action-btn-icon:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }

.text-right { text-align: right; }
.font-mono { font-family: monospace; }
.text-dark { color: #1e293b; }
.text-muted { color: #64748b; }
</style>
