<?php
// themes/admin/views/library/requests.php
$reward_default = \App\Services\SettingsService::get('library_upload_reward', 100);
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        <!-- Premium Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-invoice"></i>
                    <h1>Library Requests</h1>
                </div>
                <div class="header-subtitle" id="stats-summary">Loading processing queue...</div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-pending">0</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-approved">0</div>
                    <div class="stat-label">Approved Today</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-rewards">0</div>
                    <div class="stat-label">BB Coins Disbursed</div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Filter by title..." id="file-search">
                </div>
                <select id="type-filter" class="filter-compact">
                    <option value="">All Categories</option>
                    <option value="cad">AutoCAD</option>
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
        </div>

        <!-- Requests Area -->
        <div class="pages-content">
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th class="col-title">File Details</th>
                                <th class="col-status">Uploader</th>
                                <th class="col-date">Uploaded At</th>
                                <th class="col-status">Verify</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requests-table">
                            <tr><td colspan="5" class="text-center">Initializing secure link...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let defaultReward = <?= $reward_default ?>;

document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
    
    document.getElementById('file-search').addEventListener('input', filterTable);
    document.getElementById('type-filter').addEventListener('change', loadRequests);
});

async function loadRequests() {
    const type = document.getElementById('type-filter').value;
    const url = `<?= app_base_url('/api/library/browse?status=pending&admin_mode=true') ?>${type ? '&type='+type : ''}`;
    
    try {
        const res = await fetch(url);
        const data = await res.json();
        
        const tbody = document.getElementById('requests-table');
        
        // Update Stats
        document.getElementById('stat-pending').textContent = data.files ? data.files.length : 0;
        document.getElementById('stats-summary').textContent = `${data.files ? data.files.length : 0} documents in verification queue`;
        
        if (data.stats) {
            document.getElementById('stat-approved').textContent = data.stats.approved_today || 0;
            document.getElementById('stat-rewards').textContent = data.stats.rewards_disbursed || 0;
        }
        
        if(!data.files || data.files.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><i class="fas fa-check-circle fa-3x mb-3 text-muted"></i><h4>All caught up!</h4><p>No pending submissions for this category.</p></td></tr>';
            return;
        }
        
        tbody.innerHTML = data.files.map(file => `
            <tr class="file-row" data-title="${file.title.toLowerCase()}">
                <td>
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div class="file-icon-box">
                            <i class="fas ${getFileIcon(file.file_type)}"></i>
                        </div>
                        <div>
                            <div class="page-title-compact">${file.title}</div>
                            <div class="helper-text" style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${file.description || 'No description'}</div>
                            <div class="mt-1">
                                <span class="status-badge status-${file.file_type}">${file.file_type.toUpperCase()}</span>
                                ${file.tags ? file.tags.split(',').map(tag => `<span class="badge badge-light-admin">#${tag.trim()}</span>`).join(' ') : ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="user-info-compact">
                        <div class="avatar-sm">
                            ${file.uploader_name.charAt(0).toUpperCase()}
                        </div>
                        <div class="page-info">
                            <div class="page-title-compact">${file.uploader_name}</div>
                            <small class="text-muted">UID: ${file.uploader_id}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="date-compact">
                        ${new Date(file.created_at).toLocaleDateString()}
                        <br><small>${new Date(file.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                    </div>
                </td>
                <td>
                    <div class="actions-compact">
                        <a href="<?= app_base_url('/api/library/preview?id=') ?>${file.id}" target="_blank" class="action-btn-icon" title="View Preview Image">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= app_base_url('/api/library/download?id=') ?>${file.id}" target="_blank" class="action-btn-icon" style="color:var(--admin-primary);" title="Download & Verify Original File">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="reward-input-group">
                            <input type="number" value="${defaultReward}" class="reward-amount" id="reward-${file.id}" title="Reward BB Coins">
                            <span class="reward-currency">BB</span>
                        </div>
                        <button class="btn btn-success btn-sm btn-action" onclick="processFile(${file.id}, 'approve')">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-action" onclick="processFile(${file.id}, 'reject')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        
    } catch (e) {
        console.error(e);
    }
}

function getFileIcon(type) {
    const icons = {
        'cad': 'fa-drafting-compass',
        'excel': 'fa-file-excel',
        'pdf': 'fa-file-pdf',
        'image': 'fa-file-image',
        'doc': 'fa-file-word'
    };
    return icons[type] || 'fa-file-alt';
}

function filterTable() {
    const term = this.value.toLowerCase();
    const rows = document.querySelectorAll('.file-row');
    rows.forEach(row => {
        row.style.display = row.dataset.title.includes(term) ? '' : 'none';
    });
}

async function processFile(id, action) {
    let reason = '';
    let reward = 0;
    
    if (action === 'reject') {
        reason = prompt("Reason for rejection:");
        if (reason === null) return;
    } else {
        reward = document.getElementById(`reward-${id}`).value;
        if (!confirm(`Are you sure you want to approve this resource and award ${reward} BB Coins?`)) return;
    }
    
    try {
        const res = await fetch('<?= app_base_url("/api/admin/library/approve") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                file_id: id, 
                action: action, 
                reason: reason,
                reward_amount: reward
            })
        });
        
        const data = await res.json();
        if(data.success) {
            showNotification(data.message, 'success');
            loadRequests();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (e) {
        showNotification('Signal Lost: Encryption failed.', 'error');
    }
}

function showNotification(msg, type) {
    // Assuming admin theme has toastr or similar
    if(window.toastr) {
        toastr[type](msg);
    } else {
        alert(msg);
    }
}
</script>

<style>
/* PREMIUM ADMIN STYLES FOR LIBRARY */
.admin-wrapper-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem;
}

.admin-content-wrapper {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #eee;
}

.compact-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-title h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
}

.header-subtitle {
    opacity: 0.8;
    font-size: 0.9rem;
    margin-top: 5px;
}

.compact-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    padding: 1.5rem 2rem;
    background: #fcfcfd;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item {
    background: white;
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.03);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-icon.primary { background: rgba(79, 70, 229, 0.1); color: #4f46e5; }
.stat-icon.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon.info { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #111827;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.compact-toolbar {
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    background: white;
    border-bottom: 1px solid #f0f0f0;
}

.search-compact {
    position: relative;
    display: flex;
    align-items: center;
}

.search-compact i {
    position: absolute;
    left: 12px;
    color: #9ca3af;
}

.search-compact input {
    padding: 8px 12px 8px 35px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    width: 250px;
}

.filter-compact {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #374151;
    margin-left: 1rem;
}

.table-compact {
    width: 100%;
    border-collapse: collapse;
}

.table-compact th {
    text-align: left;
    padding: 12px 20px;
    background: #f9fafb;
    font-size: 0.75rem;
    font-weight: 700;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table-compact td {
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.file-icon-box {
    width: 40px;
    height: 40px;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.25rem;
}

.page-title-compact {
    font-weight: 700;
    color: #111827;
    margin-bottom: 2px;
}

.status-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
}

.status-cad { background: rgba(79, 70, 229, 0.1); color: #4f46e5; }
.status-excel { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.status-pdf { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.badge-light-admin {
    background: #f3f4f6;
    color: #6b7280;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    background: #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
    color: #4b5563;
}

.reward-input-group {
    display: flex;
    align-items: center;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 4px 8px;
    width: 100px;
}

.reward-amount {
    width: 100%;
    border: none;
    background: transparent;
    font-weight: 700;
    font-size: 0.85rem;
    color: #111827;
    outline: none;
    text-align: right;
}

.reward-currency {
    font-size: 0.7rem;
    font-weight: 800;
    color: #9ca3af;
    margin-left: 4px;
}

.btn-action {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.action-btn-icon {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    transition: color 0.2s;
    font-size: 1.1rem;
}

.action-btn-icon:hover {
    color: #4f46e5;
}
</style>
