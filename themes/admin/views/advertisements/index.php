<?php

/**
 * OPTIMIZED ADVERTISEMENT MANAGEMENT INTERFACE
 * Matches the 'Pages' module design as requested.
 */

// Set page variables
$page_title = 'Advertisement Management - Admin Panel';
$currentPage = 'advertisements';
$breadcrumbs = [
    ['title' => 'Admin', 'url' => app_base_url('/admin')],
    ['title' => 'Advertisements']
];

// Calculate stats
$totalAds = count($ads);
$activeAds = count(array_filter($ads, fn($a) => $a['is_active']));
$inactiveAds = count(array_filter($ads, fn($a) => !$a['is_active']));
$placements = count(array_unique(array_column($ads, 'location')));
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-ad"></i>
                    <h1>Advertisements</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalAds; ?> total • <?php echo $activeAds; ?> active campaigns</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/advertisements/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>New Campaign</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-ad"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalAds; ?></div>
                    <div class="stat-label">Total Defaults</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeAds; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveAds; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $placements; ?></div>
                    <div class="stat-label">Unique Zones</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search campaigns..." id="page-search">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="status-filter" class="filter-compact">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <!-- Future: View controls if needed -->
        </div>

        <!-- Content Area -->
        <div class="pages-content">

            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($ads)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-ad"></i>
                            <h3>No campaigns found</h3>
                            <p>Create your first advertisement to get started</p>
                            <a href="<?php echo app_base_url('/admin/advertisements/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create Campaign
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-checkbox">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th class="col-title">Identity & Code</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-author">Placement</th>
                                        <th class="col-date">Updated</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ads as $ad): ?>
                                        <tr data-page-id="<?php echo $ad['id']; ?>" class="page-row">
                                            <td>
                                                <input type="checkbox" class="page-checkbox" value="<?php echo $ad['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact"><?php echo htmlspecialchars($ad['name']); ?></div>
                                                    <div class="page-slug-compact" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <code><?php echo htmlspecialchars(substr($ad['code'], 0, 80)); ?>...</code>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <form action="<?php echo app_base_url('/admin/advertisements/toggle/' . $ad['id']); ?>" method="POST" style="display:inline;">
                                                    <button type="submit" class="status-badge status-<?php echo $ad['is_active'] ? 'published' : 'draft'; ?>" style="border:none; cursor:pointer;">
                                                        <i class="fas fa-<?php echo $ad['is_active'] ? 'check-circle' : 'pause-circle'; ?>"></i>
                                                        <?php echo $ad['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="author-compact">
                                                    <i class="fas fa-map-pin"></i>
                                                    <?php 
                                                        $locLabels = [
                                                            'header_top' => 'Header Top',
                                                            'sidebar_top' => 'Sidebar Top',
                                                            'sidebar_bottom' => 'Sidebar Bottom',
                                                            'calc_result' => 'Calculator Result',
                                                            'footer_top' => 'Footer Top'
                                                        ];
                                                        echo $locLabels[$ad['location']] ?? $ad['location'];
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-compact">
                                                    <?php echo isset($ad['updated_at']) ? date('M j', strtotime($ad['updated_at'])) : '-'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('/admin/advertisements/edit/' . $ad['id']); ?>"
                                                        class="action-btn-icon edit-btn"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo app_base_url('/admin/advertisements/delete/' . $ad['id']); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                        <button class="action-btn-icon delete-btn" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('page-search');
        const searchClear = document.getElementById('search-clear');
        
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const tableRows = document.querySelectorAll('.page-row');
                
                tableRows.forEach(row => {
                    const title = row.querySelector('.page-title-compact').textContent.toLowerCase();
                    const visible = title.includes(searchTerm);
                    row.style.display = visible ? '' : 'none';
                });
                
                searchClear.style.display = searchTerm ? 'block' : 'none';
            });
            
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            });
        }
    });
</script>

<style>
/* ========================================
   OPTIMIZED DESIGN SYSTEM (Copied from Pages)
   ======================================== */
:root {
    --gray-50: #f9fafb;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-900: #111827;
}

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
.header-title { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem; }
.header-title h1 { margin: 0; font-size: 1.75rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.5rem; opacity: 0.9; }
.header-subtitle { font-size: 0.875rem; opacity: 0.8; margin: 0; }
.btn-compact { padding: 0.625rem 1.25rem; font-size: 0.875rem; border-radius: 8px; font-weight: 500; }

/* STATS */
.compact-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
}
.stat-item {
    display: flex; align-items: center; gap: 0.75rem; padding: 1rem;
    background: var(--admin-gray-50, #f8f9fa); border-radius: 8px; border: 1px solid var(--admin-gray-200, #e5e7eb);
    transition: all 0.2s ease;
}
.stat-item:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
.stat-icon {
    width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;
}
.stat-icon.primary { background: #667eea; }
.stat-icon.success { background: #48bb78; }
.stat-icon.warning { background: #ed8936; }
.stat-icon.info { background: #4299e1; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: #1f2937; line-height: 1; margin-bottom: 0.25rem; }
.stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; }

/* TOOLBAR */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb); gap: 1rem;
}
.toolbar-left { display: flex; align-items: center; gap: 1rem; flex: 1; }
.search-compact { position: relative; flex: 1; max-width: 350px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.search-compact input {
    width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem;
    border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem;
}
.filter-compact { padding: 0.625rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: white; min-width: 120px; }

/* TABLE */
.table-container { padding: 0; }
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th {
    text-align: left; padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;
    color: #6b7280; font-weight: 600; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
}
.table-compact td { padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
.page-row:hover { background-color: #f9fafb; }
.page-title-compact { font-weight: 600; color: #111827; font-size: 0.9375rem; margin-bottom: 0.125rem; }
.page-slug-compact { font-size: 0.8125rem; color: #6b7280; font-family: monospace; }
.status-badge {
    display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem;
    border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
}
.status-published { background: #d1fae5; color: #065f46; }
.status-draft { background: #f3f4f6; color: #374151; }
.actions-compact { display: flex; gap: 0.5rem; }
.action-btn-icon {
    width: 2rem; height: 2rem; border-radius: 6px; display: flex; align-items: center; justify-content: center;
    border: 1px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer; transition: all 0.2s;
}
.action-btn-icon:hover { border-color: #d1d5db; background: #f3f4f6; color: #111827; }
.edit-btn:hover { border-color: #93c5fd; background: #eff6ff; color: #2563eb; }
.delete-btn:hover { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
.empty-state-compact { text-align: center; padding: 4rem 2rem; }
.empty-state-compact i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }
</style>
