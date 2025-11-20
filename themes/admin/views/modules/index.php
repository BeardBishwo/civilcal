<?php
// Modules Management View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-puzzle-piece"></i>
            Module Management
        </h1>
        <p class="page-description">Manage and configure your admin modules. Activate, deactivate, or configure module settings.</p>
    </div>
    
    <!-- Module Statistics -->
    <div class="stats-grid" style="margin-bottom: 32px;">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
            </div>
            <div class="stat-value">' . count($allModules ?? []) . '</div>
            <div class="stat-label">Total Modules</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">' . count($activeModules ?? []) . '</div>
            <div class="stat-label">Active Modules</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
            <div class="stat-value">' . (count($allModules ?? []) - count($activeModules ?? [])) . '</div>
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
    <div class="module-actions-bar">
        <div class="actions-left">
            <button class="btn btn-primary" onclick="refreshModules()">
                <i class="fas fa-sync-alt"></i>
                Refresh Modules
            </button>
            <button class="btn btn-secondary" onclick="installModule()">
                <i class="fas fa-plus"></i>
                Install New Module
            </button>
        </div>
        
        <div class="actions-right">
            <div class="module-filter">
                <select id="moduleFilter" onchange="filterModules(this.value)" class="form-control">
                    <option value="all">All Modules</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
            
            <div class="module-search">
                <input type="text" placeholder="Search modules..." class="form-control" onkeyup="searchModules(this.value)">
            </div>
        </div>
    </div>
    
    <!-- Modules Grid -->
    <div class="modules-grid" id="modulesGrid">
';

if (!empty($allModules)) {
    foreach ($allModules as $moduleName => $moduleInfo) {
        $isActive = isset($activeModules[$moduleName]);
        $statusClass = $isActive ? 'active' : 'inactive';
        $statusIcon = $isActive ? 'fa-check-circle' : 'fa-pause-circle';
        $statusText = $isActive ? 'Active' : 'Inactive';
        $actionText = $isActive ? 'Deactivate' : 'Activate';
        $actionClass = $isActive ? 'danger' : 'success';
        $actionIcon = $isActive ? 'fa-pause' : 'fa-play';

        $content .= '
        <div class="module-card ' . $statusClass . '" data-module="' . htmlspecialchars($moduleName) . '" data-status="' . $statusClass . '">
            <div class="module-header">
                <div class="module-icon">
                    <i class="' . htmlspecialchars($moduleInfo['icon'] ?? 'fas fa-cube') . '"></i>
                </div>
                <div class="module-status">
                    <span class="status-badge status-' . $statusClass . '">
                        <i class="fas ' . $statusIcon . '"></i>
                        ' . $statusText . '
                    </span>
                </div>
            </div>
            
            <div class="module-body">
                <h3 class="module-name">' . htmlspecialchars($moduleInfo['name']) . '</h3>
                <p class="module-description">' . htmlspecialchars($moduleInfo['description']) . '</p>
                
                <div class="module-meta">
                    <div class="meta-item">
                        <span class="meta-label">Version:</span>
                        <span class="meta-value">' . htmlspecialchars($moduleInfo['version'] ?? '1.0.0') . '</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Author:</span>
                        <span class="meta-value">' . htmlspecialchars($moduleInfo['author'] ?? 'Unknown') . '</span>
                    </div>
                </div>
            </div>
            
            <div class="module-footer">
                <div class="module-actions">
                    <button class="btn btn-' . $actionClass . ' btn-sm toggle-module" 
                            data-module="' . htmlspecialchars($moduleName) . '"
                            data-action="' . ($isActive ? 'deactivate' : 'activate') . '">
                        <i class="fas ' . $actionIcon . '"></i>
                        ' . $actionText . '
                    </button>
                    
                    ' . ($isActive ? '
                    <a href="/admin/modules/' . urlencode($moduleName) . '/settings" class="btn btn-secondary btn-sm">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                    ' : '') . '
                    
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
        ';
    }
} else {
    $content .= '
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
    ';
}

$content .= '
    </div>
    
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
            
            // Show loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> \' + (action === "activate" ? "Activating..." : "Deactivating...");
            
            try {
                await ModuleManager.toggleModule(moduleName, action);
            } catch (error) {
                // Restore button state on error
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    });
});

// Filter modules by status
function filterModules(status) {
    const modules = document.querySelectorAll(".module-card");
    
    modules.forEach(module => {
        const moduleStatus = module.dataset.status;
        
        if (status === "all" || status === moduleStatus) {
            module.style.display = "block";
        } else {
            module.style.display = "none";
        }
    });
}

// Search modules by name
function searchModules(query) {
    const modules = document.querySelectorAll(".module-card");
    const searchTerm = query.toLowerCase();
    
    modules.forEach(module => {
        const moduleName = module.querySelector(".module-name").textContent.toLowerCase();
        const moduleDescription = module.querySelector(".module-description").textContent.toLowerCase();
        
        if (moduleName.includes(searchTerm) || moduleDescription.includes(searchTerm)) {
            module.style.display = "block";
        } else {
            module.style.display = "none";
        }
    });
}

// Refresh modules list
async function refreshModules() {
    AdminApp.showLoading(true, "Refreshing modules...");
    
    try {
        // Simulate refresh delay
        await new Promise(resolve => setTimeout(resolve, 1000));
        window.location.reload();
    } catch (error) {
        AdminApp.showNotification("Failed to refresh modules", "error");
    } finally {
        AdminApp.showLoading(false);
    }
}

// Install new module
function installModule() {
    AdminApp.showNotification("Module installation feature coming soon!", "info");
}

// Dropdown functionality
document.addEventListener("click", function(e) {
    if (e.target.matches(".dropdown-toggle") || e.target.closest(".dropdown-toggle")) {
        e.preventDefault();
        const dropdown = e.target.closest(".dropdown");
        const menu = dropdown.querySelector(".dropdown-menu");
        
        // Close all other dropdowns
        document.querySelectorAll(".dropdown-menu.show").forEach(m => {
            if (m !== menu) m.classList.remove("show");
        });
        
        menu.classList.toggle("show");
    } else if (!e.target.closest(".dropdown-menu")) {
        // Close all dropdowns when clicking outside
        document.querySelectorAll(".dropdown-menu.show").forEach(m => {
            m.classList.remove("show");
        });
    }
});
</script>

<style>
/* Module Management Styles */
.module-actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 20px 24px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
}

.actions-left {
    display: flex;
    gap: 12px;
}

.actions-right {
    display: flex;
    gap: 16px;
    align-items: center;
}

.module-filter select,
.module-search input {
    min-width: 200px;
}

.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.module-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.module-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--admin-shadow-lg);
}

.module-card.active {
    border-left: 4px solid var(--admin-success);
}

.module-card.inactive {
    border-left: 4px solid var(--admin-gray-300);
}

.module-header {
    padding: 20px 24px 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.module-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--admin-success);
}

.status-badge.status-inactive {
    background: rgba(107, 114, 128, 0.1);
    color: var(--admin-gray-600);
}

.module-body {
    padding: 0 24px 16px;
}

.module-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--admin-gray-900);
    margin-bottom: 8px;
}

.module-description {
    color: var(--admin-gray-600);
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 16px;
}

.module-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
}

.meta-label {
    color: var(--admin-gray-500);
    font-weight: 500;
}

.meta-value {
    color: var(--admin-gray-700);
}

.module-footer {
    padding: 16px 24px 20px;
    border-top: 1px solid var(--admin-gray-200);
    background: var(--admin-gray-50);
}

.module-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.dropdown {
    position: relative;
    margin-left: auto;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid var(--admin-gray-200);
    border-radius: 8px;
    box-shadow: var(--admin-shadow-lg);
    min-width: 180px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    color: var(--admin-gray-700);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition);
}

.dropdown-item:hover {
    background: var(--admin-gray-50);
}

.dropdown-item.text-danger {
    color: var(--admin-danger);
}

.empty-state {
    text-align: center;
    padding: 64px 32px;
    color: var(--admin-gray-600);
}

.empty-icon {
    font-size: 64px;
    color: var(--admin-gray-300);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 24px;
    color: var(--admin-gray-700);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 16px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .module-actions-bar {
        flex-direction: column;
        gap: 16px;
    }
    
    .actions-left,
    .actions-right {
        width: 100%;
        justify-content: center;
    }
    
    .modules-grid {
        grid-template-columns: 1fr;
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Modules']
];

// Include the layout
include __DIR__ . '/../../layouts/main.php';
