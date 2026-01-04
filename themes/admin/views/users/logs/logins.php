<?php
/**
 * PREMIUM LOGIN LOGS
 * Detailed tracking of user sessions with geolocation and device info.
 */
$page_title = 'Login Logs';
$logs = $logs ?? [];
$filters = $filters ?? [];

// Helpers
function get_os_icon($os) {
    $os = strtolower($os);
    if (strpos($os, 'win') !== false) return 'windows';
    if (strpos($os, 'mac') !== false) return 'mac';
    if (strpos($os, 'android') !== false) return 'android';
    if (strpos($os, 'iphone') !== false || strpos($os, 'ios') !== false) return 'iphone';
    if (strpos($os, 'linux') !== false) return 'linux';
    return 'unknown';
}

function get_browser_icon($browser) {
    $browser = strtolower($browser);
    if (strpos($browser, 'chrome') !== false) return 'chrome';
    if (strpos($browser, 'firefox') !== false) return 'firefox';
    if (strpos($browser, 'safari') !== false) return 'safari';
    if (strpos($browser, 'edge') !== false) return 'edge';
    if (strpos($browser, 'opera') !== false) return 'opera';
    return 'unknown';
}
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-history"></i>
                    <h1>Login Logs</h1>
                </div>
                <div class="header-subtitle">Monitor user access, session security, and geolocation data.</div>
            </div>
            <div class="header-actions">
                <button onclick="location.reload()" class="btn-secondary-premium">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="<?= app_base_url('/admin/users/logs/export') ?><?= !empty($filters['search']) ? '?search='.urlencode($filters['search']) : '' ?>" class="btn-primary-premium">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="compact-toolbar">
            <form action="" method="GET" class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                           placeholder="Search IP, User, Country...">
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                <?php if (!empty($filters['search'])): ?>
                    <a href="?" class="btn-clear" title="Clear Filters"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
            <div class="toolbar-right">
                <div class="record-count">Found <?= $filters['total_records'] ?? count($logs) ?> Records</div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="table-container">
            <?php if (empty($logs)): ?>
                <div class="empty-state-compact">
                    <i class="fas fa-shield-alt"></i>
                    <h3>No logs found</h3>
                    <p>Login activity will appear here.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Location & IP</th>
                                <th>Device & Browser</th>
                                <th>Login Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): 
                                $initial = strtoupper(substr($log['username'] ?? '?', 0, 1));
                            ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-circle"><?= $initial ?></div>
                                            <div>
                                                <div class="user-name"><?= htmlspecialchars($log['username'] ?? 'Anonymous') ?></div>
                                                <div class="user-email"><?= htmlspecialchars($log['email'] ?? '') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="location-cell">
                                            <?php 
                                                // Try to get country code (assuming 'country_code' or 'iso_code' exists, or falling back to 'unknown')
                                                $countryCode = strtolower($log['country_code'] ?? $log['iso_code'] ?? 'unknown');
                                                // If code is missing but country name exists, we might default to unknown or try to map (simple mapping for demo)
                                                // For now, let's assume the backend provides 'country_code'.
                                                $flagPath = app_base_url('themes/default/assets/images/flags/' . $countryCode . '.svg');
                                                
                                                // Fallback if file doesn't exist (client-side error handling is hard in PHP without checking file_exists, 
                                                // but we can check if code is valid length).
                                                $hasFlag = strlen($countryCode) === 2; 
                                            ?>
                                            <?php if ($hasFlag): ?>
                                                <img src="<?= $flagPath ?>" alt="<?= htmlspecialchars($log['country'] ?? 'Flag') ?>" 
                                                     style="width: 20px; height: 15px; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); object-fit: cover; margin-top:3px;">
                                            <?php else: ?>
                                                <i class="fas fa-map-marker-alt text-red"></i>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <div class="ip-address"><?= htmlspecialchars($log['ip_address']) ?></div>
                                                <div class="geo-text">
                                                    <?php 
                                                    $loc = array_filter([$log['city'], $log['region'], $log['country']]);
                                                    echo htmlspecialchars(implode(', ', $loc) ?: 'Unknown');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="device-cell">
                                            <div class="device-badge" title="<?= htmlspecialchars($log['os']) ?>">
                                                <img src="<?= app_base_url('themes/default/assets/images/os/'.get_os_icon($log['os']).'.svg') ?>" alt="OS">
                                                <span><?= htmlspecialchars($log['os'] ?: 'Unknown') ?></span>
                                            </div>
                                            <div class="device-badge" title="<?= htmlspecialchars($log['browser']) ?>">
                                                <img src="<?= app_base_url('themes/default/assets/images/browsers/'.get_browser_icon($log['browser']).'.svg') ?>" alt="Browser">
                                                <span><?= htmlspecialchars($log['browser'] ?: 'Unknown') ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="time-cell">
                                            <span class="date"><?= date('M j, Y', strtotime($log['login_time'])) ?></span>
                                            <span class="time"><?= date('H:i:s', strtotime($log['login_time'])) ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (($filters['total_pages'] ?? 1) > 1): ?>
            <div class="pagination-compact">
                <div class="page-info">
                    Page <?= $filters['page'] ?? 1 ?> of <?= $filters['total_pages'] ?>
                </div>
                <div class="page-controls">
                    <?php 
                    $curr = $filters['page'] ?? 1;
                    $total = $filters['total_pages'] ?? 1;
                    // Simple range for now, can be expanded
                    $range = range(max(1, $curr-2), min($total, $curr+2));
                    ?>
                    
                    <?php if($curr > 1): ?>
                        <a href="?page=<?= $curr-1 ?>&search=<?= urlencode($filters['search']) ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php foreach($range as $p): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($filters['search']) ?>" class="page-btn <?= $p == $curr ? 'active' : '' ?>"><?= $p ?></a>
                    <?php endforeach; ?>

                    <?php if($curr < $total): ?>
                        <a href="?page=<?= $curr+1 ?>&search=<?= urlencode($filters['search']) ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
/* PREMIUM LOGS STYLES */
:root { --admin-primary: #667eea; --admin-secondary: #764ba2; --admin-bg: #f8f9fa; }
body { background: var(--admin-bg); font-family: 'Inter', sans-serif; }

.admin-wrapper-container { padding: 1rem; max-width: 1400px; margin: 0 auto; }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; min-height: 80vh; }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { color: var(--admin-primary); font-size: 1.25rem; }
.header-subtitle { color: #9ca3af; font-size: 0.9rem; margin-top: 4px; }

/* Buttons */
.btn-primary-premium { background: var(--admin-primary); color: white; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; font-size: 0.85rem; }
.btn-primary-premium:hover { background: #5a67d8; transform: translateY(-1px); }

.btn-secondary-premium { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; font-size: 0.85rem; }
.btn-secondary-premium:hover { background: rgba(255,255,255,0.2); }

/* Toolbar */
.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; border-bottom: 1px solid #e5e7eb; background: #fdfdfd; }
.toolbar-left { display: flex; gap: 10px; align-items: center; flex: 1; }
.search-compact { position: relative; width: 300px; }
.search-compact i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; }
.search-compact input { width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem; outline: none; }
.btn-filter { background: var(--admin-primary); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; }
.btn-clear { color: #6b7280; padding: 0.5rem; text-decoration: none; display: flex; align-items: center; }
.record-count { font-size: 0.85rem; color: #6b7280; font-weight: 500; }

/* Table */
.table-premium { width: 100%; border-collapse: collapse; }
.table-premium th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
.table-premium td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
.table-premium tr:hover td { background: #f8fafc; }

.user-cell { display: flex; align-items: center; gap: 12px; }
.avatar-circle { width: 36px; height: 36px; background: #eef2ff; color: #4f46e5; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; }
.user-name { font-weight: 600; color: #1f2937; font-size: 0.9rem; }
.user-email { font-size: 0.8rem; color: #6b7280; }

.location-cell { display: flex; gap: 8px; align-items: flex-start; }
.text-red { color: #ef4444; margin-top: 3px; }
.ip-address { font-family: monospace; font-weight: 600; color: #374151; font-size: 0.85rem; }
.geo-text { font-size: 0.8rem; color: #6b7280; }

.device-cell { display: flex; gap: 8px; flex-wrap: wrap; }
.device-badge { display: flex; align-items: center; gap: 6px; background: #f3f4f6; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 500; color: #4b5563; border: 1px solid #e5e7eb; }
.device-badge img { width: 14px; height: 14px; }

.time-cell { display: flex; flex-direction: column; }
.date { font-weight: 500; color: #1f2937; font-size: 0.9rem; }
.time { font-size: 0.8rem; color: #9ca3af; }

/* Pagination */
.pagination-compact { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-top: 1px solid #e5e7eb; }
.page-info { font-size: 0.85rem; color: #6b7280; }
.page-controls { display: flex; gap: 4px; }
.page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 6px; color: #4b5563; text-decoration: none; font-size: 0.85rem; transition: 0.2s; background: white; }
.page-btn.active { background: var(--admin-primary); color: white; border-color: var(--admin-primary); }
.page-btn:hover:not(.active) { background: #f3f4f6; }

.empty-state-compact { padding: 4rem; text-align: center; color: #9ca3af; }
.empty-state-compact i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; }
.empty-state-compact h3 { margin: 0; font-size: 1.1rem; color: #4b5563; }
</style>
