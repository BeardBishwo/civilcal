<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Bulk User Operations</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Perform mass actions on multiple users at once</p>
        </div>
    </div>
</div>

<!-- Bulk Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Users</div>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-check" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active</div>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-slash" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Inactive Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['inactive'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Inactive</div>
    </div>
</div>

<!-- Bulk Operation Options -->
<div class="admin-card">
    <h2 class="admin-card-title">Bulk Operations</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-user-plus" style="font-size: 2rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
            <h3 style="color: #f9fafb; margin: 0 0 1rem 0;">Activate Users</h3>
            <p style="color: #9ca3af; margin: 0 0 1rem 0;">Enable selected users' accounts</p>
            <button onclick="performBulkAction('activate')" 
                    style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-play"></i>
                <span>Activate</span>
            </button>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-user-slash" style="font-size: 2rem; color: #f87171; margin-bottom: 1rem;"></i>
            <h3 style="color: #f9fafb; margin: 0 0 1rem 0;">Deactivate Users</h3>
            <p style="color: #9ca3af; margin: 0 0 1rem 0;">Disable selected users' accounts</p>
            <button onclick="performBulkAction('deactivate')" 
                    style="padding: 0.5rem 1rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; cursor: pointer;">
                <i class="fas fa-stop"></i>
                <span>Deactivate</span>
            </button>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-user-tag" style="font-size: 2rem; color: #34d399; margin-bottom: 1rem;"></i>
            <h3 style="color: #f9fafb; margin: 0 0 1rem 0;">Change Roles</h3>
            <p style="color: #9ca3af; margin: 0 0 1rem 0;">Update roles for selected users</p>
            <button onclick="performBulkAction('changeRole')" 
                    style="padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                <i class="fas fa-exchange-alt"></i>
                <span>Change Role</span>
            </button>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-trash" style="font-size: 2rem; color: #fbbf24; margin-bottom: 1rem;"></i>
            <h3 style="color: #f9fafb; margin: 0 0 1rem 0;">Delete Users</h3>
            <p style="color: #9ca3af; margin: 0 0 1rem 0;">Permanently delete selected users</p>
            <button onclick="performBulkAction('delete')" 
                    style="padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; cursor: pointer;">
                <i class="fas fa-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    </div>
    
    <!-- Bulk Action Form -->
    <div id="bulkActionForm" style="display: none; background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <h3 style="color: #f9fafb; margin: 0 0 1rem 0;" id="actionTitle">Bulk Action</h3>
        <div id="actionContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- User List with Selection -->
<div class="admin-card">
    <h2 class="admin-card-title">User List</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <label style="color: #f9fafb; margin-right: 0.5rem;">Select All</label>
            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" 
                   style="width: 1rem; height: 1rem; accent-color: #4cc9f0; margin-right: 1rem;">
            <button onclick="performBulkAction('selected')" 
                    style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-bolt"></i>
                <span>Perform Action on Selected</span>
            </button>
        </div>
        <div>
            <input type="text" id="searchUsers" placeholder="Search users..." 
                   style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; width: 200px;">
        </div>
    </div>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; width: 50px;">
                            <input type="checkbox" id="selectAllHeader" onchange="toggleSelectAll()" 
                                   style="width: 1rem; height: 1rem; accent-color: #4cc9f0;">
                        </th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Username</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Email</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Role</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <input type="checkbox" class="userSelect" data-user-id="<?php echo $user['id']; ?>" 
                                           style="width: 1rem; height: 1rem; accent-color: #4cc9f0;">
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo $user['is_active'] ? '#34d399' : '#f87171'; ?>; 
                                          background: <?php echo $user['is_active'] ? 'rgba(52, 211, 153, 0.1)' : 'rgba(248, 113, 113, 0.1)'; ?>;
                                          padding: 0.25rem 0.5rem; 
                                          border-radius: 4px; 
                                          font-size: 0.75rem;">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo $user['created_at'] ?? 'Unknown'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Operation Scripts -->
<script>
let selectedUsers = [];

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.userSelect');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked || selectAllHeaderCheckbox.checked;
    });
    
    updateSelectedUsers();
}

function updateSelectedUsers() {
    const checkboxes = document.querySelectorAll('.userSelect:checked');
    selectedUsers = Array.from(checkboxes).map(checkbox => checkbox.dataset.userId);
    
    // Update the select-all checkbox state
    const allCheckboxes = document.querySelectorAll('.userSelect');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    
    selectAllCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    selectAllHeaderCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

// Add event listeners to individual checkboxes
document.querySelectorAll('.userSelect').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedUsers);
});

function performBulkAction(action) {
    if (selectedUsers.length === 0) {
        alert('Please select at least one user.');
        return;
    }
    
    const actionForm = document.getElementById('bulkActionForm');
    const actionTitle = document.getElementById('actionTitle');
    const actionContent = document.getElementById('actionContent');
    
    switch(action) {
        case 'activate':
            actionTitle.textContent = 'Activate Users';
            actionContent.innerHTML = `
                <p style="color: #f9fafb; margin-bottom: 1rem;">Are you sure you want to activate ${selectedUsers.length} user(s)?</p>
                <div style="display: flex; gap: 1rem;">
                    <button onclick="executeBulkAction('activate')" 
                            style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                        <i class="fas fa-check"></i>
                        <span>Confirm Activation</span>
                    </button>
                    <button onclick="cancelBulkAction()" 
                            style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; cursor: pointer;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            `;
            break;
            
        case 'deactivate':
            actionTitle.textContent = 'Deactivate Users';
            actionContent.innerHTML = `
                <p style="color: #f9fafb; margin-bottom: 1rem;">Are you sure you want to deactivate ${selectedUsers.length} user(s)?</p>
                <div style="display: flex; gap: 1rem;">
                    <button onclick="executeBulkAction('deactivate')" 
                            style="padding: 0.75rem 1.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; cursor: pointer;">
                        <i class="fas fa-check"></i>
                        <span>Confirm Deactivation</span>
                    </button>
                    <button onclick="cancelBulkAction()" 
                            style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; cursor: pointer;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            `;
            break;
            
        case 'changeRole':
            actionTitle.textContent = 'Change User Roles';
            actionContent.innerHTML = `
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Select New Role</label>
                    <select id="newRole" style="width: 100%; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="user">Regular User</option>
                        <option value="admin">Administrator</option>
                        <option value="engineer">Engineer</option>
                    </select>
                </div>
                <p style="color: #f9fafb; margin-bottom: 1rem;">Are you sure you want to change the role for ${selectedUsers.length} user(s)?</p>
                <div style="display: flex; gap: 1rem;">
                    <button onclick="executeBulkAction('changeRole')" 
                            style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                        <i class="fas fa-check"></i>
                        <span>Confirm Role Change</span>
                    </button>
                    <button onclick="cancelBulkAction()" 
                            style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; cursor: pointer;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            `;
            break;
            
        case 'delete':
            actionTitle.textContent = 'Delete Users';
            actionContent.innerHTML = `
                <p style="color: #f87171; margin-bottom: 1rem;"><i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone. Are you sure you want to delete ${selectedUsers.length} user(s)?</p>
                <div style="display: flex; gap: 1rem;">
                    <button onclick="executeBulkAction('delete')" 
                            style="padding: 0.75rem 1.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                        <span>Confirm Deletion</span>
                    </button>
                    <button onclick="cancelBulkAction()" 
                            style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; cursor: pointer;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            `;
            break;
    }
    
    actionForm.style.display = 'block';
}

function executeBulkAction(action) {
    // In a real application, this would make an AJAX call to the server
    console.log(`Executing bulk action: ${action} on users:`, selectedUsers);
    
    // Show a success message
    alert(`Bulk action '${action}' performed on ${selectedUsers.length} user(s). In a real application, this would update the server.`);
    
    // Reset selections and hide the form
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAllHeader').checked = false;
    document.querySelectorAll('.userSelect').forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedUsers = [];
    
    document.getElementById('bulkActionForm').style.display = 'none';
}

function cancelBulkAction() {
    document.getElementById('bulkActionForm').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>