<?php
/**
 * LOGIN LOGS INTERFACE
 * Detailed tracking of user sessions with geolocation and device info
 */

$logs = $logs ?? [];
$filters = $filters ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        <!-- Compact Page Header -->
        <div class="compact-header" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%);">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-history"></i>
                    <h1>Login Logs</h1>
                </div>
                <div class="header-subtitle">Tracking user access, security, and geolocation</div>
            </div>
            <div class="header-actions">
                <button onclick="location.reload()" class="btn btn-compact" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form action="" method="GET" class="search-compact" style="display: flex; gap: 0.5rem; max-width: 450px;">
                    <div style="position: relative; flex: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                               placeholder="Search IP, city, country, user..." 
                               style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem; border-radius: 8px;">Filter</button>
                    <?php if (!empty($filters['search'])): ?>
                        <a href="?" class="btn" style="padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; color: #6b7280;">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Logs Content Area -->
        <div class="pages-content">
            <div class="table-container">
                <?php if (empty($logs)): ?>
                    <div class="empty-state-compact" style="padding: 4rem 2rem; text-align: center; color: #6b7280;">
                        <i class="fas fa-shield-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <h3>No login logs found</h3>
                        <p>Detailed tracking will appear here as users log in.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper" style="overflow-x: auto;">
                        <table class="table-compact" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">User</th>
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">IP & Location</th>
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Device & Browser</th>
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 36px; height: 36px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                    <?php echo strtoupper(substr($log['username'] ?? '?', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($log['username'] ?? 'Anonymous'); ?></div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;"><?php echo htmlspecialchars($log['email'] ?? ''); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                                <div style="padding-top: 3px; color: #ef4444;">
                                                    <img src="<?php echo app_base_url('themes/default/assets/images/maps.svg'); ?>" style="width: 14px; height: 14px;" alt="Map">
                                                </div>
                                                <div>
                                                    <div style="font-weight: 500; font-family: monospace; color: #374151;"><?php echo htmlspecialchars($log['ip_address']); ?></div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                                        <?php 
                                                        $location = [];
                                                        if (!empty($log['city'])) $location[] = $log['city'];
                                                        if (!empty($log['region'])) $location[] = $log['region'];
                                                        if (!empty($log['country'])) $location[] = $log['country'];
                                                        echo htmlspecialchars(implode(', ', $location) ?: 'Unknown Location');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <?php 
                                                $osIcon = strtolower($log['os'] ?? '');
                                                $osPath = app_base_url('themes/default/assets/images/os/unknown.svg');
                                                if (strpos($osIcon, 'win') !== false) $osPath = app_base_url('themes/default/assets/images/os/windows.svg');
                                                elseif (strpos($osIcon, 'mac') !== false) $osPath = app_base_url('themes/default/assets/images/os/mac.svg');
                                                elseif (strpos($osIcon, 'android') !== false) $osPath = app_base_url('themes/default/assets/images/os/android.svg');
                                                elseif (strpos($osIcon, 'iphone') !== false || strpos($osIcon, 'ios') !== false) $osPath = app_base_url('themes/default/assets/images/os/iphone.svg');
                                                elseif (strpos($osIcon, 'linux') !== false) $osPath = app_base_url('themes/default/assets/images/os/linux.svg');

                                                $browserIcon = strtolower($log['browser'] ?? '');
                                                $browserPath = app_base_url('themes/default/assets/images/browsers/unknown.svg');
                                                if (strpos($browserIcon, 'chrome') !== false) $browserPath = app_base_url('themes/default/assets/images/browsers/chrome.svg');
                                                elseif (strpos($browserIcon, 'firefox') !== false) $browserPath = app_base_url('themes/default/assets/images/browsers/firefox.svg');
                                                elseif (strpos($browserIcon, 'safari') !== false) $browserPath = app_base_url('themes/default/assets/images/browsers/safari.svg');
                                                elseif (strpos($browserIcon, 'edge') !== false) $browserPath = app_base_url('themes/default/assets/images/browsers/edge.svg');
                                                elseif (strpos($browserIcon, 'opera') !== false) $browserPath = app_base_url('themes/default/assets/images/browsers/opera.svg');
                                                ?>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 4px 8px; background: #f3f4f6; border-radius: 6px;" title="OS: <?php echo htmlspecialchars($log['os'] ?? 'Unknown'); ?>">
                                                    <img src="<?php echo $osPath; ?>" style="width: 16px; height: 16px;" alt="OS">
                                                    <span style="font-size: 0.75rem; font-weight: 500;"><?php echo htmlspecialchars($log['os'] ?? 'OS'); ?></span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 4px 8px; background: #f3f4f6; border-radius: 6px;" title="Browser: <?php echo htmlspecialchars($log['browser'] ?? 'Unknown'); ?>">
                                                    <img src="<?php echo $browserPath; ?>" style="width: 16px; height: 16px;" alt="Browser">
                                                    <span style="font-size: 0.75rem; font-weight: 500;"><?php echo htmlspecialchars($log['browser'] ?? 'Browser'); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="font-weight: 500; color: #111827;"><?php echo date('M j, Y', strtotime($log['login_time'])); ?></div>
                                            <div style="font-size: 0.75rem; color: #6b7280;"><?php echo date('H:i:s', strtotime($log['login_time'])); ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (($filters['total_pages'] ?? 1) > 1): ?>
            <div class="pagination-compact" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-top: 1px solid #e5e7eb;">
                <div style="font-size: 0.875rem; color: #6b7280;">
                    Showing <?php echo count($logs); ?> of <?php echo $filters['total_records']; ?> entries
                </div>
                <div style="display: flex; gap: 0.25rem;">
                    <?php 
                    $curr = $filters['page'] ?? 1;
                    $total = $filters['total_pages'] ?? 1;
                    for ($i = 1; $i <= $total; $i++): 
                    ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($filters['search']); ?>" 
                           style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid <?php echo $i == $curr ? '#4f46e5' : '#e5e7eb'; ?>; 
                                  background: <?php echo $i == $curr ? '#4f46e5' : 'white'; ?>; 
                                  color: <?php echo $i == $curr ? 'white' : '#374151'; ?>;
                                  text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
    }
    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .compact-header {
        padding: 2rem;
        color: white;
    }
    .header-title {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    .header-title h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    .compact-toolbar {
        padding: 1rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: #fdfdfd;
    }
    .btn-primary {
        background: #4f46e5;
        color: white;
        border: none;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #4338ca;
    }
    .table-compact th {
        background: #f9fafb;
    }
</style>
