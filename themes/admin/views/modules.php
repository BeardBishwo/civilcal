<?php
$page_title = $page_title ?? 'Module Management';
$allModules = $allModules ?? [];
$activeModules = $activeModules ?? [];
$menuItems = $menuItems ?? [];
?>

<div class="admin-content">
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-puzzle-piece"></i>
        Module Management
    </h1>
    <p class="page-description">Manage and configure your admin modules. Activate, deactivate, or configure module settings.</p>
</div>

<!-- Module Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-puzzle-piece"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($allModules); ?></div>
        <div class="stat-label">Total Modules</div>
    </div>
    
    <?php 
    // Count active modules
    $activeCount = 0;
    foreach ($allModules as $module) {
        if (isset($module['is_active']) && $module['is_active']) {
            $activeCount++;
        } elseif (isset($module['status']) && $module['status'] === 'active') {
            $activeCount++;
        }
    }
    ?>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo $activeCount; ?></div>
        <div class="stat-label">Active Modules</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-pause-circle"></i>
            </div>
        </div>
        <div class="stat-value"><?php echo count($allModules) - $activeCount; ?></div>
        <div class="stat-label">Inactive Modules</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-cog"></i>
            </div>
        </div>
        <div class="stat-value">0</div>
        <div class="stat-label">Updates Available</div>
    </div>
</div>

<!-- Module Actions Bar -->
<div class="module-toolbar">
    <div class="module-toolbar__inner">
        <div class="module-toolbar__primary">
            <div class="module-search-group">
                <span class="module-search-group__icon"><i class="fas fa-search"></i></span>
                <input type="text" placeholder="Search modules..." class="module-search-group__input" onkeyup="searchModules(this.value)">
            </div>

            <div class="module-filter-group">
                <span class="module-filter-group__label"><i class="fas fa-filter"></i> Filter</span>
                <select id="moduleFilter" onchange="filterModules(this.value)" class="module-filter-group__select">
                    <option value="all">All Modules</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
        </div>

        <div class="module-toolbar__actions">
            <button class="btn btn-soft" onclick="refreshModules()">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
            <button class="btn btn-primary" onclick="installModule()">
                <i class="fas fa-plus"></i>
                Install Module
            </button>
        </div>
    </div>
</div>

<!-- Modules Grid -->
<div class="modules-grid" id="modulesGrid">
    <?php if (!empty($allModules)): ?>
        <?php foreach ($allModules as $module): ?>
            <?php
            // Handle both database and file system module structures
            $moduleName = $module['name'] ?? $module['slug'] ?? 'Unknown Module';
            $moduleSlug = $module['slug'] ?? strtolower(str_replace(' ', '-', $moduleName));
            $moduleDescription = $module['description'] ?? 'No description available';
            $moduleVersion = $module['version'] ?? '1.0.0';
            $moduleCategory = $module['category'] ?? 'general';
            $moduleIcon = $module['icon'] ?? 'fas fa-cube';
            
            // Determine if module is active
            $isActive = false;
            if (isset($module['is_active'])) {
                $isActive = (bool)$module['is_active'];
            } elseif (isset($module['status'])) {
                $isActive = $module['status'] === 'active';
            }
            
            $statusClass = $isActive ? 'active' : 'inactive';
            $statusIcon = $isActive ? 'fa-check-circle' : 'fa-pause-circle';
            $statusText = $isActive ? 'Active' : 'Inactive';
            $actionText = $isActive ? 'Deactivate' : 'Activate';
            $actionClass = $isActive ? 'danger' : 'success';
            $actionIcon = $isActive ? 'fa-pause' : 'fa-play';
            ?>
            
            <div class="module-card <?php echo $statusClass; ?>" data-module="<?php echo htmlspecialchars($moduleSlug); ?>" data-status="<?php echo $statusClass; ?>">
                <div class="module-header">
                    <div class="module-icon">
                        <i class="<?php echo htmlspecialchars($moduleIcon); ?>"></i>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-<?php echo $statusClass; ?>">
                            <i class="fas <?php echo $statusIcon; ?>"></i>
                            <?php echo $statusText; ?>
                        </span>
                    </div>
                </div>
                
                <div class="module-body">
                    <h3 class="module-name"><?php echo htmlspecialchars($moduleName); ?></h3>
                    <p class="module-description"><?php echo htmlspecialchars($moduleDescription); ?></p>
                    
                    <div class="module-meta">
                        <div class="meta-item">
                            <span class="meta-label">Version:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($moduleVersion); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Category:</span>
                            <span class="meta-value"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $moduleCategory))); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="module-footer">
                    <div class="module-actions">
                        <button class="btn btn-<?php echo $actionClass; ?> btn-sm toggle-module" 
                                data-module="<?php echo htmlspecialchars($moduleSlug); ?>"
                                data-action="<?php echo $isActive ? 'deactivate' : 'activate'; ?>">
                            <i class="fas <?php echo $actionIcon; ?>"></i>
                            <?php echo $actionText; ?>
                        </button>
                        
                        <?php if ($isActive): ?>
                        <a href="<?php echo app_base_url('/admin/modules/' . urlencode($moduleSlug) . '/settings'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                        <?php endif; ?>
                        
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-info-circle"></i>
                                    Module Info
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-bug"></i>
                                    Report Issue
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i>
                                    Uninstall
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <h3>No Modules Found</h3>
            <p>No admin modules are currently available. Install some modules to get started.</p>
            <button class="btn btn-primary" onclick="installModule()">
                <i class="fas fa-plus"></i>
                Install Your First Module
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Module Actions Scripts -->
<script>
// Module toggle functionality
document.addEventListener("DOMContentLoaded", function() {
    // Add event listeners to toggle buttons
    document.querySelectorAll(".toggle-module").forEach(button => {
        button.addEventListener("click", async function() {
            const moduleName = this.dataset.module;
            const action = this.dataset.action;
            
            try {
                const response = await fetch(`/admin/modules/${moduleName}/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({module: moduleName})
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Reload the page to reflect changes
                    location.reload();
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred while processing your request.', 'error');
            }
        });
    });
});

// Refresh modules function
function refreshModules() {
    location.reload();
}

// Install module function
function installModule() {
    showNotification('Module installation functionality would go here in a real implementation.', 'info');
}

// Filter modules function
function filterModules(filter) {
    const moduleCards = document.querySelectorAll('.module-card');
    moduleCards.forEach(card => {
        const status = card.dataset.status;
        if (filter === 'all' || status === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Search modules function
function searchModules(query) {
    const moduleCards = document.querySelectorAll('.module-card');
    const searchTerm = query.toLowerCase();
    
    moduleCards.forEach(card => {
        const moduleName = card.querySelector('.module-name').textContent.toLowerCase();
        const moduleDescription = card.querySelector('.module-description').textContent.toLowerCase();
        
        if (moduleName.includes(searchTerm) || moduleDescription.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<style>
/* Module Management Styles */
.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.module-card {
    border: 1px solid var(--admin-border);
    border-radius: 12px;
    overflow: hidden;
    background: white;
    transition: var(--transition);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.module-card:hover {
    box-shadow: var(--admin-shadow);
    transform: translateY(-2px);
}

.module-card.active {
    border-color: var(--admin-success);
}

.module-card.inactive {
    opacity: 0.7;
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--admin-border);
    background: var(--admin-gray-50);
}

.module-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.module-status {
    display: flex;
    align-items: center;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--admin-success);
}

.status-badge.status-inactive {
    background: rgba(245, 158, 11, 0.1);
    color: var(--admin-warning);
}

.module-body {
    padding: 20px;
}

.module-name {
    margin: 0 0 12px 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--admin-gray-800);
}

.module-description {
    margin: 0 0 16px 0;
    color: var(--admin-gray-600);
    line-height: 1.5;
}

.module-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 12px;
    color: var(--admin-gray-500);
    margin-bottom: 2px;
}

.meta-value {
    font-size: 14px;
    font-weight: 500;
    color: var(--admin-gray-700);
}

.module-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--admin-border);
    background: var(--admin-gray-50);
}

.module-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.module-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.module-actions .dropdown {
    margin-left: auto;
}

.module-actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 24px 0;
    gap: 16px;
}

.actions-left {
    display: flex;
    gap: 12px;
}

.actions-right {
    display: flex;
    gap: 12px;
    align-items: center;
}

.module-filter,
.module-search {
    min-width: 200px;
}

.module-ac.module-toolbar {
    margin: 24px 0;
}

.module-toolbar__inner {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px 20px;
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: white;
    box-shadow: 0 10px 30px -20px rgba(15, 23, 42, 0.4);
    align-items: center;
    justify-content: space-between;
}

.module-toolbar__primary {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.module-toolbar__actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.module-search-group {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, 0.4);
    border-radius: 12px;
    padding: 10px 14px;
    min-width: 240px;
    transition: all 0.2s ease;
}

.module-search-group:focus-within {
    border-color: #6366f1;
    box-shadow: 0 8px 24px -20px rgba(99, 102, 241, 0.6);
}

.module-search-group__icon {
    color: #6366f1;
    margin-right: 10px;
    font-size: 15px;
}

.module-search-group__input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 14px;
    width: 100%;
    color: #1f2937;
}

.module-search-group__input::placeholder {
    color: #9ca3af;
}

.module-filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, 0.4);
    border-radius: 12px;
    padding: 10px 14px;
    transition: all 0.2s ease;
}

.module-filter-group:hover,
.module-filter-group:focus-within {
    border-color: #0ea5e9;
    box-shadow: 0 8px 24px -20px rgba(14, 165, 233, 0.6);
}

.module-filter-group__label {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.module-filter-group__select {
    border: none;
    outline: none;
    background: transparent;
    font-size: 14px;
    color: #1f2937;
    cursor: pointer;
}

.module-filter-group__select option {
    color: #1f2937;
}

.btn.btn-soft {
    background: #f1f5f9;
    color: #0f172a;
}

.btn.btn-soft:hover {
    background: #e2e8f0;
}

.btn-primary i,
.btn-soft i {
    font-size: 14px;
}

@media (max-width: 768px) {
    .module-toolbar__inner {
        flex-direction: column;
        align-items: stretch;
    }

    .module-toolbar__primary,
    .module-toolbar__actions {
        width: 100%;
    }

    .module-toolbar__actions {
        justify-content: space-between;
    }

    .module-search-group,
    .module-filter-group {
        width: 100%;
    }
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 64px 24px;
}

.empty-icon {
    font-size: 48px;
    color: var(--admin-gray-400);
    margin-bottom: 24px;
}

.empty-state h3 {
    margin: 0 0 12px 0;
    color: var(--admin-gray-800);
}

.empty-state p {
    margin: 0 0 24px 0;
    color: var(--admin-gray-600);
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .modules-grid {
        grid-template-columns: 1fr;
    }
    
    .module-actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .actions-left,
    .actions-right {
        width: 100%;
    }
    
    .module-filter,
    .module-search {
        min-width: auto;
    }
}
</style>
