<?php
/**
 * PREMIUM CALCULATIONS HISTORY INTERFACE
 * Matching the design of Pages and Themes Management
 */

// Calculate stats
$totalCalculations = isset($stats['total']) ? $stats['total'] : 0;
$weekCalculations = isset($stats['week_count']) ? $stats['week_count'] : 0;
$uniqueUsers = isset($stats['unique_users']) ? $stats['unique_users'] : 0;
$todayCalculations = isset($stats['today_count']) ? $stats['today_count'] : 0;
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-history"></i>
                    <h1>Calculations History</h1>
                </div>
                <div class="header-subtitle"><?php echo number_format($totalCalculations); ?> total • <?php echo number_format($weekCalculations); ?> this week</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-secondary btn-compact" onclick="exportCalculations()">
                    <i class="fas fa-download"></i>
                    <span>Export</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($totalCalculations); ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($todayCalculations); ?></div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($weekCalculations); ?></div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($uniqueUsers); ?></div>
                    <div class="stat-label">Unique Users</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <!-- Client-side Search -->
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" id="calculationSearch" placeholder="Search calculations...">
                </div>
            </div>
            <div class="toolbar-right">
                <!-- Filter Dropdown -->
                <select class="form-control form-control-sm" id="calculatorFilter" style="width: auto;">
                    <option value="">All Calculators</option>
                    <?php if (!empty($calculatorTypes)): ?>
                        <?php foreach ($calculatorTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Calculations Content -->
        <?php if (empty($calculations)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No calculations found</h3>
                <p>Calculation history will appear here.</p>
            </div>
        <?php else: ?>
            <!-- Table View -->
            <div class="table-wrapper">
                <table class="table-compact" id="calculationsTable">
                    <thead>
                        <tr>
                            <th class="col-user">User</th>
                            <th class="col-calculator">Calculator Type</th>
                            <th class="col-module">Module</th>
                            <th class="col-date">Date & Time</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calculations as $calc): ?>
                            <tr class="calculation-row" 
                                data-user="<?php echo strtolower($calc['username'] ?? $calc['email'] ?? 'anonymous'); ?>"
                                data-calculator="<?php echo strtolower($calc['calculator_type'] ?? ''); ?>">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name"><?php echo htmlspecialchars($calc['username'] ?? $calc['email'] ?? 'Anonymous'); ?></div>
                                            <?php if (!empty($calc['email']) && !empty($calc['username'])): ?>
                                                <div class="user-email"><?php echo htmlspecialchars($calc['email']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($calc['calculator_type'] ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <span class="text-muted"><?php echo htmlspecialchars($calc['module'] ?? 'General'); ?></span>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div class="date-primary"><?php echo isset($calc['created_at']) ? date('M d, Y', strtotime($calc['created_at'])) : ''; ?></div>
                                        <div class="date-secondary"><?php echo isset($calc['created_at']) ? date('h:i A', strtotime($calc['created_at'])) : ''; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="actions-compact">
                                        <button class="action-btn-icon view-btn" 
                                                onclick="viewCalculation(<?php echo $calc['id'] ?? 0; ?>)" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn-icon delete-btn" 
                                                onclick="deleteCalculation(<?php echo $calc['id'] ?? 0; ?>)" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<script>
// Search functionality
document.getElementById('calculationSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.calculation-row');
    
    rows.forEach(row => {
        const user = row.getAttribute('data-user') || '';
        const calculator = row.getAttribute('data-calculator') || '';
        
        if (user.includes(searchTerm) || calculator.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Filter by calculator type
document.getElementById('calculatorFilter')?.addEventListener('change', function(e) {
    const filterValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.calculation-row');
    
    rows.forEach(row => {
        const calculator = row.getAttribute('data-calculator') || '';
        
        if (!filterValue || calculator === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// View calculation details
function viewCalculation(id) {
    // Implement view details functionality
    alert('View calculation details for ID: ' + id);
}

// Delete calculation
function deleteCalculation(id) {
    if (!confirm('Are you sure you want to delete this calculation record?')) return;
    
    fetch('<?php echo get_app_url(); ?>/admin/calculations/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete calculation'));
        }
    })
    .catch(error => {
        alert('Error deleting calculation');
        console.error(error);
    });
}

// Export calculations
function exportCalculations() {
    window.location.href = '<?php echo get_app_url(); ?>/admin/calculations/export';
}
</script>

<style>
/* User Info Styles */
.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 500;
    color: #2d3748;
}

.user-email {
    font-size: 0.75rem;
    color: #718096;
}

/* Date Info Styles */
.date-info {
    display: flex;
    flex-direction: column;
}

.date-primary {
    font-weight: 500;
    color: #2d3748;
}

.date-secondary {
    font-size: 0.75rem;
    color: #718096;
}
</style>